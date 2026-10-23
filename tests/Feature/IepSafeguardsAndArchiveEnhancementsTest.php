<?php

namespace Tests\Feature;

use App\Models\ArchivedStudent;
use App\Models\DailyLog;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IepSafeguardsAndArchiveEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $teacher;
    protected User $guardian;
    protected User $admin;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $this->teacher = User::factory()->create([
            'name' => 'Teacher Sarah',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'can_archive_students' => true,
            'password' => Hash::make('AdminSecret123!'),
        ]);

        $this->student = Student::create([
            'first_name' => 'Lucas',
            'last_name' => 'Vance',
            'learners_reference_number' => '123456789012',
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2017-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);
    }

    public function test_cannot_create_duplicate_iep_goal_under_same_developmental_domain(): void
    {
        // 1. Create initial goal for Perceptuo-Cognitive
        IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Perceptuo-Cognitive',
            'plop' => 'Can match colors with minimal prompts.',
            'goal_description' => "OBJECTIVE:\nMatch shapes independently\n\nACTIVITIES:\n[ ] Sorting blocks",
            'progress_percentage' => 20,
            'status' => 'in_progress',
        ]);

        // 2. Attempt to create a duplicate goal under the same domain
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->from(route('teacher.iep-goals.index'))
            ->post(route('teacher.iep-goals.store'), [
                'student_id' => $this->student->id,
                'domain' => 'Perceptuo-Cognitive',
                'plop' => 'Duplicate PLOP attempt',
                'goal_description' => "OBJECTIVE:\nAnother shape objective\n\nACTIVITIES:\n[ ] Flashcards",
            ]);

        $response->assertSessionHasErrors('domain');
        $this->assertCount(1, IepGoal::where('student_id', $this->student->id)->where('domain', 'Perceptuo-Cognitive')->get());
    }

    public function test_can_delete_iep_goal_and_json_response_returns_deleted_domain(): void
    {
        $goal = IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Language-Communication',
            'plop' => 'Can speak in 2-3 word phrases.',
            'goal_description' => "OBJECTIVE:\nExpress needs verbally\n\nACTIVITIES:\n[ ] Picture exchange",
            'progress_percentage' => 50,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->deleteJson(route('teacher.iep-goals.destroy', $goal->id));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'deleted_domain' => 'Language-Communication',
            'student_id' => $this->student->id,
        ]);

        $this->assertDatabaseMissing('iep_goals', ['id' => $goal->id]);
    }

    public function test_archive_pdf_strictly_excludes_daily_logs_and_includes_plop_documents_and_formal_summaries(): void
    {
        // Add an IEP goal with PLOP
        IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Engages in cooperative play for 10 minutes.',
            'goal_description' => "OBJECTIVE:\nTake turns in group games\n\nACTIVITIES:\n[x] Board games",
            'progress_percentage' => 100,
            'status' => 'achieved',
        ]);

        // Add a routine daily log that MUST be excluded
        DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => '2026-10-15',
            'quarter' => 'Q1',
            'notes' => 'ROUTINE_DAILY_LOG_SECRET_TOKEN: Routine snack time observation.',
        ]);

        // Add a formal Quarterly Summary report
        Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'q1',
            'report_date' => '2026-10-20',
            'content' => 'Q1_FORMAL_SUMMARY_TOKEN: Student has shown exceptional progress in social interaction.',
            'status' => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
        ]);

        // Add an attached student document
        StudentDocument::create([
            'student_id' => $this->student->id,
            'file_path' => 'student_documents/medical_evaluation.pdf',
            'original_name' => 'Lucas_Medical_Diagnostic_2026.pdf',
            'file_size' => '245 KB',
            'mime_type' => 'application/pdf',
        ]);

        $this->student->load(['iepGoals', 'reports', 'documents', 'teacher', 'guardian']);

        // Render PDF HTML
        $renderedHtml = view('pdf.student_archive', ['student' => $this->student])->render();

        // Must NOT contain daily log notes
        $this->assertStringNotContainsString('ROUTINE_DAILY_LOG_SECRET_TOKEN', $renderedHtml);
        $this->assertStringNotContainsString('Daily Activity Logs', $renderedHtml);

        // Must contain PLOP
        $this->assertStringContainsString('Present Level of Performance (PLOP)', $renderedHtml);
        $this->assertStringContainsString('Engages in cooperative play for 10 minutes.', $renderedHtml);

        // Must contain attached document and link
        $this->assertStringContainsString('Lucas_Medical_Diagnostic_2026.pdf', $renderedHtml);
        $this->assertStringContainsString('Attached Student Documents & Medical Records', $renderedHtml);

        // Must contain Quarterly Summary
        $this->assertStringContainsString('Q1_FORMAL_SUMMARY_TOKEN', $renderedHtml);
        $this->assertStringContainsString('Quarter 1 Progress Summary', $renderedHtml);
    }

    public function test_archived_record_deletion_requires_secure_user_code(): void
    {
        Storage::fake('local');

        $fakePdfPath = 'archives/2025-2026/test_archive.pdf';
        Storage::disk('local')->put($fakePdfPath, 'Sample Archive Content');

        $archive = ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => $fakePdfPath,
            'archived_at' => now(),
        ]);

        // 1. Attempt delete without security code
        $failResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.archive.destroy', $archive->id), [
                'security_code' => 'WRONG_CODE_999',
            ]);

        $failResponse->assertSessionHasErrors('security_code');
        $this->assertDatabaseHas('archived_students', ['id' => $archive->id]);
        $this->assertTrue(Storage::disk('local')->exists($fakePdfPath));

        // 2. Attempt delete with valid user password as secure code
        $successResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('admin.archive.destroy', $archive->id), [
                'security_code' => 'AdminSecret123!',
            ]);

        $successResponse->assertRedirect(route('admin.archive.index'));
        $successResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('archived_students', ['id' => $archive->id]);
        $this->assertFalse(Storage::disk('local')->exists($fakePdfPath));
    }

    public function test_archive_show_route_redirects_to_archive_pdf(): void
    {
        $archive = ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => 'archives/sample.pdf',
            'archived_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.archive.show', $archive->id));

        $response->assertRedirect(route('admin.archive.pdf', $archive->id));
    }
}
