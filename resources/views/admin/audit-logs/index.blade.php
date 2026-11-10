@extends('layouts.app')

@section('title', 'Audit Logs & Activity Trail')

@section('content')

<div class="w-full max-w-[1200px] mx-auto space-y-6 sm:space-y-8" x-data="{
    detailModalOpen: false,
    selectedLog: null,
    viewDetails(log) {
        this.selectedLog = log;
        this.detailModalOpen = true;
    },
    setDateRange(days) {
        const end = new Date();
        const start = new Date();
        if (days > 0) {
            start.setDate(end.getDate() - days);
            document.getElementById('filter_start_date').value = start.toISOString().split('T')[0];
            document.getElementById('filter_end_date').value = end.toISOString().split('T')[0];
        } else if (days === 0) {
            // Today
            const todayStr = end.toISOString().split('T')[0];
            document.getElementById('filter_start_date').value = todayStr;
            document.getElementById('filter_end_date').value = todayStr;
        } else {
            // All time
            document.getElementById('filter_start_date').value = '';
            document.getElementById('filter_end_date').value = '';
        }
        document.getElementById('audit-filter-form').submit();
    }
}">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-[#1E2657]/10 text-[#1E2657] border border-[#1E2657]/20">
                    <i class="fa-solid fa-shield-halved text-[#3B82F6]"></i> Compliance & Security
                </span>
                <span class="text-xs text-[#9CA0AF] font-bold">•</span>
                <span class="text-xs text-[#6B7086] font-bold">Real-Time Audit Trail</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight m-0">
                System Audit Logs
            </h1>
            <p class="text-xs sm:text-sm text-[#6B7086] font-medium mt-1 mb-0">
                Immutable, centralized activity tracking for user actions, security events, and compliance auditing.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-[#1E2657]/20 rounded-xl text-xs sm:text-sm font-extrabold text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-sm transition active:scale-95">
                <i class="fa-solid fa-file-csv text-[#10B981]"></i> Export to CSV
            </a>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F0F3FA] border border-[#1E2657]/20 rounded-xl text-xs sm:text-sm font-extrabold text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-sm transition active:scale-95">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- 2. KPI SUMMARY METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 w-full">
        <!-- Total Logged Events -->
        <div class="bg-white border border-black/15 rounded-2xl p-4 sm:p-5 shadow-sm relative overflow-hidden group hover:border-[#3B82F6]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF]">Total Interactions</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#3B82F6] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-database"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-[#1E2657] m-0">{{ number_format($totalLogsCount) }}</h3>
            <span class="text-[11px] font-semibold text-[#6B7086]">Lifetime audit entries</span>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#3B82F6]/30 group-hover:bg-[#3B82F6] transition-colors"></div>
        </div>

        <!-- Recorded Today -->
        <div class="bg-white border border-black/15 rounded-2xl p-4 sm:p-5 shadow-sm relative overflow-hidden group hover:border-[#10B981]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF]">Activity Today</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-[#10B981] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-[#10B981] m-0">{{ number_format($todayLogsCount) }}</h3>
            <span class="text-[11px] font-semibold text-[#6B7086]">Logged in past 24 hours</span>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#10B981]/30 group-hover:bg-[#10B981] transition-colors"></div>
        </div>

        <!-- Security & Auth -->
        <div class="bg-white border border-black/15 rounded-2xl p-4 sm:p-5 shadow-sm relative overflow-hidden group hover:border-[#8B5CF6]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF]">Auth & Security</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-[#8B5CF6] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-[#8B5CF6] m-0">{{ number_format($authEventsCount) }}</h3>
            <span class="text-[11px] font-semibold text-[#6B7086]">Logins, Logouts, Resets</span>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#8B5CF6]/30 group-hover:bg-[#8B5CF6] transition-colors"></div>
        </div>

        <!-- Compliance & Reports -->
        <div class="bg-white border border-black/15 rounded-2xl p-4 sm:p-5 shadow-sm relative overflow-hidden group hover:border-[#F59E0B]/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF]">Clinical & Compliance</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-[#F59E0B] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-[#F59E0B] m-0">{{ number_format($complianceEventsCount) }}</h3>
            <span class="text-[11px] font-semibold text-[#6B7086]">IEPs, Approvals, AI & Rollover</span>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#F59E0B]/30 group-hover:bg-[#F59E0B] transition-colors"></div>
        </div>
    </div>

    <!-- 3. SEARCH & MULTI-PARAMETER FILTERING TOOLBAR -->
    <div class="bg-white border border-black/15 rounded-2xl p-5 sm:p-6 shadow-sm">
        <form id="audit-filter-form" method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
            
            <!-- Quick Date Preset Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-black/5">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF]">Quick Filters:</span>
                    <button type="button" @click="setDateRange(0)" class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg border transition {{ request('start_date') === date('Y-m-d') && request('end_date') === date('Y-m-d') ? 'bg-[#1E2657] text-white border-[#1E2657]' : 'bg-slate-50 text-[#6B7086] border-slate-200 hover:bg-slate-100' }}">
                        Today
                    </button>
                    <button type="button" @click="setDateRange(7)" class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg border transition {{ request('start_date') === date('Y-m-d', strtotime('-7 days')) ? 'bg-[#1E2657] text-white border-[#1E2657]' : 'bg-slate-50 text-[#6B7086] border-slate-200 hover:bg-slate-100' }}">
                        Last 7 Days
                    </button>
                    <button type="button" @click="setDateRange(30)" class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg border transition {{ request('start_date') === date('Y-m-d', strtotime('-30 days')) ? 'bg-[#1E2657] text-white border-[#1E2657]' : 'bg-slate-50 text-[#6B7086] border-slate-200 hover:bg-slate-100' }}">
                        Last 30 Days
                    </button>
                    <button type="button" @click="setDateRange(-1)" class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg border transition {{ !request('start_date') && !request('end_date') ? 'bg-[#1E2657] text-white border-[#1E2657]' : 'bg-slate-50 text-[#6B7086] border-slate-200 hover:bg-slate-100' }}">
                        All Time
                    </button>
                </div>

                @if(request()->anyFilled(['search', 'student_name', 'role', 'action_type', 'start_date', 'end_date']))
                    <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-rose-600 hover:text-rose-800 transition">
                        <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                    </a>
                @endif
            </div>

            <!-- Inputs Grid: Search, Student, Role, Action, Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                
                <!-- 1. Global Search (4 cols) -->
                <div class="lg:col-span-3">
                    <label for="filter_search" class="block text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-1.5">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Global Search
                    </label>
                    <div class="relative">
                        <input type="text" id="filter_search" name="search" value="{{ request('search') }}" placeholder="User, username, IP, target ID..." class="w-full bg-[#F8F9FD] border border-black/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-[#1E2657] placeholder-[#9CA0AF] focus:bg-white focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition outline-none">
                    </div>
                </div>

                <!-- 2. Student Name Filter (2 cols) -->
                <div class="lg:col-span-2">
                    <label for="filter_student_name" class="block text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-1.5">
                        <i class="fa-solid fa-user-graduate mr-1"></i> Student Name
                    </label>
                    <input type="text" id="filter_student_name" name="student_name" value="{{ request('student_name') }}" placeholder="Search learner..." class="w-full bg-[#F8F9FD] border border-black/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-[#1E2657] placeholder-[#9CA0AF] focus:bg-white focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition outline-none">
                </div>

                <!-- 3. User Role Filter (2 cols) -->
                <div class="lg:col-span-2">
                    <label for="filter_role" class="block text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-1.5">
                        <i class="fa-solid fa-user-tag mr-1"></i> User Role
                    </label>
                    <select id="filter_role" name="role" class="w-full bg-[#F8F9FD] border border-black/15 rounded-xl px-3 py-2.5 text-xs sm:text-sm font-semibold text-[#1E2657] focus:bg-white focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition outline-none">
                        <option value="">All Roles</option>
                        @foreach($availableRoles as $roleKey => $roleLabel)
                            <option value="{{ $roleKey }}" {{ request('role') === $roleKey ? 'selected' : '' }}>{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Action Type Filter (2 cols) -->
                <div class="lg:col-span-2">
                    <label for="filter_action_type" class="block text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-1.5">
                        <i class="fa-solid fa-list-check mr-1"></i> Action Executed
                    </label>
                    <select id="filter_action_type" name="action_type" class="w-full bg-[#F8F9FD] border border-black/15 rounded-xl px-3 py-2.5 text-xs sm:text-sm font-semibold text-[#1E2657] focus:bg-white focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition outline-none">
                        <option value="">All Actions</option>
                        @foreach($availableActionTypes as $actionKey => $actionLabel)
                            <option value="{{ $actionKey }}" {{ request('action_type') === $actionKey ? 'selected' : '' }}>{{ $actionLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Date Range (3 cols combined: start & end) -->
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-1.5">
                        <i class="fa-solid fa-calendar mr-1"></i> Date From
                    </label>
                    <input type="date" id="filter_start_date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-[#F8F9FD] border border-black/15 rounded-xl px-3 py-2 text-xs font-semibold text-[#1E2657] focus:bg-white focus:border-[#3B82F6] outline-none">
                </div>

                <div class="lg:col-span-1">
                    <button type="submit" class="w-full h-[42px] bg-[#1E2657] text-white rounded-xl text-xs font-extrabold hover:bg-[#2E3D8F] shadow-sm transition active:scale-95 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Apply
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- 4. AUDIT TRAIL LOGS TABLE / FEED -->
    <div class="bg-white border border-black/15 rounded-2xl shadow-sm overflow-hidden">
        
        <!-- Table Header Bar -->
        <div class="p-4 sm:p-5 border-b border-black/10 bg-[#F8F9FD] flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-[13px] sm:text-sm text-[#1E2657] uppercase tracking-wider">
                    Audit Activity Records
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#1E2657]/10 text-[#1E2657]">
                    {{ $logs->total() }} Events Found
                </span>
            </div>
            <span class="text-xs text-[#9CA0AF] font-bold">
                Showing Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
            </span>
        </div>

        @if($logs->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-16 px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-50 text-[#3B82F6] mb-4">
                    <i class="fa-solid fa-clipboard-question text-2xl"></i>
                </div>
                <h3 class="text-base font-extrabold text-[#1E2657] mb-1">No Audit Records Found</h3>
                <p class="text-xs sm:text-sm text-[#6B7086] max-w-md mx-auto mb-4">
                    No activity logs match your active search filters or date range parameters. Try adjusting your query or resetting filters.
                </p>
                <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E2657] text-white rounded-xl text-xs font-bold shadow hover:bg-[#2E3D8F] transition">
                    <i class="fa-solid fa-rotate-left"></i> View All Audit Logs
                </a>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-black/10 bg-slate-50/50 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-wider">
                            <th class="py-3 px-4 sm:px-5">Timestamp</th>
                            <th class="py-3 px-4">User Details</th>
                            <th class="py-3 px-4">Action Executed</th>
                            <th class="py-3 px-4">Target Record & Student</th>
                            <th class="py-3 px-4">Network & IP</th>
                            <th class="py-3 px-4 text-center">Inspect</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 text-xs sm:text-[13px]">
                        @foreach($logs as $log)
                            @php
                                // Color badges for roles
                                $roleBadgeClass = match($log->user_role) {
                                    'Admin' => 'bg-red-50 text-red-700 border-red-200',
                                    'SPED Teacher' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'Guardian' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                };

                                // Icons & badge colors for action types
                                $actionConfig = match($log->action_type) {
                                    'login' => ['icon' => 'fa-arrow-right-to-bracket', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'logout' => ['icon' => 'fa-arrow-right-from-bracket', 'badge' => 'bg-slate-100 text-slate-600 border-slate-200'],
                                    'password_reset' => ['icon' => 'fa-key', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                    'iep_creation' => ['icon' => 'fa-bullseye', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                    'ai_formatting' => ['icon' => 'fa-wand-magic-sparkles', 'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                    'document_approval' => ['icon' => 'fa-stamp', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'document_rejection' => ['icon' => 'fa-rotate-left', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                    'student_archiving' => ['icon' => 'fa-box-archive', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                    'file_download' => ['icon' => 'fa-file-arrow-down', 'badge' => 'bg-sky-50 text-sky-700 border-sky-200'],
                                    'student_create', 'student_reenroll' => ['icon' => 'fa-user-plus', 'badge' => 'bg-teal-50 text-teal-700 border-teal-200'],
                                    default => ['icon' => 'fa-clipboard-list', 'badge' => 'bg-slate-100 text-slate-700 border-slate-200'],
                                };
                            @endphp

                            <tr class="hover:bg-[#F8F9FD] transition-colors group">
                                
                                <!-- 1. Exact Date / Timestamp -->
                                <td class="py-3.5 px-4 sm:px-5 align-top whitespace-nowrap">
                                    <div class="font-extrabold text-[#1E2657] text-xs">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-[11px] font-mono text-[#6B7086] mt-0.5">
                                        {{ $log->created_at->format('h:i:s A') }}
                                    </div>
                                    <div class="text-[10px] text-[#9CA0AF] font-bold mt-1">
                                        {{ $log->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- 2. User Details (Name, Username, Role) -->
                                <td class="py-3.5 px-4 align-top">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-[#1E2657] leading-tight truncate">
                                                {{ $log->user_name }}
                                            </div>
                                            @if($log->user_username)
                                                <div class="text-[11px] text-[#6B7086] font-mono">
                                                    @<span>{{ $log->user_username }}</span>
                                                </div>
                                            @endif
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded text-[10px] font-black uppercase tracking-wider border {{ $roleBadgeClass }}">
                                                {{ $log->user_role }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Action Executed -->
                                <td class="py-3.5 px-4 align-top">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black border {{ $actionConfig['badge'] }} mb-1">
                                        <i class="fa-solid {{ $actionConfig['icon'] }} text-[11px]"></i>
                                        <span>{{ $log->action }}</span>
                                    </div>
                                    @if($log->details)
                                        <p class="text-xs text-[#6B7086] font-medium leading-relaxed m-0 max-w-sm line-clamp-2" title="{{ $log->details }}">
                                            {{ $log->details }}
                                        </p>
                                    @endif
                                </td>

                                <!-- 4. Target Record ID & Student -->
                                <td class="py-3.5 px-4 align-top">
                                    @if($log->target_id || $log->target_type)
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-[#1E2657] border border-slate-200">
                                            <i class="fa-solid fa-cube text-[9px] text-[#9CA0AF]"></i>
                                            <span>{{ $log->target_type ?? 'Record' }}: #{{ $log->target_id ?? 'N/A' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#9CA0AF] italic">N/A</span>
                                    @endif

                                    @if($log->student_name)
                                        <div class="mt-1.5 flex items-center gap-1 text-xs font-extrabold text-[#3B82F6]">
                                            <i class="fa-solid fa-graduation-cap text-[11px]"></i>
                                            <span>{{ $log->student_name }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- 5. IP Address & Client Device -->
                                <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-[#F0F3FA] text-[#1E2657] border border-[#1E2657]/10">
                                        <i class="fa-solid fa-network-wired text-[9px] text-[#3B82F6]"></i>
                                        <span>{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                    </div>
                                    @if($log->user_agent)
                                        <div class="text-[10px] text-[#9CA0AF] truncate max-w-[150px] mt-1" title="{{ $log->user_agent }}">
                                            <i class="fa-solid fa-desktop mr-1"></i> {{ Str::limit($log->user_agent, 25) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- 6. Inspect Modal Button -->
                                <td class="py-3.5 px-4 align-top text-center">
                                    <button type="button" @click="viewDetails({{ json_encode([
                                        'id' => $log->id,
                                        'timestamp' => $log->created_at->format('M d, Y h:i:s A'),
                                        'user_name' => $log->user_name,
                                        'user_username' => $log->user_username ?? 'N/A',
                                        'user_role' => $log->user_role,
                                        'action' => $log->action,
                                        'action_type' => $log->action_type,
                                        'target_type' => $log->target_type ?? 'N/A',
                                        'target_id' => $log->target_id ?? 'N/A',
                                        'student_name' => $log->student_name ?? 'N/A',
                                        'ip_address' => $log->ip_address ?? '127.0.0.1',
                                        'user_agent' => $log->user_agent ?? 'N/A',
                                        'details' => $log->details ?? 'None'
                                    ]) }})" class="w-8 h-8 rounded-xl bg-slate-100 text-[#1E2657] hover:bg-[#1E2657] hover:text-white transition flex items-center justify-center shadow-xs cursor-pointer active:scale-95" title="Inspect Full Event Metadata">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-4 sm:p-5 border-t border-black/10 bg-[#F8F9FD] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="text-xs text-[#6B7086] font-semibold">
                    Showing <span class="font-bold text-[#1E2657]">{{ $logs->firstItem() }}</span> to <span class="font-bold text-[#1E2657]">{{ $logs->lastItem() }}</span> of <span class="font-bold text-[#1E2657]">{{ $logs->total() }}</span> audit events
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif

    </div>

    <!-- 5. EVENT DETAILS METADATA MODAL -->
    <div x-show="detailModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs" @keydown.escape.window="detailModalOpen = false">
        
        <div class="bg-white rounded-3xl shadow-2xl border border-black/15 max-w-xl w-full overflow-hidden" @click.away="detailModalOpen = false">
            
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-[#1E2657] to-[#2E3D8F] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white text-base">
                        <i class="fa-solid fa-fingerprint"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black tracking-tight leading-tight m-0">Audit Record Inspector</h3>
                        <span class="text-xs text-blue-200 font-semibold" x-text="'Log Event ID #' + (selectedLog ? selectedLog.id : '')"></span>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Content Grid -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar text-xs sm:text-sm">
                
                <template x-if="selectedLog">
                    <div class="space-y-4">
                        <!-- Action Summary Banner -->
                        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#3B82F6] block mb-0.5">Action Executed</span>
                                <span class="text-base font-black text-[#1E2657]" x-text="selectedLog.action"></span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-white text-[#1E2657] border border-blue-200" x-text="selectedLog.action_type"></span>
                        </div>

                        <!-- User & Role Info -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] m-0">Actor Credentials</h4>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">Full Name:</span>
                                    <span class="font-extrabold text-[#1E2657]" x-text="selectedLog.user_name"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">System Username:</span>
                                    <span class="font-mono font-bold text-[#1E2657]" x-text="'@' + selectedLog.user_username"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">Role:</span>
                                    <span class="font-bold text-[#1E2657]" x-text="selectedLog.user_role"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">Exact Timestamp:</span>
                                    <span class="font-mono text-[#1E2657]" x-text="selectedLog.timestamp"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Target Record Metadata -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] m-0">Target Record Metadata</h4>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">Target Entity Type:</span>
                                    <span class="font-bold text-[#1E2657]" x-text="selectedLog.target_type"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#6B7086] block">Target Record ID:</span>
                                    <span class="font-mono font-bold text-[#3B82F6]" x-text="'#' + selectedLog.target_id"></span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-[11px] text-[#6B7086] block">Associated Student:</span>
                                    <span class="font-extrabold text-[#1E2657]" x-text="selectedLog.student_name"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Narrative Details / Context -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] m-0">Activity Narrative</h4>
                            <p class="text-xs text-[#1E2657] font-medium leading-relaxed m-0 pt-1" x-text="selectedLog.details"></p>
                        </div>

                        <!-- Security & Device Fingerprint -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] m-0">Network & Security Fingerprint</h4>
                            <div class="space-y-2 pt-1 font-mono text-xs">
                                <div>
                                    <span class="text-[11px] text-[#6B7086] font-sans block">Client IP Address:</span>
                                    <span class="font-bold text-[#1E2657]" x-text="selectedLog.ip_address"></span>
                                </div>
                                <div>
                                    <span class="text-[11px] text-[#6B7086] font-sans block">User Agent (Browser/Client):</span>
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 text-[11px] text-slate-700 break-all" x-text="selectedLog.user_agent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 bg-slate-50 border-t border-black/10 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-[#1E2657] text-white rounded-xl text-xs font-extrabold hover:bg-[#2E3D8F] shadow transition cursor-pointer">
                    Close Inspector
                </button>
            </div>

        </div>
    </div>

</div>

@endsection
