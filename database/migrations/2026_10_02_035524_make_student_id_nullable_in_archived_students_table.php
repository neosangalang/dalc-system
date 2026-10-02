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
        Schema::table('archived_students', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreignId('student_id')->nullable()->change();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archived_students', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreignId('student_id')->nullable(false)->change();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }
};
