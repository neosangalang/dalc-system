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
        'contact_number',      // <-- ADDED THIS: Now Laravel will save the contact number!
        'is_active',
        'can_edit_students', 
        'password_changed_at', 
        'two_factor_secret',   
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
            'can_edit_students' => 'boolean', 
            'can_manage_credentials' => 'boolean',
            'can_manage_calendar' => 'boolean',
            'can_create_profiles' => 'boolean',
            'can_archive_students' => 'boolean',
            'can_approve_reports' => 'boolean',
        ];
    }

    
    // Relationship: A Guardian has many students (Useful if a parent has 2+ children enrolled)
    public function children()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    // Relationship: A Guardian has one student (Fallback/Singular relationship)
    public function guardianStudent()
    {
        return $this->hasOne(Student::class, 'guardian_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}