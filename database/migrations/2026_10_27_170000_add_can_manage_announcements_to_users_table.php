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
        if (!Schema::hasColumn('users', 'can_manage_announcements')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_manage_announcements')->default(false)->after('can_approve_reports');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'can_manage_announcements')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('can_manage_announcements');
            });
        }
    }
};
