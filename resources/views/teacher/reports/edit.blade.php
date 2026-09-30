@extends('layouts.app')

@section('title', 'Revise Report')

@section('content')

<div class="w-full max-w-[900px] mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
            <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center mr-3 shrink-0">
                    <i class="fa fa-pen-to-square"></i>
                </div>
                Revise Report for {{ $report->student->first_name }} {{ $report->student->last_name }}
            </h3>
            
            <a href="{{ route('teacher.reports.index') }}" class="text-sm font-bold text-[#6B7086] hover:text-[#1E2657] transition-colors decoration-transparent">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <form action="{{ route('teacher.reports.update', $report->id) }}" method="POST" class="p-6 space-y-6" x-data="aiReportEditor()">
            @csrf
            @method('PUT')

            <input type="hidden" name="student_id" value="{{ $report->student_id }}">
            <input type="hidden" name="report_type" value="{{ $report->report_type }}">
            <input type="hidden" name="report_date" value="{{ $report->report_date }}">

            <!-- ADMIN REVISION NOTES SECTION -->
            @if($report->revision_notes)
                <div class="p-5 bg-gradient-to-r from-rose-50 to-amber-50 rounded-2xl border-2 border-rose-200 shadow-sm">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fa-solid fa-comment-dots text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h4 class="text-[13px] font-extrabold text-rose-900 m-0 uppercase tracking-wider flex items-center gap-1.5">
                                    Administrator's Revision Feedback:
                                </h4>
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-extrabold uppercase tracking-widest">
                                    Action Required
                                </span>
                            </div>
                            <p class="text-[13px] font-bold text-slate-800 leading-relaxed m-0 whitespace-pre-wrap">{{ $report->revision_notes }}</p>
                            <div class="mt-3 pt-2.5 border-t border-rose-200/70 flex items-center gap-2 text-[11px] font-extrabold text-indigo-700">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>Tip: Click <strong>"Revise with AI (Groq)"</strong> below to automatically incorporate this feedback into your report!</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-2">Report Classification</label>
                @php
                    $reportTypeMap = [
                        'daily' => 'Daily Report',
                        'q1' => '1st Quarter (Q1) Report',
                        'q2' => '2nd Quarter (Q2) Report',
                        'q3' => '3rd Quarter (Q3) Report',
                        'q4' => '4th Quarter (Q4) Report',
                        'mid_year' => 'Mid-Year Report',
                        'year_end' => 'Year-End Report',
                    ];
                @endphp
                <input type="text" disabled value="{{ $reportTypeMap[$report->report_type] ?? (ucwords(str_replace('_', ' ', $report->report_type)) . ' Report') }}" class="w-full rounded-xl border-black/15 bg-slate-100 px-4 py-2.5 text-[13px] font-bold text-[#6B7086]">
            </div>

            <div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-2">
                    <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest">Report Content (Editable)</label>
                    
                    <!-- AI REGENERATE BUTTON -->
                    <button type="button" @click="generateAiContent()" :disabled="aiLoading" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white text-[12px] font-extrabold rounded-xl hover:opacity-95 transition-all shadow-md shadow-indigo-200 disabled:opacity-50 cursor-pointer">
                        <i class="fa fa-wand-magic-sparkles mr-2" :class="aiLoading ? 'fa-spin text-amber-300' : 'text-amber-300'"></i>
                        <span x-text="aiLoading ? 'AI is revising based on feedback...' : '✨ Revise with AI (Groq)'"></span>
                    </button>
                </div>

                <textarea name="content" rows="11" x-model="content" class="w-full rounded-xl border-black/15 p-4 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white leading-relaxed resize-y" required>{{ old('content', $report->content) }}</textarea>
                <p class="text-[11px] text-[#6B7086] mt-1.5 font-medium">Make direct manual changes or let the AI automatically rewrite the report addressing the Admin's revision feedback.</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                <a href="{{ route('teacher.reports.index') }}" class="px-5 py-2.5 bg-white border border-black/15 text-[#6B7086] text-[13px] font-extrabold rounded-xl hover:bg-slate-50 transition-colors shadow-sm decoration-transparent">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#10B981] flex items-center gap-2 cursor-pointer">
                    <i class="fa fa-paper-plane text-[11px]"></i> Resubmit for Approval
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function aiReportEditor() {
        return {
            content: {!! json_encode(old('content', $report->content)) !!},
            revisionNotes: {!! json_encode($report->revision_notes ?? '') !!},
            aiLoading: false,
            async generateAiContent() {
                this.aiLoading = true;
                try {
                    let response = await fetch('{{ route('teacher.reports.generate-ai') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            student_id: '{{ $report->student_id }}',
                            report_type: '{{ $report->report_type }}',
                            report_date: '{{ $report->report_date }}',
                            revision_notes: this.revisionNotes,
                            existing_content: this.content
                        })
                    });
                    let data = await response.json();
                    if(data.success) {
                        this.content = data.report_content;
                    } else {
                        alert(data.message || 'Failed to generate AI content.');
                    }
                } catch(e) {
                    console.error(e);
                    alert('An error occurred while communicating with the AI service.');
                } finally {
                    this.aiLoading = false;
                }
            }
        }
    }
</script>
@endpush

@endsection