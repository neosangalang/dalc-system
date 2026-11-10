@props([
    'student',
    'pastArchives' => collect(),
    'activeSchoolYear' => 'Active SY',
    'isAdmin' => false,
])

@php
    $hasArchives = isset($pastArchives) && $pastArchives->isNotEmpty();
    $firstArchive = $hasArchives ? $pastArchives->first() : null;
@endphp

<div x-data="{
    activeTab: 'active_year', // 'active_year' or 'historical_view'
    selectedArchiveId: {{ $hasArchives ? $firstArchive->id : 'null' }},
    selectArchive(id) {
        this.selectedArchiveId = id;
    }
}" class="bg-white rounded-2xl shadow-sm border border-black/15 flex flex-col overflow-hidden h-full">

    <!-- 1. TABS NAVIGATION HEADER -->
    <div class="px-5 py-4 border-b border-black/10 bg-[#F4F6FA] flex flex-wrap items-center justify-between gap-3">
        
        <!-- Left: Tab Buttons -->
        <div class="inline-flex p-1 bg-white border border-black/10 rounded-xl shadow-xs">
            
            <!-- Tab 1: Active Academic Year -->
            <button type="button" 
                    @click="activeTab = 'active_year'"
                    class="px-4 py-2 rounded-lg text-xs font-extrabold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'active_year' ? 'bg-[#1E2657] text-white shadow-xs' : 'text-[#6B7086] hover:text-[#1E2657] hover:bg-slate-50'">
                <i class="fa-solid fa-bullseye text-[11px]" :class="activeTab === 'active_year' ? 'text-amber-400' : 'text-[#8B5CF6]'"></i>
                <span>Active Year Records</span>
                <span class="px-1.5 py-0.2 rounded text-[10px] font-black" :class="activeTab === 'active_year' ? 'bg-white/20 text-white' : 'bg-slate-100 text-[#1E2657]'">
                    {{ $student->iepGoals->count() }}
                </span>
            </button>

            <!-- Tab 2: Historical Views (Longitudinal Comparison) -->
            <button type="button" 
                    @click="activeTab = 'historical_view'"
                    class="px-4 py-2 rounded-lg text-xs font-extrabold transition-all flex items-center gap-2 cursor-pointer"
                    :class="activeTab === 'historical_view' ? 'bg-[#1E2657] text-white shadow-xs' : 'text-[#6B7086] hover:text-[#1E2657] hover:bg-slate-50'">
                <i class="fa-solid fa-code-compare text-[11px]" :class="activeTab === 'historical_view' ? 'text-[#3B82F6]' : 'text-[#3B82F6]'"></i>
                <span>Historical Views</span>
                @if($hasArchives)
                    <span class="px-1.5 py-0.2 rounded text-[10px] font-black" :class="activeTab === 'historical_view' ? 'bg-white/20 text-white' : 'bg-blue-50 text-[#3B82F6]'">
                        {{ $pastArchives->count() }} SY
                    </span>
                @else
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 text-slate-400">
                        None
                    </span>
                @endif
            </button>

        </div>

        <!-- Right: Fast Actions / Active SY Badge -->
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[11px] font-extrabold bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-wider">
                <i class="fa-solid fa-calendar-check text-[10px]"></i> {{ $activeSchoolYear }}
            </span>
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- 2. TAB 1: ACTIVE ACADEMIC YEAR IEP GOALS & REPORTS       -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'active_year'" x-cloak class="p-6 overflow-y-auto max-h-[850px] custom-scrollbar bg-white flex-1 space-y-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-black/10">
            <div>
                <h4 class="text-sm font-extrabold text-[#1E2657] m-0">Active Academic Year Performance Slate</h4>
                <p class="text-xs text-[#6B7086] m-0">Live IEP goals, objectives, and progress records for {{ $activeSchoolYear }}.</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-extrabold text-[#6B7086] uppercase tracking-wider">Average Progress</span>
                <div class="text-base font-black text-[#1E2657]">
                    @php
                        $avgProgress = $student->iepGoals->isNotEmpty() ? round($student->iepGoals->avg('progress_percentage')) : 0;
                    @endphp
                    <span class="{{ $avgProgress >= 80 ? 'text-[#10B981]' : ($avgProgress >= 50 ? 'text-[#3B82F6]' : 'text-[#8B5CF6]') }}">{{ $avgProgress }}%</span>
                </div>
            </div>
        </div>

        @forelse($student->iepGoals as $goal)
            <div class="mb-6 pb-6 border-b border-black/10 last:border-0 last:pb-0 last:mb-0">
                
                <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                    <span class="inline-flex px-3.5 py-1.5 bg-[#8B5CF6]/10 text-[#8B5CF6] text-[10px] font-extrabold rounded-md uppercase tracking-widest border border-[#8B5CF6]/20 shadow-xs">
                        {{ str_replace('-', ' - ', strtoupper($goal->domain)) }}
                    </span>
                    <span class="text-[12px] font-extrabold text-[#6B7086] bg-[#F4F6FA] px-3.5 py-1.5 border border-black/5 rounded-md shadow-xs uppercase tracking-widest">
                        Progress: <span class="{{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#8B5CF6]' }} font-black">{{ $goal->progress_percentage }}%</span>
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 mb-4 overflow-hidden border border-black/5">
                    <div class="h-2 rounded-full transition-all duration-500 {{ $goal->progress_percentage == 100 ? 'bg-[#10B981]' : ($goal->progress_percentage >= 50 ? 'bg-[#3B82F6]' : 'bg-[#8B5CF6]') }}" style="width: {{ $goal->progress_percentage }}%"></div>
                </div>

                @if($goal->plop)
                    <div class="mb-4 p-3.5 bg-blue-50/50 rounded-xl border border-blue-100 text-xs">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#3B82F6] block mb-1">
                            <i class="fa-solid fa-clipboard-user mr-1"></i> Baseline PLOP (Present Level of Performance):
                        </span>
                        <p class="text-[#1E2657] font-semibold m-0 leading-relaxed">{{ $goal->plop }}</p>
                    </div>
                @endif

                @php
                    $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                @endphp

                <div class="space-y-4">
                    @foreach($subGoals as $subGoalText)
                        @php
                            $subGoalText = trim($subGoalText);
                            if (empty($subGoalText)) continue;

                            $objective = $subGoalText;
                            $activitiesText = '';

                            if (preg_match('/ACTIVITIES:/i', $subGoalText)) {
                                $parts = preg_split('/ACTIVITIES:/i', $subGoalText);
                                $objective = preg_replace('/OBJECTIVE:/i', '', $parts[0]);
                                $activitiesText = $parts[1];
                            }

                            $objective = trim($objective);
                            $objective = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $objective);
                            $activitiesText = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $activitiesText);
                            
                            $activitiesArray = preg_split('/\[ \]|\[x\]|\[X\]|\[\]|•/', $activitiesText);
                        @endphp

                        @if(!empty($objective))
                            <div class="p-4 sm:p-5 bg-white border-y border-r border-l-4 border-black/15 border-l-[#8B5CF6] rounded-r-xl shadow-xs">
                                <span class="text-[10px] font-extrabold tracking-widest text-[#8B5CF6] uppercase block mb-1.5 flex items-center">
                                    <i class="fa-solid fa-bullseye mr-1.5"></i> Objective:
                                </span>
                                <p class="text-xs sm:text-[13px] font-extrabold text-[#1E2657] mb-0 leading-relaxed">{{ $objective }}</p>

                                @if(count(array_filter($activitiesArray)) > 0)
                                    <div class="mt-4 pt-4 border-t border-black/10 border-dashed">
                                        <span class="text-[10px] font-extrabold tracking-widest text-[#9CA0AF] uppercase block mb-2 flex items-center">
                                            <i class="fa-solid fa-list-check mr-1.5 text-[#3B82F6]"></i> Activities & Strategies:
                                        </span>
                                        <ul class="list-disc pl-5 space-y-1.5 text-xs font-medium text-[#6B7086] m-0 leading-relaxed">
                                            @foreach($activitiesArray as $activity)
                                                @if(trim($activity))
                                                    <li>{{ trim($activity) }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-16 px-6 bg-white rounded-2xl border border-black/15">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#F4F6FA] text-[#9CA0AF] mb-3">
                    <i class="fa-solid fa-folder-open text-2xl"></i>
                </div>
                <h4 class="font-extrabold text-[#1E2657] text-sm mb-1">No Active IEP Goals Established</h4>
                <p class="text-[#6B7086] text-xs max-w-sm mx-auto m-0">
                    This student does not currently have active IEP goals recorded for {{ $activeSchoolYear }}.
                </p>
            </div>
        @endforelse

    </div>

    <!-- ======================================================== -->
    <!-- 3. TAB 2: HISTORICAL VIEWS & MULTI-YEAR COMPARISON       -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'historical_view'" x-cloak class="p-6 overflow-y-auto max-h-[850px] custom-scrollbar bg-white flex-1 space-y-6">
        
        @if(!$hasArchives)
            <!-- Empty State for Historical Views -->
            <div class="text-center py-16 px-6 bg-slate-50/60 rounded-2xl border border-dashed border-black/15">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-50 text-[#3B82F6] mb-3 shadow-xs">
                    <i class="fa-solid fa-timeline text-2xl"></i>
                </div>
                <h4 class="font-extrabold text-[#1E2657] text-base mb-1">No Historical Archive Snapshots on File</h4>
                <p class="text-xs sm:text-sm text-[#6B7086] max-w-md mx-auto mb-4">
                    This student does not have prior school year snapshots. When the administrator completes Year-End Rollover archiving, previous academic year records and evaluations will be retained here for side-by-side longitudinal comparison.
                </p>
                <button type="button" @click="activeTab = 'active_year'" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E2657] text-white rounded-xl text-xs font-extrabold shadow-sm hover:bg-[#2E3D8F] transition">
                    <i class="fa-solid fa-arrow-left"></i> Return to Active Year
                </button>
            </div>
        @else
            <!-- Year Selection & Longitudinal Comparison Header -->
            <div class="p-5 rounded-2xl bg-[#F8F9FD] border border-black/10 space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-100/70 text-[#3B82F6] border border-blue-200">
                            <i class="fa-solid fa-code-compare"></i> Multi-Year Longitudinal Inspection
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-[#1E2657] mt-1 mb-0">
                            Cross-Year Historical Comparison
                        </h3>
                        <p class="text-xs text-[#6B7086] m-0">
                            Compare archived baseline goals and clinical evaluations against the active academic year records.
                        </p>
                    </div>

                    <!-- Archived School Year Selector Pills -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mr-1">Select Archive:</span>
                        @foreach($pastArchives as $archive)
                            <button type="button" 
                                    @click="selectArchive({{ $archive->id }})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition-all border cursor-pointer"
                                    :class="selectedArchiveId === {{ $archive->id }} 
                                        ? 'bg-[#1E2657] text-white border-[#1E2657] shadow-sm' 
                                        : 'bg-white text-[#1E2657] border-black/15 hover:bg-slate-50'">
                                SY {{ $archive->school_year }}
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Loop Through Archived Snapshots & Display Side-by-Side with Active Records -->
            @foreach($pastArchives as $archive)
                <div x-show="selectedArchiveId === {{ $archive->id }}" x-cloak class="space-y-6">
                    
                    <!-- Comparative Banner Strip -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-gradient-to-r from-blue-50 via-indigo-50/40 to-slate-50 border border-blue-100">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-white text-[#1E2657] border border-black/10 shadow-xs">
                                <i class="fa-solid fa-box-archive mr-1 text-[#3B82F6]"></i> Archived: SY {{ $archive->school_year }}
                            </span>
                            <span class="text-xs font-black text-[#9CA0AF]">VS</span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-[#1E2657] text-white shadow-xs">
                                <i class="fa-solid fa-bolt mr-1 text-amber-400"></i> Active: {{ $activeSchoolYear }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($isAdmin)
                                <a href="{{ route('admin.archive.pdf', $archive->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#1E2657]/20 rounded-lg text-xs font-extrabold text-[#1E2657] hover:bg-[#1E2657] hover:text-white transition shadow-xs">
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i> Master Archive PDF
                                </a>
                            @endif
                            <span class="text-[11px] text-[#6B7086] font-medium">
                                Archived on {{ \Carbon\Carbon::parse($archive->archived_at)->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    @php
                        $archivedIeps = collect($archive->iep_snapshot ?? []);
                        $archivedReports = collect($archive->progress_snapshot ?? []);
                        $activeIeps = $student->iepGoals;
                        $activeReports = $student->reports ?? collect();

                        // Group goals by domain for side-by-side longitudinal mapping
                        $allDomains = $archivedIeps->pluck('domain')
                            ->merge($activeIeps->pluck('domain'))
                            ->unique()
                            ->values();
                    @endphp

                    <!-- 1. DOMAIN PROGRESS TRAJECTORY MATRIX -->
                    <div class="rounded-2xl border border-black/10 overflow-hidden shadow-xs">
                        <div class="px-4 py-3 bg-[#F8F9FD] border-b border-black/10 flex items-center justify-between">
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#1E2657]">
                                <i class="fa-solid fa-chart-line mr-1.5 text-[#3B82F6]"></i> Longitudinal Domain Progress Matrix
                            </span>
                            <span class="text-[11px] text-[#6B7086] font-semibold">Comparing Year-over-Year Goal Mastery</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] font-black text-[#9CA0AF] uppercase tracking-wider border-b border-black/10">
                                        <th class="py-2.5 px-4">Domain Area</th>
                                        <th class="py-2.5 px-4 text-center">Past SY {{ $archive->school_year }} Mastery</th>
                                        <th class="py-2.5 px-4 text-center">Active {{ $activeSchoolYear }} Progress</th>
                                        <th class="py-2.5 px-4 text-right">Trajectory Evaluation</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-black/5 font-semibold">
                                    @foreach($allDomains as $domain)
                                        @php
                                            $pastGoal = $archivedIeps->firstWhere('domain', $domain);
                                            $currGoal = $activeIeps->firstWhere('domain', $domain);

                                            $pastPct = $pastGoal ? ($pastGoal['progress_percentage'] ?? 0) : null;
                                            $currPct = $currGoal ? $currGoal->progress_percentage : null;

                                            $diff = ($pastPct !== null && $currPct !== null) ? ($currPct - $pastPct) : null;
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-3 px-4 font-extrabold text-[#1E2657]">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-[#8B5CF6]"></span>
                                                    {{ str_replace('-', ' - ', strtoupper($domain)) }}
                                                </span>
                                            </td>

                                            <!-- Past Achieved -->
                                            <td class="py-3 px-4 text-center">
                                                @if($pastPct !== null)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-[#1E2657]">
                                                        {{ $pastPct }}%
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic text-[11px]">Not evaluated</span>
                                                @endif
                                            </td>

                                            <!-- Active Progress -->
                                            <td class="py-3 px-4 text-center">
                                                @if($currPct !== null)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $currPct == 100 ? 'bg-emerald-50 text-[#10B981]' : 'bg-blue-50 text-[#3B82F6]' }}">
                                                        {{ $currPct }}%
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic text-[11px]">No active goal</span>
                                                @endif
                                            </td>

                                            <!-- Trajectory -->
                                            <td class="py-3 px-4 text-right">
                                                @if($pastPct === 100 && $currPct !== null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                        <i class="fa-solid fa-circle-check"></i> Mastered & Advanced
                                                    </span>
                                                @elseif($diff !== null && $diff > 0)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <i class="fa-solid fa-arrow-trend-up"></i> +{{ $diff }}% Growth
                                                    </span>
                                                @elseif($diff !== null && $diff < 0)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <i class="fa-solid fa-clock-rotate-left"></i> Baseline Reset
                                                    </span>
                                                @elseif($pastPct === null && $currPct !== null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-[#3B82F6] border border-blue-200 uppercase">
                                                        New Domain Focus
                                                    </span>
                                                @elseif($pastPct !== null && $currPct === null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200 uppercase">
                                                        Prior Competency
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. SIDE-BY-SIDE IN-DEPTH IEP GOALS & OBJECTIVES COMPARISON -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-extrabold text-[#1E2657] m-0">
                                Side-by-Side Detailed IEP Comparison
                            </h4>
                            <span class="text-xs text-[#6B7086] font-medium">
                                Past Baseline Objectives vs Current Academic Goals
                            </span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
                            
                            <!-- LEFT COLUMN: ARCHIVED SCHOOL YEAR -->
                            <div class="p-5 rounded-2xl bg-slate-50/70 border border-black/10 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-black/10">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-[#3B82F6] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-black text-[#1E2657] m-0">Archived SY {{ $archive->school_year }}</h5>
                                            <span class="text-[10px] text-[#6B7086]">Rollover Snapshot Record</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200/80 text-[#1E2657]">
                                        {{ $archivedIeps->count() }} Past Goals
                                    </span>
                                </div>

                                @forelse($archivedIeps as $pGoal)
                                    <div class="p-4 bg-white rounded-xl border border-black/10 shadow-xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-slate-100 text-[#1E2657]">
                                                {{ strtoupper($pGoal['domain'] ?? 'Domain') }}
                                            </span>
                                            <span class="text-xs font-black {{ ($pGoal['progress_percentage'] ?? 0) == 100 ? 'text-[#10B981]' : 'text-[#8B5CF6]' }}">
                                                {{ $pGoal['progress_percentage'] ?? 0 }}% Mastery
                                            </span>
                                        </div>

                                        @if(!empty($pGoal['plop']))
                                            <div class="text-[11px] p-2 bg-slate-50 rounded-lg text-slate-700">
                                                <span class="font-extrabold text-[9px] uppercase tracking-wider text-[#9CA0AF] block">Past Baseline PLOP:</span>
                                                <p class="m-0 line-clamp-3">{{ $pGoal['plop'] }}</p>
                                            </div>
                                        @endif

                                        <div class="text-xs text-[#1E2657] font-semibold leading-relaxed pt-1">
                                            <span class="font-extrabold text-[9px] uppercase tracking-wider text-[#9CA0AF] block mb-0.5">Past Target Objective:</span>
                                            <p class="m-0 text-slate-700 text-xs">{{ Str::limit(strip_tags($pGoal['goal_description'] ?? 'No objective recorded'), 220) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-xs text-slate-400">
                                        No IEP goals recorded in this archived snapshot.
                                    </div>
                                @endforelse
                            </div>

                            <!-- RIGHT COLUMN: ACTIVE SCHOOL YEAR -->
                            <div class="p-5 rounded-2xl bg-blue-50/30 border border-blue-200/60 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-blue-200/60">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-[#10B981] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-bolt"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-black text-[#1E2657] m-0">Active {{ $activeSchoolYear }}</h5>
                                            <span class="text-[10px] text-[#10B981] font-bold">Currently Enrolled & Active</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-[#1E2657]">
                                        {{ $activeIeps->count() }} Active Goals
                                    </span>
                                </div>

                                @forelse($activeIeps as $cGoal)
                                    <div class="p-4 bg-white rounded-xl border border-blue-200 shadow-xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-blue-50 text-[#3B82F6]">
                                                {{ strtoupper($cGoal->domain) }}
                                            </span>
                                            <span class="text-xs font-black {{ $cGoal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#3B82F6]' }}">
                                                {{ $cGoal->progress_percentage }}% Progress
                                            </span>
                                        </div>

                                        @if(!empty($cGoal->plop))
                                            <div class="text-[11px] p-2 bg-blue-50/50 rounded-lg text-slate-700">
                                                <span class="font-extrabold text-[9px] uppercase tracking-wider text-[#3B82F6] block">Current Baseline PLOP:</span>
                                                <p class="m-0 line-clamp-3">{{ $cGoal->plop }}</p>
                                            </div>
                                        @endif

                                        <div class="text-xs text-[#1E2657] font-semibold leading-relaxed pt-1">
                                            <span class="font-extrabold text-[9px] uppercase tracking-wider text-[#3B82F6] block mb-0.5">Current Objective:</span>
                                            <p class="m-0 text-[#1E2657] text-xs">{{ Str::limit(strip_tags($cGoal->goal_description), 220) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-xs text-slate-400">
                                        No active IEP goals created for the current academic year yet.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                    <!-- 3. CLINICAL PROGRESS REPORTS COMPARISON -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-extrabold text-[#1E2657] m-0">
                                Quarterly Clinical Reports & Evaluation Summaries
                            </h4>
                            <span class="text-xs text-[#6B7086] font-medium">
                                Past Academic Reports vs Current Term Evaluations
                            </span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
                            
                            <!-- Archived Reports -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-black/10 space-y-3">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] block">
                                    <i class="fa-solid fa-file-contract mr-1"></i> Archived Reports (SY {{ $archive->school_year }})
                                </span>

                                @forelse($archivedReports as $pRep)
                                    <div class="p-3.5 bg-white rounded-xl border border-black/10 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-[#1E2657] uppercase text-[11px]">
                                                {{ strtoupper(str_replace('_', ' ', $pRep['report_type'] ?? 'Report')) }}
                                            </span>
                                            <span class="text-[10px] text-[#6B7086]">
                                                {{ !empty($pRep['report_date']) ? \Carbon\Carbon::parse($pRep['report_date'])->format('M d, Y') : 'Archived' }}
                                            </span>
                                        </div>
                                        <p class="text-slate-600 m-0 line-clamp-3 leading-relaxed pt-1">
                                            {{ Str::limit(strip_tags($pRep['content'] ?? 'No text record'), 180) }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-xs text-slate-400">
                                        No quarterly reports archived in this rollover snapshot.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Active Reports -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-blue-50/30 border border-blue-200/60 space-y-3">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#3B82F6] block">
                                    <i class="fa-solid fa-file-circle-check mr-1"></i> Active Year Reports ({{ $activeSchoolYear }})
                                </span>

                                @forelse($activeReports as $cRep)
                                    <div class="p-3.5 bg-white rounded-xl border border-blue-200 text-xs space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-[#1E2657] uppercase text-[11px]">
                                                {{ strtoupper(str_replace('_', ' ', $cRep->report_type)) }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $cRep->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ $cRep->status }}
                                            </span>
                                        </div>
                                        <p class="text-slate-600 m-0 line-clamp-3 leading-relaxed pt-1">
                                            {{ Str::limit(strip_tags($cRep->content), 180) }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-xs text-slate-400">
                                        No quarterly progress evaluations filed for the active school year yet.
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                </div>
            @endforeach

        @endif

    </div>

</div>

