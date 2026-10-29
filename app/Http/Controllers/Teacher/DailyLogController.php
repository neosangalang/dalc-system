<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\DailyLog;
use App\Models\Report; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ImageOptimizationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class DailyLogController extends Controller
{
    public function index() {
        $teacherId = Auth::id();
        
        // Fetch students for the dropdown
        $myStudents = Student::where('teacher_id', $teacherId)->where('status', 'active')->get();
        
        // Fetch recent logs with comments and student info to display on the feed
        $recentLogs = DailyLog::with(['student', 'comments.user'])
            ->where('teacher_id', $teacherId)
            ->latest('log_date')
            ->take(15)
            ->get();

        foreach ($recentLogs as $log) {
            $logDate = \Carbon\Carbon::parse($log->log_date)->format('Y-m-d');
            $matchingReport = Report::with('comments.user')
                ->where('student_id', $log->student_id)
                ->where('report_type', 'daily')
                ->where(function($q) use ($logDate) {
                    $q->whereDate('report_date', $logDate)
                      ->orWhere(function($sub) use ($logDate) {
                          $sub->whereNull('report_date')->whereDate('created_at', $logDate);
                      });
                })
                ->first();
            if ($matchingReport && $matchingReport->comments->isNotEmpty()) {
                $merged = $log->comments->concat($matchingReport->comments)
                    ->unique('id')
                    ->sortByDesc('created_at')
                    ->values();
                $log->setRelation('comments', $merged);
            }
        }

        // Fetch active quarter for auto-selecting in log form
        \App\Models\AcademicQuarter::syncActiveStatus();
        $activeQuarter = \App\Models\AcademicQuarter::where('is_active', true)->first();
        $activeQuarterName = $activeQuarter ? strtoupper($activeQuarter->name) : 'Q1';

        return view('teacher.daily-logs.index', compact('myStudents', 'recentLogs', 'activeQuarterName'));
    }

    public function markCommentsAsRead($id)
    {
        $log = DailyLog::findOrFail($id);

        if ($log->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $log->comments()
             ->where('user_id', '!=', Auth::id())
             ->update(['is_teacher_read' => 1]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request) {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'log_date'   => 'required|date',
            'quarter'    => 'required|string',
            'raw_notes'  => 'nullable|string',
            'notes'      => 'nullable|string',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120', 
        ]);

        $rawTrimmed = trim((string)$request->raw_notes);
        $notesTrimmed = trim((string)$request->notes);

        if ($rawTrimmed === '' && $notesTrimmed === '') {
            return back()->withInput()->withErrors(['notes' => 'Please provide either Quick Notes or Formal Documentation.']);
        }

        // Security check
        $student = Student::where('id', $request->student_id)->where('teacher_id', Auth::id())->firstOrFail();

        $finalNotes = $notesTrimmed !== '' ? $request->notes : $request->raw_notes;

        // SECURE VAULT STORAGE & OPTIMIZATION
        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imagePath = ImageOptimizationService::optimizeAndStore($request->file('photo'), 'private_log_photos', 'local');
        }

        $dailyLog = DailyLog::create([
            'student_id' => $student->id,
            'teacher_id' => Auth::id(),
            'log_date'   => $request->log_date,
            'quarter'    => $request->quarter,
            'notes'      => $finalNotes, 
            'home_recommendations' => $request->home_recommendations ?? null,
            'image_path' => $imagePath, 
        ]);

        return redirect()->route('teacher.daily-logs.index')->with('success', 'Daily log saved successfully!');
    }

    public function generateAiReport(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'raw_notes' => 'required|string|max:1000',
        ]);

        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is missing. Please contact the administrator.'], 500);
        }

        $systemPrompt = <<<PROMPT
You are a Special Education teacher writing clear daily classroom observation notes. Your task is to polish informal, rough notes into simple, precise, and natural English.

STRICT WRITING RULES:
1. PRECISE & SIMPLE (NO COMPLICATED OR VAGUE JARGON):
   - Keep the language simple, direct, and natural.
   - Use straightforward everyday words. Describe what happened plainly without inflating or over-complicating it.
   - DO NOT use stiff, robotic, or overly formal phrases.
     * NEVER write: "demonstrated frustration following the occurrence of an error" -> INSTEAD write: "Neo became frustrated after making a mistake."
     * NEVER write: "subsequent to the occurrence of" -> INSTEAD write: "after" or "when".
     * NEVER write: "manifested behaviors indicative of" -> INSTEAD write: "showed" or "expressed".
     * NEVER write: "engaged in the utilization of" -> INSTEAD write: "used".
     * NEVER write: "participated in nutritional consumption" -> INSTEAD write: "ate lunch" or "ate snack".

2. 100% FACTUAL & ACCURATE (ZERO HALLUCINATION):
   - Only include what is explicitly mentioned in the teacher's rough note.
   - Do NOT assume, invent, or extrapolate extra details, causes, interventions, or feelings.

3. GRAMMAR & CLEANUP:
   - Fix spelling, verb tenses (e.g., "become" -> "became"), and punctuation.
   - Capitalize names (e.g., "neo" -> "Neo") and start of sentences.

4. CLEAN, CONCISE OUTPUT:
   - Output ONLY the rewritten note in 1 to 2 clear, simple sentences.
   - Do NOT include quotes, bullet points, markdown bolding (**), or prefixes like "Daily Note:" or "Documentation:".

