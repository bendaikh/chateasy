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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('whatsapp_profile_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['text', 'image', 'video', 'audio', 'document', 'location'])->default('text');
            $table->enum('direction', ['incoming', 'outgoing'])->default('incoming');
            $table->text('content')->nullable();
            $table->string('media_url')->nullable();
            $table->boolean('is_ai_response')->default(false);
            $table->boolean('is_read')->default(false);
            $table->string('whatsapp_message_id')->nullable();
            $table->timestamps();
            
            $table->index(['conversation_id', 'created_at']);
            $table->index('whatsapp_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
