<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;

class PurgeExpiredStudentRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-retention:purge
                            {--dry-run : Simulate the purge without deleting database records or files}
                            {--force : Bypass confirmation prompt for non-interactive execution}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automated purge protocol for student records reaching the 5-year post-graduation/archival retention threshold.';

    /**
     * Execute the console command.
     */
    public function handle(DataRetentionService $retentionService): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $retentionYears = $retentionService->getEffectiveRetentionYears();

        $this->info("===============================================================================");
        $this->info("  DALC 5-Year Data Retention & Automated Purge Protocol");
        $this->info("===============================================================================");
        $this->line("Retention Period : {$retentionYears} Years");
        $this->line("Cutoff Date      : " . $retentionService->getRetentionCutoffDate()->toFormattedDateString());
        $this->line("Execution Mode   : " . ($isDryRun ? '<comment>DRY-RUN (Simulation)</comment>' : '<error>PERMANENT PURGE (Live Deletion)</error>'));

        $eligibleCount = $retentionService->getEligibleCount();
        $legalHoldCount = $retentionService->getLegalHoldCount();

        $this->line("Eligible Records : <info>{$eligibleCount}</info>");
        $this->line("Protected Records: <info>{$legalHoldCount}</info> (Active Legal Holds)");

        if ($eligibleCount === 0) {
            $this->info("No student records currently meet the retention purge criteria. System is up to date.");
            return self::SUCCESS;
        }

        if (!$isDryRun && !$force) {
            if (!$this->confirm("Are you sure you want to permanently delete {$eligibleCount} student record(s) and all historical files? This action CANNOT be undone.", false)) {
                $this->warn("Purge execution canceled by user.");
                return self::SUCCESS;
            }
        }

        $this->line("Executing " . ($isDryRun ? "dry-run simulation" : "permanent purge") . "...");

        $results = $retentionService->runPurge($isDryRun);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Mode', $results['dry_run'] ? 'Simulation (Dry-Run)' : 'Permanent Deletion'],
                ['Students Processed', $results['total_students_processed']],
                ['Archived Snapshots Removed', $results['archived_snapshots_deleted']],
                ['Documents Removed', $results['documents_deleted']],
                ['Progress Reports Removed', $results['reports_deleted']],
                ['Teacher Daily Logs Removed', $results['daily_logs_deleted']],
                ['IEP Goals Removed', $results['iep_goals_deleted']],
                ['Physical Files Removed', $results['files_deleted']],
                ['Failed Operations', $results['failed_count']],
            ]
        );

        if ($results['failed_count'] > 0) {
            $this->error("Encountered {$results['failed_count']} error(s) during execution. Check storage/logs for details.");
            return self::FAILURE;
        }

        $this->info($isDryRun ? "Dry-run simulation completed successfully. No records were modified." : "Data retention purge executed successfully.");
        return self::SUCCESS;
    }
}

