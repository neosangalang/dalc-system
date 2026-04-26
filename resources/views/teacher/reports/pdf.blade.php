<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; font-size: 14px; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #4F6AF5; padding-bottom: 20px; margin-bottom: 30px; }
        .school-name { font-size: 24px; font-weight: bold; color: #1E2657; margin: 0; }
        .subtitle { color: #7B8DB7; font-size: 14px; margin-top: 5px; }
        
        .student-info { width: 100%; border-collapse: collapse; margin-bottom: 30px; background-color: #f8f9fa; }
        .student-info td { padding: 10px; border: 1px solid #ddd; }
        .label { font-weight: bold; width: 120px; color: #1E2657; }
        
        /* THE FIX: Removed pre-wrap and changed to left-align */
        .report-content { 
            font-size: 13px; 
            text-align: left; 
            line-height: 1.8;
            color: #1a1a1a;
        }
        
        .footer { margin-top: 50px; font-size: 12px; text-align: center; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature-line { margin-top: 60px; width: 250px; border-top: 1px solid #000; text-align: center; font-weight: bold; padding-top: 5px; float: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="school-name">Dream Achievers Learning Center</h1>
        <div class="subtitle">Official Student Progress Report</div>
    </div>

    <table class="student-info">
        <tr>
            <td class="label">Student Name:</td>
            <td>{{ $report->student->last_name }}, {{ $report->student->first_name }}</td>
            <td class="label">Date Generated:</td>
            <td>{{ \Carbon\Carbon::parse($report->created_at)->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Prepared By:</td>
            <td>{{ $report->teacher->name }}</td>
            <td class="label">Report Type:</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $report->report_type) }}</td>
        </tr>
    </table>

    <div class="report-content">
        {!! nl2br(e($report->content)) !!}
    </div>

    <div class="signature-line">
        {{ $report->teacher->name }}<br>
        <span style="font-weight: normal; font-size: 12px; color: #666;">Special Education Teacher</span>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        This document is confidential and intended solely for the parents/guardians of the named student.
    </div>

</body>
</html>