<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Student;
use App\Models\IepGoal;
use App\Notifications\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IepGoalController extends Controller
{
    public function index() {
        $teacherId = Auth::id();

        // Get only the students assigned to this specific teacher
        $myStudents = Student::where('teacher_id', $teacherId)->where('status', 'active')->get();
        
        // FIX: Added with(['comments.user']) to fetch the guardian's messages
        $goals = IepGoal::with(['comments.user'])
            ->whereHas('student', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->latest()->get();

        return view('teacher.iep.index', compact('myStudents', 'goals'));
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

        IepGoal::create([
            'student_id'       => $student->id,
            'domain'           => $request->domain,
            'plop'             => $request->plop, 
            'goal_description' => $request->goal_description,
            'progress_percentage' => 0,
            'status'           => 'in_progress',
        ]);

        // Notify the Guardian
        try {
            $student->load('guardian');
            if ($student->guardian) {
                $student->guardian->notify(new \App\Notifications\SystemAlert(
                    "Teacher " . Auth::user()->name . " added a new IEP Goal for {$student->first_name}: {$request->domain}.",
                    route('guardian.goals')
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

        // Notify the Guardian
        try {
            $goal->student->load('guardian');
            if ($goal->student && $goal->student->guardian) {
                $goal->student->guardian->notify(new \App\Notifications\SystemAlert(
                    "IEP Progress updated for {$goal->student->first_name}: {$goal->domain} ({$request->progress_percentage}%).",
                    route('guardian.goals')
                ));
            }
        } catch (\Exception $e) {
            Log::error('Guardian notification failed on IEP update: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'IEP Progress updated successfully!');
    }

    // DELETE A GOAL
    public function destroy($id)
    {
        $goal = \App\Models\IepGoal::findOrFail($id);
        $goal->delete();

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