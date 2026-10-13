<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
