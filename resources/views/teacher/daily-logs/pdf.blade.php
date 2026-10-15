<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Standardized Formal Documentation - {{ $log->student->first_name }} {{ $log->student->last_name }}</title>
    <style>
        /* Strict DomPDF-compatible simple text document styles */
        @page {
            margin: 35px 45px 35px 45px;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* School Letterhead */
        .school-header {
            text-align: center;
            margin-bottom: 12px;
        }
        .logo {
            width: 75px;
            height: auto;
            margin-bottom: 4px;
        }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.5px;
            color: #000;
        }
        .school-sub {
            font-size: 9.5pt;
            color: #444;
            margin: 1px 0;
        }
        hr.divider {
            border: 0;
            border-top: 1.5px solid #222;
            margin: 12px 0 16px 0;
        }

        /* Document Title */
        .doc-title-container {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #000;
        }
        .doc-subtitle {
            font-size: 10pt;
            color: #555;
            margin-top: 2px;
        }

        /* Metadata Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10.5pt;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 22%;
            color: #222;
        }
        .value {
            width: 28%;
            color: #111;
        }

        /* Section Block */
        .section-box {
            margin-top: 14px;
            margin-bottom: 18px;
        }
        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
            margin-bottom: 10px;
            color: #111;
        }
        .section-content {
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
            color: #111;
            margin: 0;
            padding: 2px 4px;
        }

        /* Signature block */
        .signature-section {
            margin-top: 45px;
            page-break-inside: avoid;
            width: 100%;
        }
        .sig-box {
            width: 260px;
            float: right;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-top: 45px;
            margin-bottom: 4px;
        }
        .sig-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
        }
        .sig-title {
            font-size: 9.5pt;
            color: #444;
        }

        /* Footer */
        .doc-footer {
            margin-top: 60px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 8.5pt;
            color: #666;
            text-align: center;
            clear: both;
        }
    </style>
</head>
<body>

    <!-- School Letterhead -->
    <div class="school-header">
        @if(file_exists(public_path('image/logo.png')))
            <img src="{{ public_path('image/logo.png') }}" class="logo" alt="School Logo"><br>
        @endif
        <div class="school-name">DREAM ACHIEVERS LEARNING CENTER PHILIPPINES</div>
        <div class="school-sub">2 Masunurin St. Brgy. Sikatuna Quezon City</div>
        <div class="school-sub">Contact: 09369523782 | Email: Dreamachieverslearningphil@gmail.com</div>
    </div>

    <hr class="divider">

    <!-- Document Title -->
    <div class="doc-title-container">
        <h1 class="doc-title">Standardized Formal Documentation</h1>
        <div class="doc-subtitle">Daily Student Observation & Learning Log</div>
    </div>

    <!-- Student & Record Information -->
    <table class="info-table">
        <tr>
            <td class="label">Student Name:</td>
            <td class="value font-bold">{{ $log->student->last_name }}, {{ $log->student->first_name }}</td>
            <td class="label">Observation Date:</td>
            <td class="value">{{ \Carbon\Carbon::parse($log->log_date)->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Academic Quarter:</td>
            <td class="value uppercase">{{ $log->quarter }}</td>
            <td class="label">Class:</td>
            <td class="value">{{ $log->student->class_name ?? 'SPED' }}</td>
        </tr>
        <tr>
            <td class="label">Prepared By:</td>
            <td class="value">{{ $log->teacher->name ?? 'Special Education Teacher' }}</td>
            <td class="label">Date Generated:</td>
            <td class="value">{{ now()->format('F d, Y') }}</td>
        </tr>
    </table>

    <!-- Documentation Notes -->
    <div class="section-box">
        <div class="section-heading">Standardized Observation & Progress Notes</div>
        <div class="section-content">
            {!! nl2br(e($log->notes)) !!}
        </div>
    </div>

    <!-- Home Recommendations if present -->
    @if(!empty($log->home_recommendations))
        <div class="section-box">
            <div class="section-heading">Suggested Home Activities & Follow-Up</div>
            <div class="section-content">
                {!! nl2br(e($log->home_recommendations)) !!}
            </div>
        </div>
    @endif

    <!-- Signature Block -->
    <div class="signature-section">
        <div class="sig-box">
            <div style="font-size: 10pt; text-align: left; margin-bottom: 25px;">Documented by:</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $log->teacher->name ?? 'Teacher' }}</div>
            <div class="sig-title">Special Education Teacher</div>
            <div class="sig-title">Dream Achievers Learning Center</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Footer Notice -->
    <div class="doc-footer">
        Confidential Document &bull; Dream Achievers Learning Center &bull; Generated on {{ now()->format('M d, Y h:i A') }}
    </div>

</body>
</html>
