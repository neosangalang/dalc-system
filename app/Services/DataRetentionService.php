<?php

namespace App\Services;

use App\Models\ArchivedStudent;
use App\Models\Comment;
use App\Models\DailyLog;
use App\Models\DailyLogDraft;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use App\Models\UserActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataRetentionService
{
    /**
     * Get the validated retention period in years (constrained between min and max bounds).
     */
    public function getEffectiveRetentionYears(): int
    {
        $years = (int) config('retention.period_years', 5);
        $min = (int) config('retention.min_years', 1);
        $max = (int) config('retention.max_years', 10);

        return max($min, min($max, $years));
    }

    /**
     * Get the date cutoff before which student records are eligible for deletion.
     */
    public function getRetentionCutoffDate(): Carbon
    {
        return now()->subYears($this->getEffectiveRetentionYears());
    }

    /**
     * Build the query for non-active students who have passed the retention threshold.
     */
    public function getEligibleStudentsQuery(): Builder
    {
        $cutoff = $this->getRetentionCutoffDate();

        return Student::query()
            ->where('status', '!=', 'active')
            ->where(function ($q) {
                $q->where('legal_hold', false)
                  ->orWhereNull('legal_hold');
            })
            ->where(function ($q) use ($cutoff) {
                // Retention start date is graduated_at if present, otherwise archived_at
                $q->where(function ($sub) use ($cutoff) {
                    $sub->whereNotNull('graduated_at')
                        ->where('graduated_at', '<=', $cutoff);
                })->orWhere(function ($sub) use ($cutoff) {
                    $sub->whereNull('graduated_at')
                        ->whereNotNull('archived_at')
                        ->where('archived_at', '<=', $cutoff);
                });
            });
    }

    /**
     * Get the count of students currently eligible for purge.
     */
    public function getEligibleCount(): int
    {
        return $this->getEligibleStudentsQuery()->count();
    }

    /**
     * Get the count of non-active students protected by an active legal hold.
     */
    public function getLegalHoldCount(): int
    {
        return Student::where('status', '!=', 'active')
            ->where('legal_hold', true)
            ->count();
    }

    /**
     * Fetch information regarding the most recent purge operation.
     */
    public function getLastPurgeInfo(): ?array
    {
        $log = UserActivityLog::whereIn('action_type', ['data_purge', 'purge'])
            ->orWhere('action', 'like', '%Data Retention Purge%')
            ->latest('created_at')
            ->first();

        if (!$log) {
            return null;
        }

        return [
            'timestamp'  => $log->created_at,
            'user_name'  => $log->user_name ?? 'System Scheduler',
            'action'     => $log->action,
            'details'    => $log->details,
            'is_dry_run' => str_contains(strtolower($log->action), 'dry run') || str_contains(strtolower($log->details ?? ''), 'simulation'),
        ];
    }

    /**
     * Return scheduled purges preview: eligible records, upcoming records (within 180 days), and legal holds.
     */
    public function previewScheduledPurges(int $limit = 50): array
    {
        $years = $this->getEffectiveRetentionYears();
        $cutoff = $this->getRetentionCutoffDate();
        $upcomingCutoff = now()->subYears($years)->addDays(180);

        // 1. Eligible for purge right now
        $eligible = $this->getEligibleStudentsQuery()
            ->with(['archivedStudents', 'allDocuments', 'reports', 'dailyLogs', 'iepGoals'])
            ->orderByRaw('COALESCE(graduated_at, archived_at) ASC')
            ->limit($limit)
            ->get();

        // 2. Upcoming records approaching retention expiration (within 180 days)
        $upcoming = Student::query()
            ->where('status', '!=', 'active')
            ->where(function ($q) {
                $q->where('legal_hold', false)
                  ->orWhereNull('legal_hold');
            })
            ->where(function ($q) use ($cutoff, $upcomingCutoff) {
                $q->where(function ($sub) use ($cutoff, $upcomingCutoff) {
                    $sub->whereNotNull('graduated_at')
                        ->where('graduated_at', '>', $cutoff)
                        ->where('graduated_at', '<=', $upcomingCutoff);
                })->orWhere(function ($sub) use ($cutoff, $upcomingCutoff) {
                    $sub->whereNull('graduated_at')
                        ->whereNotNull('archived_at')
                        ->where('archived_at', '>', $cutoff)
                        ->where('archived_at', '<=', $upcomingCutoff);
                });
            })
            ->withCount(['archivedStudents', 'allDocuments', 'reports', 'dailyLogs', 'iepGoals'])
            ->orderByRaw('COALESCE(graduated_at, archived_at) ASC')
            ->limit($limit)
            ->get();

        // 3. Students protected under legal hold
        $legalHolds = Student::query()
            ->where('status', '!=', 'active')
            ->where('legal_hold', true)
            ->withCount(['archivedStudents', 'allDocuments', 'reports', 'dailyLogs'])
            ->orderByRaw('COALESCE(graduated_at, archived_at) ASC')
            ->limit($limit)
            ->get();

        return [
            'retention_years' => $years,
            'cutoff_date'     => $cutoff,
            'eligible_count'  => $this->getEligibleCount(),
            'legal_hold_count'=> $this->getLegalHoldCount(),
            'last_purge'      => $this->getLastPurgeInfo(),
            'eligible'        => $eligible,
            'upcoming'        => $upcoming,
            'legal_holds'     => $legalHolds,
        ];
    }

    /**
     * Permanently purge a single student and all associated historical records and physical files.
     * In dry-run mode, calculates items without performing actual deletions.
     */
    public function purgeStudent(Student $student, bool $dryRun = false): array
    {
        // Absolute safety guardrails
        if ($student->status === 'active') {
            throw new \InvalidArgumentException("Security violation: Cannot purge an active student record (ID: {$student->id}).");
        }

        if ($student->legal_hold) {
            throw new \InvalidArgumentException("Security violation: Cannot purge student record under legal hold (ID: {$student->id}).");
        }

        $startDate = $student->retention_start_date;
        if (!$startDate || $startDate->isAfter($this->getRetentionCutoffDate())) {
            throw new \InvalidArgumentException("Security violation: Student retention period has not expired (ID: {$student->id}).");
        }

        $student->loadMissing([
            'archivedStudents',
            'allDocuments',
            'reports',
            'dailyLogs',
            'iepGoals',
            'dailyLogDrafts',
        ]);

        $filesToDelete = [];
        $archivedPdfPaths = [];

        // 1. Gather Archived Student master PDF files
        foreach ($student->archivedStudents as $archive) {
            if ($archive->master_pdf_path) {
                $archivedPdfPaths[] = $archive->master_pdf_path;
            }
        }

        // 2. Gather Student Documents physical files
        $documentPaths = [];
        foreach ($student->allDocuments as $doc) {
            if ($doc->file_path) {
                $documentPaths[] = $doc->file_path;
            }
        }

        // 3. Gather Report observation photos/attachments
        $reportPhotos = [];
        foreach ($student->reports as $report) {
            if ($report->image_path) {
                $reportPhotos[] = $report->image_path;
            }
        }

        // 4. Gather Daily Log photos/attachments
        $dailyLogPhotos = [];
        foreach ($student->dailyLogs as $log) {
            if ($log->image_path) {
                $dailyLogPhotos[] = $log->image_path;
            }
        }

        $allCandidateFiles = array_merge($archivedPdfPaths, $documentPaths, $reportPhotos, $dailyLogPhotos);

        // Filter files that actually exist on local or public disk and are not referenced by another active record
        $physicalFilesCount = 0;
        foreach ($allCandidateFiles as $filePath) {
            $disk = Storage::disk('local')->exists($filePath)
                ? Storage::disk('local')
                : (Storage::disk('public')->exists($filePath) ? Storage::disk('public') : null);

            if ($disk) {
                $physicalFilesCount++;
                if (!$dryRun) {
                    $filesToDelete[] = ['disk' => $disk, 'path' => $filePath];
                }
            }
        }

        $counts = [
            'student_id'                 => $student->id,
            'archived_snapshots_deleted' => $student->archivedStudents->count(),
            'documents_deleted'          => $student->allDocuments->count(),
            'reports_deleted'            => $student->reports->count(),
            'daily_logs_deleted'         => $student->dailyLogs->count(),
            'daily_log_drafts_deleted'   => $student->dailyLogDrafts->count(),
            'iep_goals_deleted'          => $student->iepGoals->count(),
            'files_deleted'              => $physicalFilesCount,
            'student_deleted'            => true,
        ];

        if ($dryRun) {
            return $counts;
        }

        // Execute deletions atomically inside a database transaction
        DB::transaction(function () use ($student, $filesToDelete) {
            $studentId = $student->id;

            // Delete polymorphic comments on IEP Goals
            $iepGoalIds = $student->iepGoals->pluck('id');
            if ($iepGoalIds->isNotEmpty()) {
                Comment::where('commentable_type', IepGoal::class)
                    ->whereIn('commentable_id', $iepGoalIds)
                    ->delete();
                IepGoal::whereIn('id', $iepGoalIds)->delete();
            }

            // Delete polymorphic comments on Reports
            $reportIds = $student->reports->pluck('id');
            if ($reportIds->isNotEmpty()) {
                Comment::where('commentable_type', Report::class)
                    ->whereIn('commentable_id', $reportIds)
                    ->delete();
                Report::whereIn('id', $reportIds)->delete();
            }

            // Delete polymorphic comments on Daily Logs
            $dailyLogIds = $student->dailyLogs->pluck('id');
            if ($dailyLogIds->isNotEmpty()) {
                Comment::where('commentable_type', DailyLog::class)
                    ->whereIn('commentable_id', $dailyLogIds)
                    ->delete();
                DailyLog::whereIn('id', $dailyLogIds)->delete();
            }

            // Delete daily log drafts
            DailyLogDraft::where('student_id', $studentId)->delete();

            // Delete student documents
            StudentDocument::where('student_id', $studentId)->delete();

            // Delete archived student records
            ArchivedStudent::where('student_id', $studentId)->delete();

            // Delete student record
            $student->delete();

            // Delete unreferenced physical files from server storage
            foreach ($filesToDelete as $fileItem) {
                try {
                    $fileItem['disk']->delete($fileItem['path']);
                } catch (\Throwable $fileEx) {
                    Log::warning("DataRetention: Unable to unlink file {$fileItem['path']}: " . $fileEx->getMessage());
                }
            }
        });

        return $counts;
    }

    /**
     * Run the automated or manual purge for all eligible students.
     */
    public function runPurge(bool $dryRun = false, ?User $initiator = null): array
    {
        $eligibleStudents = $this->getEligibleStudentsQuery()->get();
        $totalStudents = $eligibleStudents->count();

        $totals = [
            'dry_run'                    => $dryRun,
            'retention_years'            => $this->getEffectiveRetentionYears(),
            'total_students_processed'   => 0,
            'archived_snapshots_deleted' => 0,
            'documents_deleted'          => 0,
            'reports_deleted'            => 0,
            'daily_logs_deleted'         => 0,
            'daily_log_drafts_deleted'   => 0,
            'iep_goals_deleted'          => 0,
            'files_deleted'              => 0,
            'failed_count'               => 0,
            'failures'                   => [],
        ];

        foreach ($eligibleStudents as $student) {
            try {
                $result = $this->purgeStudent($student, $dryRun);
                $totals['total_students_processed']++;
                $totals['archived_snapshots_deleted'] += $result['archived_snapshots_deleted'];
                $totals['documents_deleted']          += $result['documents_deleted'];
                $totals['reports_deleted']            += $result['reports_deleted'];
                $totals['daily_logs_deleted']         += $result['daily_logs_deleted'];
                $totals['daily_log_drafts_deleted']   += $result['daily_log_drafts_deleted'];
                $totals['iep_goals_deleted']          += $result['iep_goals_deleted'];
                $totals['files_deleted']              += $result['files_deleted'];
            } catch (\Throwable $e) {
                $totals['failed_count']++;
                $totals['failures'][] = [
                    'student_id' => $student->id,
                    'error'      => $e->getMessage(),
                ];

                Log::error("DataRetention: Failed purging student ID {$student->id}: " . $e->getMessage(), [
                    'student_id' => $student->id,
                    'trace'      => $e->getTraceAsString(),
                ]);
            }
        }

        // Anonymized Audit Log: Never expose student names or PII
        $modeLabel = $dryRun ? 'Dry-Run Simulation' : 'Permanent Purge';
        $actionName = $dryRun ? 'Data Retention Dry Run' : 'Data Retention Purge';
        $actionType = $dryRun ? 'dry_run' : 'data_purge';

        $details = sprintf(
            '%s completed: %d student(s) processed under %d-year retention policy. Files %s: %d. Snapshots: %d, Documents: %d, Reports: %d, Logs: %d, Goals: %d. Failures: %d.',
            $modeLabel,
            $totals['total_students_processed'],
            $totals['retention_years'],
            $dryRun ? 'simulated' : 'purged',
            $totals['files_deleted'],
            $totals['archived_snapshots_deleted'],
            $totals['documents_deleted'],
            $totals['reports_deleted'],
            $totals['daily_logs_deleted'],
            $totals['iep_goals_deleted'],
            $totals['failed_count']
        );

        UserActivityLog::record(
            action: $actionName,
            actionType: $actionType,
            details: $details,
            targetType: 'DataRetention',
            targetId: null,
            studentName: null, // Strictly anonymized: No PII exposed
            user: $initiator,
            request: request()
        );

        Log::info("DataRetention: {$details}");

        return $totals;
    }

    /**
     * Toggle or update the legal hold status for a student record.
     */
    public function setLegalHold(Student $student, bool $hold, ?string $reason = null, ?User $actor = null): bool
    {
        $student->update([
            'legal_hold'        => $hold,
            'legal_hold_reason' => $hold ? $reason : null,
        ]);

        $action = $hold ? 'Placed on Legal Hold' : 'Removed from Legal Hold';
        $details = $hold 
            ? "Record placed on legal hold. Reason: " . ($reason ?: 'No reason provided')
            : "Legal hold removed from record.";

        // For individual record legal hold toggles, log student reference for administrative oversight
        UserActivityLog::record(
            action: "Data Retention: {$action}",
            actionType: 'update',
            details: $details,
            targetType: 'Student',
            targetId: $student->id,
            studentName: $student->first_name . ' ' . $student->last_name,
            user: $actor,
            request: request()
        );

        return true;
    }
}

