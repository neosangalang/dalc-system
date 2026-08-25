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
        $schoolYear = '2024-2025'; 

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
            Storage::disk('public')->put($fileName, $pdf->output());

            // Save to Database
            ArchivedStudent::create([
                'student_id'        => $student->id,
                'school_year'       => $schoolYear,
                'student_snapshot'  => $studentSnapshot,
                'iep_snapshot'      => $iepSnapshot,
                'progress_snapshot' => $progressSnapshot,
                'master_pdf_path'   => $fileName,
            ]);

            // Update Status
            $student->update(['status' => 'archived']);
        }

        return redirect()->back()->with('success', 'Year-End Archiving completed successfully!');
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
    // 4. THE RESTORE METHOD (Retrieve student)
    // ==========================================
    public function restore($id)
    {
        $archive = ArchivedStudent::findOrFail($id);

        // Set the student back to active
        Student::where('id', $archive->student_id)
            ->update(['status' => 'active']);

        // Delete the PDF file if it exists
        if ($archive->master_pdf_path && Storage::disk('public')->exists($archive->master_pdf_path)) {
            Storage::disk('public')->delete($archive->master_pdf_path);
        }

        // Remove the archive record
        $archive->delete();

        return redirect()->route('admin.archive.index')
            ->with('success', 'Student has been restored to active status successfully!');
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