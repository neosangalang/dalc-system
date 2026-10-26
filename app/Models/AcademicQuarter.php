<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AcademicQuarter extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active', 'school_year_id'];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Parent School Year relationship
     */
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Hybrid Activation Logic:
     * Automatically sync is_active status based on today's date vs start_date & end_date,
     * with the condition that only quarters belonging to the active School Year can be active.
     */
    public static function syncActiveStatus()
    {
        $activeYear = SchoolYear::active();

        // 1. If school years exist but none is active, deactivate ALL quarters
        if (SchoolYear::exists() && !$activeYear) {
            static::query()->update(['is_active' => false]);
            return;
        }

        // 2. If an active school year exists, ensure quarters in other years are deactivated
        if ($activeYear) {
            static::where(function ($query) use ($activeYear) {
                $query->where('school_year_id', '!=', $activeYear->id)
                      ->orWhereNull('school_year_id');
            })->update(['is_active' => false]);

            $targetQuarters = static::where('school_year_id', $activeYear->id)->orderBy('id', 'asc')->get();
        } else {
            // Fallback for standalone tests without school years
            $targetQuarters = static::orderBy('id', 'asc')->get();
        }

        // 3. Auto-sync active status for target quarters based on today's date
        $today = Carbon::today();
        foreach ($targetQuarters as $quarter) {
            $shouldBeActive = false;
            if ($quarter->start_date && $quarter->end_date) {
                $start = Carbon::parse($quarter->start_date)->startOfDay();
                $end   = Carbon::parse($quarter->end_date)->endOfDay();
                if ($today->between($start, $end)) {
                    $shouldBeActive = true;
                }
            }

            if ((bool) $quarter->is_active !== $shouldBeActive) {
                $quarter->update(['is_active' => $shouldBeActive]);
            }
        }
    }

    /**
     * Accessor for $quarter->computed_status
     */
    public function getComputedStatusAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'not_set';
        }

        $today = Carbon::today();
        $start = Carbon::parse($this->start_date)->startOfDay();
        $end   = Carbon::parse($this->end_date)->endOfDay();

        $isParentActive = $this->school_year_id ? ($this->schoolYear?->is_active ?? false) : true;

        if ($today->between($start, $end)) {
            return $isParentActive ? 'active' : 'standby';
        } elseif ($today->gt($end)) {
            return 'completed';
        } else {
            return 'upcoming';
        }
    }

    /**
     * Accessor for HTML5 date input format YYYY-MM-DD
     */
    public function getStartDateFormattedAttribute(): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : '';
    }

    /**
     * Accessor for HTML5 date input format YYYY-MM-DD
     */
    public function getEndDateFormattedAttribute(): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : '';
    }
}
