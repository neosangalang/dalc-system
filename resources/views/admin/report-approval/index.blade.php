@extends('layouts.app')

@section('title', 'Report Approval')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-exclamation-triangle mr-3 text-amber-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('warning') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-amber-500 hover:text-amber-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">
        
        <div class="p-6 border-b border-slate-200 flex items-center bg-white">
            <h3 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 mr-3 shadow-sm border border-emerald-100">
                    <i class="fa fa-file-circle-check"></i>
                </div>
                Pending Reports for Approval
            </h3>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Submitted By (Teacher)</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Date Submitted</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($pendingReports as $report)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            
                            <td class="px-6 py-4 text-sm font-extrabold text-slate-800">
                                {{ $report->student->first_name }} {{ $report->student->last_name }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-chalkboard-teacher text-slate-400"></i>
                                    {{ $report->teacher->name ?? 'Unknown Teacher' }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-xs font-medium text-slate-500">
                                {{ $report->updated_at->format('M d, Y - h:i A') }}
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    
                                    <a href="{{ route('admin.report-approval.show', $report->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-indigo-200 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 text-decoration-none hover:border-indigo-300">
                                        <i class="fa fa-eye mr-1.5"></i> View
                                    </a>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Approve this report? This will immediately email the parents.');">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-500 border border-transparent text-white text-xs font-bold rounded-lg hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                            <i class="fa fa-check mr-1.5"></i> Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-500 border border-transparent text-white text-xs font-bold rounded-lg hover:bg-rose-600 transition-colors shadow-sm focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                            <i class="fa fa-xmark mr-1.5"></i> Reject
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4 border border-emerald-100">
                                    <i class="fa fa-check-double text-2xl text-emerald-500"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-800 text-lg mb-1">All caught up!</h3>
                                <p class="text-slate-500 text-sm font-medium m-0">There are currently no reports waiting for your approval.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection