<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StudentMultiDocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $guardian;
    private User $otherTeacher;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->otherTeacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
    }

    public function test_student_enrollment_supports_multiple_documents(): void
    {
        $file1 = UploadedFile::fake()->create('birth_certificate.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->image('eval_form.png');

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.store'), [
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'learners_reference_number' => '123456789099',
                'class_name' => 'Grade 2 - Hope',
                'date_of_birth' => '2016-04-12',
                'gender' => 'Female',
                'diagnosis' => 'Speech and Language Impairment',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'documents' => [$file1, $file2],
            ]);

        $response->assertSessionHas('success');

        $student = Student::where('learners_reference_number', '123456789099')->firstOrFail();
        $this->assertCount(2, $student->documents);

        $docNames = $student->documents->pluck('original_name')->toArray();
        $this->assertContains('birth_certificate.pdf', $docNames);
        $this->assertContains('eval_form.png', $docNames);

        foreach ($student->documents as $doc) {
            Storage::disk('local')->assertExists($doc->file_path);
        }
    }

    public function test_student_update_appends_new_documents(): void
    {
        $student = Student::create([
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'learners_reference_number' => '987654321012',
            'class_name' => 'Joy',
            'date_of_birth' => '2014-01-01',
            'gender' => 'Male',
            'diagnosis' => 'ADHD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $initialFile = UploadedFile::fake()->create('doc1.pdf', 50, 'application/pdf');
        $student->documents()->create([
            'file_path' => $initialFile->store('student_documents', 'local'),
            'original_name' => 'doc1.pdf',
        ]);

        $newFile = UploadedFile::fake()->create('doc2.pdf', 50, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name' => 'Bob',
                'last_name' => 'Jones',
                'learners_reference_number' => '987654321012',
                'class_name' => 'Joy',
                'date_of_birth' => '2014-01-01',
                'gender' => 'Male',
                'diagnosis' => 'ADHD',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'status' => 'active',
                'documents' => [$newFile],
            ]);

        $response->assertSessionHas('success');
        $student->refresh();
        $this->assertCount(2, $student->documents);
    }

    public function test_authorized_users_can_download_document_and_unauthorized_blocked(): void
    {
        $file = UploadedFile::fake()->create('medical_chart.pdf', 100, 'application/pdf');
        $path = $file->store('student_documents', 'local');

        $student = Student::create([
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'learners_reference_number' => '555555555555',
            'class_name' => 'Grade 1 - Joy',
            'date_of_birth' => '2015-08-20',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $doc = $student->documents()->create([
            'file_path' => $path,
            'original_name' => 'medical_chart.pdf',
        ]);

        // 1. Admin can access
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('secure.student-document', $doc->id))
            ->assertStatus(200);

        // 2. Assigned teacher can access
        $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('secure.student-document', $doc->id))
            ->assertStatus(200);

        // 3. Other teacher is blocked (403)
        $this->actingAs($this->otherTeacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('secure.student-document', $doc->id))
            ->assertStatus(403);
    }

    public function test_individual_document_can_be_deleted(): void
    {
        $file = UploadedFile::fake()->create('to_delete.pdf', 50, 'application/pdf');
        $path = $file->store('student_documents', 'local');

        $student = Student::create([
            'first_name' => 'Daisy',
            'last_name' => 'Duck',
            'learners_reference_number' => '444444444444',
            'class_name' => 'Kinder - Hope',
            'date_of_birth' => '2017-02-14',
            'gender' => 'Female',
            'diagnosis' => 'Down Syndrome',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $doc = $student->documents()->create([
            'file_path' => $path,
            'original_name' => 'to_delete.pdf',
        ]);

        Storage::disk('local')->assertExists($path);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.students.delete-document', $doc->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('student_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_admin_student_index_view_renders_deferred_delete_document_state(): void
    {
        $student = Student::create([
            'first_name' => 'Eve',
            'last_name' => 'Adams',
            'learners_reference_number' => '777777777777',
            'class_name' => 'Joy',
            'date_of_birth' => '2016-03-10',
            'gender' => 'Female',
            'diagnosis' => 'ADHD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $doc = $student->documents()->create([
            'file_path' => 'student_documents/sample.pdf',
            'original_name' => 'sample.pdf',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertSee('deletedDocIds.push(' . $doc->id . ')', false);
        $response->assertSee('name="deleted_documents[]"', false);
    }

    public function test_document_is_retained_unless_save_changes_is_submitted(): void
    {
        $file = UploadedFile::fake()->create('retained.pdf', 50, 'application/pdf');
        $path = $file->store('student_documents', 'local');

        $student = Student::create([
            'first_name' => 'Sam',
            'last_name' => 'Smith',
            'learners_reference_number' => '888888888888',
            'class_name' => 'Faith',
            'date_of_birth' => '2016-05-12',
            'gender' => 'Male',
            'diagnosis' => 'ASD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $doc = $student->documents()->create([
            'file_path' => $path,
            'original_name' => 'retained.pdf',
        ]);

        Storage::disk('local')->assertExists($path);

        // Update student WITHOUT deleted_documents (simulating cancel or not deleting)
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name' => 'Sam',
                'last_name' => 'Smith',
                'learners_reference_number' => '888888888888',
                'class_name' => 'Faith',
                'date_of_birth' => '2016-05-12',
                'gender' => 'Male',
                'diagnosis' => 'ASD',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'status' => 'active',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('student_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertExists($path);
    }

    public function test_document_is_only_deleted_when_save_changes_submits_deleted_documents(): void
    {
        $file = UploadedFile::fake()->create('delete_on_save.pdf', 50, 'application/pdf');
        $path = $file->store('student_documents', 'local');

        $student = Student::create([
            'first_name' => 'Oliver',
            'last_name' => 'Queen',
            'learners_reference_number' => '999999999999',
            'class_name' => 'Valor',
            'date_of_birth' => '2015-08-20',
            'gender' => 'Male',
            'diagnosis' => 'ADHD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $doc = $student->documents()->create([
            'file_path' => $path,
            'original_name' => 'delete_on_save.pdf',
        ]);

        Storage::disk('local')->assertExists($path);

        // Update student WITH deleted_documents (clicking Save Changes)
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name' => 'Oliver',
                'last_name' => 'Queen',
                'learners_reference_number' => '999999999999',
                'class_name' => 'Valor',
                'date_of_birth' => '2015-08-20',
                'gender' => 'Male',
                'diagnosis' => 'ADHD',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'status' => 'active',
                'deleted_documents' => [$doc->id],
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('student_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_archived_student_is_uneditable(): void
    {
        $student = Student::create([
            'first_name' => 'Archived',
            'last_name' => 'Student',
            'learners_reference_number' => '112233445566',
            'class_name' => 'Past',
            'date_of_birth' => '2014-01-01',
            'gender' => 'Female',
            'diagnosis' => 'ASD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'archived',
        ]);

        // Attempting to update an archived student directly is rejected
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name' => 'Changed',
                'last_name' => 'Student',
                'learners_reference_number' => '112233445566',
                'class_name' => 'Past',
                'date_of_birth' => '2014-01-01',
                'gender' => 'Female',
                'diagnosis' => 'ASD',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'status' => 'archived',
            ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Archived',
            'status' => 'archived',
        ]);

        // View shows disabled edit button
        $viewResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.index'));

        $viewResponse->assertOk();
        $viewResponse->assertSee('Archived student is uneditable');
    }
}
