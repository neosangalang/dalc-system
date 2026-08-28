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
use Carbon\Carbon; // Required for week calculations

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
        // This looks at the 'log_date' and checks if it falls within the current Monday-Sunday
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

        // 5. Get Recent Announcements Count
        $newAnnouncementsCount = Announcement::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // 6. Fetch Academic Quarters for the Timeline
        $quarters = AcademicQuarter::orderBy('start_date', 'asc')->get();

        return view('teacher.dashboard', compact(
            'myStudentsCount',
            'logsThisWeek',
            'activeGoals',
            'reportsDue',
            'newAnnouncementsCount',
            'quarters'
        ));
    }

    public function announcements()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('teacher.announcements', compact('announcements'));
    }
}