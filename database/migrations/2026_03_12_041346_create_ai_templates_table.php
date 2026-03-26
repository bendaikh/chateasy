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
        Schema::create('ai_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_profile_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('prompt');
            $table->json('trigger_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['auto_reply', 'greeting', 'order_confirmation', 'custom'])->default('custom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_templates');
    }
};
