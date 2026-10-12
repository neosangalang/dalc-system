<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- 1. Add this import!

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==========================================
// CUSTOM SYSTEM AUTOMATIONS
// ==========================================

// 2. Tell the system to run your new robot every night at Midnight
Schedule::command('system:auto-rollover')->dailyAt('00:00');

// 3. Automated daily database backup at 1:00 AM (14-day rotation)
Schedule::command('db:backup')->dailyAt('01:00');