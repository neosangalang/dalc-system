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

        // 5. Get Recent Announcements Count
        $newAnnouncementsCount = Announcement::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // 6. Fetch Academic Quarters for the Timeline
        $quarters = AcademicQuarter::orderBy('start_date', 'asc')->get();

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

    public function announcements()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('teacher.announcements', compact('announcements'));
    }
}