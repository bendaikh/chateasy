<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'whatsapp_profile_id',
        'contact_phone',
        'contact_name',
        'last_message',
        'last_message_at',
        'unread_count',
        'is_archived',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'is_archived' => 'boolean',
    ];

    public function whatsappProfile()
    {
        return $this->belongsTo(WhatsappProfile::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
