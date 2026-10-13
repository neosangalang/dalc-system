<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminStudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['teacher', 'guardian', 'documents'])->latest()->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->get();
        $guardians = User::where('role', 'guardian')->where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'teachers', 'guardians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number', // Enforces unique 12-digit LRN
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date', // Enforced requirement
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'diagnosis_other'           => 'required_if:diagnosis,Other|nullable|string|max:255',
            'teacher_id'                => 'required|exists:users,id', // Enforced requirement
            'documents'                 => 'nullable|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'guardian_id'               => 'required|exists:users,id', // Guardian is strictly required
        ], [
            'guardian_id.required'        => 'Please select an enrolled guardian account.',
            'diagnosis_other.required_if' => 'Please specify the diagnosis when selecting Other.',
        ]);

        $finalDiagnosis = ($request->diagnosis === 'Other' && filled($request->diagnosis_other))
            ? trim($request->diagnosis_other)
            : $request->diagnosis;

        DB::transaction(function () use ($request, $finalDiagnosis) {
            $student = Student::create([
                'first_name'                => $request->first_name,
                'last_name'                 => $request->last_name,
                'learners_reference_number' => $request->learners_reference_number,
                'class_name'                => $request->class_name,
                'date_of_birth'             => $request->date_of_birth,
                'gender'                    => $request->gender,
                'diagnosis'                 => $finalDiagnosis,
                'medical_document'          => null,
                'teacher_id'                => $request->teacher_id,
                'guardian_id'               => $request->guardian_id,
                'status'                    => 'active',
            ]);

            // Save uploaded multiple documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'local');
                    $student->documents()->create([
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
                    ]);
                }
            }

            // Legacy single file fallback if passed as medical_document
            if ($request->hasFile('medical_document')) {
                $file = $request->file('medical_document');
                $path = $file->store('student_documents', 'local');
                $student->documents()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getClientMimeType(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Student Profile created successfully!');
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id, // Ignores current student ID during unique check
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date', // Enforced requirement
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'diagnosis_other'           => 'required_if:diagnosis,Other|nullable|string|max:255',
            'teacher_id'                => 'required|exists:users,id', // Enforced requirement
            'guardian_id'               => 'nullable|exists:users,id',
            'status'                    => 'required|in:active,archived',
            'documents'                 => 'nullable|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'diagnosis_other.required_if' => 'Please specify the diagnosis when selecting Other.',
        ]);

        $finalDiagnosis = ($request->diagnosis === 'Other' && filled($request->diagnosis_other))
            ? trim($request->diagnosis_other)
            : $request->diagnosis;

        DB::transaction(function () use ($request, $student, $finalDiagnosis) {
            // Include LRN and Guardian ID in the fields that are allowed to be updated
            $data = $request->only([
                'first_name',
                'last_name',
                'learners_reference_number',
                'class_name',
                'date_of_birth',
                'gender',
                'teacher_id',
                'guardian_id',
                'status',
            ]);
            $data['diagnosis'] = $finalDiagnosis;

            $student->update($data);

            // Add newly uploaded multiple documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'local');
                    $student->documents()->create([
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
                    ]);
                }
            }

            // Legacy single file fallback
            if ($request->hasFile('medical_document')) {
                $file = $request->file('medical_document');
                $path = $file->store('student_documents', 'local');
                $student->documents()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getClientMimeType(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Student profile updated successfully!');
    }

    public function reEnroll(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id,
            'class_name'                => 'nullable|string|max:255',
            'date_of_birth'             => 'required|date',
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'diagnosis_other'           => 'nullable|string|max:255',
            'teacher_id'                => 'required|exists:users,id',
            'guardian_id'               => 'nullable|exists:users,id',
            'documents'                 => 'nullable|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $finalDiagnosis = ($request->diagnosis === 'Other' && filled($request->diagnosis_other))
            ? trim($request->diagnosis_other)
            : $request->diagnosis;

        DB::transaction(function () use ($request, $student, $finalDiagnosis) {
            $data = $request->only([
                'first_name',
                'last_name',
                'learners_reference_number',
                'class_name',
                'date_of_birth',
                'gender',
                'teacher_id',
                'guardian_id',
            ]);
            $data['diagnosis'] = $finalDiagnosis;
            $data['status'] = 'active';

            $student->update($data);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'local');
                    $student->documents()->create([
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
                    ]);
                }
            }

            if ($request->hasFile('medical_document')) {
                $file = $request->file('medical_document');
                $path = $file->store('student_documents', 'local');
                $student->documents()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getClientMimeType(),
                ]);
            }
        });

        return redirect()->back()->with('success', "Student {$student->first_name} {$student->last_name} has been successfully re-enrolled and reactivated for the new school year!");
    }

    public function show($id)
    {
        $student = Student::with([
            'teacher',
            'guardian',
            'documents',
            'iepGoals',
            'dailyLogs' => function ($query) {
                $query->orderBy('log_date', 'desc')->take(5);
            }
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    public function destroy($id)
    {
        $student = Student::with('documents')->findOrFail($id);

        // Delete all attached documents from disk
        foreach ($student->documents as $doc) {
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            } elseif (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        if ($student->medical_document) {
            if (Storage::disk('local')->exists($student->medical_document)) {
                Storage::disk('local')->delete($student->medical_document);
            } elseif (Storage::disk('public')->exists($student->medical_document)) {
                Storage::disk('public')->delete($student->medical_document);
            }
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

    public function deleteDocument($id)
    {
        // Try finding as StudentDocument first
        $doc = StudentDocument::find($id);
        if ($doc) {
            if (Storage::disk('local')->exists($doc->file_path)) {
                Storage::disk('local')->delete($doc->file_path);
            } elseif (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
            return redirect()->back()->with('success', 'Document removed successfully.');
        }

        // Fallback: check if $id is Student id with legacy medical_document
        $student = Student::find($id);
        if ($student && $student->medical_document) {
            if (Storage::disk('local')->exists($student->medical_document)) {
                Storage::disk('local')->delete($student->medical_document);
            } elseif (Storage::disk('public')->exists($student->medical_document)) {
                Storage::disk('public')->delete($student->medical_document);
            }
            $student->update(['medical_document' => null]);
            return redirect()->back()->with('success', 'Document removed successfully.');
        }

        return redirect()->back()->with('success', 'Document removed successfully.');
    }
}
