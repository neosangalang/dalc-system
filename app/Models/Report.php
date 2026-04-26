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
        'status',
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
}