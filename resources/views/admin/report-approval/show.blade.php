@extends('layouts.app')

@section('title', 'View Report')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block">

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
                     <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-extrabold rounded-full border border-indigo-100 uppercase tracking-widest">
                        {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="px-8 py-8">
            <h4 class="font-extrabold text-slate-400 text-[10px] uppercase tracking-widest mb-4">Report Details</h4>
            <div class="p-8 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner">
                <div class="prose max-w-none text-slate-700 text-sm leading-relaxed">
                    {!! nl2br(e($report->content)) !!}
                </div>
            </div>
        </div>

        <div class="px-8 py-6 bg-white border-t border-slate-100 flex justify-end gap-3">
            
            <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="px-6 py-3 bg-white border border-rose-200 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-50 transition-all shadow-sm">
                    <i class="fa fa-xmark mr-2"></i> Reject Report
                </button>
            </form>

            <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Approve this report? This will trigger the notification to the guardian.');">
                @csrf
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-200">
                    <i class="fa fa-check mr-2"></i> Approve & Notify
                </button>
            </form>
            
        </div>
    </div>
</div>

@endsection