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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_manage_credentials')->default(false); // 1. Login Credentials
            $table->boolean('can_manage_calendar')->default(false);    // 2. Calendar Config
            $table->boolean('can_create_profiles')->default(false);    // 3. Initial Profile Creation
            $table->boolean('can_archive_students')->default(false);   // 4. Archiving
            $table->boolean('can_approve_reports')->default(false);    // 5. Admin Approval
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_credentials', 'can_manage_calendar', 
                'can_create_profiles', 'can_archive_students', 'can_approve_reports'
            ]);
        });
    }
};
