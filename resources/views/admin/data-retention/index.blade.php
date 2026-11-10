@extends('layouts.app')

@section('title', '5-Year Data Retention & Purge Protocol')

@section('content')

<div x-data="{ 
    activeTab: 'eligible',
    showPurgeModal: {{ ($errors->has('one_time_password') || $errors->has('confirmation')) ? 'true' : 'false' }},
    showHoldModal: false,
    selectedStudent: null,
    holdAction: true,
    holdReason: '',
    openHoldModal(student, isPlacing) {
        this.selectedStudent = student;
        this.holdAction = isPlacing;
        this.holdReason = student.legal_hold_reason || '';
        this.showHoldModal = true;
    }
}" class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <!-- NAVIGATION TABS -->
    <div class="flex items-center gap-2 border-b border-black/10 pb-3">
        <a href="{{ route('admin.archive.index') }}" class="px-4 py-2 rounded-xl text-xs sm:text-[13px] font-extrabold text-[#6B7086] hover:text-[#1E2657] hover:bg-[#F0F3FA] transition flex items-center gap-2">
            <i class="fa fa-box-archive"></i>
            <span>Year-End Archiving</span>
        </a>
        <a href="{{ route('admin.data-retention.index') }}" class="px-4 py-2 rounded-xl text-xs sm:text-[13px] font-extrabold bg-[#1E2657] text-white shadow-sm flex items-center gap-2">
            <i class="fa fa-clock-rotate-left text-rose-400"></i>
            <span>5-Year Data Retention & Purge</span>
            @if($eligibleCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500 text-white font-black animate-pulse">{{ $eligibleCount }}</span>
            @endif
        </a>
    </div>

    <!-- MAIN HEADER -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 p-5 sm:p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center text-xl shrink-0 shadow-sm">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-[#1E2657] m-0">5-Year Post-Graduation Data Retention Protocol</h2>
                    <p class="text-xs font-semibold text-[#6B7086] mt-1 mb-0">
                        Automated compliance protocol to permanently purge historical student data 5 years post-graduation or archival.
                    </p>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <!-- Dry Run Simulation Button -->
            <form action="{{ route('admin.data-retention.dry-run') }}" method="POST" class="m-0 flex-1 sm:flex-initial">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-sky-300 bg-sky-50 text-sky-800 text-xs sm:text-[13px] font-extrabold hover:bg-sky-100 transition shadow-sm active:scale-95">
                    <i class="fa-solid fa-flask"></i>
                    <span>Run Dry-Run Simulation</span>
                </button>
            </form>

            <!-- Execute Purge Button -->
            <button type="button" 
                    @click="showPurgeModal = true" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-[13px] font-extrabold transition shadow-sm active:scale-95 {{ $eligibleCount === 0 ? 'opacity-60 cursor-not-allowed' : '' }}"
                    {{ $eligibleCount === 0 ? 'disabled' : '' }}>
                <i class="fa-solid fa-trash-can"></i>
                <span>Execute Permanent Purge ({{ $eligibleCount }})</span>
            </button>
        </div>
    </div>

    <!-- STAT METRICS CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Retention Period -->
        <div class="bg-white rounded-2xl p-4 border border-black/15 shadow-sm">
            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#6B7086]">Retention Policy</span>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-2xl font-black text-[#1E2657]">{{ $retentionYears }}</span>
                <span class="text-xs font-bold text-[#6B7086]">Years</span>
            </div>
            <span class="block text-[11px] font-medium text-slate-500 mt-1">Configurable via Policy</span>
        </div>

        <!-- Cutoff Date -->
        <div class="bg-white rounded-2xl p-4 border border-black/15 shadow-sm">
            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#6B7086]">Retention Cutoff</span>
            <div class="mt-2">
                <span class="text-sm sm:text-base font-black text-[#1E2657]">{{ $cutoffDate->format('M j, Y') }}</span>
            </div>
            <span class="block text-[11px] font-medium text-slate-500 mt-1">Records on/before date</span>
        </div>

        <!-- Eligible for Purge -->
        <div class="bg-white rounded-2xl p-4 border {{ $eligibleCount > 0 ? 'border-rose-300 bg-rose-50/40' : 'border-black/15' }} shadow-sm">
            <span class="block text-[10px] font-extrabold uppercase tracking-widest {{ $eligibleCount > 0 ? 'text-rose-700' : 'text-[#6B7086]' }}">Eligible Records</span>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-2xl font-black {{ $eligibleCount > 0 ? 'text-rose-600' : 'text-[#1E2657]' }}">{{ $eligibleCount }}</span>
                <span class="text-xs font-bold text-[#6B7086]">Students</span>
            </div>
            <span class="block text-[11px] font-medium {{ $eligibleCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }} mt-1">
                {{ $eligibleCount > 0 ? 'Retention Expired' : 'All records compliant' }}
            </span>
        </div>

        <!-- Protected by Legal Hold -->
        <div class="bg-white rounded-2xl p-4 border {{ $legalHoldCount > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-black/15' }} shadow-sm">
            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#6B7086]">Active Legal Holds</span>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-2xl font-black {{ $legalHoldCount > 0 ? 'text-amber-700' : 'text-[#1E2657]' }}">{{ $legalHoldCount }}</span>
                <span class="text-xs font-bold text-[#6B7086]">Protected</span>
            </div>
            <span class="block text-[11px] font-medium text-slate-500 mt-1">Exempt from Deletion</span>
        </div>

        <!-- Last Purge Execution -->
        <div class="col-span-2 sm:col-span-2 lg:col-span-1 bg-white rounded-2xl p-4 border border-black/15 shadow-sm">
            <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#6B7086]">Last Purge Date</span>
            <div class="mt-2">
                <span class="text-xs sm:text-[13px] font-black text-[#1E2657]">
                    {{ $lastPurge ? \Carbon\Carbon::parse($lastPurge['timestamp'])->format('M j, Y') : 'No Prior Purge' }}
                </span>
            </div>
            <span class="block text-[11px] font-medium text-slate-500 mt-1 truncate">
                {{ $lastPurge['user_name'] ?? 'Automated Daily at 2:00 AM' }}
            </span>
        </div>
    </div>

    <!-- SIMULATION / PURGE RESULT CALLOUT -->
    @if(session('simulation_results'))
        @php $sim = session('simulation_results'); @endphp
        <div class="bg-sky-50 border-2 border-sky-300 rounded-2xl p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sky-900 font-black text-sm">
                    <i class="fa-solid fa-circle-check text-sky-600 text-base"></i>
                    <span>Dry-Run Simulation Results (No Data Was Deleted)</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-sky-200 text-sky-800 uppercase tracking-wider">Simulation Only</span>
            </div>
            <p class="text-xs text-sky-800 m-0">
                A permanent purge execution right now would affect <strong>{{ $sim['total_students_processed'] }} student record(s)</strong> and permanently unlink <strong>{{ $sim['files_deleted'] }} physical file(s)</strong>.
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 pt-2 border-t border-sky-200 text-center">
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">Snapshots</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['archived_snapshots_deleted'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">Documents</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['documents_deleted'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">Reports</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['reports_deleted'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">Daily Logs</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['daily_logs_deleted'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">IEP Goals</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['iep_goals_deleted'] }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/70">
                    <span class="block text-[10px] font-bold text-sky-700 uppercase">Physical Files</span>
                    <span class="text-sm font-black text-sky-900">{{ $sim['files_deleted'] }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('purge_results'))
        @php $purgeRes = session('purge_results'); @endphp
        <div class="bg-rose-50 border-2 border-rose-300 rounded-2xl p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-rose-900 font-black text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    <span>Permanent Purge Completed Successfully</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-200 text-rose-800 uppercase tracking-wider">Live Purge Executed</span>
            </div>
            <p class="text-xs text-rose-800 m-0">
                Permanently eradicated <strong>{{ $purgeRes['total_students_processed'] }} student record(s)</strong> and <strong>{{ $purgeRes['files_deleted'] }} server files</strong> from disk.
            </p>
        </div>
    @endif

    <!-- SAFETY MEASURES CALLOUT BANNER -->
    <div class="bg-white rounded-2xl border border-black/15 p-4 sm:p-5 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#1E2657]/10 text-[#1E2657] flex items-center justify-center shrink-0 mt-0.5">
                <i class="fa-solid fa-lock text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs sm:text-[13px] font-extrabold text-[#1E2657] m-0">Strict Automated Safety Safeguards Active</h4>
                <p class="text-[11px] sm:text-xs text-[#6B7086] mt-0.5 mb-0">
                    • <strong>Active Students Safeguarded:</strong> Records with active status are immune from purging regardless of enrollment duration.<br>
                    • <strong>Legal Hold Immunity:</strong> Records flagged under legal holds are strictly excluded until officially cleared.<br>
                    • <strong>Scheduler Protocol:</strong> Laravel Scheduler runs every night at <strong>02:00 AM</strong> to process records reaching 5 years post-archival.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Daily Scheduler Active
            </span>
        </div>
    </div>

    <!-- PREVIEW TABS CONTAINER -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        
        <!-- Tab Navigation Header -->
        <div class="px-5 pt-4 border-b border-black/10 flex items-center gap-3 overflow-x-auto">
            <button type="button" 
                    @click="activeTab = 'eligible'" 
                    :class="activeTab === 'eligible' ? 'border-[#3B82F6] text-[#3B82F6]' : 'border-transparent text-[#6B7086] hover:text-[#1E2657]'" 
                    class="pb-3 border-b-2 font-extrabold text-xs sm:text-[13px] flex items-center gap-2 transition whitespace-nowrap">
                <i class="fa-solid fa-calendar-xmark"></i>
                <span>Eligible for Purge ({{ $eligibleStudents->count() }})</span>
                @if($eligibleStudents->count() > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-700 font-black">{{ $eligibleStudents->count() }}</span>
                @endif
            </button>

            <button type="button" 
                    @click="activeTab = 'upcoming'" 
                    :class="activeTab === 'upcoming' ? 'border-[#3B82F6] text-[#3B82F6]' : 'border-transparent text-[#6B7086] hover:text-[#1E2657]'" 
                    class="pb-3 border-b-2 font-extrabold text-xs sm:text-[13px] flex items-center gap-2 transition whitespace-nowrap">
                <i class="fa-solid fa-clock"></i>
                <span>Approaching Expiration (Next 180 Days) ({{ $upcomingStudents->count() }})</span>
            </button>

            <button type="button" 
                    @click="activeTab = 'legal_holds'" 
                    :class="activeTab === 'legal_holds' ? 'border-[#3B82F6] text-[#3B82F6]' : 'border-transparent text-[#6B7086] hover:text-[#1E2657]'" 
                    class="pb-3 border-b-2 font-extrabold text-xs sm:text-[13px] flex items-center gap-2 transition whitespace-nowrap">
                <i class="fa-solid fa-gavel"></i>
                <span>Legal Holds & Mandatory Retention ({{ $legalHolds->count() }})</span>
            </button>
        </div>

        <!-- TAB 1: ELIGIBLE FOR PURGE -->
        <div x-show="activeTab === 'eligible'" class="p-5 sm:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <p class="text-xs text-[#6B7086] m-0">
                    These students have exceeded the {{ $retentionYears }}-year retention period starting from their graduation or archival date and are scheduled for automatic purge.
                </p>
                <span class="text-[11px] font-bold text-slate-500 shrink-0">Showing up to 100 records</span>
            </div>

            @if($eligibleStudents->isEmpty())
                <div class="text-center py-12 px-4 rounded-xl bg-slate-50 border border-dashed border-black/10">
                    <i class="fa-solid fa-circle-check text-4xl text-emerald-500 mb-3"></i>
                    <h4 class="text-sm font-extrabold text-[#1E2657] m-0">No Expired Records</h4>
                    <p class="text-xs text-[#6B7086] mt-1 mb-0">All graduated and archived student records are within the mandatory {{ $retentionYears }}-year retention window.</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-5 sm:-mx-6">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FD] border-y border-black/10 text-[#6B7086] uppercase tracking-wider font-extrabold text-[10px]">
                                <th class="py-3 px-4 sm:px-6">Student Profile</th>
                                <th class="py-3 px-4">Status & LRN</th>
                                <th class="py-3 px-4">Retention Start</th>
                                <th class="py-3 px-4">Expired Elapsed</th>
                                <th class="py-3 px-4">Attached Data Breakdown</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($eligibleStudents as $st)
                                @php
                                    $startDate = $st->retention_start_date;
                                    $daysOverdue = $st->daysUntilPurge() !== null ? abs($st->daysUntilPurge()) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-extrabold text-[#1E2657]">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-black text-xs shrink-0">
                                                {{ strtoupper(substr($st->first_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-[13px] font-black text-[#1E2657]">{{ $st->first_name }} {{ $st->last_name }}</div>
                                                <div class="text-[11px] font-normal text-[#6B7086]">{{ $st->class_name ?? 'Unassigned' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-black uppercase rounded {{ $st->status === 'graduated' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-black/10' }}">
                                            {{ ucfirst($st->status) }}
                                        </span>
                                        <div class="font-mono text-[10px] text-slate-500 mt-0.5">LRN: {{ $st->learners_reference_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E2657]">{{ $startDate ? $startDate->format('M j, Y') : 'Unknown' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $st->graduated_at ? 'Graduation Date' : 'Archival Date' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 text-rose-700 font-extrabold text-xs">
                                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            Expired +{{ $daysOverdue }} days ago
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-wrap gap-1 text-[10px] font-bold">
                                            <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700" title="Snapshots">{{ $st->archivedStudents->count() }} Snaps</span>
                                            <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700" title="Documents">{{ $st->allDocuments->count() }} Docs</span>
                                            <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700" title="Reports">{{ $st->reports->count() }} Reports</span>
                                            <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700" title="Daily Logs">{{ $st->dailyLogs->count() }} Logs</span>
                                            <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-700" title="IEP Goals">{{ $st->iepGoals->count() }} Goals</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <button type="button" 
                                                @click="openHoldModal({ id: {{ $st->id }}, name: '{{ addslashes($st->first_name . ' ' . $st->last_name) }}', legal_hold_reason: '' }, true)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 font-extrabold text-[11px] transition shadow-xs">
                                            <i class="fa-solid fa-gavel"></i>
                                            <span>Place Legal Hold</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- TAB 2: UPCOMING EXPIRATION -->
        <div x-show="activeTab === 'upcoming'" class="p-5 sm:p-6 space-y-4">
            <p class="text-xs text-[#6B7086] m-0">
                These student records will reach their 5-year retention expiration in the next 180 days. Administrators may place legal holds in advance if retention is mandated.
            </p>

            @if($upcomingStudents->isEmpty())
                <div class="text-center py-12 px-4 rounded-xl bg-slate-50 border border-dashed border-black/10">
                    <i class="fa-solid fa-calendar-check text-4xl text-[#3B82F6] mb-3"></i>
                    <h4 class="text-sm font-extrabold text-[#1E2657] m-0">No Imminent Expirations</h4>
                    <p class="text-xs text-[#6B7086] mt-1 mb-0">No archived or graduated records are reaching expiration within the next 180 days.</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-5 sm:-mx-6">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FD] border-y border-black/10 text-[#6B7086] uppercase tracking-wider font-extrabold text-[10px]">
                                <th class="py-3 px-4 sm:px-6">Student Profile</th>
                                <th class="py-3 px-4">Status & LRN</th>
                                <th class="py-3 px-4">Retention Start Date</th>
                                <th class="py-3 px-4">Projected Purge Date</th>
                                <th class="py-3 px-4">Days Remaining</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($upcomingStudents as $st)
                                @php
                                    $startDate = $st->retention_start_date;
                                    $expiry = $st->retention_expiry_date;
                                    $daysRemaining = $st->daysUntilPurge();
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-extrabold text-[#1E2657]">
                                        <div class="text-[13px] font-black text-[#1E2657]">{{ $st->first_name }} {{ $st->last_name }}</div>
                                        <div class="text-[11px] text-[#6B7086]">{{ $st->class_name ?? 'Unassigned' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-black uppercase rounded {{ $st->status === 'graduated' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($st->status) }}
                                        </span>
                                        <div class="font-mono text-[10px] text-slate-500 mt-0.5">LRN: {{ $st->learners_reference_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#1E2657]">
                                        {{ $startDate ? $startDate->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#1E2657]">
                                        {{ $expiry ? $expiry->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 text-amber-700 font-extrabold text-xs">
                                            <i class="fa-regular fa-clock"></i>
                                            In {{ $daysRemaining }} days
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <button type="button" 
                                                @click="openHoldModal({ id: {{ $st->id }}, name: '{{ addslashes($st->first_name . ' ' . $st->last_name) }}', legal_hold_reason: '' }, true)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 font-extrabold text-[11px] transition shadow-xs">
                                            <i class="fa-solid fa-gavel"></i>
                                            <span>Place Legal Hold</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- TAB 3: LEGAL HOLDS -->
        <div x-show="activeTab === 'legal_holds'" class="p-5 sm:p-6 space-y-4">
            <p class="text-xs text-[#6B7086] m-0">
                Student records under active legal holds are strictly protected from deletion. Neither manual purge nor automated daily scheduler can erase these files.
            </p>

            @if($legalHolds->isEmpty())
                <div class="text-center py-12 px-4 rounded-xl bg-slate-50 border border-dashed border-black/10">
                    <i class="fa-solid fa-scale-balanced text-4xl text-[#6B7086] mb-3"></i>
                    <h4 class="text-sm font-extrabold text-[#1E2657] m-0">No Active Legal Holds</h4>
                    <p class="text-xs text-[#6B7086] mt-1 mb-0">No student records are currently held under litigation, audit, or mandatory retention orders.</p>
                </div>
            @else
                <div class="overflow-x-auto -mx-5 sm:-mx-6">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8F9FD] border-y border-black/10 text-[#6B7086] uppercase tracking-wider font-extrabold text-[10px]">
                                <th class="py-3 px-4 sm:px-6">Protected Student</th>
                                <th class="py-3 px-4">Status & LRN</th>
                                <th class="py-3 px-4">Legal Hold Justification / Reason</th>
                                <th class="py-3 px-4">Retention Date</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            @foreach($legalHolds as $st)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 sm:px-6 font-extrabold text-[#1E2657]">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                            <div>
                                                <div class="text-[13px] font-black text-[#1E2657]">{{ $st->first_name }} {{ $st->last_name }}</div>
                                                <div class="text-[11px] text-[#6B7086]">{{ $st->class_name ?? 'Unassigned' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-black uppercase rounded bg-slate-100 text-slate-700">
                                            {{ ucfirst($st->status) }}
                                        </span>
                                        <div class="font-mono text-[10px] text-slate-500 mt-0.5">LRN: {{ $st->learners_reference_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-2 text-xs font-medium max-w-md">
                                            <i class="fa-solid fa-quote-left text-amber-500 text-[10px] mr-1"></i>
                                            {{ $st->legal_hold_reason ?: 'Mandatory administrative hold applied.' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#1E2657]">
                                        {{ $st->retention_start_date ? $st->retention_start_date->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right">
                                        <button type="button" 
                                                @click="openHoldModal({ id: {{ $st->id }}, name: '{{ addslashes($st->first_name . ' ' . $st->last_name) }}', legal_hold_reason: '{{ addslashes($st->legal_hold_reason ?? '') }}' }, false)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-extrabold text-[11px] transition shadow-xs">
                                            <i class="fa-solid fa-unlock"></i>
                                            <span>Release Hold</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    <!-- 2FA CONFIRMATION MODAL FOR LIVE PURGE -->
    <div x-show="showPurgeModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="showPurgeModal = false">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 space-y-6 border border-black/10 relative"
             @click.away="showPurgeModal = false">
            
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-3xl border border-rose-300">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <h4 class="text-xl font-black text-[#1E2657] m-0">Permanent Purge Authorization</h4>
                <p class="text-xs font-semibold text-[#6B7086] mt-2 mb-0">
                    Two-Factor Authentication (2FA) verification and explicit confirmation are required to permanently erase records.
                </p>
            </div>

            <!-- Caution Callout -->
            <div class="bg-rose-50 border-2 border-rose-300 text-rose-950 p-4 rounded-xl text-xs space-y-2 leading-relaxed shadow-xs text-left">
                <div class="flex items-center gap-2 text-rose-800 font-black text-[12px] uppercase tracking-wider">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                    <span>IRREVERSIBLE DATA ERASURE WARNING</span>
                </div>
                <p class="font-extrabold text-rose-800 m-0">
                    This will PERMANENTLY DESTROY all historical snapshots, master archive PDFs, student documents, progress reports, daily logs, observation photos, and IEP goals for {{ $eligibleCount }} student(s).
                </p>
                <p class="font-medium text-slate-700 m-0">
                    Physical files stored on server disks will be unlinked and unrecoverable.
                </p>
            </div>

            @if($errors->has('one_time_password'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 rounded-xl text-xs font-bold flex items-center">
                    <i class="fa fa-exclamation-circle mr-2 text-rose-500"></i>
                    {{ $errors->first('one_time_password') }}
                </div>
            @endif

            @if($errors->has('confirmation'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-2.5 rounded-xl text-xs font-bold flex items-center">
                    <i class="fa fa-exclamation-circle mr-2 text-rose-500"></i>
                    {{ $errors->first('confirmation') }}
                </div>
            @endif

            <form action="{{ route('admin.data-retention.purge') }}" method="POST" class="space-y-4 m-0">
                @csrf

                <div>
                    <label class="block text-xs font-extrabold text-[#1E2657] uppercase tracking-wider mb-2 text-center">
                        6-Digit Authenticator Code
                    </label>
                    <input type="text" 
                           name="one_time_password" 
                           class="w-full text-center text-2xl font-mono font-bold tracking-widest py-3 px-4 rounded-xl border border-black/20 focus:border-rose-500 focus:ring-rose-500 shadow-sm bg-white" 
                           required 
                           placeholder="• • • • • •" 
                           maxlength="6" 
                           autofocus>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-[#1E2657] mb-1.5 text-center">
                        Type <span class="font-mono text-rose-600 bg-rose-50 px-1 py-0.5 rounded border border-rose-200 font-black">PURGE</span> to confirm permanent deletion:
                    </label>
                    <input type="text" 
                           name="confirmation" 
                           class="w-full text-center uppercase tracking-widest py-2.5 px-4 rounded-xl border border-black/20 focus:border-rose-500 focus:ring-rose-500 text-sm font-extrabold bg-white shadow-sm" 
                           required 
                           placeholder="PURGE">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" 
                            @click="showPurgeModal = false" 
                            class="w-1/2 py-2.5 px-4 rounded-xl border border-black/20 text-[#6B7086] text-xs font-extrabold hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="w-1/2 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black transition shadow-sm">
                        Confirm Purge
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- LEGAL HOLD TOGGLE MODAL -->
    <div x-show="showHoldModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="showHoldModal = false">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-7 space-y-5 border border-black/10 relative"
             @click.away="showHoldModal = false">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-gavel"></i>
                </div>
                <div>
                    <h4 class="text-base font-black text-[#1E2657] m-0" x-text="holdAction ? 'Place Student on Legal Hold' : 'Release Legal Hold'"></h4>
                    <p class="text-xs text-[#6B7086] m-0 mt-0.5" x-text="selectedStudent ? selectedStudent.name : ''"></p>
                </div>
            </div>

            <form :action="'{{ url('/admin/data-retention') }}/' + (selectedStudent ? selectedStudent.id : 0) + '/legal-hold'" 
                  method="POST" 
                  class="space-y-4 m-0">
                @csrf
                <input type="hidden" name="legal_hold" :value="holdAction ? 1 : 0">

                <div x-show="holdAction">
                    <label class="block text-xs font-extrabold text-[#1E2657] mb-1.5">
                        Reason for Mandatory Retention / Legal Hold <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="legal_hold_reason" 
                              rows="3" 
                              x-model="holdReason" 
                              placeholder="e.g., Pending legal inquiry, special education audit, or dispute resolution..." 
                              class="w-full text-xs rounded-xl border border-black/20 p-3 focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm"></textarea>
                    <p class="text-[11px] text-[#6B7086] mt-1 m-0">This reason will be recorded in the system audit log.</p>
                </div>

                <div x-show="!holdAction" class="bg-amber-50 p-3 rounded-xl border border-amber-200 text-xs text-amber-900 leading-relaxed">
                    Releasing this legal hold will return the student record to the standard data retention schedule. If the record exceeds {{ $retentionYears }} years post-archival, it will become eligible for the automated purge protocol.
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" 
                            @click="showHoldModal = false" 
                            class="w-1/2 py-2 px-4 rounded-xl border border-black/20 text-[#6B7086] text-xs font-extrabold hover:bg-slate-100 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :class="holdAction ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-rose-600 hover:bg-rose-700 text-white'" 
                            class="w-1/2 py-2 px-4 rounded-xl text-xs font-black transition shadow-sm"
                            x-text="holdAction ? 'Save Legal Hold' : 'Confirm Release'">
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

