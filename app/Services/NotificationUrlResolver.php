<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\Report;
use App\Models\DailyLog;
use App\Models\IepGoal;
use App\Models\Comment;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class NotificationUrlResolver
{
    /**
     * Resolve the specific, contextual destination URL for a given notification.
     */
    public static function resolve(DatabaseNotification $notification, User $user): string
    {
        try {
            return self::performResolution($notification, $user);
        } catch (\Throwable $e) {
            Log::warning('NotificationUrlResolver fallback: ' . $e->getMessage());

            $data = $notification->data ?? [];
            $rawUrl = (string) ($data['url'] ?? '');
            if (!empty($rawUrl) && $rawUrl !== '#') {
                return $rawUrl;
            }

            return match($user->role ?? null) {
                'guardian' => route('guardian.dashboard'),
                'teacher'  => route('teacher.dashboard'),
                'admin'    => route('admin.dashboard'),
                default    => '/',
            };
        }
    }

    protected static function performResolution(DatabaseNotification $notification, User $user): string
    {
        $data = $notification->data ?? [];
        $rawUrl = (string) ($data['url'] ?? '');
        $message = (string) ($data['message'] ?? '');
        $title = (string) ($data['title'] ?? '');
        $studentName = $data['student_name'] ?? null;
        $role = $user->role ?? null;

        // If rawUrl is a specific URL that already contains query parameters or anchor tags (and is not '#' or bare root)
        if (!empty($rawUrl) && $rawUrl !== '#') {
            $parsed = parse_url($rawUrl);
            $query = $parsed['query'] ?? '';
            $fragment = $parsed['fragment'] ?? '';
            
            // Check if it already has specific targeting params
            if (!empty($query) || !empty($fragment)) {
                if ($role === 'guardian') {
                    parse_str($query, $params);
                    if (!empty($params['student_id'])) {
                        session(['active_child_id' => (int) $params['student_id']]);
                    }
                }
                return $rawUrl;
            }
        }

        // Try extracting student name from message or title if not explicitly provided
        if (empty($studentName)) {
            if (preg_match('/(?:regarding|for)\s+([A-Za-z0-9\-]+)/i', $message, $m)) {
                $studentName = trim($m[1], " .,!?;:'\"");
            } elseif (preg_match('/([A-Za-z0-9\-]+)\'s\s+(?:report|IEP|log)/i', $message, $m)) {
                $studentName = trim($m[1], " .,!?;:'\"");
            }
        }

        // Resolve student instance
        $student = null;
        if ($role === 'guardian') {
            $guardianStudents = Student::where('guardian_id', $user->id)->get();
            if (!empty($studentName)) {
                $student = $guardianStudents->first(function($s) use ($studentName) {
                    return strcasecmp($s->first_name, $studentName) === 0
                        || stripos($s->first_name, $studentName) !== false;
                });
            }
            if (!$student) {
                $student = $guardianStudents->first();
            }
        } elseif ($role === 'teacher') {
            if (!empty($studentName)) {
                $student = Student::where('teacher_id', $user->id)
                    ->where(function($q) use ($studentName) {
                        $q->where('first_name', 'like', "%{$studentName}%")
                          ->orWhere('last_name', 'like', "%{$studentName}%");
                    })->first();
            }
        }

        $studentId = $student?->id;
        if ($role === 'guardian' && $studentId) {
            session(['active_child_id' => $studentId]);
        }

        $combinedText = $title . ' ' . $message;

        // SCENARIO 1: Quarterly / Progress Report (e.g. "A new Q1 Report for Neo has been reviewed, approved...")
        if (preg_match('/\b(Q1|Q2|Q3|Q4|Mid-Year|Mid Year|Year-End|Year End|Daily)\b/i', $combinedText, $typeMatch)
            || preg_match('/report/i', $title)
            || (!empty($rawUrl) && str_contains($rawUrl, 'report'))) {

            $typeStr = strtolower($typeMatch[1] ?? '');
            $reportType = match($typeStr) {
                'q1' => 'q1',
                'q2' => 'q2',
                'q3' => 'q3',
                'q4' => 'q4',
                'mid-year', 'mid year' => 'mid_year',
                'year-end', 'year end' => 'year_end',
                'daily' => 'daily',
                default => null,
            };

            if ($role === 'guardian') {
                $reportQuery = Report::query();
                if ($studentId) {
                    $reportQuery->where('student_id', $studentId);
                }
                if ($reportType) {
                    $reportQuery->where('report_type', $reportType);
                }
                $reportQuery->where('status', 'approved');
                $report = $reportQuery->latest()->first();

                $params = [];
                if ($studentId) {
                    $params['student_id'] = $studentId;
                }
                if ($reportType) {
                    $params['type'] = $reportType;
                }
                if ($report) {
                    $params['report_id'] = $report->id;
                }

                return route('guardian.reports', $params);
            } elseif ($role === 'teacher') {
                return route('teacher.reports.index', array_filter(['student_id' => $studentId]));
            } elseif ($role === 'admin') {
                return route('admin.report-approval.index');
            }
        }

        // SCENARIO 2: Comments / Discussion Replies (e.g. "Teacher Teacher 1 posted a reply regarding Neo.")
        if (preg_match('/reply|comment|replied/i', $combinedText)) {
            if ($role === 'guardian') {
                if (preg_match('/Daily\s*Log/i', $combinedText)) {
                    $log = $studentId ? DailyLog::where('student_id', $studentId)->latest()->first() : null;
                    $url = route('guardian.dashboard', array_filter(['student_id' => $studentId, 'open_log' => $log?->id]));
                    return $log ? $url . '#log-' . $log->id : $url;
                } elseif (preg_match('/IEP\s*Goal/i', $combinedText)) {
                    $goal = $studentId ? IepGoal::where('student_id', $studentId)->latest()->first() : null;
                    $url = route('guardian.goals', array_filter(['student_id' => $studentId, 'goal_id' => $goal?->id]));
                    return $goal ? $url . '#goal-' . $goal->id : $url;
                } else {
                    // Search latest comment associated with this student's records
                    if ($studentId) {
                        $latestLogComment = Comment::where('commentable_type', DailyLog::class)
                            ->whereIn('commentable_id', DailyLog::where('student_id', $studentId)->pluck('id'))
                            ->latest()
                            ->first();

                        $latestGoalComment = Comment::where('commentable_type', IepGoal::class)
                            ->whereIn('commentable_id', IepGoal::where('student_id', $studentId)->pluck('id'))
                            ->latest()
                            ->first();

                        $latestReportComment = Comment::where('commentable_type', Report::class)
                            ->whereIn('commentable_id', Report::where('student_id', $studentId)->pluck('id'))
                            ->latest()
                            ->first();

                        $candidates = collect([$latestLogComment, $latestGoalComment, $latestReportComment])->filter()->sortByDesc('created_at');
                        $top = $candidates->first();

                        if ($top) {
                            if ($top->commentable_type === DailyLog::class) {
                                return route('guardian.dashboard', ['student_id' => $studentId, 'open_log' => $top->commentable_id]) . '#log-' . $top->commentable_id;
                            } elseif ($top->commentable_type === IepGoal::class) {
                                return route('guardian.goals', ['student_id' => $studentId, 'goal_id' => $top->commentable_id]) . '#goal-' . $top->commentable_id;
                            } elseif ($top->commentable_type === Report::class) {
                                return route('guardian.reports', ['student_id' => $studentId, 'report_id' => $top->commentable_id]);
                            }
                        }
                    }

                    return route('guardian.dashboard', array_filter(['student_id' => $studentId]));
                }
            } elseif ($role === 'teacher') {
                return route('teacher.dashboard');
            }
        }

        // SCENARIO 3: IEP Goals (New goal or milestone update)
        if (preg_match('/IEP/i', $combinedText)) {
            if ($role === 'guardian') {
                $goal = $studentId ? IepGoal::where('student_id', $studentId)->latest()->first() : null;
                $url = route('guardian.goals', array_filter(['student_id' => $studentId, 'goal_id' => $goal?->id]));
                return $goal ? $url . '#goal-' . $goal->id : $url;
            } elseif ($role === 'teacher') {
                return route('teacher.iep-goals.index');
            }
        }

        // SCENARIO 4: Announcements
        if (preg_match('/Announcement/i', $combinedText)) {
            if ($role === 'guardian') {
                return route('guardian.announcements.index');
            } elseif ($role === 'teacher') {
                return route('teacher.announcements');
            } elseif ($role === 'admin') {
                return route('admin.announcements.index');
            }
        }

        // SCENARIO 5: Existing raw URL fallback if not '#'
        if (!empty($rawUrl) && $rawUrl !== '#') {
            return $rawUrl;
        }

        // DEFAULT ROLE-BASED FALLBACK
        return match($role) {
            'guardian' => route('guardian.dashboard', array_filter(['student_id' => $studentId])),
            'teacher' => route('teacher.dashboard'),
            'admin' => route('admin.dashboard'),
            default => '/',
        };
    }
}
