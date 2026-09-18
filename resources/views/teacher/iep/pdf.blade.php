<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IEP - {{ $student->first_name }} {{ $student->last_name }}</title>
    
    <style>
        /* Strict CSS for DomPDF Formatting */
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 10px 20px;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        /* Header Styling */
        .logo { width: 100px; height: auto; margin-bottom: 5px; }
        .header-title { font-size: 14pt; margin: 0 0 5px 0; font-weight: bold; }
        .header-contact { font-size: 11pt; margin: 2px 0; }
        
        hr.divider { border: 0; border-top: 1px solid #000; margin: 15px 0; }
        
        .document-title { font-size: 12pt; margin: 15px 0; font-weight: bold; }
        
        /* Information Table */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        
        /* Goals & Activities Table */
        .domain-title { margin-top: 20px; margin-bottom: 10px; font-weight: bold; }
        .plop-section { margin-bottom: 15px; text-align: justify; }
        
        .goals-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            margin-bottom: 30px; 
        }
        .goals-table th, .goals-table td { 
            border: 1px solid #000; 
            padding: 10px; 
            vertical-align: top; 
        }
        .goals-table th { 
            text-align: center; 
            font-weight: bold; 
            padding: 12px 10px;
            background-color: #fcfcfc;
        }
        .goals-table td {
            text-align: justify;
        }
        
        /* Footer & Signatures */
        .footer-section { margin-top: 30px; page-break-inside: avoid; }
        .footer-list { margin-top: 5px; margin-bottom: 20px; }
        
        .signature-table { width: 100%; margin-top: 60px; border-collapse: collapse; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: bottom; }
    </style>
</head>
<body>

    <!-- HEADER SECTION & LOGO -->
    <div class="text-center">
        <!-- FIXED: Path now points to the 'image' subfolder! -->
        @if(file_exists(public_path('image/logo.png')))
            <img src="{{ public_path('image/logo.png') }}" class="logo" alt="Logo">
        @endif
        <div class="header-title">Dream Achievers Learning Center Philippines</div>
        <div class="header-contact">2 Masunurin St. Brgy. Sikatuna Quezon, City</div>
        <div class="header-contact">09369523782</div>
        <div class="header-contact">Dreamachieverslearningphil@gmail.com</div>
    </div>

    <!-- The horizontal line matching the Word File -->
    <hr class="divider">

    <!-- DOCUMENT TITLE -->
    <div class="text-center document-title uppercase">
        INDIVIDUALIZED EDUCATIONAL PROGRAM<br><br>
        S.Y. {{ date('Y') }}-{{ date('Y')+1 }}
    </div>

    <!-- STUDENT INFO TABLE -->
    <table class="info-table">
        <tr>
            <td style="width: 15%;">Child’s Name:</td>
            <td style="width: 45%;">{{ $student->last_name }}, {{ $student->first_name }}</td>
            <td style="width: 12%;">Class:</td>
            <td style="width: 28%;">SPED</td>
        </tr>
        <tr>
            <td>Age:</td>
            <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->age ?? '0' }} yrs old</td>
            <td>Date Of Birth:</td>
            <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td>Parents:</td>
            <td colspan="3">{{ $student->guardian->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- DYNAMIC DOMAINS & GOALS -->
    @forelse($student->iepGoals as $goal)
        <div class="domain-title uppercase">DOMAIN : {{ str_replace('-', ' - ', $goal->domain) }}</div>
        
        @if(!empty($goal->plop))
            <div class="plop-section">
                <span class="font-bold">Present Level Of Performance:</span><br>
                {!! nl2br(e($goal->plop)) !!}
            </div>
        @endif
        
        <div class="font-bold" style="margin-bottom: 10px;">Priorities for school year {{ date('Y') }}-{{ date('Y')+1 }}</div>

        <!-- THE BORDERED TABLE -->
        <table class="goals-table">
            <thead>
                <tr>
                    <th style="width: 45%;">OBJECTIVES</th>
                    <th style="width: 55%;">ACTIVITIES</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Splits the text accurately by the dashed lines (---)
                    $subGoals = preg_split('/-{3,}/', $goal->goal_description);
                @endphp

                @foreach($subGoals as $subGoalText)
                    @php
                        $subGoalText = trim($subGoalText);
                        if (empty($subGoalText)) continue;

                        $objective = $subGoalText;
                        $activities = '';

                        // Extracts the Objective and Activities sections perfectly
                        if (preg_match('/ACTIVITIES:/i', $subGoalText)) {
                            $parts = preg_split('/ACTIVITIES:/i', $subGoalText);
                            $objective = preg_replace('/OBJECTIVE:/i', '', $parts[0]);
                            $activities = $parts[1];
                        } else {
                            $objective = preg_replace('/OBJECTIVE:/i', '', $objective);
                        }

                        // Clean up text and convert brackets into solid bullet points
                        $objective = trim($objective);
                        $activities = trim($activities);
                        $activities = str_replace(['[ ]', '[x]', '[X]', '[]'], '• ', $activities);
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
        <div class="text-center" style="margin-top: 40px; font-style: italic; color: #555;">
            No IEP Goals have been established for this student yet.
        </div>
    @endforelse

    <!-- FOOTER & RECOMMENDATIONS -->
    <div class="footer-section">
        <div class="font-bold">EVALUATION AND MEASUREMENT :</div>
        <div class="footer-list">
            Observation<br>
            Performance Assessment<br>
            Portfolio<br>
            Learning Probe<br>
            Work Samples<br>
            Interview with parents / caregivers
        </div>

        <div class="font-bold" style="margin-top: 15px;">RECOMMENDATIONS :</div>
        <div class="footer-list">
            1. Attend class regularly with daily follow up at home.<br>
            2. Attend Occupational and Speech Therapy.<br>
            3. Modify home environment / routine / schedule as to provide more opportunities for the child to practice learned skills in natural setting.<br>
            4. Let the child interact with his regular peers. Aside from small group class, bring him to the park, restaurants with play areas and church with children’s class for more varied social interaction setting.<br>
            5. Limit television watching or computer gaming up to 1 hour daily as suggested by psychoeducational studies.<br>
            6. Engage the child in physical activities such as regular trips to the playground, swimming, biking, ball games etc. to improve coordination and attention;<br>
            7. READ, TALK and PLAY with your child everyday for optimum development!
        </div>

        <p style="margin-top: 20px;">
            Thank you for entrusting us with your child. If you have questions about this report, please do not hesitate to get in touch with us Mobile number 09369523782, Mondays through Fridays from 8:00am to 5:00 pm.
        </p>
    </div>

    <!-- OFFICIAL DIGITAL AUDIT STAMP SECTION -->
    <div style="margin-top: 30px; margin-bottom: 25px; page-break-inside: avoid;">
        <div style="border: 2px solid #1E2657; border-radius: 10px; padding: 14px 18px; background-color: #F8FAFC;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 40px; vertical-align: middle;">
                        <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #10B981; color: #ffffff; text-align: center; line-height: 30px; font-weight: bold; font-size: 15px;">✓</div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div style="font-size: 8pt; font-weight: bold; color: #C4922E; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">OFFICIAL DIGITAL AUDIT STAMP & VERIFICATION</div>
                        <div style="font-size: 10pt; font-weight: bold; color: #1E2657;">VERIFIED & AUDITED BY DREAM ACHIEVERS LEARNING CENTER</div>
                        <div style="font-size: 8pt; color: #475569; margin-top: 4px; line-height: 1.4;">
                            Special Education Teacher: <strong>{{ $student->teacher->name ?? 'Special Education Teacher' }}</strong> &nbsp;|&nbsp; 
                            SPED Consultant: <strong>Margaret J. Cobarrubias, MA (PRC Lic. # 1464922)</strong><br>
                            Parent / Guardian: <strong>{{ $student->guardian->name ?? 'Parent / Guardian' }}</strong> &nbsp;|&nbsp; 
                            Audit Timestamp: <strong>{{ date('F d, Y \a\t h:i:s A') }}</strong><br>
                            Document Security Hash: <span style="font-family: monospace; font-weight: bold; color: #1E2657;">DALC-AUDIT-IEP-{{ strtoupper(substr(md5($student->id . date('Y-m-d')), 0, 10)) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>