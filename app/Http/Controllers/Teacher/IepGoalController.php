<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Student;
use App\Models\IepGoal;
use App\Notifications\SystemAlert;
use App\Notifications\GuardianAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IepGoalController extends Controller
{
    public function index(Request $request) {
        $teacherId = Auth::id();

        // Get all active students assigned to this specific teacher
        $allStudents = Student::where('teacher_id', $teacherId)->where('status', 'active')->orderBy('first_name')->get();
        
        $selectedStudentId = $request->query('student_id');
        if ($selectedStudentId && $allStudents->contains('id', (int) $selectedStudentId)) {
            // User picked a specific student: view ONLY that student
            $myStudents = $allStudents->where('id', (int) $selectedStudentId);
        } else {
            $selectedStudentId = null;
            $myStudents = $allStudents;
        }

        // Fetch goals, scoped if student is selected
        $goalsQuery = IepGoal::with(['comments.user'])
            ->whereHas('student', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            });

        if ($selectedStudentId) {
            $goalsQuery->where('student_id', (int) $selectedStudentId);
        }

        $goals = $goalsQuery->latest()->get();

        $allAssignedDomains = IepGoal::whereHas('student', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->get(['student_id', 'domain'])
          ->groupBy('student_id')
          ->map(fn($g) => $g->pluck('domain')->values());

        return view('teacher.iep.index', compact('myStudents', 'allStudents', 'goals', 'selectedStudentId', 'allAssignedDomains'));
    }

    public function store(Request $request) {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'domain'           => 'required|string|max:255',
            'plop'             => 'required|string', 
            'goal_description' => 'required|string',
        ]);

        // Security check: Ensure the student actually belongs to this teacher
        $student = Student::where('id', $request->student_id)
                          ->where('teacher_id', Auth::id())
                          ->firstOrFail();

        // Enforce domain uniqueness per student
        $existingGoal = IepGoal::where('student_id', $student->id)
                               ->where('domain', $request->domain)
                               ->first();

        if ($existingGoal) {
            return redirect()->back()
                ->withErrors(['domain' => "An IEP Goal for the '{$request->domain}' domain already exists for {$student->first_name}."])
                ->withInput();
        }

        IepGoal::create([
            'student_id'       => $student->id,
            'domain'           => $request->domain,
            'plop'             => $request->plop, 
            'goal_description' => $request->goal_description,
            'progress_percentage' => 0,
            'status'           => 'in_progress',
        ]);

        // Notify the Guardian via in-app bell & email
        try {
            $student->load('guardian');
            if ($student->guardian) {
                $teacherName = Auth::user()->name;
                $student->guardian->notify(new GuardianAlertNotification(
                    title: 'New IEP Goal Assigned',
                    message: "Teacher {$teacherName} added a new IEP Goal for {$student->first_name} under {$request->domain}.",
                    url: route('guardian.goals'),
                    studentName: $student->first_name,
                    actionText: 'View IEP Goals',
                    extraDetails: "Domain: {$request->domain} | Target: " . Str::limit(strip_tags($request->goal_description), 140)
                ));
            }
        } catch (\Exception $e) {
            Log::error('Guardian notification failed on IEP create: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'New IEP Goal and Baseline PLOP successfully assigned!');
    }

    public function generateAi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
            'present_level' => 'required|string|max:2000'
        ]);

        // Fetching the API key from config or env
        $apiKey = config('services.groq.api_key', env('GROQ_API_KEY'));

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API key is missing. Please contact the administrator.'], 500);
        }

        try {
            // Using the exact model ID with the openai/ prefix
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-20b', 
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are an expert Special Education Teacher. Output STRICTLY a JSON array of objects. No markdown formatting, no conversational text."
                        ],
                        [
                            'role' => 'user',
                            'content' => "Domain: '{$request->domain}'\nPresent Level of Performance: '{$request->present_level}'\n\nGenerate 3 specific, measurable IEP objectives and 2-3 matching activities for each.\nFormat exactly like this:\n[\n  {\n    \"objective\": \"To follow classroom routines dependently...\",\n    \"activities\": [\"Games / toys\", \"Social stories\", \"Token system\"]\n  }\n]"
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $aiText = $response->json('choices.0.message.content');
                
                // Clean up any markdown blocks the AI might accidentally include
                $aiText = str_replace(['```json', '```'], '', $aiText);
                $aiText = trim($aiText);

                $goalsArray = json_decode($aiText, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($goalsArray)) {
                    return response()->json(['success' => true, 'goals' => $goalsArray]);
                }
                
                return response()->json(['success' => false, 'message' => 'Failed to parse AI response. Raw output: ' . $aiText], 500);
            }
            return response()->json(['success' => false, 'message' => 'AI Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // UPDATE AN EXISTING GOAL
    public function update(Request $request, $id)
    {
        $goal = IepGoal::findOrFail($id);

        $request->validate([
            'goal_description' => 'required|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'nullable|string',
            'plop' => 'nullable|string',
        ]);

        // Automated progress status tracking: Complete ('achieved') ONLY when progress reaches exactly 100%
        $calculatedStatus = ((int)$request->progress_percentage === 100) ? 'achieved' : 'in_progress';

        $updateData = [
            'goal_description' => $request->goal_description,
            'progress_percentage' => $request->progress_percentage,
            'status' => $calculatedStatus,
        ];

        if ($request->has('plop')) {
            $updateData['plop'] = $request->plop;
        }

        $goal->update($updateData);

        // Notify the Guardian via in-app bell & email
        try {
            $goal->student->load('guardian');
            if ($goal->student && $goal->student->guardian) {
                $studentName = $goal->student->first_name;
                $goal->student->guardian->notify(new GuardianAlertNotification(
                    title: 'IEP Milestone Progress Update',
                    message: "IEP progress updated for {$studentName}: {$goal->domain} is now at {$request->progress_percentage}%.",
                    url: route('guardian.goals'),
                    studentName: $studentName,
                    actionText: 'View IEP Progress',
                    extraDetails: "Milestone status: {$goal->domain} — {$request->progress_percentage}% achieved."
                ));
            }
        } catch (\Exception $e) {
            Log::error('Guardian notification failed on IEP update: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'IEP Progress updated successfully!');
    }

    // DELETE A GOAL
    public function destroy(Request $request, $id)
    {
        $goal = \App\Models\IepGoal::findOrFail($id);
        $domain = $goal->domain;
        $studentId = $goal->student_id;
        $goal->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'IEP Goal deleted successfully!',
                'deleted_domain' => $domain,
                'student_id' => $studentId,
            ]);
        }

        return redirect()->back()->with('success', 'IEP Goal deleted successfully!');
    }

    // ==========================================
    // THE NEW IEP PDF EXPORT METHOD
    // ==========================================
    public function downloadIepPdf($studentId)
    {
        $student = Student::with(['iepGoals', 'guardian', 'teacher'])->findOrFail($studentId);

        // Load the specialized IEP view
        $pdf = Pdf::loadView('teacher.iep.pdf', compact('student'));

        // Format the file name
        $fileName = 'IEP_' . $student->last_name . '_' . $student->first_name . '_SY2025-2026.pdf';

        return $pdf->stream($fileName); 
    }

    // ==========================================
    // MARK COMMENTS AS READ
    // ==========================================
    public function markCommentsAsRead($id)
    {
        $goal = IepGoal::findOrFail($id);
        
        // Use 1 instead of true to ensure MySQL accepts the update
        $goal->comments()
             ->where('user_id', '!=', Auth::id())
             ->update(['is_teacher_read' => 1]);

        return response()->json(['success' => true]);
    }
}