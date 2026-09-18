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
                <span class="stat-number">{{ $student->dailyLogs->count() }}</span>
                <span class="stat-label"> Daily Logs</span>
            </span>
            <span class="stat">
                <span class="stat-number">{{ $student->dailyLogs->pluck('quarter')->unique()->count() }}</span>
                <span class="stat-label"> Quarters Covered</span>
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

        {{-- ===== DAILY LOGS ===== --}}
        <div class="section-title">Daily Activity Logs</div>

        @if($student->dailyLogs->count() > 0)
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Quarter</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->dailyLogs->sortByDesc('log_date') as $index => $log)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($log->log_date)->format('M j, Y') }}</td>
                            <td>{{ $log->quarter }}</td>
                            <td>{{ $log->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ===== SIGNATURE & DIGITAL AUDIT STAMP SECTION ===== --}}
        <div style="margin-top: 30px; width: 100%;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <!-- DIGITAL AUDIT STAMP SEAL (LEFT) -->
                    <td style="width: 55%; vertical-align: bottom;">
                        <div style="border: 1.5px dashed #1E2657; border-radius: 6px; padding: 10px 12px; background-color: #F8FAFC; max-width: 310px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 32px; vertical-align: middle;">
                                        <div style="width: 24px; height: 24px; border-radius: 50%; background-color: #10B981; color: #ffffff; text-align: center; line-height: 24px; font-weight: bold; font-size: 12px;">✓</div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div style="font-size: 8px; font-weight: bold; color: #C4922E; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px;">OFFICIAL DIGITAL AUDIT STAMP</div>
                                        <div style="font-size: 9px; font-weight: bold; color: #1E2657;">VERIFIED & ARCHIVED BY DALC SYSTEM</div>
                                        <div style="font-size: 8px; color: #64748B; margin-top: 2px; line-height: 1.3;">
                                            Audit Date: <strong>{{ now()->format('Y-m-d H:i:s') }}</strong><br>
                                            Doc Hash: <span style="font-family: monospace; font-weight: bold; color: #1E2657;">DALC-ARC-{{ strtoupper(substr(md5($student->id . $student->created_at), 0, 10)) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    
                    <!-- TEACHER E-SIGNATURE (RIGHT) -->
                    <td style="width: 45%; vertical-align: bottom; text-align: center;">
                        @if($student->teacher && $student->teacher->signature_image && file_exists(public_path('storage/' . $student->teacher->signature_image)))
                            <img src="{{ public_path('storage/' . $student->teacher->signature_image) }}" style="max-height: 48px; max-width: 180px; margin-bottom: -12px;">
                        @else
                            <div style="font-family: 'Georgia', cursive; font-size: 16px; font-style: italic; color: #1E2657; font-weight: bold; margin-bottom: -6px;">
                                {{ $student->teacher->name ?? 'System Administrator' }}
                            </div>
                        @endif
                        <div style="border-top: 1px solid #1E2657; padding-top: 4px; font-weight: bold; font-size: 11px; color: #1E2657;">
                            {{ $student->teacher->name ?? 'System Administrator' }}
                        </div>
                        <div style="font-size: 10px; color: #64748B;">
                            Special Education Teacher
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        <strong>Dream Achievers Learning Center PH</strong> &nbsp;|&nbsp;
        Student Master Archive &nbsp;|&nbsp;
        Generated on {{ now()->format('F j, Y \a\t g:i A') }} &nbsp;|&nbsp;
        This document is system-generated and serves as an official archived record.
    </div>

</body>
</html>