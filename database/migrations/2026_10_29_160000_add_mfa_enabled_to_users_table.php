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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mfa_enabled')) {
                $table->boolean('mfa_enabled')->default(false)->after('two_factor_secret');
            }
        });

        // Admin and Teacher accounts require MFA
        DB::table('users')->whereIn('role', ['admin', 'teacher'])->update(['mfa_enabled' => true]);

        // For existing guardians who already configured MFA, keep it enabled
        DB::table('users')->where('role', 'guardian')->whereNotNull('two_factor_secret')->update(['mfa_enabled' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mfa_enabled')) {
                $table->dropColumn('mfa_enabled');
            }
        });
    }
};

