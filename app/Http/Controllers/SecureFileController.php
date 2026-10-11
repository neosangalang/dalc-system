<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyLog;
use App\Models\Student;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    /**
     * Serve Daily Log observation photos securely.
     */
    public function show($id)
    {
        $log = DailyLog::with('student')->findOrFail($id);
        $student = $log->student;
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true, 
            'teacher'  => $student ? ($student->teacher_id == $user->id) : false, 
            'guardian' => $student ? ($student->guardian_id == $user->id) : false, 
            default    => false,
        };

        if (!$isAuthorized) {
            abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized.');
        }

        if (!$log->image_path) {
            abort(404, 'No photo attached to this daily log.');
        }

        $disk = Storage::disk('local');
        $path = $log->image_path;

        if (!$disk->exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = Storage::disk('public');
            } else {
                abort(404, 'File not found in the secure vault.');
            }
        }

        return $disk->response($path);
    }

    /**
     * Serve Student confidential medical documents securely.
     */
    public function medicalDocument($id)
    {
        $student = Student::findOrFail($id);
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true,
            'teacher'  => $student->teacher_id == $user->id,
            'guardian' => $student->guardian_id == $user->id,
            default    => false,
        };

        if (!$isAuthorized) {
            abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized to access this medical document.');
        }

        if (!$student->medical_document) {
            abort(404, 'No medical document on file for this student.');
        }

        $disk = Storage::disk('local');
        $path = $student->medical_document;

        if (!$disk->exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = Storage::disk('public');
            } else {
                abort(404, 'Medical document file not found.');
            }
        }

        return $disk->response($path);
    }

    /**
     * Serve Report observation photos securely.
     */
    public function reportPhoto($id)
    {
        $report = Report::with('student')->findOrFail($id);
        $student = $report->student;
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true,
            'teacher'  => ($report->teacher_id == $user->id) || ($student && $student->teacher_id == $user->id),
            'guardian' => ($student && $student->guardian_id == $user->id),
            default    => false,
        };

        if (!$isAuthorized) {
            abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized to access this report photo.');
        }

        if (!$report->image_path) {
            abort(404, 'No observation photo attached to this report.');
        }

        $disk = Storage::disk('local');
        $path = $report->image_path;

        if (!$disk->exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = Storage::disk('public');
            } else {
                abort(404, 'Report photo file not found.');
            }
        }

        return $disk->response($path);
    }
}