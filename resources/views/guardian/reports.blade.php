@extends('layouts.app')

@section('title', 'Official Reports')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]" x-data="{ activeViewModal: null, enlargedPhotoUrl: null, enlargedPhotoTitle: '' }">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mb-1 m-0 leading-tight">Official Student Reports</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Review progress summaries and official documentation for <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
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
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border-2 border-slate-300 overflow-hidden md:border-t-4 border-t-indigo-600">
        
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-2 md:border-x-0 md:border-t-0 border-slate-200 border-t-4 border-t-indigo-600 md:border-b-2 bg-white rounded-[16px] md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0">
            <h5 class="font-extrabold text-slate-800 m-0 text-base sm:text-lg flex items-center">
                <i class="fa fa-file-signature text-indigo-600 mr-2.5"></i> Document Library
            </h5>
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
                                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
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
                                    @if($report->report_type === 'daily' && $report->report_date)
                                        {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                    @else
                                        {{ $report->created_at->format('M d, Y') }}
                                    @endif
                                </span>
                            </td>
                            
                            <!-- 5. Actions Footer (2-col Grid on mobile) -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-[16px] md:rounded-none">
                                <div class="grid grid-cols-2 md:flex md:justify-end gap-2 w-full md:w-auto">
                                    <button @click="activeViewModal = {{ $report->id }}" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-white md:bg-indigo-50 border-2 border-slate-300 md:border-indigo-300 text-indigo-600 md:text-indigo-700 text-[13px] md:text-xs font-bold rounded-lg hover:bg-slate-50 md:hover:bg-indigo-100 transition-colors shadow-sm">
                                        <i class="fa fa-book-open mr-1.5 text-indigo-500"></i> Read
                                    </button>
                                    
                                    <a href="{{ route('guardian.reports.pdf', $report->id) }}" target="_blank" title="View PDF Report" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-rose-500 text-white text-[13px] md:text-xs font-bold rounded-lg border-2 border-rose-600 hover:bg-rose-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                        <i class="fa fa-file-pdf mr-1.5"></i> PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell p-4 sm:p-6 bg-white rounded-[16px] md:rounded-none">
                                <div class="w-full text-center py-10 sm:py-12 border-[3px] border-dashed border-slate-300 rounded-[16px] sm:rounded-2xl bg-slate-50/50">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-3 sm:mb-4 border-2 border-indigo-200">
                                        <i class="fa fa-folder-open text-2xl sm:text-3xl text-indigo-400"></i>
                                    </div>
                                    <h6 class="text-[14px] sm:text-base font-extrabold text-slate-800 m-0 mb-1">No reports available</h6>
                                    <p class="text-[12px] sm:text-sm font-medium text-slate-500 m-0">No official reports have been published for {{ $activeChild->first_name ?? 'this student' }} yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- READING MODALS (Placed outside <table> so HTML parser does not foster-parent or corrupt the forms) -->
        @foreach($reports as $report)
            <div x-show="activeViewModal === {{ $report->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="activeViewModal === {{ $report->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="activeViewModal = null"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="activeViewModal === {{ $report->id }}" x-transition class="inline-block align-bottom bg-white rounded-[16px] sm:rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border-[3px] border-slate-300 relative z-10">
                        
                        <!-- Modal Header -->
                        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b-2 border-slate-200 bg-slate-50 flex justify-between items-start shrink-0">
                            <div>
                                <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-0.5 leading-tight">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }} Report</h5>
                                <p class="text-[11px] sm:text-xs font-medium text-slate-500 m-0">Generated on {{ $report->created_at->format('F j, Y') }}</p>
                            </div>
                            <button type="button" @click="activeViewModal = null" class="text-slate-400 hover:text-slate-700 ml-4"><i class="fa fa-times text-xl"></i></button>
                        </div>
                        
                        <!-- Modal Body (Scrollable) -->
                        <div class="p-4 sm:p-6 max-h-[60vh] sm:max-h-[600px] overflow-y-auto bg-white [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-slate-50 [&::-webkit-scrollbar-thumb]:bg-slate-300">
                            @if($report->report_type === 'daily')
                                <p class="mb-3 sm:mb-4 text-slate-800 font-extrabold text-[14px] sm:text-base">Dear Parent/Guardian,</p>
                                
                                @php
                                    $reportDate = $report->report_date ? $report->report_date : $report->created_at->format('Y-m-d');
                                    $dailyLogWithPhoto = \App\Models\DailyLog::where('student_id', $report->student_id)
                                        ->whereDate('log_date', $reportDate)
                                        ->latest()
                                        ->first();

                                    $photoUrl = null;
                                    if ($report->image_path) {
                                        $photoUrl = route('secure.report-photo', $report->id);
                                    } elseif ($dailyLogWithPhoto && $dailyLogWithPhoto->image_path) {
                                        $photoUrl = route('secure.log-photo', $dailyLogWithPhoto->id);
                                    }
                                @endphp

                                @if($photoUrl)
                                    <div class="mb-5 sm:mb-6 p-3 sm:p-4 bg-slate-50 rounded-2xl border-2 border-slate-200 flex flex-col items-center">
                                        <div class="relative group cursor-pointer max-w-md w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                                             @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($report->student->first_name) }}'"
                                             title="Click to enlarge photo">
                                            <img src="{{ $photoUrl }}" alt="Observation Photo" class="max-h-56 sm:max-h-72 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2">
                                                <i class="fa-solid fa-magnifying-glass-plus text-base"></i> Click to enlarge
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($report->student->first_name) }}'"
                                                class="mt-2.5 text-indigo-600 hover:text-indigo-800 font-bold text-xs flex items-center gap-1.5 cursor-pointer bg-transparent border-0 p-0">
                                            <i class="fa-solid fa-camera text-indigo-500"></i> View enlarged photo
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="p-4 sm:p-5 shadow-sm bg-white rounded-xl text-[13px] sm:text-base leading-relaxed border-y-2 border-r-2 border-l-4 border-slate-200 border-l-indigo-600 text-slate-700 whitespace-pre-wrap">{{ $report->content }}</div>

                                @if($dailyLogWithPhoto && $dailyLogWithPhoto->home_recommendations)
                                    <div class="mt-4 p-4 sm:p-5 flex flex-col justify-center bg-emerald-50/60 rounded-xl border-y-2 border-r-2 border-l-4 border-emerald-200 border-l-emerald-500 shadow-sm">
                                        <h6 class="font-extrabold text-emerald-800 mb-1.5 flex items-center m-0 text-[13px] sm:text-[14px]">
                                            <i class="fa fa-house-user mr-2 text-emerald-600"></i> Tonight's Suggested Home Activity:
                                        </h6>
                                        <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed text-emerald-950 whitespace-pre-wrap font-medium">{{ $dailyLogWithPhoto->home_recommendations }}</p>
                                    </div>
                                @endif
                                
                                <p class="mt-5 sm:mt-6 mb-0 text-slate-600 font-medium italic text-[13px] sm:text-base">Warmly,<br>Teacher {{ $report->teacher->name ?? 'Staff' }}</p>
                            @else
                                @if($report->image_path)
                                    <div class="mb-5 p-3 sm:p-4 bg-slate-50 rounded-2xl border-2 border-slate-200 flex flex-col items-center">
                                        <div class="relative group cursor-pointer max-w-md w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                                             @click="enlargedPhotoUrl = '{{ route('secure.report-photo', $report->id) }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($report->student->first_name) }}'"
                                             title="Click to enlarge photo">
                                            <img src="{{ route('secure.report-photo', $report->id) }}" alt="Report Photo" class="max-h-56 sm:max-h-72 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2">
                                                <i class="fa-solid fa-magnifying-glass-plus text-base"></i> Click to enlarge
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="p-4 sm:p-5 bg-slate-50 rounded-xl border-2 border-slate-300 whitespace-pre-wrap text-[13px] sm:text-sm text-slate-700 leading-relaxed">{{ $report->content }}</div>
                            @endif

                            <div class="mt-6 sm:mt-8 pt-5 sm:pt-6 border-t-[3px] border-dashed border-slate-300">
                                @include('components.comment-thread', ['item' => $report])
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="px-4 sm:px-6 py-4 sm:py-5 bg-slate-50 border-t-2 border-slate-200 flex flex-col-reverse sm:flex-row justify-between items-center gap-3 shrink-0">
                            <button type="button" @click="activeViewModal = null" class="w-full sm:w-auto px-6 py-3 sm:py-2.5 bg-slate-800 text-white text-[13px] sm:text-sm font-bold rounded-xl border-2 border-slate-900 hover:bg-slate-900 transition-colors shadow-sm">
                                Close Window
                            </button>
                            <a href="{{ route('guardian.reports.pdf', $report->id) }}" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-rose-500 text-white text-[13px] sm:text-sm font-bold rounded-xl border-2 border-rose-600 hover:bg-rose-600 transition-colors shadow-sm decoration-transparent">
                                <i class="fa fa-file-pdf mr-2"></i> View PDF Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="bg-sky-50 border-2 border-sky-300 text-sky-800 px-4 sm:px-5 py-3 sm:py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-600 text-lg sm:text-xl shrink-0"></i> 
            <span class="text-[13px] sm:text-sm font-bold leading-snug">No children are currently linked to your account.</span>
        </div>
    @endif

    <!-- Enlarge Photo Lightbox Modal -->
    <div x-show="enlargedPhotoUrl" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6" @keydown.escape.window="enlargedPhotoUrl = null">
        <div x-show="enlargedPhotoUrl" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="enlargedPhotoUrl = null"></div>
        <div x-show="enlargedPhotoUrl" x-transition class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col z-10 border border-slate-200">
            <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm sm:text-base truncate pr-4 m-0" x-text="enlargedPhotoTitle"></h4>
                <button type="button" @click="enlargedPhotoUrl = null" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto flex items-center justify-center bg-slate-900/5 max-h-[calc(90vh-60px)]">
                <img :src="enlargedPhotoUrl" :alt="enlargedPhotoTitle" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-md">
            </div>
        </div>
    </div>

</div>

@endsection