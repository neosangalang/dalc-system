<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function index() {
        // Fetch only active students assigned to this specific teacher
        $students = Student::where('teacher_id', Auth::id())
                           ->where('status', 'active')
                           ->orderBy('last_name', 'asc')
                           ->get();

        return view('teacher.students.index', compact('students'));
    }

    public function show($id)
    {
        // Fetch the active student, ensuring they belong to the currently logged-in teacher
        $student = Student::where('id', $id)
            ->where('teacher_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        // Return the read-only profile view we just created
        return view('teacher.students.show', compact('student'));
    }

    public function edit($id)
    {
        // Security Check: Only allow if the admin granted this teacher edit permissions
        if (!Auth::user()->can_edit_students) {
            abort(403, 'You do not have permission to edit student profiles. Please contact an Administrator.');
        }

        $student = Student::where('id', $id)
            ->where('teacher_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        return view('teacher.students.edit', compact('student'));
    }
}