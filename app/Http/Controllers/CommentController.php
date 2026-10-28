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
use App\Notifications\GuardianAlertNotification;

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

        // 2b. Mirror comments between DailyLog and Daily Report so both views stay connected
        try {
            if ($request->commentable_type === 'App\Models\DailyLog') {
                $log = DailyLog::find($request->commentable_id);
                if ($log) {
                    $logDate = \Carbon\Carbon::parse($log->log_date)->format('Y-m-d');
                    $matchingReport = Report::where('student_id', $log->student_id)
                        ->where('report_type', 'daily')
                        ->where(function($q) use ($logDate) {
                            $q->whereDate('report_date', $logDate)
                              ->orWhere(function($sub) use ($logDate) {
                                  $sub->whereNull('report_date')->whereDate('created_at', $logDate);
                              });
                        })
                        ->first();
                    if ($matchingReport) {
                        Comment::create([
                            'user_id'          => $user->id,
                            'commentable_id'   => $matchingReport->id,
                            'commentable_type' => 'App\Models\Report',
                            'body'             => $request->body,
                            'is_teacher_read'  => $user->role === 'teacher' ? 1 : 0,
                            'is_guardian_read' => $user->role === 'guardian' ? 1 : 0,
                        ]);
                    }
                }
            } elseif ($request->commentable_type === 'App\Models\Report') {
                $rep = Report::find($request->commentable_id);
                if ($rep && $rep->report_type === 'daily') {
                    $repDate = $rep->report_date ? \Carbon\Carbon::parse($rep->report_date)->format('Y-m-d') : $rep->created_at->format('Y-m-d');
                    $matchingLog = DailyLog::where('student_id', $rep->student_id)
                        ->whereDate('log_date', $repDate)
                        ->first();
                    if ($matchingLog) {
                        Comment::create([
                            'user_id'          => $user->id,
                            'commentable_id'   => $matchingLog->id,
                            'commentable_type' => 'App\Models\DailyLog',
                            'body'             => $request->body,
                            'is_teacher_read'  => $user->role === 'teacher' ? 1 : 0,
                            'is_guardian_read' => $user->role === 'guardian' ? 1 : 0,
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Mirroring comment between DailyLog and Report failed: ' . $e->getMessage());
        }

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
                        $targetTypeLabel = match($request->commentable_type) {
                            'App\Models\DailyLog' => 'Daily Log',
                            'App\Models\IepGoal'  => 'IEP Goal',
                            'App\Models\Report'   => 'Progress Report',
                            default               => 'Update',
                        };

                        $guardian->notify(new GuardianAlertNotification(
                            title: "New Reply on {$targetTypeLabel}",
                            message: "Teacher {$user->name} posted a reply regarding {$childName}.",
                            url: $url,
                            studentName: $childName,
                            actionText: 'View Reply',
                            extraDetails: 'A new message was added to the discussion thread on your parent portal.'
                        ));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification error in CommentController: ' . $e->getMessage());
        }

        return back()->with('success', 'Comment posted!');
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        if ($comment->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to edit this comment.');
        }

        $comment->update([
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comment updated successfully!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'You are not authorized to delete this comment.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}