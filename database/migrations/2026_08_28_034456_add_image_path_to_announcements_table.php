<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This adds the image_path string column right after the content column
            $table->string('image_path')->nullable()->after('content');
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // This removes it if you ever need to rollback the database
            $table->dropColumn('image_path');
        });
    }
};