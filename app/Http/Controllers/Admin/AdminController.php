<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ProgressReport; // <-- Added this!
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        // Fetch real-time counts directly from your MySQL database
        $totalStudents  = Student::where('status', 'active')->count();
        $activeTeachers = User::where('role', 'teacher')->where('is_active', true)->count();
        $guardians      = User::where('role', 'guardian')->where('is_active', true)->count();
        $pendingReports = ProgressReport::where('status', 'pending')->count(); // <-- Added this!
        
        // Get the 5 most recently added students
        $recentStudents = Student::with(['teacher', 'guardian'])->latest()->take(5)->get();

        // Send all these variables to the blade view
        return view('admin.dashboard', compact(
            'totalStudents', 'activeTeachers', 'guardians', 'pendingReports', 'recentStudents'
        ));
    }
}