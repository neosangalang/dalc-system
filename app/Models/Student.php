<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'learners_reference_number',
        'class_name',
        'date_of_birth',
        'gender',
        'diagnosis',
        'teacher_id',
        'guardian_id',
        'status',
        'archived_at',
        'graduated_at',
        'legal_hold',
        'legal_hold_reason',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'archived_at'   => 'datetime',
        'graduated_at'  => 'datetime',
        'legal_hold'    => 'boolean',
    ];
    public function teacher() { 
        return $this->belongsTo(User::class, 'teacher_id'); 
    }
    
    public function guardian() { 
        return $this->belongsTo(User::class, 'guardian_id'); 
    }
    
    public function iepGoals() { 
        return $this->hasMany(IepGoal::class); 
    }
    
    public function dailyLogs() { 
        return $this->hasMany(DailyLog::class); 
    }
    
    public function reports() { 
        return $this->hasMany(Report::class); 
    }
    public function documents() {
        return $this->hasMany(StudentDocument::class)->where('is_archived', false);
    }

    public function allDocuments() {
        return $this->hasMany(StudentDocument::class);
    }

    public function archivedDocuments() {
        return $this->hasMany(StudentDocument::class)->where('is_archived', true);
    }

    public function archivedStudents() {
        return $this->hasMany(ArchivedStudent::class);
    }

    public function dailyLogDrafts() {
        return $this->hasMany(DailyLogDraft::class, 'student_id');
    }

    /**
     * Start point for the retention period (graduation date takes precedence, then archival date).
     */
    public function getRetentionStartDateAttribute()
    {
        return $this->graduated_at ?? $this->archived_at;
    }

    /**
     * The timestamp when this record reaches the retention expiry threshold.
     */
    public function getRetentionExpiryDateAttribute()
    {
        $startDate = $this->retention_start_date;
        if (!$startDate) {
            return null;
        }

        $years = (int) config('retention.period_years', 5);
        $min = (int) config('retention.min_years', 1);
        $max = (int) config('retention.max_years', 10);
        $effectiveYears = max($min, min($max, $years));

        return $startDate->copy()->addYears($effectiveYears);
    }

    /**
     * Determine whether the student record is currently eligible for automated or manual purge.
     */
    public function isEligibleForPurge(): bool
    {
        if ($this->status === 'active') {
            return false;
        }

        if ($this->legal_hold) {
            return false;
        }

        $expiry = $this->retention_expiry_date;
        if (!$expiry) {
            return false;
        }

        return $expiry->isPast();
    }

    /**
     * Calculate days remaining until retention expiration (negative if already expired).
     */
    public function daysUntilPurge(): ?int
    {
        $expiry = $this->retention_expiry_date;
        if (!$expiry) {
            return null;
        }

        return (int) now()->diffInDays($expiry, false);
    }
}