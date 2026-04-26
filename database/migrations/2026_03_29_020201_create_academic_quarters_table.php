<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
 {
     Schema::create('academic_quarters', function (Blueprint $table) {
         $table->id();
         $table->string('name'); // Will store 'Q1', 'Q2', etc.
         $table->date('start_date')->nullable();
         $table->date('end_date')->nullable();
         $table->boolean('is_active')->default(false);
         $table->timestamps();
     });
 }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_quarters');
    }
};
