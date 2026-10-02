<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminStudentController extends Controller
{
    public function index() {
        $students = Student::with(['teacher', 'guardian'])->latest()->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->get();
        $guardians = User::where('role', 'guardian')->where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'teachers', 'guardians'));
    }

    public function store(Request $request) {
        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number', // Enforces unique 12-digit LRN
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date', // Enforced requirement
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'teacher_id'                => 'required|exists:users,id', // Enforced requirement
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'guardian_id'               => 'nullable|exists:users,id', // Uses dropdown from User Management
        ]);

        DB::transaction(function () use ($request) {
            $medicalDocPath = null;
            if ($request->hasFile('medical_document')) {
                $medicalDocPath = $request->file('medical_document')->store('medical_documents', 'public');
            }

            Student::create([
                'first_name'                => $request->first_name,
                'last_name'                 => $request->last_name,
                'learners_reference_number' => $request->learners_reference_number, 
                'class_name'                => $request->class_name,
                'date_of_birth'             => $request->date_of_birth,
                'gender'                    => $request->gender,
                'diagnosis'                 => $request->diagnosis,
                'medical_document'          => $medicalDocPath,
                'teacher_id'                => $request->teacher_id,
                'guardian_id'               => $request->guardian_id ?: null,
                'status'                    => 'active',
            ]);
        });

        return redirect()->back()->with('success', 'Student Profile created successfully!');
    }

    public function update(Request $request, $id) {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id, // Ignores current student ID during unique check
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date', // Enforced requirement
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'teacher_id'                => 'required|exists:users,id', // Enforced requirement
            'guardian_id'               => 'nullable|exists:users,id', 
            'status'                    => 'required|in:active,archived',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($request, $student) {
            // Include LRN and Guardian ID in the fields that are allowed to be updated
            $data = $request->only([
                'first_name', 'last_name', 'learners_reference_number', 'class_name', 'date_of_birth',
                'gender', 'diagnosis', 'teacher_id', 'guardian_id', 'status',
            ]);

            if ($request->hasFile('medical_document')) {
                if ($student->medical_document) {
                    Storage::disk('public')->delete($student->medical_document);
                }
                $data['medical_document'] = $request->file('medical_document')->store('medical_documents', 'public');
            }

            $student->update($data);
            // Note: Old manual guardian update logic removed because Guardian linking is now just an ID dropdown!
        });

        return redirect()->back()->with('success', 'Student profile updated successfully!');
    }

    public function reEnroll(Request $request, $id) {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id,
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date',
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'teacher_id'                => 'required|exists:users,id',
            'guardian_id'               => 'nullable|exists:users,id',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($request, $student) {
            $data = $request->only([
                'first_name', 'last_name', 'learners_reference_number', 'class_name', 'date_of_birth',
                'gender', 'diagnosis', 'teacher_id', 'guardian_id',
            ]);
            $data['status'] = 'active';

            if ($request->hasFile('medical_document')) {
                if ($student->medical_document) {
                    Storage::disk('public')->delete($student->medical_document);
                }
                $data['medical_document'] = $request->file('medical_document')->store('medical_documents', 'public');
            }

            $student->update($data);
        });

        return redirect()->back()->with('success', "Student {$student->first_name} {$student->last_name} has been successfully re-enrolled and reactivated for the new school year!");
    }

    public function show($id) {
        $student = Student::with([
            'teacher',
            'guardian',
            'iepGoals',
            'dailyLogs' => function ($query) {
                $query->orderBy('log_date', 'desc')->take(5);
            }
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function destroy($id) {
        $student = Student::findOrFail($id);

        if ($student->medical_document) {
            Storage::disk('public')->delete($student->medical_document);
        }

        $guardianId = $student->guardian_id;
        $student->delete();

        if ($guardianId) {
            $remainingChildren = Student::where('guardian_id', $guardianId)->count();
            if ($remainingChildren === 0) {
                User::where('id', $guardianId)->where('role', 'guardian')->delete();
            }
        }

        return redirect()->back()->with('success', 'Student profile permanently deleted.');
    }
}