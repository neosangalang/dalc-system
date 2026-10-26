<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the quarters associated with this school year.
     */
    public function quarters()
    {
        return $this->hasMany(AcademicQuarter::class)->orderBy('name', 'asc');
    }

    /**
     * Get the currently active school year.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Set this school year as active and deactivate all others.
     * Enforces the single-active-year invariant, then syncs quarters immediately.
     */
    public function setAsActive(): void
    {
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);

        // Immediately trigger the hybrid quarter sync
        AcademicQuarter::syncActiveStatus();
    }
}
