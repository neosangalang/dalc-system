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

        // 1. Without formal daily report, raw daily log notes should NOT appear for the guardian
        $response = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.recommendations'));
        $response->assertOk();
        $response->assertDontSee('Alice had a wonderful time in class today.');

        // Also check guardian dashboard: raw notes should NOT appear
        $dashResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertDontSee('Alice had a wonderful time in class today.');

        // 2. When a formal approved Daily Report exists, its content and home recommendations are displayed
        $report = Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => $today,
            'content' => "Official AI-generated Daily Report narrative summary for Alice.\n\nTonight's Suggested Activity:\nPractice counting with blocks tonight.",
            'status' => 'approved',
        ]);

        $responseWithReport = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.recommendations'));
        $responseWithReport->assertOk();
        $responseWithReport->assertSee('Daily Report:');
        $responseWithReport->assertSee('Official AI-generated Daily Report narrative summary for Alice.');
        $responseWithReport->assertSee('Practice counting with blocks tonight.');

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

    public function test_activity_date_strictly_determines_active_session_vs_previous_reports(): void
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
            'first_name' => 'Lucas',
            'last_name' => 'Miller',
            'learners_reference_number' => '445566778899',
            'class_name' => 'Bloom',
            'date_of_birth' => '2016-04-12',
            'gender' => 'Male',
            'diagnosis' => 'Speech and Language Impairment',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $yesterday = now()->subDay()->format('Y-m-d');

        // Teacher submits daily report for activity that happened YESTERDAY
        Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => $yesterday,
            'content' => 'Lucas completed puzzle games yesterday.',
            'status' => 'approved',
        ]);

        DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => $yesterday,
            'quarter' => 'Q1',
            'notes' => 'Lucas completed puzzle games yesterday.',
            'home_recommendations' => 'Read bedtime story together.',
            'created_at' => now(), // Typed in today
        ]);

        // 1. Visit "Today's Active Session": Yesterday's activity must NOT be displayed here
        $todayResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.recommendations'));

        $todayResponse->assertOk();
        $todayResponse->assertDontSee('Lucas completed puzzle games yesterday.');
        $todayResponse->assertSee("Today's Active Session", false);
        $todayResponse->assertSee('Previous Reports (1)');
        $todayResponse->assertDontSee('Archive & History');

        // 2. Visit "Previous Reports" (Archive): Yesterday's activity must appear here
        $archiveResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.logs.archive'));

        $archiveResponse->assertOk();
        $archiveResponse->assertSee('Lucas completed puzzle games yesterday.');
        $archiveResponse->assertSee('Previous Reports (1)');
        $archiveResponse->assertSee('id="activity_date_selector"', false);
        // Quarter filter buttons should NOT exist
        $archiveResponse->assertDontSee('>All<', false);
        $archiveResponse->assertDontSee('>Q1<', false);
    }

    public function test_previous_reports_loads_strictly_one_report_by_selected_date(): void
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
            'first_name' => 'Maya',
            'last_name' => 'Cruz',
            'learners_reference_number' => '556677889900',
            'class_name' => 'Sprout',
            'date_of_birth' => '2017-08-20',
            'gender' => 'Female',
            'diagnosis' => 'Global Developmental Delay (GDD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $twoDaysAgo = now()->subDays(2)->format('Y-m-d');
        $threeDaysAgo = now()->subDays(3)->format('Y-m-d');

        Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => $twoDaysAgo,
            'content' => 'Report for Maya from TWO days ago.',
            'status' => 'approved',
        ]);
        DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => $twoDaysAgo,
            'quarter' => 'Q1',
            'notes' => 'Report for Maya from TWO days ago.',
            'home_recommendations' => 'Two days ago activity recommendation.',
        ]);

        Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'daily',
            'report_date' => $threeDaysAgo,
            'content' => 'Report for Maya from THREE days ago.',
            'status' => 'approved',
        ]);
        DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'log_date' => $threeDaysAgo,
            'quarter' => 'Q1',
            'notes' => 'Report for Maya from THREE days ago.',
            'home_recommendations' => 'Three days ago activity recommendation.',
        ]);

        // When requesting two days ago, strictly show two days ago
        $responseTwoDays = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.logs.archive', ['date' => $twoDaysAgo]));

        $responseTwoDays->assertOk();
        $responseTwoDays->assertSee('Report for Maya from TWO days ago.');
        $responseTwoDays->assertDontSee('Report for Maya from THREE days ago.');

        // When requesting three days ago, strictly show three days ago
        $responseThreeDays = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.logs.archive', ['date' => $threeDaysAgo]));

        $responseThreeDays->assertOk();
        $responseThreeDays->assertSee('Report for Maya from THREE days ago.');
        $responseThreeDays->assertDontSee('Report for Maya from TWO days ago.');
    }
}
