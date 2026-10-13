<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AcademicQuarter extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Automatically sync is_active status based on today's date vs start_date & end_date
     */
    public static function syncActiveStatus()
    {
        $today = Carbon::today();
        $quarters = static::orderBy('id', 'asc')->get();

        foreach ($quarters as $quarter) {
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

        if ($today->between($start, $end)) {
            return 'active';
        } elseif ($today->gt($end)) {
            return 'completed';
        } else {
            return 'upcoming';
        }
    }

    /**
     * Accessor for HTML5 date input format YYYY-MM-DD
     */
    public function getStartDateFormattedAttribute()
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d') : '';
    }

    /**
     * Accessor for HTML5 date input format YYYY-MM-DD
     */
    public function getEndDateFormattedAttribute()
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d') : '';
    }
}
