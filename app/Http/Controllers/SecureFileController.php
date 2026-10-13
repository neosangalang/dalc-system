<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyLog;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    /**
     * Securely serves observation photos attached to Daily Logs.
     */
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
        if ($log->image_path && $disk->exists($log->image_path)) {
            return $disk->response($log->image_path);
        }

        // Backward compatibility fallback for legacy public uploads
        if ($log->image_path && Storage::disk('public')->exists($log->image_path)) {
            return Storage::disk('public')->response($log->image_path);
        }

        abort(404, 'File not found in the secure vault.');
    }

    /**
     * Securely serves photos attached to Student Reports.
     */
    public function showReportPhoto($id)
    {
        $report = Report::findOrFail($id);
        $student = $report->student;
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true, 
            'teacher'  => ($student && $student->teacher_id == $user->id) || $report->teacher_id == $user->id, 
            'guardian' => $student && $student->guardian_id == $user->id, 
            default    => false,
        };

        if (!$isAuthorized) abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized to view this report media.');

        $disk = Storage::disk('local');
        if ($report->image_path && $disk->exists($report->image_path)) {
            return $disk->response($report->image_path);
        }

        // Backward compatibility fallback for legacy public uploads
        if ($report->image_path && Storage::disk('public')->exists($report->image_path)) {
            return Storage::disk('public')->response($report->image_path);
        }

        abort(404, 'Report photo not found in the secure vault.');
    }

    /**
     * Securely serves sensitive Student Medical Documents.
     */
    public function showMedicalDocument($id)
    {
        $student = Student::with('documents')->findOrFail($id);
        $firstDoc = $student->documents->first();
        if ($firstDoc) {
            return $this->showDocument($firstDoc->id);
        }

        abort(404, 'No document associated with this student.');
    }

    /**
     * Alias for route compatibility.
     */
    public function reportPhoto($id)
    {
        return $this->showReportPhoto($id);
    }

    /**
     * Alias for route compatibility.
     */
    public function medicalDocument($id)
    {
        return $this->showMedicalDocument($id);
    }

    /**
     * Securely serves sensitive Student Documents.
     */
    public function showDocument($id)
    {
        $document = StudentDocument::findOrFail($id);
        $student = $document->student;
        $user = Auth::user();

        $isAuthorized = match($user->role) {
            'admin'    => true, 
            'teacher'  => $student->teacher_id == $user->id, 
            'guardian' => $student->guardian_id == $user->id, 
            default    => false,
        };

        if (!$isAuthorized) {
            abort(403, 'Security Violation: User ID ' . $user->id . ' is not authorized to access student records.');
        }

        $disk = Storage::disk('local');
        if ($disk->exists($document->file_path)) {
            return $disk->response($document->file_path, $document->original_name);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->response($document->file_path, $document->original_name);
        }

        abort(404, 'Document file not found.');
    }

    public function document($id)
    {
        return $this->showDocument($id);
    }
}