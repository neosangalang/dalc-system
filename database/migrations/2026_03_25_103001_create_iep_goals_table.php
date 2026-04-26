<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iep_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('domain'); // e.g., Communication, Self-Care, Academic
            $table->text('goal_description');
            $table->integer('progress_percentage')->default(0);
            $table->enum('status', ['in_progress', 'achieved'])->default('in_progress');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('iep_goals');
    }
};