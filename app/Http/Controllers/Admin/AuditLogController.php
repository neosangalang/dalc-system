<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display a listing of user activity audit logs with multi-parameter filtering.
     */
    public function index(Request $request)
    {
        $filters = [
            'search'       => $request->input('search'),
            'student_name' => $request->input('student_name'),
            'role'         => $request->input('role'),
            'action_type'  => $request->input('action_type'),
            'start_date'   => $request->input('start_date'),
            'end_date'     => $request->input('end_date'),
        ];

        $query = UserActivityLog::query()->filter($filters)->latest('created_at');

        // Fast KPI summary metrics (unfiltered/lifetime vs today)
        $totalLogsCount = UserActivityLog::count();
        $todayLogsCount = UserActivityLog::whereDate('created_at', today())->count();
        $authEventsCount = UserActivityLog::whereIn('action_type', ['login', 'logout', 'password_reset'])->count();
        $complianceEventsCount = UserActivityLog::whereIn('action_type', [
            'iep_creation', 
            'document_approval', 
            'document_rejection', 
            'student_archiving', 
            'ai_formatting'
        ])->count();

        // Paginate results with persistent query strings
        $logs = $query->paginate(20)->withQueryString();

        // Distinct action types and roles for filter dropdowns
        $availableActionTypes = [
            'login'               => 'User Login',
            'logout'              => 'User Logout',
            'password_reset'      => 'Password Reset',
            'iep_creation'        => 'IEP Creation',
            'ai_formatting'       => 'Daily Log AI Formatting',
            'document_approval'   => 'Document Approval/Stamping',
            'document_rejection'  => 'Document Revision Requested',
            'student_archiving'   => 'Student Archiving / Rollover',
            'file_download'       => 'File / PDF Download',
            'student_create'      => 'Student Profile Enrolled',
            'student_reenroll'    => 'Student Re-enrolled',
            'student_update'      => 'Student Profile Updated',
            'account_credentials' => 'Account Credentials Changed',
            'account_mfa'         => 'MFA Setting Changed',
        ];

        $availableRoles = [
            'Admin'        => 'Admin',
            'SPED Teacher' => 'SPED Teacher',
            'Guardian'     => 'Guardian',
        ];

        return view('admin.audit-logs.index', compact(
            'logs',
            'filters',
            'totalLogsCount',
            'todayLogsCount',
            'authEventsCount',
            'complianceEventsCount',
            'availableActionTypes',
            'availableRoles'
        ));
    }

    /**
     * Fetch single log details via JSON (for modal inspection).
     */
    public function show($id)
    {
        $log = UserActivityLog::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'log' => [
                'id'            => $log->id,
                'user_name'     => $log->user_name,
                'user_username' => $log->user_username,
                'user_role'     => $log->user_role,
                'action'        => $log->action,
                'action_type'   => $log->action_type,
                'details'       => $log->details,
                'target_type'   => $log->target_type,
                'target_id'     => $log->target_id,
                'student_name'  => $log->student_name,
                'ip_address'    => $log->ip_address,
                'user_agent'    => $log->user_agent,
                'timestamp'     => $log->created_at->format('M d, Y h:i:s A'),
                'time_ago'      => $log->created_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * Export filtered audit logs as CSV for compliance reviews.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = [
            'search'       => $request->input('search'),
            'student_name' => $request->input('student_name'),
            'role'         => $request->input('role'),
            'action_type'  => $request->input('action_type'),
            'start_date'   => $request->input('start_date'),
            'end_date'     => $request->input('end_date'),
        ];

        $fileName = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'User Full Name',
                'System Username',
                'User Role',
                'Action Executed',
                'Action Type',
                'Target Type',
                'Target Record ID',
                'Associated Student',
                'Details / Context',
                'IP Address',
                'Device / User Agent',
            ]);

            UserActivityLog::query()
                ->filter($filters)
                ->latest('created_at')
                ->chunk(250, function ($logs) use ($handle) {
                    foreach ($logs as $log) {
                        fputcsv($handle, [
                            $log->id,
                            $log->created_at->format('Y-m-d H:i:s'),
                            $log->user_name,
                            $log->user_username ?? 'N/A',
                            $log->user_role,
                            $log->action,
                            $log->action_type,
                            $log->target_type ?? 'N/A',
                            $log->target_id ?? 'N/A',
                            $log->student_name ?? 'N/A',
                            $log->details ?? '',
                            $log->ip_address ?? 'N/A',
                            $log->user_agent ?? 'N/A',
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}

