<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IepGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'domain',
        'plop', 
        'goal_description',
        'progress_percentage', // <-- ADDED: This allows the progress bar to save!
        'status'
    ];

    public function student() 
    {
        return $this->belongsTo(Student::class);
    }
    
    // Add this to ProgressReport, IepGoal, and DailyLog models
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }
}