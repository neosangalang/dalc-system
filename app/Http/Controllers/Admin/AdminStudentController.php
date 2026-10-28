<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\ArchivedStudent;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminStudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['teacher', 'guardian', 'documents'])->latest()->get();
        $archivedStudents = $students->filter(function ($student) {
            return in_array($student->status, ['archived', 'inactive']);
        })->values();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->get();
        $guardians = User::where('role', 'guardian')->where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'teachers', 'guardians', 'archivedStudents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number', // Enforces unique 12-digit LRN
            'class_name'                => 'required|string|max:255',
            'date_of_birth'             => 'required|date', // Enforced requirement
            'gender'                    => 'required|in:Male,Female',
            'diagnosis'                 => 'required|string',
            'diagnosis_other'           => 'required_if:diagnosis,Other|nullable|string|max:255',
            'teacher_id'                => 'required|exists:users,id', // Enforced requirement
            'documents'                 => 'nullable|array',
            'documents.*'               => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'guardian_id'               => 'required|exists:users,id', // Guardian is strictly required
            'parental_consent_verified' => 'nullable|boolean',
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

        if ($student->status === 'archived') {
            return redirect()->back()->withErrors(['error' => 'Archived students cannot be edited. Please re-enroll the student to make changes.']);
        }

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id, // Ignores current student ID during unique check
            'class_name'                => 'required|string|max:255',
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
            'deleted_documents'         => 'nullable|array',
            'deleted_documents.*'       => 'integer',
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

            // Delete documents that were staged for deletion during this save
            if ($request->filled('deleted_documents')) {
                foreach ($request->deleted_documents as $docId) {
                    $doc = $student->documents()->find($docId);
                    if ($doc) {
                        $filePath = $doc->file_path;
                        $doc->delete();

                        // Safety check: only delete physical file if no other document or snapshot reference uses this path
                        $isReferencedElsewhere = StudentDocument::where('file_path', $filePath)->exists();
                        if (!$isReferencedElsewhere) {
                            if (Storage::disk('local')->exists($filePath)) {
                                Storage::disk('local')->delete($filePath);
                            } elseif (Storage::disk('public')->exists($filePath)) {
                                Storage::disk('public')->delete($filePath);
                            }
                        }
                    }
                }
            }

            // Add newly uploaded multiple documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'local');
                    $student->documents()->create([
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
                        'is_archived'   => false,
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
                    'is_archived'   => false,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Student profile updated successfully!');
    }

    public function getArchivedDocuments($id)
    {
        $student = Student::findOrFail($id);

        $latestArchive = ArchivedStudent::where('student_id', $student->id)
            ->orderBy('archived_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $documents = [];
        $schoolYear = null;
        $archivedAt = null;

        if ($latestArchive) {
            $schoolYear = $latestArchive->school_year;
            $archivedAt = $latestArchive->archived_at ? $latestArchive->archived_at->format('M j, Y') : ($latestArchive->created_at ? $latestArchive->created_at->format('M j, Y') : 'N/A');
            $snapshot = $latestArchive->student_snapshot ?? [];
            $rawDocs = $snapshot['documents'] ?? [];

            if (empty($rawDocs)) {
                $rawDocs = $latestArchive->documents->toArray();
            }

            foreach ($rawDocs as $index => $doc) {
                $fileSize = $doc['file_size'] ?? 'N/A';
                if (is_numeric($fileSize)) {
                    $fileSize = number_format($fileSize / 1024, 1) . ' KB';
                }

                $documents[] = [
                    'index'         => $index,
                    'id'            => $doc['id'] ?? null,
                    'original_name' => $doc['original_name'] ?? 'Document',
                    'file_size'     => $fileSize,
                    'mime_type'     => $doc['mime_type'] ?? 'application/pdf',
                    'view_url'      => route('admin.archive.document', ['id' => $latestArchive->id, 'index' => $index]),
                ];
            }
        }

        // Fallback: If no archive record found or archive had no documents, check student's existing documents
        if (empty($documents)) {
            $existingDocs = $student->allDocuments()->get();
            if ($existingDocs->isNotEmpty()) {
                foreach ($existingDocs as $index => $doc) {
                    $fileSize = $doc->file_size ?: 'N/A';
                    if (is_numeric($fileSize)) {
                        $fileSize = number_format($fileSize / 1024, 1) . ' KB';
                    }

                    $documents[] = [
                        'index'         => $index,
                        'id'            => $doc->id,
                        'original_name' => $doc->original_name,
                        'file_size'     => $fileSize,
                        'mime_type'     => $doc->mime_type ?: 'application/pdf',
                        'view_url'      => route('secure.student-document', $doc->id),
                    ];
                }
            }
        }

        return response()->json([
            'success'     => true,
            'student_id'  => $student->id,
            'has_archive' => $latestArchive !== null,
            'school_year' => $schoolYear,
            'archived_at' => $archivedAt,
            'documents'   => $documents,
        ]);
    }

    public function reEnroll(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        if ($student->status === 'active') {
            return redirect()->back()->with('error', 'Only archived students can be re-enrolled. This student is currently active.');
        }

        $request->validate([
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'learners_reference_number' => 'required|digits:12|unique:students,learners_reference_number,' . $id,
            'class_name'                => 'required|string|max:255',
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
            $student->loadMissing('iepGoals.comments', 'reports', 'documents');

            // Safety snapshot safeguard: If student has prior goals and no archive record exists, snapshot before reset
            if ($student->iepGoals->isNotEmpty() && !ArchivedStudent::where('student_id', $student->id)->exists()) {
                $activeSY = SchoolYear::active();
                $currentYear = (int) date('Y');
                $currentMonth = (int) date('n');
                $fallbackSchoolYear = ($currentMonth >= 6) 
                    ? $currentYear . '-' . ($currentYear + 1) 
                    : ($currentYear - 1) . '-' . $currentYear;
                $schoolYearName = $activeSY ? $activeSY->name : $fallbackSchoolYear;

                $safetyArchive = ArchivedStudent::create([
                    'student_id'        => $student->id,
                    'school_year'       => $schoolYearName,
                    'student_snapshot'  => $student->toArray(),
                    'iep_snapshot'      => $student->iepGoals->toArray(),
                    'progress_snapshot' => $student->reports->toArray(),
                    'master_pdf_path'   => null,
                    'archived_at'       => now(),
                ]);

                // Mark current documents as archived for this safety snapshot
                $student->documents()->update([
                    'archived_student_id' => $safetyArchive->id,
                    'is_archived'         => true,
                ]);
            }

            // Fresh IEP initialization: delete previous goals and their comments
            foreach ($student->iepGoals as $iepGoal) {
                $iepGoal->comments()->delete();
                $iepGoal->delete();
            }

            // Find documents to duplicate / clone from the latest archive snapshot
            $latestArchive = ArchivedStudent::where('student_id', $student->id)
                ->orderBy('archived_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $docsToClone = [];
            if ($latestArchive) {
                $snapshot = $latestArchive->student_snapshot ?? [];
                $docsToClone = $snapshot['documents'] ?? [];
                if (empty($docsToClone)) {
                    $docsToClone = $latestArchive->documents->toArray();
                }
            }

            // Fallback if no archive docs, check student's existing documents
            if (empty($docsToClone)) {
                $docsToClone = $student->allDocuments->toArray();
            }

            // Decouple previous documents: mark any existing documents as archived
            $student->allDocuments()->update([
                'is_archived'         => true,
                'archived_student_id' => $latestArchive?->id,
            ]);

            // Execute automatic file duplication upon re-enrollment confirmation:
            // Cloning the snapshot's attached documents to generate new, independent file references for the active academic cycle
            foreach ($docsToClone as $doc) {
                $sourcePath = $doc['file_path'] ?? null;
                if (!$sourcePath) {
                    continue;
                }

                $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
                $newFilename = 'student_documents/' . \Illuminate\Support\Str::random(40) . ($extension ? '.' . $extension : '');

                $disk = Storage::disk('local')->exists($sourcePath)
                    ? Storage::disk('local')
                    : (Storage::disk('public')->exists($sourcePath) ? Storage::disk('public') : null);

                if ($disk) {
                    Storage::disk('local')->put($newFilename, $disk->get($sourcePath));
                }

                StudentDocument::create([
                    'student_id'          => $student->id,
                    'archived_student_id' => null,
                    'is_archived'         => false,
                    'file_path'           => $newFilename,
                    'original_name'       => $doc['original_name'] ?? 'Cloned_Document',
                    'file_size'           => $doc['file_size'] ?? null,
                    'mime_type'           => $doc['mime_type'] ?? null,
                ]);
            }

            // Maintain the single existing student profile by updating in place
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

            // Add newly uploaded multiple documents for this new academic cycle
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'local');
                    $student->documents()->create([
                        'file_path'           => $path,
                        'original_name'       => $file->getClientOriginalName(),
                        'file_size'           => $file->getSize(),
                        'mime_type'           => $file->getClientMimeType(),
                        'is_archived'         => false,
                        'archived_student_id' => null,
                    ]);
                }
            }

            if ($request->hasFile('medical_document')) {
                $file = $request->file('medical_document');
                $path = $file->store('student_documents', 'local');
                $student->documents()->create([
                    'file_path'           => $path,
                    'original_name'       => $file->getClientOriginalName(),
                    'file_size'           => $file->getSize(),
                    'mime_type'           => $file->getClientMimeType(),
                    'is_archived'         => false,
                    'archived_student_id' => null,
                ]);
            }
        });

        return redirect()->back()->with('success', "Student {$student->first_name} {$student->last_name} has been successfully re-enrolled and reactivated for the new school year! A fresh IEP slate has been initialized.");
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

        $pastArchives = ArchivedStudent::where('student_id', $student->id)
            ->orderBy('school_year', 'desc')
            ->get();

        return view('admin.students.show', compact('student', 'pastArchives'));
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
        $doc = StudentDocument::find($id);
        if ($doc) {
            $filePath = $doc->file_path;
            $doc->delete();

            // Safety check: only delete physical file if no other document or snapshot reference uses this path
            $isReferencedElsewhere = StudentDocument::where('file_path', $filePath)->exists();
            if (!$isReferencedElsewhere) {
                if (Storage::disk('local')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                } elseif (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            return redirect()->back()->with('success', 'Document removed successfully.');
        }

        return redirect()->back()->with('error', 'Document not found or already deleted.');
    }
}
