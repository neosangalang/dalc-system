<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() 
    {
        // Fetch real-time counts directly from your MySQL database
        $totalStudents  = Student::where('status', 'active')->count();
        $activeTeachers = User::where('role', 'teacher')->where('is_active', true)->count();
        $guardians      = User::where('role', 'guardian')->where('is_active', true)->count();
        
        // Count reports waiting for approval
        $pendingReports = Report::where('status', 'pending_approval')->count();
        
        // Get the 5 most recently added students
        $recentStudents = Student::with(['teacher', 'guardian'])->latest()->take(5)->get();

        // Send all these variables to the blade view
        return view('admin.dashboard', compact(
            'totalStudents', 'activeTeachers', 'guardians', 'pendingReports', 'recentStudents'
        ));
    }

    public function markNotificationAsRead($id)
    {
        // Find the specific notification for the logged-in user and mark it read
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead(); 

        // Redirect the admin to the dynamic URL saved in the notification data
        return redirect($notification->data['url'] ?? route('admin.dashboard'));
    }
}