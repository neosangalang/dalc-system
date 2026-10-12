<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--retention=14 : Number of days to retain backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform an automated backup of the database and rotate old backups.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting automated database backup...');

        $dbConnection = config('database.default');
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $sqlFilename = "dalc_backup_{$timestamp}.sql";
        $sqlPath = $backupDir . DIRECTORY_SEPARATOR . $sqlFilename;

        $dumped = false;

        if ($dbConnection === 'mysql') {
            $dbConfig = config("database.connections.{$dbConnection}");
            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? 3306;
            $database = $dbConfig['database'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'] ?? '';

            $dumped = $this->tryMysqldump($host, $port, $database, $username, $password, $sqlPath);
            if (!$dumped) {
                $this->warn('mysqldump executable not available or failed. Falling back to native PDO database export...');
                $dumped = $this->nativePdoDump($sqlPath, 'mysql');
            }
        } elseif ($dbConnection === 'sqlite') {
            $dumped = $this->nativePdoDump($sqlPath, 'sqlite');
        } else {
            $this->error("Automated backup is not configured for driver: {$dbConnection}.");
            return Command::FAILURE;
        }

        if (!$dumped || !file_exists($sqlPath) || filesize($sqlPath) === 0) {
            $this->error('Database backup failed: Backup file could not be generated.');
            Log::error('Database backup failed to create valid dump.');
            return Command::FAILURE;
        }

        // Compress backup using gzip
        $gzPath = $sqlPath . '.gz';
        $content = file_get_contents($sqlPath);
        $compressed = gzencode($content, 9);
        file_put_contents($gzPath, $compressed);
        @unlink($sqlPath);

        $fileSizeKb = round(filesize($gzPath) / 1024, 2);
        $this->info("Backup successfully created: {$gzPath} ({$fileSizeKb} KB)");
        Log::info("Automated database backup created: {$gzPath} ({$fileSizeKb} KB)");

        // Rotate old backups
        $this->rotateOldBackups($backupDir, (int) $this->option('retention'));

        return Command::SUCCESS;
    }

    /**
     * Attempt to run mysqldump.
     */
    protected function tryMysqldump(string $host, $port, string $database, string $username, string $password, string $outputPath): bool
    {
        $mysqldumpPath = $this->findMysqldumpBinary();
        if (!$mysqldumpPath) {
            return false;
        }

        $command = [
            $mysqldumpPath,
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
        ];

        if (!empty($password)) {
            $command[] = "--password={$password}";
        }

        $command = array_merge($command, [
            '--quick',
            '--single-transaction',
            '--skip-lock-tables',
            $database,
        ]);

        try {
            $process = new Process($command);
            $process->setTimeout(300);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('mysqldump command failed: ' . $process->getErrorOutput());
                return false;
            }

            file_put_contents($outputPath, $process->getOutput());
            return true;
        } catch (\Throwable $e) {
            Log::warning('mysqldump exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Locate mysqldump binary in PATH or Laragon installations.
     */
    protected function findMysqldumpBinary(): ?string
    {
        $candidates = [
            'mysqldump',
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
        ];

        if (is_dir('C:\\laragon\\bin\\mysql')) {
            $dirs = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
            if (!empty($dirs)) {
                $candidates = array_merge($candidates, $dirs);
            }
        }

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysqldump') {
                $check = new Process(['where', 'mysqldump']);
                $check->run();
                if ($check->isSuccessful() && !empty(trim($check->getOutput()))) {
                    return 'mysqldump';
                }
            } elseif (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Native PHP PDO database dumper fallback (supports MySQL and SQLite).
     */
    protected function nativePdoDump(string $outputPath, string $driver = 'mysql'): bool
    {
        try {
            $handle = fopen($outputPath, 'w');
            if (!$handle) {
                return false;
            }

            fwrite($handle, "-- DALC System Automated Database Dump ({$driver})\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");

            if ($driver === 'mysql') {
                fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
                $tablesResult = DB::select('SHOW TABLES');
                $dbName = config('database.connections.mysql.database');
                $columnKey = 'Tables_in_' . $dbName;
                $tables = array_map(fn($t) => $t->$columnKey ?? array_values((array) $t)[0], $tablesResult);
            } else {
                $tablesResult = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tables = array_map(fn($t) => $t->name, $tablesResult);
            }

            foreach ($tables as $table) {
                if ($driver === 'mysql') {
                    $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                    $createSql = $createTable[0]->{'Create Table'} ?? null;
                    if ($createSql) {
                        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                        fwrite($handle, $createSql . ";\n\n");
                    }
                } else {
                    $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                    $createSql = $createTable[0]->sql ?? null;
                    if ($createSql) {
                        fwrite($handle, "DROP TABLE IF EXISTS \"{$table}\";\n");
                        fwrite($handle, $createSql . ";\n\n");
                    }
                }

                // Export table data
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    foreach ($rows->chunk(100) as $chunk) {
                        $inserts = [];
                        foreach ($chunk as $row) {
                            $values = array_map(function ($val) {
                                if (is_null($val)) return 'NULL';
                                return DB::getPdo()->quote($val);
                            }, (array) $row);
                            $inserts[] = '(' . implode(', ', $values) . ')';
                        }
                        if (!empty($inserts)) {
                            fwrite($handle, "INSERT INTO `{$table}` VALUES \n" . implode(",\n", $inserts) . ";\n");
                        }
                    }
                    fwrite($handle, "\n");
                }
            }

            if ($driver === 'mysql') {
                fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            }
            fclose($handle);

            return true;
        } catch (\Throwable $e) {
            Log::error('Native PDO database dump failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete backups older than specified retention days.
     */
    protected function rotateOldBackups(string $backupDir, int $retentionDays): void
    {
        $cutoffTime = time() - ($retentionDays * 86400);
        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'dalc_backup_*.sql*');

        $deletedCount = 0;
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                @unlink($file);
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Rotated {$deletedCount} backup(s) older than {$retentionDays} days.");
        }
    }
}
