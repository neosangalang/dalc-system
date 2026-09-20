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
use App\Models\User;
use App\Notifications\SystemAlert;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teacherId = auth()->id();

        // 1. Fetch the teacher's students for the dropdown menu
        $myStudents = Student::where('teacher_id', $teacherId)->get();

        // 2. Start the base query
        $query = Report::whereHas('student', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });

        // 3. APPLY FILTERS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($subQ) use ($search) {
                    $subQ->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhere('report_type', 'like', "%{$search}%"); 
            });
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('type')) {
            $query->where('report_type', $request->type); 
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

        // Capture the newly created report to use its ID for action routing
        $report = Report::create([
            'student_id' => $request->student_id,
            'teacher_id' => auth()->id(),
            'report_type' => $request->report_type,
            'report_date' => $request->report_date,
            'content' => $request->content,
            'status' => $status,
        ]);

        // Notify the Admin and route them directly to this specific report
        if ($status === 'pending_approval') {
            $admin = User::where('role', 'admin')->first();
            
            if ($admin) {
                $admin->notify(new SystemAlert(
                    auth()->user()->name . ' submitted a new report for ' . $report->student->first_name . '.', 
                    route('admin.report-approval.show', $report->id) 
                ));
            }
        }

        $message = $status === 'draft' ? 'Report saved as a draft!' : 'Report successfully submitted to Administration for approval!';

        return redirect()->route('teacher.reports.create')->with('success', $message);
    }

    /**
     * Show the form for editing a rejected report.
     */
    public function edit(string $id)
    {
        $report = Report::where('teacher_id', auth()->id())->findOrFail($id);
        
        // Ensure only rejected/needs-revision reports can be edited through this flow
        if ($report->status !== 'rejected') {
            return redirect()->route('teacher.reports.index')->with('error', 'You can only edit reports that require revision.');
        }

        $myStudents = Student::where('teacher_id', auth()->id())->get();

        return view('teacher.reports.edit', compact('report', 'myStudents'));
    }

    /**
     * Update the revised report and resubmit to admin.
     */
    public function update(Request $request, string $id)
    {
        $report = Report::where('teacher_id', auth()->id())->findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'report_type' => 'required|string',
            'content' => 'required|string',
            'report_date' => 'nullable|date'
        ]);

        // Update report content and set status back to pending_approval for review
        $report->update([
            'student_id' => $request->student_id,
            'report_type' => $request->report_type,
            'report_date' => $request->report_date,
            'content' => $request->content,
            'status' => 'pending_approval',
        ]);

        // Notify Admin of the resubmission
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new SystemAlert(
                auth()->user()->name . ' resubmitted a revised report for ' . $report->student->first_name . '.', 
                route('admin.report-approval.show', $report->id) 
            ));
        }

        return redirect()->route('teacher.reports.index')->with('success', 'Report successfully updated and resubmitted to Administration for approval!');
    }

    /**
     * THE AI REPORT GENERATOR (Groq Llama 3)
     */
    public function generateAiReport(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'report_type' => 'required|string',
            'report_date' => 'nullable|date',
            'revision_notes' => 'nullable|string',
            'existing_content' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $apiKey = config('services.groq.api_key') ?? env('GROQ_API_KEY');

        $goals = IepGoal::where('student_id', $student->id)->where('status', 'in_progress')->get();
        $goalDescriptions = $goals->pluck('goal_description')->implode('; ');

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

        if ($dailyLogs->isEmpty() && empty($request->existing_content)) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot generate report: No daily logs found for this student during the selected timeframe. Please ensure daily logs are recorded before generating a summary.'
            ]);
        }

        $logSummaries = $dailyLogs->map(function($log) {
            return "Date: {$log->log_date} | Notes: {$log->notes}";
        })->implode("\n");
        
        $timeframe = strtoupper(str_replace('_', ' ', $request->report_type)); 
        
        // Check if this is an AI Revision based on Admin Notes
        if ($request->filled('revision_notes')) {
            $prompt = "You are an expert Special Education Teacher revising an official student report based on specific Administrative revision notes.

STUDENT: {$student->first_name} {$student->last_name}
REPORT TYPE / TIMEFRAME: {$timeframe}

ADMINISTRATOR'S REQUIRED REVISIONS & INSTRUCTIONS:
\"{$request->revision_notes}\"

PREVIOUS REPORT DRAFT:
\"{$request->existing_content}\"

ACTIVE IEP GOALS:
{$goalDescriptions}

DAILY LOGS & CLASSROOM NOTES:
{$logSummaries}

STRICT REVISION RULES:
1. Thoroughly incorporate and address all feedback and instructions provided by the Administrator.
2. Refine, expand, or clarify the draft using supporting details from daily logs and IEP goals.
3. Keep the format concise: write exactly 2 cohesive paragraphs (or 1 paragraph if daily report).
4. Do NOT use headers, bullet points, asterisks, bold titles, or conversational filler. Output only clean narrative paragraphs.
5. Maintain a compassionate, objective, and professional Special Education tone.";
        }
        elseif ($request->report_type === 'daily') {
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
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile', 
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7
                ]);

            if ($response->successful()) {
                $aiText = $response->json('choices.0.message.content');
                return response()->json(['success' => true, 'report_content' => trim($aiText)]);
            }

            return response()->json(['success' => false, 'message' => 'Groq API Error: ' . $response->body()], 500);

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
        $report = Report::with(['student', 'teacher', 'approver'])->findOrFail($id);
        $pdf = Pdf::loadView('teacher.reports.pdf', compact('report'));
        $fileName = $report->student->last_name . '_' . $report->student->first_name . '_Report.pdf';

        return $pdf->download($fileName);
    }
}