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

        $activeStudents = Student::with([
            'iepGoals',
            'reports' => function($q) {
                $q->whereIn('report_type', ['q1', 'q2', 'q3', 'q4', 'mid_year', 'year_end'])
                  ->where('status', 'approved')
                  ->orderBy('report_date', 'asc');
            },
            'documents',
            'teacher',
            'guardian'
        ])
        ->where('status', 'active')
        ->get();

        if ($activeStudents->isEmpty()) {
            return back()->with('error', 'No active students found to archive.');
        }

        foreach ($activeStudents as $student) {
            
            // Convert to array to match Model $casts (strictly excluding routine daily logs/reports)
            $studentSnapshot = $student->toArray();
            $iepSnapshot = $student->iepGoals->toArray();
            $progressSnapshot = $student->reports->toArray();

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
    // 3. THE SHOW METHOD (Redirects to master PDF)
    // ==========================================
    public function show($id)
    {
        return redirect()->route('admin.archive.pdf', $id);
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
    // 5. THE DESTROY METHOD (Permanently delete archive)
    // ==========================================
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'security_code' => 'required|string',
        ], [
            'security_code.required' => 'Please enter your secure authentication code to proceed.',
        ]);

        $admin = $request->user();
        $code = trim($request->security_code);
        $isValid = false;

        // Verify against 2FA Authenticator code if configured
        if (!empty($admin->two_factor_secret)) {
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            try {
                $isValid = $google2fa->verifyKey($admin->two_factor_secret, $code);
            } catch (\Exception $e) {
                $isValid = false;
            }
        }

        // Dual fallback: Verify against user's password
        if (!$isValid && \Illuminate\Support\Facades\Hash::check($code, $admin->password)) {
            $isValid = true;
        }

        if (!$isValid) {
            return back()->withErrors(['security_code' => 'Security verification failed: Invalid user code. Action aborted.']);
        }

        $archive = ArchivedStudent::with('student.documents')->findOrFail($id);
        $studentName = $archive->student_name;

        // 1. Delete physical PDF file from storage disk
        if ($archive->master_pdf_path) {
            if (Storage::disk('local')->exists($archive->master_pdf_path)) {
                Storage::disk('local')->delete($archive->master_pdf_path);
            } elseif (Storage::disk('public')->exists($archive->master_pdf_path)) {
                Storage::disk('public')->delete($archive->master_pdf_path);
            }
        }

        // 2. Permanently delete student and attached files if student profile still exists in database
        if ($archive->student_id && ($student = Student::with('documents')->find($archive->student_id))) {
            foreach ($student->documents as $doc) {
                if (Storage::disk('local')->exists($doc->file_path)) {
                    Storage::disk('local')->delete($doc->file_path);
                } elseif (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
            }
            $student->delete();
        }

        // 3. Permanently delete the archived student record
        $archive->delete();

        return redirect()->route('admin.archive.index')
            ->with('success', "Archived record for {$studentName} has been permanently deleted.");
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