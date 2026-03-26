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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_profile_id')->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('labels')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['whatsapp_profile_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
