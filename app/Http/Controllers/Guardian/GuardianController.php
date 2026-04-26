<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\DailyLog;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; 

class GuardianController extends Controller
{
    // ==========================================
    // HELPER: GET THE CURRENTLY SELECTED CHILD
    // ==========================================
    private function getActiveChildData()
    {
        $guardianId = Auth::id();
        
        $myChildren = Student::with('teacher')->where('guardian_id', $guardianId)->get();
        $activeChild = null;

        if ($myChildren->isNotEmpty()) {
            $activeId = session('active_child_id');
            $activeChild = $myChildren->where('id', $activeId)->first();
            
            if (!$activeChild) {
                $activeChild = $myChildren->first();
                session(['active_child_id' => $activeChild->id]);
            }
        }
        
        return [$activeChild, $myChildren];
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    public function dashboard()
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();

        if (!$activeChild) {
            return view('guardian.dashboard', compact('myChildren', 'activeChild'));
        }

        // Fetch data ONLY for the active child
        $activeGoals = IepGoal::with('student')
            ->where('student_id', $activeChild->id)
            ->where('status', 'in_progress')
            ->get();

        $recentReports = Report::with('student')
            ->where('student_id', $activeChild->id)
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = DailyLog::with(['student', 'teacher'])
            ->where('student_id', $activeChild->id)
            ->latest('log_date')
            ->take(5)
            ->get();

        return view('guardian.dashboard', compact('myChildren', 'activeChild', 'activeGoals', 'recentReports', 'recentLogs'));
    }

    // ==========================================
    // REPORTS
    // ==========================================
    public function reports(Request $request)
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();
        
        if (!$activeChild) {
            $reports = collect();
            return view('guardian.reports', compact('reports', 'myChildren', 'activeChild'));
        }

        $query = Report::with(['student', 'teacher'])
            ->where('student_id', $activeChild->id)
            ->where('status', 'approved');

        if ($request->has('type')) {
            $query->where('report_type', $request->type);
        }

        $reports = $query->latest()->get();

        return view('guardian.reports', compact('reports', 'myChildren', 'activeChild'));
    }

    public function downloadReportPdf($id)
    {
        $guardianId = Auth::id();
        $studentIds = Student::where('guardian_id', $guardianId)->pluck('id');

        $report = Report::with(['student', 'teacher'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->findOrFail($id);

        $pdf = Pdf::loadView('teacher.reports.pdf', compact('report'));
        $fileName = $report->student->last_name . '_' . $report->student->first_name . '_Report.pdf';

        return $pdf->download($fileName);
    }

    // ==========================================
    // GOALS
    // ==========================================
    public function goals()
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();
        
        if ($activeChild) {
            $activeChild->load(['iepGoals' => function($query) {
                $query->orderBy('progress_percentage', 'desc');
            }]);
        }

        return view('guardian.goals', compact('myChildren', 'activeChild'));
    }

    public function downloadIepPdf($studentId)
    {
        $student = Student::with(['iepGoals', 'guardian', 'teacher'])
            ->where('guardian_id', auth()->id()) 
            ->findOrFail($studentId);

        $pdf = Pdf::loadView('teacher.iep.pdf', compact('student'));
        $fileName = 'IEP_' . $student->last_name . '_' . $student->first_name . '_SY2025-2026.pdf';
        
        return $pdf->download($fileName);
    }

    // ==========================================
    // RECOMMENDATIONS
    // ==========================================
    public function recommendations()
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();

        $recentRecommendations = collect();

        if ($activeChild) {
            $recentRecommendations = DailyLog::with('student')
                ->where('student_id', $activeChild->id)
                ->whereNotNull('home_recommendations')
                ->latest('log_date')
                ->take(5)
                ->get();
        }

        return view('guardian.recommendations', compact('recentRecommendations', 'myChildren', 'activeChild'));
    }

    // ==========================================
    // UNIVERSAL SWITCHER (Updates the Session)
    // ==========================================
    public function switchChild($id)
    {
        $guardianId = Auth::id();
        $validChild = Student::where('guardian_id', $guardianId)->where('id', $id)->first();

        if (!$validChild) {
            abort(403, 'Unauthorized access to student profile.');
        }

        session(['active_child_id' => $validChild->id]);

        return redirect()->back()->with('success', "Switched profile to {$validChild->first_name}");
    }

    // ==========================================
    // MISC
    // ==========================================
    public function feedback()
    {
        return view('guardian.feedback');
    }

    public function storeFeedback(Request $request)
    {
        return back()->with('success', 'Your feedback has been sent to the teacher.');
    }

    public function notifications()
    {
        return view('guardian.notifications');
    }
}