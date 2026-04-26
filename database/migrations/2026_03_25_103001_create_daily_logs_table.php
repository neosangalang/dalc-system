<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('log_date');
            $table->string('quarter');
            $table->text('notes');
            $table->text('ai_generated_report')->nullable(); // Gemini will fill this in later!
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('daily_logs');
    }
};