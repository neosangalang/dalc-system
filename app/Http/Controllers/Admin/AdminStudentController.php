<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- Added to handle file uploads!

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
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'class_name'        => 'required|string|max:255',
            'date_of_birth'     => 'required|date',
            'gender'            => 'required|in:Male,Female',
            'diagnosis'         => 'required|string',
            'teacher_id'        => 'required|exists:users,id',
            'medical_document'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // <-- Max 5MB file
            'guardian_action'   => 'required|in:existing,new',
            'existing_guardian_id' => 'required_if:guardian_action,existing',
            'guardian_first_name'  => 'required_if:guardian_action,new|string|max:255',
            'guardian_last_name'   => 'required_if:guardian_action,new|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $guardianId = null;

            if ($request->guardian_action === 'new') {
                $cleanLastName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $request->guardian_last_name));
                $generatedUsername = $cleanLastName . rand(10, 99); 
                
                $guardian = User::create([
                    'name'      => $request->guardian_first_name . ' ' . $request->guardian_last_name,
                    'email'     => $generatedUsername . '@dalc.local',
                    'username'  => $generatedUsername, 
                    'password'  => Hash::make('12345678'),
                    'role'      => 'guardian',
                    'is_active' => true,
                ]);
                $guardianId = $guardian->id;
            } else {
                $guardianId = $request->existing_guardian_id;
            }

            // FILE UPLOAD LOGIC
            $medicalDocPath = null;
            if ($request->hasFile('medical_document')) {
                $medicalDocPath = $request->file('medical_document')->store('medical_documents', 'public');
            }

            Student::create([
                'first_name'     => $request->first_name,
                'last_name'      => $request->last_name,
                'class_name'     => $request->class_name,
                'date_of_birth'  => $request->date_of_birth,
                'gender'         => $request->gender,
                'diagnosis'      => $request->diagnosis,
                'medical_document'=> $medicalDocPath, // <-- Saving the file path!
                'teacher_id'     => $request->teacher_id,
                'guardian_id'    => $guardianId, 
                'status'         => 'active',
            ]);
        });

        return redirect()->back()->with('success', 'Student Profile created successfully!');
    }

    public function update(Request $request, $id) {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'class_name'        => 'required|string|max:255',
            'date_of_birth'     => 'required|date',
            'gender'            => 'required|in:Male,Female',
            'diagnosis'         => 'required|string',
            'teacher_id'        => 'required|exists:users,id',
            'guardian_id'       => 'required|exists:users,id', 
            'status'            => 'required|in:active,archived',
            'medical_document'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['medical_document']);

        // IF THEY UPLOAD A NEW FILE, DELETE THE OLD ONE AND SAVE THE NEW ONE
        if ($request->hasFile('medical_document')) {
            if ($student->medical_document) {
                Storage::disk('public')->delete($student->medical_document);
            }
            $data['medical_document'] = $request->file('medical_document')->store('medical_documents', 'public');
        }

        $student->update($data);

        return redirect()->back()->with('success', 'Student profile updated successfully!');
    }

    public function show($id) {
        $student = Student::with([
            'teacher', 
            'guardian', 
            'iepGoals', 
            'dailyLogs' => function($query) {
                $query->orderBy('log_date', 'desc')->take(5);
            }
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function destroy($id) {
        $student = Student::findOrFail($id);
        
        // Delete their medical document from the server folder
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