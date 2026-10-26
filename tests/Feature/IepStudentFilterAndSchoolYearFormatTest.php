<?php

namespace Tests\Feature;

use App\Models\AcademicQuarter;
use App\Models\IepGoal;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IepStudentFilterAndSchoolYearFormatTest extends TestCase
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

    public function test_iep_index_shows_all_students_when_no_student_id_query_is_provided(): void
    {
        $teacher = $this->createTeacher('Teacher Jane');
        $guardian = $this->createGuardian();

        $student1 = $this->createStudent($teacher, $guardian, 'Alice', 'Wonderland');
        $student2 = $this->createStudent($teacher, $guardian, 'Bob', 'Builder');

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.iep-goals.index'));

        $response->assertStatus(200);
        $response->assertSee('Alice');
        $response->assertSee('Bob');
        $response->assertViewHas('myStudents', function ($students) {
            return $students->count() === 2;
        });
    }

    public function test_iep_index_scopes_to_only_picked_student_when_student_id_is_provided(): void
    {
        $teacher = $this->createTeacher('Teacher Jane');
        $guardian = $this->createGuardian();

        $student1 = $this->createStudent($teacher, $guardian, 'Alice', 'Wonderland');
        $student2 = $this->createStudent($teacher, $guardian, 'Bob', 'Builder');

        IepGoal::create([
            'student_id' => $student1->id,
            'domain' => 'Psychomotor',
            'plop' => 'Baseline 1',
            'goal_description' => 'OBJECTIVE: Run',
            'progress_percentage' => 20,
            'status' => 'in_progress',
        ]);

        IepGoal::create([
            'student_id' => $student2->id,
            'domain' => 'Self-Help',
            'plop' => 'Baseline 2',
            'goal_description' => 'OBJECTIVE: Eat',
            'progress_percentage' => 30,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.iep-goals.index', ['student_id' => $student1->id]));

        $response->assertStatus(200);
        $response->assertSee('Single Student Filter Active');
        $response->assertSee('Viewing IEP only for');
        $response->assertSee('Alice Wonderland');
        // Bob should not be in the accordion list
        $response->assertDontSee('Bob Builder</h6', false);
        $response->assertViewHas('myStudents', function ($students) use ($student1) {
            return $students->count() === 1 && $students->first()->id === $student1->id;
        });
        $response->assertViewHas('allStudents', function ($all) {
            return $all->count() === 2;
        });
        $response->assertViewHas('selectedStudentId', $student1->id);
    }

    public function test_academic_quarters_view_modal_does_not_contain_custom_tab(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $schoolYear = SchoolYear::create([
            'name' => 'SY 2026-2027',
            'is_active' => true,
        ]);

        AcademicQuarter::create(['name' => 'Q1', 'school_year_id' => $schoolYear->id]);
        AcademicQuarter::create(['name' => 'Q2', 'school_year_id' => $schoolYear->id]);
        AcademicQuarter::create(['name' => 'Q3', 'school_year_id' => $schoolYear->id]);
        AcademicQuarter::create(['name' => 'Q4', 'school_year_id' => $schoolYear->id]);

        $response = $this->actingAs($admin)
            ->withSession(['mfa_verified' => true])
            ->get(route('admin.quarters.index'));

        $response->assertStatus(200);
        $response->assertDontSee('modalMode === \'custom\'', false);
        $response->assertDontSee('id="custom_school_year_name"', false);
    }
}
