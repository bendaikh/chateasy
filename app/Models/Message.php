<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'whatsapp_profile_id',
        'type',
        'direction',
        'content',
        'media_url',
        'is_ai_response',
        'is_read',
        'whatsapp_message_id',
    ];

    protected $casts = [
        'is_ai_response' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function whatsappProfile()
    {
        return $this->belongsTo(WhatsappProfile::class);
    }
}
