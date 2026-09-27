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
<div x-data="{ activeTab: 'create' }" class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <!-- BACK BUTTON -->
    <div class="flex">
        <a href="{{ route('teacher.students.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 hover:text-[#1E2657] shadow-sm transition-colors decoration-transparent focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
            <i class="fa fa-arrow-left mr-2"></i> Back to Students
        </a>
    </div>

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
            <p class="text-[13px] font-medium text-[#6B7086] mt-1 sm:ml-14 m-0 leading-tight">Record observations and generate formal documentation.</p>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="bg-[#F4F6FA] p-1.5 rounded-2xl flex w-full sm:w-auto shadow-sm border border-black/5">
            <button @click="activeTab = 'create'" 
                    :class="activeTab === 'create' ? 'bg-white text-[#3B82F6] shadow-sm border border-black/5' : 'text-[#6B7086] hover:text-[#1E2657]'" 
                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl font-extrabold text-[13px] transition-all flex items-center justify-center">
                <i class="fa fa-pen mr-2"></i> New Log
            </button>
            <button @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'bg-white text-[#3B82F6] shadow-sm border border-black/5' : 'text-[#6B7086] hover:text-[#1E2657]'" 
                    class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl font-extrabold text-[13px] transition-all flex items-center justify-center">
                <i class="fa fa-clock-rotate-left mr-2"></i> Recent History
            </button>
        </div>
    </div>

    @php
        $selectedStudentId = old('student_id', request('student_id'));
        $selectedStudent = $myStudents->firstWhere('id', $selectedStudentId);
    @endphp

    <!-- TAB 1: CREATE NEW LOG FORM -->
    <div x-show="activeTab === 'create'" x-transition.opacity.duration.300ms class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA]">
                <h6 class="text-[14px] font-extrabold text-[#1E2657] m-0 flex items-center">
                    <i class="fa fa-plus-circle text-[#3B82F6] mr-2"></i> Create Observation Entry
                </h6>
            </div>
            
            <div class="p-5 sm:p-8">
                <form id="dailyLogForm" action="{{ route('teacher.daily-logs.store') }}" method="POST" enctype="multipart/form-data" class="m-0">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                        
                        <!-- LEFT COLUMN: Main Form Content -->
                        <div class="lg:col-span-7">
                            
                            <input type="hidden" name="student_id" id="student_id" value="{{ $selectedStudentId }}">

                            @if($selectedStudent)
                                <div class="mb-5 flex items-center gap-4 bg-[#F4F6FA] border border-black/10 p-3 sm:p-4 rounded-2xl shadow-sm">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-extrabold text-sm shadow-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shrink-0 border border-black/10">
                                        {{ strtoupper(substr($selectedStudent->first_name, 0, 1) . substr($selectedStudent->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest m-0 mb-0.5">Logging Observation For</p>
                                        <p class="text-[16px] font-extrabold text-[#1E2657] m-0 leading-tight">{{ $selectedStudent->first_name }} {{ $selectedStudent->last_name }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Date</label>
                                    <input type="date" name="log_date" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" value="{{ old('log_date', date('Y-m-d')) }}" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Quarter</label>
                                    <select name="quarter" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white" required>
                                        <option value="Q1" {{ old('quarter') == 'Q1' ? 'selected' : '' }}>Q1</option>
                                        <option value="Q2" {{ old('quarter') == 'Q2' ? 'selected' : '' }}>Q2</option>
                                        <option value="Q3" {{ old('quarter') == 'Q3' ? 'selected' : '' }}>Q3</option>
                                        <option value="Q4" {{ old('quarter') == 'Q4' ? 'selected' : '' }}>Q4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5 flex items-center">
                                    <i class="fa fa-camera text-[#9CA0AF] mr-1.5"></i> Attach Photo (Optional)
                                </label>
                                <input type="file" name="photo" id="photoInput" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-black/15 rounded-xl cursor-pointer shadow-sm bg-white" accept="image/jpeg, image/png">
                            </div>

                            <hr class="border-black/10 my-6">

                            <div class="p-5 mb-6 bg-[#F4F6FA] border border-dashed border-[#3B82F6]/30 rounded-2xl relative overflow-hidden">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-[12px] font-extrabold text-[#3B82F6] uppercase tracking-widest m-0 flex items-center">
                                        <i class="fa fa-bolt mr-1.5"></i> 1. Quick Notes (Informal)
                                    </label>
                                </div>
                                <textarea name="raw_notes" id="rawNotes" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm mb-4 custom-scrollbar bg-white" rows="4" required placeholder="e.g., he ate snacks, played with blocks, got distracted...">{{ old('raw_notes') }}</textarea>
                                
                                <button type="button" id="generateAiBtn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
                                    <i class="fa fa-magic mr-1.5"></i> Rewrite Professionally
                                </button>

                                <div id="aiLoading" class="text-center text-[#3B82F6] text-[12px] mt-4 font-extrabold hidden">
                                    <i class="fa fa-spinner fa-spin mr-1.5"></i> AI is thinking...
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] font-extrabold text-[#10B981] uppercase tracking-widest mb-2 flex items-center">
                                    <i class="fa fa-file-alt mr-1.5"></i> 2. Daily Summary
                                </label>
                                <textarea name="notes" id="formalNotes" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] bg-white focus:border-[#10B981] focus:ring-[#10B981] shadow-sm custom-scrollbar" rows="6" required placeholder="The AI-standardized summary will appear here...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Home Recommendations -->
                        <div class="lg:col-span-5 flex flex-col h-full">
                            <div class="p-5 bg-[#10B981]/10 border border-dashed border-[#10B981]/30 rounded-2xl relative overflow-hidden flex-1 flex flex-col min-h-[300px]">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="text-[12px] font-extrabold text-[#10B981] uppercase tracking-widest m-0 flex items-center">
                                        <i class="fa fa-home mr-1.5"></i> 3. Home Recommendations
                                    </label>
                                </div>
                                
                                <textarea name="home_recommendations" id="home_recommendations" class="flex-1 w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] bg-white focus:border-[#10B981] focus:ring-[#10B981] shadow-sm mb-4 custom-scrollbar resize-none" placeholder="AI will generate home activities here...">{{ old('home_recommendations') }}</textarea>
                                
                                <button type="button" id="btnGenerateRecommendations" class="mt-auto w-full inline-flex items-center justify-center px-4 py-3 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#10B981] focus:ring-offset-1">
                                    <i class="fa fa-lightbulb mr-1.5"></i> Generate Home Activities
                                </button>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <div class="lg:col-span-12 mt-4 border-t border-black/10 pt-6 sm:pt-8">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 bg-[#1E2657] text-white text-[14px] font-extrabold rounded-xl hover:bg-[#12163A] transition-colors shadow-sm">
                                <i class="fa fa-save mr-2"></i> Save Daily Log
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 2: RECENT LOGS HISTORY -->
    <div x-show="activeTab === 'history'" x-transition.opacity.duration.300ms x-cloak class="max-w-4xl mx-auto">
        
        <!-- FILTER BAR -->
        <div class="flex flex-col space-y-3 mb-6 px-1">
            <h6 class="text-[13px] font-extrabold text-[#1E2657] m-0">Previously Submitted Logs</h6>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Student Filter -->
                <select id="filterStudent" class="w-full sm:w-64 rounded-xl border-black/15 px-3 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                    <option value="all">All Assigned Students</option>
                    @foreach($myStudents as $student)
                        <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>

                <!-- Search -->
                <div class="relative w-full sm:flex-1 shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#9CA0AF] pointer-events-none">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" id="logSearch" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Search content...">
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
                     data-student-id="{{ $log->student_id }}">
                    
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
                        
                        <form action="{{ route('teacher.daily-logs.destroy', $log->id) }}" method="POST" class="absolute top-4 sm:top-5 right-4 sm:right-5 m-0" onsubmit="return confirm('Are you sure you want to permanently delete this daily log?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-full bg-white border border-black/10 text-[#9CA0AF] hover:text-rose-500 hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm" title="Delete Log">
                                <i class="fa fa-trash-alt text-[12px]"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 sm:p-6 text-[13px] sm:text-[14px] text-[#6B7086] leading-relaxed bg-white font-medium">
                        @if($log->image_path)
                            <div class="mb-5 text-center bg-[#F4F6FA] rounded-xl p-3 border border-dashed border-black/15">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="max-h-48 sm:max-h-64 w-auto mx-auto rounded-lg shadow-sm border border-black/10 object-contain">
                            </div>
                        @endif

                        <div class="mb-2">
                            <span class="font-extrabold text-[#3B82F6] text-[10px] uppercase tracking-widest flex items-center mb-1">
                                <i class="fa fa-file-lines mr-1.5"></i> Daily Summary
                            </span>
                        </div>
                        <div class="whitespace-pre-line text-[#1E2657]">
                            {{ $log->notes }}
                        </div>
                        
                        @if($log->home_recommendations)
                            <div class="mt-6 p-5 rounded-xl bg-[#10B981]/5 border border-[#10B981]/20 border-l-4 border-l-[#10B981]">
                                <span class="font-extrabold text-[#10B981] text-[10px] uppercase tracking-widest flex items-center mb-2">
                                    <i class="fa fa-check-circle mr-1.5"></i> Home Recommendations
                                </span>
                                <p class="mb-0 text-[#1E2657] font-medium whitespace-pre-line text-[13px] leading-relaxed">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Comments Section (Interactive trigger to mark as read) -->
                    <div class="px-5 py-4 sm:px-6 sm:py-5 bg-[#F4F6FA] border-t border-black/10 transition-colors duration-500"
                         :class="hasUnread ? 'bg-red-50/40' : 'bg-[#F4F6FA]'"
                         @mouseenter.once="markAsRead()"
                         @focusin.once="markAsRead()"
                         @touchstart.once="markAsRead()">
                        <div class="text-[10px] font-extrabold uppercase tracking-widest mb-3 flex items-center justify-between"
                             :class="hasUnread ? 'text-red-500' : 'text-[#9CA0AF]'">
                            <span class="flex items-center">
                                <i class="fa fa-comments mr-1.5" :class="hasUnread ? 'text-red-500 animate-bounce' : 'text-[#3B82F6]'"></i> Comments & Feedback
                            </span>
                            <span x-show="hasUnread" class="text-[9px] font-extrabold text-red-500 uppercase tracking-widest">
                                Hover to mark read
                            </span>
                        </div>
                        <div class="bg-white p-0 sm:p-4 rounded-xl border border-black/15 shadow-sm">
                            @include('components.comment-thread', ['item' => $log])
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-16 px-6 bg-white rounded-2xl shadow-sm border border-black/15">
                    <div class="w-16 h-16 rounded-full bg-[#F4F6FA] border border-black/5 text-[#9CA0AF] flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-clock-rotate-left text-2xl"></i>
                    </div>
                    <h6 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No History Found</h6>
                    <p class="text-[13px] font-medium text-[#6B7086] m-0">You haven't submitted any daily logs yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
// --- AUTO-SAVE DRAFT LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const studentIdInput = document.getElementById('student_id');
    const rawNotes = document.getElementById('rawNotes');
    const formalNotes = document.getElementById('formalNotes');
    const homeRecs = document.getElementById('home_recommendations');
    const form = document.getElementById('dailyLogForm');

    if (studentIdInput && studentIdInput.value) {
        const sid = studentIdInput.value;
        const rawKey = `dalc_draft_raw_${sid}`;
        const formalKey = `dalc_draft_formal_${sid}`;
        const homeKey = `dalc_draft_home_${sid}`;

        if (localStorage.getItem(rawKey) && !rawNotes.value) rawNotes.value = localStorage.getItem(rawKey);
        if (localStorage.getItem(formalKey) && !formalNotes.value) formalNotes.value = localStorage.getItem(formalKey);
        if (localStorage.getItem(homeKey) && !homeRecs.value) homeRecs.value = localStorage.getItem(homeKey);

        rawNotes.addEventListener('input', () => localStorage.setItem(rawKey, rawNotes.value));
        formalNotes.addEventListener('input', () => localStorage.setItem(formalKey, formalNotes.value));
        homeRecs.addEventListener('input', () => localStorage.setItem(homeKey, homeRecs.value));

        if (form) {
            form.addEventListener('submit', function() {
                localStorage.removeItem(rawKey);
                localStorage.removeItem(formalKey);
                localStorage.removeItem(homeKey);
            });
        }
    }
});

// --- FRONTEND IMAGE VALIDATION LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG or PNG only).');
                    this.value = '';
                } else if (file.size > 5 * 1024 * 1024) {
                    alert('File is too large. Please select an image smaller than 5MB.');
                    this.value = ''; 
                }
            }
        });
    }
});

