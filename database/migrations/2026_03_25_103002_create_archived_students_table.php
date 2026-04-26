<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('archived_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('school_year');
            $table->json('student_snapshot');   // full student data JSON
            $table->json('iep_snapshot');       // all IEP goals
            $table->json('progress_snapshot');  // all reports
            $table->string('master_pdf_path')->nullable();
            $table->timestamp('archived_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('archived_students');
    }
};
