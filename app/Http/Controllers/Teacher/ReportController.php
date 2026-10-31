<?php

namespace App\Http\Controllers\Teacher;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ImageOptimizationService;
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
use App\Notifications\GuardianAlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, exclude archived reports from the active list so it clears up after archive
            $query->where('status', '!=', 'archived');
        }

        // 4. Execute the query with Pagination
        $reports = $query->with(['student', 'teacher', 'comments.user'])->latest()->paginate(10)->withQueryString();

        // Merge comments from matching DailyLogs for daily reports
        foreach ($reports as $report) {
            if ($report->report_type === 'daily') {
                $repDate = $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('Y-m-d') : $report->created_at->format('Y-m-d');
                $matchingLog = DailyLog::with('comments.user')
                    ->where('student_id', $report->student_id)
                    ->whereDate('log_date', $repDate)
                    ->first();
                if ($matchingLog && $matchingLog->comments->isNotEmpty()) {
                    $merged = $report->comments->concat($matchingLog->comments)
                        ->unique('id')
                        ->sortByDesc('created_at')
                        ->values();
                    $report->setRelation('comments', $merged);
                }
            }
        }

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

        $reportDate = $request->report_date ?: date('Y-m-d');

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
            $imagePath = ImageOptimizationService::optimizeAndStore($request->file('photo'), 'private_report_photos', 'local');
        }

        // Capture the newly created report to use its ID for action routing
        $reportData = [
            'student_id' => $request->student_id,
            'teacher_id' => auth()->id(),
            'report_type' => $request->report_type,
            'report_date' => $reportDate,
            'content' => $finalContent,
            'image_path' => $imagePath,
            'status' => $status,
        ];

        if ($status === 'approved') {
            $reportData['approved_by'] = auth()->id();
            $reportData['approved_at'] = now();
        }

        $report = Report::create($reportData);

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

        // If Daily Report, sync narrative, home recommendations and photo to daily log for parents
        if ($request->report_type === 'daily') {
            $log = DailyLog::where('student_id', $request->student_id)
                ->whereDate('log_date', $reportDate)
                ->first();

            $homeRec = $request->filled('home_recommendations') ? trim($request->home_recommendations) : null;

            if ($log) {
                $updates = [
                    'notes' => $finalContent,
                ];
                if ($homeRec) {
                    $updates['home_recommendations'] = $homeRec;
                }
                if ($imagePath) {
                    $updates['image_path'] = $imagePath;
                }
                $log->update($updates);
            } else {
                \App\Models\AcademicQuarter::syncActiveStatus();
                $activeQuarter = \App\Models\AcademicQuarter::where('is_active', true)->first();
                $quarterName = $activeQuarter ? strtoupper($activeQuarter->name) : 'Q1';

                DailyLog::create([
                    'student_id' => $request->student_id,
                    'teacher_id' => auth()->id(),
                    'log_date'   => $reportDate,
                    'quarter'    => $quarterName,
                    'notes'      => $finalContent,
                    'home_recommendations' => $homeRec,
                    'image_path' => $imagePath,
                ]);
            }
        }

        // When Daily Report is submitted (published), notify the Guardian via in-app bell & email
        if ($request->report_type === 'daily' && $status === 'approved') {
            try {
                $student = Student::with('guardian')->find($request->student_id);
                if ($student && $student->guardian) {
                    $hasHomeAdvice = $request->filled('home_recommendations');
                    $title = $hasHomeAdvice ? 'Daily Report & Home Activity' : 'Daily Report';
                    $teacherName = auth()->user()->name;
                    $message = "Teacher {$teacherName} published today's Daily Report" . ($hasHomeAdvice ? " and home recommendations" : "") . " for {$student->first_name}.";
                    
                    $extraDetails = $hasHomeAdvice 
                        ? "Tonight's Suggested Activity: " . Str::limit(strip_tags($request->home_recommendations), 140) 
                        : "Daily Summary: " . Str::limit(strip_tags($finalContent), 120);

                    $targetUrl = \Carbon\Carbon::parse($reportDate)->isToday() 
                        ? route('guardian.dashboard') 
                        : route('guardian.reports');

                    $student->guardian->notify(new GuardianAlertNotification(
                        title: $title,
                        message: $message,
                        url: $targetUrl,
                        studentName: $student->first_name,
                        actionText: 'View Daily Report',
                        extraDetails: $extraDetails
                    ));
                }
            } catch (\Exception $e) {
                Log::error('Guardian notification failed on daily report submit: ' . $e->getMessage());
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
        $apiKey = trim(config('services.groq.api_key') ?? env('GROQ_API_KEY'), '"\' ');
        $model = config('services.groq.model', 'openai/gpt-oss-20b');

        $goals = IepGoal::where('student_id', $student->id)->where('status', 'in_progress')->get();
        $goalDescriptions = $goals->pluck('goal_description')->implode('; ');

        $logsQuery = DailyLog::where('student_id', $student->id);

        if ($request->report_type === 'daily' && $request->report_date) {
            $logsQuery->whereDate('log_date', $request->report_date);
            
        } elseif (in_array($request->report_type, ['q1', 'q2', 'q3', 'q4'])) {
            $activeSY = \App\Models\SchoolYear::active();
            $quarterCalendar = $activeSY
                ? $activeSY->quarters()->where('name', strtoupper($request->report_type))->first()
                : AcademicQuarter::where('name', strtoupper($request->report_type))->first();
            
            if ($quarterCalendar && $quarterCalendar->start_date && $quarterCalendar->end_date) {
                $logsQuery->whereBetween('log_date', [
                    $quarterCalendar->start_date, 
                    $quarterCalendar->end_date
                ]);
            } else {
                $logsQuery->where('quarter', strtoupper($request->report_type));
            }
        } elseif ($request->report_type === 'mid_year') {
            $activeSY = \App\Models\SchoolYear::active();
            $q1 = $activeSY ? $activeSY->quarters()->where('name', 'Q1')->first() : AcademicQuarter::where('name', 'Q1')->first();
            $q2 = $activeSY ? $activeSY->quarters()->where('name', 'Q2')->first() : AcademicQuarter::where('name', 'Q2')->first();
            if ($q1 && $q2 && $q1->start_date && $q2->end_date) {
                $logsQuery->whereBetween('log_date', [$q1->start_date, $q2->end_date]);
            } else {
                $logsQuery->whereIn('quarter', ['Q1', 'Q2']);
            }
        } elseif ($request->report_type === 'year_end') {
            $activeSY = \App\Models\SchoolYear::active();
            $q1 = $activeSY ? $activeSY->quarters()->where('name', 'Q1')->first() : AcademicQuarter::where('name', 'Q1')->first();
            $q4 = $activeSY ? $activeSY->quarters()->where('name', 'Q4')->first() : AcademicQuarter::where('name', 'Q4')->first();
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
2. Refine, expand, or clarify the draft using supporting details strictly from daily logs and IEP goals. Do NOT fabricate unrecorded student actions or achievements.
3. Keep the format concise: write exactly 2 cohesive paragraphs (or 1 paragraph if daily report).
4. Do NOT use headers, bullet points, asterisks (**), bold titles, or conversational filler. Output only clean narrative paragraphs.
5. Maintain a compassionate, objective, and professional Special Education tone.";
        }
        elseif ($request->report_type === 'daily') {
            if ($request->section === 'home_recommendation') {
                $prompt = "You are an expert Special Education teacher writing actionable home-based recommendations for {$student->first_name}'s parents.

OBSERVATION NOTES:
{$logSummaries}

STRICT QUALITY GUIDELINES:
1. STRICT GROUNDING (NO HALLUCINATIONS): Suggest activities that directly connect to what was observed or practiced in the notes. Do not invent unmentioned skills, challenges, or behaviors.
2. SUSTAINABLE: Activities must be realistic, take 5-10 minutes, and use common household objects (e.g., during dinner, bedtime, or routine play). Do not recommend complex therapeutic protocols or materials parents do not have.
3. SPECIFIC & PRECISE: State exactly what parents can do and say in clear, concrete steps.
4. FORMAT: Output exactly 1-2 clear, practical home activities in a warm, concise paragraph (under 4 sentences). Do NOT output headers, titles, bullet points, asterisks (**), or narrative summaries.";
            } else {
                $prompt = "You are an expert Special Education teacher writing a formal Daily Report summary for {$student->first_name}.

OBSERVATION NOTES:
{$logSummaries}

STRICT QUALITY GUIDELINES:
1. STRICT FACTUAL ACCURACY (NO HALLUCINATIONS): Summarize ONLY what is documented in the observation notes. Do not extrapolate, exaggerate, or invent achievements or behavioral issues.
2. PROFESSIONAL & WARM: Objective, supportive Special Education language suitable for official school records and parent viewing.
3. CONCISE: Exactly one cohesive paragraph summarizing today's classroom activities, engagement, and support provided.
4. FORMAT: Output only the narrative paragraph. Do NOT include headers, bullet points, asterisks (**), or home recommendations.";
            }
        } 
        else {
            $prompt = "You are an expert Special Education Teacher writing an official {$timeframe} progress report for {$student->first_name} {$student->last_name}.

ACTIVE IEP GOALS:
{$goalDescriptions}

DOCUMENTED DAILY LOGS & CLASSROOM OBSERVATIONS:
{$logSummaries}

STRICT QUALITY RULES:
1. STRICT GROUNDING (NO HALLUCINATION): Base every statement exclusively on the documented daily logs and active IEP goals. If logs are sparse, concisely summarize only observed patterns without inventing events, milestones, or diagnostic conclusions.
2. PRECISE TWO-PARAGRAPH STRUCTURE:
   - Paragraph 1: Summarize the student's documented academic and developmental progress, highlighting strengths and concrete achievements from this period.
   - Paragraph 2: Address documented behavioral observations, support accommodations used, and measurable progress toward active IEP goals.
3. SUSTAINABLE & CLEAR TONE: Use objective, professional, and compassionate Special Education terminology that is direct and easily understood by both educators and parents.
4. FORMAT: Exactly two clean narrative paragraphs. Do NOT include titles, headers, bullet points, or markdown formatting like asterisks (**).";
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) DALC-System/1.0',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model, 
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.2
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

        return $pdf->stream($fileName);
    }
}