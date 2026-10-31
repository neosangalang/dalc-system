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
<div x-data="{ activeTab: '{{ request('tab', 'create') }}' }" class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">


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
                <form id="dailyLogForm" action="{{ route('teacher.daily-logs.store') }}" method="POST" class="m-0 space-y-6">
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

    <!-- TAB 2: FORMAL DOCUMENTATION ARCHIVE & LIST -->
    <div x-show="activeTab === 'history'" x-transition.opacity.duration.300ms x-cloak class="w-full">
        
        <!-- FILTER BAR WITH GUARDIAN-STYLE ARCHIVE DATE NAVIGATION & SORTING -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-black/15 mb-4 sm:mb-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-black/10">
                <h6 class="text-xs sm:text-[14px] font-extrabold text-[#1E2657] m-0 flex items-center">
                    <i class="fa fa-filter text-[#3B82F6] mr-2"></i> Daily Logs Filters & Archive Navigation
                </h6>
                <span id="archiveCountBadge" class="text-[11px] font-extrabold text-[#6B7086] bg-[#F4F6FA] border border-black/10 px-3 py-1 rounded-xl self-start sm:self-auto flex items-center shadow-xs">
                    <i class="fa-solid fa-clock-rotate-left text-[#3B82F6] mr-1.5"></i>
                    <span id="archiveCountText">Loading archive...</span>
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-end">
                <!-- 1. Student Filter -->
                <div class="md:col-span-3">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5 flex items-center">
                        <i class="fa fa-user-graduate text-[#3B82F6] mr-1.5"></i> Filter by Student
                    </label>
                    <select id="filterStudent" class="w-full rounded-xl border-black/15 px-3 py-2 text-xs sm:text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                        <option value="all">All Assigned Students</option>
                        @foreach($myStudents as $student)
                            <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Archive Date Navigation & Sorting Component -->
                <div class="md:col-span-5">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5 flex items-center">
                        <i class="fa-regular fa-calendar-days text-[#3B82F6] mr-1.5"></i> Archive Date Navigation
                    </label>
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <!-- Step to Older Report (Previous) -->
                        <button type="button" id="btnPrevDate" title="Previous older report" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:border-slate-200">
                            <i class="fa-solid fa-chevron-left text-[11px] text-slate-500"></i>
                            <span class="hidden sm:inline">Previous</span>
                        </button>

                        <!-- Date Picker Input -->
                        <div class="relative flex items-center flex-1 min-w-[120px]">
                            <div class="absolute left-2.5 pointer-events-none text-indigo-600 text-xs">
                                <i class="fa-regular fa-calendar-days"></i>
                            </div>
                            <input type="date" id="filterDate" class="w-full pl-7 sm:pl-8 pr-2 py-1.5 sm:py-2 bg-white border-2 border-indigo-200 hover:border-indigo-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-slate-800 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-colors cursor-pointer" title="Select specific log date">
                        </div>

                        <!-- Step to Newer Report (Next) -->
                        <button type="button" id="btnNextDate" title="Next newer report" class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:border-slate-200">
                            <span class="hidden sm:inline">Next</span>
                            <i class="fa-solid fa-chevron-right text-[11px] text-slate-500"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. Quarter Filter -->
                <div class="md:col-span-2">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Quarter</label>
                    <select id="filterQuarter" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                        <option value="all">All Quarters</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>

                <!-- 4. Search Keywords -->
                <div class="md:col-span-2">
                    <label class="block text-[9px] sm:text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Keywords</label>
                    <div class="relative w-full shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-[#9CA0AF] pointer-events-none">
                            <i class="fa fa-search text-xs"></i>
                        </span>
                        <input type="text" id="logSearch" class="w-full border-0 py-1.5 sm:py-2 pl-8 pr-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Search notes...">
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
                     data-log-date="{{ \Carbon\Carbon::parse($log->log_date)->format('Y-m-d') }}"
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

                    <!-- Card Body: Clean Standardized Formal Documentation -->
                    <div class="p-5 sm:p-6 text-[13px] sm:text-[14px] text-[#6B7086] leading-relaxed bg-white font-medium">
                        <div class="p-4 bg-[#F4F6FA] rounded-xl border border-black/10 text-[#1E2657]">
                            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                                <h6 class="text-[11px] font-extrabold text-[#10B981] uppercase tracking-widest m-0 flex items-center">
                                    <i class="fa-solid fa-file-contract mr-1.5"></i> Standardized Formal Documentation
                                </h6>
                                <a href="{{ route('teacher.daily-logs.pdf', $log->id) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-[#1E2657] bg-white border border-black/15 rounded-lg hover:bg-slate-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent" 
                                   title="View Standardized Formal Documentation as PDF">
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                    <span>View PDF</span>
                                </a>
                            </div>
                            <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed font-medium whitespace-pre-wrap">{{ $log->notes }}</p>
                        </div>

                        @if(!empty($log->home_recommendations))
                            <div class="mt-4 p-4 bg-emerald-50/60 rounded-xl border border-emerald-200 text-emerald-950">
                                <h6 class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-widest mb-1.5 flex items-center m-0">
                                    <i class="fa fa-house-user mr-1.5 text-emerald-600"></i> Tonight's Suggested Home Activity
                                </h6>
                                <p class="m-0 text-[13px] leading-relaxed font-medium whitespace-pre-wrap">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-dashed border-black/10">
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

            <!-- Dynamic Empty State when filters yield no matches -->
            <div id="noMatchingLogsState" class="hidden w-full text-center py-12 sm:py-16 px-4 sm:px-6 bg-white rounded-2xl shadow-sm border-2 border-dashed border-slate-200">
                <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>
                <h5 id="noMatchingLogsTitle" class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-1">
                    No Report Found for Selected Date
                </h5>
                <p id="noMatchingLogsDesc" class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 max-w-md mx-auto mb-5">
                    There are no formal documentation records recorded on this specific date. Pick another date using the selector above or reset filters.
                </p>
                <button type="button" id="btnResetFilters" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1E2657] hover:bg-[#12163A] text-white font-extrabold text-xs rounded-xl transition-colors shadow-sm cursor-pointer">
                    <i class="fa-solid fa-rotate-left"></i> View All Recorded Logs
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// --- AUTO-SAVE DRAFT & CACHE LOGIC ---
const dailyLogCache = {};
let currentDailyLogKey = '';

document.addEventListener("DOMContentLoaded", function() {
    const studentIdInput = document.getElementById('student_id');
    const logDateInput = document.getElementById('log_date');
    const rawNotes = document.getElementById('rawNotes');
    const formalNotes = document.getElementById('formalNotes');
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
        } else {
            if (rawNotes) rawNotes.value = '';
            if (formalNotes) formalNotes.value = '';
        }
    }

    // Save initial state if preset
    currentDailyLogKey = getCacheKey();
    if (currentDailyLogKey) {
        dailyLogCache[currentDailyLogKey] = {
            rawNotes: rawNotes ? rawNotes.value : '',
            formalNotes: formalNotes ? formalNotes.value : ''
        };
    }

    if (rawNotes) rawNotes.addEventListener('input', saveCurrentToCache);
    if (formalNotes) formalNotes.addEventListener('input', saveCurrentToCache);

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

            window.hasUnsavedAiChanges = false;
        });
    }
});

