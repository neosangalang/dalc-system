<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianDailyReportAndRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardian_dashboard_shows_daily_reports_and_home_recommendation_card(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $student = Student::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Grade 1 - Joy',
            'date_of_birth' => '2015-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $response = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.dashboard'));

        $response->assertOk();
        $response->assertSee('Daily Activity', false);
        $response->assertDontSee('Daily Logs & Home Recommendation', false);
        $response->assertSee('Quarter 1');
        $response->assertSee('Quarter 4');
    }

    public function test_guardian_dashboard_and_recommendations_show_daily_report_above_home_recommendations(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $student = Student::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'learners_reference_number' => '987654321098',
            'class_name' => 'Seeds',
            'date_of_birth' => '2016-03-15',
            'gender' => 'Female',
            'diagnosis' => 'Attention Deficit Hyperactivity Disorder (ADHD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $today = now()->format('Y-m-d');

        // Create a DailyLog
        $log = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => $today,
            'quarter' => 'Q1',
            'notes' => 'Alice had a wonderful time in class today.',
            'home_recommendations' => 'Practice counting with blocks tonight.',
        ]);

        // 1. Without formal daily report, fallback classroom notes should be labeled "Daily Report:"
        $response = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.recommendations'));
        $response->assertOk();
        $response->assertSee('Daily Report:');
        $response->assertSee('Alice had a wonderful time in class today.');
        $response->assertSee("Suggested Activity:");
        $response->assertSee('Practice counting with blocks tonight.');

        // Also check guardian dashboard session card
        $dashResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('Daily Report:');
        $dashResponse->assertSee('Alice had a wonderful time in class today.');

        // 2. When a formal approved Daily Report exists, its content should be displayed under "Daily Report:"
        $report = Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => $today,
            'content' => 'Official AI-generated Daily Report narrative summary for Alice.',
            'status' => 'approved',
        ]);

        $responseWithReport = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.recommendations'));
        $responseWithReport->assertOk();
        $responseWithReport->assertSee('Daily Report:');
        $responseWithReport->assertSee('Official AI-generated Daily Report narrative summary for Alice.');

        $dashWithReport = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.dashboard'));
        $dashWithReport->assertOk();
        $dashWithReport->assertSee('Daily Report:');
        $dashWithReport->assertSee('Official AI-generated Daily Report narrative summary for Alice.');
    }

    public function test_daily_report_creation_syncs_to_daily_log_and_comments_are_mirrored(): void
    {
        $guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
        $student = Student::create([
            'first_name' => 'Neo',
            'last_name' => 'Sangalang',
            'learners_reference_number' => '112233445566',
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2017-01-01',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $today = now()->format('Y-m-d');

        // Teacher submits a Daily Report
        $reportResponse = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.reports.store'), [
                'student_id' => $student->id,
                'report_type' => 'daily',
                'report_date' => $today,
                'content' => 'Neo had an amazing session today with emotional regulation.',
                'home_recommendations' => 'Try breathing exercises before bed.',
                'action' => 'submit',
            ]);

        $reportResponse->assertSessionHasNoErrors();
        $reportResponse->assertRedirect();

        // Verify DailyLog was automatically created and synced
        $this->assertDatabaseHas('daily_logs', [
            'student_id' => $student->id,
            'log_date' => $today,
        ]);

        $dailyLog = DailyLog::where('student_id', $student->id)->whereDate('log_date', $today)->first();
        $this->assertNotNull($dailyLog);
        $this->assertStringContainsString('Neo had an amazing session today', $dailyLog->notes);
        $this->assertEquals('Try breathing exercises before bed.', $dailyLog->home_recommendations);

        // Guardian posts a comment on the DailyLog
        $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true])
            ->post(route('comments.store'), [
                'commentable_type' => 'App\Models\DailyLog',
                'commentable_id' => $dailyLog->id,
                'body' => 'Thank you Teacher, Neo loved the activity!',
            ]);

        // Verify comment is mirrored to the Report
        $report = Report::where('student_id', $student->id)->where('report_type', 'daily')->first();
        $this->assertNotNull($report);
        $this->assertDatabaseHas('comments', [
            'commentable_type' => 'App\Models\Report',
            'commentable_id' => $report->id,
            'body' => 'Thank you Teacher, Neo loved the activity!',
        ]);

        // Teacher views reports and sees the comment
        $teacherReportView = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.reports.index'));
        $teacherReportView->assertOk();
        $teacherReportView->assertSee('Thank you Teacher, Neo loved the activity!');
    }
}
