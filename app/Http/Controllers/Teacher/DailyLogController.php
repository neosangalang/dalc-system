<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\DailyLog;
use App\Models\Report; // Added Report Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DailyLogController extends Controller
{
    public function index() {
        $teacherId = Auth::id();
        
        // Fetch students for the dropdown
        $myStudents = Student::where('teacher_id', $teacherId)->where('status', 'active')->get();
        
        // Fetch recent logs to display on the right side
        $recentLogs = DailyLog::with('student')
            ->where('teacher_id', $teacherId)
            ->latest('log_date')
            ->take(10)
            ->get();

        return view('teacher.daily-logs.index', compact('myStudents', 'recentLogs'));
    }

   public function store(Request $request) {
    
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'log_date'   => 'required|date',
            'quarter'    => 'required|string',
            'raw_notes'  => 'required|string',
            'notes'      => 'nullable|string',
            'home_recommendations' => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120', 
        ]);

        // Security check
        $student = Student::where('id', $request->student_id)->where('teacher_id', Auth::id())->firstOrFail();

        $finalNotes = $request->filled('notes') ? $request->notes : $request->raw_notes;

       // ==========================================
        // SECURE VAULT STORAGE
        // ==========================================
        $imagePath = null;
        if ($request->hasFile('photo')) {
            // Notice we added 'local' here. This forces it to bypass the internet!
            $imagePath = $request->file('photo')->store('private_log_photos', 'local');
        }

        DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => Auth::id(),
            'log_date'   => $request->log_date,
            'quarter'    => $request->quarter,
            'notes'      => $finalNotes, 
            'home_recommendations' => $request->home_recommendations,
            'image_path' => $imagePath, // Save path to database
        ]);

        return redirect()->route('teacher.daily-logs.index')->with('success', 'Daily log saved successfully!');
    }

    public function generateAiReport(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'raw_notes' => 'required|string|max:1000',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'AI API key is missing. Please contact the administrator.'], 500);
        }

        $prompt = "You are an expert Special Education (SPED) teacher. Convert the following informal daily notes into formal, objective, professional IEP documentation language. Keep it concise, maintain the original meaning, and do not add fabricated details.
        
        CRITICAL INSTRUCTIONS:
        - Output ONLY the final rewritten sentence.
        - Do NOT include any titles, prefixes, or introductory text (e.g., do not write 'Formal Documentation:').
        - Do NOT use markdown formatting like asterisks (**).
        
        Informal Notes: " . $request->raw_notes;

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $formalText = $response->json('candidates.0.content.parts.0.text');
                
                $formalText = preg_replace('/^\*\*.*?\*\*:?\s*/', '', trim($formalText));
                $formalText = preg_replace('/^(Formal Documentation|Documentation):?\s*/i', '', $formalText);
                
                return response()->json(['formal_note' => trim($formalText)]);
            }

            return response()->json(['error' => 'Google Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function generateAiRecommendations(\Illuminate\Http\Request $request)
    {
        // Require student_id so we know whose history to look up!
        $request->validate([
            'notes' => 'required|string',
            'student_id' => 'required|exists:students,id'
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'AI API key is missing. Please check your .env file.']);
        }

        $studentId = $request->student_id;

        // 1. Fetch "Quick Notes" (Last 3 days of daily logs)
        $recentLogs = DailyLog::where('student_id', $studentId)
            ->latest('log_date')
            ->take(3)
            ->get(['log_date', 'notes']);

        // 2. Fetch "Formal Notes" (Latest approved quarterly report)
        $formalReport = Report::where('student_id', $studentId)
            ->where('status', 'approved')
            ->latest()
            ->first(['report_type', 'content']);

        // 3. Build the highly contextual Prompt
        $prompt = "You are a Special Education Teacher. Generate a short, friendly paragraph containing 2 specific, easy home-based activities the parents can do tonight. Keep it under 4 sentences. Tone must be warm and encouraging. Do not use markdown formatting like asterisks (**).\n\n";
        
        $prompt .= "--- HISTORICAL CONTEXT ---\n";
        
        if ($formalReport) {
            $prompt .= "LATEST FORMAL REPORT (" . strtoupper($formalReport->report_type) . "):\n" . $formalReport->content . "\n\n";
        } else {
            $prompt .= "LATEST FORMAL REPORT: No formal report available yet.\n\n";
        }

        if ($recentLogs->isNotEmpty()) {
            $prompt .= "RECENT QUICK NOTES (Past few days):\n";
            foreach ($recentLogs as $log) {
                // Ensure we don't output empty notes
                if (!empty($log->notes)) {
                    $prompt .= "- " . \Carbon\Carbon::parse($log->log_date)->format('M d') . ": " . $log->notes . "\n";
                }
            }
            $prompt .= "\n";
        }

        $prompt .= "--- TODAY'S FOCUS ---\n";
        $prompt .= "TODAY'S NEW NOTES:\n" . $request->notes . "\n\n";
        
        $prompt .= "Based on the formal goals, recent history, and especially TODAY'S new notes, write the recommendation to the parents.";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($response->successful()) {
                $aiText = $response->json('candidates.0.content.parts.0.text');
                
                if (!$aiText) {
                    return response()->json(['success' => false, 'message' => 'Google AI blocked the response due to safety filters.']);
                }

                return response()->json(['success' => true, 'recommendation' => trim($aiText)]);
            }
            
            return response()->json(['success' => false, 'message' => 'Google Error: ' . $response->body()]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $log = DailyLog::findOrFail($id);

        // Security check: Make sure a teacher can only delete THEIR OWN logs
        if ($log->teacher_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'You are not authorized to delete this log.');
        }

        $log->delete();

        return back()->with('success', 'Daily log permanently deleted.');
    }
}