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
        $response->assertSee('Daily Reports & Home Recommendation', false);
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
}
