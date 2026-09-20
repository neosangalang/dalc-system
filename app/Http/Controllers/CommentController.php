<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Comment;
use App\Models\DailyLog;
use App\Models\IepGoal;
use App\Models\Report;
use App\Notifications\SystemAlert;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body'             => 'required',
            'commentable_id'   => 'required|integer',
            'commentable_type' => 'required|string',
        ]);

        // 1. Allow IEP Goals, Reports, Daily Logs, and Announcements to accept comments
        $allowedModels = [
            'App\Models\IepGoal', 
            'App\Models\Report', 
            'App\Models\DailyLog',
            'App\Models\Announcement'
        ];

        if (!in_array($request->commentable_type, $allowedModels)) {
            return back()->with('error', 'Invalid comment type.');
        }

        /** @var User $user */
        $user = Auth::user();

        // 2. Save the comment to the database
        Comment::create([
            'user_id'          => $user->id,
            'commentable_id'   => $request->commentable_id,
            'commentable_type' => $request->commentable_type,
            'body'             => $request->body,
            'is_teacher_read'  => $user->role === 'teacher' ? 1 : 0,
            'is_guardian_read' => $user->role === 'guardian' ? 1 : 0,
        ]);

        // 3. Notify the respective party
        try {
            $target = null;
            if ($request->commentable_type === 'App\Models\DailyLog') {
                $target = DailyLog::with(['student.guardian', 'teacher'])->find($request->commentable_id);
            } elseif ($request->commentable_type === 'App\Models\IepGoal') {
                $target = IepGoal::with(['student.guardian', 'student.teacher'])->find($request->commentable_id);
            } elseif ($request->commentable_type === 'App\Models\Report') {
                $target = Report::with(['student.guardian', 'teacher'])->find($request->commentable_id);
            }

            if ($target) {
                if ($user->role === 'guardian') {
                    // Guardian commented -> Notify the teacher
                    $teacher = $target->teacher ?? ($target->student->teacher ?? null);
                    if ($teacher && $teacher->id !== $user->id) {
                        $url = match($request->commentable_type) {
                            'App\Models\DailyLog' => route('teacher.daily-logs.index'),
                            'App\Models\IepGoal'  => route('teacher.iep-goals.index'),
                            'App\Models\Report'   => route('teacher.reports.index'),
                            default               => route('teacher.dashboard'),
                        };
                        $childName = $target->student->first_name ?? 'your student';
                        $teacher->notify(new SystemAlert(
                            "{$user->name} (Guardian) left a reply regarding {$childName}.",
                            $url
                        ));
                    }
                } else {
                    // Teacher/Admin commented -> Notify the guardian
                    $guardian = $target->student->guardian ?? null;
                    if ($guardian && $guardian->id !== $user->id) {
                        $url = match($request->commentable_type) {
                            'App\Models\DailyLog' => route('guardian.dashboard'),
                            'App\Models\IepGoal'  => route('guardian.goals'),
                            'App\Models\Report'   => route('guardian.reports'),
                            default               => route('guardian.dashboard'),
                        };
                        $childName = $target->student->first_name ?? 'your child';
                        $guardian->notify(new SystemAlert(
                            "Teacher {$user->name} posted a reply regarding {$childName}.",
                            $url
                        ));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification error in CommentController: ' . $e->getMessage());
        }

        return back()->with('success', 'Comment posted!');
    }
}