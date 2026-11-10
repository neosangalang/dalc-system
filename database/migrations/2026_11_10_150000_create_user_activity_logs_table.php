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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_username')->nullable();
            $table->string('user_role')->nullable(); // 'Admin', 'SPED Teacher', 'Guardian'
            $table->string('action'); // e.g., 'User Login', 'User Logout', 'Password Reset', 'IEP Creation', 'Daily Log AI Formatting', 'Document Approval/Stamping', 'Student Archiving', 'File Download'
            $table->string('action_type')->index(); // e.g., 'login', 'logout', 'password_reset', 'iep_creation', 'ai_formatting', 'document_approval', 'document_rejection', 'student_archiving', 'file_download'
            $table->text('details')->nullable();
            $table->string('target_type')->nullable(); // 'Student', 'Report', 'IepGoal', 'DailyLog', 'User', 'ArchivedStudent', 'StudentDocument'
            $table->string('target_id')->nullable(); // Target Record ID
            $table->string('student_name')->nullable()->index(); // Associated student name for quick filtering
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('user_role');
            $table->index('target_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};

