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
        DB::table('students')->where('status', 'inactive')->update(['status' => 'archived']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('students')->where('status', 'archived')->update(['status' => 'inactive']);
    }
};
