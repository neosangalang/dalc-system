<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicQuarter; 
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoQuarterRollover extends Command
{
    // The terminal command name
    protected $signature = 'system:auto-rollover';

    // The description of what it does
    protected $description = 'Checks if today is the end of a quarter and automatically archives reports.';

    public function handle()
    {
        // 1. Get the current active quarter from your database
        $currentQuarter = AcademicQuarter::where('is_active', true)->first();

        if (!$currentQuarter) {
            $this->error('No active quarter found in the system.');
            return;
        }

        // 2. Check if TODAY matches the end date of the quarter
        if (Carbon::today()->isSameDay(Carbon::parse($currentQuarter->end_date))) {
            
            $this->info("Today is the end of {$currentQuarter->name}. Starting automatic rollover...");
            Log::info("SYSTEM: Automatic Quarterly Rollover triggered for {$currentQuarter->name}.");

            // 3. YOUR ARCHIVING LOGIC GOES HERE
            // (This is the same code you have inside your ArchiveController)
            
            // Example:
            // Report::where('quarter', $currentQuarter->name)
            //       ->where('status', 'approved')
            //       ->update(['is_archived' => true]);

            // 4. Move to the next quarter
            $currentQuarter->update(['is_active' => false]);
            
            $nextQuarter = AcademicQuarter::where('id', '>', $currentQuarter->id)->first();
            if ($nextQuarter) {
                $nextQuarter->update(['is_active' => true]);
                Log::info("SYSTEM: Successfully moved to {$nextQuarter->name}.");
            }

            $this->info('Rollover complete!');
        } else {
            $this->info('Today is not the end of the quarter. No action needed.');
        }
    }
}