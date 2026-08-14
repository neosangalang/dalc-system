@extends('layouts.app')

@section('title', 'Daily Logging')

@section('content')

<!-- Added x-data="{ activeTab: 'create' }" to control the tabs -->
<div x-data="{ activeTab: 'create' }" class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-[13px] sm:text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Oops! Something went wrong:
            </div>
            <ul class="list-disc pl-5 text-[13px] sm:text-sm font-medium m-0 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h4 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mr-3 shadow-sm shrink-0">
                    <i class="fa fa-pencil-alt"></i>
                </div>
                Daily Logging
            </h4>
            <p class="text-slate-500 text-[13px] sm:text-sm font-medium mt-1 ml-[52px] m-0 leading-tight">Record observations and generate formal documentation.</p>
        </div>

        <!-- TAB NAVIGATION (Segmented Control) -->
        <div class="bg-slate-200/60 p-1.5 rounded-[14px] flex w-full sm:w-auto shadow-inner">
            <button @click="activeTab = 'create'" 
                    :class="activeTab === 'create' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-900/5' : 'text-slate-500 hover:text-slate-700'" 
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-[10px] font-extrabold text-[13px] sm:text-sm transition-all flex items-center justify-center">
                <i class="fa fa-pen mr-2"></i> New Log
            </button>
            <button @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-900/5' : 'text-slate-500 hover:text-slate-700'" 
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-[10px] font-extrabold text-[13px] sm:text-sm transition-all flex items-center justify-center">
                <i class="fa fa-clock-rotate-left mr-2"></i> Recent History
            </button>
        </div>
    </div>

    <!-- TAB 1: CREATE NEW LOG FORM (Expanded to max-w-6xl for PC split layout) -->
    <div x-show="activeTab === 'create'" x-transition.opacity.duration.300ms class="max-w-6xl mx-auto">
        <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md border border-slate-300 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-slate-200 bg-white">
                <h6 class="text-base font-extrabold text-slate-800 m-0 flex items-center">
                    <i class="fa fa-plus-circle text-indigo-600 mr-2"></i> Create Observation Entry
                </h6>
            </div>
            
            <div class="p-4 sm:p-6 sm:px-8">
                <form action="{{ route('teacher.daily-logs.store') }}" method="POST" enctype="multipart/form-data" class="m-0">
                    @csrf
                    
                    <!-- GRID FOR DESKTOP SPLIT LAYOUT -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                        
                        <!-- LEFT COLUMN: Main Form Content -->
                        <div class="lg:col-span-7">
                            
                            <div class="mb-4">
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Student</label>
                                <select name="student_id" id="student_id" class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                    <option value="" disabled selected>Select a student...</option>
                                    @foreach($myStudents as $student)
                                        <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4">
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                                    <input type="date" name="log_date" class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Quarter</label>
                                    <select name="quarter" class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                        <option value="Q1">Q1</option>
                                        <option value="Q2">Q2</option>
                                        <option value="Q3">Q3</option>
                                        <option value="Q4">Q4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-5 sm:mb-6">
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                                    <i class="fa fa-camera text-slate-400 mr-1.5"></i> Attach Photo (Optional)
                                </label>
                                <input type="file" name="photo" class="block w-full text-[12px] sm:text-sm text-slate-500 file:mr-3 sm:file:mr-4 file:py-2 sm:file:py-2.5 file:px-3 sm:file:px-4 file:rounded-xl file:border-0 file:text-[12px] sm:file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-300 rounded-xl cursor-pointer shadow-sm" accept="image/*">
                            </div>

                            <hr class="border-slate-200 my-5 sm:my-6">

                            <div class="p-4 sm:p-5 mb-5 bg-indigo-50/50 border border-dashed border-indigo-300 rounded-xl relative overflow-hidden">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-[11px] sm:text-xs font-extrabold text-indigo-600 uppercase tracking-wider m-0 flex items-center">
                                        <i class="fa fa-bolt mr-1.5"></i> 1. Quick Notes (Informal)
                                    </label>
                                </div>
                                <textarea name="raw_notes" id="rawNotes" class="w-full rounded-xl border-indigo-300 px-3.5 py-3 text-[13px] sm:text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm mb-3 custom-scrollbar" rows="4" required placeholder="e.g., he ate snacks, played with blocks, got distracted..."></textarea>
                                
                                <button type="button" id="generateAiBtn" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                    <i class="fa fa-magic mr-1.5"></i> Rewrite Professionally
                                </button>

                                <div id="aiLoading" class="text-center text-indigo-600 text-xs mt-3 font-bold hidden">
                                    <i class="fa fa-spinner fa-spin mr-1"></i> AI is thinking...
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                                    <i class="fa fa-file-alt text-emerald-500 mr-1.5"></i> 2. Formal Documentation
                                </label>
                                <textarea name="notes" id="formalNotes" class="w-full rounded-xl border-slate-300 px-3.5 py-3 text-[13px] sm:text-sm font-medium text-slate-700 bg-slate-50 focus:border-emerald-500 focus:ring-emerald-500 shadow-inner custom-scrollbar" rows="6" required placeholder="The AI-standardized report will appear here..."></textarea>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Home Recommendations -->
                        <div class="lg:col-span-5 flex flex-col h-full">
                            <div class="p-4 sm:p-5 bg-emerald-50/50 border border-dashed border-emerald-300 rounded-xl relative overflow-hidden flex-1 flex flex-col min-h-[300px]">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="text-[11px] sm:text-xs font-extrabold text-emerald-600 uppercase tracking-wider m-0 flex items-center">
                                        <i class="fa fa-home mr-1.5"></i> 3. Home Recommendations
                                    </label>
                                </div>
                                <!-- Uses flex-1 to automatically stretch and fill the height of the right column on PC -->
                                <textarea name="home_recommendations" id="home_recommendations" class="flex-1 w-full rounded-xl border-emerald-300 px-3.5 py-3 text-[13px] sm:text-sm font-medium text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm mb-4 custom-scrollbar resize-none" placeholder="AI will generate home activities here..."></textarea>
                                
                                <button type="button" id="btnGenerateRecommendations" class="mt-auto w-full inline-flex items-center justify-center px-4 py-2.5 sm:py-3 bg-emerald-500 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                    <i class="fa fa-lightbulb mr-1.5"></i> Generate Home Activities
                                </button>
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON FULL WIDTH ACROSS BOTH COLUMNS -->
                        <div class="lg:col-span-12 mt-2 sm:mt-4 border-t border-slate-100 pt-6 sm:pt-8">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3.5 sm:py-4 bg-slate-800 text-white text-[13px] sm:text-base font-extrabold rounded-xl hover:bg-slate-900 transition-colors shadow-sm border border-slate-700">
                                <i class="fa fa-save mr-2"></i> Save Daily Log
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB 2: RECENT LOGS HISTORY (Kept at max-w-4xl for readability of feed) -->
    <div x-show="activeTab === 'history'" x-transition.opacity.duration.300ms x-cloak class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 sm:mb-5 gap-3 sm:gap-4 px-1">
            <h6 class="text-[12px] sm:text-sm font-extrabold text-slate-600 m-0">Previously Submitted Logs</h6>
            
            <div class="relative w-full sm:w-72 shadow-sm rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="logSearch" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] sm:text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 bg-transparent" placeholder="Search by student or content...">
            </div>
        </div>

        <div id="logs-feed-container" class="space-y-4 sm:space-y-6">
            @forelse($recentLogs as $log)
                <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md border border-slate-200 sm:border-slate-300 overflow-hidden hover:shadow-lg transition-shadow duration-200 log-item">
                    
                    <!-- Card Header -->
                    <div class="p-4 sm:px-6 sm:py-5 border-b border-slate-100 sm:border-slate-200 bg-slate-50/40 sm:bg-slate-50/50 relative">
                        <div class="flex items-start gap-3 sm:gap-4 pr-10 sm:pr-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-white font-extrabold text-sm sm:text-base shadow-sm bg-gradient-to-br from-indigo-500 to-purple-500 shrink-0 border border-indigo-200">
                                {{ strtoupper(substr($log->student->first_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 pt-0.5">
                                <h6 class="text-[14px] sm:text-[16px] font-extrabold text-slate-800 m-0 leading-tight mb-1.5">{{ $log->student->first_name }} {{ $log->student->last_name }}</h6>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-[11px] sm:text-xs font-bold text-slate-500 m-0 flex items-center">
                                        <i class="fa fa-calendar-alt mr-1.5 text-slate-400"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('M j, Y') }}
                                    </p>
                                    <span class="hidden sm:inline-block text-slate-300 text-[10px]">•</span>
                                    <span class="px-2 py-0.5 rounded md:rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 text-[9px] sm:text-[10px] font-extrabold uppercase shadow-sm leading-none tracking-wider">
                                        {{ $log->quarter }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('teacher.daily-logs.destroy', $log->id) }}" method="POST" class="absolute top-3 sm:top-5 right-3 sm:right-5 m-0" onsubmit="return confirm('Are you sure you want to permanently delete this daily log?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white sm:bg-transparent border border-slate-200 sm:border-none text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shadow-sm sm:shadow-none" title="Delete Log">
                                <i class="fa fa-trash-alt text-[12px] sm:text-sm"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 sm:p-6 text-[13.5px] sm:text-[15px] text-slate-700 leading-relaxed bg-white">
                        @if($log->image_path)
                            <div class="mb-4 sm:mb-5 text-center bg-slate-50 rounded-xl p-2 sm:p-3 border border-dashed border-slate-300">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="max-h-48 sm:max-h-64 w-auto mx-auto rounded-lg shadow-sm border border-slate-200 object-contain">
                            </div>
                        @endif

                        <div class="whitespace-pre-line font-medium text-slate-800">
                            {{ $log->notes }}
                        </div>
                        
                        @if($log->home_recommendations)
                            <div class="mt-5 sm:mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 border-l-4 border-l-emerald-500">
                                <span class="font-extrabold text-emerald-700 text-[10px] sm:text-[11px] uppercase tracking-wider flex items-center mb-2">
                                    <i class="fa fa-check-circle mr-1.5 text-emerald-500"></i> Home Recommendations
                                </span>
                                <p class="mb-0 text-emerald-900 font-medium whitespace-pre-line text-[13px] sm:text-[14px] leading-relaxed">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Comments Section -->
                    <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-50/80 border-t border-slate-100 sm:border-slate-200">
                        <div class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2.5 flex items-center">
                            <i class="fa fa-comments mr-1.5 text-indigo-400"></i> Comments & Feedback
                        </div>
                        <div class="bg-white p-0 sm:p-4 rounded-xl sm:border border-slate-300 sm:shadow-sm">
                            @include('components.comment-thread', ['item' => $log])
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-16 sm:py-20 px-6 bg-white rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md border border-slate-200 sm:border-slate-300">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-clock-rotate-left text-2xl sm:text-3xl"></i>
                    </div>
                    <h6 class="text-[16px] sm:text-lg font-extrabold text-slate-800 m-0 mb-1">No History Found</h6>
                    <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0">You haven't submitted any daily logs yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
// --- FEED SEARCH FILTER LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('logSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            document.querySelectorAll('.log-item').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
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

    // Update UI to show loading
    btn.disabled = true;
    loading.classList.remove('hidden'); 
    output.value = "Translating notes..."; 

    // Send request to Controller
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
        }
    })
    .catch(error => {
        alert('Something went wrong connecting to the AI.');
        output.value = "";
    })
    .finally(() => {
        // Reset UI
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
        alert('Please select a student from the dropdown first!');
        return;
    }

    if (!rawNotes.trim()) {
        alert('Please write some informal notes first so the AI knows what to recommend!');
        return;
    }

    // Change button state to loading
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
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating recommendations.');
    })
    .finally(() => {
        // Restore button
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection