<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المشروع (مثل عيادة أسنان)
            $table->text('description')->nullable(); // تفاصيل المشروع
            $table->date('start_date')->nullable(); // تاريخ البدء
            $table->date('due_date')->nullable(); // تاريخ التسليم
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};