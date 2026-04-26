<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Student;
use App\Models\IepGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IepGoalController extends Controller
{
    public function index() {
        $teacherId = Auth::id();

        // Get only the students assigned to this specific teacher
        $myStudents = Student::where('teacher_id', $teacherId)->where('status', 'active')->get();
        
        // Get all IEP goals for these students
        $goals = IepGoal::whereHas('student', function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->latest()->get();

        return view('teacher.iep.index', compact('myStudents', 'goals'));
    }

    public function store(Request $request) {
        // 1. ADDED 'plop' to the validation rules
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

        // 2. ADDED 'plop' to the database creation array
        IepGoal::create([
            'student_id'       => $student->id,
            'domain'           => $request->domain,
            'plop'             => $request->plop, 
            'goal_description' => $request->goal_description,
            'progress_percentage' => 0,
            'status'           => 'in_progress',
        ]);

        return redirect()->back()->with('success', 'New IEP Goal and Baseline PLOP successfully assigned!');
    }

    // RENAMED to match your route and javascript!
    public function generateAi(\Illuminate\Http\Request $request)
    {
        // 1. We now accept the Domain AND the Present Level of Performance
        $request->validate([
            'domain' => 'required|string',
            'present_level' => 'required|string|max:2000'
        ]);

        $apiKey = env('GEMINI_API_KEY');

        // 2. The upgraded prompt forces the AI to output Objectives and Activities
        $prompt = "You are an expert Special Education Teacher. 
        Domain: '{$request->domain}'
        Present Level of Performance: '{$request->present_level}'
        
        Generate 3 specific, measurable IEP objectives and 2-3 matching activities for each.
        Output STRICTLY a JSON array of objects. No markdown formatting, no conversational text.
        Format exactly like this:
        [
            {
                \"objective\": \"To follow classroom routines dependently...\",
                \"activities\": [\"Games / toys\", \"Social stories\", \"Token system\"]
            }
        ]";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                // FIXED the model name below to gemini-2.0-flash
                ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

            if ($response->successful()) {
                $aiText = $response->json('candidates.0.content.parts.0.text');
                
                // 3. Clean up any markdown blocks Gemini might accidentally include
                $aiText = str_replace(['```json', '```'], '', $aiText);
                $aiText = trim($aiText);

                $goalsArray = json_decode($aiText, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($goalsArray)) {
                    return response()->json(['success' => true, 'goals' => $goalsArray]);
                }
                
                return response()->json(['success' => false, 'message' => 'Failed to parse AI response. Raw output: ' . $aiText], 500);
            }
            return response()->json(['success' => false, 'message' => 'Google AI Error: ' . $response->body()], 500);

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
            'status' => 'required|string',
        ]);

        $goal->update([
            'goal_description' => $request->goal_description,
            'progress_percentage' => $request->progress_percentage,
            'status' => $request->status,
        ]);

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

        return $pdf->stream($fileName); // Use ->stream() to preview in browser, or ->download() to force file download
    }
}