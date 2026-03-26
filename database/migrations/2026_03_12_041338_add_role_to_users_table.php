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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['superadmin', 'customer'])->default('customer')->after('email');
            $table->string('company_name')->nullable()->after('name');
            $table->string('phone')->nullable()->after('company_name');
            $table->enum('subscription_plan', ['starter', 'advanced', 'professional', 'enterprise'])->nullable()->after('phone');
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_plan');
            $table->boolean('is_active')->default(true)->after('subscription_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'company_name', 'phone', 'subscription_plan', 'subscription_ends_at', 'is_active']);
        });
    }
};
