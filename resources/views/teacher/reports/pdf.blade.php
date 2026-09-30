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
        
        .footer { margin-top: 40px; font-size: 11px; text-align: center; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        
        /* AUTOMATED DIGITAL SIGNATURE STAMP */
        .digital-sig-container {
            margin-top: 45px;
            width: 320px;
            float: right;
            border: 2px solid #1E2657;
            border-radius: 8px;
            padding: 12px 15px;
            background-color: #F8FAFC;
        }
        .digital-sig-header {
            font-size: 9px;
            font-weight: bold;
            color: #C4922E;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 4px;
        }
        .digital-sig-name {
            font-size: 14px;
            font-weight: bold;
            color: #1E2657;
            margin-top: 4px;
        }
        .digital-sig-role {
            font-size: 11px;
            color: #475569;
            margin-bottom: 8px;
        }
        .digital-sig-badge {
            display: inline-block;
            background-color: #10B981;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .digital-sig-meta {
            font-size: 9px;
            color: #64748B;
            margin-top: 6px;
            line-height: 1.3;
        }
        .ref-code {
            font-family: 'Courier', monospace;
            font-weight: bold;
            color: #1E2657;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="school-name">Dream Achievers Learning Center PH</h1>
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
@php
    $reportTypeMap = [
        'daily' => 'Daily Report',
        'q1' => '1st Quarter Report',
        'q2' => '2nd Quarter Report',
        'q3' => '3rd Quarter Report',
        'q4' => '4th Quarter Report',
        'mid_year' => 'Mid-Year Report',
        'year_end' => 'Year-End Report',
    ];
@endphp
            <td class="label">Report Type:</td>
            <td style="text-transform: capitalize;">{{ $reportTypeMap[$report->report_type] ?? str_replace('_', ' ', $report->report_type) }}</td>
        </tr>
    </table>

    <div class="report-content">
        {!! nl2br(e($report->content)) !!}
    </div>

    <!-- OFFICIAL DIGITAL AUDIT STAMP SECTION -->
    <div style="margin-top: 40px; width: 100%; page-break-inside: avoid;">
        <div style="border: 2px solid #1E2657; border-radius: 10px; padding: 14px 18px; background-color: #F8FAFC; max-width: 480px; margin: 0 auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 45px; vertical-align: middle;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #10B981; color: #ffffff; text-align: center; line-height: 32px; font-weight: bold; font-size: 16px;">✓</div>
                    </td>
                    <td style="vertical-align: middle;">
                        <div style="font-size: 9px; font-weight: bold; color: #C4922E; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px;">OFFICIAL DIGITAL AUDIT STAMP</div>
                        <div style="font-size: 11px; font-weight: bold; color: #1E2657;">
                            @if($report->status === 'approved')
                                APPROVED & AUDITED BY ADMIN: {{ $report->approver->name ?? 'Administrator' }}
                            @else
                                DIGITALLY VERIFIED BY DALC SYSTEM
                            @endif
                        </div>
                        <div style="font-size: 9px; color: #475569; margin-top: 3px; line-height: 1.4;">
                            Prepared By: <strong>{{ $report->teacher->name ?? 'Special Education Teacher' }}</strong> (Special Education Teacher)<br>
                            Audit Timestamp: <strong>{{ \Carbon\Carbon::parse($report->approved_at ?? ($report->updated_at ?? $report->created_at))->format('F d, Y \a\t h:i:s A') }}</strong><br>
                            Document Security Hash: <span style="font-family: 'Courier', monospace; font-weight: bold; color: #1E2657;">DALC-AUDIT-{{ strtoupper(substr(md5($report->id . ($report->approved_at ?? $report->created_at)), 0, 10)) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        This document is confidential and intended solely for the parents/guardians of the named student.
    </div>

</body>
</html>