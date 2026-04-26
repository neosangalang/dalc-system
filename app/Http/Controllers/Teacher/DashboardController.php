<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AcademicQuarter; // <-- This tells the controller what an AcademicQuarter is
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

        // 1. Fetch the teacher's students
        $myStudents = Student::where('teacher_id', $teacherId)->get();
        $myStudentsCount = $myStudents->count();
        
        // (Placeholders for your other stats)
        $logsThisWeek = 0; 
        $activeGoals = 0;  

        // 2. THE FIX: Fetch the quarters from the database
        $quarters = AcademicQuarter::orderBy('name')->get();

        // 3. Pass EVERYTHING to the view
        return view('teacher.dashboard', compact(
            'myStudents', 
            'myStudentsCount', 
            'logsThisWeek', 
            'activeGoals', 
            'quarters' // <-- This was the missing piece!
        ));
    }
}