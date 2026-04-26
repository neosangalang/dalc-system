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
    'master_pdf_path'
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
}