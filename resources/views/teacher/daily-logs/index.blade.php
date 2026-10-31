@extends('layouts.app')

@section('title', 'Daily Logging')

@section('content')

<style>
    [x-cloak] { display: none !important; }
    /* Custom scrollbar for the text areas */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9CA0AF; }
</style>

<!-- Main Container -->
<div x-data="{ activeTab: 'create', enlargedPhotoUrl: null, enlargedPhotoTitle: '' }" class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">


    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] m-0 flex items-center tracking-tight">
                <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm shrink-0">
                    <i class="fa fa-pencil-alt text-lg"></i>
                </div>
                Daily Logging
            </h1>
            <p class="text-[13px] font-medium text-[#6B7086] mt-1 sm:ml-14 m-0 leading-tight">Record observations.</p>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="bg-[#F4F6FA] p-1.5 rounded-2xl flex w-full sm:w-auto shadow-sm border border-black/5">
            <button @click="activeTab = 'create'" 
                    :class="activeTab === 'create' ? 'bg-white text-[#3B82F6] shadow-sm border border-black/5' : 'text-[#6B7086] hover:text-[#1E2657]'" 
                    class="flex-1 sm:flex-none px-3.5 sm:px-6 py-2.5 rounded-xl font-extrabold text-[12px] sm:text-[13px] transition-all flex items-center justify-center active:scale-95 text-center">
                <i class="fa fa-pen mr-1.5 sm:mr-2"></i> Observation
            </button>
            <button @click="activeTab = 'history'; if(typeof window.syncStudentFilter === 'function') window.syncStudentFilter();" 
                    :class="activeTab === 'history' ? 'bg-white text-[#3B82F6] shadow-sm border border-black/5' : 'text-[#6B7086] hover:text-[#1E2657]'" 
                    class="flex-1 sm:flex-none px-3.5 sm:px-6 py-2.5 rounded-xl font-extrabold text-[12px] sm:text-[13px] transition-all flex items-center justify-center active:scale-95 text-center">
                <i class="fa fa-file-contract mr-1.5 sm:mr-2"></i> Daily Logs
            </button>
        </div>
    </div>

    @php
        $selectedStudentId = old('student_id', request('student_id'));
    @endphp

    <!-- TAB 1: CREATE NEW LOG FORM -->
    <div x-show="activeTab === 'create'" x-transition.opacity.duration.300ms class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA]">
                <h6 class="text-[14px] font-extrabold text-[#1E2657] m-0 flex items-center">
                    <i class="fa fa-plus-circle text-[#3B82F6] mr-2"></i> Create Observation Entry
                </h6>
            </div>
            
            <div class="p-4 sm:p-8">
                <form id="dailyLogForm" action="{{ route('teacher.daily-logs.store') }}" method="POST" enctype="multipart/form-data" class="m-0 space-y-6">
                    @csrf
                    
                    <!-- STUDENT SELECTION DROPDOWN -->
                    <div class="p-4 bg-[#F4F6FA] border border-black/10 rounded-2xl shadow-sm">
                        <div class="flex items-center mb-1.5">
                            <label class="text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest flex items-center m-0">
                                <i class="fa fa-user-graduate text-[#3B82F6] mr-2"></i> Select Student
                            </label>
                            <span class="mx-2.5 text-black/20">|</span>
                            <a href="{{ route('teacher.students.index') }}" class="text-[10px] font-extrabold text-[#6B7086] hover:text-[#3B82F6] transition-colors uppercase tracking-widest flex items-center decoration-transparent">
                                <i class="fa fa-gear mr-1.5"></i> Manage Student List
                            </a>
                        </div>
                        <select name="student_id" id="student_id" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer" required>
                            <option value="" disabled {{ empty($selectedStudentId) ? 'selected' : '' }}>Select a student to log for...</option>
                            @foreach($myStudents as $student)
                                <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Date</label>
                            <input type="date" name="log_date" id="log_date" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" value="{{ old('log_date', date('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Quarter</label>
                            @php $selectedQ = old('quarter', $activeQuarterName ?? 'Q1'); @endphp
                            <select name="quarter" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" required>
                                <option value="Q1" {{ $selectedQ == 'Q1' ? 'selected' : '' }}>Q1</option>
                                <option value="Q2" {{ $selectedQ == 'Q2' ? 'selected' : '' }}>Q2</option>
                                <option value="Q3" {{ $selectedQ == 'Q3' ? 'selected' : '' }}>Q3</option>
                                <option value="Q4" {{ $selectedQ == 'Q4' ? 'selected' : '' }}>Q4</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-black/10 my-6">

                    <!-- Notification Banner when no student is selected -->
                    <div id="noStudentNotice" class="{{ empty($selectedStudentId) ? '' : 'hidden' }} p-3.5 bg-amber-50 border border-amber-200/80 rounded-xl text-amber-800 text-xs font-bold flex items-center gap-2.5 mb-4">
                        <i class="fa fa-circle-info text-amber-500 text-sm shrink-0"></i>
                        <span>Please select a student above to start typing quick notes or formal documentation.</span>
                    </div>

                    <div class="p-5 bg-[#F4F6FA] border border-dashed border-[#3B82F6]/30 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[12px] font-extrabold text-[#3B82F6] uppercase tracking-widest m-0 flex items-center">
                                <i class="fa fa-bolt mr-1.5"></i> 1. Quick Notes (Informal)
                            </label>
                        </div>
                        <textarea name="raw_notes" id="rawNotes" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm mb-4 custom-scrollbar bg-white transition-all" rows="4" placeholder="e.g., he ate snacks, played with blocks, got distracted...">{{ old('raw_notes') }}</textarea>
                        
                        <button type="button" id="generateAiBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all active:scale-98 shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 cursor-pointer">
                            <i class="fa fa-magic mr-1.5"></i> Rewrite Professionally
                        </button>

                        <!-- AI Clinical Disclaimer Notice -->
                        <div class="mt-2.5 flex items-center justify-between text-[11px] text-[#6B7086] px-1">
                            <span class="flex items-center gap-1.5">
                                <i class="fa fa-shield-halved text-[#3B82F6]"></i>
                                <span>AI SPED Draft: Educator review required before saving</span>
                            </span>
                            <span class="text-[10px] text-[#9CA0AF] hidden sm:inline">Zero-PII Proxy &bull; Groq Cloud</span>
                        </div>

                        <div id="aiLoading" class="text-center text-[#3B82F6] text-[12px] mt-4 font-extrabold hidden">
                            <i class="fa fa-spinner fa-spin mr-1.5"></i> AI is thinking...
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-extrabold text-[#10B981] uppercase tracking-widest mb-2 flex items-center">
                            <i class="fa fa-file-alt mr-1.5"></i> 2. Formal Documentation (Standardized Notes)
                        </label>
                        <textarea name="notes" id="formalNotes" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] bg-white focus:border-[#10B981] focus:ring-[#10B981] shadow-sm custom-scrollbar transition-all" rows="6" placeholder="Type formal documentation directly here, or click 'Rewrite Professionally' above...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- 3. Tonight's Suggested Activity (Home Practice) -->
                    <div class="p-5 bg-emerald-50/50 border border-dashed border-emerald-300 rounded-2xl relative overflow-hidden">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[12px] font-extrabold text-emerald-800 uppercase tracking-widest m-0 flex items-center">
                                <i class="fa-solid fa-house-chimney-user mr-1.5 text-emerald-600"></i> 3. Tonight's Suggested Activity (Home Practice)
                            </label>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-md border border-emerald-300/70">Optional</span>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-600 mb-3 m-0">Provide actionable 5-10 minute home activities for parents to practice with the child tonight.</p>

                        <button type="button" id="btnGenerateRecommendations" class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-700 transition-all active:scale-98 shadow-sm cursor-pointer mb-3">
                            <i class="fa-solid fa-lightbulb mr-1.5"></i> Generate Suggested Activity via AI
                        </button>

                        <div id="recLoading" class="text-center text-emerald-600 text-[12px] mb-3 font-extrabold hidden">
                            <i class="fa fa-spinner fa-spin mr-1.5"></i> AI is thinking...
                        </div>

                        <textarea name="home_recommendations" id="home_recommendations" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] bg-white focus:border-emerald-500 focus:ring-emerald-500 shadow-sm custom-scrollbar transition-all" rows="4" placeholder="Suggested home practice activities will appear here, or type your own instructions for parents...">{{ old('home_recommendations') }}</textarea>
                    </div>

                    <!-- 4. Attach Session Photo (Optional) -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <label class="block text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider mb-1.5 flex items-center">
                            <i class="fa fa-camera text-indigo-500 mr-1.5"></i> Attach Session Photo (Optional)
                        </label>
                        <input type="file" name="photo" id="photoInput" onchange="handleFileInputChange(this, 'photo_input_preview', 'photo_input_filename')" class="block w-full text-[13px] font-medium text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer shadow-sm bg-white" accept="image/jpeg, image/png, image/jpg">
                        <div id="photo_input_preview" class="hidden mt-2 p-2.5 bg-indigo-50/80 border border-indigo-200 rounded-xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <i class="fa fa-camera text-indigo-600 text-sm"></i>
                                <span id="photo_input_filename" class="text-[12px] font-extrabold text-slate-800 truncate"></span>
                            </div>
                            <button type="button" onclick="clearSelectedFile('photoInput', 'photo_input_preview')" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer shrink-0 flex items-center gap-1">
                                <i class="fa fa-times text-xs"></i> Remove
                            </button>
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="border-t border-black/10 pt-6">
                        <button type="submit" id="saveDailyLogBtn" class="w-full inline-flex items-center justify-center px-6 py-4 bg-[#1E2657] text-white text-[14px] font-extrabold rounded-xl hover:bg-[#12163A] transition-all active:scale-98 shadow-sm cursor-pointer">
                            <i class="fa fa-save mr-2"></i> Save Daily Log & Send to Formal Documentation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 2: FORMAL DOCUMENTATION LIST -->
    <div x-show="activeTab === 'history'" x-transition.opacity.duration.300ms x-cloak class="w-full">
        
        <!-- FILTER BAR WITH SORTING OPTIONS -->
        <div class="bg-white p-3 sm:p-5 rounded-2xl shadow-sm border border-black/15 mb-4 sm:mb-6 space-y-2 sm:space-y-3">
            <h6 class="text-xs sm:text-[14px] font-extrabold text-[#1E2657] m-0 flex items-center">
                <i class="fa fa-filter text-[#3B82F6] mr-2"></i> Daily Logs Filters & Search
            </h6>
            
            <div class="grid grid-cols-2 sm:grid-cols-12 gap-2 sm:gap-3">
                <!-- Student Filter -->
                <div class="col-span-2 sm:col-span-3">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5 sm:mb-1">Filter by Student</label>
                    <select id="filterStudent" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                        <option value="all">All Assigned Students</option>
                        @foreach($myStudents as $student)
                            <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-span-1 sm:col-span-3">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5 sm:mb-1">Date</label>
                    <input type="date" id="filterDate" class="w-full rounded-xl border-black/15 px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                </div>

                <!-- Quarter Filter -->
                <div class="col-span-1 sm:col-span-2">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5 sm:mb-1">Quarter</label>
                    <select id="filterQuarter" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                        <option value="all">All Quarters</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="col-span-2 sm:col-span-4">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5 sm:mb-1">Search Keywords</label>
                    <div class="relative w-full shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 sm:pl-3 text-[#9CA0AF] pointer-events-none">
                            <i class="fa fa-search text-xs"></i>
                        </span>
                        <input type="text" id="logSearch" class="w-full border-0 py-1.5 sm:py-2 pl-8 sm:pl-9 pr-3 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Search documentation content...">
                    </div>
                </div>
            </div>
        </div>

        <div id="logs-feed-container" class="space-y-4 sm:space-y-6">
            @forelse($recentLogs as $log)
                @php
                    $unreadLogCommentsCount = $log->comments->filter(function($comment) {
                        return $comment->user_id != Auth::id() && !$comment->is_teacher_read;
                    })->count();
                @endphp

                <div x-data="{ 
                        unreadCount: {{ (int) $unreadLogCommentsCount }}, 
                        hasUnread: {{ $unreadLogCommentsCount > 0 ? 'true' : 'false' }},
                        markAsRead() {
                            if (this.hasUnread) {
                                this.hasUnread = false;
                                this.unreadCount = 0;
                                fetch('{{ route('teacher.daily-logs.mark-read', $log->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                }).catch(e => console.error(e));
                            }
                        }
                     }"
                     class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden hover:shadow-md transition-shadow duration-200 log-item" 
                     data-student-id="{{ $log->student_id }}"
                     data-log-date="{{ $log->log_date }}"
                     data-quarter="{{ $log->quarter }}">
                    
                    <!-- Card Header -->
                    <div class="p-5 sm:px-6 border-b border-black/10 bg-[#F4F6FA] relative">
                        <div class="flex items-start gap-4 pr-10 sm:pr-0">
                            <div class="relative w-11 h-11 rounded-full flex items-center justify-center text-white font-extrabold text-sm shadow-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shrink-0 border border-black/10">
                                {{ strtoupper(substr($log->student->first_name, 0, 1)) }}

                                <!-- Reactive Red Notification Dot on Avatar -->
                                <span x-show="hasUnread" 
                                      x-transition:leave="transition ease-in duration-200"
                                      x-transition:leave-start="opacity-100 scale-100"
                                      x-transition:leave-end="opacity-0 scale-75"
                                      class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="flex-1 pt-0.5">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h6 class="text-[15px] font-extrabold text-[#1E2657] m-0 leading-tight">{{ $log->student->first_name }} {{ $log->student->last_name }}</h6>
                                    
                                    <!-- Reactive 'New Guardian Message' Badge -->
                                    <span x-show="hasUnread" 
                                          x-transition:leave="transition ease-in duration-200"
                                          x-transition:leave-start="opacity-100 scale-100"
                                          x-transition:leave-end="opacity-0 scale-75"
                                          class="px-2.5 py-1 rounded-md bg-red-500 text-white text-[9px] font-extrabold uppercase tracking-widest shadow-sm animate-pulse flex items-center gap-1">
                                        <i class="fa fa-envelope"></i> New Message
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 flex-wrap mt-1.5">
                                    <p class="text-[11px] font-extrabold text-[#9CA0AF] m-0 flex items-center uppercase tracking-widest">
                                        <i class="fa fa-calendar-alt mr-1.5"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('M j, Y') }}
                                    </p>
                                    <span class="hidden sm:inline-block text-[#E8E5DC] text-[10px]">•</span>
                                    <span class="px-2.5 py-1 rounded-md border border-[#3B82F6]/20 bg-[#3B82F6]/10 text-[#3B82F6] text-[9px] font-extrabold uppercase shadow-sm leading-none tracking-widest">
                                        {{ $log->quarter }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('teacher.daily-logs.destroy', $log->id) }}" method="POST" class="absolute top-4 sm:top-5 right-4 sm:right-5 m-0" onsubmit="return confirm('Are you sure you want to permanently delete this documentation entry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-full bg-white border border-black/10 text-[#9CA0AF] hover:text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm" title="Delete Entry">
                                <i class="fa fa-trash-alt text-[12px]"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6 text-[13px] sm:text-[14px] text-[#6B7086] leading-relaxed bg-white font-medium flex flex-col gap-5 sm:gap-6">
                        @php
                            $photoUrl = $log->image_path ? route('secure.log-photo', $log->id) : null;

                            $fullContent = $log->notes ?? '';
                            $dailyReportText = $fullContent;
                            $suggestedActivityText = $log->home_recommendations ?? '';

                            if (preg_match('/^(.*?)(?:\r?\n)+(?:Tonight\'s Suggested Activity|Tonight\'s Suggested Home Activity|Home Activity):?\s*(.*)$/si', $fullContent, $matches)) {
                                $dailyReportText = trim($matches[1]);
                                if (empty($suggestedActivityText) && !empty($matches[2])) {
                                    $suggestedActivityText = trim($matches[2]);
                                }
                            }

                            $formatHtml = function($rawText) {
                                if (empty($rawText)) return '';
                                $escaped = e(trim($rawText));
                                $formatted = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="font-extrabold text-slate-900">$1</strong>', $escaped);
                                
                                $lines = explode("\n", $formatted);
                                $output = [];
                                $inList = false;
                                
                                foreach ($lines as $line) {
                                    $trimmed = trim($line);
                                    if (preg_match('/^[-*•]\s+(.*)$/', $trimmed, $m)) {
                                        if (!$inList) {
                                            $output[] = '<ul class="list-none pl-0 space-y-2 mt-2 mb-2">';
                                            $inList = true;
                                        }
                                        $output[] = '<li class="flex items-start gap-2.5 text-[13px] sm:text-[14px] leading-relaxed text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span><span class="flex-1">' . $m[1] . '</span></li>';
                                    } else {
                                        if ($inList) {
                                            $output[] = '</ul>';
                                            $inList = false;
                                        }
                                        if (!empty($trimmed)) {
                                            $output[] = '<p class="m-0 mb-2 leading-relaxed text-[13px] sm:text-[14px] text-slate-700 font-normal">' . $trimmed . '</p>';
                                        }
                                    }
                                }
                                if ($inList) {
                                    $output[] = '</ul>';
                                }
                                return implode("\n", $output);
                            };
                        @endphp

                        <!-- TOP SECTION: DAILY REPORT (LEFT) + OBSERVATION PHOTO (RIGHT) -->
                        @if($photoUrl)
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-6 items-stretch">
                                
                                <!-- LEFT: DAILY REPORT CARD (Dark Blue / Indigo Accent) -->
                                @if(!empty($dailyReportText))
                                    <div class="md:col-span-7 lg:col-span-8 flex flex-col">
                                        <div class="p-5 sm:p-6 bg-white rounded-2xl border-2 border-indigo-200/90 shadow-sm relative overflow-hidden h-full flex flex-col justify-between">
                                            <div>
                                                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-indigo-100 flex-wrap gap-2">
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs shrink-0">
                                                            <i class="fa-solid fa-file-lines"></i>
                                                        </div>
                                                        <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                                            Daily Report:
                                                        </h6>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200/70">
                                                            Archived Report
                                                        </span>
                                                        <a href="{{ route('teacher.daily-logs.pdf', $log->id) }}" 
                                                           target="_blank"
                                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-[#1E2657] bg-white border border-black/15 rounded-lg hover:bg-slate-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent" 
                                                           title="View Standardized Formal Documentation as PDF">
                                                            <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                                            <span>View PDF</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="m-0 leading-relaxed text-slate-700 font-medium">
                                                    {!! $formatHtml($dailyReportText) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- RIGHT: OBSERVATION PHOTO (Light Blue Accent) -->
                                <div class="{{ !empty($dailyReportText) ? 'md:col-span-5 lg:col-span-4' : 'md:col-span-12' }} flex flex-col">
                                    <div class="bg-slate-50/90 rounded-2xl border-2 border-indigo-200/80 p-3 sm:p-4 shadow-sm flex flex-col justify-between h-full">
                                        <div>
                                            <div class="flex items-center justify-between mb-2.5 px-0.5">
                                                <span class="text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider flex items-center gap-1.5">
                                                    <i class="fa fa-camera text-indigo-500"></i> Session Photo
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline-block">
                                                    Click to enlarge
                                                </span>
                                            </div>

                                            <div class="relative group cursor-pointer w-full overflow-hidden rounded-xl border border-slate-300 shadow-xs bg-slate-200 flex items-center justify-center min-h-[180px] max-h-[260px] sm:max-h-[300px]"
                                                 @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Session Photo - {{ addslashes($log->student->first_name) }}'"
                                                 title="Click to view full image">
                                                <img src="{{ $photoUrl }}" 
                                                     alt="Session Photo" 
                                                     class="w-full h-full max-h-[260px] sm:max-h-[300px] object-cover group-hover:scale-105 transition-transform duration-300 block"
                                                     loading="lazy">
                                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[1px]">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-base"></i> View full size
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" 
                                                @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Session Photo - {{ addslashes($log->student->first_name) }}'"
                                                class="w-full mt-3 py-2 px-3 bg-white hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 font-extrabold text-[11px] sm:text-xs rounded-xl border border-indigo-200 shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                            <i class="fa-solid fa-expand text-indigo-500 text-xs"></i> View enlarged photo
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @else
                            <!-- NO PHOTO: DAILY REPORT SPANS FULL WIDTH -->
                            @if(!empty($dailyReportText))
                                <div class="w-full">
                                    <div class="p-5 sm:p-6 bg-white rounded-2xl border-2 border-indigo-200/90 shadow-sm relative overflow-hidden">
                                        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-indigo-100 flex-wrap gap-2">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs shrink-0">
                                                    <i class="fa-solid fa-file-lines"></i>
                                                </div>
                                                <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                                    Daily Report:
                                                </h6>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200/70">
                                                    Archived Report
                                                </span>
                                                <a href="{{ route('teacher.daily-logs.pdf', $log->id) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-[#1E2657] bg-white border border-black/15 rounded-lg hover:bg-slate-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent" 
                                                   title="View Standardized Formal Documentation as PDF">
                                                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                                    <span>View PDF</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="m-0 leading-relaxed text-slate-700 font-medium">
                                            {!! $formatHtml($dailyReportText) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- BOTTOM SECTION: TONIGHT'S SUGGESTED ACTIVITY (SPANS FULL WIDTH, Green Accent) -->
                        @if(!empty($suggestedActivityText))
                            <div class="w-full">
                                <div class="p-5 sm:p-6 bg-emerald-50/40 rounded-2xl border-2 border-emerald-300/80 shadow-sm relative overflow-hidden">
                                    <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-emerald-100/90 flex-wrap gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs shrink-0">
                                                <i class="fa-solid fa-house-chimney-user"></i>
                                            </div>
                                            <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                                Tonight's Suggested Activity:
                                            </h6>
                                        </div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-md border border-emerald-300">
                                            Home Practice
                                        </span>
                                    </div>
                                    <div class="m-0 leading-relaxed text-slate-800 font-medium text-[13px] sm:text-[14px]">
                                        {!! $formatHtml($suggestedActivityText) !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-1 pt-4 border-t border-dashed border-black/10">
                            @include('components.comment-thread', ['item' => $log])
                        </div>
                    </div>
                </div>
            @empty
                <div class="w-full text-center py-12 bg-white rounded-2xl border-2 border-dashed border-black/15 p-6">
                    <div class="w-14 h-14 rounded-full bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <h6 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No formal documentation records found</h6>
                    <p class="text-[13px] font-medium text-[#9CA0AF] m-0">Submitted observation entries and standardized notes will automatically populate here.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Enlarge Photo Lightbox Modal -->
    <div x-show="enlargedPhotoUrl" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-2 sm:p-6" @keydown.escape.window="enlargedPhotoUrl = null">
        <div x-show="enlargedPhotoUrl" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="enlargedPhotoUrl = null"></div>
        <div x-show="enlargedPhotoUrl" x-transition class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col z-10 border border-slate-200">
            <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm sm:text-base truncate pr-4 m-0" x-text="enlargedPhotoTitle"></h4>
                <button type="button" @click="enlargedPhotoUrl = null" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 active:scale-90 flex items-center justify-center text-white transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto flex items-center justify-center bg-slate-900/5 max-h-[calc(90vh-60px)]">
                <img :src="enlargedPhotoUrl" :alt="enlargedPhotoTitle" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-md">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// --- AUTO-SAVE DRAFT LOGIC ---
// --- STRICT DATE BINDING & FILTER SYNC LOGIC ---
const dailyLogCache = {};
let currentDailyLogKey = '';

function handleFileInputChange(input, previewId, filenameId) {
    const preview = document.getElementById(previewId);
    const filenameEl = document.getElementById(filenameId);
    if (input.files && input.files[0]) {
        if (filenameEl) filenameEl.textContent = input.files[0].name;
        if (preview) preview.classList.remove('hidden');
    }
}

function clearSelectedFile(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (input) input.value = '';
    if (preview) preview.classList.add('hidden');
}

document.addEventListener("DOMContentLoaded", function() {
    const studentIdInput = document.getElementById('student_id');
    const logDateInput = document.getElementById('log_date');
    const rawNotes = document.getElementById('rawNotes');
    const formalNotes = document.getElementById('formalNotes');
    const homeRec = document.getElementById('home_recommendations');
    const photoInput = document.getElementById('photoInput');
    const btnGenRec = document.getElementById('btnGenerateRecommendations');
    const form = document.getElementById('dailyLogForm');
    const filterStudent = document.getElementById('filterStudent');

    function getCacheKey() {
        const sid = studentIdInput ? studentIdInput.value : '';
        const date = logDateInput ? logDateInput.value : '';
        if (!sid || !date) return '';
        return `${sid}_${date}`;
    }

    function saveCurrentToCache() {
        if (!currentDailyLogKey) return;
        if (!dailyLogCache[currentDailyLogKey]) dailyLogCache[currentDailyLogKey] = {};
        if (rawNotes) dailyLogCache[currentDailyLogKey].rawNotes = rawNotes.value;
        if (formalNotes) dailyLogCache[currentDailyLogKey].formalNotes = formalNotes.value;
        if (homeRec) dailyLogCache[currentDailyLogKey].homeRec = homeRec.value;
    }

    function loadFromCache() {
        const newKey = getCacheKey();
        if (currentDailyLogKey && currentDailyLogKey !== newKey) {
            saveCurrentToCache();
        }
        currentDailyLogKey = newKey;

        if (!newKey) return;

        const cached = dailyLogCache[newKey];
        if (cached) {
            if (rawNotes) rawNotes.value = cached.rawNotes || '';
            if (formalNotes) formalNotes.value = cached.formalNotes || '';
            if (homeRec) homeRec.value = cached.homeRec || '';
        } else {
            if (rawNotes) rawNotes.value = '';
            if (formalNotes) formalNotes.value = '';
            if (homeRec) homeRec.value = '';
        }
    }

    // Save initial state if preset
    currentDailyLogKey = getCacheKey();
    if (currentDailyLogKey) {
        dailyLogCache[currentDailyLogKey] = {
            rawNotes: rawNotes ? rawNotes.value : '',
            formalNotes: formalNotes ? formalNotes.value : '',
            homeRec: homeRec ? homeRec.value : ''
        };
    }

    if (rawNotes) rawNotes.addEventListener('input', saveCurrentToCache);
    if (formalNotes) formalNotes.addEventListener('input', saveCurrentToCache);
    if (homeRec) homeRec.addEventListener('input', saveCurrentToCache);

    if (logDateInput) {
        logDateInput.addEventListener('change', loadFromCache);
        logDateInput.addEventListener('input', loadFromCache);
    }

    window.syncStudentFilter = function() {
        if (studentIdInput && studentIdInput.value && filterStudent) {
            filterStudent.value = studentIdInput.value;
            if (typeof window.applyFilters === 'function') {
                window.applyFilters();
            }
        }
    };

    function updateStudentInputState() {
        const hasStudent = Boolean(studentIdInput && studentIdInput.value);
        const noStudentNotice = document.getElementById('noStudentNotice');
        const saveBtn = document.getElementById('saveDailyLogBtn');
        const generateAiBtn = document.getElementById('generateAiBtn');

        if (!hasStudent) {
            if (rawNotes) {
                rawNotes.disabled = true;
                rawNotes.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                rawNotes.classList.remove('bg-white');
                rawNotes.setAttribute('placeholder', 'Select a student above to enable Quick Notes...');
            }
            if (formalNotes) {
                formalNotes.disabled = true;
                formalNotes.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                formalNotes.classList.remove('bg-white');
                formalNotes.setAttribute('placeholder', 'Select a student above to enable Formal Documentation...');
            }
            if (homeRec) {
                homeRec.disabled = true;
                homeRec.classList.add('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                homeRec.classList.remove('bg-white');
                homeRec.setAttribute('placeholder', 'Select a student above to enable Tonight\'s Suggested Activity...');
            }
            if (btnGenRec) {
                btnGenRec.disabled = true;
                btnGenRec.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (photoInput) {
                photoInput.disabled = true;
            }
            if (generateAiBtn) {
                generateAiBtn.disabled = true;
                generateAiBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            if (noStudentNotice) noStudentNotice.classList.remove('hidden');
        } else {
            if (rawNotes) {
                rawNotes.disabled = false;
                rawNotes.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                rawNotes.classList.add('bg-white');
                rawNotes.setAttribute('placeholder', 'e.g., he ate snacks, played with blocks, got distracted...');
            }
            if (formalNotes) {
                formalNotes.disabled = false;
                formalNotes.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                formalNotes.classList.add('bg-white');
                formalNotes.setAttribute('placeholder', 'Type formal documentation directly here, or click \'Rewrite Professionally\' above...');
            }
            if (homeRec) {
                homeRec.disabled = false;
                homeRec.classList.remove('bg-slate-100', 'cursor-not-allowed', 'opacity-70');
                homeRec.classList.add('bg-white');
                homeRec.setAttribute('placeholder', 'Suggested home practice activities will appear here, or type your own instructions for parents...');
            }
            if (btnGenRec) {
                btnGenRec.disabled = false;
                btnGenRec.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (photoInput) {
                photoInput.disabled = false;
            }
            if (generateAiBtn) {
                generateAiBtn.disabled = false;
                generateAiBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (noStudentNotice) noStudentNotice.classList.add('hidden');
        }
    }

    // Initial student input state
    updateStudentInputState();

    if (studentIdInput) {
        studentIdInput.addEventListener('change', function() {
            updateStudentInputState();
            loadFromCache();
            window.syncStudentFilter();
        });
        // Initial sync if student already pre-selected
        if (studentIdInput.value) {
            window.syncStudentFilter();
        }
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!studentIdInput || !studentIdInput.value) {
                e.preventDefault();
                window.showToast('Please select a student before saving.', 'warning');
                if (studentIdInput) studentIdInput.focus();
                return false;
            }

            const raw = rawNotes ? rawNotes.value.trim() : '';
            const formal = formalNotes ? formalNotes.value.trim() : '';

            if (!raw && !formal) {
                e.preventDefault();
                window.showToast('Please provide notes in Quick Notes or directly in Formal Documentation before saving.', 'warning');
                if (formalNotes) formalNotes.focus();
                return false;
            }

            // Ensure inputs are not disabled so their values are sent in POST
            if (rawNotes) rawNotes.disabled = false;
            if (formalNotes) formalNotes.disabled = false;
            if (homeRec) homeRec.disabled = false;
            if (photoInput) photoInput.disabled = false;

            window.hasUnsavedAiChanges = false;
        });
    }
});

// --- FEED SEARCH & FILTER LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('logSearch');
    const filterStudent = document.getElementById('filterStudent');
    const filterDate = document.getElementById('filterDate');
    const filterQuarter = document.getElementById('filterQuarter');
    const logItems = document.querySelectorAll('.log-item');

    window.applyFilters = function() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const studentId = filterStudent ? filterStudent.value : 'all';
        const dateVal = filterDate ? filterDate.value : '';
        const quarter = filterQuarter ? filterQuarter.value : 'all';

        logItems.forEach(card => {
            const text = card.textContent.toLowerCase();
            const cardStudentId = card.getAttribute('data-student-id');
            const cardLogDate = card.getAttribute('data-log-date');
            const cardQuarter = card.getAttribute('data-quarter');

            const matchesSearch = text.includes(term);
            const matchesStudent = (studentId === 'all' || cardStudentId === studentId);
            const matchesDate = (!dateVal || cardLogDate === dateVal);
            const matchesQuarter = (quarter === 'all' || cardQuarter === quarter);

            if (matchesSearch && matchesStudent && matchesDate && matchesQuarter) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    };

    if (searchInput) searchInput.addEventListener('input', window.applyFilters);
    if (filterStudent) filterStudent.addEventListener('change', window.applyFilters);
    if (filterDate) filterDate.addEventListener('change', window.applyFilters);
    if (filterQuarter) filterQuarter.addEventListener('change', window.applyFilters);

    window.applyFilters();
});

// --- AI REWRITE LOGIC ---
document.getElementById('generateAiBtn').addEventListener('click', function() {
    let sid = document.getElementById('student_id') ? document.getElementById('student_id').value : '';
    if (!sid) {
        window.showToast('Please select a student first!', 'warning');
        return;
    }
    let rawNotes = document.getElementById('rawNotes').value;
    let btn = this;
    let loading = document.getElementById('aiLoading');
    let output = document.getElementById('formalNotes');

    if (!rawNotes.trim()) {
        window.showToast('Please type some quick notes first!', 'warning');
        return;
    }

    btn.disabled = true;
    loading.classList.remove('hidden'); 
    output.value = "Translating notes..."; 

    fetch('{{ route('teacher.daily-logs.generate-ai') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ raw_notes: rawNotes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            window.showToast(data.error, 'error');
            output.value = "";
        } else {
            output.value = data.formal_note;
            window.hasUnsavedAiChanges = true;
            window.showToast('Formal note generated successfully.', 'success');

            let sid = document.getElementById('student_id').value;
            let date = document.getElementById('log_date').value;
            if (sid && date) {
                let key = `${sid}_${date}`;
                if (!dailyLogCache[key]) dailyLogCache[key] = {};
                dailyLogCache[key].formalNotes = data.formal_note;
                dailyLogCache[key].rawNotes = document.getElementById('rawNotes').value;
                currentDailyLogKey = key;
            }
        }
    })
    .catch(error => {
        window.showToast('Something went wrong connecting to the AI.', 'error');
        output.value = "";
    })
    .finally(() => {
        btn.disabled = false;
        loading.classList.add('hidden'); 
    });
});

// --- AI HOME RECOMMENDATIONS LOGIC ---
const btnGenRec = document.getElementById('btnGenerateRecommendations');
if (btnGenRec) {
    btnGenRec.addEventListener('click', function() {
        const sid = document.getElementById('student_id') ? document.getElementById('student_id').value : '';
        if (!sid) {
            window.showToast('Please select a student first!', 'warning');
            return;
        }

        const formalNotesEl = document.getElementById('formalNotes');
        const rawNotesEl = document.getElementById('rawNotes');
        const notesToSend = (formalNotesEl ? formalNotesEl.value.trim() : '') || (rawNotesEl ? rawNotesEl.value.trim() : '');
        const targetBox = document.getElementById('home_recommendations');
        const loading = document.getElementById('recLoading');

        if (!notesToSend) {
            window.showToast('Please provide some quick notes or formal documentation first so the AI knows what to recommend!', 'warning');
            return;
        }

        btnGenRec.disabled = true;
        if (loading) loading.classList.remove('hidden');

        fetch('{{ route('teacher.daily-logs.generate-recommendations') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                notes: notesToSend,
                student_id: sid
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.recommendation) {
                if (targetBox) {
                    targetBox.value = data.recommendation;
                    window.hasUnsavedAiChanges = true;
                }
                window.showToast('Suggested activity generated successfully.', 'success');

                const date = document.getElementById('log_date') ? document.getElementById('log_date').value : '';
                if (sid && date) {
                    const key = `${sid}_${date}`;
                    if (!dailyLogCache[key]) dailyLogCache[key] = {};
                    dailyLogCache[key].homeRec = data.recommendation;
                }
            } else {
                window.showToast(data.message || 'Failed to generate recommendations.', 'error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            window.showToast('Something went wrong connecting to the AI recommendations.', 'error');
        })
        .finally(() => {
            btnGenRec.disabled = false;
            if (loading) loading.classList.add('hidden');
        });
    });
}
</script>
@endpush
@endsection