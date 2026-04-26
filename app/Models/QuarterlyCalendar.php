<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year', 'q1_start', 'q1_end', 'q2_start', 'q2_end', 
        'q3_start', 'q3_end', 'q4_start', 'q4_end'
    ];

    protected $casts = [
        'q1_start' => 'date', 'q1_end' => 'date',
        'q2_start' => 'date', 'q2_end' => 'date',
        'q3_start' => 'date', 'q3_end' => 'date',
        'q4_start' => 'date', 'q4_end' => 'date',
    ];
}