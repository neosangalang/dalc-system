<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::table('academic_quarters', function (Blueprint $table) {
            $table->foreignId('school_year_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('school_years')
                  ->cascadeOnDelete();
        });

        // Migrate existing academic_quarters into an initial SchoolYear
        $existingQuarters = DB::table('academic_quarters')->get();
        if ($existingQuarters->isNotEmpty()) {
            $q1 = $existingQuarters->firstWhere('name', 'Q1') ?: $existingQuarters->first();
            if ($q1 && $q1->start_date) {
                $startYear = Carbon::parse($q1->start_date)->year;
                $name = 'SY ' . $startYear . '-' . ($startYear + 1);
            } else {
                $curY = (int) date('Y');
                $curM = (int) date('n');
                $name = 'SY ' . (($curM >= 6) ? $curY . '-' . ($curY + 1) : ($curY - 1) . '-' . $curY);
            }

            $schoolYearId = DB::table('school_years')->insertGetId([
                'name'       => $name,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('academic_quarters')->update(['school_year_id' => $schoolYearId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_quarters', function (Blueprint $table) {
            $table->dropForeign(['school_year_id']);
            $table->dropColumn('school_year_id');
        });

        Schema::dropIfExists('school_years');
    }
};
