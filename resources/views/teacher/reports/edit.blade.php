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

            <div>
                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-2">Report Classification</label>
                <input type="text" disabled value="{{ ucwords(str_replace('_', ' ', $report->report_type)) }} Report" class="w-full rounded-xl border-black/15 bg-slate-100 px-4 py-2.5 text-[13px] font-bold text-[#6B7086]">
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest">Report Content (Editable)</label>
                    
                    <!-- AI REGENERATE BUTTON -->
                    <button type="button" @click="generateAiContent()" :disabled="aiLoading" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[11px] font-extrabold rounded-lg hover:opacity-90 transition-all shadow-sm disabled:opacity-50">
                        <i class="fa fa-wand-magic-sparkles mr-1.5" :class="aiLoading ? 'fa-spin' : ''"></i>
                        <span x-text="aiLoading ? 'AI is rewriting...' : 'Polish with AI'"></span>
                    </button>
                </div>

                <textarea name="content" rows="10" x-model="content" class="w-full rounded-xl border-black/15 p-4 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white leading-relaxed" required>{{ old('content', $report->content) }}</textarea>
                <p class="text-[11px] text-[#6B7086] mt-1.5 font-medium">Make the requested corrections or click "Polish with AI" to have the model refine the text based on daily logs.</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                <a href="{{ route('teacher.reports.index') }}" class="px-5 py-2.5 bg-white border border-black/15 text-[#6B7086] text-[13px] font-extrabold rounded-xl hover:bg-slate-50 transition-colors shadow-sm decoration-transparent">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#10B981]">
                    <i class="fa fa-paper-plane mr-1.5"></i> Resubmit for Approval
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
                            report_date: '{{ $report->report_date }}'
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