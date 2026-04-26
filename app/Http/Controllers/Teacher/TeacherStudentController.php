<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function index() {
        // Fetch only the students assigned to this specific teacher
        $students = Student::where('teacher_id', Auth::id())
                           ->orderBy('last_name', 'asc')
                           ->get();

        return view('teacher.students.index', compact('students'));
    }
}