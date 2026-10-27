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
        Schema::table('student_documents', function (Blueprint $table) {
            $table->foreignId('archived_student_id')
                ->nullable()
                ->after('student_id')
                ->constrained('archived_students')
                ->nullOnDelete();

            $table->boolean('is_archived')
                ->default(false)
                ->after('mime_type');

            $table->index(['student_id', 'is_archived']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'is_archived']);
            $table->dropForeign(['archived_student_id']);
            $table->dropColumn(['archived_student_id', 'is_archived']);
        });
    }
};