// --- FEED SEARCH FILTER LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('logSearch');
    const filterStudent = document.getElementById('filterStudent');
    const logItems = document.querySelectorAll('.log-item');

    function applyFilters() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const studentId = filterStudent ? filterStudent.value : 'all';

        logItems.forEach(card => {
            const text = card.textContent.toLowerCase();
            const cardStudentId = card.getAttribute('data-student-id');

            const matchesSearch = text.includes(term);
            const matchesStudent = (studentId === 'all' || cardStudentId === studentId);

            if (matchesSearch && matchesStudent) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterStudent) filterStudent.addEventListener('change', applyFilters);

    // Initial run to apply the auto-selected student filter on page load
    applyFilters();
});

// --- AI REWRITE LOGIC ---
document.getElementById('generateAiBtn').addEventListener('click', function() {
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
            
            let sid = document.getElementById('student_id').value;
            if(sid) localStorage.setItem(`dalc_draft_formal_${sid}`, data.formal_note);
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

// --- AI HOME RECOMMENDATIONS LOGIC ---
document.getElementById('btnGenerateRecommendations').addEventListener('click', function() {
    let rawNotes = document.getElementById('rawNotes').value;
    let studentId = document.getElementById('student_id').value; 
    let btn = this;
    let targetBox = document.getElementById('home_recommendations');

    if (!studentId) {
        alert('Please return to the student list and select a student first!');
        return;
    }

    if (!rawNotes.trim()) {
        alert('Please write some informal notes first so the AI knows what to recommend!');
        return;
    }

    let originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Generating Activities...';
    btn.disabled = true;

    fetch('{{ route('teacher.daily-logs.generate-recommendations') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            notes: rawNotes,
            student_id: studentId 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            targetBox.value = data.recommendation;
            
            if(studentId) localStorage.setItem(`dalc_draft_home_${studentId}`, data.recommendation);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating recommendations.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection