<?php

namespace Tests\Feature;

use App\Models\ArchivedStudent;
use App\Models\Comment;
use App\Models\IepGoal;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IepReEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $priorTeacher;
    protected User $newTeacher;
    protected User $guardian;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Boss',
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
            'can_create_profiles' => true,
        ]);

        $this->priorTeacher = User::factory()->create([
            'name' => 'Prior Teacher',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->newTeacher = User::factory()->create([
            'name' => 'New Teacher',
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'name' => 'Guardian Jane',
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        SchoolYear::create([
            'name' => '2026-2027',
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'first_name' => 'Leo',
            'last_name' => 'Santillan',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Playgroup Morning (Love A)',
            'date_of_birth' => '2019-03-15',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->priorTeacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'archived',
        ]);
    }

    public function test_re_enrollment_resets_iep_goals_and_comments_for_new_school_year(): void
    {
        // 1. Create previous IEP goal with comments
        $goal = IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Previous year baseline PLOP.',
            'goal_description' => "OBJECTIVE:\nPrior year objective\n\nACTIVITIES:\n[x] Playgroup activity",
            'progress_percentage' => 45,
            'status' => 'in_progress',
        ]);

        $comment = Comment::create([
            'user_id' => $this->priorTeacher->id,
            'commentable_type' => IepGoal::class,
            'commentable_id' => $goal->id,
            'body' => 'Good progress made in term 2.',
        ]);

        $this->assertDatabaseHas('iep_goals', ['id' => $goal->id]);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);

        // 2. Perform Re-Enrollment for the new school year
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 3. Assert student is reactivated and updated
        $this->student->refresh();
        $this->assertEquals('active', $this->student->status);
        $this->assertEquals('Grade 1 - Joy', $this->student->class_name);
        $this->assertEquals($this->newTeacher->id, $this->student->teacher_id);

        // 4. Assert previous IEP goals and comments were cleanly removed from active tracking
        $this->assertDatabaseMissing('iep_goals', ['id' => $goal->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->assertCount(0, $this->student->iepGoals);

        // 5. Assert safety snapshot was created in archived_students
        $this->assertDatabaseHas('archived_students', [
            'student_id' => $this->student->id,
            'school_year' => '2026-2027',
        ]);
    }

    public function test_re_enrollment_preserves_existing_archive_snapshot_without_duplicate(): void
    {
        // 1. Existing archive record created earlier during year-end archiving
        ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => 'archives/2025-2026/sample.pdf',
            'archived_at' => now()->subMonths(2),
        ]);

        $goal = IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Gross Motor',
            'plop' => 'Can jump with two feet.',
            'goal_description' => "OBJECTIVE:\nPrior Gross Motor objective\n\nACTIVITIES:\n[ ] Obstacle course",
            'progress_percentage' => 10,
            'status' => 'in_progress',
        ]);

        // 2. Perform Re-Enrollment
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Kinder Self-Control',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals(1, ArchivedStudent::where('student_id', $this->student->id)->count());
        $this->assertDatabaseMissing('iep_goals', ['id' => $goal->id]);
    }

    public function test_teacher_can_add_new_goals_under_same_domains_after_re_enrollment(): void
    {
        // Student previously had Socio-Emotional goal
        IepGoal::create([
            'student_id' => $this->student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Old baseline PLOP from last year.',
            'goal_description' => "OBJECTIVE:\nOld objective\n\nACTIVITIES:\n[x] Old activity",
            'progress_percentage' => 100,
            'status' => 'achieved',
        ]);

        // Re-enroll student with new teacher
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        // New teacher creates a brand-new goal for Socio-Emotional domain without conflict
        $createResponse = $this->actingAs($this->newTeacher)
            ->withSession(['mfa_verified' => true])
            ->post(route('teacher.iep-goals.store'), [
                'student_id' => $this->student->id,
                'domain' => 'Socio-Emotional',
                'plop' => 'Fresh new school year baseline PLOP.',
                'goal_description' => "OBJECTIVE:\nBrand new school year objective\n\nACTIVITIES:\n[ ] New classroom activity",
            ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertCount(1, IepGoal::where('student_id', $this->student->id)->where('domain', 'Socio-Emotional')->get());
        $newGoal = IepGoal::where('student_id', $this->student->id)->first();
        $this->assertEquals('Fresh new school year baseline PLOP.', $newGoal->plop);
        $this->assertEquals(0, $newGoal->progress_percentage);
    }

    public function test_student_master_file_shows_fresh_state_and_archive_link_after_re_enrollment(): void
    {
        ArchivedStudent::create([
            'student_id' => $this->student->id,
            'school_year' => '2025-2026',
            'student_snapshot' => $this->student->toArray(),
            'iep_snapshot' => [],
            'progress_snapshot' => [],
            'master_pdf_path' => 'archives/2025-2026/sample.pdf',
            'archived_at' => now(),
        ]);

        // Re-enroll
        $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.re-enroll', $this->student->id), [
                'first_name' => 'Leo',
                'last_name' => 'Santillan',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2019-03-15',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->newTeacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $showResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.students.show', $this->student->id));

        $showResponse->assertOk();
        $showResponse->assertSee('Currently Enrolled');
        $showResponse->assertSee('No Active IEP Goals Found');
        $showResponse->assertSee('SY 2025-2026 Archive');
    }
}
