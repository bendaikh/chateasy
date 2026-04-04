<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaAdsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facebook_access_token_encrypted',
        'facebook_ad_account_id',
        'facebook_page_id',
        'facebook_business_id',
        'facebook_token_expires_at',
        'facebook_connected',
        'tiktok_access_token_encrypted',
        'tiktok_advertiser_id',
        'tiktok_app_id',
        'tiktok_token_expires_at',
        'tiktok_connected',
    ];

    protected $casts = [
        'facebook_token_expires_at' => 'datetime',
        'tiktok_token_expires_at' => 'datetime',
        'facebook_connected' => 'boolean',
        'tiktok_connected' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFacebookTokenValid(): bool
    {
        return $this->facebook_connected 
            && $this->facebook_token_expires_at 
            && $this->facebook_token_expires_at->isFuture();
    }

    public function isTikTokTokenValid(): bool
    {
        return $this->tiktok_connected 
            && $this->tiktok_token_expires_at 
            && $this->tiktok_token_expires_at->isFuture();
    }
}
