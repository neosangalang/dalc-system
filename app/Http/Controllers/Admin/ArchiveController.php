<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ArchivedStudent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    // ==========================================
    // 1. THE INDEX METHOD (Shows the page)
    // ==========================================
    public function index(Request $request)
    {
        // Get all unique school years for the filter dropdown
        $schoolYears = ArchivedStudent::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        // Filter by selected year, or show all
        $query = ArchivedStudent::with('student')->latest();

        if ($request->filled('school_year')) {
            $query->where('school_year', $request->school_year);
        }

        // Search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $archives = $query->get();

        return view('admin.archiving.index', compact('archives', 'schoolYears'));
    }

    // ==========================================
    // 2. THE RUN METHOD (Processes the archive)
    // ==========================================
    public function runArchive(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ], [
            'one_time_password.required' => 'Please enter your 2-Factor Authentication code.',
            'one_time_password.digits'   => 'The authentication code must be exactly 6 digits.',
        ]);

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $admin = $request->user();

        if (empty($admin->two_factor_secret) || !$google2fa->verifyKey($admin->two_factor_secret, $request->one_time_password)) {
            return back()->withErrors(['one_time_password' => 'Invalid 2-Factor Authentication code. Please try again.'])->withInput();
        }

        // Calculate real-time school year dynamically based on current date
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        $defaultSchoolYear = ($currentMonth >= 6) 
            ? $currentYear . '-' . ($currentYear + 1) 
            : ($currentYear - 1) . '-' . $currentYear;

        $schoolYear = $request->input('school_year') ?: $defaultSchoolYear;
        $now = now();

        $activeStudents = Student::with(['iepGoals', 'dailyLogs', 'teacher', 'guardian'])
            ->where('status', 'active')
            ->get();

        if ($activeStudents->isEmpty()) {
            return back()->with('error', 'No active students found to archive.');
        }

        foreach ($activeStudents as $student) {
            
            // Convert to array to match Model $casts
            $studentSnapshot = $student->toArray();
            $iepSnapshot = $student->iepGoals->toArray();
            $progressSnapshot = $student->dailyLogs->toArray();

            // Generate PDF
            $pdf = Pdf::loadView('pdf.student_archive', ['student' => $student]);
            $fileName = 'archives/' . $schoolYear . '/' . $student->id . '_' . $student->last_name . '.pdf';
            Storage::disk('local')->put($fileName, $pdf->output());

            // Save to Database with real-time timestamp
            ArchivedStudent::create([
                'student_id'        => $student->id,
                'school_year'       => $schoolYear,
                'student_snapshot'  => $studentSnapshot,
                'iep_snapshot'      => $iepSnapshot,
                'progress_snapshot' => $progressSnapshot,
                'master_pdf_path'   => $fileName,
                'archived_at'       => $now,
            ]);

            // Update Status to Inactive (Frozen)
            $student->update(['status' => 'inactive']);
        }

        return redirect()->back()->with('success', "Year-End Archiving for SY {$schoolYear} completed successfully on " . $now->format('M j, Y h:i A') . "! Active student profiles are now transition to Inactive (Frozen) status.");
    }

    // ==========================================
    // 3. THE SHOW METHOD (View archived data)
    // ==========================================
    public function show($id)
    {
        $archive = ArchivedStudent::with('student')->findOrFail($id);

        return view('admin.archiving.show', compact('archive'));
    }

    // ==========================================
    // 4. THE MASTER PDF METHOD (Stream secure PDF)
    // ==========================================
    public function masterPdf($id)
    {
        $archive = ArchivedStudent::with('student')->findOrFail($id);

        if (!$archive->master_pdf_path) {
            abort(404, 'No master PDF record found.');
        }

        $disk = Storage::disk('local');
        $path = $archive->master_pdf_path;

        if (!$disk->exists($path)) {
            if (Storage::disk('public')->exists($path)) {
                $disk = Storage::disk('public');
            } else {
                abort(404, 'Master archive document not found in storage.');
            }
        }

        $studentName = $archive->student ? ($archive->student->last_name . '_' . $archive->student->first_name) : 'Student_' . $archive->student_id;
        $downloadName = $studentName . '_SY_' . $archive->school_year . '_Archive.pdf';

        return $disk->response($path, $downloadName, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
        ]);
    }

    // ==========================================
    // 5. THE RESTORE METHOD (Retrieve student)
    // ==========================================
    public function restore($id)
    {
        $archive = ArchivedStudent::findOrFail($id);

        // Reactivate existing student or recreate profile if previously deleted
        if ($archive->student_id && Student::where('id', $archive->student_id)->exists()) {
            Student::where('id', $archive->student_id)->update(['status' => 'active']);
        } else {
            $snap = $archive->student_snapshot ?? [];
            Student::create([
                'first_name'                => $snap['first_name'] ?? 'Restored',
                'last_name'                 => $snap['last_name'] ?? 'Student',
                'learners_reference_number' => $snap['learners_reference_number'] ?? null,
                'class_name'                => $snap['class_name'] ?? null,
                'date_of_birth'             => $snap['date_of_birth'] ?? now()->toDateString(),
                'gender'                    => $snap['gender'] ?? 'Male',
                'diagnosis'                 => $snap['diagnosis'] ?? ($snap['exceptionality'] ?? 'Restored Profile'),
                'teacher_id'                => $snap['teacher_id'] ?? null,
                'guardian_id'               => $snap['guardian_id'] ?? null,
                'status'                    => 'active',
            ]);
        }

        // Delete the PDF file if it exists (check private first, then public fallback)
        if ($archive->master_pdf_path) {
            if (Storage::disk('local')->exists($archive->master_pdf_path)) {
                Storage::disk('local')->delete($archive->master_pdf_path);
            } elseif (Storage::disk('public')->exists($archive->master_pdf_path)) {
                Storage::disk('public')->delete($archive->master_pdf_path);
            }
        }

        // Remove the archive record
        $archive->delete();

        return redirect()->route('admin.archive.index')
            ->with('success', 'Student profile has been reactivated to active status successfully!');
    }

    public function archiveQuarterlyReports(\Illuminate\Http\Request $request)
{
    // 1. Validate the date the admin picked
    $request->validate([
        'cutoff_date' => 'required|date'
    ]);

    // 2. The "Scenario A" Magic: Just change the status!
    $archivedCount = \App\Models\Report::where('status', 'approved')
                        ->whereDate('created_at', '<=', $request->cutoff_date)
                        ->update(['status' => 'archived']);

    // 3. Return to the page with a success message
    return back()->with('success', "Quarterly Rollover Complete! {$archivedCount} active reports have been safely hidden from teacher dashboards.");
}
}