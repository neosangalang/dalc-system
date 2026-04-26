<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    public function show($id)
    {
        $log = DailyLog::findOrFail($id);
        $student = $log->student;
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true, 
            'teacher'  => $student->teacher_id == $user->id, 
            'guardian' => $student->guardian_id == $user->id, 
            default    => false,
        };

        if (!$isAuthorized) abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized.');

        $disk = Storage::disk('local');
        if (!$disk->exists($log->image_path)) abort(404, 'File not found in the secure vault.');

        return $disk->response($log->image_path);
    }
}