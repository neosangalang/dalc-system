<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianReportCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardian_can_post_comment_on_q1_report(): void
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
            'first_name' => 'John',
            'last_name' => 'Doe',
            'learners_reference_number' => '123456789012',
            'class_name' => 'Grade 1 - Joy',
            'date_of_birth' => '2015-05-10',
            'gender' => 'Male',
            'diagnosis' => 'Autism Spectrum Disorder (ASD)',
            'teacher_id' => $teacher->id,
            'guardian_id' => $guardian->id,
        ]);

        $report = Report::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'report_type' => 'q1',
            'content' => 'Q1 Progress Report content here.',
            'status' => 'approved',
            'approved_by' => $teacher->id,
            'approved_at' => now(),
        ]);

        $ajaxResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->post(route('comments.store'), [
                'commentable_type' => Report::class,
                'commentable_id' => $report->id,
                'body' => '<p>This is an ajax comment on Q1 report.</p>',
            ], ['X-Requested-With' => 'XMLHttpRequest']);

        $ajaxResponse->assertStatus(302);

        $viewResponse = $this->actingAs($guardian)
            ->withSession(['mfa_verified' => true, 'active_child_id' => $student->id])
            ->get(route('guardian.reports', ['type' => 'q1']));

        $viewResponse->assertOk();
        $viewResponse->assertSee('chat-history-' . $report->id);
        $viewResponse->assertSee('This is an ajax comment on Q1 report.');

        $content = $viewResponse->getContent();
        // Verify that </tbody> appears before the reading modal
        $tbodyEndPos = strpos($content, '</tbody>');
        $modalPos = strpos($content, 'x-show="activeViewModal === ' . $report->id . '"');
        $this->assertNotFalse($tbodyEndPos);
        $this->assertNotFalse($modalPos);
        $this->assertGreaterThan($tbodyEndPos, $modalPos, 'Reading modal must be placed outside the table tbody to prevent foster-parenting form corruption.');

        // Verify form attributes
        $viewResponse->assertSee('id="comment-form-' . $report->id . '"', false);
        $viewResponse->assertSee('action="' . route('comments.store') . '"', false);
        $viewResponse->assertSee('name="commentable_type" value="' . Report::class . '"', false);
        $viewResponse->assertSee('name="commentable_id" value="' . $report->id . '"', false);
    }
}
