<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'commentable_id', 'commentable_type', 'body', 'is_teacher_read', 'is_guardian_read'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function commentable() {
        return $this->morphTo();
    }
}