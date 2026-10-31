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
        
        $myChildren = Student::with('teacher')->where('guardian_id', $guardianId)->where('status', 'active')->get();
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
        
        $user = Auth::user();
        $lastSeen = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                $lastSeen = $user->last_seen_announcements_at;
            }
        } catch (\Throwable $e) {}

        if (!$lastSeen) {
            $lastSeen = session('last_seen_announcements');
        }

        if (!$lastSeen && request()->hasCookie('last_seen_announcements_' . $user->id)) {
            try {
                $lastSeen = \Carbon\Carbon::createFromTimestamp((int) request()->cookie('last_seen_announcements_' . $user->id));
            } catch (\Throwable $e) {}
        }

        $newAnnouncementsCount = 0;
        if ($lastSeen) {
            $newAnnouncementsCount = Announcement::where('created_at', '>', $lastSeen)->count();
        } else {
            $newAnnouncementsCount = Announcement::where('created_at', '>', $user->created_at ?? now()->subDays(3))->count();
        }

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

        // Active Daily Reports for today (Strictly check activity date: must be today and status: approved)
        $dailyReports = Report::with(['comments.user', 'teacher', 'student'])
            ->where('student_id', $activeChild->id)
            ->where('report_type', 'daily')
            ->where('status', 'approved')
            ->where(function($q) {
                $q->whereDate('report_date', \Carbon\Carbon::today())
                  ->orWhere(function($sub) {
                      $sub->whereNull('report_date')
                          ->whereDate('created_at', \Carbon\Carbon::today());
                  });
            })
            ->latest('id')
            ->get();

        $recentLogs = collect();
        foreach ($dailyReports as $rep) {
            $dateStr = $rep->report_date ? \Carbon\Carbon::parse($rep->report_date)->format('Y-m-d') : $rep->created_at->format('Y-m-d');
            
            $log = DailyLog::with(['student', 'teacher', 'comments.user'])
                ->where('student_id', $activeChild->id)
                ->whereDate('log_date', $dateStr)
                ->latest('id')
                ->first();

            if ($log) {
                $log->daily_report = $rep;
                $reportComments = $rep->comments ?? collect();
                $logComments = $log->comments ?? collect();
                $mergedComments = $logComments->concat($reportComments)
                    ->unique('id')
                    ->sortByDesc('created_at')
                    ->values();
                $log->setRelation('comments', $mergedComments);

                if (!$log->image_path && $rep->image_path) {
                    $log->image_path = $rep->image_path;
                }
                $recentLogs->push($log);
            } else {
                $virtualLog = new DailyLog([
                    'student_id' => $rep->student_id,
                    'teacher_id' => $rep->teacher_id,
                    'log_date'   => $dateStr,
                    'quarter'    => 'Q1',
                    'notes'      => $rep->content,
                    'image_path' => $rep->image_path,
                ]);
                $virtualLog->id = $rep->id;
                $virtualLog->setRelation('student', $rep->student);
                $virtualLog->setRelation('teacher', $rep->teacher);
                $virtualLog->setRelation('comments', $rep->comments ?? collect());
                $virtualLog->daily_report = $rep;
                $recentLogs->push($virtualLog);
            }
        }

        // Active Home Recommendations (Strictly check activity date: must be today and only if daily report is approved)
        $homeActivities = collect();
        if ($dailyReports->isNotEmpty()) {
            $homeActivities = DailyLog::where('student_id', $activeChild->id)
                ->whereNotNull('home_recommendations')
                ->where('home_recommendations', '!=', '')
                ->whereDate('log_date', \Carbon\Carbon::today())
                ->latest('log_date')
                ->take(1)
                ->get();
        }

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
        $guardian = Auth::user();
        $now = now();

        // Mark announcements as seen so the dashboard badge clears permanently
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                \App\Models\User::where('id', $guardian->id)->update(['last_seen_announcements_at' => $now]);
                $guardian->last_seen_announcements_at = $now;
            }
        } catch (\Throwable $e) {}
        session(['last_seen_announcements' => $now]);
        cookie()->queue('last_seen_announcements_' . $guardian->id, $now->timestamp, 60 * 24 * 30);

        // Also mark unread announcement notifications for this guardian as read
        try {
            $guardian->unreadNotifications()
                ->where(function($q) {
                    $q->where('data->url', route('guardian.announcements.index'))
                      ->orWhere('type', 'like', '%Announcement%');
                })
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {}

        $announcements = Announcement::with(['comments.user'])->latest()->get();
        list($activeChild, $myChildren) = $this->getActiveChildData();

        return view('guardian.announcements', compact('announcements', 'myChildren', 'activeChild'));
    }

    public function markAnnouncementsSeen(Request $request)
    {
        $guardian = Auth::user();
        $now = now();

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                \App\Models\User::where('id', $guardian->id)->update(['last_seen_announcements_at' => $now]);
                $guardian->last_seen_announcements_at = $now;
            }
        } catch (\Throwable $e) {}
        session(['last_seen_announcements' => $now]);
        cookie()->queue('last_seen_announcements_' . $guardian->id, $now->timestamp, 60 * 24 * 30);

        try {
            $guardian->unreadNotifications()
                ->where(function($q) {
                    $q->where('data->url', route('guardian.announcements.index'))
                      ->orWhere('type', 'like', '%Announcement%');
                })
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
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

        return $pdf->stream($fileName);
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
        
        return $pdf->stream($fileName);
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
            // 1. Active Daily Reports for today (Strictly approved daily reports)
            $dailyReports = Report::with(['comments.user', 'teacher', 'student'])
                ->where('student_id', $activeChild->id)
                ->where('report_type', 'daily')
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->whereDate('report_date', \Carbon\Carbon::today())
                      ->orWhere(function($sub) {
                          $sub->whereNull('report_date')
                              ->whereDate('created_at', \Carbon\Carbon::today());
                      });
                })
                ->latest('id')
                ->get();

            foreach ($dailyReports as $rep) {
                $dateStr = $rep->report_date ? \Carbon\Carbon::parse($rep->report_date)->format('Y-m-d') : $rep->created_at->format('Y-m-d');
                
                $log = DailyLog::with(['student', 'teacher', 'comments.user'])
                    ->where('student_id', $activeChild->id)
                    ->whereDate('log_date', $dateStr)
                    ->latest('id')
                    ->first();

                if ($log) {
                    $log->daily_report = $rep;
                    $reportComments = $rep->comments ?? collect();
                    $logComments = $log->comments ?? collect();
                    $mergedComments = $logComments->concat($reportComments)
                        ->unique('id')
                        ->sortByDesc('created_at')
                        ->values();
                    $log->setRelation('comments', $mergedComments);

                    if (!$log->image_path && $rep->image_path) {
                        $log->image_path = $rep->image_path;
                    }
                    $recentRecommendations->push($log);
                } else {
                    $virtualLog = new DailyLog([
                        'student_id' => $rep->student_id,
                        'teacher_id' => $rep->teacher_id,
                        'log_date'   => $dateStr,
                        'quarter'    => 'Q1',
                        'notes'      => $rep->content,
                        'image_path' => $rep->image_path,
                    ]);
                    $virtualLog->id = $rep->id;
                    $virtualLog->setRelation('student', $rep->student);
                    $virtualLog->setRelation('teacher', $rep->teacher);
                    $virtualLog->setRelation('comments', $rep->comments ?? collect());
                    $virtualLog->daily_report = $rep;
                    $recentRecommendations->push($virtualLog);
                }
            }

            // 2. Count Previous Reports (Only count dates with APPROVED daily reports yesterday or earlier)
            $availableDates = Report::where('student_id', $activeChild->id)
                ->where('report_type', 'daily')
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->whereDate('report_date', '<', \Carbon\Carbon::today())
                      ->orWhere(function($sub) {
                          $sub->whereNull('report_date')
                              ->whereDate('created_at', '<', \Carbon\Carbon::today());
                      });
                })
                ->get()
                ->map(fn($r) => $r->report_date ? \Carbon\Carbon::parse($r->report_date)->format('Y-m-d') : $r->created_at->format('Y-m-d'))
                ->unique()
                ->sortDesc()
                ->values();

            $archivedCount = $availableDates->count();
        }

        return view('guardian.recommendations', compact('recentRecommendations', 'archivedCount', 'myChildren', 'activeChild'));
    }

    // ==========================================
    // DEDICATED PREVIOUS REPORTS & ACTIVITIES ARCHIVE
    // ==========================================
    public function logsArchive(Request $request)
    {
        list($activeChild, $myChildren) = $this->getActiveChildData();

        $selectedLog = null;
        $selectedDate = null;
        $availableDates = collect();
        $prevDate = null;
        $nextDate = null;
        $archivedCount = 0;
        $archivedLogs = collect();

        if ($activeChild) {
            // Find all historical dates where an APPROVED Daily Report exists (yesterday or earlier)
            $availableDates = Report::where('student_id', $activeChild->id)
                ->where('report_type', 'daily')
                ->where('status', 'approved')
                ->where(function($q) {
                    $q->whereDate('report_date', '<', \Carbon\Carbon::today())
                      ->orWhere(function($sub) {
                          $sub->whereNull('report_date')
                              ->whereDate('created_at', '<', \Carbon\Carbon::today());
                      });
                })
                ->get()
                ->map(fn($r) => $r->report_date ? \Carbon\Carbon::parse($r->report_date)->format('Y-m-d') : $r->created_at->format('Y-m-d'))
                ->unique()
                ->sortDesc()
                ->values();

            $archivedCount = $availableDates->count();

            // Determine selected historical date
            $yesterdayStr = \Carbon\Carbon::yesterday()->format('Y-m-d');
            if ($request->filled('date')) {
                try {
                    $parsed = \Carbon\Carbon::parse($request->date)->format('Y-m-d');
                    // Prevent picking today or future in the archive
                    $selectedDate = $parsed < \Carbon\Carbon::today()->format('Y-m-d') ? $parsed : $yesterdayStr;
                } catch (\Exception $e) {
                    $selectedDate = $availableDates->first() ?: $yesterdayStr;
                }
            } else {
                $selectedDate = $availableDates->first() ?: $yesterdayStr;
            }

            // Calculate previous (older) and next (newer) available report dates for quick navigation
            if ($availableDates->isNotEmpty()) {
                $newerDates = $availableDates->filter(fn($d) => $d > $selectedDate)->values();
                $olderDates = $availableDates->filter(fn($d) => $d < $selectedDate)->values();

                $nextDate = $newerDates->last(); // closest newer date
                $prevDate = $olderDates->first(); // closest older date
            }

            // Strictly load ONE approved Daily Report corresponding to the selected date
            $dailyReport = Report::with(['comments.user', 'teacher', 'student'])
                ->where('student_id', $activeChild->id)
                ->where('report_type', 'daily')
                ->where('status', 'approved')
                ->where(function($q) use ($selectedDate) {
                    $q->whereDate('report_date', $selectedDate)
                      ->orWhere(function($sub) use ($selectedDate) {
                          $sub->whereNull('report_date')
                              ->whereDate('created_at', $selectedDate);
                      });
                })
                ->latest('id')
                ->first();

            if ($dailyReport) {
                $log = DailyLog::with(['student', 'teacher', 'comments.user'])
                    ->where('student_id', $activeChild->id)
                    ->whereDate('log_date', $selectedDate)
                    ->latest('id')
                    ->first();

                if ($log) {
                    $log->daily_report = $dailyReport;
                    $reportComments = $dailyReport->comments ?? collect();
                    $logComments = $log->comments ?? collect();
                    $mergedComments = $logComments->concat($reportComments)
                        ->unique('id')
                        ->sortByDesc('created_at')
                        ->values();
                    $log->setRelation('comments', $mergedComments);

                    if (!$log->image_path && $dailyReport->image_path) {
                        $log->image_path = $dailyReport->image_path;
                    }
                    $selectedLog = $log;
                    $archivedLogs = collect([$log]);
                } else {
                    $virtualLog = new DailyLog([
                        'student_id' => $dailyReport->student_id,
                        'teacher_id' => $dailyReport->teacher_id,
                        'log_date'   => $dailyReport->report_date ?: $dailyReport->created_at->format('Y-m-d'),
                        'quarter'    => 'Q1',
                        'notes'      => $dailyReport->content,
                        'image_path' => $dailyReport->image_path,
                    ]);
                    $virtualLog->id = $dailyReport->id;
                    $virtualLog->setRelation('student', $dailyReport->student);
                    $virtualLog->setRelation('teacher', $dailyReport->teacher);
                    $virtualLog->setRelation('comments', $dailyReport->comments ?? collect());
                    $virtualLog->daily_report = $dailyReport;
                    $selectedLog = $virtualLog;
                    $archivedLogs = collect([$virtualLog]);
                }
            }
        }

        return view('guardian.logs-archive', compact(
            'selectedLog',
            'archivedLogs',
            'selectedDate',
            'availableDates',
            'prevDate',
            'nextDate',
            'archivedCount',
            'myChildren',
            'activeChild'
        ));
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