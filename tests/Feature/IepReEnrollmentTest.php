<?php

namespace Tests\Feature;

use App\Models\ArchivedStudent;
use App\Models\Comment;
use App\Models\IepGoal;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IepReEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $priorTeacher;
    protected User $newTeacher;
    protected User $guardian;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'can_create_profiles' => true,
        ]);

        $this->priorTeacher = User::factory()->create([
            'name' => 'Prior Teacher',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->newTeacher = User::factory()->create([
            'name' => 'New Teacher',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'name' => 'Guardian Jane',
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        SchoolYear::create([
            'name' => '2026-2027',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'first_name' => 'Leo',
            'last_name' => 'Santillan',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Playgroup Morning (Love A)',
            'date_of_birth' => '2019-03-15',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->priorTeacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'archived',
        ]);
    }

    public function test_re_enrollment_resets_iep_goals_and_comments_for_new_school_year(): void
    {
        // 1. Create previous IEP goal with comments
        $goal = IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Previous year baseline PLOP.',
            'goal_description' => "OBJECTIVE:\nPrior year objective\n\nACTIVITIES:\n[x] Playgroup activity",
            'progress_percentage' => 45,
            'status' => 'in_progress',
        ]);

        $comment = Comment::create([
            'user_id' => $this->priorTeacher->id,
            'commentable_type' => IepGoal::class,
            'commentable_id' => $goal->id,
            'body' => 'Good progress made in term 2.',
        ]);

        $this->assertDatabaseHas('iep_goals', ['id' => $goal->id]);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);

        // 2. Perform Re-Enrollment for the new school year
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 3. Assert student is reactivated and updated
        $this->student->refresh();
        $this->assertEquals('active', $this->student->status);
        $this->assertEquals('Grade 1 - Joy', $this->student->class_name);
        $this->assertEquals($this->newTeacher->id, $this->student->teacher_id);

        // 4. Assert previous IEP goals and comments were cleanly removed from active tracking
        $this->assertDatabaseMissing('iep_goals', ['id' => $goal->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertCount(0, $this->student->iepGoals);

        // 5. Assert safety snapshot was created in archived_students
        $this->assertDatabaseHas('archived_students', [
            'student_id' => $this->student->id,
            'school_year' => '2026-2027',
        ]);
    }

    public function test_re_enrollment_preserves_existing_archive_snapshot_without_duplicate(): void
    {
        // 1. Existing archive record created earlier during year-end archiving
        ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => 'archives/2025-2026/sample.pdf',
            'archived_at' => now()->subMonths(2),
        ]);

        $goal = IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Gross Motor',
            'plop' => 'Can jump with two feet.',
            'goal_description' => "OBJECTIVE:\nPrior Gross Motor objective\n\nACTIVITIES:\n[ ] Obstacle course",
            'progress_percentage' => 10,
            'status' => 'in_progress',
        ]);

        // 2. Perform Re-Enrollment
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Kinder Self-Control',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals(1, ArchivedStudent::where('student_id', $this->student->id)->count());
        $this->assertDatabaseMissing('iep_goals', ['id' => $goal->id]);
    }

    public function test_teacher_can_add_new_goals_under_same_domains_after_re_enrollment(): void
    {
        // Student previously had Socio-Emotional goal
        IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Old baseline PLOP from last year.',
            'goal_description' => "OBJECTIVE:\nOld objective\n\nACTIVITIES:\n[x] Old activity",
            'progress_percentage' => 100,
            'status' => 'achieved',
        ]);

        // Re-enroll student with new teacher
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        // New teacher creates a brand-new goal for Socio-Emotional domain without conflict
        $createResponse = $this->actingAs($this->newTeacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.iep-goals.store'), [
                'student_id' => $this->student->id,
                'domain' => 'Socio-Emotional',
                'plop' => 'Fresh new school year baseline PLOP.',
                'goal_description' => "OBJECTIVE:\nBrand new school year objective\n\nACTIVITIES:\n[ ] New classroom activity",
            ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertCount(1, IepGoal::where('student_id', $this->student->id)->where('domain', 'Socio-Emotional')->get());
        $newGoal = IepGoal::where('student_id', $this->student->id)->first();
        $this->assertEquals('Fresh new school year baseline PLOP.', $newGoal->plop);
        $this->assertEquals(0, $newGoal->progress_percentage);
    }

    public function test_student_master_file_shows_fresh_state_and_archive_link_after_re_enrollment(): void
    {
        ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => 'archives/2025-2026/sample.pdf',
            'archived_at' => now(),
        ]);

        // Re-enroll
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $showResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.show', $this->student->id));

        $showResponse->assertOk();
        $showResponse->assertSee('Currently Enrolled');
        $showResponse->assertSee('No Active IEP Goals Found');
        $showResponse->assertSee('SY 2025-2026 Archive');
    }

    public function test_re_enrollment_api_fetches_latest_archived_snapshot_documents(): void
    {
        Storage::fake('local');
        $fakeFilePath = 'student_documents/historical_eval.pdf';
        Storage::disk('local')->put($fakeFilePath, 'Historical Evaluation Content');

        $archive = ArchivedStudent::create([
            'student_id'       => $this->student->id,
            'school_year'      => '2025-2026',
            'student_snapshot' => [
                'first_name' => 'Leo',
                'last_name'  => 'Santillan',
                'documents'  => [
                    [
                        'id'            => 99,
                        'file_path'     => $fakeFilePath,
                        'original_name' => 'Leo_Diagnostic_2025.pdf',
                        'file_size'     => 10240,
                        'mime_type'     => 'application/pdf',
                    ]
                ]
            ],
            'iep_snapshot'      => [],
            'progress_snapshot' => [],
            'master_pdf_path'   => 'archives/2025-2026/sample.pdf',
            'archived_at'       => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.archived-documents', $this->student->id));

        $response->assertOk();
        $response->assertJson([
            'success'     => true,
            'has_archive' => true,
            'school_year' => '2025-2026',
            'documents'   => [
                [
                    'original_name' => 'Leo_Diagnostic_2025.pdf',
                    'file_size'     => '10.0 KB',
                    'mime_type'     => 'application/pdf',
                    'view_url'      => route('admin.archive.document', ['id' => $archive->id, 'index' => 0]),
                ]
            ]
        ]);
    }

    public function test_re_enrollment_automatically_duplicates_files_and_preserves_single_student_record(): void
    {
        Storage::fake('local');
        $originalFilePath = 'student_documents/original_assessment.pdf';
        Storage::disk('local')->put($originalFilePath, 'ORIGINAL_ASSESSMENT_CONTENT');

        $archive = ArchivedStudent::create([
            'student_id'       => $this->student->id,
            'school_year'      => '2025-2026',
            'student_snapshot' => [
                'first_name' => 'Leo',
                'last_name'  => 'Santillan',
                'documents'  => [
                    [
                        'file_path'     => $originalFilePath,
                        'original_name' => 'Leo_Assessment_2025.pdf',
                        'file_size'     => 2048,
                        'mime_type'     => 'application/pdf',
                    ]
                ]
            ],
            'iep_snapshot'      => [],
            'progress_snapshot' => [],
            'master_pdf_path'   => 'archives/2025-2026/sample.pdf',
            'archived_at'       => now(),
        ]);

        // Perform Re-enrollment
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name'                => 'Leo',
                'last_name'                 => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name'                => 'Grade 2 - Peace',
                'date_of_birth'             => '2019-03-15',
                'gender'                    => 'Male',
                'diagnosis'                 => 'Autism Spectrum Disorder (ASD)',
                'teacher_id'                => $this->newTeacher->id,
                'guardian_id'               => $this->guardian->id,
            ]);

        $response->assertRedirect();

        // 1. Assert exactly 1 student record exists in the directory (Single Profile maintained)
        $this->assertEquals(1, Student::where('learners_reference_number', '123456789012')->count());
        $this->student->refresh();
        $this->assertEquals('active', $this->student->status);
        $this->assertEquals('Grade 2 - Peace', $this->student->class_name);

        // 2. Assert active student profile has newly cloned document reference
        $activeDocs = $this->student->documents;
        $this->assertCount(1, $activeDocs);
        $clonedDoc = $activeDocs->first();
        $this->assertEquals('Leo_Assessment_2025.pdf', $clonedDoc->original_name);
        $this->assertFalse($clonedDoc->is_archived);

        // 3. Assert physical file was duplicated on storage disk (two distinct file paths exist)
        $this->assertNotEquals($originalFilePath, $clonedDoc->file_path);
        Storage::disk('local')->assertExists($originalFilePath);
        Storage::disk('local')->assertExists($clonedDoc->file_path);
        $this->assertEquals('ORIGINAL_ASSESSMENT_CONTENT', Storage::disk('local')->get($clonedDoc->file_path));
    }

    public function test_modifying_or_deleting_active_document_preserves_historical_snapshot_file(): void
    {
        Storage::fake('local');
        $historicalFilePath = 'student_documents/historical_medical.pdf';
        Storage::disk('local')->put($historicalFilePath, 'HISTORICAL_PRESERVED_CONTENT');

        $archive = ArchivedStudent::create([
            'student_id'       => $this->student->id,
            'school_year'      => '2025-2026',
            'student_snapshot' => [
                'first_name' => 'Leo',
                'last_name'  => 'Santillan',
                'documents'  => [
                    [
                        'file_path'     => $historicalFilePath,
                        'original_name' => 'Historical_Medical.pdf',
                        'file_size'     => 4096,
                        'mime_type'     => 'application/pdf',
                    ]
                ]
            ],
            'iep_snapshot'      => [],
            'progress_snapshot' => [],
            'master_pdf_path'   => 'archives/2025-2026/sample.pdf',
            'archived_at'       => now(),
        ]);

        // Re-enroll to create decoupled clone
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name'                => 'Leo',
                'last_name'                 => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name'                => 'Grade 1 - Joy',
                'date_of_birth'             => '2019-03-15',
                'gender'                    => 'Male',
                'diagnosis'                 => 'Autism Spectrum Disorder (ASD)',
                'teacher_id'                => $this->newTeacher->id,
                'guardian_id'               => $this->guardian->id,
            ]);

        $this->student->refresh();
        $clonedDoc = $this->student->documents->first();
        $clonedFilePath = $clonedDoc->file_path;

        // Verify both physical files exist before deletion
        Storage::disk('local')->assertExists($historicalFilePath);
        Storage::disk('local')->assertExists($clonedFilePath);

        // Delete active document in current school year
        $deleteResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.students.delete-document', $clonedDoc->id));

        $deleteResponse->assertSessionHas('success');

        // Assert active cloned document was deleted from DB and storage
        $this->assertDatabaseMissing('student_documents', ['id' => $clonedDoc->id]);
        Storage::disk('local')->assertMissing($clonedFilePath);

        // CRITICAL ASSERTION: The historical file from previous snapshot MUST REMAIN 100% INTACT
        Storage::disk('local')->assertExists($historicalFilePath);
        $this->assertEquals('HISTORICAL_PRESERVED_CONTENT', Storage::disk('local')->get($historicalFilePath));

        // Historical archive streaming route remains functional
        $streamResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.archive.document', ['id' => $archive->id, 'index' => 0]));

        $streamResponse->assertOk();
    }

    public function test_explicit_archive_snapshot_deletion_purges_snapshot_files_without_deleting_active_student(): void
    {
        Storage::fake('local');
        $historicalFilePath = 'student_documents/to_purge_snapshot.pdf';
        $masterPdfPath = 'archives/2025-2026/sample_master.pdf';
        Storage::disk('local')->put($historicalFilePath, 'PURGEABLE_HISTORICAL_CONTENT');
        Storage::disk('local')->put($masterPdfPath, 'PDF_MASTER_CONTENT');

        $archive = ArchivedStudent::create([
            'student_id'       => $this->student->id,
            'school_year'      => '2025-2026',
            'student_snapshot' => [
                'first_name' => 'Leo',
                'last_name'  => 'Santillan',
                'documents'  => [
                    [
                        'file_path'     => $historicalFilePath,
                        'original_name' => 'Snapshot_Record.pdf',
                        'file_size'     => 1024,
                        'mime_type'     => 'application/pdf',
                    ]
                ]
            ],
            'iep_snapshot'      => [],
            'progress_snapshot' => [],
            'master_pdf_path'   => $masterPdfPath,
            'archived_at'       => now(),
        ]);

        // Re-enroll student into active state
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name'                => 'Leo',
                'last_name'                 => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name'                => 'Grade 1 - Joy',
                'date_of_birth'             => '2019-03-15',
                'gender'                    => 'Male',
                'diagnosis'                 => 'Autism Spectrum Disorder (ASD)',
                'teacher_id'                => $this->newTeacher->id,
                'guardian_id'               => $this->guardian->id,
            ]);

        $this->student->refresh();
        $this->assertEquals('active', $this->student->status);
        $activeDoc = $this->student->documents->first();
        $activeFilePath = $activeDoc->file_path;

        // Admin explicitly deletes the historical archive record with 2FA / password security code
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $otp = $google2fa->getCurrentOtp($this->admin->two_factor_secret);

        $deleteArchiveResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.archive.destroy', $archive->id), [
                'security_code' => $otp,
            ]);

        $deleteArchiveResponse->assertRedirect(route('admin.archive.index'));
        $deleteArchiveResponse->assertSessionHas('success');

        // 1. Assert archived_students record is deleted
        $this->assertDatabaseMissing('archived_students', ['id' => $archive->id]);

        // 2. Assert snapshot physical files and master PDF are purged
        Storage::disk('local')->assertMissing($historicalFilePath);
        Storage::disk('local')->assertMissing($masterPdfPath);

        // 3. CRITICAL: Assert active student profile was NOT deleted and remains active
        $this->assertDatabaseHas('students', [
            'id'     => $this->student->id,
            'status' => 'active',
        ]);

        // 4. CRITICAL: Assert active student's cloned files are still intact
        $this->assertDatabaseHas('student_documents', ['id' => $activeDoc->id]);
        Storage::disk('local')->assertExists($activeFilePath);
    }

    public function test_re_enroll_index_view_only_includes_archived_students_and_excludes_active_students(): void
    {
        $activeStudent = Student::create([
            'first_name'                => 'Active',
            'last_name'                 => 'Learner',
            'learners_reference_number' => '999888777666',
            'class_name'                => 'Grade 2 - Peace',
            'date_of_birth'             => '2018-05-10',
            'gender'                    => 'Female',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $this->priorTeacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertViewHas('archivedStudents');

        /** @var \Illuminate\Support\Collection $archivedInView */
        $archivedInView = $response->viewData('archivedStudents');

        // Assert only archived students are in the re-enroll selection list
        $this->assertTrue($archivedInView->contains('id', $this->student->id));
        $this->assertFalse($archivedInView->contains('id', $activeStudent->id));

        // Assert searchable input placeholder exists
        $response->assertSee('Search student name, ID, LRN...');
    }

    public function test_re_enroll_fails_if_student_is_already_active(): void
    {
        $activeStudent = Student::create([
            'first_name'                => 'Active',
            'last_name'                 => 'Learner',
            'learners_reference_number' => '111222333444',
            'class_name'                => 'Grade 2 - Peace',
            'date_of_birth'             => '2018-05-10',
            'gender'                    => 'Female',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $this->priorTeacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $activeStudent->id), [
                'first_name'                => 'Active',
                'last_name'                 => 'Learner',
                'learners_reference_number' => '111222333444',
                'class_name'                => 'Grade 3 - Patience',
                'date_of_birth'             => '2018-05-10',
                'gender'                    => 'Female',
                'diagnosis'                 => 'ADHD',
                'teacher_id'                => $this->newTeacher->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Only archived students can be re-enrolled. This student is currently active.');
    }

    public function test_student_profile_displays_guardian_email_and_phone(): void
    {
        $this->guardian->update([
            'email'          => 'guardian.jane@example.com',
            'contact_number' => '09171234567',
        ]);

        // 1. Admin student profile view
        $adminResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.show', $this->student->id));

        $adminResponse->assertOk();
        $adminResponse->assertSee('Primary Guardian');
        $adminResponse->assertSee('Guardian Jane');
        $adminResponse->assertSee('guardian.jane@example.com');
        $adminResponse->assertSee('09171234567');

        // 2. Teacher student profile view
        $this->student->update([
            'status'     => 'active',
            'teacher_id' => $this->priorTeacher->id,
        ]);

        $teacherResponse = $this->actingAs($this->priorTeacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.students.show', $this->student->id));

        $teacherResponse->assertOk();
        $teacherResponse->assertSee('Primary Guardian');
        $teacherResponse->assertSee('Guardian Jane');
        $teacherResponse->assertSee('guardian.jane@example.com');
        $teacherResponse->assertSee('09171234567');
    }
}

