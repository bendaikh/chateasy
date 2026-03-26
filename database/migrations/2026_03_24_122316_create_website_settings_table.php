<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Site Information
            $table->string('site_name')->default('My Store');
            $table->text('site_description')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            
            // Hero Section
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_button_text')->default('Shop Now');
            $table->string('hero_button_link')->default('/');
            $table->string('hero_background_color')->default('#eff6ff');
            
            // Top Banner
            $table->boolean('show_top_banner')->default(true);
            $table->string('banner_text')->nullable();
            $table->string('banner_icon')->default('local_fire_department');
            $table->string('banner_bg_color')->default('#f97316');
            
            // Colors & Theme
            $table->string('primary_color')->default('#10b981');
            $table->string('secondary_color')->default('#3b82f6');
            $table->string('accent_color')->default('#f59e0b');
            
            // Contact Information
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('whatsapp_number')->nullable();
            
            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('youtube_url')->nullable();
            
            // Footer
            $table->text('footer_about')->nullable();
            $table->string('footer_copyright')->nullable();
            
            // Features/Guarantees
            $table->json('features')->nullable();
            
            // SEO
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
