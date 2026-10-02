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

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md border border-slate-300 overflow-hidden">
        <form action="{{ route('teacher.reports.store') }}" method="POST" id="report-generation-form" class="m-0">
            @csrf
            
            <!-- Hidden field for final compiled content -->
            <input type="hidden" name="content" id="final_content_input" value="">

            <div class="p-4 sm:p-6 md:p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            1. Choose Student
                        </label>
                        <select name="student_id" id="student_id" class="w-full rounded-xl border-slate-300 px-3.5 sm:px-4 py-3 sm:py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer" required>
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
                        <select name="report_type" id="report_type" class="w-full rounded-xl border-slate-300 px-3.5 sm:px-4 py-3 sm:py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer" required>
                            <option value="" disabled selected>Select report type...</option>
                            <option value="daily">Daily Report</option>
                            <option value="q1">1st Quarter (Q1) Report</option>
                            <option value="q2">2nd Quarter (Q2) Report</option>
                            <option value="q3">3rd Quarter (Q3) Report</option>
                            <option value="q4">4th Quarter (Q4) Report</option>
                            <option value="mid_year">Mid-Year Report</option>
                            <option value="year_end">Year-End Report</option>
                        </select>
                    </div>
                </div>

                <!-- DATE SELECTION (Only visible for Daily Report) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6 hidden" id="date-selection-row">
                    <div>
                        <label class="block text-[11px] font-extrabold text-indigo-600 uppercase tracking-wider mb-1.5 flex items-center">
                            Select Date for Daily Report <span class="text-rose-500 ml-1">*</span>
                        </label>
                        <input type="date" name="report_date" id="report_date" class="w-full rounded-xl border-indigo-300 px-3.5 sm:px-4 py-3 sm:py-2.5 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-indigo-50/30">
                    </div>
                </div>

                <!-- ================= DAILY REPORT SECTIONS (Visible only for Daily Report) ================= -->
                <div id="daily-sections-wrapper" class="hidden space-y-6">
                    
                    <!-- 1. Daily Report Summary -->
                    <div class="p-5 sm:p-6 bg-slate-50 border border-dashed border-indigo-300 rounded-xl relative overflow-hidden">
                        <div class="flex justify-between items-center mb-2">
                            <h6 class="text-[13px] sm:text-sm font-extrabold text-indigo-600 m-0 flex items-center">
                                <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> 1. Daily Report Summary
                            </h6>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500 mb-4 m-0">Generate a comprehensive summary of classroom observations for today.</p>
                        
                        <button type="button" id="trigger-daily-ai-btn" class="w-full inline-flex justify-center items-center px-4 py-3.5 sm:py-3 bg-indigo-600 text-white text-[13px] sm:text-sm font-extrabold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                            <i class="fa-solid fa-robot mr-2"></i> <span>Generate Daily Report via AI</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-[12px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                            Daily Report Narrative
                        </label>
                        <textarea id="daily_content" class="w-full rounded-xl border-slate-300 px-4 py-4 text-[13px] sm:text-sm font-medium text-slate-700 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner custom-scrollbar" rows="8" placeholder="Daily Report content will appear here..."></textarea>
                    </div>

                    <!-- 2. Home-Based Recommendation (Optional) -->
                    <div class="p-5 sm:p-6 bg-amber-50/50 border border-dashed border-amber-300 rounded-xl relative overflow-hidden">
                        <div class="flex justify-between items-center mb-2">
                            <h6 class="text-[13px] sm:text-sm font-extrabold text-amber-700 m-0 flex items-center">
                                <i class="fa-solid fa-house-user mr-2"></i> 2. Home-Based Recommendation (Optional)
                            </h6>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600 bg-amber-100 px-2.5 py-1 rounded-full border border-amber-200">Optional</span>
                        </div>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-600 mb-4 m-0">Generate tailored home activities for parents to practice tonight with their child.</p>
                        
                        <button type="button" id="trigger-home-ai-btn" class="w-full inline-flex justify-center items-center px-4 py-3.5 sm:py-3 bg-amber-600 text-white text-[13px] sm:text-sm font-extrabold rounded-xl hover:bg-amber-700 transition-colors shadow-sm focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                            <i class="fa-solid fa-lightbulb mr-2"></i> <span>Generate Home-Based Recommendations via AI</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-[12px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                            Home-Based Recommendations (Optional)
                        </label>
                        <textarea name="home_recommendations" id="home_recommendations" class="w-full rounded-xl border-slate-300 px-4 py-4 text-[13px] sm:text-sm font-medium text-slate-700 bg-white focus:border-amber-500 focus:ring-amber-500 shadow-inner custom-scrollbar" rows="4" placeholder="Home-based recommendations will appear here (optional)..."></textarea>
                    </div>

                </div>

                <!-- ================= NON-DAILY REPORT SECTION (Visible for Quarterly, Mid-Year, Year-End) ================= -->
                <div id="general-section-wrapper" class="hidden space-y-6">
                    
                    <div class="p-5 sm:p-6 bg-slate-50 border border-dashed border-indigo-300 rounded-xl relative overflow-hidden">
                        <h6 class="text-[13px] sm:text-sm font-extrabold text-indigo-600 mb-1 flex items-center m-0">
                            <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Generate Report via AI
                        </h6>
                        <p class="text-[11px] sm:text-xs font-medium text-slate-500 mb-4 m-0">The AI will compile past logs and IEP goals to draft a formal summary report.</p>
                        
                        <button type="button" id="trigger-general-ai-btn" class="w-full inline-flex justify-center items-center px-4 py-3.5 sm:py-3 bg-indigo-600 text-white text-[13px] sm:text-sm font-extrabold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                            <i class="fa-solid fa-robot mr-2"></i> <span id="general-btn-text">Generate Report</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-[12px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                            Report Narrative & Review
                        </label>
                        <textarea id="general_content" class="w-full rounded-xl border-slate-300 px-4 py-4 text-[13px] sm:text-sm font-medium text-slate-700 bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-inner custom-scrollbar" rows="14" placeholder="Report content will appear here..."></textarea>
                    </div>

                </div>

                <!-- ================= ACTIONS / SUBMIT SECTION ================= -->
                <div id="review-action-section" class="hidden mt-8 pt-6 border-t border-slate-200">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 w-full">
                        <button type="submit" name="action" value="draft" id="btn-save-draft" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-3.5 sm:py-2.5 bg-white border border-slate-300 text-slate-700 text-[13px] sm:text-sm font-bold rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                            Save as Draft
                        </button>
                        
                        <!-- FOR DAILY REPORT: Published Directly to Guardian (No Admin Approval Needed) -->
                        <button type="submit" name="action" value="submit" id="btn-publish-report" class="hidden w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 sm:py-2.5 bg-emerald-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                            <i class="fa fa-paper-plane mr-2"></i> Publish Report
                        </button>

                        <!-- FOR MAJOR REPORTS: Sent to Admin for Approval -->
                        <button type="submit" name="action" value="submit" id="btn-submit-approval" class="hidden w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 sm:py-2.5 bg-indigo-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
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
document.addEventListener("DOMContentLoaded", function() {
    const studentSelect = document.getElementById('student_id');
    const reportTypeSelect = document.getElementById('report_type');
    const dateRow = document.getElementById('date-selection-row');
    const dateInput = document.getElementById('report_date');

    const dailyWrapper = document.getElementById('daily-sections-wrapper');
    const generalWrapper = document.getElementById('general-section-wrapper');
    const reviewActionSection = document.getElementById('review-action-section');

    const dailyContent = document.getElementById('daily_content');
    const homeRec = document.getElementById('home_recommendations');
    const generalContent = document.getElementById('general_content');
    const finalContentInput = document.getElementById('final_content_input');

    const triggerDailyBtn = document.getElementById('trigger-daily-ai-btn');
    const triggerHomeBtn = document.getElementById('trigger-home-ai-btn');
    const triggerGeneralBtn = document.getElementById('trigger-general-ai-btn');

    const btnPublish = document.getElementById('btn-publish-report');
    const btnApproval = document.getElementById('btn-submit-approval');

    const form = document.getElementById('report-generation-form');

    // Default date pre-selection to Today
    const todayStr = new Date().toISOString().split('T')[0];
    let lastSelectedDate = todayStr;
    dateInput.value = todayStr;

    // Cache structure: cache[cacheKey] = { dailyContent, homeRec, generalContent, dailyGen, homeGen, generalGen }
    const cache = {};

    function getCacheKey() {
        const studentId = studentSelect.value || 'none';
        const type = reportTypeSelect.value || 'none';
        if (type === 'daily') {
            const d = dateInput.value || 'nodate';
            return `${studentId}_daily_${d}`;
        }
        return `${studentId}_${type}_all`;
    }

    function saveCurrentToCache() {
        const key = getCacheKey();
        if (!cache[key]) cache[key] = {};
        
        cache[key].dailyContent = dailyContent.value;
        cache[key].homeRec = homeRec.value;
        cache[key].generalContent = generalContent.value;
    }

    function setBtnState(btn, stateType, defaultText, iconClass) {
        if (!btn) return;
        const span = btn.querySelector('span');
        const icon = btn.querySelector('i');
        
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');

        if (stateType === 'success') {
            if (span) span.innerText = 'Generated Successfully';
            if (icon) icon.className = 'fa-solid fa-check mr-2';
            btn.classList.replace('bg-indigo-600', 'bg-emerald-600');
            btn.classList.replace('hover:bg-indigo-700', 'hover:bg-emerald-700');
            btn.classList.replace('bg-amber-600', 'bg-emerald-600');
            btn.classList.replace('hover:bg-amber-700', 'hover:bg-emerald-700');
        } else {
            if (span) span.innerText = defaultText;
            if (icon) icon.className = iconClass || 'fa-solid fa-robot mr-2';
            btn.classList.replace('bg-emerald-600', btn === triggerHomeBtn ? 'bg-amber-600' : 'bg-indigo-600');
            btn.classList.replace('hover:bg-emerald-700', btn === triggerHomeBtn ? 'hover:bg-amber-700' : 'hover:bg-indigo-700');
        }
    }

    function syncState() {
        const type = reportTypeSelect.value;
        const studentId = studentSelect.value;

        if (!type) {
            dateRow.classList.add('hidden');
            dailyWrapper.classList.add('hidden');
            generalWrapper.classList.add('hidden');
            reviewActionSection.classList.add('hidden');
            return;
        }

        const key = getCacheKey();
        const cached = cache[key] || {};

        if (type === 'daily') {
            // Show date selection & preset/retain date
            dateRow.classList.remove('hidden');
            if (!dateInput.value) {
                dateInput.value = lastSelectedDate || todayStr;
            }

            dailyWrapper.classList.remove('hidden');
            generalWrapper.classList.add('hidden');

            // Action buttons: Daily Report skips admin approval
            btnApproval.classList.add('hidden');
            btnPublish.classList.remove('hidden');

            // Load text from cache or set empty
            dailyContent.value = cached.dailyContent || '';
            homeRec.value = cached.homeRec || '';

            setBtnState(triggerDailyBtn, cached.dailyGen ? 'success' : 'ready', 'Generate Daily Report via AI', 'fa-solid fa-robot mr-2');
            setBtnState(triggerHomeBtn, cached.homeGen ? 'success' : 'ready', 'Generate Home-Based Recommendations via AI', 'fa-solid fa-lightbulb mr-2');
        } else {
            // Non-daily report (Quarterly, Mid-Year, Year-End)
            dateRow.classList.add('hidden');
            dailyWrapper.classList.add('hidden');
            generalWrapper.classList.remove('hidden');

            // Action buttons: Requires admin approval
            btnPublish.classList.add('hidden');
            btnApproval.classList.remove('hidden');

            generalContent.value = cached.generalContent || '';

            const typeLabel = reportTypeSelect.options[reportTypeSelect.selectedIndex]?.text || 'Report';
            document.getElementById('general-btn-text').innerText = `Generate ${typeLabel} via AI`;

            setBtnState(triggerGeneralBtn, cached.generalGen ? 'success' : 'ready', `Generate ${typeLabel} via AI`, 'fa-solid fa-robot mr-2');
        }

        reviewActionSection.classList.remove('hidden');
    }

    // EVENT: Student Selection Change
    studentSelect.addEventListener('change', function() {
        saveCurrentToCache();
        syncState();
    });

    // EVENT: Report Type Change
    reportTypeSelect.addEventListener('change', function() {
        saveCurrentToCache();
        syncState();
    });

    // EVENT: Date Picker Change
    dateInput.addEventListener('change', function() {
        saveCurrentToCache();
        if (this.value) {
            lastSelectedDate = this.value;
        }
        syncState();
    });

    // GENERIC AI FETCH HELPER
    function runAiGeneration(btn, sectionType) {
        const studentId = studentSelect.value;
        const type = reportTypeSelect.value;
        const date = dateInput.value;

        if (!studentId) {
            alert('Please select a student first.');
            studentSelect.focus();
            return;
        }

        if (!type) {
            alert('Please select a report type first.');
            reportTypeSelect.focus();
            return;
        }

        // DATE VALIDATION: Check if date is selected when generating Daily Report
        if (type === 'daily' && (!date || date.trim() === '')) {
            alert('Please select a valid date before generating the Daily Report.');
            dateInput.focus();
            return;
        }

        const span = btn.querySelector('span');
        const icon = btn.querySelector('i');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        if (icon) icon.className = 'fa-solid fa-spinner fa-spin mr-2';
        if (span) span.innerText = 'Generating content via AI...';

        fetch("{{ route('teacher.reports.generate-ai') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                student_id: studentId,
                report_type: type,
                report_date: date,
                section: sectionType
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const key = getCacheKey();
                if (!cache[key]) cache[key] = {};

                if (type === 'daily') {
                    if (sectionType === 'home_recommendation') {
                        homeRec.value = data.report_content;
                        cache[key].homeRec = data.report_content;
                        cache[key].homeGen = true;
                        setBtnState(triggerHomeBtn, 'success', '', 'fa-solid fa-lightbulb mr-2');
                    } else {
                        dailyContent.value = data.report_content;
                        cache[key].dailyContent = data.report_content;
                        cache[key].dailyGen = true;
                        setBtnState(triggerDailyBtn, 'success', '', 'fa-solid fa-robot mr-2');
                    }
                } else {
                    generalContent.value = data.report_content;
                    cache[key].generalContent = data.report_content;
                    cache[key].generalGen = true;
                    setBtnState(triggerGeneralBtn, 'success', '', 'fa-solid fa-robot mr-2');
                }
            } else {
                alert("AI Error: " + (data.message || 'Failed to generate report content.'));
                syncState();
            }
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong connecting to the server.");
            syncState();
        });
    }

    if (triggerDailyBtn) {
        triggerDailyBtn.addEventListener('click', function() {
            runAiGeneration(this, 'daily_report');
        });
    }

    if (triggerHomeBtn) {
        triggerHomeBtn.addEventListener('click', function() {
            runAiGeneration(this, 'home_recommendation');
        });
    }

    if (triggerGeneralBtn) {
        triggerGeneralBtn.addEventListener('click', function() {
            runAiGeneration(this, 'general');
        });
    }

    // FORM SUBMIT: Compile final content before sending
    form.addEventListener('submit', function(e) {
        const type = reportTypeSelect.value;
        if (type === 'daily') {
            let compiled = dailyContent.value.trim();
            if (!compiled) {
                e.preventDefault();
                alert('Please enter or generate the Daily Report content before saving.');
                dailyContent.focus();
                return;
            }
            finalContentInput.value = compiled;
        } else {
            let compiled = generalContent.value.trim();
            if (!compiled) {
                e.preventDefault();
                alert('Please enter or generate the report content before saving.');
                generalContent.focus();
                return;
            }
            finalContentInput.value = compiled;
        }
    });

    // Initial sync
    syncState();
});
</script>
@endpush

@endsection