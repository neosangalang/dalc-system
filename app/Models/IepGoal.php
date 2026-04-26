<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IepGoal extends Model
{
    use HasFactory;

   // Inside app/Models/IepGoal.php
protected $fillable = [
    'student_id',
    'teacher_id',
    'domain',
    'plop', // <-- ADD THIS NEW LINE
    'goal_description',
    'status'
];

    public function student() {
        return $this->belongsTo(Student::class);
    }
    // Add this to ProgressReport, IepGoal, and DailyLog models
public function comments()
{
    return $this->morphMany(Comment::class, 'commentable')->latest();
}
}