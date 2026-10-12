<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Announcement;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentEnrollAndAnnouncementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacher;
    private User $guardian;

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
        ]);

        $this->guardian = User::factory()->create([
            'role' => 'guardian',
            'is_active' => true,
            'password_changed_at' => now(),
            'two_factor_secret' => 'JBSWY3DPEHPK3PXP',
        ]);
    }

    public function test_student_enrollment_fails_without_guardian(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2015-05-10',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => null,
            ]);

        $response->assertSessionHasErrors('guardian_id');
        $this->assertDatabaseMissing('students', [
            'learners_reference_number' => '123456789012',
        ]);
    }

    public function test_student_enrollment_succeeds_with_guardian(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'learners_reference_number' => '123456789012',
                'class_name' => 'Grade 1 - Joy',
                'date_of_birth' => '2015-05-10',
                'gender' => 'Male',
                'diagnosis' => 'Autism Spectrum Disorder (ASD)',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'learners_reference_number' => '123456789012',
            'guardian_id' => $this->guardian->id,
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
        ]);
    }

    public function test_student_enrollment_stores_custom_diagnosis_when_other_selected(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->post(route('admin.students.store'), [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'learners_reference_number' => '987654321098',
                'class_name' => 'Kinder Self-Control',
                'date_of_birth' => '2016-08-15',
                'gender' => 'Female',
                'diagnosis' => 'Other',
                'diagnosis_other' => 'Sensory Processing Disorder',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('students', [
            'first_name' => 'Jane',
            'learners_reference_number' => '987654321098',
            'diagnosis' => 'Sensory Processing Disorder',
        ]);
        $this->assertDatabaseMissing('students', [
            'learners_reference_number' => '987654321098',
            'diagnosis' => 'Other',
        ]);
    }

    public function test_student_update_stores_custom_diagnosis(): void
    {
        $student = Student::create([
            'first_name' => 'Sam',
            'last_name' => 'Brown',
            'learners_reference_number' => '112233445566',
            'class_name' => 'Grade 2 - Peace',
            'date_of_birth' => '2014-01-01',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $this->teacher->id,
            'guardian_id' => $this->guardian->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->put(route('admin.students.update', $student->id), [
                'first_name' => 'Sam',
                'last_name' => 'Brown',
                'learners_reference_number' => '112233445566',
                'class_name' => 'Grade 2 - Peace',
                'date_of_birth' => '2014-01-01',
                'gender' => 'Male',
                'diagnosis' => 'Other',
                'diagnosis_other' => 'Auditory Processing Disorder',
                'teacher_id' => $this->teacher->id,
                'guardian_id' => $this->guardian->id,
                'status' => 'active',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'diagnosis' => 'Auditory Processing Disorder',
        ]);
    }

    public function test_announcement_comment_author_can_edit(): void
    {
        $announcement = Announcement::create([
            'type' => 'Event',
            'title' => 'School Fair',
            'content' => 'Annual school fair next Friday.',
        ]);

        $comment = Comment::create([
            'user_id' => $this->teacher->id,
            'commentable_type' => 'App\Models\Announcement',
            'commentable_id' => $announcement->id,
            'body' => 'Initial comment text',
        ]);

        // Non-author tries to edit -> 403 Forbidden
        $unauthorizedResponse = $this->actingAs($this->guardian)
            ->withSession(['mfa_verified' => true])
            ->put(route('comments.update', $comment->id), [
                'body' => 'Hacked text',
            ]);
        $unauthorizedResponse->assertStatus(403);
        $this->assertEquals('Initial comment text', $comment->fresh()->body);

        // Author edits -> 302 Redirect + updated in DB
        $authorResponse = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->put(route('comments.update', $comment->id), [
                'body' => 'Updated comment text',
            ]);
        $authorResponse->assertRedirect();
        $this->assertEquals('Updated comment text', $comment->fresh()->body);
    }

    public function test_announcement_comment_author_and_admin_can_delete(): void
    {
        $announcement = Announcement::create([
            'type' => 'General',
            'title' => 'Parent Teacher Conference',
            'content' => 'Schedule for next month.',
        ]);

        $comment = Comment::create([
            'user_id' => $this->guardian->id,
            'commentable_type' => 'App\Models\Announcement',
            'commentable_id' => $announcement->id,
            'body' => 'Will be attending!',
        ]);

        // Another non-author user tries to delete -> 403
        $unauthorizedResponse = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->delete(route('comments.destroy', $comment->id));
        $unauthorizedResponse->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);

        // Admin can delete -> 302 and removed from DB
        $adminResponse = $this->actingAs($this->admin)
            ->withSession(['mfa_verified' => true])
            ->delete(route('comments.destroy', $comment->id));
        $adminResponse->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_teacher_announcement_seen_clears_notification_count(): void
    {
        // Initially teacher was created 2 days ago
        $this->teacher->update(['created_at' => now()->subDays(2), 'last_seen_announcements_at' => now()->subDays(2)]);

        // Admin posts an announcement
        Announcement::create([
            'type' => 'General',
            'title' => 'Important Notice',
            'content' => 'Classes suspended due to weather.',
            'created_at' => now(),
        ]);

        // Check teacher dashboard view data: newAnnouncementsCount must be 1
        $dashboardResponse = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertViewHas('newAnnouncementsCount', 1);

        // Teacher views announcements
        $announcementsResponse = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.announcements'));
        $announcementsResponse->assertOk();

        // Returning to dashboard: newAnnouncementsCount must be 0
        $dashboardAfterResponse = $this->actingAs($this->teacher)
            ->withSession(['mfa_verified' => true])
            ->get(route('teacher.dashboard'));
        $dashboardAfterResponse->assertOk();
        $dashboardAfterResponse->assertViewHas('newAnnouncementsCount', 0);
    }
}
