<?php

namespace Tests\Feature;

use App\Models\AcademicQuarter;
use App\Models\DailyLog;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentInputLockAndDirectNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_save_daily_log_directly_with_formal_notes_without_quick_notes(): void
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
            'first_name' => 'Leo',
            'last_name' => 'Cruz',
            'learners_reference_number' => '123456789011',
            'class_name' => 'Kinder Self-Control',
            'date_of_birth' => '2018-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.daily-logs.store'), [
                'student_id' => $student->id,
                'log_date'   => '2026-10-27',
                'quarter'    => 'Q1',
                'raw_notes'  => '', // empty quick notes
                'notes'      => 'Directly typed formal notes by the teacher without quick notes.',
            ]);

        $response->assertRedirect(route('teacher.daily-logs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('daily_logs', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'notes'      => 'Directly typed formal notes by the teacher without quick notes.',
        ]);
    }

    public function test_daily_log_requires_either_raw_notes_or_formal_notes(): void
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
            'first_name' => 'Leo',
            'last_name' => 'Cruz',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Kinder Self-Control',
            'date_of_birth' => '2018-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.daily-logs.store'), [
                'student_id' => $student->id,
                'log_date'   => '2026-10-27',
                'quarter'    => 'Q1',
                'raw_notes'  => '   ',
                'notes'      => '   ',
            ]);

        $response->assertSessionHasErrors(['notes']);
    }

    public function test_daily_logs_ui_renders_student_input_lock_logic(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.daily-logs.index'));

        $response->assertOk();
        $response->assertSee('updateStudentInputState', false);
        $response->assertSee('noStudentNotice', false);
        $response->assertSee('Please select a student above to start typing quick notes or formal documentation', false);
    }

    public function test_reports_create_ui_renders_student_input_lock_logic(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.reports.create'));

        $response->assertOk();
        $response->assertSee('applyStudentSelectionLock', false);
        $response->assertSee('reportNoStudentNotice', false);
        $response->assertSee('Please select a student in Step 1 to unlock report typing and AI generation', false);
    }

    public function test_quarters_ui_renders_confirm_typing_modal(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'can_manage_calendar' => true,
        ]);

        $sy = SchoolYear::create([
            'name' => 'SY 2027-2028',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.quarters.index'));

        $response->assertOk();
        $response->assertSee('openSetActiveConfirm', false);
        $response->assertSee('openDeleteConfirm', false);
        $response->assertSee('syConfirmInput', false);
        $response->assertSee('Type CONFIRM', false);
    }
}
