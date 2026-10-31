<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfViewingTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $guardian;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

        $this->teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $this->student = Student::create([
            'first_name' => 'Leo',
            'last_name' => 'Valdez',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Grade 1 - Hope',
            'date_of_birth' => '2016-05-15',
            'gender' => 'Male',
            'diagnosis' => 'ADHD',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);
    }

    public function test_teacher_can_view_daily_log_pdf_inline(): void
    {
        $log = DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => '2026-10-31',
            'quarter' => 'Q2',
            'notes' => 'Great performance in fine motor coordination.',
        ]);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.pdf', $log->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('DailyLog_valdez_leo_2026-10-31.pdf', $response->headers->get('content-disposition'));
    }

    public function test_teacher_can_view_report_pdf_inline(): void
    {
        $report = Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'quarterly',
            'content' => 'Quarter 2 progress evaluation text.',
            'status' => 'approved',
            'report_date' => '2026-10-31',
        ]);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.reports.pdf', $report->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Valdez_Leo_Report.pdf', $response->headers->get('content-disposition'));
    }

    public function test_teacher_can_view_iep_pdf_inline(): void
    {
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.iep-goals.pdf', $this->student->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('IEP_Valdez_Leo_SY2025-2026.pdf', $response->headers->get('content-disposition'));
    }

    public function test_guardian_can_view_report_pdf_inline(): void
    {
        $report = Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'quarterly',
            'content' => 'Approved report for guardian view.',
            'status' => 'approved',
            'report_date' => '2026-10-31',
        ]);

        $response = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true])
            ->get(route('guardian.reports.pdf', $report->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Valdez_Leo_Report.pdf', $response->headers->get('content-disposition'));
    }

    public function test_guardian_can_view_iep_pdf_inline(): void
    {
        $response = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true])
            ->get(route('guardian.goals.download-iep', $this->student->id));

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('IEP_Valdez_Leo_SY2025-2026.pdf', $response->headers->get('content-disposition'));
    }

    public function test_student_document_is_served_inline(): void
    {
        $file = UploadedFile::fake()->create('medical_cert.pdf', 100, 'application/pdf');
        $path = $file->store('student_documents', 'local');

        $doc = $this->student->documents()->create([
            'file_path' => $path,
            'original_name' => 'medical_cert.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('secure.student-document', $doc->id));

        $response->assertOk();
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }
}

