<?php

namespace App\Http\Controllers\Guardian;

use App\Models\Announcement;
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
        $featuredAnnouncements = Announcement::latest()->take(3)->get();
        
        $lastSeen = session('last_seen_announcements', now()->subDays(3));
        $newAnnouncementsCount = Announcement::where('created_at', '>', $lastSeen)->count();

        list($activeChild, $myChildren) = $this->getActiveChildData();

        // 1. Initialize the unread count
        $unreadIepCommentsCount = 0;

        if (!$activeChild) {
            return view('guardian.dashboard', compact('myChildren', 'activeChild', 'featuredAnnouncements', 'newAnnouncementsCount', 'unreadIepCommentsCount'));
        }

        $activeGoals = IepGoal::with('student')
            ->where('student_id', $activeChild->id)
            ->where('status', 'in_progress')
            ->get();

        // 2. Count unread teacher replies for this specific child
        $unreadIepCommentsCount = IepGoal::with('comments')
            ->where('student_id', $activeChild->id)
            ->get()
            ->flatMap->comments
            ->where('user_id', '!=', Auth::id())
            ->where('is_guardian_read', 0)
            ->count();

        $recentReports = Report::with('student')
            ->where('student_id', $activeChild->id)
            ->where('status', 'approved')
            ->latest()
            ->take(5)
            ->get();

        // Active Daily Logs (Only logs posted within the last 24 hours or for today; older logs are archived)
        $recentLogs = DailyLog::with(['student', 'teacher', 'comments.user'])
            ->where('student_id', $activeChild->id)
            ->where(function($query) {
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subHours(24))
                      ->orWhereDate('log_date', '>=', \Carbon\Carbon::today());
            })
            ->latest('log_date')
            ->get();

        // Active Home Recommendations (Within last 24 hours or for today)
        $homeActivities = DailyLog::where('student_id', $activeChild->id)
            ->whereNotNull('home_recommendations')
            ->where('home_recommendations', '!=', '')
            ->where(function($query) {
                $query->where('created_at', '>=', \Carbon\Carbon::now()->subHours(24))
                      ->orWhereDate('log_date', '>=', \Carbon\Carbon::today());
            })
            ->latest('log_date')
            ->take(1)
            ->get();

        // 3. Pass the unread count and active items to the view
        return view('guardian.dashboard', compact(
            'myChildren', 
            'activeChild', 
            'activeGoals', 
            'recentReports', 
            'recentLogs', 
            'homeActivities',
            'featuredAnnouncements', 
            'newAnnouncementsCount', 
            'unreadIepCommentsCount'
        ));
    }

    // ==========================================
    // ANNOUNCEMENTS
    // ==========================================
    public function announcements()
    {
        $announcements = Announcement::latest()->get();
        list($activeChild, $myChildren) = $this->getActiveChildData();
        
        // Mark announcements as seen so the dashboard badge clears
        session(['last_seen_announcements' => now()]);
        
        return view('guardian.announcements', compact('announcements', 'myChildren', 'activeChild'));
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

        $report = Report::with(['student', 'teacher', 'approver'])
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
            ->where('guardian_id', Auth::id())
            ->findOrFail($studentId);

        $pdf = Pdf::loadView('teacher.iep.pdf', compact('student'));
        $fileName = 'IEP_' . $student->last_name . '_' . $student->first_name . '_SY2025-2026.pdf';
        
        return $pdf->download($fileName);
    }

    // ==========================================
    // RECOMMENDATIONS / TODAY'S ACTIVE SESSION
    // ==========================================
    public function recommendations()
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();

        $recentRecommendations = collect();
        $archivedCount = 0;

        if ($activeChild) {
            // 1. Active Recommendations (Only within the last 24 hours or for today)
            $recentRecommendations = DailyLog::with(['student', 'teacher', 'comments.user'])
                ->where('student_id', $activeChild->id)
                ->where(function($query) {
                    $query->where('created_at', '>=', \Carbon\Carbon::now()->subHours(24))
                          ->orWhereDate('log_date', '>=', \Carbon\Carbon::today());
                })
                ->latest('log_date')
                ->get();

            // 2. Count Archived Past Due Logs for link badge
            $archivedCount = DailyLog::where('student_id', $activeChild->id)
                ->where('created_at', '<', \Carbon\Carbon::now()->subHours(24))
                ->whereDate('log_date', '<', \Carbon\Carbon::today())
                ->count();
        }

        return view('guardian.recommendations', compact('recentRecommendations', 'archivedCount', 'myChildren', 'activeChild'));
    }

    // ==========================================
    // DEDICATED LOGS & ACTIVITIES ARCHIVE
    // ==========================================
    public function logsArchive(Request $request)
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();

        $archivedLogs = collect();

        if ($activeChild) {
            $query = DailyLog::with(['student', 'teacher', 'comments.user'])
                ->where('student_id', $activeChild->id)
                ->where('created_at', '<', \Carbon\Carbon::now()->subHours(24))
                ->whereDate('log_date', '<', \Carbon\Carbon::today());

            if ($request->filled('quarter')) {
                $query->where('quarter', $request->quarter);
            }

            $archivedLogs = $query->latest('log_date')->get();
        }

        return view('guardian.logs-archive', compact('archivedLogs', 'myChildren', 'activeChild'));
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
        $notifications = Auth::user()->notifications()->latest()->paginate(15);
        list($activeChild, $myChildren) = $this->getActiveChildData();
        return view('guardian.notifications', compact('notifications', 'activeChild', 'myChildren'));
    }

    // ==========================================
    // MARK COMMENTS AS READ (GUARDIAN)
    // ==========================================
    public function markCommentsAsRead($id)
    {
        $goal = IepGoal::findOrFail($id);
        
        // Find any comments on this goal NOT written by the current guardian, and mark them read
        $goal->comments()
             ->where('user_id', '!=', Auth::id())
             ->update(['is_guardian_read' => 1]);

        return response()->json(['success' => true]);
    }
}