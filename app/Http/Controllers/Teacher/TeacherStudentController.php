<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ArchivedStudent;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller
{
    public function index() {
        // Fetch active students assigned to this specific teacher with relationships
        $students = Student::with(['teacher', 'guardian', 'documents'])
                           ->where('teacher_id', Auth::id())
                           ->where('status', 'active')
                           ->orderBy('last_name', 'asc')
                           ->get();

        return view('teacher.students.index', compact('students'));
    }

    public function show($id)
    {
        // Fetch the active student, ensuring they belong to the currently logged-in teacher
        $student = Student::with([
            'teacher', 
            'guardian', 
            'documents', 
            'iepGoals',
            'reports' => function ($query) {
                $query->whereIn('report_type', ['q1', 'q2', 'q3', 'q4', 'mid_year', 'year_end'])
                      ->orderBy('report_date', 'asc');
            }
        ])
            ->where('id', $id)
            ->where('teacher_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        $pastArchives = ArchivedStudent::where('student_id', $student->id)
            ->orderBy('school_year', 'desc')
            ->get();

        $activeSchoolYear = SchoolYear::active()?->name ?? ('SY ' . date('Y') . '-' . (date('Y') + 1));

        // Return the student profile view with multi-year comparison data
        return view('teacher.students.show', compact('student', 'pastArchives', 'activeSchoolYear'));
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