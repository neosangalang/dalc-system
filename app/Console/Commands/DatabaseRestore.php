<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {file? : The backup filename to restore} {--force : Bypass confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the MySQL database from a backup file in storage/app/backups.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            $this->error('No backups directory found.');
            return Command::FAILURE;
        }

        $allBackups = glob($backupDir . DIRECTORY_SEPARATOR . 'dalc_backup_*.sql*');
        if (empty($allBackups)) {
            $this->error('No database backups found in ' . $backupDir);
            return Command::FAILURE;
        }

        // Sort latest first
        usort($allBackups, fn($a, $b) => filemtime($b) <=> filemtime($a));

        $chosenFile = $this->argument('file');
        if ($chosenFile) {
            $targetPath = $backupDir . DIRECTORY_SEPARATOR . basename($chosenFile);
            if (!file_exists($targetPath)) {
                $this->error("Backup file not found: {$chosenFile}");
                return Command::FAILURE;
            }
        } else {
            $options = array_map('basename', $allBackups);
            if ($this->option('force')) {
                $targetPath = $allBackups[0]; // pick latest
            } else {
                $chosenName = $this->choice('Select backup to restore', $options, 0);
                $targetPath = $backupDir . DIRECTORY_SEPARATOR . $chosenName;
            }
        }

        $filename = basename($targetPath);
        if (!$this->option('force')) {
            if (!$this->confirm("WARNING: This will overwrite tables in the database with contents of [{$filename}]. Continue?")) {
                $this->warn('Restore cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info("Restoring database from [{$filename}]...");

        $tempSqlFile = null;
        if (str_ends_with($targetPath, '.gz')) {
            $compressedContent = file_get_contents($targetPath);
            $sqlContent = gzdecode($compressedContent);
            if ($sqlContent === false) {
                $this->error('Failed to decompress gzip backup file.');
                return Command::FAILURE;
            }
            $tempSqlFile = $backupDir . DIRECTORY_SEPARATOR . 'temp_restore_' . time() . '.sql';
            file_put_contents($tempSqlFile, $sqlContent);
            $pathToExecute = $tempSqlFile;
        } else {
            $pathToExecute = $targetPath;
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Read and execute SQL statements
            $sql = file_get_contents($pathToExecute);
            DB::unprepared($sql);
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info("Database restore completed successfully from: {$filename}");
            Log::info("Database successfully restored from backup: {$filename}");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Database restore error: ' . $e->getMessage());
            Log::error('Database restore failed: ' . $e->getMessage());
            return Command::FAILURE;
        } finally {
            if ($tempSqlFile && file_exists($tempSqlFile)) {
                @unlink($tempSqlFile);
            }
        }
    }
}
