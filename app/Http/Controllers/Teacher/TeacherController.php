<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\DailyLog;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\Announcement;
use App\Models\AcademicQuarter;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacherId = Auth::id();

        // 1. Count Active Students
        $myStudentsCount = Student::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->count();

        // 2. Count Logs THIS WEEK
        $logsThisWeek = DailyLog::where('teacher_id', $teacherId)
            ->whereBetween('log_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->count();

        // 3. Count Active IEP Goals
        $activeGoals = IepGoal::whereHas('student', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->where('status', 'in_progress')->count();

        // 4. Count Reports Due (Draft or Pending)
        $reportsDue = Report::whereHas('student', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->whereIn('status', ['draft', 'pending_approval'])->count();

        // 5. Get Recent Unseen Announcements Count
        $teacher = Auth::user();
        $lastSeen = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                $lastSeen = $teacher->last_seen_announcements_at;
            }
        } catch (\Throwable $e) {}

        if (!$lastSeen) {
            $lastSeen = session('last_seen_announcements');
        }

        if (!$lastSeen && request()->hasCookie('last_seen_announcements_' . $teacher->id)) {
            try {
                $lastSeen = \Carbon\Carbon::createFromTimestamp((int) request()->cookie('last_seen_announcements_' . $teacher->id));
            } catch (\Throwable $e) {}
        }

        $newAnnouncementsCount = 0;
        if ($lastSeen) {
            $newAnnouncementsCount = Announcement::where('created_at', '>', $lastSeen)->count();
        } else {
            $newAnnouncementsCount = Announcement::where('created_at', '>', $teacher->created_at ?? Carbon::now()->subDays(7))->count();
        }

        // 6. Fetch Active School Year's Quarters for the Timeline
        AcademicQuarter::syncActiveStatus();
        $activeSchoolYear = \App\Models\SchoolYear::active();
        $quarters = $activeSchoolYear ? $activeSchoolYear->quarters : AcademicQuarter::orderBy('id', 'asc')->get();

        // 7. Calculate unread IEP comments from guardians
        $unreadIepCommentsCount = IepGoal::whereHas('student', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->with('comments')->get()->flatMap->comments
          ->where('user_id', '!=', $teacherId)
          ->where('is_teacher_read', 0)
          ->count();

        // 8. Calculate unread Daily Log comments from guardians
        $unreadDailyLogCommentsCount = DailyLog::where('teacher_id', $teacherId)
          ->with('comments')->get()->flatMap->comments
          ->where('user_id', '!=', $teacherId)
          ->where('is_teacher_read', 0)
          ->count();

        return view('teacher.dashboard', compact(
            'myStudentsCount',
            'logsThisWeek',
            'activeGoals',
            'reportsDue',
            'newAnnouncementsCount',
            'quarters',
            'unreadIepCommentsCount',
            'unreadDailyLogCommentsCount'
        ));
    }

    public function announcements(Request $request)
    {
        $teacher = Auth::user();
        $now = now();

        // Mark announcements as seen so the dashboard badge is removed
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                \App\Models\User::where('id', $teacher->id)->update(['last_seen_announcements_at' => $now]);
                $teacher->last_seen_announcements_at = $now;
            }
        } catch (\Throwable $e) {}
        session(['last_seen_announcements' => $now]);
        cookie()->queue('last_seen_announcements_' . $teacher->id, $now->timestamp, 60 * 24 * 30);

        // Mark any announcement notifications for this teacher as read
        try {
            $teacher->unreadNotifications()
                ->where(function($q) {
                    $q->where('data->url', route('teacher.announcements'))
                      ->orWhere('type', 'like', '%Announcement%');
                })
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {}

        // List of all unique available announcement dates (descending: latest first)
        $allAnnouncements = Announcement::orderBy('created_at', 'desc')->get();
        $availableDates = $allAnnouncements
            ->map(fn($a) => \Carbon\Carbon::parse($a->created_at)->format('Y-m-d'))
            ->unique()
            ->values();

        $selectedDate = null;
        $prevDate = null;
        $nextDate = null;

        if ($request->query('date') === 'all') {
            $selectedDate = 'all';
            $announcements = Announcement::with(['comments.user'])->latest('created_at')->latest('id')->get();
        } else {
            // Determine selected date: from query param or default to latest available date
            if ($request->filled('date')) {
                try {
                    $selectedDate = \Carbon\Carbon::parse($request->date)->format('Y-m-d');
                } catch (\Exception $e) {
                    $selectedDate = $availableDates->first() ?: now()->format('Y-m-d');
                }
            } else {
                $selectedDate = $availableDates->first() ?: now()->format('Y-m-d');
            }

            // Calculate previous (older) and next (newer) announcement dates for quick navigation
            if ($availableDates->isNotEmpty()) {
                $newerDates = $availableDates->filter(fn($d) => $d > $selectedDate)->values();
                $olderDates = $availableDates->filter(fn($d) => $d < $selectedDate)->values();

                $nextDate = $newerDates->last();  // closest newer date
                $prevDate = $olderDates->first(); // closest older date
            }

            // Strictly load announcement(s) for the specified date
            $announcements = Announcement::with(['comments.user'])
                ->whereDate('created_at', $selectedDate)
                ->latest('created_at')
                ->latest('id')
                ->get();
        }

        return view('teacher.announcements', compact(
            'announcements',
            'availableDates',
            'selectedDate',
            'prevDate',
            'nextDate',
            'allAnnouncements'
        ));
    }

    public function markAnnouncementsSeen(Request $request)
    {
        $teacher = Auth::user();
        $now = now();

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_seen_announcements_at')) {
                \App\Models\User::where('id', $teacher->id)->update(['last_seen_announcements_at' => $now]);
                $teacher->last_seen_announcements_at = $now;
            }
        } catch (\Throwable $e) {}
        session(['last_seen_announcements' => $now]);
        cookie()->queue('last_seen_announcements_' . $teacher->id, $now->timestamp, 60 * 24 * 30);

        try {
            $teacher->unreadNotifications()
                ->where(function($q) {
                    $q->where('data->url', route('teacher.announcements'))
                      ->orWhere('type', 'like', '%Announcement%');
                })
                ->update(['read_at' => $now]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }
}