// --- FEED SEARCH, ARCHIVE DATE NAVIGATION & SORTING LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('logSearch');
    const filterStudent = document.getElementById('filterStudent');
    const filterDate = document.getElementById('filterDate');
    const btnPrevDate = document.getElementById('btnPrevDate');
    const btnNextDate = document.getElementById('btnNextDate');
    const filterQuarter = document.getElementById('filterQuarter');
    const logItems = document.querySelectorAll('.log-item');
    const noMatchingLogsState = document.getElementById('noMatchingLogsState');
    const noMatchingLogsTitle = document.getElementById('noMatchingLogsTitle');
    const archiveCountText = document.getElementById('archiveCountText');
    const btnResetFilters = document.getElementById('btnResetFilters');

    // Helper: format YYYY-MM-DD to readable date like "Oct 29, 2026"
    function formatDateReadable(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${months[month]} ${day}, ${year}`;
    }

    // Get all distinct recorded dates sorted descending for the selected student & quarter
    function getAvailableDates() {
        const studentId = filterStudent ? filterStudent.value : 'all';
        const quarter = filterQuarter ? filterQuarter.value : 'all';
        const datesSet = new Set();

        logItems.forEach(card => {
            const cardStudentId = card.getAttribute('data-student-id');
            const cardQuarter = card.getAttribute('data-quarter');
            const cardDate = card.getAttribute('data-log-date');

            const matchesStudent = (studentId === 'all' || cardStudentId === studentId);
            const matchesQuarter = (quarter === 'all' || cardQuarter === quarter);

            if (matchesStudent && matchesQuarter && cardDate) {
                datesSet.add(cardDate);
            }
        });

        // Sort descending (newest first)
        return Array.from(datesSet).sort((a, b) => b.localeCompare(a));
    }

    // Update Previous / Next navigation buttons
    function updateNavButtons(availableDates) {
        const currentDate = filterDate ? filterDate.value : '';

        if (!btnPrevDate || !btnNextDate) return;

        if (availableDates.length === 0) {
            btnPrevDate.disabled = true;
            btnNextDate.disabled = true;
            btnPrevDate.title = 'No archived dates available';
            btnNextDate.title = 'No archived dates available';
            return;
        }

        if (!currentDate) {
            // Viewing all: Next is disabled, Previous jumps to the latest recorded date
            btnNextDate.disabled = true;
            btnNextDate.title = 'Already viewing latest records';

            btnPrevDate.disabled = false;
            btnPrevDate.title = `Jump to latest report: ${formatDateReadable(availableDates[0])}`;
            return;
        }

        const currentIndex = availableDates.indexOf(currentDate);

        if (currentIndex === -1) {
            const newerDates = availableDates.filter(d => d > currentDate);
            const olderDates = availableDates.filter(d => d < currentDate);

            btnNextDate.disabled = newerDates.length === 0;
            btnNextDate.title = newerDates.length > 0 
                ? `Jump to newer report: ${formatDateReadable(newerDates[newerDates.length - 1])}` 
                : 'No newer reports';

            btnPrevDate.disabled = olderDates.length === 0;
            btnPrevDate.title = olderDates.length > 0 
                ? `Jump to older report: ${formatDateReadable(olderDates[0])}` 
                : 'No older reports';
            return;
        }

        // List is sorted descending: index 0 is newest, index N is oldest
        // Next moves towards newer date (index - 1)
        if (currentIndex > 0) {
            const nextDate = availableDates[currentIndex - 1];
            btnNextDate.disabled = false;
            btnNextDate.title = `Next newer report: ${formatDateReadable(nextDate)}`;
        } else {
            btnNextDate.disabled = true;
            btnNextDate.title = 'No newer archived reports (latest)';
        }

        // Previous moves towards older date (index + 1)
        if (currentIndex < availableDates.length - 1) {
            const prevDate = availableDates[currentIndex + 1];
            btnPrevDate.disabled = false;
            btnPrevDate.title = `Previous older report: ${formatDateReadable(prevDate)}`;
        } else {
            btnPrevDate.disabled = true;
            btnPrevDate.title = 'No older archived reports (earliest)';
        }
    }

    // Apply filtering to .log-item cards
    window.applyFilters = function() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const studentId = filterStudent ? filterStudent.value : 'all';
        const dateVal = filterDate ? filterDate.value : '';
        const quarter = filterQuarter ? filterQuarter.value : 'all';

        const availableDates = getAvailableDates();
        updateNavButtons(availableDates);

        let visibleCount = 0;

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
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update Archive Count Badge
        if (archiveCountText) {
            if (dateVal) {
                archiveCountText.textContent = `${visibleCount} Entry on ${formatDateReadable(dateVal)}`;
            } else {
                archiveCountText.textContent = `${visibleCount} Recorded ${visibleCount === 1 ? 'Entry' : 'Entries'}`;
            }
        }

        // Handle Empty State
        if (noMatchingLogsState) {
            if (visibleCount === 0 && logItems.length > 0) {
                noMatchingLogsState.classList.remove('hidden');
                if (noMatchingLogsTitle) {
                    if (dateVal) {
                        noMatchingLogsTitle.textContent = `No Report Found for ${formatDateReadable(dateVal)}`;
                    } else {
                        noMatchingLogsTitle.textContent = 'No Formal Documentation Records Found';
                    }
                }
            } else {
                noMatchingLogsState.classList.add('hidden');
            }
        }
    };

    // Event listeners
    if (searchInput) searchInput.addEventListener('input', window.applyFilters);
    if (filterStudent) filterStudent.addEventListener('change', window.applyFilters);
    if (filterQuarter) filterQuarter.addEventListener('change', window.applyFilters);

    if (filterDate) {
        filterDate.addEventListener('change', window.applyFilters);
    }

    if (btnPrevDate) {
        btnPrevDate.addEventListener('click', function() {
            const availableDates = getAvailableDates();
            if (availableDates.length === 0) return;
            const currentDate = filterDate ? filterDate.value : '';

            if (!currentDate) {
                filterDate.value = availableDates[0];
            } else {
                const idx = availableDates.indexOf(currentDate);
                if (idx !== -1 && idx < availableDates.length - 1) {
                    filterDate.value = availableDates[idx + 1];
                } else if (idx === -1) {
                    const older = availableDates.filter(d => d < currentDate);
                    if (older.length > 0) filterDate.value = older[0];
                }
            }

            window.applyFilters();
        });
    }

    if (btnNextDate) {
        btnNextDate.addEventListener('click', function() {
            const availableDates = getAvailableDates();
            if (availableDates.length === 0) return;
            const currentDate = filterDate ? filterDate.value : '';

            if (currentDate) {
                const idx = availableDates.indexOf(currentDate);
                if (idx > 0) {
                    filterDate.value = availableDates[idx - 1];
                } else if (idx === -1) {
                    const newer = availableDates.filter(d => d > currentDate);
                    if (newer.length > 0) filterDate.value = newer[newer.length - 1];
                }
            }

            window.applyFilters();
        });
    }

    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', function() {
            if (filterDate) filterDate.value = '';
            if (searchInput) searchInput.value = '';
            if (filterQuarter) filterQuarter.value = 'all';
            window.applyFilters();
        });
    }

    // Initialize on page load
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
</script>
@endpush
@endsection