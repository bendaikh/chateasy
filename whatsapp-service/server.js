const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const qrcode = require('qrcode');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "http://127.0.0.1:6500",
        methods: ["GET", "POST"]
    }
});

app.use(express.json());

// Store active WhatsApp clients
const clients = new Map();

// Socket.IO connection
io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);

    socket.on('init-whatsapp', async (data) => {
        const { sessionId, userId } = data;
        
        console.log(`Initializing WhatsApp for session: ${sessionId}`);

        // Check if client already exists
        if (clients.has(sessionId)) {
            console.log('Client already exists for session:', sessionId);
            const existingClient = clients.get(sessionId);
            
            // Check if still connected
            const state = await existingClient.getState();
            if (state === 'CONNECTED') {
                socket.emit('whatsapp-already-connected', { sessionId });
                
                // Get and send chats
                try {
                    const chats = await existingClient.getChats();
                    const chatList = await Promise.all(chats.slice(0, 50).map(async (chat) => {
                        const contact = await chat.getContact();
                        const lastMessage = chat.lastMessage;
                        
                        return {
                            id: chat.id._serialized,
                            name: chat.name || contact.pushname || contact.number,
                            isGroup: chat.isGroup,
                            unreadCount: chat.unreadCount,
                            timestamp: chat.timestamp,
                            lastMessage: lastMessage ? lastMessage.body : null
                        };
                    }));

                    socket.emit('chats-list', { sessionId, chats: chatList });
                } catch (error) {
                    console.error('Error getting chats:', error);
                }
                
                return;
            }
        }

        // Create new WhatsApp client
        const client = new Client({
            authStrategy: new LocalAuth({
                clientId: sessionId
            }),
            puppeteer: {
                headless: true,
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu'
                ]
            }
        });

        // QR Code event
        client.on('qr', async (qr) => {
            console.log('QR Code received for session:', sessionId);
            
            // Generate QR code as data URL
            const qrDataUrl = await qrcode.toDataURL(qr);
            
            // Send QR code to frontend
            socket.emit('qr-code', {
                sessionId,
                qrCode: qrDataUrl
            });
        });

        // Ready event
        client.on('ready', async () => {
            console.log('WhatsApp client is ready:', sessionId);
            
            const info = client.info;
            
            // Send connection success to frontend
            socket.emit('whatsapp-connected', {
                sessionId,
                userId,
                phone: info.wid.user,
                name: info.pushname,
                platform: info.platform
            });

            // Store client
            clients.set(sessionId, client);
        });

        // Message event
        client.on('message', async (message) => {
            console.log('Message received:', message.from, message.body);
            
            const chat = await message.getChat();
            const contact = await message.getContact();
            
            const messageData = {
                sessionId,
                userId,
                messageId: message.id._serialized,
                from: message.from,
                to: message.to,
                body: message.body,
                type: message.type,
                timestamp: message.timestamp,
                isGroup: chat.isGroup,
                contactName: contact.pushname || contact.name || message.from,
                sender: message.fromMe ? 'outgoing' : 'incoming'
            };
            
            // Broadcast message to all connected clients (not just the one that initialized)
            io.emit('new-message', messageData);
            console.log('Broadcasted message to all clients');
        });

        // Disconnected event
        client.on('disconnected', (reason) => {
            console.log('WhatsApp disconnected:', sessionId, reason);
            socket.emit('whatsapp-disconnected', { sessionId, reason });
            clients.delete(sessionId);
        });

        // Auth failure event
        client.on('auth_failure', (msg) => {
            console.log('Authentication failure:', sessionId, msg);
            socket.emit('whatsapp-error', { sessionId, error: 'Authentication failed' });
            clients.delete(sessionId);
        });

        // Error handling
        client.on('error', (error) => {
            console.error('WhatsApp client error:', sessionId, error);
        });

        // Initialize client with error handling
        try {
            await client.initialize();
        } catch (error) {
            console.error('Failed to initialize WhatsApp client:', error);
            socket.emit('whatsapp-error', { sessionId, error: 'Failed to initialize' });
        }
    });

    socket.on('send-message', async (data) => {
        const { sessionId, to, message } = data;
        const client = clients.get(sessionId);

        if (!client) {
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            const chatId = to.includes('@c.us') ? to : `${to}@c.us`;
            const sentMessage = await client.sendMessage(chatId, message);
            
            socket.emit('message-sent', {
                messageId: sentMessage.id._serialized,
                timestamp: sentMessage.timestamp
            });
        } catch (error) {
            console.error('Error sending message:', error);
            socket.emit('error', { message: 'Failed to send message' });
        }
    });

    socket.on('reconnect-session', async (data) => {
        const { sessionId } = data;
        
        console.log(`Attempting to reconnect session: ${sessionId}`);

        // Check if client exists in memory
        if (clients.has(sessionId)) {
            const client = clients.get(sessionId);
            const state = await client.getState();
            
            if (state === 'CONNECTED') {
                socket.emit('whatsapp-already-connected', { sessionId });
                return;
            }
        }

        // Try to initialize from saved session
        socket.emit('init-whatsapp', data);
    });

    socket.on('get-chats', async (data) => {
        const { sessionId } = data;
        const client = clients.get(sessionId);

        if (!client) {
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            const chats = await client.getChats();
            const chatList = await Promise.all(chats.map(async (chat) => {
                const contact = await chat.getContact();
                const lastMessage = chat.lastMessage;
                
                return {
                    id: chat.id._serialized,
                    name: chat.name || contact.pushname || contact.number,
                    isGroup: chat.isGroup,
                    unreadCount: chat.unreadCount,
                    timestamp: chat.timestamp,
                    lastMessage: lastMessage ? lastMessage.body : null
                };
            }));

            socket.emit('chats-list', { sessionId, chats: chatList });
        } catch (error) {
            console.error('Error getting chats:', error);
            socket.emit('error', { message: 'Failed to get chats' });
        }
    });

    socket.on('get-messages', async (data) => {
        const { sessionId, chatId, limit = 50 } = data;
        const client = clients.get(sessionId);

        if (!client) {
            socket.emit('error', { message: 'Client not found' });
            return;
        }

        try {
            const chat = await client.getChatById(chatId);
            const messages = await chat.fetchMessages({ limit });

            const messageList = messages.map(msg => ({
                id: msg.id._serialized,
                body: msg.body,
                type: msg.type,
                timestamp: msg.timestamp,
                from: msg.from,
                to: msg.to,
                sender: msg.fromMe ? 'outgoing' : 'incoming',
                hasMedia: msg.hasMedia
            }));

            socket.emit('messages-list', { sessionId, chatId, messages: messageList });
        } catch (error) {
            console.error('Error getting messages:', error);
            socket.emit('error', { message: 'Failed to get messages' });
        }
    });

    socket.on('disconnect', () => {
        console.log('Client disconnected:', socket.id);
    });
});

// REST API endpoints
app.post('/api/disconnect', (req, res) => {
    const { sessionId } = req.body;
    const client = clients.get(sessionId);

    if (client) {
        client.destroy();
        clients.delete(sessionId);
        res.json({ success: true, message: 'Client disconnected' });
    } else {
        res.status(404).json({ success: false, message: 'Client not found' });
    }
});

app.get('/api/status', (req, res) => {
    res.json({
        success: true,
        activeSessions: clients.size
    });
});

const PORT = process.env.WHATSAPP_PORT || 3000;

server.listen(PORT, () => {
    console.log(`WhatsApp service running on port ${PORT}`);
});
