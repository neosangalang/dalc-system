<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add raw_notes column to daily_logs table to permanently store informal quick notes
        if (Schema::hasTable('daily_logs') && !Schema::hasColumn('daily_logs', 'raw_notes')) {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->text('raw_notes')->nullable()->after('quarter');
            });
        }

        // 2. Create daily_log_drafts table to persistently store in-progress quick notes & formal notes per teacher, student, and date
        if (!Schema::hasTable('daily_log_drafts')) {
            Schema::create('daily_log_drafts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->date('log_date');
                $table->string('quarter', 10)->nullable();
                $table->text('raw_notes')->nullable();
                $table->text('formal_notes')->nullable();
                $table->timestamps();

                $table->unique(['teacher_id', 'student_id', 'log_date'], 'teacher_student_date_draft_unique');
                $table->index(['teacher_id', 'student_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('daily_log_drafts')) {
            Schema::dropIfExists('daily_log_drafts');
        }

        if (Schema::hasTable('daily_logs') && Schema::hasColumn('daily_logs', 'raw_notes')) {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->dropColumn('raw_notes');
            });
        }
    }
};

