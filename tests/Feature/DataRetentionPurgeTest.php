<?php

namespace Tests\Feature;

use App\Models\ArchivedStudent;
use App\Models\Comment;
use App\Models\DailyLog;
use App\Models\DailyLogDraft;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\DataRetentionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class DataRetentionPurgeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $adminWithoutPermission;
    protected User $teacher;
    protected User $guardian;
    protected string $twoFactorSecret = 'JBSWY3DPEHPK3PXP';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'name'                 => 'Admin Supervisor',
            'role'                 => 'admin',
            'is_active'            => true,
            'password_changed_at'  => now(),
            'two_factor_secret'    => $this->twoFactorSecret,
            'mfa_enabled'          => true,
            'can_archive_students' => true,
            'can_create_profiles'  => true,
            'password'             => Hash::make('AdminPass123!'),
        ]);

        $this->adminWithoutPermission = User::factory()->create([
            'name'                 => 'Restricted Admin',
            'role'                 => 'admin',
            'is_active'            => true,
            'password_changed_at'  => now(),
            'two_factor_secret'    => $this->twoFactorSecret,
            'mfa_enabled'          => true,
            'can_archive_students' => false,
        ]);

        $this->teacher = User::factory()->create([
            'name'                => 'SPED Specialist',
            'role'                => 'teacher',
            'is_active'           => true,
            'password_changed_at' => now(),
        ]);

        $this->guardian = User::factory()->create([
            'name'                => 'Parent Maria',
            'role'                => 'guardian',
            'is_active'           => true,
            'password_changed_at' => now(),
        ]);
    }

    /**
     * Helper to generate a valid 2FA OTP.
     */
    protected function getValidOtp(): string
    {
        return (new Google2FA())->getCurrentOtp($this->twoFactorSecret);
    }

    /**
     * Test 1: Graduation and archival timestamps are properly tracked and reset on re-enrollment.
     */
    public function test_graduated_and_archived_dates_are_tracked_and_reset_on_reenrollment(): void
    {
        $student = Student::create([
            'first_name'                => 'Leo',
            'last_name'                 => 'Santos',
            'learners_reference_number' => '123456789012',
            'class_name'                => 'Grade 1 - Faith',
            'date_of_birth'             => '2015-05-10',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Autism',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'active',
        ]);

        // 1. Transition to Graduated via AdminStudentController@update
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name'                => 'Leo',
                'last_name'                 => 'Santos',
                'learners_reference_number' => '123456789012',
                'class_name'                => 'Grade 1 - Faith',
                'date_of_birth'             => '2015-05-10',
                'gender'                    => 'Male',
                'diagnosis'                 => 'Autism',
                'teacher_id'                => $this->teacher->id,
                'guardian_id'               => $this->guardian->id,
                'status'                    => 'graduated',
            ]);

        $student->refresh();
        $this->assertEquals('graduated', $student->status);
        $this->assertNotNull($student->graduated_at);
        $this->assertNotNull($student->retention_start_date);

        // 2. Re-enroll student back into active status
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $student->id), [
                'first_name'                => 'Leo',
                'last_name'                 => 'Santos',
                'learners_reference_number' => '123456789012',
                'class_name'                => 'Grade 2 - Hope',
                'date_of_birth'             => '2015-05-10',
                'gender'                    => 'Male',
                'diagnosis'                 => 'Autism',
                'teacher_id'                => $this->teacher->id,
                'guardian_id'               => $this->guardian->id,
            ]);

        $student->refresh();
        $this->assertEquals('active', $student->status);
        $this->assertNull($student->graduated_at);
        $this->assertNull($student->archived_at);
        $this->assertFalse($student->legal_hold);
        $this->assertNull($student->retention_start_date);
    }

    /**
     * Test 2: Active student records are NEVER purged, even if retention start date is simulated in the distant past.
     */
    public function test_active_students_are_never_purged_even_if_older_than_five_years(): void
    {
        $activeStudent = Student::create([
            'first_name'                => 'Active',
            'last_name'                 => 'Student',
            'learners_reference_number' => '111111111111',
            'class_name'                => 'Grade 3 - Joy',
            'date_of_birth'             => '2012-01-01',
            'gender'                    => 'Female',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'active',
            'archived_at'               => now()->subYears(6), // Even if archived_at is old
            'legal_hold'                => false,
        ]);

        $service = app(DataRetentionService::class);
        $this->assertFalse($activeStudent->isEligibleForPurge());
        $this->assertEquals(0, $service->getEligibleCount());

        $this->expectException(\InvalidArgumentException::class);
        $service->purgeStudent($activeStudent);
    }

    /**
     * Test 3: Records younger than the 5-year retention period are NOT eligible for deletion.
     */
    public function test_records_under_five_years_are_not_eligible_for_purge(): void
    {
        $recentArchived = Student::create([
            'first_name'                => 'Recently',
            'last_name'                 => 'Archived',
            'learners_reference_number' => '222222222222',
            'class_name'                => 'Grade 4 - Love',
            'date_of_birth'             => '2014-03-15',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Dyslexia',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(3), // Only 3 years old (< 5 years)
            'legal_hold'                => false,
        ]);

        $service = app(DataRetentionService::class);
        $this->assertFalse($recentArchived->isEligibleForPurge());
        $this->assertEquals(0, $service->getEligibleCount());
    }

    /**
     * Test 4: Records under active legal hold are strictly protected from purge.
     */
    public function test_records_under_legal_hold_are_exempt_from_purge(): void
    {
        $heldStudent = Student::create([
            'first_name'                => 'Protected',
            'last_name'                 => 'Record',
            'learners_reference_number' => '333333333333',
            'class_name'                => 'Grade 5 - Peace',
            'date_of_birth'             => '2010-02-20',
            'gender'                    => 'Female',
            'diagnosis'                 => 'Down Syndrome',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'graduated',
            'graduated_at'              => now()->subYears(6), // 6 years old (> 5 years)
            'legal_hold'                => true,
            'legal_hold_reason'         => 'Pending Special Education compliance review',
        ]);

        $service = app(DataRetentionService::class);
        $this->assertFalse($heldStudent->isEligibleForPurge());
        $this->assertEquals(0, $service->getEligibleCount());
        $this->assertEquals(1, $service->getLegalHoldCount());

        $this->expectException(\InvalidArgumentException::class);
        $service->purgeStudent($heldStudent);
    }

    /**
     * Test 5: Dry-Run simulation calculates affected counts without deleting database records or disk files.
     */
    public function test_dry_run_mode_simulates_without_deleting_database_or_files(): void
    {
        $student = Student::create([
            'first_name'                => 'Simulated',
            'last_name'                 => 'Purge',
            'learners_reference_number' => '444444444444',
            'class_name'                => 'Grade 6 - Grace',
            'date_of_birth'             => '2009-08-12',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Autism',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(6),
            'legal_hold'                => false,
        ]);

        // Create sample file on fake local disk
        $filePath = 'student_documents/simulated_test.pdf';
        Storage::disk('local')->put($filePath, 'Test Document Contents');

        StudentDocument::create([
            'student_id'    => $student->id,
            'file_path'     => $filePath,
            'original_name' => 'test.pdf',
            'file_size'     => 1024,
            'mime_type'     => 'application/pdf',
            'is_archived'   => true,
        ]);

        $service = app(DataRetentionService::class);
        $this->assertEquals(1, $service->getEligibleCount());

        $results = $service->runPurge(dryRun: true, initiator: $this->admin);

        $this->assertTrue($results['dry_run']);
        $this->assertEquals(1, $results['total_students_processed']);
        $this->assertEquals(1, $results['documents_deleted']);
        $this->assertEquals(1, $results['files_deleted']);

        // Verify DB record and physical file STILL EXIST
        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseHas('student_documents', ['student_id' => $student->id]);
        $this->assertTrue(Storage::disk('local')->exists($filePath));
    }

    /**
     * Test 6: Permanent purge deletes all historical DB records, unlinks physical files, and unlinks polymorphic comments.
     */
    public function test_complete_historical_data_and_files_purged_after_five_years(): void
    {
        $student = Student::create([
            'first_name'                => 'Historical',
            'last_name'                 => 'Student',
            'learners_reference_number' => '555555555555',
            'class_name'                => 'Grade 6 - Grace',
            'date_of_birth'             => '2008-04-10',
            'gender'                    => 'Female',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'graduated',
            'graduated_at'              => now()->subYears(5)->subMonths(3), // 5.25 years ago
            'legal_hold'                => false,
        ]);

        // 1. Physical Master PDF Snapshot
        $archivePdfPath = 'archives/2020-2021/' . $student->id . '_snapshot.pdf';
        Storage::disk('local')->put($archivePdfPath, 'Master Archive PDF Stream');

        $archive = ArchivedStudent::create([
            'student_id'       => $student->id,
            'school_year'      => '2020-2021',
            'student_snapshot' => ['name' => 'Historical Student'],
            'iep_snapshot'     => [],
            'progress_snapshot'=> [],
            'master_pdf_path'  => $archivePdfPath,
            'archived_at'      => now()->subYears(5)->subMonths(3),
        ]);

        // 2. Student Document
        $docPath = 'student_documents/historical_cert.pdf';
        Storage::disk('local')->put($docPath, 'Official School Document');
        $doc = StudentDocument::create([
            'student_id'          => $student->id,
            'archived_student_id' => $archive->id,
            'file_path'           => $docPath,
            'original_name'       => 'cert.pdf',
            'file_size'           => 2048,
            'mime_type'           => 'application/pdf',
            'is_archived'         => true,
        ]);

        // 3. Report with observation photo and comment
        $reportPhotoPath = 'reports/photo_historical.jpg';
        Storage::disk('local')->put($reportPhotoPath, 'Photo Binary Data');
        $report = Report::create([
            'student_id'  => $student->id,
            'teacher_id'  => $this->teacher->id,
            'report_type' => 'year_end',
            'report_date' => '2021-03-25',
            'content'     => 'Year end progress evaluation',
            'image_path'  => $reportPhotoPath,
            'status'      => 'approved',
        ]);
        $reportComment = Comment::create([
            'commentable_type' => Report::class,
            'commentable_id'   => $report->id,
            'user_id'          => $this->teacher->id,
            'body'             => 'Teacher observation comment',
        ]);

        // 4. Daily Log with photo and comment
        $logPhotoPath = 'daily_logs/activity_photo.jpg';
        Storage::disk('local')->put($logPhotoPath, 'Daily Log Photo Data');
        $dailyLog = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => $this->teacher->id,
            'log_date'   => '2021-02-14',
            'quarter'    => 'Q3',
            'notes'      => 'Historical routine daily log',
            'image_path' => $logPhotoPath,
        ]);
        $logComment = Comment::create([
            'commentable_type' => DailyLog::class,
            'commentable_id'   => $dailyLog->id,
            'user_id'          => $this->guardian->id,
            'body'             => 'Parent acknowledgement',
        ]);

        // 5. Daily Log Draft
        DailyLogDraft::create([
            'student_id'   => $student->id,
            'teacher_id'   => $this->teacher->id,
            'log_date'     => '2021-02-15',
            'quarter'      => 'Q3',
            'raw_notes'    => 'Unfinished draft notes',
            'formal_notes' => 'Draft',
        ]);

        // 6. IEP Goal with comment
        $iepGoal = IepGoal::create([
            'student_id'       => $student->id,
            'domain'           => 'Cognitive',
            'goal_description' => 'Reading Comprehension Goal',
            'status'           => 'achieved',
        ]);
        $goalComment = Comment::create([
            'commentable_type' => IepGoal::class,
            'commentable_id'   => $iepGoal->id,
            'user_id'          => $this->teacher->id,
            'body'             => 'Goal attained successfully',
        ]);

        // Verify all exist before purge
        $this->assertTrue(Storage::disk('local')->exists($archivePdfPath));
        $this->assertTrue(Storage::disk('local')->exists($docPath));
        $this->assertTrue(Storage::disk('local')->exists($reportPhotoPath));
        $this->assertTrue(Storage::disk('local')->exists($logPhotoPath));

        // EXECUTE LIVE PURGE
        $service = app(DataRetentionService::class);
        $results = $service->runPurge(dryRun: false, initiator: $this->admin);

        $this->assertFalse($results['dry_run']);
        $this->assertEquals(1, $results['total_students_processed']);
        $this->assertEquals(1, $results['archived_snapshots_deleted']);
        $this->assertEquals(1, $results['documents_deleted']);
        $this->assertEquals(1, $results['reports_deleted']);
        $this->assertEquals(1, $results['daily_logs_deleted']);
        $this->assertEquals(1, $results['daily_log_drafts_deleted']);
        $this->assertEquals(1, $results['iep_goals_deleted']);
        $this->assertEquals(4, $results['files_deleted']);

        // Verify Database Records Eradicated
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('archived_students', ['id' => $archive->id]);
        $this->assertDatabaseMissing('student_documents', ['id' => $doc->id]);
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
        $this->assertDatabaseMissing('daily_logs', ['id' => $dailyLog->id]);
        $this->assertDatabaseMissing('daily_log_drafts', ['student_id' => $student->id]);
        $this->assertDatabaseMissing('iep_goals', ['id' => $iepGoal->id]);
        $this->assertDatabaseMissing('comments', ['id' => $reportComment->id]);
        $this->assertDatabaseMissing('comments', ['id' => $logComment->id]);
        $this->assertDatabaseMissing('comments', ['id' => $goalComment->id]);

        // Verify Physical Disk Files Deleted
        $this->assertFalse(Storage::disk('local')->exists($archivePdfPath));
        $this->assertFalse(Storage::disk('local')->exists($docPath));
        $this->assertFalse(Storage::disk('local')->exists($reportPhotoPath));
        $this->assertFalse(Storage::disk('local')->exists($logPhotoPath));
    }

    /**
     * Test 7: Artisan scheduled command executes automated purge cleanly.
     */
    public function test_artisan_command_purges_expired_records(): void
    {
        $expiredStudent = Student::create([
            'first_name'                => 'Scheduled',
            'last_name'                 => 'Student',
            'learners_reference_number' => '666666666666',
            'class_name'                => 'Grade 6 - Grace',
            'date_of_birth'             => '2008-01-01',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Autism',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(6),
            'legal_hold'                => false,
        ]);

        $exitCode = Artisan::call('data-retention:purge', [
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseMissing('students', ['id' => $expiredStudent->id]);
    }

    /**
     * Test 8a: Unauthorized users without can_archive_students permission receive 403 Forbidden.
     */
    public function test_unauthorized_user_without_archive_permission_cannot_access_retention_dashboard(): void
    {
        $unauthorizedTeacher = User::factory()->create([
            'name'                 => 'Restricted Teacher',
            'role'                 => 'teacher',
            'is_active'            => true,
            'password_changed_at'  => now(),
            'two_factor_secret'    => $this->twoFactorSecret,
            'can_archive_students' => false,
        ]);

        $this->actingAs($unauthorizedTeacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.data-retention.index'))
            ->assertStatus(403);
    }

    /**
     * Test 8b: Admin authorization and 2FA OTP verification for manual web purge.
     */
    public function test_admin_authorization_and_2fa_verification_for_manual_purge(): void
    {
        $expiredStudent = Student::create([
            'first_name'                => 'WebPurge',
            'last_name'                 => 'Student',
            'learners_reference_number' => '777777777777',
            'class_name'                => 'Grade 6',
            'date_of_birth'             => '2007-01-01',
            'gender'                    => 'Female',
            'diagnosis'                 => 'Down Syndrome',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(6),
            'legal_hold'                => false,
        ]);

        // 1. Authorized admin can view retention dashboard
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.data-retention.index'))
            ->assertStatus(200)
            ->assertSee('5-Year Post-Graduation Data Retention Protocol')
            ->assertSee('WebPurge Student');

        // 3. Purge fails if confirmation phrase is incorrect
        $otp = $this->getValidOtp();
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.data-retention.purge'), [
                'one_time_password' => $otp,
                'confirmation'      => 'INCORRECT',
            ])
            ->assertSessionHasErrors('confirmation');

        // 4. Purge fails if 2FA OTP is invalid
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.data-retention.purge'), [
                'one_time_password' => '000000',
                'confirmation'      => 'PURGE',
            ])
            ->assertSessionHasErrors('one_time_password');

        // 5. Successful 2FA and confirmation executes permanent purge
        $validOtp = $this->getValidOtp();
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.data-retention.purge'), [
                'one_time_password' => $validOtp,
                'confirmation'      => 'PURGE',
            ])
            ->assertRedirect(route('admin.data-retention.index'))
            ->assertSessionHas('purge_results');

        $this->assertDatabaseMissing('students', ['id' => $expiredStudent->id]);
    }

    /**
     * Test 9: Anonymized audit logging logs operations without exposing student PII.
     */
    public function test_audit_log_records_purge_without_exposing_sensitive_pii(): void
    {
        $student = Student::create([
            'first_name'                => 'Secret',
            'last_name'                 => 'Individual',
            'learners_reference_number' => '888888888888',
            'class_name'                => 'Grade 6',
            'date_of_birth'             => '2008-01-01',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Autism',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(6),
            'legal_hold'                => false,
        ]);

        $service = app(DataRetentionService::class);
        $service->runPurge(dryRun: false, initiator: $this->admin);

        // Find the audit log
        $log = UserActivityLog::where('action', 'Data Retention Purge')->latest()->first();

        $this->assertNotNull($log);
        $this->assertEquals('data_purge', $log->action_type);
        $this->assertNull($log->student_name); // Zero PII leakage
        $this->assertStringNotContainsString('Secret Individual', $log->details);
        $this->assertStringContainsString('1 student(s) processed', $log->details);
    }

    /**
     * Test 10: Legal hold toggle endpoint sets and clears legal hold with audit reason.
     */
    /**
     * Test 10: Legal hold toggle endpoint sets and clears legal hold with audit reason.
     */
    public function test_legal_hold_toggle_and_reason(): void
    {
        $student = Student::create([
            'first_name'                => 'Audited',
            'last_name'                 => 'Student',
            'learners_reference_number' => '999999999999',
            'class_name'                => 'Grade 5',
            'date_of_birth'             => '2010-01-01',
            'gender'                    => 'Female',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $this->teacher->id,
            'guardian_id'               => $this->guardian->id,
            'status'                    => 'archived',
            'archived_at'               => now()->subYears(6),
            'legal_hold'                => false,
        ]);

        // Place Legal Hold
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.data-retention.legal-hold', $student->id), [
                'legal_hold'        => 1,
                'legal_hold_reason' => 'Government agency subpoena #12345',
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertTrue($student->legal_hold);
        $this->assertEquals('Government agency subpoena #12345', $student->legal_hold_reason);

        // Release Legal Hold
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.data-retention.legal-hold', $student->id), [
                'legal_hold'        => 0,
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertFalse($student->legal_hold);
        $this->assertNull($student->legal_hold_reason);
    }

    /**
     * Test 11: Retention period is configurable and subject to min/max validation.
     */
    public function test_retention_period_is_configurable_and_validated(): void
    {
        $service = app(DataRetentionService::class);

        // Default 5 years
        $this->assertEquals(5, $service->getEffectiveRetentionYears());

        // Configure custom 3 years
        config(['retention.period_years' => 3]);
        $this->assertEquals(3, $service->getEffectiveRetentionYears());

        // Under min (0) clamps to min (1)
        config(['retention.period_years' => 0]);
        $this->assertEquals(1, $service->getEffectiveRetentionYears());

        // Over max (15) clamps to max (10)
        config(['retention.period_years' => 15]);
        $this->assertEquals(10, $service->getEffectiveRetentionYears());
    }
}
