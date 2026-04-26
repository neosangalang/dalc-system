<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicQuarter;
use App\Models\IepGoal;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function dashboard() {
        // Get the ID of the currently logged-in teacher
        $teacherId = Auth::id();

        // Count how many active students belong to this teacher
        $myStudentsCount = Student::where('teacher_id', $teacherId)->where('status', 'active')->count();
        
        // Count how many active IEP goals belong to this teacher's students
        $activeGoals = IepGoal::whereHas('student', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('status', 'in_progress')->count();

        // We will build the Daily Logs logic next, so we will set this to 0 for now
        $logsThisWeek = 0; 

        // THE FIX: Fetch the quarters from the database!
        $quarters = AcademicQuarter::orderBy('name')->get();

        // Fetch the 4 most recent students assigned to this teacher
        $myStudents = Student::where('teacher_id', $teacherId)
            ->withCount(['iepGoals' => function($query) {
                $query->where('status', 'in_progress');
            }])
            ->latest()
            ->take(4)
            ->get();

        // Pass 'quarters' into the view alongside everything else
        return view('teacher.dashboard', compact(
            'myStudentsCount', 'logsThisWeek', 'activeGoals', 'myStudents', 'quarters'
        ));
    }
}