@extends('layouts.app')

@section('title', 'Report Approval')

@section('content')

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-exclamation-triangle mr-3 text-amber-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('warning') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-amber-500 hover:text-amber-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border border-black/15 overflow-hidden">
        
        <!-- Header Section -->
        <div class="p-5 sm:p-6 border-b border-black/10 flex items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0">
            <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-10 h-10 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981] mr-3 shrink-0">
                    <i class="fa fa-file-circle-check"></i>
                </div>
                Pending Reports for Approval
            </h3>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                
                <!-- TABLE HEADER -->
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Student</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Submitted By</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Date Submitted</th>
                        <th scope="col" class="px-6 py-4 text-right text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                
                <!-- TABLE BODY -->
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-black/5 bg-transparent md:bg-white">
                    @forelse($pendingReports as $report)
                        <tr class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none hover:bg-slate-50 transition-colors duration-150">
                            
                            <!-- 1. Student Name -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Student</span>
                                <div class="text-[13px] font-extrabold text-[#1E2657] text-right md:text-left">
                                    {{ $report->student->first_name }} {{ $report->student->last_name }}
                                </div>
                            </td>
                            
                            <!-- 2. Report Type -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Type</span>
                                <div class="text-right md:text-left">
                                    <span class="inline-flex px-2.5 py-1 text-[9px] font-extrabold rounded-md bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20 uppercase tracking-widest">
                                        {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}
                                    </span>
                                </div>
                            </td>
                            
                            <!-- 3. Submitted By (Teacher) -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Teacher</span>
                                <div class="text-[12px] font-bold text-[#1E2657] flex items-center justify-end md:justify-start gap-1.5 md:gap-2">
                                    <i class="fa fa-chalkboard-teacher text-[#3B82F6]"></i>
                                    {{ $report->teacher->name ?? 'Unknown Teacher' }}
                                </div>
                            </td>
                            
                            <!-- 4. Date Submitted -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Date</span>
                                <div class="text-[11px] font-medium text-[#6B7086] text-right md:text-left">
                                    {{ $report->updated_at->format('M d, Y - h:i A') }}
                                </div>
                            </td>
                            
                            <!-- 5. Actions Footer -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 bg-[#F4F6FA] md:bg-transparent rounded-b-2xl md:rounded-none border-t border-black/5 md:border-none">
                                <div class="flex justify-between md:justify-end gap-2 w-full">
                                    
                                    <a href="{{ route('admin.report-approval.show', $report->id) }}" class="flex-1 md:flex-none inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-white border border-black/15 text-[#6B7086] text-[11px] md:text-xs font-extrabold rounded-lg hover:bg-slate-50 hover:text-[#3B82F6] transition-colors shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 text-decoration-none hover:border-black/30">
                                        <i class="fa fa-eye mr-1.5"></i> View
                                    </a>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="flex-1 md:flex-none m-0" onsubmit="return confirm('Approve this report? This will immediately email the parents.');">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-[#10B981] border border-transparent text-white text-[11px] md:text-xs font-extrabold rounded-lg hover:bg-emerald-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#10B981] focus:ring-offset-1">
                                            <i class="fa fa-check mr-1.5"></i> Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="flex-1 md:flex-none m-0">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-rose-500 border border-transparent text-white text-[11px] md:text-xs font-extrabold rounded-lg hover:bg-rose-600 transition-colors shadow-sm focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                            <i class="fa fa-xmark mr-1.5"></i> Reject
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F4F6FA] mb-4 border border-black/5">
                                    <i class="fa fa-check-double text-2xl text-[#9CA0AF]"></i>
                                </div>
                                <h3 class="font-extrabold text-[#1E2657] text-lg mb-1">All caught up!</h3>
                                <p class="text-[#6B7086] text-[13px] font-medium m-0">There are currently no reports waiting for your approval.</p>
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