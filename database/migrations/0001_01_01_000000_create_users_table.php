<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name'); // الاسم الكامل
            $table->string('email')->unique();
            $table->string('phone_number')->nullable(); // رقم الهاتف
            $table->timestamp('email_verified_at')->nullable();
            
            $table->string('email_verification_token', 64)->nullable();
            $table->timestamp('verification_token_expires_at')->nullable(); 

            $table->string('password');
            
            // ربط المستخدم بجدول الأدوار (Foreign Key)
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            
            $table->string('status')->default('active'); // حالة الحساب (active / inactive)
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};