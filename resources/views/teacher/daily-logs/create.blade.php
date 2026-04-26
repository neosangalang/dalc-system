@extends('layouts.app')

@section('title', 'New Daily Log')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Create Daily Log</h4>
            <p class="text-muted mb-0">Record student progress and behavior.</p>
        </div>
        <a href="{{ route('teacher.daily-logs.index') }}" class="btn btn-light border fw-bold shadow-sm">
            <i class="fa fa-arrow-left me-2"></i> Back to Logs
        </a>
    </div>

    <div class="data-card p-4 p-md-5 border-top border-4 border-primary">
        <form action="{{ route('teacher.daily-logs.store') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label fw-bold" style="font-size: 13px;">Select Student <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select py-2" required>
                        <option value="" disabled selected>Choose a student...</option>
                        <option value="1">Maria Santos</option>
                        <option value="2">Juan Dela Cruz</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Date <span class="text-danger">*</span></label>
                    <input type="date" name="log_date" class="form-control py-2" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr style="border-color: var(--border);" class="my-4">

            <h5 class="fw-bold mb-3"><i class="fa fa-robot text-primary me-2"></i> AI Assistant</h5>
            <div class="row mb-4 bg-light p-4 rounded" style="border: 1px dashed #ced4da;">
                
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label fw-bold text-dark" style="font-size: 13px;">Quick Notes (Informal)</label>
                    <textarea id="rawNotes" name="raw_notes" class="form-control" rows="6" placeholder="e.g., Maria refused writing today and became upset during math activity..."></textarea>
                    
                    <button type="button" id="generateAiBtn" class="btn btn-sm text-white mt-3 fw-bold px-3 py-2 shadow-sm" style="background: linear-gradient(135deg, #4F6AF5, #9B6DFF); border: none; border-radius: 8px;">
                        <i class="fa fa-magic me-2"></i> Standardize with AI
                    </button>
                    <span id="aiLoading" class="text-muted small ms-2 d-none">
                        <i class="fa fa-spinner fa-spin text-primary me-1"></i> AI is writing...
                    </span>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-success" style="font-size: 13px;"><i class="fa fa-check-circle me-1"></i> Formal SPED Documentation</label>
                    <textarea id="formalNotes" name="notes" class="form-control" rows="6" placeholder="The AI-standardized report will appear here..." required></textarea>
                    <small class="text-muted mt-2 d-block" style="font-size: 12px;"><i class="fa fa-info-circle"></i> You can edit the generated text before saving.</small>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" style="border-radius: 8px;">Save Daily Log</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('generateAiBtn').addEventListener('click', function() {
    let rawNotes = document.getElementById('rawNotes').value;
    let btn = this;
    let loading = document.getElementById('aiLoading');
    let output = document.getElementById('formalNotes');

    if (!rawNotes.trim()) {
        alert('Please type some notes first!');
        return;
    }

    btn.disabled = true;
    loading.classList.remove('d-none');
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
        }
    })
    .catch(error => {
        alert('Something went wrong connecting to the AI.');
        output.value = "";
    })
    .finally(() => {
        btn.disabled = false;
        loading.classList.add('d-none');
    });
});
</script>
@endpush
@endsection