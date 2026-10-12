<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add composite indexes for scalability
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->index(['student_id', 'log_date'], 'daily_logs_student_date_idx');
            $table->index(['teacher_id', 'log_date'], 'daily_logs_teacher_date_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['student_id', 'status'], 'reports_student_status_idx');
            $table->index(['teacher_id', 'report_date'], 'reports_teacher_date_idx');
        });

        // 2. Encrypt any existing plaintext 2FA secrets
        $users = DB::table('users')->whereNotNull('two_factor_secret')->get();
        foreach ($users as $user) {
            $val = $user->two_factor_secret;
            // Check if already encrypted (JSON payload with iv, value, mac)
            $isEncrypted = false;
            try {
                $decoded = json_decode(base64_decode($val), true);
                if (is_array($decoded) && isset($decoded['iv'], $decoded['value'], $decoded['mac'])) {
                    $isEncrypted = true;
                }
            } catch (\Throwable $e) {
                $isEncrypted = false;
            }

            if (!$isEncrypted && !empty($val)) {
                $encrypted = Crypt::encryptString($val);
                DB::table('users')->where('id', $user->id)->update(['two_factor_secret' => $encrypted]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_logs', function (Blueprint $table) {
            $table->dropIndex('daily_logs_student_date_idx');
            $table->dropIndex('daily_logs_teacher_date_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_student_status_idx');
            $table->dropIndex('reports_teacher_date_idx');
        });

        // Decrypt 2FA secrets back if rolling down
        $users = DB::table('users')->whereNotNull('two_factor_secret')->get();
        foreach ($users as $user) {
            try {
                $decrypted = Crypt::decryptString($user->two_factor_secret);
                DB::table('users')->where('id', $user->id)->update(['two_factor_secret' => $decrypted]);
            } catch (\Throwable $e) {
                // Keep as is if not decryptable
            }
        }
    }
};
