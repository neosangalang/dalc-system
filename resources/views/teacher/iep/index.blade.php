@extends('layouts.app')

@section('title', 'IEP & Goal Management')

@section('content')

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9CA0AF; }
</style>

<!-- Main Container -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8" 
     x-data="{ 
        showAddModal: false, 
        activeAccordion: {{ $selectedStudentId ? (int) $selectedStudentId : 'null' }}, 
        activeEditModal: null,
        showDeleteGoalModal: false,
        deleteGoalId: null,
        deleteGoalDomain: '',
        deleteGoalStudentName: '',
        deleteGoalStudentId: null,
        deleteGoalFormAction: '',
        isDeletingGoal: false,
        openDeleteGoalModal(id, studentName, domain, actionUrl, studentId) {
            this.deleteGoalId = id;
            this.deleteGoalStudentName = studentName;
            this.deleteGoalDomain = domain;
            this.deleteGoalFormAction = actionUrl;
            this.deleteGoalStudentId = studentId;
            this.showDeleteGoalModal = true;
            this.isDeletingGoal = false;
        },
        executeDeleteGoal() {
            if (this.isDeletingGoal) return;
            this.isDeletingGoal = true;
            fetch(this.deleteGoalFormAction, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'DELETE',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                const goalId = this.deleteGoalId;
                const studentId = this.deleteGoalStudentId || data.student_id;
                const domain = this.deleteGoalDomain || data.deleted_domain;

                this.isDeletingGoal = false;
                this.showDeleteGoalModal = false;

                // Remove from local domain tracking map
                if (window.studentAssignedDomains && window.studentAssignedDomains[studentId]) {
                    window.studentAssignedDomains[studentId] = window.studentAssignedDomains[studentId].filter(d => d !== domain);
                }

                // Dynamically update domain dropdown options
                if (typeof window.updateAvailableDomains === 'function') {
                    window.updateAvailableDomains();
                }

                // Animate and remove the row/card from DOM
                const row = document.getElementById(`goal-row-${goalId}`);
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        row.remove();
                        const tbody = document.getElementById(`student-goals-tbody-${studentId}`);
                        if (tbody && tbody.querySelectorAll('.goal-card-item, tr').length === 0) {
                            const tableCont = document.getElementById(`student-table-container-${studentId}`);
                            const emptyCard = document.getElementById(`student-empty-card-${studentId}`);
                            if (tableCont) tableCont.classList.add('hidden');
                            if (emptyCard) emptyCard.classList.remove('hidden');
                        }
                    }, 300);
                }

                // Update goal counter badge
                const countBadge = document.getElementById(`student-goals-count-${studentId}`);
                if (countBadge && window.studentAssignedDomains && window.studentAssignedDomains[studentId]) {
                    countBadge.textContent = `${window.studentAssignedDomains[studentId].length} Goals`;
                }
            })
            .catch(err => {
                console.error(err);
                this.isDeletingGoal = false;
                alert('An error occurred while deleting the goal. Please try again.');
            });
        }
     }">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down mb-6">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- HEADER & SEARCH -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] m-0 flex items-center tracking-tight">
                <div class="w-10 h-10 rounded-full bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 flex items-center justify-center text-[#8B5CF6] mr-3 shadow-sm shrink-0">
                    <i class="fa fa-bullseye text-lg"></i>
                </div>
                IEP & Goal Management
            </h1>
            <p class="text-[13px] font-medium text-[#6B7086] mt-1 m-0 sm:ml-14 leading-tight">Manage individualized learning objectives for your students.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Student Selector Dropdown -->
            <div class="relative w-full sm:w-56 shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#3B82F6] pointer-events-none">
                    <i class="fa fa-user-graduate text-xs"></i>
                </span>
                <select id="studentFilterSelect" 
                        onchange="if(this.value) { window.location.href = '{{ route('teacher.iep-goals.index') }}?student_id=' + this.value; } else { window.location.href = '{{ route('teacher.iep-goals.index') }}'; }" 
                        class="w-full border-0 py-2.5 pl-8 pr-8 text-[13px] font-extrabold text-[#1E2657] focus:ring-0 bg-transparent cursor-pointer">
                    <option value="">All Students ({{ $allStudents->count() }})</option>
                    @foreach($allStudents as $studentOption)
                        <option value="{{ $studentOption->id }}" {{ $selectedStudentId == $studentOption->id ? 'selected' : '' }}>
                            {{ $studentOption->first_name }} {{ $studentOption->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative w-full sm:w-56 shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#9CA0AF] pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="studentSearchInput" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] sm:text-sm font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] placeholder:font-medium bg-transparent" placeholder="Search students...">
            </div>

            <button @click="showAddModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 whitespace-nowrap">
                <i class="fa fa-plus mr-2"></i> Add Goal
            </button>
        </div>
    </div>

    @if($selectedStudentId && $myStudents->isNotEmpty())
        @php $currentSelectedStudent = $myStudents->first(); @endphp
        <div class="bg-blue-50/80 border border-blue-200/90 text-[#1E2657] px-4 sm:px-5 py-3 rounded-2xl shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#3B82F6] text-white flex items-center justify-center text-xs font-black shrink-0 shadow-xs">
                    {{ strtoupper(substr($currentSelectedStudent->first_name, 0, 1) . substr($currentSelectedStudent->last_name, 0, 1)) }}
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#3B82F6] block leading-none mb-1">Single Student Filter Active</span>
                    <span class="text-[13px] sm:text-[14px] font-extrabold text-[#1E2657]">
                        Viewing IEP only for <span class="text-[#3B82F6]">{{ $currentSelectedStudent->first_name }} {{ $currentSelectedStudent->last_name }}</span>
                    </span>
                </div>
            </div>
            <a href="{{ route('teacher.iep-goals.index') }}" class="inline-flex items-center px-3.5 py-1.5 bg-white border border-black/15 text-[#6B7086] hover:text-[#1E2657] hover:border-black/30 text-[12px] font-extrabold rounded-xl shadow-2xs transition-all whitespace-nowrap">
                <i class="fa fa-users mr-1.5 text-[#3B82F6]"></i> View All Students ({{ $allStudents->count() }})
            </a>
        </div>
    @endif

    <!-- ACCORDION SECTION -->
    <div class="space-y-4" id="iepAccordion">
        @forelse($myStudents as $student)
            @php
                $studentGoals = $goals->where('student_id', $student->id);
                $activeCount = $studentGoals->where('status', 'in_progress')->count();
                
                // Bulletproof filter to bypass boolean vs integer mismatch in DB
                $studentUnreadCount = $studentGoals->flatMap->comments->filter(function($comment) {
                    return $comment->user_id != Auth::id() && !$comment->is_teacher_read;
                })->count();
            @endphp
            
            <!-- Student Accordion Wrapper with Reactive Unread State -->
            <div x-data="{ studentUnreadCount: {{ (int) $studentUnreadCount }} }" 
                 class="bg-white rounded-2xl border border-black/15 shadow-sm overflow-hidden transition-all duration-200 student-accordion-item" 
                 data-student-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
                
                <button @click="activeAccordion = activeAccordion === {{ $student->id }} ? null : {{ $student->id }}" 
                        class="w-full px-4 py-4 sm:px-6 sm:py-5 flex flex-wrap sm:flex-nowrap items-center justify-between text-left transition-colors focus:outline-none"
                        :class="activeAccordion === {{ $student->id }} ? 'bg-slate-50/50 border-b border-black/10' : 'hover:bg-slate-50'">
                    
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-0 w-full sm:w-auto">
                        <div class="relative w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center text-white font-extrabold text-sm sm:text-base shadow-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shrink-0 border border-black/10">
                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            
                            <!-- Reactive Red Dot Indicator on the Avatar -->
                            <span x-show="studentUnreadCount > 0" 
                                  x-transition:leave="transition ease-in duration-200"
                                  x-transition:leave-start="opacity-100 scale-100"
                                  x-transition:leave-end="opacity-0 scale-75"
                                  class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <h6 class="text-[15px] sm:text-[16px] font-extrabold text-[#1E2657] m-0">{{ $student->first_name }} {{ $student->last_name }}</h6>
                            <p class="text-[10px] sm:text-[11px] font-bold text-[#9CA0AF] m-0 mt-0.5"><i class="fa fa-id-badge text-[#3B82F6] mr-1"></i> Assigned Student</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-start sm:justify-end">
                        
                        <!-- Reactive 'New Message' Badge on the Header -->
                        <span x-show="studentUnreadCount > 0" 
                              x-transition:leave="transition ease-in duration-200"
                              x-transition:leave-start="opacity-100 scale-100"
                              x-transition:leave-end="opacity-0 scale-75"
                              class="px-2 py-1 sm:px-3 sm:py-1.5 rounded-md bg-red-500 text-white text-[9px] font-extrabold uppercase tracking-widest shadow-sm animate-pulse">
                            <i class="fa fa-envelope mr-1"></i> New Message
                        </span>

                        <span id="student-goals-count-{{ $student->id }}" class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-md border border-black/10 bg-[#F4F6FA] text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest shadow-sm">{{ $studentGoals->count() }} Goals</span>
                        
                        @if($activeCount > 0)
                            <span class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-md bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20 text-[9px] font-extrabold uppercase tracking-widest shadow-sm"><i class="fa fa-spinner fa-spin mr-1"></i> {{ $activeCount }} Active</span>
                        @endif
                        <i class="fa fa-chevron-down text-[#9CA0AF] transition-transform ml-auto sm:ml-2" :class="activeAccordion === {{ $student->id }} ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                
                <div x-show="activeAccordion === {{ $student->id }}" x-collapse x-cloak>
                    <div class="p-4 sm:p-0 bg-white border-t border-black/10">
                        
                        <!-- MODULAR EXPANDABLE GOALS CONTAINER -->
                        <div id="student-table-container-{{ $student->id }}" class="p-4 sm:p-6 bg-[#F8FAFC]/40 {{ $studentGoals->count() > 0 ? '' : 'hidden' }}">
                            <div id="student-goals-tbody-{{ $student->id }}" class="space-y-3 sm:space-y-3.5" x-data="{ openGoalId: null }">
                                @foreach($studentGoals as $goal)
                                    @php
                                        // Bulletproof unread check for specific goal
                                        $goalUnreadCount = $goal->comments->filter(function($comment) {
                                            return $comment->user_id != Auth::id() && !$comment->is_teacher_read;
                                        })->count();

                                        // Parse goal_description into structured objectives and activities
                                        $rawDesc = $goal->goal_description ?? '';
                                        $sections = preg_split('/-{4,}/', $rawDesc);
                                        $parsedObjectives = [];

                                        foreach ($sections as $sec) {
                                            $sec = trim($sec);
                                            if (empty($sec)) continue;

                                            $objText = '';
                                            $actText = '';

                                            if (preg_match('/ACTIVITIES:/i', $sec)) {
                                                $parts = preg_split('/ACTIVITIES:/i', $sec, 2);
                                                $objText = trim(preg_replace('/^OBJECTIVE:\s*/i', '', $parts[0]));
                                                $actText = trim($parts[1] ?? '');
                                            } else {
                                                $objText = trim(preg_replace('/^OBJECTIVE:\s*/i', '', $sec));
                                            }

                                            $activities = [];
                                            if (!empty($actText)) {
                                                $lines = preg_split('/\r\n|\r|\n/', $actText);
                                                foreach ($lines as $line) {
                                                    $line = trim($line);
                                                    if (empty($line)) continue;
                                                    if (str_starts_with($line, '- ')) {
                                                        $line = preg_replace('/^-\s*/', '[ ] ', $line);
                                                    }
                                                    $isChecked = (bool) preg_match('/^\[[xX]\]/', $line);
                                                    $cleanLine = trim(preg_replace('/^\[[xX\s]*\]\s*/', '', $line));
                                                    if (!empty($cleanLine)) {
                                                        $activities[] = [
                                                            'text' => $cleanLine,
                                                            'checked' => $isChecked
                                                        ];
                                                    }
                                                }
                                            }

                                            if (!empty($objText) || !empty($activities)) {
                                                $parsedObjectives[] = [
                                                    'objective' => $objText ?: 'Objective Statement',
                                                    'activities' => $activities
                                                ];
                                            }
                                        }

                                        if (empty($parsedObjectives)) {
                                            $parsedObjectives[] = [
                                                'objective' => $rawDesc ?: 'No objective statement recorded.',
                                                'activities' => []
                                            ];
                                        }

                                        $primaryObjective = $parsedObjectives[0]['objective'] ?? '';
                                        // Brief summary of objective for default view
                                        $objectiveSummary = \Illuminate\Support\Str::limit($primaryObjective, 90, '...');
                                        $totalObjectivesCount = count($parsedObjectives);

                                        // Domain visual style mapping
                                        $domainConfig = match($goal->domain) {
                                            'Language-Communication' => [
                                                'badge' => 'bg-[#3B82F6]/10 text-[#3B82F6] border-[#3B82F6]/20',
                                                'icon' => 'fa-comments',
                                                'border' => 'border-l-[#3B82F6]',
                                            ],
                                            'Self-Help' => [
                                                'badge' => 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20',
                                                'icon' => 'fa-hands-holding-child',
                                                'border' => 'border-l-[#10B981]',
                                            ],
                                            'Perceptuo-Cognitive' => [
                                                'badge' => 'bg-[#8B5CF6]/10 text-[#8B5CF6] border-[#8B5CF6]/20',
                                                'icon' => 'fa-brain',
                                                'border' => 'border-l-[#8B5CF6]',
                                            ],
                                            'Socio-Emotional' => [
                                                'badge' => 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/20',
                                                'icon' => 'fa-heart',
                                                'border' => 'border-l-[#F59E0B]',
                                            ],
                                            'Psychomotor' => [
                                                'badge' => 'bg-rose-50 text-rose-600 border-rose-200',
                                                'icon' => 'fa-child-reaching',
                                                'border' => 'border-l-rose-500',
                                            ],
                                            default => [
                                                'badge' => 'bg-[#F4F6FA] text-[#6B7086] border-black/10',
                                                'icon' => 'fa-bullseye',
                                                'border' => 'border-l-slate-400',
                                            ],
                                        };
                                    @endphp

                                    <!-- MODULAR GOAL CARD: Expandable Layout -->
                                    <div id="goal-row-{{ $goal->id }}" 
                                         x-data="{ 
                                             goalUnread: {{ $goalUnreadCount > 0 ? 'true' : 'false' }}, 
                                             unreadCount: {{ (int) $goalUnreadCount }} 
                                         }" 
                                         class="goal-card-item bg-white border border-black/10 rounded-2xl shadow-xs overflow-hidden transition-all duration-200 hover:border-black/20 hover:shadow-sm"
                                         :class="openGoalId === {{ $goal->id }} ? 'ring-1 ring-[#3B82F6]/20 shadow-sm' : ''">

                                        <!-- DEFAULT (COLLAPSED) VIEW: INTERACTIVE HEADER -->
                                        <div @click="openGoalId = openGoalId === {{ $goal->id }} ? null : {{ $goal->id }}" 
                                             class="w-full px-4 sm:px-5 py-3.5 sm:py-4 flex flex-col md:flex-row md:items-center justify-between gap-3 text-left cursor-pointer transition-colors select-none"
                                             :class="openGoalId === {{ $goal->id }} ? 'bg-slate-50/80 border-b border-black/10' : 'hover:bg-slate-50/60'">
                                            
                                            <!-- Left: Domain Badge + Brief Summary of Objective -->
                                            <div class="flex items-start sm:items-center gap-3 flex-1 min-w-0">
                                                <!-- Domain Badge with Icon -->
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-widest border shadow-xs shrink-0 {{ $domainConfig['badge'] }}">
                                                    <i class="fa {{ $domainConfig['icon'] }} text-[10px]"></i>
                                                    {{ str_replace('-', ' - ', $goal->domain) }}
                                                </span>

                                                <!-- Brief Summary of Objective -->
                                                <div class="flex-1 min-w-0 pr-2">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <p class="text-[13px] sm:text-[14px] font-bold text-[#1E2657] m-0 truncate leading-snug" title="{{ $primaryObjective }}">
                                                            {{ $objectiveSummary }}
                                                        </p>
                                                        
                                                        @if($totalObjectivesCount > 1)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#F4F6FA] text-[#6B7086] border border-black/10 text-[9px] font-extrabold shrink-0" title="{{ $totalObjectivesCount }} Objectives Defined">
                                                                <i class="fa fa-layer-group text-[8px] mr-1"></i> {{ $totalObjectivesCount }} Objectives
                                                            </span>
                                                        @endif

                                                        <!-- Reactive 'New Reply' Badge on Goal Header -->
                                                        <span x-show="goalUnread" 
                                                              x-transition:leave="transition ease-in duration-200"
                                                              x-transition:leave-start="opacity-100 scale-100"
                                                              x-transition:leave-end="opacity-0 scale-75"
                                                              class="inline-flex items-center px-2 py-0.5 rounded-md text-[8px] font-extrabold bg-red-500 text-white uppercase tracking-widest shadow-xs shrink-0 animate-pulse">
                                                            <i class="fa-solid fa-bell mr-1"></i> New Reply
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right: Progress, Status & Chevron Toggle -->
                                            <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-black/5">
                                                <!-- Compact Progress Indicator -->
                                                <div class="flex items-center gap-2">
                                                    <div class="w-16 sm:w-20 h-1.5 bg-[#E8E5DC] rounded-full overflow-hidden shadow-inner hidden sm:block">
                                                        <div class="h-full rounded-full transition-all duration-700 ease-out {{ $goal->progress_percentage == 100 ? 'bg-[#10B981]' : 'bg-[#3B82F6]' }}" style="width: {{ $goal->progress_percentage }}%;"></div>
                                                    </div>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#3B82F6]' }}">
                                                        {{ $goal->progress_percentage == 100 ? 'Achieved ✓' : $goal->progress_percentage . '%' }}
                                                    </span>
                                                </div>

                                                <!-- Compact Status Badge -->
                                                @if($goal->status === 'in_progress')
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#F59E0B]/10 text-[#F59E0B] uppercase tracking-widest border border-[#F59E0B]/20 shrink-0">
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#10B981]/10 text-[#10B981] uppercase tracking-widest border border-[#10B981]/20 shrink-0">
                                                        Achieved
                                                    </span>
                                                @endif

                                                <!-- Expand / Collapse Indicator Button -->
                                                <button type="button" 
                                                        @click.stop="openGoalId = openGoalId === {{ $goal->id }} ? null : {{ $goal->id }}"
                                                        class="w-7 h-7 rounded-lg bg-white border border-black/10 flex items-center justify-center text-[#9CA0AF] hover:text-[#1E2657] transition-all shrink-0 ml-1 shadow-xs"
                                                        aria-label="Expand goal details">
                                                    <i class="fa fa-chevron-down text-xs transition-transform duration-300" :class="openGoalId === {{ $goal->id }} ? 'rotate-180' : ''"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- EXPANDED VIEW: REVEALED ON CLICK -->
                                        <div x-show="openGoalId === {{ $goal->id }}" x-collapse x-cloak>
                                            <div class="p-4 sm:p-6 bg-white space-y-5">
                                                
                                                <!-- Baseline Present Level of Performance (PLOP) if available -->
                                                @if(!empty($goal->plop))
                                                    <div class="p-3.5 sm:p-4 bg-[#F8FAFC] border border-black/10 rounded-xl shadow-2xs">
                                                        <div class="flex items-center gap-1.5 mb-1.5">
                                                            <i class="fa-solid fa-clipboard-user text-[#8B5CF6] text-xs"></i>
                                                            <span class="text-[10px] font-extrabold text-[#8B5CF6] uppercase tracking-wider">Present Level of Performance (Baseline)</span>
                                                        </div>
                                                        <p class="text-[12px] font-medium text-[#1E2657] m-0 leading-relaxed whitespace-pre-line">{{ $goal->plop }}</p>
                                                    </div>
                                                @endif

                                                <!-- Full Objectives & Structured Activities -->
                                                <div class="space-y-4">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-[11px] font-extrabold text-[#6B7086] uppercase tracking-wider flex items-center">
                                                            <i class="fa-solid fa-bullseye text-[#3B82F6] mr-1.5"></i> Detailed Objectives & Activities
                                                        </span>
                                                        <span class="text-[10px] font-bold text-[#9CA0AF]">
                                                            {{ $totalObjectivesCount }} {{ \Illuminate\Support\Str::plural('objective', $totalObjectivesCount) }}
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-3.5">
                                                        @foreach($parsedObjectives as $idx => $objItem)
                                                            <div class="border border-black/10 rounded-xl p-4 bg-white shadow-2xs border-l-4 {{ $domainConfig['border'] }}">
                                                                <!-- Objective Header & Statement -->
                                                                <div class="mb-3">
                                                                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#9CA0AF] block mb-1">
                                                                        Objective #{{ $idx + 1 }}
                                                                    </span>
                                                                    <p class="text-[13px] sm:text-[14px] font-extrabold text-[#1E2657] m-0 leading-relaxed">
                                                                        {{ $objItem['objective'] }}
                                                                    </p>
                                                                </div>

                                                                <!-- Activities Checklist for this Objective -->
                                                                @if(!empty($objItem['activities']))
                                                                    <div class="mt-3 pt-3 border-t border-black/5">
                                                                        <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#6B7086] block mb-2 flex items-center">
                                                                            <i class="fa-solid fa-list-check text-[#3B82F6] mr-1"></i> Activities & Interventions:
                                                                        </span>
                                                                        <div class="space-y-1.5">
                                                                            @foreach($objItem['activities'] as $act)
                                                                                <div class="flex items-start gap-2.5 px-3 py-2 rounded-lg {{ $act['checked'] ? 'bg-[#F4F6FA] border border-black/5 text-[#9CA0AF]' : 'bg-white border border-black/10 text-[#1E2657]' }}">
                                                                                    @if($act['checked'])
                                                                                        <div class="w-4 h-4 rounded bg-[#10B981] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                                                                                            <i class="fa fa-check text-[9px]"></i>
                                                                                        </div>
                                                                                        <span class="text-[12px] font-medium line-through leading-tight pt-[1px]">{{ $act['text'] }}</span>
                                                                                    @else
                                                                                        <div class="w-4 h-4 rounded border border-black/20 bg-white shrink-0 mt-0.5 shadow-2xs"></div>
                                                                                        <span class="text-[12px] font-medium leading-tight pt-[1px]">{{ $act['text'] }}</span>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="mt-2 text-[11px] font-medium text-[#9CA0AF] italic">
                                                                        No specific activities defined for this objective.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Full Progress Bar & Status Section -->
                                                <div class="p-4 bg-[#F8FAFC] border border-black/10 rounded-xl shadow-2xs">
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                                        <span class="text-[10px] font-extrabold text-[#6B7086] uppercase tracking-wider flex items-center">
                                                            <i class="fa-solid fa-chart-line text-[#3B82F6] mr-1.5"></i> Progress Tracking & Mastery
                                                        </span>
                                                        <span class="text-[12px] font-extrabold uppercase tracking-wider {{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#3B82F6]' }}">
                                                            {{ $goal->progress_percentage }}% Mastery Level
                                                        </span>
                                                    </div>

                                                    <div class="w-full h-2.5 bg-[#E8E5DC] rounded-full overflow-hidden shadow-inner mb-2">
                                                        <div class="h-full rounded-full transition-all duration-700 ease-out {{ $goal->progress_percentage == 100 ? 'bg-[#10B981]' : 'bg-[#3B82F6]' }}" style="width: {{ $goal->progress_percentage }}%;"></div>
                                                    </div>

                                                    <div class="flex items-center justify-between text-[11px] font-medium text-[#6B7086]">
                                                        <span>Status: <strong class="{{ $goal->status === 'achieved' ? 'text-[#10B981]' : 'text-[#F59E0B]' }}">{{ $goal->status === 'achieved' ? 'Achieved / Completed' : 'In Progress' }}</strong></span>
                                                        <span>Last updated: {{ $goal->updated_at ? $goal->updated_at->diffForHumans() : 'Recently' }}</span>
                                                    </div>
                                                </div>

                                                <!-- Actions Footer -->
                                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-3 border-t border-black/10">
                                                    <div class="flex items-center gap-2">
                                                        <!-- Discussion comments info / counter -->
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#F4F6FA] border border-black/10 rounded-xl text-[11px] font-bold text-[#6B7086]">
                                                            <i class="fa-solid fa-comments text-[#8B5CF6]"></i>
                                                            {{ $goal->comments->count() }} {{ \Illuminate\Support\Str::plural('Note', $goal->comments->count()) }}
                                                        </span>
                                                        <span class="text-[11px] font-medium text-[#9CA0AF] hidden md:inline">
                                                            Click 'Edit / Track' to update checklist & review guardian notes
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center gap-2 justify-end">
                                                        <!-- Edit / Track Button -->
                                                        <button type="button"
                                                                @click="activeEditModal = {{ $goal->id }}; 
                                                                    if(goalUnread) { 
                                                                        goalUnread = false; 
                                                                        studentUnreadCount = Math.max(0, studentUnreadCount - unreadCount);
                                                                        unreadCount = 0;
                                                                        fetch('{{ route('teacher.iep-goals.mark-read', $goal->id) }}', { 
                                                                            method: 'POST', 
                                                                            headers: { 
                                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                                                                                'Accept': 'application/json',
                                                                                'Content-Type': 'application/json'
                                                                            } 
                                                                        })
                                                                        .then(response => {
                                                                            if (!response.ok) throw new Error('Network response was not ok');
                                                                            return response.json();
                                                                        })
                                                                        .then(data => console.log('Successfully marked as read!'))
                                                                        .catch(err => console.error('Database Update Failed:', err)); 
                                                                    }" 
                                                                class="inline-flex justify-center items-center px-4 py-2 bg-[#3B82F6] text-white text-[12px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-xs cursor-pointer">
                                                            <i class="fa fa-edit mr-1.5"></i> Edit / Track Progress
                                                        </button>

                                                        <!-- Delete Goal Button -->
                                                        <button type="button" 
                                                                @click="openDeleteGoalModal({{ $goal->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', '{{ addslashes($goal->domain) }}', '{{ route('teacher.iep-goals.destroy', $goal->id) }}', {{ $student->id }})" 
                                                                class="inline-flex items-center justify-center px-3.5 py-2 bg-white border border-rose-200 text-rose-500 rounded-xl hover:bg-rose-50 transition-colors shadow-xs text-[12px] font-bold cursor-pointer"
                                                                title="Delete Goal">
                                                            <i class="fa fa-trash mr-1.5 sm:mr-0"></i> <span class="sm:hidden text-xs">Delete</span>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                            <div id="student-empty-card-{{ $student->id }}" class="text-center py-10 sm:py-12 px-6 bg-white rounded-b-2xl {{ $studentGoals->count() > 0 ? 'hidden' : '' }}">
                                <div class="w-14 h-14 rounded-full bg-[#F4F6FA] border border-black/5 shadow-sm flex items-center justify-center mx-auto mb-4">
                                    <i class="fa fa-clipboard-list text-xl text-[#9CA0AF]"></i>
                                </div>
                                <h6 class="text-[14px] font-extrabold text-[#1E2657] mb-1 m-0">No Active Goals</h6>
                                <p class="text-[12px] font-medium text-[#6B7086] mb-5 m-0">There are currently no IEP goals assigned to {{ $student->first_name }}.</p>
                                <button type="button" @click="activeAccordion = null; showAddModal = true; setTimeout(() => { $refs.studentSelect.value = '{{ $student->id }}'; $refs.studentSelect.dispatchEvent(new Event('change')); }, 100);" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 text-[#6B7086] hover:text-[#3B82F6] hover:border-[#3B82F6]/30 text-[12px] font-extrabold rounded-xl transition-colors shadow-sm cursor-pointer">
                                    <i class="fa fa-plus mr-1.5"></i> Create First Goal
                                </button>
                            </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 sm:py-16 px-6 bg-white rounded-2xl shadow-sm border border-black/15">
                <i class="fa fa-users-slash text-4xl sm:text-5xl text-[#E8E5DC] mb-4 block"></i>
                <h5 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No Students Found</h5>
                <p class="text-[13px] font-medium text-[#6B7086] m-0">You have no students assigned to your roster yet.</p>
            </div>
        @endforelse
    </div>

    <div id="no-search-results" class="text-center py-12 sm:py-16 px-6 bg-white rounded-2xl shadow-sm border border-black/15 mt-4" style="display: none;">
        <i class="fa fa-search text-4xl sm:text-5xl text-[#E8E5DC] mb-4 block"></i>
        <h5 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No matches found</h5>
        <p class="text-[13px] font-medium text-[#6B7086] m-0">We couldn't find any students matching that name.</p>
    </div>

    <!-- ========================================== -->
    <!-- EDIT/TRACK PROGRESS MODALS -->
    <!-- ========================================== -->
    @foreach($myStudents as $student)
        @foreach($goals->where('student_id', $student->id) as $goal)
            <div x-show="activeEditModal === {{ $goal->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="activeEditModal === {{ $goal->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="activeEditModal = null"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="activeEditModal === {{ $goal->id }}" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-black/15 relative z-10">
                        
                        <div class="flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh]">
                            
                            <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center shrink-0">
                                <h5 class="text-base font-extrabold text-[#1E2657] m-0 leading-tight">Track Progress: {{ $student->first_name }}</h5>
                                <button type="button" @click="activeEditModal = null" class="text-[#9CA0AF] hover:text-[#1E2657] ml-4"><i class="fa fa-times text-xl"></i></button>
                            </div>
                            
                            <div class="p-5 sm:p-6 flex-1 overflow-y-auto custom-scrollbar bg-white">
                                
                                <form action="{{ route('teacher.iep-goals.update', $goal->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-6 interactive-checklist-container" data-goal-domain="{{ $goal->domain }}">
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest m-0">Interactive Activity Tracker</label>
                                            <button type="button" class="text-[#6B7086] hover:text-[#3B82F6] text-[11px] font-extrabold transition-colors toggle-edit-goal-btn flex items-center bg-white px-3 py-1.5 rounded-lg border border-black/15 shadow-sm cursor-pointer">
                                                <i class="fa fa-pen mr-1.5"></i> <span class="toggle-btn-label">Edit Goal Fields</span>
                                            </button>
                                        </div>
                                        
                                        <!-- Visual Checklist with interactive checkboxes for progress tracking -->
                                        <div class="visual-checklist p-4 border border-black/15 rounded-2xl bg-white shadow-sm max-h-[400px] overflow-y-auto text-[13px] text-[#6B7086] custom-scrollbar"></div>
                                        
                                        <!-- Structured UI Editor for editing objectives & activities with dedicated UI fields -->
                                        <div class="goal-structured-editor hidden mt-3 space-y-4 p-4 border border-black/15 rounded-2xl bg-[#F8FAFC]">
                                            
                                            <!-- PLOP Retention & AI Suggestions in Goal Editing Phase -->
                                            <div class="p-4 bg-white border border-black/15 rounded-2xl shadow-sm">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                                                    <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-wider m-0 flex items-center">
                                                        <i class="fa-solid fa-clipboard-user text-[#8B5CF6] mr-1.5"></i> Present Level of Performance (PLOP)
                                                    </label>
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-[#8B5CF6] bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-200 w-fit">
                                                        <i class="fa-solid fa-layer-group text-[9px]"></i> Domain: {{ $goal->domain }}
                                                    </span>
                                                </div>
                                                <p class="text-[11px] font-medium text-[#6B7086] mb-2.5 m-0 leading-relaxed">The student's baseline performance context. You can edit this directly and request additional AI suggestions below without restarting.</p>
                                                
                                                <textarea name="plop" class="edit-plop-input w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[12px] font-medium text-[#1E2657] focus:border-[#8B5CF6] focus:ring-[#8B5CF6] shadow-sm bg-white mb-2.5 custom-scrollbar" rows="3" placeholder="Enter Present Level of Performance...">{{ $goal->plop }}</textarea>
                                                
                                                <button type="button" class="generate-edit-ai-btn w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#8B5CF6] text-white text-[12px] font-extrabold rounded-xl hover:bg-purple-600 transition-colors shadow-sm cursor-pointer">
                                                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Request Additional AI Suggestions
                                                </button>
                                                
                                                <!-- AI Suggestions Results in Edit Modal -->
                                                <div class="edit-ai-results-container mt-3 hidden border-t border-black/10 pt-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-[10px] font-extrabold text-[#10B981] uppercase tracking-widest flex items-center">
                                                            <i class="fa-solid fa-lightbulb mr-1.5"></i> Suggested Strategies (Based on PLOP)
                                                        </span>
                                                        <button type="button" class="dismiss-edit-ai-btn text-[#9CA0AF] hover:text-slate-600 text-xs font-bold transition-colors">
                                                            <i class="fa fa-times"></i> Close
                                                        </button>
                                                    </div>
                                                    <div class="edit-ai-results-list space-y-2.5 max-h-[260px] overflow-y-auto custom-scrollbar pr-1"></div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pb-2 border-b border-black/10">
                                                <span class="text-[11px] font-extrabold text-[#1E2657] uppercase tracking-wider flex items-center">
                                                    <i class="fa-solid fa-pen-to-square text-[#3B82F6] mr-1.5"></i> Edit Objectives & Activities
                                                </span>
                                                <button type="button" class="add-edit-objective-btn inline-flex items-center px-2.5 py-1 bg-white border border-[#3B82F6]/30 text-[#3B82F6] hover:bg-[#3B82F6]/10 text-[10px] font-extrabold rounded-lg transition-colors shadow-sm cursor-pointer">
                                                    <i class="fa fa-plus mr-1"></i> Add Objective
                                                </button>
                                            </div>
                                            <div class="edit-objectives-cards-list space-y-3"></div>
                                        </div>

                                        <!-- Hidden Raw Textarea synced with the structured editor and checklist -->
                                        <textarea name="goal_description" class="hidden goal-raw-text" required>{{ $goal->goal_description }}</textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 bg-[#F4F6FA] p-5 rounded-2xl border border-black/10">
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Calculated Progress (%)</label>
                                            <input type="number" name="progress_percentage" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm goal-progress-input bg-white" min="0" max="100" value="{{ $goal->progress_percentage }}" readonly required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</label>
                                            <!-- Automated Status Tracking: manual select dropdown removed -->
                                            <input type="hidden" name="status" class="goal-status-hidden" value="{{ $goal->progress_percentage == 100 ? 'achieved' : 'in_progress' }}">
                                            <div class="goal-status-display flex items-center gap-2 px-4 py-2.5 rounded-xl border font-extrabold text-[13px] shadow-sm select-none transition-all {{ $goal->progress_percentage == 100 ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-amber-200 bg-amber-50 text-amber-600' }}">
                                                @if($goal->progress_percentage == 100)
                                                    <i class="fa fa-circle-check text-emerald-500 text-[14px]"></i>
                                                    <span>Complete</span>
                                                @else
                                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                    <span>In Progress</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0 pt-2 mb-2">
                                        <button type="button" @click="activeEditModal = null" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center justify-center"><i class="fa fa-save mr-1.5"></i> Save Progress</button>
                                    </div>
                                </form>
                                
                                <div class="mt-6 border-t border-dashed border-black/15 pt-6">
                                    @include('components.comment-thread', ['item' => $goal])
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    <!-- ========================================== -->
    <!-- CREATE IEP GOAL MODAL -->
    <!-- ========================================== -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showAddModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showAddModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-black/15 relative z-10">
                
                <form action="{{ route('teacher.iep-goals.store') }}" method="POST" class="m-0 flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh]">
                    @csrf
                    
                    <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center shrink-0">
                        <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-plus-circle text-[#3B82F6] mr-2"></i> Assign New IEP Goal</h5>
                        <button type="button" @click="showAddModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                    </div>
                    
                    <div class="p-5 sm:p-6 flex-1 overflow-y-auto custom-scrollbar bg-white">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-6">
                            <div>
                                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Select Student</label>
                                <select x-ref="studentSelect" name="student_id" id="iep-student-select" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm" required>
                                    <option value="" disabled {{ !$selectedStudentId ? 'selected' : '' }}>Choose a student...</option>
                                    @foreach($allStudents as $student)
                                        <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>{{ $student->first_name }} {{ $student->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Development Domain</label>
                                <select id="domain-select" name="domain" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm" required>
                                    <option value="" disabled selected>Select a student first...</option>
                                    <option value="Perceptuo-Cognitive">Perceptuo - Cognitive</option>
                                    <option value="Socio-Emotional">Socio - Emotional</option>
                                    <option value="Psychomotor">Psychomotor</option>
                                    <option value="Language-Communication">Language - Communication</option>
                                    <option value="Self-Help">Self - Help</option>
                                </select>
                            </div>
                            <div id="domain-limit-warning" class="hidden col-span-1 sm:col-span-2 bg-amber-50 border border-amber-200 text-amber-800 p-3.5 rounded-xl text-xs font-bold flex items-center gap-2">
                                <i class="fa fa-info-circle text-amber-500 text-sm shrink-0"></i>
                                <span>All 5 developmental domains have already been assigned for this student. Delete an existing goal if you need to reassign a domain.</span>
                            </div>
                        </div>

                        <!-- STRATEGY & AI BUILDER SECTION -->
                        <div class="mb-3">
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest m-0">
                                <i class="fa-solid fa-wand-magic-sparkles text-[#8B5CF6] mr-1.5"></i> Goal Strategy Options
                            </label>
                        </div>

                        <!-- AI BUILDER SECTION -->
                        <div class="p-5 mb-6 bg-[#F4F6FA] border border-dashed border-[#3B82F6]/30 rounded-2xl relative overflow-hidden shadow-inner">
                            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none hidden sm:block">
                                <i class="fa-solid fa-robot text-8xl text-[#1E2657]"></i>
                            </div>
                            <div class="relative z-10">
                                <h6 class="text-[13px] sm:text-[14px] font-extrabold text-[#8B5CF6] mb-1 flex items-center m-0">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> AI IEP Builder
                                </h6>
                                <p class="text-[12px] font-medium text-[#9CA0AF] mb-5 m-0">Input specific baseline observations to generate measurable objectives and sustainable activities.</p>
                                
                                <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5">Present Level of Performance</label>
                                <textarea name="plop" id="ai-present-level" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm mb-4 custom-scrollbar" rows="4" required placeholder="e.g., Can sit for 5-10 minutes during group time with verbal cues; struggles with independent task transitions..."></textarea>
                                
                                <button type="button" id="generate-goals-btn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#8B5CF6] text-white text-[13px] font-extrabold rounded-xl hover:bg-purple-600 transition-colors shadow-sm">
                                    <i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities
                                </button>

                                <!-- AI Clinical Disclaimer & Ethics Notice -->
                                <div class="mt-2.5 flex items-center justify-between text-[11px] text-[#6B7086] px-1">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa fa-shield-halved text-[#8B5CF6]"></i>
                                        <span>AI SPED Assistant Draft &bull; Review against clinical baselines</span>
                                    </span>
                                    <span class="text-[10px] text-[#9CA0AF] hidden sm:inline">Zero-PII Proxy &bull; Groq Cloud</span>
                                </div>

                                <div id="ai-results-container" class="mt-5 hidden">
                                    <hr class="border-black/10 my-4">
                                    <p class="text-[10px] font-extrabold text-[#10B981] uppercase tracking-widest mb-3 m-0"><i class="fa-solid fa-check mr-1"></i> Suggested IEP Strategies:</p>
                                    
                                    <div class="w-full border border-black/15 rounded-2xl overflow-hidden shadow-sm">
                                        <table class="w-full text-left bg-transparent sm:bg-white block sm:table">
                                            <thead class="hidden sm:table-header-group bg-white border-b border-black/10">
                                                <tr>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[40%]">Objective</th>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[45%]">Activities</th>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[15%] text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="goals-list-tbody" class="block sm:table-row-group divide-y-0 sm:divide-y divide-black/5 space-y-3 sm:space-y-0 p-2 sm:p-0 bg-[#F4F6FA] sm:bg-transparent"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OBJECTIVES & ACTIVITIES STRUCTURED BUILDER -->
                        <div class="border-t border-black/10 pt-5">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] uppercase tracking-widest m-0 flex items-center">
                                        <i class="fa-solid fa-bullseye text-[#3B82F6] mr-2 text-sm"></i> Objectives & Activities Area
                                    </label>
                                    <p class="text-[11px] font-medium text-[#6B7086] m-0 mt-0.5">Edit generated objectives or manually add customized activities in distinct fields.</p>
                                </div>
                                <button type="button" id="add-objective-card-btn" class="inline-flex items-center px-3 py-1.5 bg-white border border-[#3B82F6]/30 text-[#3B82F6] hover:bg-[#3B82F6]/10 text-xs font-extrabold rounded-xl transition-colors shadow-sm cursor-pointer shrink-0">
                                    <i class="fa fa-plus mr-1.5"></i> Add Objective
                                </button>
                            </div>

                            <!-- Empty State -->
                            <div id="objectives-empty-state" class="p-6 text-center border-2 border-dashed border-black/15 rounded-2xl bg-[#F8FAFC]">
                                <div class="w-10 h-10 rounded-full bg-white border border-black/10 flex items-center justify-center mx-auto mb-2 text-[#9CA0AF] shadow-sm">
                                    <i class="fa-solid fa-layer-group text-sm"></i>
                                </div>
                                <p class="text-[12px] font-extrabold text-[#1E2657] m-0">No Objectives Staged Yet</p>
                                <p class="text-[11px] font-medium text-[#9CA0AF] m-0 mt-1 mb-3">Generate strategies with AI above, or input your custom objectives manually from scratch.</p>
                                <button type="button" id="empty-state-manual-btn" class="inline-flex items-center px-3.5 py-1.5 bg-[#3B82F6] text-white text-xs font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm cursor-pointer">
                                    <i class="fa-solid fa-plus mr-1.5"></i> Add Custom Objective
                                </button>
                            </div>

                            <!-- Dynamic Objectives Cards Container -->
                            <div id="objectives-cards-container" class="space-y-4"></div>

                            <!-- Hidden compiled goal_description that strictly preserves the DB/PDF/Guardian format -->
                            <textarea id="final-goal-input" name="goal_description" class="hidden" required></textarea>
                        </div>
                    </div>
                    
                    <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                        <button type="submit" id="save-iep-goal-submit-btn" class="w-full sm:w-auto px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center justify-center"><i class="fa fa-save mr-1.5"></i> Save IEP Goal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MANDATORY IEP GOAL DELETION CONFIRMATION MODAL -->
    <!-- ========================================== -->
    <div x-show="showDeleteGoalModal" x-cloak class="fixed inset-0 z-[120] overflow-y-auto text-left" aria-labelledby="delete-goal-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteGoalModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="if(!isDeletingGoal) showDeleteGoalModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteGoalModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-black/15 p-6 relative z-50">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-base font-extrabold text-[#1E2657] m-0" id="delete-goal-modal-title">Delete IEP Goal</h4>
                        <p class="text-xs text-[#6B7086] mt-1.5 font-medium leading-relaxed">
                            Are you sure you want to delete the IEP Goal for developmental domain <strong class="text-rose-600" x-text="deleteGoalDomain"></strong> for <strong class="text-[#1E2657]" x-text="deleteGoalStudentName"></strong>?
                        </p>
                        <p class="text-[11px] text-rose-600 font-bold mt-2 bg-rose-50 border border-rose-200/60 rounded-lg p-2.5">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> All recorded objectives, activities, checklist progress, and guardian comments under this domain will be permanently removed.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-4 border-t border-black/5">
                    <button type="button" 
                            @click="showDeleteGoalModal = false" 
                            :disabled="isDeletingGoal"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#6B7086] hover:text-[#1E2657] text-xs font-extrabold rounded-xl transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" 
                            @click="executeDeleteGoal()" 
                            :disabled="isDeletingGoal"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold rounded-xl transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <template x-if="isDeletingGoal">
                            <span class="flex items-center"><i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Deleting...</span>
                        </template>
                        <template x-if="!isDeletingGoal">
                            <span class="flex items-center"><i class="fa-solid fa-trash mr-1.5"></i> Yes, Delete Goal</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ==========================================
// 0. SEARCH FILTER LOGIC
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('studentSearchInput');
    const noResultsMsg = document.getElementById('no-search-results');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const accordionItems = document.querySelectorAll('.student-accordion-item');
            let visibleCount = 0;

            accordionItems.forEach(item => {
                const studentName = item.getAttribute('data-student-name');
                if (studentName.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (visibleCount === 0 && searchTerm !== '') {
                noResultsMsg.style.display = 'block';
            } else {
                noResultsMsg.style.display = 'none';
            }
        });
    }
});

// ==========================================
// 1. DEVELOPMENTAL DOMAIN FILTERING & PLOP MEMORY LOGIC 
// ==========================================
const ALL_DEVELOPMENTAL_DOMAINS = [
    { value: "Perceptuo-Cognitive", label: "Perceptuo - Cognitive" },
    { value: "Socio-Emotional", label: "Socio - Emotional" },
    { value: "Psychomotor", label: "Psychomotor" },
    { value: "Language-Communication", label: "Language - Communication" },
    { value: "Self-Help", label: "Self - Help" }
];

window.studentAssignedDomains = {!! json_encode($allAssignedDomains ?? []) !!};

window.updateAvailableDomains = function(preselectedDomain = '') {
    const studentSelect = document.getElementById('iep-student-select') || document.querySelector('select[name="student_id"]');
    const domainSelect = document.getElementById('domain-select');
    const domainWarning = document.getElementById('domain-limit-warning');
    const submitBtn = document.getElementById('save-iep-goal-submit-btn');
    const generateBtn = document.getElementById('generate-goals-btn');
    const addCardBtn = document.getElementById('add-objective-card-btn');

    if (!domainSelect) return;

    const studentId = studentSelect ? studentSelect.value : '';
    const existing = (studentId && window.studentAssignedDomains[studentId]) ? window.studentAssignedDomains[studentId] : [];

    const availableDomains = ALL_DEVELOPMENTAL_DOMAINS.filter(d => !existing.includes(d.value));

    domainSelect.innerHTML = '';

    if (!studentId) {
        domainSelect.innerHTML = '<option value="" disabled selected>Select a student first...</option>';
        domainSelect.disabled = true;
        if (domainWarning) domainWarning.classList.add('hidden');
        if (submitBtn) submitBtn.disabled = true;
        if (generateBtn) generateBtn.disabled = true;
        return;
    }

    if (availableDomains.length === 0) {
        domainSelect.innerHTML = '<option value="" disabled selected>All 5 developmental domains assigned</option>';
        domainSelect.disabled = true;
        if (domainWarning) domainWarning.classList.remove('hidden');
        if (submitBtn) submitBtn.disabled = true;
        if (generateBtn) generateBtn.disabled = true;
        if (addCardBtn) addCardBtn.disabled = true;
    } else {
        domainSelect.disabled = false;
        if (domainWarning) domainWarning.classList.add('hidden');
        if (submitBtn) submitBtn.disabled = false;
        if (generateBtn) generateBtn.disabled = false;
        if (addCardBtn) addCardBtn.disabled = false;

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.disabled = true;
        defaultOption.textContent = 'Select Domain...';
        if (!preselectedDomain) defaultOption.selected = true;
        domainSelect.appendChild(defaultOption);

        availableDomains.forEach(domain => {
            const opt = document.createElement('option');
            opt.value = domain.value;
            opt.textContent = domain.label;
            if (preselectedDomain && preselectedDomain === domain.value) {
                opt.selected = true;
            }
            domainSelect.appendChild(opt);
        });
    }

    domainSelect.dispatchEvent(new Event('change'));
};

document.addEventListener("DOMContentLoaded", function() {
    let domainSelect = document.getElementById('domain-select');
    let plopInput = document.getElementById('ai-present-level');
    let studentSelect = document.getElementById('iep-student-select') || document.querySelector('select[name="student_id"]');
    
    let savedMemory = localStorage.getItem('iep_plop_memory');
    let plopMemory = savedMemory ? JSON.parse(savedMemory) : {};
    let previousDomain = domainSelect ? domainSelect.value : '';

    if (studentSelect) {
        studentSelect.addEventListener('change', function() {
            window.updateAvailableDomains();
        });
        window.updateAvailableDomains();
    }

    if (domainSelect && plopInput) {
        if (previousDomain && plopMemory[previousDomain]) plopInput.value = plopMemory[previousDomain];

        plopInput.addEventListener('input', function() {
            if (previousDomain) {
                plopMemory[previousDomain] = this.value;
                localStorage.setItem('iep_plop_memory', JSON.stringify(plopMemory));
            }
        });

        domainSelect.addEventListener('change', function() {
            let newDomain = this.value;
            if (previousDomain) {
                plopMemory[previousDomain] = plopInput.value;
                localStorage.setItem('iep_plop_memory', JSON.stringify(plopMemory));
            }
            plopInput.value = (newDomain && plopMemory[newDomain]) ? plopMemory[newDomain] : ''; 
            previousDomain = newDomain;
        });

        let form = plopInput.closest('form');
        if (form) form.addEventListener('submit', () => localStorage.removeItem('iep_plop_memory'));
    }
});

// ==========================================
// ==========================================
// 2. UNIVERSAL IEP GOAL PARSER & COMPILER
// ==========================================
function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function parseGoalDescription(rawText) {
    if (!rawText || !rawText.trim()) return [];
    
    let sections = rawText.split(/-{3,}/);
    let results = [];

    sections.forEach(sec => {
        let text = sec.trim();
        if (!text) return;

        let objText = '';
        let actLines = [];

        if (/ACTIVITIES:/i.test(text)) {
            let parts = text.split(/ACTIVITIES:/i);
            objText = parts[0].replace(/OBJECTIVE:/i, '').trim();
            actLines = parts[1].trim().split('\n');
        } else {
            objText = text.replace(/OBJECTIVE:/i, '').trim();
        }

        let activities = [];
        actLines.forEach(line => {
            let l = line.trim();
            if (!l) return;
            if (l.startsWith('- ')) l = l.replace('- ', '[ ] ');

            let checked = /^\[x\]/i.test(l);
            let actContent = l.replace(/^\[[\s]*\]|^\[x\]/i, '').trim();
            if (actContent) {
                activities.push({ text: actContent, checked: checked });
            }
        });

        if (activities.length === 0) {
            activities.push({ text: '', checked: false });
        }

        results.push({
            objective: objText,
            activities: activities
        });
    });

    return results;
}

function compileGoalDescription(objectivesArray) {
    if (!objectivesArray || objectivesArray.length === 0) return '';
    
    let blocks = [];
    objectivesArray.forEach(item => {
        let obj = (item.objective || '').trim();
        let acts = (item.activities || [])
            .map(a => typeof a === 'string' ? { text: a.trim(), checked: false } : { text: (a.text || '').trim(), checked: !!a.checked })
            .filter(a => a.text.length > 0);
        
        let actLines = acts.map(a => (a.checked ? '[x] ' : '[ ] ') + a.text);

        if (obj || actLines.length > 0) {
            blocks.push(`OBJECTIVE:\n${obj}\n\nACTIVITIES:\n${actLines.join('\n')}`);
        }
    });

    return blocks.join('\n\n------------------------\n\n');
}

// ==========================================
// 3. CREATE GOAL MODAL (AI + MANUAL BUILDER)
// ==========================================
let createObjectives = [];

function renderCreateCards() {
    const container = document.getElementById('objectives-cards-container');
    const emptyState = document.getElementById('objectives-empty-state');

    if (!container) return;

    if (createObjectives.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        container.innerHTML = '';
        syncCreateGoalDescription();
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    container.innerHTML = '';

    createObjectives.forEach((objItem, objIndex) => {
        const card = document.createElement('div');
        card.className = 'objective-card bg-[#F8FAFC] border border-black/15 rounded-2xl p-4 sm:p-5 relative transition-all duration-150 shadow-sm';
        
        let actsHtml = '';
        objItem.activities.forEach((act, actIndex) => {
            let actText = typeof act === 'string' ? act : (act.text || '');
            actsHtml += `
                <div class="activity-row flex items-center gap-2 mb-2" data-act-index="${actIndex}">
                    <span class="w-6 h-6 rounded-lg bg-white border border-black/10 flex items-center justify-center text-[#9CA0AF] text-[10px] font-extrabold shrink-0 shadow-sm">•</span>
                    <input type="text" class="activity-input flex-1 rounded-xl border-black/15 px-3.5 py-2 text-[12px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" placeholder="e.g. Visual routine schedule" value="${escapeHtml(actText)}">
                    <button type="button" class="remove-act-btn text-[#9CA0AF] hover:text-rose-500 p-2 transition-colors rounded-lg shrink-0 cursor-pointer" title="Remove Activity">
                        <i class="fa fa-times text-xs"></i>
                    </button>
                </div>
            `;
        });

        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#3B82F6] text-white flex items-center justify-center text-[10px] font-extrabold">${objIndex + 1}</span>
                    <h6 class="text-[13px] font-extrabold text-[#1E2657] m-0">Objective #${objIndex + 1}</h6>
                </div>
                ${createObjectives.length > 1 ? `
                    <button type="button" class="remove-card-btn text-[#9CA0AF] hover:text-rose-500 text-xs font-bold transition-colors p-1 flex items-center gap-1 cursor-pointer">
                        <i class="fa fa-trash-alt"></i> <span class="hidden sm:inline">Delete</span>
                    </button>
                ` : ''}
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5">Objective Goal Statement</label>
                <textarea class="objective-input w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white custom-scrollbar" rows="2" placeholder="e.g. Student will independently transition between class activities...">${escapeHtml(objItem.objective)}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest m-0">Activities & Interventions</label>
                    <button type="button" class="add-act-btn text-[11px] font-extrabold text-[#3B82F6] hover:text-blue-700 transition-colors flex items-center gap-1 cursor-pointer">
                        <i class="fa fa-plus text-[10px]"></i> Add Activity
                    </button>
                </div>
                <div class="activities-container">
                    ${actsHtml}
                </div>
            </div>
        `;

        const objInput = card.querySelector('.objective-input');
        objInput.addEventListener('input', (e) => {
            createObjectives[objIndex].objective = e.target.value;
            syncCreateGoalDescription();
        });

        card.querySelectorAll('.activity-row').forEach(row => {
            const actIdx = parseInt(row.getAttribute('data-act-index'));
            const actInput = row.querySelector('.activity-input');
            const removeActBtn = row.querySelector('.remove-act-btn');

            actInput.addEventListener('input', (e) => {
                createObjectives[objIndex].activities[actIdx] = e.target.value;
                syncCreateGoalDescription();
            });

            removeActBtn.addEventListener('click', () => {
                createObjectives[objIndex].activities.splice(actIdx, 1);
                if (createObjectives[objIndex].activities.length === 0) {
                    createObjectives[objIndex].activities.push('');
                }
                renderCreateCards();
                syncCreateGoalDescription();
            });
        });

        const addActBtn = card.querySelector('.add-act-btn');
        addActBtn.addEventListener('click', () => {
            createObjectives[objIndex].activities.push('');
            renderCreateCards();
            syncCreateGoalDescription();
        });

        const removeCardBtn = card.querySelector('.remove-card-btn');
        if (removeCardBtn) {
            removeCardBtn.addEventListener('click', () => {
                createObjectives.splice(objIndex, 1);
                renderCreateCards();
                syncCreateGoalDescription();
            });
        }

        container.appendChild(card);
    });

    syncCreateGoalDescription();
}

function syncCreateGoalDescription() {
    const hiddenInput = document.getElementById('final-goal-input');
    if (!hiddenInput) return;
    hiddenInput.value = compileGoalDescription(createObjectives);
}

function addBlankCustomGoal() {
    createObjectives.push({
        objective: '',
        activities: ['', '']
    });
    renderCreateCards();
    const container = document.getElementById('objectives-cards-container');
    if (container) {
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        const lastCardObjInput = container.querySelector('.objective-card:last-child .objective-input');
        if (lastCardObjInput) lastCardObjInput.focus();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const emptyManualBtn = document.getElementById('empty-state-manual-btn');
    const addCardBtn = document.getElementById('add-objective-card-btn');

    if (emptyManualBtn) emptyManualBtn.addEventListener('click', addBlankCustomGoal);
    if (addCardBtn) addCardBtn.addEventListener('click', addBlankCustomGoal);
});

// AI Generation Trigger
document.getElementById('generate-goals-btn').addEventListener('click', function() {
    let btn = this;
    let presentLevelText = document.getElementById('ai-present-level').value;
    let domainValue = document.getElementById('domain-select').value;
    let resultsContainer = document.getElementById('ai-results-container');
    let tbody = document.getElementById('goals-list-tbody');

    if(domainValue === '') { alert('Please select a Development Domain first!'); return; }
    if(presentLevelText.trim() === '') { alert('Please enter the Present Level of Performance first!'); return; }

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Analyzing...';
    btn.disabled = true;

    fetch("{{ route('teacher.iep-goals.generate-ai') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ domain: domainValue, present_level: presentLevelText })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = '<i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities';
        btn.disabled = false;

        if(data.success) {
            tbody.innerHTML = '';
            resultsContainer.classList.remove('hidden');
            data.goals.forEach((item) => {
                let activityList = item.activities.map(a => `<li class="mb-1.5">${a}</li>`).join('');
                 
                let tr = document.createElement('tr');
                tr.className = "block sm:table-row bg-white border border-black/10 sm:border-none rounded-2xl sm:rounded-none shadow-sm sm:shadow-none hover:bg-slate-50 transition-colors";
                tr.innerHTML = `
                    <td class="block sm:table-cell font-extrabold text-[#3B82F6] align-top py-4 px-5 border-b border-black/5 sm:border-none text-[13px]">
                        <span class="sm:hidden text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-wider block mb-1">Objective</span>
                        ${item.objective}
                    </td>
                    <td class="block sm:table-cell align-top py-4 px-5 border-b border-black/5 sm:border-none">
                        <span class="sm:hidden text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-wider block mb-1.5">Suggested Activities</span>
                        <ul class="mb-0 pl-4 list-disc text-[#6B7086] text-[12px] font-medium leading-relaxed">${activityList}</ul>
                    </td>
                    <td class="block sm:table-cell text-center align-middle py-4 px-5 bg-[#F4F6FA] sm:bg-transparent rounded-b-2xl sm:rounded-none">
                        <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-[#10B981]/50 text-[#10B981] text-[12px] font-extrabold rounded-xl hover:bg-[#10B981]/10 transition-colors use-goal-btn shadow-sm cursor-pointer">
                            <i class="fa fa-arrow-down mr-1.5"></i> Add to Goal
                        </button>
                    </td>
                `;

                let useBtn = tr.querySelector('.use-goal-btn');
                useBtn.onclick = function() {
                    createObjectives.push({
                        objective: item.objective,
                        activities: Array.isArray(item.activities) ? [...item.activities] : []
                    });
                    renderCreateCards();

                    this.innerHTML = '<i class="fa fa-check mr-1"></i> Added to Goal';
                    this.className = "w-full inline-flex items-center justify-center px-4 py-2 bg-[#10B981] text-white text-[12px] font-extrabold rounded-xl transition-colors use-goal-btn shadow-sm";
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fa fa-arrow-down mr-1"></i> Add Another';
                        this.className = "w-full inline-flex items-center justify-center px-4 py-2 border border-[#10B981]/50 text-[#10B981] text-[12px] font-extrabold rounded-xl hover:bg-[#10B981]/10 transition-colors use-goal-btn shadow-sm";
                    }, 1500);

                    const container = document.getElementById('objectives-cards-container');
                    if (container) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                };

                tbody.appendChild(tr);
            });
        } else {
            alert("AI Error: " + data.message);
        }
    }).catch(error => {
        btn.innerHTML = '<i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities';
        btn.disabled = false;
        alert("Something went wrong communicating with the AI.");
    });
});

// ==========================================
// 4. INTERACTIVE CHECKLIST & EDIT MODAL LOGIC
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.interactive-checklist-container').forEach(container => {
        let textarea = container.querySelector('.goal-raw-text');
        let visualList = container.querySelector('.visual-checklist');
        let editorWrapper = container.querySelector('.goal-structured-editor');
        let editCardsContainer = container.querySelector('.edit-objectives-cards-list');
        let addEditObjBtn = container.querySelector('.add-edit-objective-btn');
        let toggleBtn = container.querySelector('.toggle-edit-goal-btn');
        let toggleLabel = container.querySelector('.toggle-btn-label');
        let form = container.closest('form');
        let progressInput = form.querySelector('.goal-progress-input');
        let statusHidden = form.querySelector('.goal-status-hidden');
        let statusDisplay = form.querySelector('.goal-status-display');

        // PLOP & AI Elements in Edit Modal
        let editPlopInput = container.querySelector('.edit-plop-input');
        let editAiBtn = container.querySelector('.generate-edit-ai-btn');
        let editAiResults = container.querySelector('.edit-ai-results-container');
        let editAiList = container.querySelector('.edit-ai-results-list');
        let dismissAiBtn = container.querySelector('.dismiss-edit-ai-btn');
        let goalDomain = container.getAttribute('data-goal-domain') || '';

        let isEditing = false;
        let currentObjectives = [];

        function updateStatusDisplay(percent) {
            let isComplete = (percent === 100);
            if (statusHidden) {
                statusHidden.value = isComplete ? 'achieved' : 'in_progress';
            }
            if (statusDisplay) {
                if (isComplete) {
                    statusDisplay.className = 'goal-status-display flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 font-extrabold text-[13px] shadow-sm select-none transition-all';
                    statusDisplay.innerHTML = '<i class="fa fa-circle-check text-emerald-500 text-[14px]"></i> <span>Complete</span>';
                } else {
                    statusDisplay.className = 'goal-status-display flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-600 font-extrabold text-[13px] shadow-sm select-none transition-all';
                    statusDisplay.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> <span>In Progress</span>';
                }
            }
        }

        // Handle on-demand AI suggestion generation inside edit modal
        if (editAiBtn && editPlopInput && editAiResults && editAiList) {
            if (dismissAiBtn) {
                dismissAiBtn.addEventListener('click', () => {
                    editAiResults.classList.add('hidden');
                });
            }

            editAiBtn.addEventListener('click', function() {
                let plopText = editPlopInput.value.trim();
                if (!plopText) {
                    alert('Please enter or update the Present Level of Performance (PLOP) first!');
                    editPlopInput.focus();
                    return;
                }

                let originalBtnHtml = editAiBtn.innerHTML;
                editAiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Analyzing PLOP...';
                editAiBtn.disabled = true;

                fetch("{{ route('teacher.iep-goals.generate-ai') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        domain: goalDomain,
                        present_level: plopText
                    })
                })
                .then(res => res.json())
                .then(data => {
                    editAiBtn.innerHTML = originalBtnHtml;
                    editAiBtn.disabled = false;

                    if (data.success && Array.isArray(data.goals)) {
                        editAiList.innerHTML = '';
                        editAiResults.classList.remove('hidden');

                        data.goals.forEach(item => {
                            let actListHtml = (item.activities || []).map(a => `<li class="text-[11px] text-[#6B7086]">${escapeHtml(a)}</li>`).join('');
                            let card = document.createElement('div');
                            card.className = 'p-3 bg-[#F8FAFC] border border-black/10 rounded-xl shadow-xs flex flex-col sm:flex-row sm:items-start justify-between gap-3 hover:border-[#8B5CF6]/40 transition-colors';
                            card.innerHTML = `
                                <div class="flex-1">
                                    <span class="text-[10px] font-extrabold text-[#8B5CF6] uppercase tracking-wider block mb-1">Suggested Objective</span>
                                    <p class="text-[12px] font-bold text-[#1E2657] m-0 mb-1 leading-snug">${escapeHtml(item.objective)}</p>
                                    <ul class="list-disc pl-4 space-y-0.5 m-0 mb-0">${actListHtml}</ul>
                                </div>
                                <button type="button" class="add-ai-to-edit-btn inline-flex items-center justify-center px-3 py-1.5 bg-[#10B981] hover:bg-emerald-600 text-white text-[11px] font-extrabold rounded-lg transition-colors shrink-0 shadow-sm cursor-pointer self-start sm:self-center">
                                    <i class="fa fa-plus mr-1"></i> Add to Goal
                                </button>
                            `;

                            let addBtn = card.querySelector('.add-ai-to-edit-btn');
                            addBtn.addEventListener('click', function() {
                                currentObjectives.push({
                                    objective: item.objective,
                                    activities: (item.activities || []).map(a => ({ text: a, checked: false }))
                                });
                                renderEditCards();
                                syncEditToTextarea();

                                this.innerHTML = '<i class="fa fa-check mr-1"></i> Added';
                                this.classList.remove('bg-[#10B981]', 'hover:bg-emerald-600');
                                this.classList.add('bg-slate-400', 'cursor-default');
                                this.disabled = true;

                                const containerEl = editCardsContainer;
                                if (containerEl) {
                                    containerEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                }
                            });

                            editAiList.appendChild(card);
                        });
                    } else {
                        alert("AI Error: " + (data.message || 'Could not parse suggestions.'));
                    }
                })
                .catch(err => {
                    editAiBtn.innerHTML = originalBtnHtml;
                    editAiBtn.disabled = false;
                    alert("Something went wrong communicating with the AI service.");
                });
            });
        }

        function renderEditCards() {
            if (!editCardsContainer) return;
            editCardsContainer.innerHTML = '';

            if (currentObjectives.length === 0) {
                currentObjectives.push({ objective: '', activities: [{ text: '', checked: false }] });
            }

            currentObjectives.forEach((objItem, objIndex) => {
                const card = document.createElement('div');
                card.className = 'bg-white border border-black/15 rounded-xl p-3.5 sm:p-4 shadow-sm';

                let actsHtml = '';
                objItem.activities.forEach((act, actIndex) => {
                    actsHtml += `
                        <div class="edit-activity-row flex items-center gap-2 mb-2" data-act-index="${actIndex}">
                            <input type="checkbox" class="edit-act-chk rounded border-black/20 text-[#3B82F6] focus:ring-[#3B82F6] cursor-pointer w-4 h-4 shrink-0 shadow-sm" ${act.checked ? 'checked' : ''}>
                            <input type="text" class="edit-act-text flex-1 rounded-xl border-black/15 px-3 py-1.5 text-[12px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" placeholder="Activity / Intervention text" value="${escapeHtml(act.text)}">
                            <button type="button" class="remove-edit-act-btn text-[#9CA0AF] hover:text-rose-500 p-1.5 transition-colors shrink-0 cursor-pointer" title="Remove Activity">
                                <i class="fa fa-times text-xs"></i>
                            </button>
                        </div>
                    `;
                });

                card.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-extrabold text-[#3B82F6] uppercase tracking-wider">Objective #${objIndex + 1}</span>
                        ${currentObjectives.length > 1 ? `
                            <button type="button" class="remove-edit-card-btn text-[#9CA0AF] hover:text-rose-500 text-xs transition-colors cursor-pointer">
                                <i class="fa fa-trash-alt"></i>
                            </button>
                        ` : ''}
                    </div>

                    <div class="mb-3">
                        <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1">Objective Statement</label>
                        <textarea class="edit-obj-statement w-full rounded-xl border-black/15 px-3 py-2 text-[12px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white custom-scrollbar" rows="2">${escapeHtml(objItem.objective)}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest m-0">Activities</label>
                            <button type="button" class="add-edit-act-btn text-[10px] font-extrabold text-[#3B82F6] hover:text-blue-700 transition-colors flex items-center gap-1 cursor-pointer">
                                <i class="fa fa-plus text-[9px]"></i> Add Activity
                            </button>
                        </div>
                        <div class="edit-activities-list">
                            ${actsHtml}
                        </div>
                    </div>
                `;

                const objInput = card.querySelector('.edit-obj-statement');
                objInput.addEventListener('input', (e) => {
                    currentObjectives[objIndex].objective = e.target.value;
                    syncEditToTextarea();
                });

                card.querySelectorAll('.edit-activity-row').forEach(row => {
                    const actIdx = parseInt(row.getAttribute('data-act-index'));
                    const actChk = row.querySelector('.edit-act-chk');
                    const actText = row.querySelector('.edit-act-text');
                    const remBtn = row.querySelector('.remove-edit-act-btn');

                    actChk.addEventListener('change', () => {
                        currentObjectives[objIndex].activities[actIdx].checked = actChk.checked;
                        syncEditToTextarea();
                    });

                    actText.addEventListener('input', () => {
                        currentObjectives[objIndex].activities[actIdx].text = actText.value;
                        syncEditToTextarea();
                    });

                    remBtn.addEventListener('click', () => {
                        currentObjectives[objIndex].activities.splice(actIdx, 1);
                        if (currentObjectives[objIndex].activities.length === 0) {
                            currentObjectives[objIndex].activities.push({ text: '', checked: false });
                        }
                        renderEditCards();
                        syncEditToTextarea();
                    });
                });

                const addActBtn = card.querySelector('.add-edit-act-btn');
                addActBtn.addEventListener('click', () => {
                    currentObjectives[objIndex].activities.push({ text: '', checked: false });
                    renderEditCards();
                    syncEditToTextarea();
                });

                const remCardBtn = card.querySelector('.remove-edit-card-btn');
                if (remCardBtn) {
                    remCardBtn.addEventListener('click', () => {
                        currentObjectives.splice(objIndex, 1);
                        renderEditCards();
                        syncEditToTextarea();
                    });
                }

                editCardsContainer.appendChild(card);
            });
        }

        function syncEditToTextarea() {
            textarea.value = compileGoalDescription(currentObjectives);
            renderChecklist();
        }

        if (toggleBtn && editorWrapper && visualList) {
            toggleBtn.addEventListener('click', () => {
                isEditing = !isEditing;
                if (isEditing) {
                    editorWrapper.classList.remove('hidden');
                    visualList.classList.add('hidden');
                    if (toggleLabel) toggleLabel.textContent = 'View Tracker Checklist';
                    toggleBtn.classList.add('bg-blue-50', 'text-[#3B82F6]', 'border-[#3B82F6]/30');
                    currentObjectives = parseGoalDescription(textarea.value);
                    renderEditCards();
                } else {
                    editorWrapper.classList.add('hidden');
                    visualList.classList.remove('hidden');
                    if (toggleLabel) toggleLabel.textContent = 'Edit Goal Fields';
                    toggleBtn.classList.remove('bg-blue-50', 'text-[#3B82F6]', 'border-[#3B82F6]/30');
                    renderChecklist();
                }
            });
        }

        if (addEditObjBtn) {
            addEditObjBtn.addEventListener('click', () => {
                currentObjectives.push({ objective: '', activities: [{ text: '', checked: false }] });
                renderEditCards();
                syncEditToTextarea();
            });
        }

        textarea.addEventListener('input', renderChecklist);
        function renderChecklist() {
            let lines = textarea.value.split('\n');
            let html = '';
            let totalBoxes = 0;
            let checkedBoxes = 0;
            
            lines.forEach((line, index) => {
                if (line.trim().startsWith('- ')) { line = line.replace('- ', '[ ] '); }

                if (line.trim().match(/^\[[\s]*\]/)) {
                    totalBoxes++;
                    let text = line.trim().replace(/^\[[\s]*\]/, '').trim();
                    html += `
                        <div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <input class="dynamic-checkbox mt-0.5 rounded border-black/20 text-[#3B82F6] focus:ring-[#3B82F6] cursor-pointer w-4 h-4 shrink-0 shadow-sm" type="checkbox" data-line="${index}">
                            <label class="text-[13px] font-medium text-[#1E2657] cursor-pointer leading-tight pt-[1px]">${text}</label>
                        </div>`;
                } 
                else if (line.trim().match(/^\[x\]/i)) {
                    totalBoxes++;
                    checkedBoxes++;
                    let text = line.trim().replace(/^\[x\]/i, '').trim();
                    html += `
                        <div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl bg-[#F4F6FA] transition-colors border border-black/5">
                            <input class="dynamic-checkbox mt-0.5 rounded border-[#10B981] bg-[#10B981] text-[#10B981] focus:ring-[#10B981] cursor-pointer w-4 h-4 shrink-0 shadow-sm" type="checkbox" data-line="${index}" checked>
                            <label class="text-[13px] font-medium text-[#9CA0AF] line-through cursor-pointer leading-tight pt-[1px]">${text}</label>
                        </div>`;
                } 
                else if (line.trim() !== '' && !line.includes('---')) {
                    html += `<div class="mt-4 mb-2 px-1 text-[11px] font-extrabold text-[#3B82F6] uppercase tracking-widest">${line}</div>`;
                } 
                else if (line.includes('---')) {
                    html += `<hr class="border-black/10 my-4">`;
                }
            });
            
            if(totalBoxes === 0 && textarea.value.trim() !== '') {
                html += `<div class="text-[#9CA0AF] text-[13px] italic px-1 mt-2">No trackable activities found. Click 'Edit Goal Fields' to add activities.</div>`;
            }

            visualList.innerHTML = html;
            
            if (totalBoxes > 0) {
                let percent = Math.round((checkedBoxes / totalBoxes) * 100);
                
                progressInput.readOnly = true;
                progressInput.value = percent;
                progressInput.classList.add('bg-slate-100', 'text-slate-500', 'pointer-events-none'); 
                
                updateStatusDisplay(percent);
            } else {
                progressInput.readOnly = false;
                progressInput.classList.remove('bg-slate-100', 'text-slate-500', 'pointer-events-none');
                updateStatusDisplay(parseInt(progressInput.value) || 0);
            }

            visualList.querySelectorAll('.dynamic-checkbox').forEach(chk => {
                chk.addEventListener('change', function() {
                    let lineIdx = this.getAttribute('data-line');
                    let currentLines = textarea.value.split('\n');
                    
                    if (currentLines[lineIdx].match(/^-\s+/)) { currentLines[lineIdx] = currentLines[lineIdx].replace(/^-\s+/, '[ ] '); }
                    
                    if (this.checked) { 
                        currentLines[lineIdx] = currentLines[lineIdx].replace(/\[[\s]*\]/, '[x]'); 
                    } else { 
                        currentLines[lineIdx] = currentLines[lineIdx].replace(/\[x\]/i, '[ ]'); 
                    }
                    
                    textarea.value = currentLines.join('\n');
                    renderChecklist();
                });
            });
        }

        if (progressInput) {
            progressInput.addEventListener('input', function() {
                let val = Math.min(100, Math.max(0, parseInt(this.value) || 0));
                updateStatusDisplay(val);
            });
        }

        renderChecklist();
    });
});
</script>
@endpush