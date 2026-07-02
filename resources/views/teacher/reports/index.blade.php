@extends('layouts.app')

@section('title', 'My Reports')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- ADDED: bg-slate-50 and min-h-screen for high contrast background -->
<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block" x-data="{ activeViewModal: null }">

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

    <!-- UPGRADED: shadow-md and border-slate-300 -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h6 class="text-sm font-extrabold text-slate-800 m-0 flex items-center">
                <i class="fa fa-filter text-indigo-600 mr-2"></i> Filter Reports
            </h6>
        </div>
        <div class="p-6">
            <form action="{{ route('teacher.reports.index') }}" method="GET" class="m-0">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    
                    <div class="md:col-span-4">
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            <i class="fa fa-search mr-1"></i> Search Keyword
                        </label>
                        <!-- UPGRADED: border-slate-300 -->
                        <div class="relative w-full shadow-sm rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                                <i class="fa fa-font"></i>
                            </span>
                            <input type="text" name="search" class="w-full border-0 py-2.5 pl-10 pr-3 text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 bg-transparent" placeholder="Search name or type..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            <i class="fa fa-user-graduate mr-1"></i> Specific Student
                        </label>
                        <!-- UPGRADED: border-slate-300 -->
                        <select name="student_id" class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-indigo-50/30">
                            <option value="">All My Students</option>
                            @if(isset($myStudents))
                                @foreach($myStudents as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            <i class="fa fa-file-alt mr-1"></i> Report Type
                        </label>
                        <!-- UPGRADED: border-slate-300 -->
                        <select name="type" class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">All Types</option>
                            <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                            Filter
                        </button>
                        
                        @if(request('search') || request('student_id') || request('type'))
                            <a href="{{ route('teacher.reports.index') }}" title="Clear Filters" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-rose-300 text-rose-500 text-sm font-bold rounded-xl hover:bg-rose-50 transition-colors shadow-sm decoration-transparent">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

    <!-- UPGRADED: shadow-md and border-slate-300 -->
    <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-200 flex flex-wrap justify-between items-center bg-white gap-4">
            <h5 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                    <i class="fa fa-file-signature"></i>
                </div>
                My Generated Reports
            </h5>
            
            <a href="{{ route('teacher.reports.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 decoration-transparent">
                <i class="fa fa-wand-magic-sparkles mr-2"></i> Generate New Report
            </a>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <!-- UPGRADED: border-slate-300 -->
                <thead class="bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student Name</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <!-- UPGRADED: divide-slate-200 -->
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            
                            <td class="px-6 py-4 text-sm font-extrabold text-slate-800">
                                {{ $report->student->first_name }} {{ $report->student->last_name }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-widest shadow-sm">
                                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                @if($report->report_type === 'daily' && $report->report_date)
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                @else
                                    {{ $report->created_at->format('M d, Y') }}
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'pending_approval' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending_approval' => 'Pending Approval',
                                        'approved' => 'Approved',
                                        'rejected' => 'Needs Revision',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-widest border shadow-sm {{ $statusClasses[$report->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ $statusLabels[$report->status] ?? 'Unknown' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    
                                    <button @click="activeViewModal = {{ $report->id }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors shadow-sm hover:border-indigo-300">
                                        <i class="fa fa-eye mr-1.5"></i> View
                                    </button>
                                    
                                    <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="inline-flex items-center px-3 py-1.5 bg-rose-50 border border-rose-300 text-rose-600 text-xs font-bold rounded-lg hover:bg-rose-100 transition-colors shadow-sm decoration-transparent hover:border-rose-400">
                                        <i class="fa fa-file-pdf mr-1.5"></i> PDF
                                    </a>

                                    <form action="{{ route('teacher.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report? This cannot be undone.');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete Report" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-slate-300 text-slate-400 rounded-lg hover:bg-rose-50 hover:border-rose-300 hover:text-rose-500 transition-colors shadow-sm">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- UPGRADED MODAL OUTLINE -->
                        <div x-show="activeViewModal === {{ $report->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="activeViewModal = null"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-300">
                                    
                                    <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex justify-between items-start">
                                        <div>
                                            <h5 class="text-lg font-extrabold text-slate-800 m-0 mb-0.5">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }}</h5>
                                            <p class="text-xs font-medium text-slate-500 m-0">Generated on {{ $report->created_at->format('F j, Y') }}</p>
                                        </div>
                                        <button type="button" @click="activeViewModal = null" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                    </div>
                                    
                                    <div class="p-6">
                                        <!-- UPGRADED: border-slate-300 -->
                                        <div class="p-5 bg-slate-50 rounded-xl border border-slate-300 shadow-inner whitespace-pre-wrap text-sm text-slate-700 leading-relaxed max-h-[400px] overflow-y-auto">
                                            {{ $report->content }}
                                        </div>
                                        
                                        <div class="mt-6 pt-6 border-t border-dashed border-slate-300">
                                            @include('components.comment-thread', ['item' => $report])
                                        </div>
                                    </div>
                                    
                                    <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-between items-center gap-3">
                                        <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-rose-300 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-50 transition-colors shadow-sm decoration-transparent">
                                            <i class="fa fa-file-pdf mr-2"></i> Download as PDF
                                        </a>
                                        <button type="button" @click="activeViewModal = null" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-colors shadow-sm border border-slate-700">
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <i class="fa fa-file-alt text-3xl text-slate-300"></i>
                                </div>
                                <h6 class="text-base font-extrabold text-slate-800 m-0 mb-1">No reports generated yet.</h6>
                                <p class="text-sm font-medium text-slate-500 m-0 mb-4">Create quarterly or annual summaries for your students.</p>
                                <a href="{{ route('teacher.reports.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm decoration-transparent">
                                    Generate Your First Report
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($reports, 'links'))
            <!-- UPGRADED: border-slate-200 -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

@endsection