<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'teacher_id', 'log_date', 'quarter', 'notes', 'ai_generated_report','home_recommendations','image_path'];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    // Add this to ProgressReport, IepGoal, and DailyLog models
public function comments()
{
    return $this->morphMany(Comment::class, 'commentable')->latest();
}
}