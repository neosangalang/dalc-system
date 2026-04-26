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
    public function index()
    {
        // Fetch records and name the variable '$archives' to match your Blade view
        $archives = ArchivedStudent::with('student')->latest()->get();

        // Load the view at resources/views/admin/archiving/index.blade.php
        return view('admin.archiving.index', compact('archives'));
    }

    // ==========================================
    // 2. THE RUN METHOD (Processes the archive)
    // ==========================================
    public function runArchive(Request $request)
    {
        $schoolYear = '2024-2025'; 

        $activeStudents = Student::with(['iepGoals', 'dailyLogs'])
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