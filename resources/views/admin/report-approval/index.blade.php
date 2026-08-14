@extends('layouts.app')

@section('title', 'Report Approval')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-[13px] sm:text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-exclamation-triangle mr-3 text-amber-500 text-lg"></i>
                <span class="font-bold text-[13px] sm:text-sm">{{ session('warning') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-amber-500 hover:text-amber-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border border-slate-300 overflow-hidden">
        
        <!-- Header Section (Becomes a floating rounded block on mobile) -->
        <div class="p-4 sm:p-6 border border-slate-200 md:border-none md:border-b flex items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0">
            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 mr-3 shadow-sm border border-emerald-100">
                    <i class="fa fa-file-circle-check"></i>
                </div>
                Pending Reports for Approval
            </h3>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                
                <!-- TABLE HEADER (Hidden on mobile) -->
                <thead class="hidden md:table-header-group bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Submitted By</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Date Submitted</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                
                <!-- TABLE BODY (Transforms to stacked cards on mobile) -->
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-slate-200 bg-transparent md:bg-white">
                    @forelse($pendingReports as $report)
                        <tr class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150">
                            
                            <!-- 1. Student Name -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Student</span>
                                <div class="text-[13px] sm:text-sm font-extrabold text-slate-800 text-right md:text-left">
                                    {{ $report->student->first_name }} {{ $report->student->last_name }}
                                </div>
                            </td>
                            
                            <!-- 2. Report Type -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Type</span>
                                <div class="text-right md:text-left">
                                    <span class="inline-flex px-3 py-1 text-[10px] sm:text-[11px] font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}
                                    </span>
                                </div>
                            </td>
                            
                            <!-- 3. Submitted By (Teacher) -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Teacher</span>
                                <div class="text-[12px] sm:text-sm font-semibold text-slate-700 flex items-center justify-end md:justify-start gap-1.5 md:gap-2">
                                    <i class="fa fa-chalkboard-teacher text-slate-400"></i>
                                    {{ $report->teacher->name ?? 'Unknown Teacher' }}
                                </div>
                            </td>
                            
                            <!-- 4. Date Submitted -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Date</span>
                                <div class="text-[11px] sm:text-xs font-medium text-slate-500 text-right md:text-left">
                                    {{ $report->updated_at->format('M d, Y - h:i A') }}
                                </div>
                            </td>
                            
                            <!-- 5. Actions Footer (Fills width on mobile) -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-2xl md:rounded-none border-t border-slate-100 md:border-none">
                                <div class="flex justify-between md:justify-end gap-2 w-full">
                                    
                                    <a href="{{ route('admin.report-approval.show', $report->id) }}" class="flex-1 md:flex-none inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-white border border-indigo-200 text-indigo-600 text-[11px] md:text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 text-decoration-none hover:border-indigo-300">
                                        <i class="fa fa-eye mr-1.5"></i> View
                                    </a>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="flex-1 md:flex-none m-0" onsubmit="return confirm('Approve this report? This will immediately email the parents.');">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-emerald-500 border border-transparent text-white text-[11px] md:text-xs font-bold rounded-lg hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                            <i class="fa fa-check mr-1.5"></i> Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="flex-1 md:flex-none m-0">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-rose-500 border border-transparent text-white text-[11px] md:text-xs font-bold rounded-lg hover:bg-rose-600 transition-colors shadow-sm focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                            <i class="fa fa-xmark mr-1.5"></i> Reject
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 mb-4 border border-emerald-100">
                                    <i class="fa fa-check-double text-2xl text-emerald-500"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-800 text-lg mb-1">All caught up!</h3>
                                <p class="text-slate-500 text-[13px] sm:text-sm font-medium m-0">There are currently no reports waiting for your approval.</p>
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