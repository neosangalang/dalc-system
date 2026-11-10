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
                                <span>Tip: Click <strong>"Revise with AI"</strong> below to automatically incorporate this feedback into your report!</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-2">Report Classification</label>
                <input type="text" disabled value="{{ ucwords(str_replace('_', ' ', $report->report_type)) }} Report" class="w-full rounded-xl border-black/15 bg-slate-100 px-4 py-2.5 text-[13px] font-bold text-[#6B7086]">
            </div>

            <!-- ORIGINAL REPORT SENT TO ADMIN (TEACHER'S PREVIOUS WORK) -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 overflow-hidden shadow-xs transition-all">
                <div class="px-5 py-3.5 bg-slate-100/90 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#1E2657]/10 text-[#1E2657] flex items-center justify-center shrink-0 text-xs shadow-xs">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-[12px] font-extrabold text-[#1E2657] m-0 uppercase tracking-wider">
                                    Original Report Sent to Admin
                                </h4>
                                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold uppercase tracking-wider">
                                    Reference Baseline
                                </span>
                            </div>
                            <p class="text-[11px] font-medium text-[#6B7086] m-0">Your previous draft before the administrator requested revisions.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- COPY ORIGINAL BUTTON -->
                        <button type="button" @click="copyOriginalText()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-300 text-[11px] font-bold rounded-lg transition-all shadow-xs cursor-pointer">
                            <i class="fa-solid" :class="copiedOriginal ? 'fa-check text-emerald-500' : 'fa-copy text-slate-400'"></i>
                            <span x-text="copiedOriginal ? 'Copied!' : 'Copy Text'"></span>
                        </button>

                        <!-- RESTORE TO EDITOR BUTTON -->
                        <button type="button" @click="restoreOriginal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-300 text-[11px] font-bold rounded-lg transition-all shadow-xs cursor-pointer" title="Revert the editable box below back to this original text">
                            <i class="fa-solid fa-rotate-left text-slate-400"></i>
                            <span>Restore to Editor</span>
                        </button>

                        <!-- TOGGLE COLLAPSE -->
                        <button type="button" @click="showOriginal = !showOriginal" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs transition-colors cursor-pointer" :title="showOriginal ? 'Collapse original report' : 'Expand original report'">
                            <i class="fa-solid" :class="showOriginal ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>

                <div x-show="showOriginal" x-transition.duration.200ms class="p-5 space-y-3">
                    <div class="p-4 bg-white rounded-xl border border-slate-200 text-[13px] text-slate-800 leading-relaxed font-normal whitespace-pre-wrap select-text max-h-72 overflow-y-auto shadow-inner">
                        {{ $report->content }}
                    </div>

                    @if($report->image_path)
                        <div class="pt-2 flex items-center gap-3 text-xs text-slate-600 font-medium">
                            <i class="fa-solid fa-paperclip text-slate-400"></i>
                            <span>Attached Photo:</span>
                            <a href="{{ route('secure.report-photo', $report->id) }}" target="_blank" class="text-indigo-600 hover:underline font-bold flex items-center gap-1">
                                View attached photo <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-500">
                        <i class="fa-solid fa-circle-info text-indigo-500 text-[10px]"></i>
                        <span>This original report is kept visible here so you can easily reference your previous work while updating the draft below.</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest m-0">Revised Report Content (Editable)</label>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-200">
                            Active Draft
                        </span>
                        <span x-show="aiDraftPendingReview" x-cloak class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10.5px] font-extrabold border border-amber-300 shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> AI-Generated Draft &bull; Review Required
                        </span>
                        <span x-show="teacherValidated" x-cloak class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10.5px] font-extrabold border border-emerald-300 shadow-xs">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Teacher Validated &bull; Reviewed
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" @click="restoreOriginal()" x-show="content !== originalContent" x-cloak class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 hover:text-rose-600 transition-colors cursor-pointer mr-1">
                            <i class="fa-solid fa-rotate-left"></i> Revert to Original
                        </button>

                        <!-- AI REGENERATE BUTTON -->
                        <button type="button" @click="generateAiContent()" :disabled="aiLoading" class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 text-white text-[12px] font-extrabold rounded-xl hover:opacity-95 transition-all shadow-md shadow-indigo-200 disabled:opacity-50 cursor-pointer">
                            <i class="fa fa-wand-magic-sparkles mr-2" :class="aiLoading ? 'fa-spin text-amber-300' : 'text-amber-300'"></i>
                            <span x-text="aiLoading ? 'AI is revising based on feedback...' : '✨ Revise with AI'"></span>
                        </button>
                    </div>
                </div>

                <!-- Contextual Hallucination Warning Banner -->
                <div class="mb-3 p-3 bg-amber-50/90 border border-amber-200/90 rounded-xl text-amber-900 text-[11.5px] leading-relaxed font-semibold flex items-start gap-2 shadow-xs">
                    <span class="shrink-0 text-sm leading-none mt-0.5">💡</span>
                    <div class="flex-1">
                        <span class="font-extrabold text-amber-950">Notice:</span> AI models may occasionally generate incomplete details or inaccuracies (hallucinations). Always review, edit, and validate
                    </div>
                </div>

                <textarea name="content" rows="11" x-model="content" @input="onContentInput()" class="w-full rounded-xl border-black/15 p-4 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white leading-relaxed resize-y" required>{{ old('content', $report->content) }}</textarea>
                <p class="text-[11px] text-[#6B7086] mt-1.5 font-medium">You can edit the report directly, or use AI to rewrite it with the administrator's suggestions while keeping your original work intact above.</p>
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
            originalContent: {!! json_encode($report->content) !!},
            revisionNotes: {!! json_encode($report->revision_notes ?? '') !!},
            aiLoading: false,
            showOriginal: true,
            copiedOriginal: false,
            aiDraftPendingReview: false,
            teacherValidated: false,
            onContentInput() {
                if (this.aiDraftPendingReview) {
                    this.aiDraftPendingReview = false;
                    this.teacherValidated = true;
                }
            },
            copyOriginalText() {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(this.originalContent);
                } else {
                    const textArea = document.createElement("textarea");
                    textArea.value = this.originalContent;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                }
                this.copiedOriginal = true;
                setTimeout(() => { this.copiedOriginal = false; }, 2000);
                if (window.showToast) {
                    window.showToast('Original report copied to clipboard.', 'success');
                }
            },
            restoreOriginal() {
                if (this.content === this.originalContent || confirm('Revert the editor back to your original submitted report? Any unsaved edits will be replaced.')) {
                    this.content = this.originalContent;
                    this.aiDraftPendingReview = false;
                    this.teacherValidated = false;
                    if (window.showToast) {
                        window.showToast('Original draft restored to editor.', 'success');
                    }
                }
            },
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
                            existing_content: this.originalContent || this.content
                        })
                    });
                    let data = await response.json();
                    if(data.success) {
                        this.content = data.report_content;
                        this.aiDraftPendingReview = true;
                        this.teacherValidated = false;
                        window.hasUnsavedAiChanges = true;
                        window.showToast('AI revised content generated! Your original submission remains preserved above.', 'success');
                    } else {
                        window.showToast(data.message || 'Failed to generate AI content.', 'error');
                    }
                } catch(e) {
                    console.error(e);
                    window.showToast('An error occurred while communicating with the AI service.', 'error');
                } finally {
                    this.aiLoading = false;
                }
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector('form[action*="reports"]');
        if (form) {
            form.addEventListener('submit', function() {
                window.hasUnsavedAiChanges = false;
            });
        }
    });
</script>
@endpush

@endsection