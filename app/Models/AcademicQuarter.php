<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AcademicQuarter extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    /**
     * Automatically sync the is_active flag for ALL quarters
     * based on today's date vs each quarter's start/end range.
     */
    public static function syncActiveStatus(): void
    {
        $today = Carbon::today()->toDateString();

        // Set ALL quarters inactive first
        static::query()->update(['is_active' => false]);

        // Activate the quarter whose date range contains today
        static::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->update(['is_active' => true]);
    }

    /**
     * Get the quarter name (Q1, Q2, Q3, Q4) matching a specific date.
     */
    public static function getQuarterForDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $quarter = static::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        return $quarter ? $quarter->name : null;
    }

    /**
     * Computed status accessor for display purposes.
     * Returns: 'active', 'completed', 'upcoming', or 'not_set'
     */
    public function getComputedStatusAttribute(): string
    {
        if (empty($this->start_date) || empty($this->end_date)) {
            return 'not_set';
        }

        $today = Carbon::today();
        $start = Carbon::parse($this->start_date);
        $end   = Carbon::parse($this->end_date);

        if ($today->between($start, $end)) {
            return 'active';
        }

        if ($today->greaterThan($end)) {
            return 'completed';
        }

        return 'upcoming';
    }
}
