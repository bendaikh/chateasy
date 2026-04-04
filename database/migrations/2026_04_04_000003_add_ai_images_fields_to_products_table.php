<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('ai_generated_images')->nullable()->after('images');
            $table->string('ai_images_status')->default('none')->after('ai_generated_images');
            // Possible values: 'none', 'pending', 'generating', 'completed', 'failed'
            $table->integer('ai_images_progress')->default(0)->after('ai_images_status');
            // Progress percentage (0-100)
            $table->integer('ai_images_total')->default(5)->after('ai_images_progress');
            // Total number of images to generate
            $table->integer('ai_images_generated')->default(0)->after('ai_images_total');
            // Number of images generated so far
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ai_generated_images',
                'ai_images_status',
                'ai_images_progress',
                'ai_images_total',
                'ai_images_generated'
            ]);
        });
    }
};
