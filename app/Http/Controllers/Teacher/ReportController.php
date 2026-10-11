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
use Illuminate\Support\Facades\Storage;

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
            'home_recommendations' => 'nullable|string',
            'action' => 'required|in:draft,submit'
        ]);

        if ($request->report_type === 'daily') {
            // Daily reports skip admin review and are published directly to guardians
            $status = $request->action === 'submit' ? 'approved' : 'draft';
        } else {
            $status = $request->action === 'submit' ? 'pending_approval' : 'draft';
        }

        $finalContent = $request->content;
        if ($request->report_type === 'daily' && $request->filled('home_recommendations') && !str_contains($finalContent, "Tonight's Suggested Activity")) {
            $finalContent .= "\n\nTonight's Suggested Activity:\n" . trim($request->home_recommendations);
        }

        $imagePath = null;
        if ($request->report_type === 'daily' && $request->hasFile('photo')) {
            $request->validate([
                'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            ]);
            $imagePath = $request->file('photo')->store('reports', 'local');
        }

        // Capture the newly created report to use its ID for action routing
        $report = Report::create([
            'student_id' => $request->student_id,
            'teacher_id' => auth()->id(),
            'report_type' => $request->report_type,
            'report_date' => $request->report_date,
            'content' => $finalContent,
            'image_path' => $imagePath,
            'status' => $status,
        ]);

        // Notify the Admin and route them directly to this specific report if pending approval
        if ($status === 'pending_approval') {
            $admin = User::where('role', 'admin')->first();
            
            if ($admin) {
                $admin->notify(new SystemAlert(
                    auth()->user()->name . ' submitted a new report for ' . $report->student->first_name . '.', 
                    route('admin.report-approval.show', $report->id) 
                ));
            }
        }

        // If Daily Report, sync home recommendations to daily log for parents
        if ($request->report_type === 'daily' && $request->filled('home_recommendations')) {
            $logDate = $request->report_date ?: date('Y-m-d');
            $log = DailyLog::where('student_id', $request->student_id)
                ->whereDate('log_date', $logDate)
                ->first();

            if ($log) {
                $log->update(['home_recommendations' => trim($request->home_recommendations)]);
            }
        }

        if ($status === 'draft') {
            $message = 'Report saved as a draft!';
        } elseif ($request->report_type === 'daily') {
            $message = 'Daily report published directly to guardian!';
        } else {
            $message = 'Report successfully submitted to Administration for approval!';
        }

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
            'section' => 'nullable|string',
            'revision_notes' => 'nullable|string',
            'existing_content' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);
        $apiKey = env('GROQ_API_KEY');
        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');

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
        } elseif ($request->report_type === 'mid_year') {
            $q1 = AcademicQuarter::where('name', 'Q1')->first();
            $q2 = AcademicQuarter::where('name', 'Q2')->first();
            if ($q1 && $q2 && $q1->start_date && $q2->end_date) {
                $logsQuery->whereBetween('log_date', [$q1->start_date, $q2->end_date]);
            } else {
                $logsQuery->whereIn('quarter', ['Q1', 'Q2']);
            }
        } elseif ($request->report_type === 'year_end') {
            $q1 = AcademicQuarter::where('name', 'Q1')->first();
            $q4 = AcademicQuarter::where('name', 'Q4')->first();
            if ($q1 && $q4 && $q1->start_date && $q4->end_date) {
                $logsQuery->whereBetween('log_date', [$q1->start_date, $q4->end_date]);
            } else {
                $logsQuery->whereIn('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
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
            if ($request->section === 'home_recommendation') {
                $prompt = "You are an expert Special Education teacher writing home-based recommendations for a student named {$student->first_name}.
                Read the following daily observation notes and generate 1-2 easy, practical, and engaging home activities for the parents to practice tonight with their child.
                Do NOT output headers or daily report narratives. Output only 1-2 practical home-based activities for tonight.
                
                Rough Observation Notes:
                " . $logSummaries;
            } else {
                $prompt = "You are an expert Special Education teacher writing a formal Daily Report summary for a student named {$student->first_name}.
                Read the following daily observation notes and generate a clear, warm, and professional daily report narrative paragraph summarizing the student's observations, activities, and progress today.
                Do NOT include home recommendations or activity lists. Output only the classroom progress summary.
                
                Rough Observation Notes:
                " . $logSummaries;
            }
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
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DALC-System/1.0',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-20b', 
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

        if ($report->teacher_id === auth()->id() || auth()->user()->role === 'admin') {
            if ($report->image_path) {
                if (Storage::disk('local')->exists($report->image_path)) {
                    Storage::disk('local')->delete($report->image_path);
                } elseif (Storage::disk('public')->exists($report->image_path)) {
                    Storage::disk('public')->delete($report->image_path);
                }
            }
            $report->delete();
            return redirect()->route('teacher.reports.index')->with('success', 'Report successfully deleted.');
        }

        return redirect()->route('teacher.reports.index')->with('error', 'You are not authorized to delete this report.');
    }

    public function deleteAttachment(string $id)
    {
        $report = Report::findOrFail($id);

        if ($report->teacher_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'You are not authorized to delete this attachment.');
        }

        if ($report->image_path) {
            if (Storage::disk('local')->exists($report->image_path)) {
                Storage::disk('local')->delete($report->image_path);
            } elseif (Storage::disk('public')->exists($report->image_path)) {
                Storage::disk('public')->delete($report->image_path);
            }
            $report->update(['image_path' => null]);
        }

        return redirect()->back()->with('success', 'Attached photo deleted successfully.');
    }
    
    public function downloadPdf($id)
    {
        $report = Report::with(['student', 'teacher', 'approver'])->findOrFail($id);
        $pdf = Pdf::loadView('teacher.reports.pdf', compact('report'));
        $fileName = $report->student->last_name . '_' . $report->student->first_name . '_Report.pdf';

        return $pdf->download($fileName);
    }
}