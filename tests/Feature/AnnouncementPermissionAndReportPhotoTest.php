<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnouncementPermissionAndReportPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $guardian;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

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
            'can_manage_announcements' => false,
        ]);

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->student = Student::create([
            'first_name' => 'Neo',
            'last_name' => 'Tester',
            'learners_reference_number' => '123456789099',
            'class_name' => 'Grade 1 - Hope',
            'date_of_birth' => '2015-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
        ]);
    }

    public function test_teacher_without_announcement_permission_cannot_access_admin_announcements(): void
    {
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.announcements.index'));

        $response->assertForbidden();
    }

    public function test_teacher_with_announcement_permission_can_access_admin_announcements(): void
    {
        $this->teacher->update(['can_manage_announcements' => true]);

        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.announcements.index'));

        $response->assertOk();
    }

    public function test_admin_can_update_teacher_announcement_permission(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->patch(route('admin.accounts.permissions', $this->teacher->id), [
                'can_manage_announcements' => '1',
                'can_manage_credentials' => '1',
            ]);

        $response->assertRedirect();
        $this->teacher->refresh();
        $this->assertTrue($this->teacher->can_manage_announcements);
        $this->assertTrue($this->teacher->can_manage_credentials);
    }

    public function test_accounts_view_has_renamed_label_and_announcement_module(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.accounts.index'));

        $response->assertOk();
        $response->assertSee('1. Login Credentials Configuration (FOR GUARDIAN ONLY)', false);
        $response->assertSee('6. Announcement Module', false);
    }

    public function test_guardian_can_view_report_with_photo_and_home_recommendation(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('observation.jpg');
        $storedPath = $file->store('private_report_photos', 'local');

        $report = Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'daily',
            'report_date' => now()->toDateString(),
            'content' => 'Neo had an engaging learning session today.',
            'image_path' => $storedPath,
            'status' => 'approved',
        ]);

        $dailyLog = DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => now()->toDateString(),
            'quarter' => 'Quarter 1',
            'notes' => 'Neo was attentive.',
            'home_recommendations' => 'Practice sensory motor coordination with blocks.',
            'image_path' => $storedPath,
        ]);

        // Secure photo access for guardian
        $photoResponse = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true])
            ->get(route('secure.report-photo', $report->id));

        $photoResponse->assertOk();

        // Guardian reports page contains photo route and home recommendations
        $reportsView = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $this->student->id])
            ->get(route('guardian.reports'));

        $reportsView->assertOk();
        $reportsView->assertSee(route('secure.report-photo', $report->id), false);
        $reportsView->assertSee('Practice sensory motor coordination with blocks.');
        $reportsView->assertSee('Suggested Home Activity');

        // Guardian recommendations page also shows the photo from report
        $recomView = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $this->student->id])
            ->get(route('guardian.recommendations'));

        $recomView->assertOk();
        $recomView->assertSee('Practice sensory motor coordination with blocks.');
        $recomView->assertSee(route('secure.log-photo', $dailyLog->id), false);
    }
}
