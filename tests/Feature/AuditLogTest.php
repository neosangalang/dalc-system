<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\ArchivedStudent;
use App\Models\SchoolYear;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Spatie roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'guardian']);
    }

    protected function createAdmin(): User
    {
        $admin = User::factory()->create([
            'role'                => 'admin',
            'username'            => 'admin_audit',
            'is_active'           => true,
            'password_changed_at' => now(),
            'two_factor_secret'   => encrypt('JBSWY3DPEHPK3PXP'),
            'mfa_enabled'         => true,
        ]);
        $admin->assignRole('admin');
        return $admin;
    }

    protected function createTeacher(): User
    {
        $teacher = User::factory()->create([
            'role'                => 'teacher',
            'username'            => 'sped_teacher',
            'is_active'           => true,
            'password_changed_at' => now(),
            'two_factor_secret'   => encrypt('JBSWY3DPEHPK3PXP'),
            'mfa_enabled'         => true,
        ]);
        $teacher->assignRole('teacher');
        return $teacher;
    }

    protected function createGuardian(): User
    {
        $guardian = User::factory()->create([
            'role'                => 'guardian',
            'username'            => 'parent_user',
            'is_active'           => true,
            'password_changed_at' => now(),
            'two_factor_secret'   => null,
            'mfa_enabled'         => false,
        ]);
        $guardian->assignRole('guardian');
        return $guardian;
    }

    public function test_user_activity_log_record_helper_creates_log()
    {
        $admin = $this->createAdmin();

        $log = UserActivityLog::record(
            action: 'Document Approval/Stamping',
            actionType: 'document_approval',
            details: 'Approved Q1 report for John Doe',
            targetType: 'Report',
            targetId: 42,
            studentName: 'John Doe',
            user: $admin
        );

        $this->assertNotNull($log);
        $this->assertDatabaseHas('user_activity_logs', [
            'id'           => $log->id,
            'user_name'    => $admin->name,
            'user_role'    => 'Admin',
            'action'       => 'Document Approval/Stamping',
            'action_type'  => 'document_approval',
            'target_id'    => '42',
            'student_name' => 'John Doe',
        ]);
    }

    public function test_audit_logs_index_requires_admin_role()
    {
        $teacher = $this->createTeacher();

        // Teachers should receive 403 Forbidden
        $response = $this->actingAs($teacher)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);

        // Admins can access successfully
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.audit-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('System Audit Logs');
    }

    public function test_multi_parameter_filters_work()
    {
        $admin = $this->createAdmin();

        // Create diverse sample logs
        UserActivityLog::record(
            action: 'IEP Creation',
            actionType: 'iep_creation',
            details: 'Created goal for Neo Vance',
            targetType: 'IepGoal',
            targetId: 101,
            studentName: 'Neo Vance',
            user: $admin
        );

        UserActivityLog::record(
            action: 'Document Approval/Stamping',
            actionType: 'document_approval',
            details: 'Approved report for Alice Wonderland',
            targetType: 'Report',
            targetId: 202,
            studentName: 'Alice Wonderland',
            user: $admin
        );

        // 1. Filter by student name
        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.audit-logs.index', ['student_name' => 'Neo Vance']));
        $response->assertStatus(200);
        $response->assertSee('Neo Vance');
        $response->assertDontSee('Alice Wonderland');

        // 2. Filter by action type
        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.audit-logs.index', ['action_type' => 'document_approval']));
        $response->assertStatus(200);
        $response->assertSee('Alice Wonderland');
        $response->assertDontSee('Neo Vance');
    }

    public function test_audit_logs_csv_export_works()
    {
        $admin = $this->createAdmin();

        UserActivityLog::record(
            action: 'Student Archiving',
            actionType: 'student_archiving',
            details: 'Year-End Rollover completed for SY 2025-2026',
            targetType: 'ArchivedStudent',
            targetId: 99,
            studentName: 'Batch Archive',
            user: $admin
        );

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.audit-logs.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Student Archiving', $response->streamedContent());
        $this->assertStringContainsString('Year-End Rollover completed for SY 2025-2026', $response->streamedContent());
    }

    public function test_student_profile_renders_historical_views_tab()
    {
        $admin = $this->createAdmin();
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();

        $student = Student::create([
            'first_name'                => 'Timothy',
            'last_name'                 => 'Drake',
            'learners_reference_number' => '123456789012',
            'class_name'                => 'SPED Room 101',
            'date_of_birth'             => '2015-05-15',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Autism Spectrum Disorder',
            'teacher_id'                => $teacher->id,
            'guardian_id'               => $guardian->id,
            'status'                    => 'active',
        ]);

        // Create an archived snapshot
        ArchivedStudent::create([
            'student_id'        => $student->id,
            'school_year'       => '2025-2026',
            'student_snapshot'  => $student->toArray(),
            'iep_snapshot'      => [
                [
                    'domain' => 'cognitive-academic',
                    'goal_description' => 'OBJECTIVE: Master single digit addition. ACTIVITIES: Token board.',
                    'progress_percentage' => 100,
                    'plop' => 'Can identify numerals 1 to 20.',
                ]
            ],
            'progress_snapshot' => [
                [
                    'report_type' => 'q1',
                    'report_date' => '2025-10-15',
                    'content' => 'Consistent growth shown during Q1 sessions.',
                ]
            ],
            'archived_at'       => now()->subYear(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.show', $student->id));

        $response->assertStatus(200);
        $response->assertSee('Active Year Records');
        $response->assertSee('Historical Views');
        $response->assertSee('SY 2025-2026');
        $response->assertSee('Longitudinal Domain Progress Matrix');

        // Verify Teacher view also renders Historical Views
        $teacherResponse = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.students.show', $student->id));

        $teacherResponse->assertStatus(200);
        $teacherResponse->assertSee('Historical Views');
        $teacherResponse->assertSee('SY 2025-2026');
    }

    public function test_single_audit_log_show_endpoint_returns_json()
    {
        $admin = $this->createAdmin();

        $log = UserActivityLog::record(
            action: 'User Login',
            actionType: 'login',
            details: 'Successful user authentication',
            targetType: 'User',
            targetId: $admin->id,
            user: $admin
        );

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.audit-logs.show', $log->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'log' => [
                'id'        => $log->id,
                'user_name' => $admin->name,
                'action'    => 'User Login',
            ]
        ]);
    }

    public function test_login_and_logout_automatically_record_audit_logs()
    {
        $admin = $this->createAdmin();

        // 1. Test Login
        $response = $this->post(route('login'), [
            'username' => 'admin_audit',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('user_activity_logs', [
            'user_name'   => $admin->name,
            'action'      => 'User Login',
            'action_type' => 'login',
        ]);

        // 2. Test Logout
        $logoutResponse = $this->actingAs($admin)
            ->post(route('logout'));

        $this->assertDatabaseHas('user_activity_logs', [
            'user_name'   => $admin->name,
            'action'      => 'User Logout',
            'action_type' => 'logout',
        ]);
    }

    public function test_iep_creation_records_audit_log()
    {
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();

        $student = Student::create([
            'first_name'                => 'Bruce',
            'last_name'                 => 'Wayne',
            'learners_reference_number' => '987654321098',
            'class_name'                => 'Room 2B',
            'date_of_birth'             => '2016-02-19',
            'gender'                    => 'Male',
            'diagnosis'                 => 'Speech Delay',
            'teacher_id'                => $teacher->id,
            'guardian_id'               => $guardian->id,
            'status'                    => 'active',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.iep-goals.store'), [
                'student_id'       => $student->id,
                'domain'           => 'communication-language',
                'plop'             => 'Communicates in 2-word phrases.',
                'goal_description' => 'OBJECTIVE: Speak in 4-word sentences.',
            ]);

        $this->assertDatabaseHas('user_activity_logs', [
            'action'       => 'IEP Creation',
            'action_type'  => 'iep_creation',
            'student_name' => 'Bruce Wayne',
        ]);
    }

    public function test_report_approval_records_audit_log()
    {
        $admin = $this->createAdmin();
        $teacher = $this->createTeacher();
        $guardian = $this->createGuardian();

        $student = Student::create([
            'first_name'                => 'Clark',
            'last_name'                 => 'Kent',
            'learners_reference_number' => '112233445566',
            'class_name'                => 'Room 3C',
            'date_of_birth'             => '2016-06-18',
            'gender'                    => 'Male',
            'diagnosis'                 => 'ADHD',
            'teacher_id'                => $teacher->id,
            'guardian_id'               => $guardian->id,
            'status'                    => 'active',
        ]);

        $report = Report::create([
            'student_id'  => $student->id,
            'teacher_id'  => $teacher->id,
            'report_type' => 'q1',
            'content'     => 'Clark has excelled this quarter.',
            'status'      => 'pending_approval',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.report-approval.update-status', $report->id), [
                'status' => 'approved',
            ]);

        $this->assertDatabaseHas('user_activity_logs', [
            'action'       => 'Document Approval/Stamping',
            'action_type'  => 'document_approval',
            'target_id'    => (string) $report->id,
            'student_name' => 'Clark Kent',
        ]);
    }
}
