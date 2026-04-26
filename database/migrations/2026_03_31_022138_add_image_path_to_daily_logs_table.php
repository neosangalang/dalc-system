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
        Schema::table('daily_logs', function (Blueprint $table) {
            // Adds a nullable string column to store the image location
            $table->string('image_path')->nullable()->after('home_recommendations');
        });
    }

    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
