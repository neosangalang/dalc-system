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
    Schema::create('comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Who wrote it
        
        // This creates 'commentable_id' and 'commentable_type' automatically
        // This tells the database: "This comment belongs to DailyLog ID 5" or "IepGoal ID 2"
        $table->morphs('commentable'); 
        
        $table->text('body'); // The message
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
