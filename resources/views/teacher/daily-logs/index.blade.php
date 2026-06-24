@extends('layouts.app')

@section('title', 'Daily Logging')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Oops! Something went wrong:
            </div>
            <ul class="list-disc pl-5 text-sm font-medium m-0 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8">
        <h4 class="text-2xl font-extrabold text-slate-800 m-0 flex items-center">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                <i class="fa fa-pencil-alt"></i>
            </div>
            Daily Logging
        </h4>
        <p class="text-slate-500 text-sm font-medium mt-1 ml-[52px] m-0">Record observations and generate formal documentation.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-white">
                    <h6 class="text-base font-extrabold text-slate-800 m-0 flex items-center">
                        <i class="fa fa-plus-circle text-indigo-600 mr-2"></i> New Daily Log
                    </h6>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('teacher.daily-logs.store') }}" method="POST" enctype="multipart/form-data" class="m-0">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Student</label>
                            <select name="student_id" id="student_id" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                <option value="" disabled selected>Select a student...</option>
                                @foreach($myStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                                <input type="date" name="log_date" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Quarter</label>
                                <select name="quarter" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                    <option value="Q1">Q1</option>
                                    <option value="Q2">Q2</option>
                                    <option value="Q3">Q3</option>
                                    <option value="Q4">Q4</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                                <i class="fa fa-camera text-slate-400 mr-1.5"></i> Attach Photo (Optional)
                            </label>
                            <input type="file" name="photo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl cursor-pointer" accept="image/*">
                        </div>

                        <hr class="border-slate-100 my-6">

                        <div class="p-5 mb-5 bg-indigo-50/50 border border-dashed border-indigo-300 rounded-xl relative overflow-hidden">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider m-0 flex items-center">
                                    <i class="fa fa-bolt mr-1.5"></i> 1. Quick Notes (Informal)
                                </label>
                            </div>
                            <textarea name="raw_notes" id="rawNotes" class="w-full rounded-xl border-indigo-200 px-4 py-3 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm mb-3" rows="3" required placeholder="e.g., he ate snacks, played with blocks, got distracted..."></textarea>
                            
                            <button type="button" id="generateAiBtn" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                                <i class="fa fa-magic mr-1.5"></i> Rewrite Professionally with AI
                            </button>

                            <div id="aiLoading" class="text-center text-indigo-600 text-xs mt-3 font-bold hidden">
                                <i class="fa fa-spinner fa-spin mr-1"></i> AI is thinking...
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fa fa-file-alt text-emerald-500 mr-1.5"></i> 2. Formal Documentation
                            </label>
                            <textarea name="notes" id="formalNotes" class="w-full rounded-xl border-slate-200 px-4 py-3 text-sm font-medium text-slate-700 bg-slate-50 focus:border-emerald-500 focus:ring-emerald-500 shadow-inner" rows="4" required placeholder="The AI-standardized report will appear here..."></textarea>
                        </div>

                        <div class="p-5 mb-6 bg-emerald-50/50 border border-dashed border-emerald-300 rounded-xl relative overflow-hidden">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-extrabold text-emerald-600 uppercase tracking-wider m-0 flex items-center">
                                    <i class="fa fa-home mr-1.5"></i> 3. Home Recommendations
                                </label>
                            </div>
                            <textarea name="home_recommendations" id="home_recommendations" class="w-full rounded-xl border-emerald-200 px-4 py-3 text-sm font-medium text-slate-700 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm mb-3" rows="3" placeholder="AI will generate home activities here..."></textarea>
                            
                            <button type="button" id="btnGenerateRecommendations" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                <i class="fa fa-lightbulb mr-1.5"></i> Generate Home Activities with AI
                            </button>
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-slate-800 text-white text-sm font-extrabold rounded-xl hover:bg-slate-900 transition-colors shadow-sm">
                            <i class="fa fa-save mr-2"></i> Save Daily Log
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            
            <div class="flex flex-wrap justify-between items-end mb-5 gap-3 px-1">
                <h6 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider m-0">Recent Daily Logs</h6>
                
                <div class="relative w-full sm:w-56 shadow-sm rounded-xl overflow-hidden border border-slate-200 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" id="logSearch" class="w-full border-0 py-2 pl-9 pr-3 text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 bg-transparent" placeholder="Search logs...">
                </div>
            </div>

            <div id="logs-feed-container" class="space-y-5">
                @forelse($recentLogs as $log)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-200 log-item">
                        
                        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-start bg-white">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-extrabold text-base shadow-sm bg-gradient-to-br from-indigo-500 to-purple-500 shrink-0">
                                    {{ strtoupper(substr($log->student->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="text-sm font-extrabold text-slate-800 m-0">{{ $log->student->first_name }} {{ $log->student->last_name }}</h6>
                                    <p class="text-xs font-medium text-slate-500 m-0 mt-0.5"><i class="fa fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('M j, Y') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-md border border-slate-200 bg-slate-50 text-indigo-600 text-[10px] font-extrabold uppercase">{{ $log->quarter }}</span>
                                
                                <form action="{{ route('teacher.daily-logs.destroy', $log->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to permanently delete this daily log?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Delete Log">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="p-5 text-sm text-slate-700 leading-relaxed">
                            @if($log->image_path)
                                <div class="mb-4 text-center bg-slate-50 rounded-xl p-3 border border-dashed border-slate-300">
                                    <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="max-h-48 w-auto mx-auto rounded-lg shadow-sm border border-slate-200">
                                </div>
                            @endif

                            <div class="whitespace-pre-line">
                                {{ $log->notes }}
                            </div>
                            
                            @if($log->home_recommendations)
                                <div class="mt-5 p-4 rounded-xl bg-emerald-50 border-l-4 border-emerald-500">
                                    <span class="font-extrabold text-emerald-700 text-xs uppercase tracking-wider flex items-center mb-1.5">
                                        <i class="fa fa-check-circle mr-1.5"></i> Home Activities Attached
                                    </span>
                                    <p class="mb-0 text-emerald-900 font-medium whitespace-pre-line">{{ $log->home_recommendations }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                            <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fa fa-comments mr-1.5"></i> Comments
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                                @include('components.comment-thread', ['item' => $log])
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="text-center py-16 px-6 bg-white rounded-2xl shadow-sm border border-slate-200">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                            <i class="fa fa-clipboard text-2xl"></i>
                        </div>
                        <h6 class="text-base font-extrabold text-slate-800 m-0 mb-1">No Logs Yet</h6>
                        <p class="text-sm font-medium text-slate-500 m-0">Record your first log using the form on the left.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

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
    loading.classList.remove('hidden'); // Tailwind override
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
            // Drop the polished text into the formal box
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
        loading.classList.add('hidden'); // Tailwind override
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