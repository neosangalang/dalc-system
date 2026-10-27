<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'school_year', 
        'student_snapshot', 
        'iep_snapshot', 
        'progress_snapshot', 
        'master_pdf_path',
        'archived_at'
    ];

    protected $casts = [
        'student_snapshot' => 'array',
        'iep_snapshot' => 'array',
        'progress_snapshot' => 'array',
        'archived_at' => 'datetime',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function documents() {
        return $this->hasMany(StudentDocument::class, 'archived_student_id');
    }

    public function getStudentNameAttribute() {
        if ($this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        }
        $snap = $this->student_snapshot ?? [];
        return ($snap['first_name'] ?? 'Unknown') . ' ' . ($snap['last_name'] ?? 'Student');
    }
}