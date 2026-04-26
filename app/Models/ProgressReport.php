<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'teacher_id', 
        'quarter', 
        'summary_text', 
        'status'
    ];

    // This fixes the "Call to undefined relationship 'student'" error
    public function student() {
        return $this->belongsTo(Student::class);
    }

    // This fixes the "Call to undefined relationship 'teacher'" error
    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    // A single report can have many comments underneath it
    // Add this to ProgressReport, IepGoal, and DailyLog models
public function comments()
{
    return $this->morphMany(Comment::class, 'commentable')->latest();
}
}