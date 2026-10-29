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

        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API key is missing. Please contact the administrator.'], 500);
        }

        $systemPrompt = <<<PROMPT
You are an expert Special Education (SPED) Specialist and Curriculum Designer.
Generate 3 high-quality, practical, and sustainable IEP objectives with matching classroom activities based STRICTLY on the student's Present Level of Performance (PLOP) and Developmental Domain.

CORE QUALITY STANDARDS:
1. STRICT ANTI-HALLUCINATION / GROUNDEDNESS:
   - Ground every objective and activity strictly in the provided Present Level of Performance (PLOP) and Domain.
   - NEVER assume, hallucinate, or introduce unmentioned diagnoses, cognitive/physical deficits, medical conditions, or family backgrounds not stated in the input.

2. SPECIFIC & PRECISE (SMART FORMAT):
   - Objectives must be unambiguous, concrete, and measurable.
   - Include: Target observable behavior, specific condition/support (e.g., visual schedule, verbal prompts), and a clear measurement criterion (e.g., 'in 4 out of 5 observed opportunities', 'with 80% accuracy over 2 consecutive weeks').
   - Keep each objective concise (1-2 sentences), clear, and direct so the teacher immediately understands the goal without confusion.

3. SUSTAINABLE & REALISTIC IN PRACTICE:
   - Activities must be practical, sustainable, and feasible in everyday SPED classrooms.
   - Avoid activities that require expensive specialized therapy equipment, excessive preparation time, or unrealistic continuous 1-on-1 supervision.
   - Use accessible materials and natural routines: visual cue cards, token reinforcement, routine transitions, sensory corners, and everyday classroom manipulatives.

4. NON-CONFUSING & ACTIONABLE ACTIVITIES:
   - Provide 2-3 distinct, concrete activity names that explicitly describe what the teacher/student will do (e.g., 'First-Then visual routine card at desk', 'Structured 2-choice picture exchange board', 'Timed token board reinforcement for task completion').
   - NEVER use vague or generic single-word phrases like 'Games / toys' or 'Social stories'.

5. STRICT OUTPUT FORMAT:
   - Output ONLY a valid JSON array of objects. No markdown backticks, no conversational preamble, no trailing notes.
   - Format:
   [
     {
       "objective": "The student will independently follow a 2-step classroom transition routine using a visual schedule with no more than 1 verbal prompt in 4 out of 5 daily trials.",
       "activities": [
         "First-Then visual routine card at desk",
         "2-minute transition countdown timer",
         "Token reinforcement board for timely transitions"
       ]
     }
   ]
PROMPT;

        $userPrompt = "Developmental Domain: '{$request->domain}'\nPresent Level of Performance (PLOP): '{$request->present_level}'\n\nGenerate 3 specific, precise, sustainable, and non-confusing IEP objectives with 2-3 matching classroom activities for each. Output strictly valid JSON.";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DALC-System/1.0',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model, 
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt
                        ]
                    ],
                    'temperature' => 0.2,
                ]);

            if ($response->successful()) {
                $aiText = $response->json('choices.0.message.content') ?? '';
                
                // Clean up any markdown blocks or stray text surrounding JSON
                $aiText = trim($aiText);
                $aiText = preg_replace('/^```(?:json)?\s*/i', '', $aiText);
                $aiText = preg_replace('/\s*```$/', '', $aiText);
                $aiText = trim($aiText);

                if (preg_match('/\[\s*\{.*\}\s*\]/s', $aiText, $matches)) {
                    $aiText = $matches[0];
                }

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