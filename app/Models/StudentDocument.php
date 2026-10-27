<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'archived_student_id',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'is_archived',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function archivedStudent()
    {
        return $this->belongsTo(ArchivedStudent::class, 'archived_student_id');
    }
}
