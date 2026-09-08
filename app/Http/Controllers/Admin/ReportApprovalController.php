<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Notifications\DocumentReadyNotification;
use App\Notifications\SystemAlert; 

class ReportApprovalController extends Controller
{
    /**
     * Display a listing of the resource with search and filtering.
     */
    public function index(Request $request)
    {
        $query = Report::with(['student', 'teacher'])
            ->where('status', 'pending_approval');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($subQ) use ($search) {
                    $subQ->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('teacher', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type);
        }

        $pendingReports = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('admin.report-approval.index', compact('pendingReports'));
    }

    /**
     * Display the specified report.
     */
    public function show($id)
    {
        $report = Report::with(['student', 'teacher', 'student.guardian'])
            ->findOrFail($id);

        return view('admin.report-approval.show', compact('report'));
    }

    /**
     * Update the status of the report and trigger notifications.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'revision_notes' => 'nullable|string|max:3000',
        ]);

        $report = Report::with(['student.guardian', 'teacher'])->findOrFail($id);
        
        $report->status = $request->status;
        if ($request->status === 'rejected') {
            $report->revision_notes = $request->revision_notes;
        }
        $report->save();

        if ($report->status === 'approved') {
            
            if ($report->teacher) {
                $report->teacher->notify(new SystemAlert(
                    'Your report for ' . $report->student->first_name . ' was approved.',
                    route('teacher.reports.index') 
                ));
            }

            if ($report->student && $report->student->guardian) {
                $documentType = strtoupper(str_replace('_', ' ', $report->report_type)) . ' Report';

                $report->student->guardian->notify(new SystemAlert(
                    "New {$documentType} for {$report->student->first_name} is approved and ready for viewing.",
                    route('guardian.reports')
                ));
                
                return redirect()->route('admin.report-approval.index')->with('success', 'Report Approved! Guardian and Teacher have been notified.');
            }
            
            return redirect()->route('admin.report-approval.index')->with('warning', 'Report Approved and Teacher notified, but this student has no Guardian account linked.');
        }

        if ($report->status === 'rejected') {
            
            if ($report->teacher) {
                $noteSnippet = $request->filled('revision_notes') ? ' (Notes: "' . \Illuminate\Support\Str::limit($request->revision_notes, 60) . '")' : '.';
                $report->teacher->notify(new SystemAlert(
                    'Revision Required: Changes requested on ' . $report->student->first_name . '\'s report' . $noteSnippet,
                    route('teacher.reports.edit', $report->id) 
                ));
            }

            return redirect()->route('admin.report-approval.index')->with('success', 'Report returned for revision with instructions. The Teacher has been notified.');
        }
    }
}