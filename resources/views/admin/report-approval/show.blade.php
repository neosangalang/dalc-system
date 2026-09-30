@extends('layouts.app')

@section('title', 'View Report')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block" x-data="{ showRevisionModal: false }">

    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('admin.report-approval.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="fa fa-arrow-left mr-2"></i> Back to Pending Reports
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-8 py-8 border-b border-slate-100">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 mb-2">
                        {{ $report->student->first_name }} {{ $report->student->last_name }}
                    </h2>
                    <p class="text-slate-500 font-medium">
                        Submitted by <span class="font-bold text-slate-700">{{ $report->teacher->name }}</span> 
                        on {{ $report->created_at->format('M d, Y, h:i A') }}
                    </p>
                </div>
                <div class="text-right">
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
                     <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-extrabold rounded-full border border-indigo-100 uppercase tracking-widest">
                        {{ strtoupper($reportTypeMap[$report->report_type] ?? str_replace('_', ' ', $report->report_type)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-8 py-8">
            <h4 class="font-extrabold text-slate-400 text-[10px] uppercase tracking-widest mb-4">Report Details</h4>
            <div class="p-8 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner">
                <div class="prose max-w-none text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">
                    {{ $report->content }}
                </div>
            </div>

            @if($report->revision_notes)
                <div class="mt-6 p-5 bg-amber-50 rounded-xl border border-amber-200">
                    <h5 class="text-xs font-extrabold text-amber-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left"></i> Previous Revision Notes:
                    </h5>
                    <p class="text-xs font-medium text-amber-900 m-0 leading-relaxed">{{ $report->revision_notes }}</p>
                </div>
            @endif
        </div>

        <div class="px-8 py-6 bg-white border-t border-slate-100 flex justify-end gap-3">
            
            <!-- TRIGGER REVISION MODAL -->
            <button type="button" @click="showRevisionModal = true" class="px-6 py-3 bg-white border border-rose-200 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-50 transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <i class="fa fa-pen-to-square"></i> Request Revision / Reject
            </button>

            <!-- APPROVE FORM -->
            <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Approve this report? This will trigger the notification to the guardian.');">
                @csrf
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-200 flex items-center gap-2 cursor-pointer">
                    <i class="fa fa-check"></i> Approve & Notify
                </button>
            </form>
            
        </div>
    </div>

    <!-- REVISION NOTES MODAL -->
    <div x-show="showRevisionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showRevisionModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showRevisionModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="showRevisionModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 p-6 relative z-50">
                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-extrabold text-slate-800 m-0">Request Report Revision</h4>
                            <p class="text-xs font-medium text-slate-500 m-0">Send feedback to Teacher {{ $report->teacher->name }}</p>
                        </div>
                    </div>
                    <button type="button" @click="showRevisionModal = false" class="text-slate-400 hover:text-slate-600 text-lg">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="rejected">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Revision Notes & Feedback <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="revision_notes" rows="4" required placeholder="e.g. Please add more details regarding fine motor progress and specify behavioral support techniques in math activities. The AI will use these notes to revise the draft." class="w-full rounded-xl border border-slate-300 p-3.5 text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 transition-all resize-none shadow-sm"></textarea>
                        <p class="text-[11px] font-medium text-slate-500 mt-1.5 leading-snug">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-500 mr-1"></i>
                            The Teacher will see these notes, and their <strong>AI Assistant</strong> will automatically rewrite and polish the report based on your feedback.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                        <button type="button" @click="showRevisionModal = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-200 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-paper-plane text-[11px]"></i> Submit Revision Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection