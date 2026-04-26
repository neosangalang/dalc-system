@extends('layouts.app')

@section('title', 'Generate AI Report')

@section('content')
<div class="data-card">
    <div class="data-card-header">
        <h5><i class="fa fa-file-signature me-2" style="color:var(--primary)"></i> AI-Assisted Report Generation</h5>
    </div>
    
    <div class="card-body p-4">
        <form action="{{ route('teacher.reports.store') }}" method="POST" id="report-generation-form">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">1. Choose Student</label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="" disabled selected>Select a student...</option>
                        @foreach($myStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">2. Choose Type of Report</label>
                    <select name="report_type" id="report_type" class="form-control" required>
                        <option value="" disabled selected>Select report type...</option>
                        <option value="daily">Daily Log Summary</option>
                        
                        <option value="q1">1st Quarter (Q1) Report</option>
                        <option value="q2">2nd Quarter (Q2) Report</option>
                        <option value="q3">3rd Quarter (Q3) Report</option>
                        <option value="q4">4th Quarter (Q4) Report</option>
                        
                        <option value="mid_year">Mid-Year Summary</option>
                        <option value="year_end">Year-End Summary</option>
                    </select>
                </div>
            </div>

            <div class="row d-none" id="date-selection-row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-primary">Select Date for Daily Report</label>
                    <input type="date" name="report_date" id="report_date" class="form-control">
                </div>
            </div>

            <div class="p-4 mb-4 mt-3" style="background-color: #f8f9fa; border: 1px dashed #4F6AF5; border-radius: 8px;">
                <h6 class="fw-bold text-primary mb-2">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generate Draft via AI
                </h6>
                <p class="small text-muted mb-3">The AI will compile data based on your selections and draft a professional report.</p>
                
                <button type="button" id="trigger-ai-btn" class="btn btn-primary fw-bold w-100 py-2">
                    <i class="fa-solid fa-robot me-2"></i> Generate Report
                </button>
            </div>

            <div id="review-section" class="d-none">
                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="fa fa-edit me-2 text-success"></i>Review and Edit Report</h6>
                <div class="mb-4">
                    <textarea name="content" id="ai-generated-content" class="form-control" rows="10" placeholder="AI generated content will appear here..."></textarea>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">Save as Draft</button>
                    <button type="submit" name="action" value="submit" class="btn btn-success fw-bold">
                        <i class="fa fa-paper-plane me-2"></i> Submit for Approval
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Logic for Flowchart Decision: "Daily Report?"
    const reportTypeSelect = document.getElementById('report_type');
    const dateRow = document.getElementById('date-selection-row');
    const dateInput = document.getElementById('report_date');

    reportTypeSelect.addEventListener('change', function() {
        if (this.value === 'daily') {
            dateRow.classList.remove('d-none');
            dateInput.required = true;
        } else {
            dateRow.classList.add('d-none');
            dateInput.required = false;
            dateInput.value = ''; 
        }
    });

    // THE REAL AI FETCH CALL
    document.getElementById('trigger-ai-btn').addEventListener('click', function() {
        let studentId = document.getElementById('student_id').value;
        let type = document.getElementById('report_type').value;
        let date = document.getElementById('report_date').value;

        if(!studentId || !type) {
            alert('Please select a student and report type first.');
            return;
        }

        let btn = this;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Analyzing Data & Drafting Report...';
        btn.disabled = true;

        fetch("{{ route('teacher.reports.generate-ai') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                student_id: studentId,
                report_type: type,
                report_date: date
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Show the review section and inject the AI's text!
                document.getElementById('review-section').classList.remove('d-none');
                document.getElementById('ai-generated-content').value = data.report_content;
                
                btn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Report Generated Successfully';
                btn.classList.replace('btn-primary', 'btn-success');
            } else {
                alert("AI Error: " + data.message);
                btn.innerHTML = '<i class="fa-solid fa-robot me-2"></i> Generate Report';
                btn.disabled = false;
            }
        })
        .catch(error => {
            alert("Something went wrong communicating with the server.");
            btn.innerHTML = '<i class="fa-solid fa-robot me-2"></i> Generate Report';
            btn.disabled = false;
        });
    });
</script>
@endsection