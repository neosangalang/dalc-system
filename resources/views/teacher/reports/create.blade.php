@extends('layouts.app')

@section('title', 'Generate AI Report')

@section('content')

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block">

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 sm:mb-8 gap-4 md:gap-0">
        <div>
            <h4 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm shrink-0">
                    <i class="fa fa-file-signature"></i>
                </div>
                AI-Assisted Report
            </h4>
            <p class="text-slate-500 text-[13px] sm:text-sm font-medium mt-1 ml-[52px] m-0 leading-tight">Select a student and timeframe to automatically draft a formal progress summary.</p>
        </div>
        <a href="{{ route('teacher.reports.index') }}" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2.5 sm:py-2 bg-white border border-slate-300 text-slate-600 text-[13px] sm:text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm decoration-transparent mt-2 md:mt-0">
            <i class="fa fa-arrow-left mr-2"></i> Back to Reports
        </a>
    </div>

    <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md border border-slate-300 overflow-hidden">
        <form action="{{ route('teacher.reports.store') }}" method="POST" id="report-generation-form" class="m-0">
            @csrf
            
            <div class="p-4 sm:p-6 md:p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            1. Choose Student
                        </label>
                        <select name="student_id" id="student_id" class="w-full rounded-xl border-slate-300 px-3.5 sm:px-4 py-3 sm:py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" required>
                            <option value="" disabled selected>Select a student...</option>
                            @foreach($myStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            2. Choose Type of Report
                        </label>
                        <select name="report_type" id="report_type" class="w-full rounded-xl border-slate-300 px-3.5 sm:px-4 py-3 sm:py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" required>
                            <option value="" disabled selected>Select quarter report type...</option>
                            <option value="q1">1st Quarter (Q1) Report</option>
                            <option value="q2">2nd Quarter (Q2) Report</option>
                            <option value="q3">3rd Quarter (Q3) Report</option>
                            <option value="q4">4th Quarter (Q4) Report</option>
                        </select>
                    </div>
                </div>

                <div class="p-5 sm:p-6 mb-6 bg-slate-50 border border-dashed border-indigo-300 rounded-xl relative overflow-hidden">
                    <h6 class="text-[13px] sm:text-sm font-extrabold text-indigo-600 mb-1 flex items-center m-0">
                        <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Generate Draft via AI
                    </h6>
                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mb-4 m-0">The AI will compile data based on your selections and draft a professional report.</p>
                    
                    <button type="button" id="trigger-ai-btn" class="w-full inline-flex justify-center items-center px-4 py-3.5 sm:py-3 bg-indigo-600 text-white text-[13px] sm:text-sm font-extrabold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <i class="fa-solid fa-robot mr-2"></i> <span>Generate Report</span>
                    </button>
                </div>

                <div id="review-section" class="hidden">
                    <hr class="border-slate-200 my-6 sm:my-8">
                    
                    <h6 class="text-[15px] sm:text-base font-extrabold text-slate-800 mb-4 flex items-center m-0">
                        <i class="fa fa-edit text-emerald-500 mr-2"></i> Review and Edit Report
                    </h6>
                    
                    <div class="mb-6">
                        <!-- Increased rows slightly to make reviewing easier on mobile -->
                        <textarea name="content" id="ai-generated-content" class="w-full rounded-xl border-slate-300 px-4 py-4 text-[13px] sm:text-sm font-medium text-slate-700 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner custom-scrollbar" rows="16" placeholder="AI generated content will appear here..."></textarea>
                    </div>
                    
                    <!-- Stack buttons on mobile, align right on PC -->
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 w-full">
                        <button type="submit" name="action" value="draft" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-3.5 sm:py-2.5 bg-white border border-slate-300 text-slate-700 text-[13px] sm:text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                            Save as Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 sm:py-2.5 bg-emerald-500 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                            <i class="fa fa-paper-plane mr-2"></i> Submit for Approval
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    // Logic for Flowchart Decision: "Daily Report?"
    const reportTypeSelect = document.getElementById('report_type');
    const dateRow = document.getElementById('date-selection-row');
    const dateInput = document.getElementById('report_date');

    reportTypeSelect.addEventListener('change', function() {
        if (this.value === 'daily') {
            dateRow.classList.remove('hidden');
            dateInput.required = true;
        } else {
            dateRow.classList.add('hidden');
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
        let btnText = btn.querySelector('span');
        let icon = btn.querySelector('i');
        
        // UI Loading State
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        icon.className = 'fa-solid fa-spinner fa-spin mr-2';
        btnText.innerText = 'Analyzing Data & Drafting Report...';

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
                document.getElementById('review-section').classList.remove('hidden');
                document.getElementById('ai-generated-content').value = data.report_content;
                
                // Optional: Scroll down smoothly to the editor on mobile
                document.getElementById('review-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // UI Success State
                icon.className = 'fa-solid fa-check mr-2';
                btnText.innerText = 'Report Generated Successfully';
                btn.classList.replace('bg-indigo-600', 'bg-emerald-500');
                btn.classList.replace('hover:bg-indigo-700', 'hover:bg-emerald-600');
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            } else {
                alert("AI Error: " + data.message);
                
                // UI Reset State
                icon.className = 'fa-solid fa-robot mr-2';
                btnText.innerText = 'Generate Report';
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        })
        .catch(error => {
            alert("Something went wrong communicating with the server.");
            
            // UI Reset State
            icon.className = 'fa-solid fa-robot mr-2';
            btnText.innerText = 'Generate Report';
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
        });
    });
</script>
@endpush

@endsection