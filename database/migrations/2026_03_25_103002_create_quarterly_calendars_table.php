<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quarterly_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('school_year'); // e.g., 2024-2025
            $table->date('q1_start'); $table->date('q1_end');
            $table->date('q2_start'); $table->date('q2_end');
            $table->date('q3_start'); $table->date('q3_end');
            $table->date('q4_start'); $table->date('q4_end');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('quarterly_calendars');
    }
};