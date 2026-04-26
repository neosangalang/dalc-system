<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('class_name');
            $table->date('date_of_birth');
            $table->string('gender');
            $table->string('diagnosis'); 
            
            // CHANGED: This now stores the file path instead of text!
            $table->string('medical_document')->nullable(); 
            
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guardian_id')->constrained('users')->onDelete('cascade');
            
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};