<?php

namespace Tests\Feature;

use App\Models\AcademicQuarter;
use App\Models\ArchivedStudent;
use App\Models\DailyLog;
use App\Models\Report;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class YearEndArchivingAndQuarterlyRolloverTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $teacher;
    protected User $guardian;
    protected Student $student;
    protected SchoolYear $schoolYear;
    protected AcademicQuarter $q1;
    protected AcademicQuarter $q2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolYear = SchoolYear::create([
            'name' => '2026-2027',
            'is_active' => true,
        ]);

        $this->q1 = AcademicQuarter::create([
            'name' => 'Q1',
            'school_year_id' => $this->schoolYear->id,
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
            'is_active' => true,
        ]);

        $this->q2 = AcademicQuarter::create([
            'name' => 'Q2',
            'school_year_id' => $this->schoolYear->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-11-30',
            'is_active' => false,
        ]);

        $this->teacher = User::factory()->create([
            'name' => 'Teacher Sarah',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'name' => 'Parent John',
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
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

    public function test_quarterly_rollover_purges_daily_reports_while_preserving_raw_teacher_daily_logs(): void
    {
        // 1. Simulate date at the end of Q1
        Carbon::setTestNow(Carbon::parse('2026-08-31 12:00:00'));

        // 2. Create a Daily Report for Q1
        $dailyReport = Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'daily',
            'report_date' => '2026-07-15',
            'content' => 'Lucas completed all morning sensory activities successfully.',
            'status' => 'approved',
        ]);

        // 3. Create a Quarterly Progress Report (non-daily) for Q1
        $quarterlyReport = Report::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'report_type' => 'q1',
            'report_date' => '2026-08-30',
            'content' => 'Lucas made solid progress on all Q1 milestones.',
            'status' => 'approved',
        ]);

        // 4. Create raw Teacher Daily Log in daily_logs table for Q1
        $dailyLog = DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => '2026-07-15',
            'quarter' => 'Q1',
            'notes' => 'Lucas completed all morning sensory activities successfully.',
            'home_recommendations' => 'Practice sensory calming breathing before bedtime.',
        ]);

        $this->artisan('system:auto-rollover')
            ->expectsOutput("Today is the end of Q1. Starting automatic rollover...")
            ->expectsOutput("1 reports archived.")
            ->expectsOutput("1 daily reports purged (raw teacher daily logs permanently preserved).")
            ->expectsOutput("Rollover complete!")
            ->assertSuccessful();

        Carbon::setTestNow(); // Reset time

        // 5. Assertions:
        // - Daily Report must be PURGED from reports table
        $this->assertDatabaseMissing('reports', ['id' => $dailyReport->id]);

        // - Non-daily quarterly report must be ARCHIVED
        $this->assertDatabaseHas('reports', [
            'id' => $quarterlyReport->id,
            'status' => 'archived',
        ]);

        // - Raw teacher Daily Log must be PERMANENTLY PRESERVED in daily_logs
        $this->assertDatabaseHas('daily_logs', [
            'id' => $dailyLog->id,
            'student_id' => $this->student->id,
            'notes' => 'Lucas completed all morning sensory activities successfully.',
        ]);
    }

    public function test_year_end_archiving_purges_and_deletes_all_active_teacher_daily_logs_for_academic_year(): void
    {
        // 1. Create active Teacher Daily Logs for Lucas
        $log1 = DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => '2026-07-10',
            'quarter' => 'Q1',
            'notes' => 'Day 1 teacher notes',
        ]);

        $log2 = DailyLog::create([
            'student_id' => $this->student->id,
            'teacher_id' => $this->teacher->id,
            'log_date' => '2026-10-15',
            'quarter' => 'Q2',
            'notes' => 'Day 2 teacher notes',
        ]);

        $this->assertDatabaseCount('daily_logs', 2);

        // 2. Generate valid 2FA code
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $otp = $google2fa->getCurrentOtp($this->admin->two_factor_secret);

        // 3. Attempt without CONFIRM phrase when field is submitted -> Should fail validation
        $failResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.archive.run'), [
                'one_time_password' => $otp,
                'confirmation' => 'WRONG_CONFIRM',
                'school_year' => '2026-2027',
            ]);

        $failResponse->assertSessionHasErrors('confirmation');
        $this->assertDatabaseCount('daily_logs', 2); // Logs are not purged

        // 4. Run Year-End Archiving with valid 2FA and CONFIRM text
        $successResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.archive.run'), [
                'one_time_password' => $otp,
                'confirmation' => 'CONFIRM',
                'school_year' => '2026-2027',
            ]);

        $successResponse->assertSessionHas('success');

        // 5. Assert: Active students are archived
        $this->assertEquals('archived', $this->student->fresh()->status);
        $this->assertDatabaseHas('archived_students', ['student_id' => $this->student->id]);

        // 6. Assert: All active Teacher Daily Logs for that academic year are purged and deleted
        $this->assertDatabaseMissing('daily_logs', ['id' => $log1->id]);
        $this->assertDatabaseMissing('daily_logs', ['id' => $log2->id]);
        $this->assertDatabaseCount('daily_logs', 0);
    }
}
