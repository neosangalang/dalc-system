<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'class_name',
        'date_of_birth',
        'gender',
        'diagnosis',
        'medical_document', // <--- Changed here!
        'teacher_id',
        'guardian_id',
        'status'
    ];
    public function teacher() { 
        return $this->belongsTo(User::class, 'teacher_id'); 
    }
    
    public function guardian() { 
        return $this->belongsTo(User::class, 'guardian_id'); 
    }
    
    public function iepGoals() { 
        return $this->hasMany(IepGoal::class); 
    }
    
    public function dailyLogs() { 
        return $this->hasMany(DailyLog::class); 
    }
    
}