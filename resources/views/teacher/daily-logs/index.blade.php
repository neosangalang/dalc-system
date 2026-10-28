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

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm animate-fade-in-down mb-6">
            <div class="flex items-center mb-2 font-extrabold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Oops! Something went wrong:
            </div>
            <ul class="list-disc pl-5 text-[13px] sm:text-sm font-medium m-0 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] m-0 flex items-center tracking-tight">
                <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm shrink-0">
                    <i class="fa fa-pencil-alt text-lg"></i>
                </div>
                Daily Logging
            </h1>
            <p class="text-[13px] font-medium text-[#6B7086] mt-1 sm:ml-14 m-0 leading-tight">Record observations and confirm formal documentation.</p>
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
                        
                        <button type="button" id="generateAiBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
                            <i class="fa fa-magic mr-1.5"></i> Rewrite Professionally
                        </button>

                        <!-- AI Clinical Disclaimer & Ethics Notice -->
                        <div class="mt-2.5 flex items-center justify-between text-[11px] text-[#6B7086] px-1">
                            <span class="flex items-center gap-1.5">
                                <i class="fa fa-shield-halved text-[#3B82F6]"></i>
                                <span>AI SPED Draft: Educator review required before saving (<a href="{{ route('legal.ai-ethics') }}" target="_blank" class="text-[#3B82F6] font-bold hover:underline">Clinical Policy</a>)</span>
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
                        <button type="submit" id="saveDailyLogBtn" class="w-full inline-flex items-center justify-center px-6 py-4 bg-[#1E2657] text-white text-[14px] font-extrabold rounded-xl hover:bg-[#12163A] transition-all shadow-sm">
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
                    <div class="p-5 sm:p-6 text-[13px] sm:text-[14px] text-[#6B7086] leading-relaxed bg-white font-medium">
                        @if($log->image_path)
                            <div class="mb-4 text-center">
                                <div class="relative group cursor-pointer inline-block max-w-sm rounded-xl overflow-hidden shadow-sm border border-black/10"
                                     @click="enlargedPhotoUrl = '{{ route('secure.log-photo', $log->id) }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($log->student->first_name) }}'"
                                     title="Click to enlarge photo">
                                    <img src="{{ route('secure.log-photo', $log->id) }}" alt="Observation Photo" class="max-h-[220px] w-auto mx-auto object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2">
                                        <i class="fa-solid fa-magnifying-glass-plus text-base"></i> Click to enlarge
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="p-4 bg-[#F4F6FA] rounded-xl border border-black/10 text-[#1E2657]">
                            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                                <h6 class="text-[11px] font-extrabold text-[#10B981] uppercase tracking-widest m-0 flex items-center">
                                    <i class="fa-solid fa-file-contract mr-1.5"></i> Standardized Formal Documentation
                                </h6>
                                <a href="{{ route('teacher.daily-logs.pdf', $log->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-[#1E2657] bg-white border border-black/15 rounded-lg hover:bg-slate-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent" 
                                   title="Download Standardized Formal Documentation as PDF">
                                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                    <span>Download PDF</span>
                                </a>
                            </div>
                            <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed font-medium whitespace-pre-wrap">{{ $log->notes }}</p>
                        </div>

                        @if($log->home_recommendations)
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
        </div>
    </div>

    <!-- Enlarge Photo Lightbox Modal -->
    <div x-show="enlargedPhotoUrl" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6" @keydown.escape.window="enlargedPhotoUrl = null">
        <div x-show="enlargedPhotoUrl" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="enlargedPhotoUrl = null"></div>
        <div x-show="enlargedPhotoUrl" x-transition class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col z-10 border border-slate-200">
            <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm sm:text-base truncate pr-4 m-0" x-text="enlargedPhotoTitle"></h4>
                <button type="button" @click="enlargedPhotoUrl = null" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer">
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
                alert('Please select a student before saving.');
                if (studentIdInput) studentIdInput.focus();
                return false;
            }

            const raw = rawNotes ? rawNotes.value.trim() : '';
            const formal = formalNotes ? formalNotes.value.trim() : '';

            if (!raw && !formal) {
                e.preventDefault();
                alert('Please provide notes in Quick Notes or directly in Formal Documentation before saving.');
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
        alert('Please select a student first!');
        return;
    }
    let rawNotes = document.getElementById('rawNotes').value;
    let btn = this;
    let loading = document.getElementById('aiLoading');
    let output = document.getElementById('formalNotes');

    if (!rawNotes.trim()) {
        alert('Please type some quick notes first!');
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
            alert(data.error);
            output.value = "";
        } else {
            output.value = data.formal_note;
            window.hasUnsavedAiChanges = true;

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
        alert('Something went wrong connecting to the AI.');
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