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
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'medical_document')) {
            // Ensure any unmigrated medical documents are safely copied to student_documents
            if (Schema::hasTable('student_documents')) {
                $studentsWithDocs = DB::table('students')
                    ->whereNotNull('medical_document')
                    ->where('medical_document', '!=', '')
                    ->get();

                foreach ($studentsWithDocs as $student) {
                    $alreadyExists = DB::table('student_documents')
                        ->where('student_id', $student->id)
                        ->where('file_path', $student->medical_document)
                        ->exists();

                    if (!$alreadyExists) {
                        DB::table('student_documents')->insert([
                            'student_id'    => $student->id,
                            'file_path'     => $student->medical_document,
                            'original_name' => basename($student->medical_document),
                            'file_size'     => null,
                            'mime_type'     => null,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('medical_document');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'medical_document')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('medical_document')->nullable();
            });
        }
    }
};
