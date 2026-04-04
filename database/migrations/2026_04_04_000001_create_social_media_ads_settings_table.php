<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_media_ads_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Facebook Ads fields
            $table->text('facebook_access_token_encrypted')->nullable();
            $table->string('facebook_ad_account_id')->nullable();
            $table->string('facebook_page_id')->nullable();
            $table->string('facebook_business_id')->nullable();
            $table->timestamp('facebook_token_expires_at')->nullable();
            $table->boolean('facebook_connected')->default(false);
            
            // TikTok Ads fields
            $table->text('tiktok_access_token_encrypted')->nullable();
            $table->string('tiktok_advertiser_id')->nullable();
            $table->string('tiktok_app_id')->nullable();
            $table->timestamp('tiktok_token_expires_at')->nullable();
            $table->boolean('tiktok_connected')->default(false);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_ads_settings');
    }
};
