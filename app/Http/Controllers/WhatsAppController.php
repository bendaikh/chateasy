<?php

namespace App\Http\Controllers;

use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsAppController extends Controller
{
    public function saveConnection(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'phone' => 'required|string',
            'name' => 'required|string'
        ]);
        
        try {
            // First, try to find by session_id or phone_number
            $profile = WhatsappProfile::where('session_id', $request->session_id)
                ->orWhere(function($query) use ($request) {
                    $query->where('phone_number', $request->phone)
                          ->where('user_id', auth()->id());
                })
                ->where('user_id', auth()->id())
                ->first();
            
            if ($profile) {
                // Update existing profile
                $profile->update([
                    'session_id' => $request->session_id,
                    'name' => $request->name,
                    'phone_number' => $request->phone,
                    'status' => 'connected',
                    'is_active' => true,
                    'last_connected_at' => now()
                ]);
            } else {
                // Create new profile
                $profile = WhatsappProfile::create([
                    'user_id' => auth()->id(),
                    'session_id' => $request->session_id,
                    'name' => $request->name,
                    'phone_number' => $request->phone,
                    'status' => 'connected',
                    'is_active' => true,
                    'last_connected_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Connection saved successfully.',
                'profile' => $profile
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save connection: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function generateQrCode(Request $request)
    {
        $sessionId = Str::uuid()->toString();
        
        session(['whatsapp_session_id' => $sessionId]);
        
        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'qr_code' => $this->generateQrCodeData($sessionId),
            'message' => 'QR code generated. Please scan with WhatsApp.'
        ]);
    }
    
    public function checkConnection(Request $request)
    {
        $sessionId = session('whatsapp_session_id');
        
        if (!$sessionId) {
            return response()->json([
                'success' => false,
                'connected' => false,
                'message' => 'No active session found.'
            ]);
        }
        
        $profile = WhatsappProfile::where('session_id', $sessionId)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($profile && $profile->status === 'connected') {
            return response()->json([
                'success' => true,
                'connected' => true,
                'profile' => $profile,
                'message' => 'WhatsApp connected successfully!'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'connected' => false,
            'message' => 'Waiting for WhatsApp connection...'
        ]);
    }
    
    public function webhook(Request $request)
    {
        $data = $request->all();
        
        if (isset($data['type']) && $data['type'] === 'qr_scanned') {
            $this->handleQrScanned($data);
        } elseif (isset($data['type']) && $data['type'] === 'message') {
            $this->handleIncomingMessage($data);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function disconnect(Request $request, $profileId)
    {
        $profile = WhatsappProfile::where('id', $profileId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $profile->update([
            'status' => 'disconnected',
            'is_active' => false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'WhatsApp profile disconnected successfully.'
        ]);
    }
    
    public function getConversations(Request $request, $profileId)
    {
        $conversations = Conversation::where('whatsapp_profile_id', $profileId)
            ->whereHas('whatsappProfile', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'conversations' => $conversations
        ]);
    }
    
    public function getMessages(Request $request, $conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->whereHas('whatsappProfile', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
    
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:4096'
        ]);
        
        $conversation = Conversation::whereHas('whatsappProfile', function($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($conversationId);
        
        $message = Message::create([
            'conversation_id' => $conversationId,
            'whatsapp_profile_id' => $conversation->whatsapp_profile_id,
            'message_id' => 'msg_' . Str::uuid(),
            'sender' => 'outgoing',
            'content' => $request->message,
            'type' => 'text',
            'status' => 'pending',
            'timestamp' => now()
        ]);
        
        $conversation->update([
            'last_message_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    private function generateQrCodeData($sessionId)
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($sessionId);
    }
    
    private function handleQrScanned($data)
    {
        $sessionId = $data['session_id'] ?? null;
        
        if (!$sessionId) {
            return;
        }
        
        $userId = session('user_id');
        
        WhatsappProfile::updateOrCreate(
            [
                'session_id' => $sessionId,
            ],
            [
                'user_id' => $userId,
                'name' => $data['name'] ?? 'WhatsApp User',
                'phone_number' => $data['phone'] ?? 'Unknown',
                'profile_picture' => $data['profile_picture'] ?? null,
                'status' => 'connected',
                'is_active' => true,
                'last_connected_at' => now()
            ]
        );
    }
    
    private function handleIncomingMessage($data)
    {
        $profileId = $data['profile_id'] ?? null;
        $fromNumber = $data['from'] ?? null;
        
        if (!$profileId || !$fromNumber) {
            return;
        }
        
        $conversation = Conversation::firstOrCreate(
            [
                'whatsapp_profile_id' => $profileId,
                'contact_number' => $fromNumber
            ],
            [
                'contact_name' => $data['contact_name'] ?? $fromNumber,
                'last_message_at' => now()
            ]
        );
        
        Message::create([
            'conversation_id' => $conversation->id,
            'whatsapp_profile_id' => $profileId,
            'message_id' => $data['message_id'] ?? 'msg_' . Str::uuid(),
            'sender' => 'incoming',
            'content' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'text',
            'status' => 'received',
            'timestamp' => $data['timestamp'] ?? now()
        ]);
        
        $conversation->update([
            'last_message_at' => now()
        ]);
    }
}
