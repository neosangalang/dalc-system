<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->index('student_id');
        });

        // Migrate existing medical documents if any exist
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'medical_document')) {
            $existingStudents = DB::table('students')
                ->whereNotNull('medical_document')
                ->where('medical_document', '!=', '')
                ->get();

            foreach ($existingStudents as $student) {
                $filename = basename($student->medical_document);
                DB::table('student_documents')->insert([
                    'student_id'    => $student->id,
                    'file_path'     => $student->medical_document,
                    'original_name' => $filename,
                    'file_size'     => null,
                    'mime_type'     => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
