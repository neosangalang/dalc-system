<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'graduated_at')) {
                $table->timestamp('graduated_at')->nullable()->after('archived_at');
            }
            if (!Schema::hasColumn('students', 'legal_hold')) {
                $table->boolean('legal_hold')->default(false)->after('graduated_at');
            }
            if (!Schema::hasColumn('students', 'legal_hold_reason')) {
                $table->string('legal_hold_reason')->nullable()->after('legal_hold');
            }
        });

        // Backfill existing archived students with an initial archival timestamp
        try {
            $archivedStudents = DB::table('students')->where('status', 'archived')->get();
            foreach ($archivedStudents as $student) {
                $latestArchive = DB::table('archived_students')
                    ->where('student_id', $student->id)
                    ->orderBy('archived_at', 'desc')
                    ->first();

                $archivedAt = $latestArchive?->archived_at ?? $student->updated_at ?? now();

                DB::table('students')
                    ->where('id', $student->id)
                    ->whereNull('archived_at')
                    ->update(['archived_at' => $archivedAt]);
            }
        } catch (\Throwable $e) {
            // Non-critical if table empty or during testing
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('students', 'legal_hold_reason')) {
                $columnsToDrop[] = 'legal_hold_reason';
            }
            if (Schema::hasColumn('students', 'legal_hold')) {
                $columnsToDrop[] = 'legal_hold';
            }
            if (Schema::hasColumn('students', 'graduated_at')) {
                $columnsToDrop[] = 'graduated_at';
            }
            if (Schema::hasColumn('students', 'archived_at')) {
                $columnsToDrop[] = 'archived_at';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

