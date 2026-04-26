<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

   protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'is_active',
        'can_edit_students', // <-- Added this
        'password_changed_at', // ADD THIS LINE!
        'two_factor_secret',   // ADD THIS LINE TOO!
        'can_manage_credentials',
        'can_manage_calendar',
        'can_create_profiles',
        'can_archive_students',
        'can_approve_reports',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'can_edit_students' => 'boolean', // <-- Added this
        ];
    }

    
    // Inside app/Models/User.php
    public function children()
    {
        // This assumes your students table has a 'guardian_id' column
        return $this->hasMany(Student::class, 'guardian_id');
    }

    // Relationship: A Guardian has one student (or many depending on your business logic, keeping it to one for now based on your blueprint)
    public function guardianStudent()
    {
        return $this->hasOne(Student::class, 'guardian_id');
    }
}