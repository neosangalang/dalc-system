<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyLogPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_teacher_can_download_standardized_formal_documentation_pdf(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $teacher = User::factory()->create([
            'name' => 'Jane Teacher',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $student = Student::create([
            'first_name' => 'Alex',
            'last_name' => 'Smith',
            'learners_reference_number' => '123456789099',
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2016-04-12',
            'gender' => 'Male',
            'diagnosis' => 'Speech Delay',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $log = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => '2026-10-12',
            'quarter' => 'Q1',
            'notes' => 'Student Alex demonstrated remarkable attentiveness during motor skill development.',
            'home_recommendations' => 'Encourage hand-eye coordination games at home.',
        ]);

        // 1. Verify Download PDF button is visible on the daily logs index page
        $indexResponse = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.index'));

        $indexResponse->assertOk();
        $indexResponse->assertSee(route('teacher.daily-logs.pdf', $log->id), false);
        $indexResponse->assertSee('Download PDF', false);

        // 2. Verify PDF download endpoint returns a valid PDF download
        $pdfResponse = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.pdf', $log->id));

        $pdfResponse->assertOk();
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
        $this->assertStringContainsString('DailyLog_smith_alex_2026-10-12.pdf', $pdfResponse->headers->get('content-disposition'));
        $this->assertNotEmpty($pdfResponse->getContent());
    }

    public function test_unauthorized_teacher_cannot_download_other_teachers_daily_log_pdf(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $teacher1 = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $teacher2 = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $student = Student::create([
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'learners_reference_number' => '987654321012',
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2017-01-15',
            'gender' => 'Male',
            'diagnosis' => 'ADHD',
            'teacher_id' => $teacher1->id,
            'guardian_id' => $guardian->id,
        ]);

        $log = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher1->id,
            'log_date' => '2026-10-12',
            'quarter' => 'Q1',
            'notes' => 'Confidential observation notes.',
        ]);

        // Teacher 2 attempts to download Teacher 1's student daily log
        $response = $this->actingAs($teacher2)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.pdf', $log->id));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $student = Student::create([
            'first_name' => 'Emma',
            'last_name' => 'Watson',
            'learners_reference_number' => '112233445566',
            'class_name' => 'SPED',
            'date_of_birth' => '2018-03-20',
            'gender' => 'Female',
            'diagnosis' => 'Sensory',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $log = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => '2026-10-12',
            'quarter' => 'Q1',
            'notes' => 'Some documentation notes.',
        ]);

        $response = $this->get(route('teacher.daily-logs.pdf', $log->id));
        $response->assertRedirect(route('login'));
    }
}
