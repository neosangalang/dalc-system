@extends('layouts.app')

@section('title', 'Daily Logging')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body {
        background-color: #F8FAFC;
        font-family: 'Inter', sans-serif;
        color: #1E293B;
    }

    /* Clean Form Cards */
    .form-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .form-header {
        padding: 20px 24px;
        border-bottom: 1px solid #E2E8F0;
        background: #FFFFFF;
    }
    
    /* AI Assistant Blocks */
    .ai-block {
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .ai-block-primary { border-color: #818CF8; background: #EEF2FF; }
    .ai-block-success { border-color: #34D399; background: #ECFDF5; }

    /* Feed Timeline Elements */
    .log-feed-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        margin-bottom: 20px;
        transition: box-shadow 0.2s;
    }
    .log-feed-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }
    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
    }
    .log-body {
        padding: 20px;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #334155;
    }
    .log-footer {
        background: #F8FAFC;
        padding: 12px 20px;
        border-top: 1px solid #E2E8F0;
        border-radius: 0 0 16px 16px;
    }

    /* Form Inputs */
    .clean-input {
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        padding: 10px 14px;
        font-size: 14px;
    }
    .clean-input:focus {
        border-color: #4F6AF5;
        box-shadow: 0 0 0 3px rgba(79, 106, 245, 0.1);
    }
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        margin-bottom: 6px;
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fa fa-exclamation-triangle me-2"></i> <strong>Oops! Something went wrong:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-dark"><i class="fa fa-pencil-alt me-2" style="color:#4F6AF5"></i>Daily Logging</h4>
        <p class="text-muted small m-0 mt-1">Record observations and generate formal documentation.</p>
    </div>
</div>

<div class="row g-4 align-items-start">
    
    <div class="col-xl-5 col-lg-6">
        <div class="form-card" style="position: sticky; top: 20px;">
            <div class="form-header">
                <h6 class="fw-bold m-0"><i class="fa fa-plus-circle text-primary me-2"></i>New Daily Log</h6>
            </div>
            
            <div class="p-4">
                <form action="{{ route('teacher.daily-logs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" id="student_id" class="form-select clean-input" required>
                            <option value="" disabled selected>Select a student...</option>
                            @foreach($myStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="log_date" class="form-control clean-input" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Quarter</label>
                            <select name="quarter" class="form-select clean-input" required>
                                <option value="Q1">Q1</option>
                                <option value="Q2">Q2</option>
                                <option value="Q3">Q3</option>
                                <option value="Q4">Q4</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><i class="fa fa-camera text-muted me-1"></i> Attach Photo (Optional)</label>
                        <input type="file" name="photo" class="form-control clean-input" accept="image/*" style="font-size: 13px;">
                    </div>

                    <hr class="mb-4" style="border-color: #E2E8F0;">

                    <div class="ai-block ai-block-primary">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label text-primary m-0"><i class="fa fa-bolt me-1"></i> 1. Quick Notes (Informal)</label>
                        </div>
                        <textarea name="raw_notes" id="rawNotes" class="form-control clean-input mb-2 border-primary" rows="3" required placeholder="e.g., he ate snacks, played with blocks, got distracted..."></textarea>
                        
                        <button type="button" id="generateAiBtn" class="btn btn-sm w-100 fw-bold shadow-sm" style="background: #4F6AF5; color: white;">
                            <i class="fa fa-magic me-1"></i> Rewrite Professionally with AI
                        </button>

                        <div id="aiLoading" class="text-center text-primary small mt-2 fw-bold d-none">
                            <i class="fa fa-spinner fa-spin me-1"></i> AI is thinking...
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-dark"><i class="fa fa-file-alt text-success me-1"></i> 2. Formal Documentation</label>
                        <textarea name="notes" id="formalNotes" class="form-control clean-input bg-light" rows="4" required placeholder="The AI-standardized report will appear here..."></textarea>
                    </div>

                    <div class="ai-block ai-block-success mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label text-success m-0"><i class="fa fa-home me-1"></i> 3. Home Recommendations (For Parents)</label>
                        </div>
                        <textarea name="home_recommendations" id="home_recommendations" class="form-control clean-input mb-2 border-success" rows="3" placeholder="AI will generate home activities here..."></textarea>
                        
                        <button type="button" id="btnGenerateRecommendations" class="btn btn-sm w-100 fw-bold text-white shadow-sm" style="background-color: #34D399;">
                            <i class="fa fa-lightbulb me-1"></i> Generate Home Activities with AI
                        </button>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="border-radius: 10px; font-size: 15px;">
                        <i class="fa fa-save me-2"></i> Save Daily Log
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-7 col-lg-6">
        <div class="d-flex justify-content-between align-items-end mb-3 px-1">
            <h6 class="fw-bold m-0 text-muted text-uppercase" style="letter-spacing: 0.5px; font-size: 12px;">Recent Daily Logs</h6>
            <div class="input-group shadow-sm" style="width: 220px; border-radius: 8px; overflow: hidden; border: 1px solid #CBD5E1;">
                <span class="input-group-text bg-white border-0 text-muted"><i class="fa fa-search"></i></span>
                <input type="text" id="logSearch" class="form-control form-control-sm border-0 ps-0" style="box-shadow: none;" placeholder="Search logs...">
            </div>
        </div>

        <div id="logs-feed-container">
            @forelse($recentLogs as $log)
                <div class="log-feed-card log-item">
                    
                    <div class="log-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 42px; height: 42px; background: linear-gradient(135deg, #4F6AF5, #8B5CF6); font-size: 15px;">
                                {{ strtoupper(substr($log->student->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="m-0 fw-bold text-dark">{{ $log->student->first_name }} {{ $log->student->last_name }}</h6>
                                <small class="text-muted"><i class="fa fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('M j, Y') }}</small>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-primary border px-2 py-1 fw-bold">{{ $log->quarter }}</span>
                            
                            <form action="{{ route('teacher.daily-logs.destroy', $log->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to permanently delete this daily log?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0 ms-2 opacity-75" title="Delete Log" onmouseover="this.classList.remove('opacity-75')" onmouseout="this.classList.add('opacity-75')">
                                    <i class="fa fa-trash-alt fs-6"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="log-body">
                        @if($log->image_path)
                            <div class="mb-3 text-center bg-light rounded p-2" style="border: 1px dashed #cbd5e1;">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="img-fluid rounded shadow-sm" style="max-height: 200px; width: auto;">
                            </div>
                        @endif

                        <div style="font-size: 14px;">
                            {{ $log->notes }}
                        </div>
                        
                        @if($log->home_recommendations)
                            <div class="mt-3 p-3 rounded" style="background: #F0FDF4; border-left: 3px solid #10B981;">
                                <span class="fw-bold text-success" style="font-size: 12px;"><i class="fa fa-check-circle me-1"></i> Home Activities Attached</span>
                                <p class="mb-0 mt-1 small text-dark">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="log-footer">
                        <div class="small fw-bold text-muted mb-2"><i class="fa fa-comments me-1"></i> Comments</div>
                        <div class="comment-wrapper bg-white p-3 rounded border shadow-sm">
                            @include('components.comment-thread', ['item' => $log])
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border">
                    <div class="mx-auto mb-3" style="width: 60px; height: 60px; background: #EEF2FF; color: #4F6AF5; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fa fa-clipboard"></i>
                    </div>
                    <h6 class="fw-bold text-dark">No Logs Yet</h6>
                    <p class="small">Record your first log using the form on the left.</p>
                </div>
            @endforelse
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
    loading.classList.remove('d-none');
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
        loading.classList.add('d-none');
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
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Generating Activities...';
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