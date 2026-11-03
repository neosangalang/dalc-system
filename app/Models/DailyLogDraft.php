<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLogDraft extends Model
{
    use HasFactory;

    protected $table = 'daily_log_drafts';

    protected $fillable = [
        'teacher_id',
        'student_id',
        'log_date',
        'quarter',
        'raw_notes',
        'formal_notes',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

