<!DOCTYPE html>
<html>
<head>
    <title>IEP - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #000; }
        .text-center { text-align: center; }
        .header { margin-bottom: 25px; }
        .header h3 { margin: 0 0 5px 0; font-size: 18px; }
        .header h4 { margin: 5px 0; font-size: 15px; text-decoration: underline; }
        .header p { margin: 2px 0; font-size: 11px; }
        
        .student-info { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .student-info td { padding: 6px 0; }
        .student-info .label { width: 12%; font-weight: bold; }
        .student-info .value { width: 38%; border-bottom: 1px solid #000; font-weight: bold; }
        
        .domain-box { 
            border: 2px solid #000; 
            background-color: #f8f9fa; 
            padding: 8px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 25px;
            font-size: 14px;
        }
        
        .goal-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .goal-table th, .goal-table td { border: 2px solid #000; padding: 12px; vertical-align: top; }
        .goal-table th { text-align: left; font-size: 13px; background-color: #fff; }
        
        /* NEW: Signature Section Styling */
        .signature-section { margin-top: 60px; width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        .signature-line { border-bottom: 1px solid #000; height: 30px; margin-bottom: 5px; }
        .signature-name { font-weight: bold; font-size: 13px; }
        .signature-title { font-size: 11px; }
    </style>
</head>
<body>

    <div class="text-center header">
        <h3>Dream Achievers Learning Center Philippines</h3>
        <p>2 Masunurin St. Brgy. Sikatuna Quezon, City</p>
        <p>09369523782</p>
        <p>Dreamachieverslearningphil@gmail.com</p>
        
        <hr style="border: 1px solid #000; margin: 15px 0;">
        
        <h4>INDIVIDUALIZED EDUCATIONAL PROGRAM</h4>
        <h4>S.Y. {{ date('Y') }}-{{ date('Y')+1 }}</h4>
    </div>

    <table class="student-info">
        <tr>
            <td class="label">Child's Name:</td>
            <td class="value">{{ $student->last_name }}, {{ $student->first_name }}</td>
            <td class="label" style="padding-left: 20px;">Class:</td>
            <td class="value">SPED</td>
        </tr>
        <tr>
            <td class="label">Age:</td>
            <td class="value">{{ \Carbon\Carbon::parse($student->date_of_birth)->age ?? 'N/A' }} yrs old</td>
            <td class="label" style="padding-left: 20px;">DOB:</td>
            <td class="value">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Parents:</td>
            <td colspan="3" class="value">{{ $student->guardian->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @forelse($student->iepGoals as $goal)
        <div class="domain-box">
            DOMAIN : {{ str_replace('-', ' - ', strtoupper($goal->domain)) }}
        </div>

        <table class="goal-table">
            <thead>
                <tr>
                    <th style="width: 50%;">OBJECTIVES</th>
                    <th style="width: 50%;">ACTIVITIES</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                @endphp

                @foreach($subGoals as $subGoalText)
                    @php
                        $subGoalText = trim($subGoalText);
                        if (empty($subGoalText)) continue;

                        $objective = $subGoalText;
                        $activities = '';

                        if (preg_match('/ACTIVITIES:/i', $subGoalText)) {
                            $parts = preg_split('/ACTIVITIES:/i', $subGoalText);
                            $objective = preg_replace('/OBJECTIVE:/i', '', $parts[0]);
                            $activities = $parts[1];
                        }

                        $objective = trim($objective);
                        $activities = trim($activities);

                        $objective = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $objective);
                        $activities = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $activities);

                        $activities = str_replace(['[ ]', '[x]', '[X]', '[]'], '•', $activities);
                    @endphp

                    @if(!empty($objective))
                    <tr>
                        <td>{!! nl2br(e($objective)) !!}</td>
                        <td>{!! nl2br(e($activities)) !!}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        
    @empty
        <div style="text-align: center; margin-top: 50px; font-style: italic;">
            No IEP Goals have been assigned to this student yet.
        </div>
    @endforelse

    <table class="signature-section">
        <tr>
            <td style="width: 40%; text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $student->teacher->name ?? '_________________________' }}</div>
                <div class="signature-title">SPED Teacher</div>
            </td>
            
            <td style="width: 20%;"></td> <td style="width: 40%; text-align: center;">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $student->guardian->name ?? '_________________________' }}</div>
                <div class="signature-title">Parent / Guardian</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; padding-top: 40px;">
                <div style="width: 40%; margin: 0 auto;">
                    <div class="signature-line"></div>
                    <div class="signature-name">_________________________</div>
                    <div class="signature-title">School Administrator / SPED Coordinator</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>