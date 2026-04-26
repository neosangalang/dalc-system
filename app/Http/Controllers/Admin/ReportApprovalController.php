<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Notifications\DocumentReadyNotification;

class ReportApprovalController extends Controller
{
    public function index()
    {
        // Fetch all reports that are waiting for the Principal/Admin to approve them
        $pendingReports = Report::with(['student', 'teacher'])
            ->where('status', 'pending_approval')
            ->latest()
            ->get();

        return view('admin.report-approval.index', compact('pendingReports'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        // Find the report and make sure we load the student's Guardian!
        $report = Report::with('student.guardian')->findOrFail($id);
        
        // Update the status in the database
        $report->status = $request->status;
        $report->save();

        // ==========================================
        // THE TRIGGER: Send the Email to the Parent!
        // ==========================================
        if ($report->status === 'approved') {
            
            // Check if the student actually has a guardian account linked
            if ($report->student && $report->student->guardian) {
                
                // Format the document name nicely (e.g., "q1" becomes "Q1", "mid_year" becomes "Mid Year")
                $documentType = strtoupper(str_replace('_', ' ', $report->report_type)) . ' Report';

                // FIRE THE NOTIFICATION!
                $report->student->guardian->notify(new DocumentReadyNotification(
                    $documentType,
                    $report->student->first_name,
                    route('guardian.reports') // The link they will click in the email
                ));
                
                return redirect()->back()->with('success', 'Report Approved! An email notification has been sent to the Guardian.');
            }
            
            return redirect()->back()->with('warning', 'Report Approved, but this student has no Guardian account linked to receive the email.');
        }

        // If rejected, it just sends it back to the Teacher's draft folder
        return redirect()->back()->with('success', 'Report has been rejected and sent back to the Teacher for revisions.');
    }
}