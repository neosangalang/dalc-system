<?php

namespace Tests\Feature;

use App\Models\IepGoal;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IepProgressUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createTeacher(string $name = 'Teacher'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'teacher',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
    }

    private function createGuardian(): User
    {
        return User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);
    }

    private function createStudent(User $teacher, User $guardian, string $firstName, string $lastName): Student
    {
        return Student::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'learners_reference_number' => (string) rand(100000000000, 999999999999),
            'class_name' => 'SPED Primary',
            'date_of_birth' => '2017-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
            'status' => 'active',
        ]);
    }

    public function test_updating_iep_progress_flashes_student_and_goal_and_preserves_view_state(): void
    {
        $teacher = $this->createTeacher('Teacher Neo');
        $guardian = $this->createGuardian();
        $student = $this->createStudent($teacher, $guardian, 'Neo', 'Sangalang');

        $goal = IepGoal::create([
            'student_id' => $student->id,
            'domain' => 'Socio-Emotional',
            'plop' => 'Baseline frustration on transitions',
            'goal_description' => "OBJECTIVE: Transition with minimal frustration\nACTIVITIES:\n[ ] Visual schedule\n[ ] Countdown timer",
            'progress_percentage' => 0,
            'status' => 'in_progress',
        ]);

        $updatedDescription = "OBJECTIVE: Transition with minimal frustration\nACTIVITIES:\n[x] Visual schedule\n[ ] Countdown timer";

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->from(route('teacher.iep-goals.index'))
            ->put(route('teacher.iep-goals.update', $goal->id), [
                'goal_description' => $updatedDescription,
                'progress_percentage' => 50,
                'status' => 'in_progress',
                'plop' => 'Updated baseline assessment',
            ]);

        $response->assertRedirect(route('teacher.iep-goals.index'));
        $response->assertSessionHas('success', 'IEP Progress updated successfully!');
        $response->assertSessionHas('active_student_id', $student->id);
        $response->assertSessionHas('active_goal_id', $goal->id);

        $goal->refresh();
        $this->assertEquals(50, $goal->progress_percentage);
        $this->assertEquals('Updated baseline assessment', $goal->plop);

        // Following the redirect should render with active accordion and open goal for this student
        $indexResponse = $this->actingAs($teacher)
            ->withSession([
                'mfa_verified' => true,
                'active_student_id' => $student->id,
                'active_goal_id' => $goal->id,
            ])
            ->get(route('teacher.iep-goals.index'));

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee("activeAccordion: {$student->id}", false);
        $indexResponse->assertSee("openGoalId: {$goal->id}", false);
        $indexResponse->assertSee('50% Mastery Level');
    }

    public function test_creating_iep_goal_flashes_active_student_and_goal(): void
    {
        $teacher = $this->createTeacher('Teacher Neo');
        $guardian = $this->createGuardian();
        $student = $this->createStudent($teacher, $guardian, 'Neo', 'Sangalang');

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->from(route('teacher.iep-goals.index'))
            ->post(route('teacher.iep-goals.store'), [
                'student_id' => $student->id,
                'domain' => 'Psychomotor',
                'plop' => 'Fine motor skills baseline',
                'goal_description' => "OBJECTIVE: Cut paper along curved lines\nACTIVITIES:\n[ ] Practice with scissors",
            ]);

        $createdGoal = IepGoal::where('student_id', $student->id)->where('domain', 'Psychomotor')->firstOrFail();

        $response->assertRedirect(route('teacher.iep-goals.index'));
        $response->assertSessionHas('success', 'New IEP Goal and Baseline PLOP successfully assigned!');
        $response->assertSessionHas('active_student_id', $student->id);
        $response->assertSessionHas('active_goal_id', $createdGoal->id);
    }
}

