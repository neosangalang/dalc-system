<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeacherStudentsViewTest extends TestCase
{
    use RefreshDatabase;

    private User $teacher;
    private User $guardian;
    private Student $assignedStudent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Teacher Jane Doe',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'name' => 'Guardian John Smith',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);

        $this->assignedStudent = Student::create([
            'first_name' => 'Alice',
            'last_name' => 'Wonderland',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Playgroup Morning (Love A)',
            'date_of_birth' => '2018-04-12',
            'gender' => 'Female',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        StudentDocument::create([
            'student_id' => $this->assignedStudent->id,
            'file_path' => 'student_documents/test.pdf',
            'original_name' => 'medical_report.pdf',
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
        ]);
    }

    public function test_teacher_students_index_renders_redesigned_ui_successfully(): void
    {
        $response = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.students.index'));

        $response->assertOk();

        // Header elements
        $response->assertSee('My Assigned Students');
        $response->assertSee('Back to Dashboard');

        // Filter Bar elements
        $response->assertSee('Search student name, ID, LRN...');
        $response->assertSee('Filter by Class');
        $response->assertSee('Filter by Diagnosis');
        $response->assertSee('Status');
        $response->assertSee('id="filterSearchInput"', false);
        $response->assertSee('id="filterClass"', false);
        $response->assertSee('id="filterDiagnosis"', false);
        $response->assertSee('id="filterStatus"', false);

        // Student Data elements
        $response->assertSee('Alice Wonderland');
        $response->assertSee('123456789012');
        $response->assertSee('Playgroup Morning (Love A)');
        $response->assertSee('Autism Spectrum Disorder (ASD)');
        $response->assertSee('Teacher Jane Doe');
        $response->assertSee('Guardian John Smith');
        $response->assertSee('Documents (1)');

        // Action links
        $response->assertSee(route('teacher.students.show', $this->assignedStudent->id));
        $response->assertSee(route('teacher.iep-goals.index', ['student_id' => $this->assignedStudent->id]));
        $response->assertSee(route('teacher.iep-goals.pdf', $this->assignedStudent->id));
        $response->assertSee(route('teacher.daily-logs.index', ['student_id' => $this->assignedStudent->id]));
    }
}