EXAMPLES:
- Input: neo become frustrated after making a mistake
  Output: Neo became frustrated after making a mistake.
- Input: worked on tracing letters needed 2 verbal reminders to stay on task
  Output: Worked on tracing letters and needed two verbal reminders to stay on task.
- Input: shared crayons with classmate during art time and smiled
  Output: Shared crayons with a classmate during art class and showed a positive attitude.
- Input: refused to wear shoes during sensory play but calmed down with teacher help
  Output: Refused to wear shoes during sensory play, but calmed down with help from the teacher.
PROMPT;

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
                            'content' => "Informal Notes: " . $request->raw_notes
                        ]
                    ],
                    'temperature' => 0.2,
                ]);

            if ($response->successful()) {
                $formalText = $response->json('choices.0.message.content');
                
                $formalText = trim($formalText);
                $formalText = preg_replace('/^\*\*.*?\*\*:?\s*/', '', $formalText);
                $formalText = preg_replace('/^(Formal Documentation|Documentation|Daily Note|Note):?\s*/i', '', $formalText);
                $formalText = trim($formalText, " \t\n\r\0\x0B\"'");
                
                return response()->json(['formal_note' => $formalText]);
            }

            return response()->json(['error' => 'AI Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function generateAiRecommendations(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'notes' => 'required|string',
            'student_id' => 'required|exists:students,id'
        ]);

        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API key is missing. Please check your .env file.']);
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
        $contextPrompt = "--- HISTORICAL CONTEXT ---\n";
        
        if ($formalReport) {
            $contextPrompt .= "LATEST FORMAL REPORT (" . strtoupper($formalReport->report_type) . "):\n" . $formalReport->content . "\n\n";
        } else {
            $contextPrompt .= "LATEST FORMAL REPORT: No formal report available yet.\n\n";
        }

        if ($recentLogs->isNotEmpty()) {
            $contextPrompt .= "RECENT QUICK NOTES (Past few days):\n";
            foreach ($recentLogs as $log) {
                if (!empty($log->notes)) {
                    $contextPrompt .= "- " . \Carbon\Carbon::parse($log->log_date)->format('M d') . ": " . $log->notes . "\n";
                }
            }
            $contextPrompt .= "\n";
        }

        $contextPrompt .= "--- TODAY'S FOCUS ---\n";
        $contextPrompt .= "TODAY'S NEW NOTES:\n" . $request->notes . "\n\n";
        $contextPrompt .= "Based on the formal goals, recent history, and especially TODAY'S new notes, write the recommendation to the parents.";

        $systemPrompt = <<<PROMPT
You are an expert Special Education Teacher writing actionable, home-based recommendations for parents.

CRITICAL QUALITY STANDARDS:
1. STRICT ANTI-HALLUCINATION / GROUNDEDNESS:
   - Anchor your suggestions ONLY to the specific skills practiced, progress made, or challenges documented in TODAY'S NEW NOTES.
   - Do NOT invent, assume, or hallucinate events, tantrums, abilities, or behaviors that are not explicitly stated.

2. SUSTAINABLE & LOW-BURDEN:
   - Home activities must be sustainable and realistic for busy parents.
   - Each activity must take only 5 to 10 minutes and use everyday household items (e.g., spoons during dinner, clothing items during dressing, bedtime reading, simple sorting).
   - NEVER suggest complicated therapeutic protocols, expensive specialized equipment, or high-stress drills.

3. SPECIFIC & PRECISE:
   - State exactly what the parent should do and what the child practices in clear, concrete steps (e.g., 'Ask your child to hand you 2 blue socks while folding laundry to reinforce color identification').
   - Avoid vague general advice like 'practice communication' or 'work on behavior'.

4. CLEAR, WARM, AND CONCISE:
   - Write a single cohesive, warm paragraph containing exactly 2 practical activities.
   - Keep it strictly under 4 sentences.
   - Do NOT use markdown asterisks (**), headers, bullet points, or numbering. Just clear, compassionate sentences.
PROMPT;

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
                            'content' => $contextPrompt
                        ]
                    ],
                    'temperature' => 0.2,
                ]);

            if ($response->successful()) {
                $aiText = $response->json('choices.0.message.content');
                
                if (!$aiText) {
                    return response()->json(['success' => false, 'message' => 'AI blocked the response due to safety filters.']);
                }

                return response()->json(['success' => true, 'recommendation' => trim($aiText)]);
            }
            
            return response()->json(['success' => false, 'message' => 'AI Error: ' . $response->body()]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $log = DailyLog::findOrFail($id);

        if ($log->teacher_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'You are not authorized to delete this log.');
        }

        $log->delete();

        return back()->with('success', 'Daily log permanently deleted.');
    }

    public function downloadPdf($id)
    {
        $log = DailyLog::with(['student', 'teacher'])->findOrFail($id);

        if ($log->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized access to daily log documentation.');
        }

        $pdf = Pdf::loadView('teacher.daily-logs.pdf', compact('log'));

        $safeLastName = Str::slug($log->student->last_name ?? 'Student');
        $safeFirstName = Str::slug($log->student->first_name ?? '');
        $dateStr = \Carbon\Carbon::parse($log->log_date)->format('Y-m-d');
        $fileName = "DailyLog_{$safeLastName}_{$safeFirstName}_{$dateStr}.pdf";

        return $pdf->download($fileName);
    }
}