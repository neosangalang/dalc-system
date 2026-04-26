<?php

namespace App\Http\Controllers\Teacher;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\IepGoal;
use App\Models\Report;
use App\Models\AcademicQuarter;
use App\Models\DailyLog;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(\Illuminate\Http\Request $request)
{
    $teacherId = auth()->id();

    // 1. Fetch the teacher's students for the dropdown menu
    $myStudents = \App\Models\Student::where('teacher_id', $teacherId)->get();

    // 2. Start the base query
    $query = \App\Models\Report::whereHas('student', function($q) use ($teacherId) {
        $q->where('teacher_id', $teacherId);
    });

    // 3. APPLY FILTERS
    
    // Filter by Keyword Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('student', function($subQ) use ($search) {
                $subQ->where('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('report_type', 'like', "%{$search}%"); // <-- Fixed here
        });
    }

    // Filter by Specific Student
    if ($request->filled('student_id')) {
        $query->where('student_id', $request->student_id);
    }

    // Filter by Report Type
    if ($request->filled('type')) {
        $query->where('report_type', $request->type); // <-- Fixed here!
    }

    // 4. Execute the query with Pagination
    $reports = $query->latest()->paginate(10)->withQueryString();

    return view('teacher.reports.index', compact('reports', 'myStudents'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch students for the dropdown
        $myStudents = Student::all(); 
        
        return view('teacher.reports.create', compact('myStudents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'report_type' => 'required|string',
            'content' => 'required|string',
            'action' => 'required|in:draft,submit'
        ]);

        $status = $request->action === 'submit' ? 'pending_approval' : 'draft';

        Report::create([
            'student_id' => $request->student_id,
            'teacher_id' => auth()->id(),
            'report_type' => $request->report_type,
            'report_date' => $request->report_date,
            'content' => $request->content,
            'status' => $status,
        ]);

        $message = $status === 'draft' ? 'Report saved as a draft!' : 'Report successfully submitted to Administration for approval!';

        return redirect()->route('teacher.reports.create')->with('success', $message);
    }

    /**
     * THE AI REPORT GENERATOR
     * This talks to Gemini to draft the report based on the student's history and selected dates.
     */
    public function generateAiReport(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'report_type' => 'required|string',
            'report_date' => 'nullable|date'
        ]);

        $student = Student::findOrFail($request->student_id);
        $apiKey = env('GEMINI_API_KEY');

        // 1. Grab Active IEP Goals
        $goals = IepGoal::where('student_id', $student->id)->where('status', 'in_progress')->get();
        $goalDescriptions = $goals->pluck('goal_description')->implode('; ');

        // ==========================================
        // 2. THE SMART DATABASE FILTER
        // ==========================================
        $logsQuery = DailyLog::where('student_id', $student->id);

        if ($request->report_type === 'daily' && $request->report_date) {
            $logsQuery->whereDate('log_date', $request->report_date);
            
        } elseif (in_array($request->report_type, ['q1', 'q2', 'q3', 'q4'])) {
            $quarterCalendar = AcademicQuarter::where('name', strtoupper($request->report_type))->first();
            
            if ($quarterCalendar && $quarterCalendar->start_date && $quarterCalendar->end_date) {
                $logsQuery->whereBetween('log_date', [
                    $quarterCalendar->start_date, 
                    $quarterCalendar->end_date
                ]);
            } else {
                $logsQuery->where('quarter', strtoupper($request->report_type));
            }
            
        } else {
            $logsQuery->limit(100);
        }

        $dailyLogs = $logsQuery->orderBy('log_date', 'asc')->get();

        // ==========================================
        // NEW: THE GATEKEEPER
        // Prevent AI from generating "fluff" if no logs exist for the period
        // ==========================================
        if ($dailyLogs->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot generate report: No daily logs found for this student during the selected timeframe. Please ensure daily logs are recorded before generating a summary.'
            ]);
        }

        $logSummaries = $dailyLogs->map(function($log) {
            return "Date: {$log->log_date} | Notes: {$log->notes}";
        })->implode("\n");
        
        // ==========================================
        // 3. THE PROMPT LOGIC
        // ==========================================
        $timeframe = strtoupper(str_replace('_', ' ', $request->report_type)); 
        
        if ($request->report_type === 'daily') {
            $prompt = "You are an expert Special Education teacher writing a daily update for a parent. 
            Read the following rough notes about the student ({$student->first_name}) and summarize them into ONE simple, warm, and professional paragraph. 
            
            Strict Rules:
            - Do NOT use bullet points, lists, or headers.
            - Do NOT write more than one paragraph.
            - Keep the tone encouraging but factual.
            
            Rough Notes:
            " . $logSummaries;
        } 
        else {
            $prompt = "You are an expert Special Education Teacher. Write a highly professional {$timeframe} report for a student named {$student->first_name} {$student->last_name}. 
            
            Context 1: The student's current active IEP Goals: {$goalDescriptions}.
            Context 2: The student's daily logs specifically from this {$timeframe} period: 
            {$logSummaries}
            
            Strict Rules:
            - You MUST write EXACTLY two paragraphs. No more, no less.
            - Paragraph 1 should summarize the student's overall progress, highlighting strengths and achievements based on the daily logs.
            - Paragraph 2 should address behavioral trends, areas needing support, and progress toward IEP goals.
            - Do NOT use headers, titles, bullet points, or bold text. Just output two clean paragraphs.
            - Use a warm, professional, and objective tone.";
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($response->successful()) {
                $aiText = $response->json('candidates.0.content.parts.0.text');
                return response()->json(['success' => true, 'report_content' => trim($aiText)]);
            }

            return response()->json(['success' => false, 'message' => 'Google AI Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $report = Report::findOrFail($id);

        if ($report->teacher_id === auth()->id()) {
            $report->delete();
            return redirect()->route('teacher.reports.index')->with('success', 'Report successfully deleted.');
        }

        return redirect()->route('teacher.reports.index')->with('error', 'You are not authorized to delete this report.');
    }
    
    public function downloadPdf($id)
    {
        $report = Report::with(['student', 'teacher'])->findOrFail($id);
        $pdf = Pdf::loadView('teacher.reports.pdf', compact('report'));
        $fileName = $report->student->last_name . '_' . $report->student->first_name . '_Report.pdf';

        return $pdf->download($fileName);
    }
}