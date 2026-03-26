# WhatsApp Service Setup

This service handles real WhatsApp Web connections using whatsapp-web.js.

## Installation

1. Navigate to the whatsapp-service directory:
```bash
cd whatsapp-service
```

2. Install dependencies (if not already done):
```bash
npm install
```

## Starting the Service

To start the WhatsApp service, run:

```bash
npm start
```

Or from the root directory:
```bash
node whatsapp-service/server.js
```

The service will start on port 3000 (http://localhost:3000)

## How It Works

1. The service creates a Socket.IO server that listens for WhatsApp connection requests
2. When a user clicks "Connect New WhatsApp", a real WhatsApp Web QR code is generated
3. Users scan the QR code with their WhatsApp mobile app
4. Once connected, all conversations and messages are synchronized in real-time
5. Messages can be sent and received through the live chat interface

## Important Notes

- Make sure port 3000 is available
- The service needs to run alongside your Laravel application
- WhatsApp sessions are stored in the `.wwebjs_auth` folder
- Keep the service running for active WhatsApp connections

## Troubleshooting

If you get "Invalid QR Code" errors:
- Make sure the WhatsApp service is running
- Check that no other service is using port 3000
- Try restarting the WhatsApp service

## Running Both Services

You can run both the Laravel server and WhatsApp service:

Terminal 1 (Laravel):
```bash
php artisan serve --port=6500
```

Terminal 2 (Vite):
```bash
npm run dev
```

Terminal 3 (WhatsApp Service):
```bash
node whatsapp-service/server.js
```
