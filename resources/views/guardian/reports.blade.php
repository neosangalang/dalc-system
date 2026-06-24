@extends('layouts.app')

@section('title', 'Official Reports')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block" x-data="{ activeViewModal: null }">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1 m-0">Official Student Reports</h3>
            <p class="text-sm font-medium text-slate-500 m-0">Review progress summaries and official documentation for <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full shadow-sm text-indigo-600 font-bold text-sm hover:bg-slate-50 focus:outline-none transition-colors">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shrink-0">
                        <i class="fa fa-user"></i>
                    </div>
                    Viewing: {{ $activeChild->first_name }}
                    <i class="fa fa-chevron-down ml-1 text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <ul x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 list-none pl-0">
                    <li><h6 class="px-4 py-2 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider m-0">My Children</h6></li>
                    
                    @foreach($myChildren as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium transition-colors decoration-transparent {{ $activeChild->id === $child->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                @if($activeChild->id === $child->id)
                                    <i class="fa fa-check text-white text-xs mt-0.5"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    @if(isset($activeChild))
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden border-t-4 border-t-indigo-600">
        
        <div class="px-6 py-5 border-b border-slate-100 bg-white">
            <h5 class="font-extrabold text-slate-800 m-0 text-lg flex items-center">
                <i class="fa fa-file-signature text-indigo-600 mr-2.5"></i> Document Library
            </h5>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Prepared By</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Date Generated</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                            
                            <td class="px-6 py-4 text-sm font-extrabold text-slate-800">
                                {{ $report->student->first_name }} {{ $report->student->last_name }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-widest shadow-sm">
                                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                Teacher {{ $report->teacher->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                @if($report->report_type === 'daily' && $report->report_date)
                                    {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                @else
                                    {{ $report->created_at->format('M d, Y') }}
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <button @click="activeViewModal = {{ $report->id }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors shadow-sm">
                                        <i class="fa fa-book-open mr-1.5"></i> Read
                                    </button>
                                    
                                    <a href="{{ route('guardian.reports.pdf', $report->id) }}" title="Download PDF" class="inline-flex items-center px-3 py-1.5 bg-rose-500 text-white text-xs font-bold rounded-lg hover:bg-rose-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                        <i class="fa fa-file-pdf mr-1.5"></i> PDF
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <div x-show="activeViewModal === {{ $report->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="activeViewModal = null"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200">
                                    
                                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-start">
                                        <div>
                                            <h5 class="text-lg font-extrabold text-slate-800 m-0 mb-0.5">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }} Report</h5>
                                            <p class="text-xs font-medium text-slate-500 m-0">Generated on {{ $report->created_at->format('F j, Y') }}</p>
                                        </div>
                                        <button type="button" @click="activeViewModal = null" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                    </div>
                                    
                                    <div class="p-6">
                                        @if($report->report_type === 'daily')
                                            <p class="mb-4 text-slate-800 font-extrabold text-base">Dear Parent/Guardian,</p>
                                            
                                            @php
                                                $reportDate = $report->report_date ? $report->report_date : $report->created_at->format('Y-m-d');
                                                $dailyLogWithPhoto = \App\Models\DailyLog::where('student_id', $report->student_id)
                                                    ->whereDate('log_date', $reportDate)
                                                    ->whereNotNull('image_path')
                                                    ->latest()
                                                    ->first();
                                            @endphp

                                            @if($dailyLogWithPhoto)
                                                <div class="text-center mb-6 p-3 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                                                    <img src="{{ asset('storage/' . $dailyLogWithPhoto->image_path) }}" alt="Student Progress Photo" class="max-h-72 w-auto mx-auto rounded-lg shadow-sm border-4 border-white">
                                                </div>
                                            @endif
                                            
                                            <div class="p-5 shadow-sm bg-white rounded-xl text-base leading-relaxed border-l-4 border-indigo-500 text-slate-700 whitespace-pre-wrap">{{ $report->content }}</div>
                                            
                                            <p class="mt-6 mb-0 text-slate-500 font-medium italic">Warmly,<br>Teacher {{ $report->teacher->name ?? 'Staff' }}</p>
                                        @else
                                            <div class="p-5 bg-slate-50 rounded-xl border border-slate-200 whitespace-pre-wrap text-sm text-slate-700 leading-relaxed">{{ $report->content }}</div>
                                        @endif

                                        <div class="mt-8 pt-6 border-t border-dashed border-slate-200">
                                            @include('components.comment-thread', ['item' => $report])
                                        </div>
                                    </div>
                                    
                                    <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex justify-end">
                                        <button type="button" @click="activeViewModal = null" class="px-6 py-2.5 bg-slate-800 text-white text-sm font-bold rounded-xl hover:bg-slate-900 transition-colors shadow-sm">
                                            Close Window
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                                    <i class="fa fa-folder-open text-3xl text-indigo-300"></i>
                                </div>
                                <h6 class="text-base font-extrabold text-slate-800 m-0 mb-1">No reports available</h6>
                                <p class="text-sm font-medium text-slate-500 m-0">No official reports have been published for {{ $activeChild->first_name }} yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="bg-sky-50 border border-sky-200 text-sky-800 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-500 text-xl"></i> 
            <span class="text-sm font-bold">No children are currently linked to your account.</span>
        </div>
    @endif

</div>

@endsection