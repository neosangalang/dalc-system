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
            <td class="label">Report Type:</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $report->report_type) }}</td>
        </tr>
    </table>

    <div class="report-content">
        {!! nl2br(e($report->content)) !!}
    </div>

    <!-- SIGNATURE & DIGITAL AUDIT STAMP SECTION -->
    <div style="margin-top: 40px; width: 100%;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- DIGITAL AUDIT STAMP SEAL (LEFT) -->
                <td style="width: 55%; vertical-align: bottom;">
                    <div style="border: 1.5px dashed #1E2657; border-radius: 8px; padding: 10px 14px; background-color: #F8FAFC; max-width: 320px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 35px; vertical-align: middle;">
                                    <div style="width: 26px; height: 26px; border-radius: 50%; background-color: #10B981; color: #ffffff; text-align: center; line-height: 26px; font-weight: bold; font-size: 13px;">✓</div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="font-size: 9px; font-weight: bold; color: #C4922E; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px;">OFFICIAL DIGITAL AUDIT STAMP</div>
                                    <div style="font-size: 10px; font-weight: bold; color: #1E2657;">VERIFIED BY DALC SYSTEM</div>
                                    <div style="font-size: 8px; color: #64748B; margin-top: 2px; line-height: 1.3;">
                                        Audit Date: <strong>{{ \Carbon\Carbon::parse($report->updated_at ?? $report->created_at)->format('Y-m-d H:i:s') }}</strong><br>
                                        Doc Hash: <span style="font-family: monospace; font-weight: bold; color: #1E2657;">DALC-AUDIT-{{ strtoupper(substr(md5($report->id . $report->created_at), 0, 10)) }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                
                <!-- TEACHER E-SIGNATURE (RIGHT) -->
                <td style="width: 45%; vertical-align: bottom; text-align: center;">
                    @if($report->teacher && $report->teacher->signature_image && file_exists(public_path('storage/' . $report->teacher->signature_image)))
                        <img src="{{ public_path('storage/' . $report->teacher->signature_image) }}" style="max-height: 48px; max-width: 180px; margin-bottom: -12px;">
                    @else
                        <!-- Stylish Cursive E-Signature -->
                        <div style="font-family: 'Georgia', cursive; font-size: 18px; font-style: italic; color: #1E2657; margin-bottom: -6px; font-weight: bold; letter-spacing: 1px;">
                            {{ $report->teacher->name }}
                        </div>
                    @endif
                    <div style="border-top: 1px solid #1E2657; padding-top: 4px; font-weight: bold; font-size: 12px; color: #1E2657;">
                        {{ $report->teacher->name }}
                    </div>
                    <div style="font-weight: normal; font-size: 10px; color: #64748B;">
                        Special Education Teacher
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        This document is confidential and intended solely for the parents/guardians of the named student.
    </div>

</body>
</html>