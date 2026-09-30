@extends('layouts.app')

@section('title', 'Official Reports')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

@php
    $reportTypeMap = [
        'q1' => '1st Quarter Report',
        'q2' => '2nd Quarter Report',
        'q3' => '3rd Quarter Report',
        'q4' => '4th Quarter Report',
        'mid_year' => 'Mid-Year Report',
        'year_end' => 'Year-End Report',
    ];

    $currentType = request('type', 'all');
@endphp

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]" x-data="{ activeViewModal: null }">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mb-1 m-0 leading-tight">Official Student Reports</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Review formal evaluations and academic reports for <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div x-data="{ open: false }" class="relative w-full md:w-auto">
                <!-- Dropdown Button: Full width on mobile -->
                <button @click="open = !open" @click.away="open = false" class="w-full md:w-auto inline-flex items-center justify-between md:justify-start gap-2 px-4 py-2.5 sm:py-2 bg-white border-2 border-slate-300 rounded-xl md:rounded-full shadow-sm text-indigo-600 font-bold text-[13px] sm:text-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none transition-colors">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shrink-0">
                            <i class="fa fa-user"></i>
                        </div>
                        <span>Viewing: {{ $activeChild->first_name }}</span>
                    </div>
                    <i class="fa fa-chevron-down ml-1 text-xs text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Dropdown Menu: Full width on mobile -->
                <ul x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-full md:w-56 bg-white rounded-xl shadow-lg border-2 border-slate-300 py-2 z-50 list-none pl-0">
                    <li><h6 class="px-4 py-2 text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider m-0">My Children</h6></li>
                    
                    @foreach($myChildren as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-2.5 sm:py-2 text-[13px] sm:text-sm font-medium transition-colors decoration-transparent {{ $activeChild->id === $child->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
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

    <!-- SEPARATE REPORT CATEGORY NAVIGATION TABS -->
    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
        <a href="{{ route('guardian.reports') }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'all' || empty($currentType) ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-layer-group"></i> All Formal Reports
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'q1' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-calendar-check"></i> Quarter 1 (Q1)
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'q2' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-calendar-check"></i> Quarter 2 (Q2)
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'q3' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-calendar-check"></i> Quarter 3 (Q3)
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'q4' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-calendar-check"></i> Quarter 4 (Q4)
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'mid_year']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'mid_year' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-award"></i> Mid-Year Report
        </a>
        <a href="{{ route('guardian.reports', ['type' => 'year_end']) }}" class="px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold whitespace-nowrap transition-all shadow-sm decoration-transparent flex items-center gap-1.5 {{ $currentType === 'year_end' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border-2 border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            <i class="fa-solid fa-graduation-cap"></i> Year-End Report
        </a>
    </div>

    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border-2 border-slate-300 overflow-hidden md:border-t-4 border-t-indigo-600">
        
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-2 md:border-x-0 md:border-t-0 border-slate-200 border-t-4 border-t-indigo-600 md:border-b-2 bg-white rounded-[16px] md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0 flex items-center justify-between">
            <h5 class="font-extrabold text-slate-800 m-0 text-base sm:text-lg flex items-center">
                <i class="fa fa-file-signature text-indigo-600 mr-2.5"></i> 
                @if($currentType !== 'all' && isset($reportTypeMap[$currentType]))
                    {{ $reportTypeMap[$currentType] }} Library
                @else
                    Official Document Library
                @endif
            </h5>
            <span class="text-xs font-extrabold bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full border border-indigo-200">
                {{ $reports->count() }} {{ \Illuminate\Support\Str::plural('document', $reports->count()) }}
            </span>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER -->
                <thead class="hidden md:table-header-group bg-slate-50 border-b-[3px] border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Prepared By</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Date Generated</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <!-- MOBILE CARDS / DESKTOP ROWS -->
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y-2 divide-slate-200 bg-transparent md:bg-white">
                    @forelse($reports as $report)
                        <tr class="block md:table-row relative bg-white md:bg-transparent rounded-[16px] md:rounded-none shadow-sm md:shadow-none border-2 border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150 pt-2 md:pt-0">
                            
                            <!-- 1. Student Name -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="md:hidden block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Student Name</span>
                                <div class="text-[14px] sm:text-sm font-extrabold text-slate-800 leading-tight">
                                    {{ $report->student->first_name }} {{ $report->student->last_name }}
                                </div>
                            </td>
                            
                            <!-- 2. Report Type -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-2.5 md:px-6 md:py-4 border-b border-slate-100 md:border-none mt-1 md:mt-0">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Report Type</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border-2 border-indigo-200 uppercase tracking-widest shadow-sm">
                                    {{ $reportTypeMap[$report->report_type] ?? ucwords(str_replace('_', ' ', $report->report_type)) }}
                                </span>
                            </td>
                            
                            <!-- 3. Prepared By -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-2.5 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Prepared By</span>
                                <span class="text-[13px] sm:text-sm font-medium text-slate-700">
                                    Teacher {{ $report->teacher->name ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- 4. Date Generated -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-2.5 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Date Generated</span>
                                <span class="text-[12px] sm:text-xs font-bold text-slate-600">
                                    {{ $report->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            
                            <!-- 5. Actions Footer -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-[16px] md:rounded-none">
                                <div class="grid grid-cols-2 md:flex md:justify-end gap-2 w-full md:w-auto">
                                    <button @click="activeViewModal = {{ $report->id }}" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-white md:bg-indigo-50 border-2 border-slate-300 md:border-indigo-300 text-indigo-600 md:text-indigo-700 text-[13px] md:text-xs font-bold rounded-lg hover:bg-slate-50 md:hover:bg-indigo-100 transition-colors shadow-sm">
                                        <i class="fa fa-book-open mr-1.5 text-indigo-500"></i> Read
                                    </button>
                                    
                                    <a href="{{ route('guardian.reports.pdf', $report->id) }}" title="Download PDF" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-rose-500 text-white text-[13px] md:text-xs font-bold rounded-lg border-2 border-rose-600 hover:bg-rose-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                        <i class="fa fa-file-pdf mr-1.5"></i> PDF
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- READING MODAL -->
                        <div x-show="activeViewModal === {{ $report->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="activeViewModal = null"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="activeViewModal === {{ $report->id }}" x-transition class="inline-block align-bottom bg-white rounded-[16px] sm:rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border-[3px] border-slate-300 relative z-10">
                                    
                                    <!-- Modal Header -->
                                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b-2 border-slate-200 bg-slate-50 flex justify-between items-start shrink-0">
                                        <div>
                                            <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-0.5 leading-tight">{{ $report->student->first_name }}'s {{ $reportTypeMap[$report->report_type] ?? ucwords(str_replace('_', ' ', $report->report_type)) }}</h5>
                                            <p class="text-[11px] sm:text-xs font-medium text-slate-500 m-0">Generated on {{ $report->created_at->format('F j, Y') }}</p>
                                        </div>
                                        <button type="button" @click="activeViewModal = null" class="text-slate-400 hover:text-slate-700 ml-4"><i class="fa fa-times text-xl"></i></button>
                                    </div>
                                    
                                    <!-- Modal Body (Scrollable) -->
                                    <div class="p-4 sm:p-6 max-h-[60vh] sm:max-h-[600px] overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-slate-50 [&::-webkit-scrollbar-thumb]:bg-slate-300">
                                        <div class="p-4 sm:p-5 bg-slate-50 rounded-xl border-2 border-slate-300 whitespace-pre-wrap text-[13px] sm:text-sm text-slate-700 leading-relaxed">{{ $report->content }}</div>

                                        <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t-[3px] border-dashed border-slate-300">
                                            @include('components.comment-thread', ['item' => $report])
                                        </div>
                                    </div>
                                    
                                    <!-- Modal Footer -->
                                    <div class="px-4 sm:px-6 py-4 sm:py-5 bg-slate-50 border-t-2 border-slate-200 flex justify-end shrink-0">
                                        <button type="button" @click="activeViewModal = null" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 bg-slate-800 text-white text-[13px] sm:text-sm font-bold rounded-xl border-2 border-slate-900 hover:bg-slate-900 transition-colors shadow-sm">
                                            Close Window
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell p-4 sm:p-6 bg-white rounded-[16px] md:rounded-none">
                                <div class="w-full text-center py-10 sm:py-12 border-[3px] border-dashed border-slate-300 rounded-[16px] sm:rounded-2xl bg-slate-50/50">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-3 sm:mb-4 border-2 border-indigo-200">
                                        <i class="fa fa-folder-open text-2xl sm:text-3xl text-indigo-400"></i>
                                    </div>
                                    <h6 class="text-[14px] sm:text-base font-extrabold text-slate-800 m-0 mb-1">
                                        @if($currentType !== 'all' && isset($reportTypeMap[$currentType]))
                                            No {{ $reportTypeMap[$currentType] }}s available
                                        @else
                                            No formal reports available
                                        @endif
                                    </h6>
                                    <p class="text-[12px] sm:text-sm font-medium text-slate-500 m-0">No approved official reports are available in this category for {{ $activeChild->first_name ?? 'this student' }} yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="bg-sky-50 border-2 border-sky-300 text-sky-800 px-4 sm:px-5 py-3 sm:py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-600 text-lg sm:text-xl shrink-0"></i> 
            <span class="text-[13px] sm:text-sm font-bold leading-snug">No children are currently linked to your account.</span>
        </div>
    @endif

</div>

@endsection