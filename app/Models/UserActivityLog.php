<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_username',
        'user_role',
        'action',
        'action_type',
        'details',
        'target_type',
        'target_id',
        'student_name',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Map system roles to human-readable labels.
     */
    public static function formatRole(?string $role): string
    {
        return match (strtolower((string) $role)) {
            'admin' => 'Admin',
            'teacher' => 'SPED Teacher',
            'guardian' => 'Guardian',
            default => filled($role) ? ucfirst($role) : 'System',
        };
    }

    /**
     * Static helper to safely record an audit log event.
     */
    public static function record(
        string $action,
        string $actionType,
        ?string $details = null,
        ?string $targetType = null,
        $targetId = null,
        ?string $studentName = null,
        ?User $user = null,
        ?Request $request = null
    ): ?self {
        try {
            $user = $user ?? Auth::user();
            $request = $request ?? (request() instanceof Request ? request() : null);

            $userName = $user ? $user->name : 'System';
            $userUsername = $user ? $user->username : null;
            $userRole = $user ? self::formatRole($user->role) : 'System';

            $ip = $request ? $request->ip() : null;
            $userAgent = $request ? substr((string) $request->userAgent(), 0, 500) : null;

            return self::create([
                'user_id'       => $user?->id,
                'user_name'     => $userName,
                'user_username' => $userUsername,
                'user_role'     => $userRole,
                'action'        => $action,
                'action_type'   => $actionType,
                'details'       => $details,
                'target_type'   => $targetType,
                'target_id'     => $targetId !== null ? (string) $targetId : null,
                'student_name'  => $studentName,
                'ip_address'    => $ip,
                'user_agent'    => $userAgent,
            ]);
        } catch (\Throwable $e) {
            // Audit logging must NEVER crash the user request
            Log::warning('Failed to record UserActivityLog: ' . $e->getMessage(), [
                'action' => $action,
                'actionType' => $actionType,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Scope for multi-parameter search & filtering.
     */
    public function scopeFilter($query, array $filters)
    {
        // 1. Global Keyword Search
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_username', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('target_id', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Student Name
        if (!empty($filters['student_name'])) {
            $student = trim($filters['student_name']);
            $query->where('student_name', 'like', "%{$student}%");
        }

        // 3. Filter by User Role
        if (!empty($filters['role']) && $filters['role'] !== 'all') {
            $role = $filters['role'];
            $query->where(function ($q) use ($role) {
                $q->where('user_role', $role)
                  ->orWhere('user_role', self::formatRole($role));
            });
        }

        // 4. Filter by Action Type
        if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
            $query->where('action_type', $filters['action_type']);
        }

        // 5. Filter by Date Range
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query;
    }
}

