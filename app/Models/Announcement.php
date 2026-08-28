<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    // You MUST add 'image_path' here!
    protected $fillable = [
        'type',
        'title',
        'content',
        'image_path', 
    ];
}