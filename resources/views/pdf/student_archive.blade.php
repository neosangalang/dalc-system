<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Archive: {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1E2657;
            line-height: 1.5;
        }

        /* ===== HEADER ===== */
        .header {
            background-color: #1E2657;
            color: #FFFFFF;
            padding: 25px 30px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 11px;
            color: #B0B5D0;
            margin: 0;
        }

        .header .school-year {
            display: inline-block;
            background-color: rgba(255,255,255,0.15);
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            color: #FFFFFF;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        /* ===== CONTENT AREA ===== */
        .content {
            padding: 25px 30px;
        }

        /* ===== SECTION HEADERS ===== */
        .section-title {
            font-size: 13px;
            font-weight: 800;
            color: #1E2657;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #1E2657;
            padding-bottom: 6px;
            margin-bottom: 15px;
            margin-top: 30px;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        /* ===== STUDENT INFO TABLE ===== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 7px 12px;
            vertical-align: top;
            border: 1px solid #E2E4EC;
        }

        .info-table .label {
            background-color: #F4F6FA;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6B7086;
            width: 28%;
        }

        .info-table .value {
            font-size: 11px;
            font-weight: 600;
            color: #1E2657;
        }

        /* ===== SUMMARY BOX ===== */
        .summary-box {
            background-color: #F4F6FA;
            border: 1px solid #E2E4EC;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .summary-box .stat {
            display: inline-block;
            margin-right: 30px;
        }

        .summary-box .stat-number {
            font-size: 18px;
            font-weight: 800;
            color: #1E2657;
        }

        .summary-box .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6B7086;
            font-weight: 700;
        }

        /* ===== IEP GOAL CARDS ===== */
        .goal-card {
            border: 1px solid #E2E4EC;
            border-radius: 6px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .goal-card-header {
            background-color: #F4F6FA;
            padding: 10px 15px;
            border-bottom: 1px solid #E2E4EC;
        }

        .goal-card-header .goal-number {
            display: inline-block;
            background-color: #8B5CF6;
            color: #FFFFFF;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 10px;
            font-weight: 700;
            margin-right: 8px;
        }

        .goal-card-header .domain-name {
            font-size: 12px;
            font-weight: 800;
            color: #1E2657;
        }

        .goal-card-header .goal-meta {
            float: right;
            text-align: right;
        }

        .goal-card-header .progress-text {
            font-size: 10px;
            font-weight: 700;
            color: #6B7086;
            margin-right: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-progress {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-achieved {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .goal-card-body {
            padding: 12px 15px;
        }

        .objective-label {
            font-size: 10px;
            font-weight: 800;
            color: #8B5CF6;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .objective-text {
            font-size: 11px;
            color: #1E2657;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .activities-label {
            font-size: 10px;
            font-weight: 800;
            color: #F59E0B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            margin-top: 8px;
        }

        .activity-item {
            font-size: 10px;
            color: #6B7086;
            line-height: 1.6;
            padding: 3px 0;
            padding-left: 18px;
            position: relative;
        }

        .activity-item::before {
            content: "☐";
            position: absolute;
            left: 0;
            color: #D1D5DB;
        }

        .activity-item.checked::before {
            content: "☑";
            color: #10B981;
        }

        .goal-separator {
            border: none;
            border-top: 1px dashed #E2E4EC;
            margin: 12px 0;
        }

        /* ===== DAILY LOGS TABLE ===== */
        .logs-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .logs-table thead th {
            background-color: #1E2657;
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 8px 12px;
            text-align: left;
        }

        .logs-table thead th:first-child {
            width: 5%;
        }

        .logs-table thead th:nth-child(2) {
            width: 15%;
        }

        .logs-table thead th:nth-child(3) {
            width: 13%;
        }

        .logs-table tbody td {
            padding: 8px 12px;
            border: 1px solid #E2E4EC;
            font-size: 10px;
            vertical-align: top;
        }

        .logs-table tbody tr:nth-child(even) {
            background-color: #FAFBFD;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 30px;
            padding: 15px 30px;
            border-top: 2px solid #E2E4EC;
            font-size: 9px;
            color: #9CA0AF;
            text-align: center;
        }

        .footer strong {
            color: #6B7086;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <p style="font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #8B93B8; margin-bottom: 6px;">
            Dream Achievers Learning Center PH
        </p>
        <h1>Student Master Archive</h1>
        <p>{{ $student->first_name }} {{ $student->last_name }}</p>
        <span class="school-year">SY 2024–2025</span>
    </div>

    <div class="content">

        {{-- ===== STUDENT INFORMATION ===== --}}
        <div class="section-title">Student Information</div>

        <table class="info-table">
            <tr>
                <td class="label">Full Name</td>
                <td class="value">{{ $student->first_name }} {{ $student->last_name }}</td>
                <td class="label">LRN</td>
                <td class="value">{{ $student->learners_reference_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Date of Birth</td>
                <td class="value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('F j, Y') : 'N/A' }}</td>
                <td class="label">Gender</td>
                <td class="value">{{ ucfirst($student->gender ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Class</td>
                <td class="value">{{ $student->class_name ?? 'N/A' }}</td>
                <td class="label">Diagnosis</td>
                <td class="value">{{ $student->diagnosis ?? ($student->exceptionality ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Teacher</td>
                <td class="value">{{ $student->teacher->name ?? 'N/A' }}</td>
                <td class="label">Guardian</td>
                <td class="value">{{ $student->guardian->name ?? 'N/A' }}</td>
            </tr>
        </table>

        @php
            $formalReports = $student->reports ? $student->reports->whereIn('report_type', ['q1', 'q2', 'q3', 'q4', 'mid_year', 'year_end'])->sortBy('report_date') : collect();
            $documents = $student->documents ?? collect();
        @endphp

        {{-- ===== SUMMARY STATS ===== --}}
        <div class="summary-box">
            <span class="stat">
                <span class="stat-number">{{ $student->iepGoals->count() }}</span>
                <span class="stat-label"> IEP Goals</span>
            </span>
            <span class="stat">
                <span class="stat-number">{{ $student->iepGoals->where('status', 'achieved')->count() }}</span>
                <span class="stat-label"> Achieved</span>
            </span>
            <span class="stat">
                <span class="stat-number">{{ $formalReports->count() }}</span>
                <span class="stat-label"> Progress Summaries</span>
            </span>
            <span class="stat">
                <span class="stat-number">{{ $documents->count() }}</span>
                <span class="stat-label"> Attached Files</span>
            </span>
        </div>

        {{-- ===== IEP GOALS (Card Layout) ===== --}}
        <div class="section-title">Individualized Education Program (IEP) Goals</div>

        @if($student->iepGoals->count() > 0)
            @foreach($student->iepGoals as $index => $goal)
                <div class="goal-card">
                    {{-- Card Header --}}
                    <div class="goal-card-header">
                        <span class="goal-number">{{ $index + 1 }}</span>
                        <span class="domain-name">{{ $goal->domain }}</span>
                        <span class="goal-meta">
                            <span class="progress-text">{{ $goal->progress_percentage }}%</span>
                            <span class="badge {{ $goal->status === 'achieved' ? 'badge-achieved' : 'badge-progress' }}">
                                {{ $goal->status === 'achieved' ? 'Achieved' : 'In Progress' }}
                            </span>
                        </span>
                    </div>

                    {{-- Card Body --}}
                    <div class="goal-card-body">
                        @if(!empty($goal->plop))
                            <div style="background-color: #F8FAFC; border-left: 3px solid #8B5CF6; padding: 7px 12px; margin-bottom: 12px; border-radius: 4px;">
                                <div style="font-size: 9px; font-weight: 800; color: #8B5CF6; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 3px;">
                                    Present Level of Performance (PLOP):
                                </div>
                                <div style="font-size: 10px; color: #1E2657; line-height: 1.5;">
                                    {!! nl2br(e($goal->plop)) !!}
                                </div>
                            </div>
                        @endif
                        @php
                            $description = $goal->goal_description;
                            // Split by objective markers (--- OBJECTIVE: or . OBJECTIVE:)
                            $parts = preg_split('/(-{3,}\s*OBJECTIVE:|(?<=\.)\s*OBJECTIVE:)/', $description, -1, PREG_SPLIT_NO_EMPTY);
                        @endphp

                        @if(count($parts) > 1)
                            @foreach($parts as $partIndex => $part)
                                @php
                                    $part = trim($part);
                                    $sections = preg_split('/(ACTIVITIES:\s*)/', $part, 2, PREG_SPLIT_DELIM_CAPTURE);
                                    $objectiveText = trim($sections[0] ?? '');
                                    $activitiesText = isset($sections[2]) ? trim($sections[2]) : '';
                                    $objectiveText = preg_replace('/^OBJECTIVE:\s*/i', '', $objectiveText);
                                @endphp

                                @if($partIndex > 0)
                                    <hr class="goal-separator">
                                @endif

                                @if($objectiveText)
                                    <p class="objective-label">⊕ Objective {{ count($parts) > 1 ? $partIndex + 1 : '' }}</p>
                                    <p class="objective-text">{{ $objectiveText }}</p>
                                @endif

                                @if($activitiesText)
                                    <p class="activities-label">✦ Activities</p>
                                    @php
                                        $activities = preg_split('/\[\s*[x]?\s*\]/', $activitiesText, -1, PREG_SPLIT_NO_EMPTY);
                                        preg_match_all('/\[(\s*x?\s*)\]/', $activitiesText, $checkmarks);
                                    @endphp
                                    @foreach($activities as $actIndex => $activity)
                                        @php
                                            $activity = trim($activity);
                                            $isChecked = isset($checkmarks[1][$actIndex]) && str_contains($checkmarks[1][$actIndex], 'x');
                                        @endphp
                                        @if($activity)
                                            <div class="activity-item {{ $isChecked ? 'checked' : '' }}">{{ $activity }}</div>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @else
                            {{-- Single block, try to split OBJECTIVE / ACTIVITIES --}}
                            @php
                                $sections = preg_split('/(ACTIVITIES:\s*)/', $description, 2, PREG_SPLIT_DELIM_CAPTURE);
                                $objectiveText = trim($sections[0] ?? '');
                                $activitiesText = isset($sections[2]) ? trim($sections[2]) : '';
                                $objectiveText = preg_replace('/^OBJECTIVE:\s*/i', '', $objectiveText);
                            @endphp

                            @if($objectiveText)
                                <p class="objective-label">⊕ Objective</p>
                                <p class="objective-text">{{ $objectiveText }}</p>
                            @endif

                            @if($activitiesText)
                                <p class="activities-label">✦ Activities</p>
                                @php
                                    $activities = preg_split('/\[\s*[x]?\s*\]/', $activitiesText, -1, PREG_SPLIT_NO_EMPTY);
                                    preg_match_all('/\[(\s*x?\s*)\]/', $activitiesText, $checkmarks);
                                @endphp
                                @foreach($activities as $actIndex => $activity)
                                    @php
                                        $activity = trim($activity);
                                        $isChecked = isset($checkmarks[1][$actIndex]) && str_contains($checkmarks[1][$actIndex], 'x');
                                    @endphp
                                    @if($activity)
                                        <div class="activity-item {{ $isChecked ? 'checked' : '' }}">{{ $activity }}</div>
                                    @endif
                                @endforeach
                            @elseif(!$activitiesText && $objectiveText === '')
                                <p class="objective-text">{{ $description }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p style="color: #9CA0AF; font-style: italic; padding: 10px 0;">No IEP goals were recorded for this student.</p>
        @endif

        {{-- ===== ATTACHED DOCUMENTS & FILES ===== --}}
        <div class="section-title">Attached Student Documents & Medical Records</div>

        @if($documents->count() > 0)
            <table class="info-table" style="margin-bottom: 25px;">
                <thead>
                    <tr style="background-color: #F4F6FA;">
                        <td style="font-weight: 700; font-size: 9px; text-transform: uppercase; width: 6%;">#</td>
                        <td style="font-weight: 700; font-size: 9px; text-transform: uppercase; width: 44%;">Document Title / File Name</td>
                        <td style="font-weight: 700; font-size: 9px; text-transform: uppercase; width: 15%;">File Size</td>
                        <td style="font-weight: 700; font-size: 9px; text-transform: uppercase; width: 35%;">Secure Access Link</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $docIdx => $doc)
                        <tr>
                            <td style="font-size: 10px; text-align: center;">{{ $docIdx + 1 }}</td>
                            <td style="font-size: 10px; font-weight: 600; color: #1E2657;">{{ $doc->original_name }}</td>
                            <td style="font-size: 10px; color: #6B7086;">{{ $doc->file_size ?? 'N/A' }}</td>
                            <td style="font-size: 10px;">
                                <a href="{{ url(route('secure.student-document', $doc->id)) }}" target="_blank" style="color: #3B82F6; text-decoration: underline; font-weight: 700;">
                                    View / Download Document
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #9CA0AF; font-style: italic; padding: 8px 0; margin-bottom: 20px;">No uploaded medical or educational documents on file for this student.</p>
        @endif

        {{-- ===== ACADEMIC & DEVELOPMENTAL SUMMARIES ===== --}}
        <div class="section-title">Academic & Developmental Progress Summaries</div>

        @if($formalReports->count() > 0)
            @foreach($formalReports as $repIdx => $report)
                @php
                    $typeTitle = match($report->report_type) {
                        'q1'        => 'Quarter 1 Progress Summary',
                        'q2'        => 'Quarter 2 Progress Summary',
                        'q3'        => 'Quarter 3 Progress Summary',
                        'q4'        => 'Quarter 4 Progress Summary',
                        'mid_year'  => 'Mid-Year Comprehensive Summary',
                        'year_end'  => 'Annual (Year-End) Comprehensive Summary',
                        default     => strtoupper(str_replace('_', ' ', $report->report_type)) . ' Summary',
                    };
                    $typeColor = match($report->report_type) {
                        'q1'        => '#3B82F6',
                        'q2'        => '#10B981',
                        'q3'        => '#8B5CF6',
                        'q4'        => '#F59E0B',
                        'mid_year'  => '#EC4899',
                        'year_end'  => '#1E2657',
                        default     => '#6B7086',
                    };
                @endphp
                <div class="goal-card" style="margin-bottom: 18px; page-break-inside: avoid;">
                    <div class="goal-card-header" style="border-left: 4px solid {{ $typeColor }};">
                        <span style="display: inline-block; background-color: {{ $typeColor }}; color: #FFFFFF; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: 700; text-transform: uppercase; margin-right: 8px;">
                            {{ $typeTitle }}
                        </span>
                        <span class="goal-meta">
                            <span class="progress-text" style="margin-right: 0;">
                                Date: {{ $report->report_date ? \Carbon\Carbon::parse($report->report_date)->format('F j, Y') : ($report->created_at ? $report->created_at->format('F j, Y') : 'N/A') }}
                            </span>
                        </span>
                    </div>
                    <div class="goal-card-body">
                        <div style="font-size: 10px; color: #6B7086; margin-bottom: 8px;">
                            Authoring Teacher: <strong>{{ $report->teacher->name ?? ($student->teacher->name ?? 'Assigned SPED Teacher') }}</strong>
                            @if($report->approved_at)
                                &nbsp;|&nbsp; Approved on: <strong>{{ \Carbon\Carbon::parse($report->approved_at)->format('M j, Y') }}</strong>
                            @endif
                        </div>
                        <div style="font-size: 11px; color: #1E2657; line-height: 1.6; white-space: pre-line;">
                            {!! nl2br(e($report->content)) !!}
                        </div>
                        @if($report->image_path)
                            <div style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #E2E4EC;">
                                <span style="font-size: 9px; font-weight: 700; color: #6B7086; text-transform: uppercase;">Attached Progress Photo / Artifact:</span><br>
                                @php
                                    $fullImagePath = public_path('storage/' . $report->image_path);
                                    if (!file_exists($fullImagePath)) {
                                        $fullImagePath = storage_path('app/public/' . $report->image_path);
                                    }
                                @endphp
                                @if(file_exists($fullImagePath))
                                    <img src="{{ $fullImagePath }}" style="max-width: 250px; max-height: 180px; margin-top: 6px; border-radius: 4px; border: 1px solid #E2E4EC;" alt="Report Artifact">
                                @else
                                    <a href="{{ asset('storage/' . $report->image_path) }}" target="_blank" style="font-size: 10px; color: #3B82F6; text-decoration: underline;">View Attached Artifact File</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p style="color: #9CA0AF; font-style: italic; padding: 10px 0; margin-bottom: 20px;">No formal progress summaries (Quarterly, Mid-Year, or Annual) were filed for this school year.</p>
        @endif

        {{-- ===== OFFICIAL DIGITAL AUDIT STAMP SECTION ===== --}}
        <div style="margin-top: 30px; width: 100%; page-break-inside: avoid;">
            <div style="border: 2px solid #1E2657; border-radius: 10px; padding: 14px 18px; background-color: #F8FAFC; max-width: 480px; margin: 0 auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 40px; vertical-align: middle;">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #10B981; color: #ffffff; text-align: center; line-height: 30px; font-weight: bold; font-size: 15px;">✓</div>
                        </td>
                        <td style="vertical-align: middle;">
                            <div style="font-size: 8pt; font-weight: bold; color: #C4922E; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">OFFICIAL DIGITAL AUDIT STAMP</div>
                            <div style="font-size: 10pt; font-weight: bold; color: #1E2657;">VERIFIED & ARCHIVED BY DREAM ACHIEVERS LEARNING CENTER</div>
                            <div style="font-size: 8pt; color: #475569; margin-top: 3px; line-height: 1.4;">
                                Archived Teacher: <strong>{{ $student->teacher->name ?? 'System Administrator' }}</strong> (Special Education Teacher)<br>
                                Audit Timestamp: <strong>{{ now()->format('F d, Y \a\t h:i:s A') }}</strong><br>
                                Document Security Hash: <span style="font-family: 'Courier', monospace; font-weight: bold; color: #1E2657;">DALC-ARC-{{ strtoupper(substr(md5($student->id . $student->created_at), 0, 10)) }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="clear: both;"></div>

    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <div style="font-weight: 800; color: #1E2657; font-size: 9.5px; margin-bottom: 3px;">
            CONFIDENTIAL SPECIAL EDUCATION MASTER ARCHIVE &bull; PROTECTED UNDER RA 10173 (DATA PRIVACY ACT OF 2012)
        </div>
        <div style="color: #64748b; font-size: 8.5px; line-height: 1.4; max-width: 90%; margin: 0 auto 4px auto;">
            This master document is an official system-generated institutional snapshot of student records, evaluations, and clinical baselines. Unauthorized duplication, scanning, or external publication is strictly prohibited by law.
        </div>
        <div style="color: #94a3b8; font-size: 8px;">
            <strong>Dream Achievers Learning Center PH</strong> &nbsp;|&nbsp;
            Generated on {{ now()->format('F j, Y \a\t g:i A') }} &nbsp;|&nbsp;
            2 Masunurin St., Brgy. Sikatuna, Quezon City
        </div>
    </div>

</body>
</html>