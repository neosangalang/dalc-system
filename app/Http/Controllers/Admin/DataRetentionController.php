<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\DataRetentionService;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class DataRetentionController extends Controller
{
    /**
     * Display the 5-Year Data Retention & Automated Purge Protocol dashboard.
     */
    public function index(Request $request, DataRetentionService $retentionService)
    {
        $previewData = $retentionService->previewScheduledPurges(100);

        return view('admin.data-retention.index', [
            'retentionYears'   => $previewData['retention_years'],
            'cutoffDate'       => $previewData['cutoff_date'],
            'eligibleCount'    => $previewData['eligible_count'],
            'legalHoldCount'   => $previewData['legal_hold_count'],
            'lastPurge'        => $previewData['last_purge'],
            'eligibleStudents' => $previewData['eligible'],
            'upcomingStudents' => $previewData['upcoming'],
            'legalHolds'       => $previewData['legal_holds'],
        ]);
    }

    /**
     * Run a dry-run purge simulation without deleting any database records or server files.
     */
    public function dryRun(Request $request, DataRetentionService $retentionService)
    {
        $admin = $request->user();

        $results = $retentionService->runPurge(dryRun: true, initiator: $admin);

        return redirect()->route('admin.data-retention.index')
            ->with('simulation_results', $results)
            ->with('success', "Dry-run simulation completed successfully: {$results['total_students_processed']} record(s) simulated. 0 database records or files were altered.");
    }

    /**
     * Execute live, permanent purge of all student records exceeding the retention threshold.
     */
    public function purge(Request $request, DataRetentionService $retentionService)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
            'confirmation'      => 'required|in:PURGE',
        ], [
            'one_time_password.required' => 'Please enter your 6-digit Two-Factor Authentication code.',
            'one_time_password.digits'   => 'The authentication code must be exactly 6 digits.',
            'confirmation.in'            => 'You must type PURGE in all capital letters to authorize permanent data deletion.',
            'confirmation.required'      => 'You must type PURGE to authorize permanent data deletion.',
        ]);

        $google2fa = new Google2FA();
        $admin = $request->user();

        if (empty($admin->two_factor_secret) || !$google2fa->verifyKey($admin->two_factor_secret, $request->one_time_password)) {
            return back()->withErrors(['one_time_password' => 'Invalid Two-Factor Authentication code. Please try again.'])->withInput();
        }

        $results = $retentionService->runPurge(dryRun: false, initiator: $admin);

        return redirect()->route('admin.data-retention.index')
            ->with('purge_results', $results)
            ->with('success', "Permanent data purge completed successfully: {$results['total_students_processed']} student profile(s) and {$results['files_deleted']} physical file(s) permanently removed.");
    }

    /**
     * Toggle or update legal hold protection on a non-active student record.
     */
    public function toggleLegalHold(Request $request, $id, DataRetentionService $retentionService)
    {
        $student = Student::where('status', '!=', 'active')->findOrFail($id);

        $request->validate([
            'legal_hold'        => 'required|boolean',
            'legal_hold_reason' => 'nullable|string|max:500',
        ]);

        $isHold = $request->boolean('legal_hold');
        $reason = $request->input('legal_hold_reason');

        $retentionService->setLegalHold($student, $isHold, $reason, $request->user());

        $statusText = $isHold ? 'placed on Legal Hold' : 'released from Legal Hold';

        return redirect()->back()->with('success', "Student record #{$student->id} successfully {$statusText}.");
    }
}

