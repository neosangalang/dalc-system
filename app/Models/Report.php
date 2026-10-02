<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // This stops the "Mass Assignment" error!
    protected $fillable = [
        'student_id',
        'teacher_id',
        'report_type',
        'report_date',
        'content',
        'image_path',
        'status',
        'revision_notes',
        'approved_by',
        'approved_at',
    ];

    // Link the report to the Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Link the report to the Teacher
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Link the report to the Approver (Admin)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Link the report to its comments (Required for the comment thread to work)
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}