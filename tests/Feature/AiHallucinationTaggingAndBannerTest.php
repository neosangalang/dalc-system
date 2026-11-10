<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiHallucinationTaggingAndBannerTest extends TestCase
{
    use RefreshDatabase;

    private function createTeacher(string $name = 'Teacher'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
    }

    private function createGuardian(): User
    {
        return User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);
    }

    private function createStudent(User $teacher, User $guardian): Student
    {
        return Student::create([
            'first_name' => 'Neo',
            'last_name' => 'Sangalang',
            'learners_reference_number' => (string) rand(100000000000, 999999999999),
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2017-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);
    }

    public function test_daily_logs_ui_displays_hallucination_warning_banner_and_validation_badge(): void
    {
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();
        $this->createStudent($teacher, $guardian);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.index'));

        $response->assertOk();
        $response->assertSee('AI models may occasionally generate incomplete details or inaccuracies (hallucinations). Always review, edit, and validate', false);
        $response->assertSee('Notice:', false);
        $response->assertSee('id="formalNotesValidationBadge"', false);
    }

    public function test_report_create_ui_displays_hallucination_warning_banner_and_validation_badges(): void
    {
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();
        $this->createStudent($teacher, $guardian);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.reports.create'));

        $response->assertOk();
        $response->assertSee('AI models may occasionally generate incomplete details or inaccuracies (hallucinations). Always review, edit, and validate', false);
        $response->assertSee('Notice:', false);
        $response->assertSee('id="dailyValidationBadge"', false);
        $response->assertSee('id="homeValidationBadge"', false);
        $response->assertSee('id="generalValidationBadge"', false);
    }

    public function test_report_revise_ui_displays_hallucination_warning_banner_and_validation_badges(): void
    {
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();
        $student = $this->createStudent($teacher, $guardian);

        $report = Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => now()->format('Y-m-d'),
            'content' => 'Sample initial content.',
            'status' => 'rejected',
            'revision_notes' => 'Please add more details.',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.reports.edit', $report->id));

        $response->assertOk();
        $response->assertSee('AI models may occasionally generate incomplete details or inaccuracies (hallucinations). Always review, edit, and validate', false);
        $response->assertSee('Notice:', false);
        $response->assertSee('AI-Generated Draft &bull; Review Required', false);
        $response->assertSee('Teacher Validated &bull; Reviewed', false);
    }

    public function test_iep_ui_displays_hallucination_warning_banner(): void
    {
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();
        $this->createStudent($teacher, $guardian);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.iep-goals.index'));

        $response->assertOk();
        $response->assertSee('AI models may occasionally generate incomplete details or inaccuracies (hallucinations). Always review, edit, and validate', false);
        $response->assertSee('Notice:', false);
    }
}
