<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicQuarter; 
use App\Models\Report;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoQuarterRollover extends Command
{
    // The terminal command name
    protected $signature = 'system:auto-rollover {--force : Force the rollover execution regardless of whether today is the quarter end date}';

    // The description of what it does
    protected $description = 'Checks if today is the end of a quarter, archives reports, and automatically purges daily reports while permanently preserving raw teacher daily logs.';

    public function handle()
    {
        AcademicQuarter::syncActiveStatus();

        // 1. Get the current active quarter from your database
        $currentQuarter = AcademicQuarter::where('is_active', true)->first();

        if (!$currentQuarter) {
            $this->error('No active quarter found in the system.');
            return;
        }

        // 2. Check if TODAY matches the end date of the quarter (or if --force is provided)
        if ($this->option('force') || Carbon::today()->isSameDay(Carbon::parse($currentQuarter->end_date))) {
            
            $this->info("Today is the end of {$currentQuarter->name}. Starting automatic rollover...");
            Log::info("SYSTEM: Automatic Quarterly Rollover triggered for {$currentQuarter->name}.");

            // 3. Archive all approved reports up to this quarter's end date (excluding daily reports)
            $archivedCount = Report::where('status', 'approved')
                                  ->where('report_type', '!=', 'daily')
                                  ->whereDate('created_at', '<=', $currentQuarter->end_date)
                                  ->update(['status' => 'archived']);

            Log::info("SYSTEM: {$archivedCount} approved reports archived for {$currentQuarter->name}.");
            $this->info("{$archivedCount} reports archived.");

            // 3b. Automatically purge the Daily Reports for that quarter to keep the active dashboards clean and optimized,
            // while permanently preserving the raw teacher Daily Logs in the database as the permanent historical record.
            $dailyReportsQuery = Report::where('report_type', 'daily');

            if ($currentQuarter->start_date && $currentQuarter->end_date) {
                $startDate = Carbon::parse($currentQuarter->start_date)->startOfDay();
                $endDate   = Carbon::parse($currentQuarter->end_date)->endOfDay();
                $dailyReportsQuery->where(function($q) use ($currentQuarter, $startDate, $endDate) {
                    $q->whereBetween('report_date', [$currentQuarter->start_date, $currentQuarter->end_date])
                      ->orWhere(function($sub) use ($startDate, $endDate) {
                          $sub->whereNull('report_date')
                              ->whereBetween('created_at', [$startDate, $endDate]);
                      });
                });
            } else {
                $dailyReportsQuery->where(function($q) use ($currentQuarter) {
                    $q->whereDate('report_date', '<=', $currentQuarter->end_date)
                      ->orWhere(function($sub) use ($currentQuarter) {
                          $sub->whereNull('report_date')
                              ->whereDate('created_at', '<=', $currentQuarter->end_date);
                      });
                });
            }

            $dailyReportIds = (clone $dailyReportsQuery)->pluck('id');
            if ($dailyReportIds->isNotEmpty()) {
                Comment::where('commentable_type', Report::class)
                    ->whereIn('commentable_id', $dailyReportIds)
                    ->delete();
            }

            $purgedDailyCount = $dailyReportsQuery->delete();

            Log::info("SYSTEM: {$purgedDailyCount} daily reports purged for {$currentQuarter->name}, raw teacher daily logs permanently preserved.");
            $this->info("{$purgedDailyCount} daily reports purged (raw teacher daily logs permanently preserved).");

            // 4. Move to the next quarter within the same school year
            $currentQuarter->update(['is_active' => false]);
            
            $nextQuarter = AcademicQuarter::where('school_year_id', $currentQuarter->school_year_id)
                ->where('id', '>', $currentQuarter->id)
                ->orderBy('id', 'asc')
                ->first();
            if ($nextQuarter) {
                $nextQuarter->update(['is_active' => true]);
                Log::info("SYSTEM: Successfully moved to {$nextQuarter->name}.");
                $this->info("Moved to {$nextQuarter->name}.");
            } else {
                Log::warning("SYSTEM: No next quarter found after {$currentQuarter->name}.");
                $this->warn('No next quarter found. Please create new quarters.');
            }

            $this->info('Rollover complete!');
        } else {
            $this->info('Today is not the end of the quarter. No action needed.');
        }
    }
}