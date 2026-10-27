@extends('layouts.app')

@section('title', 'Previous Reports & Activities')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]" x-data="{ enlargedPhotoUrl: null, enlargedPhotoTitle: '' }">

    <!-- TOP HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <a href="{{ route('guardian.recommendations') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i> Back to Today's Session
                </a>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 leading-tight">Previous Daily Reports & Activities</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Historical archive of daily reports and home recommendations for <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
        </div>

        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div x-data="{ open: false }" class="relative w-full md:w-auto">
                <button @click="open = !open" @click.away="open = false" class="w-full md:w-auto inline-flex items-center justify-between md:justify-start gap-2 px-4 py-2.5 sm:py-2 bg-white border-2 border-slate-300 rounded-xl md:rounded-full shadow-sm text-indigo-600 font-bold text-[13px] sm:text-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none transition-colors">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shrink-0">
                            <i class="fa fa-user"></i>
                        </div>
                        <span>Viewing: {{ $activeChild->first_name }}</span>
                    </div>
                    <i class="fa fa-chevron-down ml-1 text-xs text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
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

    <!-- NAVIGATION TABS & DATE SELECTOR COMPONENT (Replacing Quarter Tags) -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b-2 border-slate-200 pb-3 mb-6 sm:mb-8">
        
        <!-- Left: Navigation Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar">
            <a href="{{ route('guardian.recommendations') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-bold text-slate-600 hover:text-indigo-600 hover:bg-slate-100 transition-all flex items-center gap-2 shrink-0 active:scale-95">
                <i class="fa-solid fa-sparkles text-amber-500"></i> Today's Active Session
            </a>
            <a href="{{ route('guardian.logs.archive') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold bg-indigo-600 text-white shadow-sm flex items-center gap-2 shrink-0 active:scale-95">
                <i class="fa-solid fa-clock-rotate-left"></i> Previous Reports ({{ $archivedCount }})
            </a>
        </div>

        <!-- Right: Dedicated Date Selector Component -->
        <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto w-full lg:w-auto">
            
            <!-- Step to Older Report -->
            @if(!empty($prevDate))
                <a href="{{ route('guardian.logs.archive', ['date' => $prevDate]) }}" 
                   title="Previous report: {{ \Carbon\Carbon::parse($prevDate)->format('M d, Y') }}"
                   class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95">
                    <i class="fa-solid fa-chevron-left text-[11px] text-slate-500"></i>
                    <span class="hidden sm:inline">Older</span>
                </a>
            @else
                <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-chevron-left text-[11px] text-slate-300"></i>
                    <span class="hidden sm:inline">Older</span>
                </span>
            @endif

            <!-- Date Picker Form -->
            <form method="GET" action="{{ route('guardian.logs.archive') }}" class="flex items-center gap-1.5 m-0">
                <div class="relative flex items-center">
                    <div class="absolute left-3 pointer-events-none text-indigo-600 text-xs">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <input type="date" 
                           name="date" 
                           id="activity_date_selector"
                           value="{{ $selectedDate }}" 
                           max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                           onchange="this.form.submit()" 
                           class="pl-8 pr-2.5 py-1.5 sm:py-2 bg-white border-2 border-indigo-200 hover:border-indigo-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-slate-800 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-colors cursor-pointer"
                           title="Select specific activity date">
                </div>

                @if($availableDates->count() > 1)
                    <!-- Quick Dropdown of Available Dates with Recorded Reports -->
                    <select onchange="if(this.value) window.location.href='{{ route('guardian.logs.archive') }}?date=' + this.value"
                            class="py-1.5 sm:py-2 px-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition-colors cursor-pointer max-w-[130px] sm:max-w-[170px] truncate"
                            title="Jump to recorded date">
                        <option value="" disabled>All Dates...</option>
                        @foreach($availableDates as $d)
                            <option value="{{ $d }}" {{ $selectedDate === $d ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($d)->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </form>

            <!-- Step to Newer Report -->
            @if(!empty($nextDate))
                <a href="{{ route('guardian.logs.archive', ['date' => $nextDate]) }}" 
                   title="Next report: {{ \Carbon\Carbon::parse($nextDate)->format('M d, Y') }}"
                   class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95">
                    <span class="hidden sm:inline">Newer</span>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-500"></i>
                </a>
            @else
                <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center gap-1.5 shadow-xs">
                    <span class="hidden sm:inline">Newer</span>
                    <i class="fa-solid fa-chevron-right text-[11px] text-slate-300"></i>
                </span>
            @endif

        </div>
    </div>

    <!-- SINGLE REPORT DISPLAY FOR SELECTED DATE -->
    @if(isset($activeChild))
        @if($selectedLog)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                
                <!-- CARD HEADER -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200/80 bg-gradient-to-r from-slate-50/80 via-white to-white flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-xs">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <h5 class="font-extrabold text-slate-800 m-0 text-[15px] sm:text-lg leading-tight">
                                Session on {{ \Carbon\Carbon::parse($selectedLog->log_date)->format('F j, Y') }}
                            </h5>
                            <p class="text-[11px] sm:text-xs font-semibold text-slate-500 m-0 mt-0.5">
                                Logged by Teacher {{ $selectedLog->teacher->name ?? 'Staff' }} • {{ \Carbon\Carbon::parse($selectedLog->log_date)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    
                    <span class="self-start sm:self-auto inline-flex items-center px-3 sm:px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] sm:text-xs border border-slate-200 uppercase tracking-wider">
                        {{ $selectedLog->quarter }}
                    </span>
                </div>
                
                <!-- CARD BODY -->
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-6 items-start">
                    @php
                        $photoUrl = null;
                        if ($selectedLog->image_path) {
                            $photoUrl = route('secure.log-photo', $selectedLog->id);
                        } elseif (isset($selectedLog->daily_report) && $selectedLog->daily_report && $selectedLog->daily_report->image_path) {
                            $photoUrl = route('secure.report-photo', $selectedLog->daily_report->id);
                        }

                        // Parse content and extract "Tonight's Suggested Activity" if nested inside report narrative
                        $fullContent = (isset($selectedLog->daily_report) && $selectedLog->daily_report && !empty($selectedLog->daily_report->content)) 
                            ? $selectedLog->daily_report->content 
                            : ($selectedLog->notes ?? '');

                        $dailyReportText = $fullContent;
                        $suggestedActivityText = $selectedLog->home_recommendations ?? '';

                        if (preg_match('/^(.*?)(?:\r?\n)+(?:Tonight\'s Suggested Activity|Tonight\'s Suggested Home Activity|Home Activity):?\s*(.*)$/si', $fullContent, $matches)) {
                            $dailyReportText = trim($matches[1]);
                            if (empty($suggestedActivityText) && !empty($matches[2])) {
                                $suggestedActivityText = trim($matches[2]);
                            }
                        }

                        $formatHtml = function($rawText) {
                            if (empty($rawText)) return '';
                            $escaped = e(trim($rawText));
                            $formatted = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="font-extrabold text-slate-900">$1</strong>', $escaped);
                            
                            $lines = explode("\n", $formatted);
                            $output = [];
                            $inList = false;
                            
                            foreach ($lines as $line) {
                                $trimmed = trim($line);
                                if (preg_match('/^[-*•]\s+(.*)$/', $trimmed, $m)) {
                                    if (!$inList) {
                                        $output[] = '<ul class="list-none pl-0 space-y-2 mt-2 mb-2">';
                                        $inList = true;
                                    }
                                    $output[] = '<li class="flex items-start gap-2.5 text-[13px] sm:text-[14px] leading-relaxed text-slate-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-2 shrink-0"></span><span class="flex-1">' . $m[1] . '</span></li>';
                                } else {
                                    if ($inList) {
                                        $output[] = '</ul>';
                                        $inList = false;
                                    }
                                    if (!empty($trimmed)) {
                                        $output[] = '<p class="m-0 mb-2 leading-relaxed text-[13px] sm:text-[14px] text-slate-700 font-normal">' . $trimmed . '</p>';
                                    }
                                }
                            }
                            if ($inList) {
                                $output[] = '</ul>';
                            }
                            return implode("\n", $output);
                        };
                    @endphp

                    @if($photoUrl)
                        <!-- OBSERVATION PHOTO COLUMN (Responsive for Mobile and PC) -->
                        <div class="md:col-span-5 w-full">
                            <div class="bg-slate-50/90 rounded-2xl border border-slate-200 p-3 sm:p-4 shadow-xs flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2.5 px-0.5">
                                    <span class="text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fa fa-camera text-indigo-500"></i> Session Photo
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline-block">
                                        Click to enlarge
                                    </span>
                                </div>

                                <div class="relative group cursor-pointer w-full overflow-hidden rounded-xl border border-slate-200/90 shadow-xs bg-slate-200 flex items-center justify-center min-h-[200px] max-h-[300px] sm:max-h-[340px]"
                                     @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Session Photo - {{ addslashes($selectedLog->student->first_name) }}'"
                                     title="Click to view full image">
                                    
                                    <img src="{{ $photoUrl }}" 
                                         alt="Session Photo" 
                                         class="w-full h-full max-h-[300px] sm:max-h-[340px] object-cover group-hover:scale-105 transition-transform duration-300 block"
                                         loading="lazy">
                                    
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[1px]">
                                        <i class="fa-solid fa-magnifying-glass-plus text-base"></i> View full size
                                    </div>
                                </div>

                                <button type="button" 
                                        @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Session Photo - {{ addslashes($selectedLog->student->first_name) }}'"
                                        class="w-full mt-3 py-2 px-3 bg-white hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 font-extrabold text-[11px] sm:text-xs rounded-xl border border-indigo-200 shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                    <i class="fa-solid fa-expand text-indigo-500 text-xs"></i> View enlarged photo
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- CONTENT COLUMN (Responsive for Mobile and PC) -->
                    <div class="{{ $photoUrl ? 'md:col-span-7' : 'md:col-span-12' }} w-full flex flex-col gap-4">
                        
                        <!-- 1. DAILY REPORT CARD -->
                        @if(!empty($dailyReportText))
                            <div class="p-5 sm:p-6 bg-white rounded-2xl border border-indigo-100/90 shadow-sm relative overflow-hidden">
                                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-indigo-50">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs shrink-0">
                                            <i class="fa-solid fa-file-lines"></i>
                                        </div>
                                        <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                            Daily Report:
                                        </h6>
                                    </div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200/60">
                                        Archived Log
                                    </span>
                                </div>
                                <div class="m-0 leading-relaxed text-slate-700 font-medium">
                                    {!! $formatHtml($dailyReportText) !!}
                                </div>
                            </div>
                        @endif

                        <!-- 2. HOME ACTIVITY CARD -->
                        @if(!empty($suggestedActivityText))
                            <div class="p-5 sm:p-6 bg-emerald-50/40 rounded-2xl border border-emerald-200/90 shadow-sm relative overflow-hidden">
                                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-emerald-100/80">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs shrink-0">
                                            <i class="fa-solid fa-house-chimney-user"></i>
                                        </div>
                                        <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                            Tonight's Suggested Activity:
                                        </h6>
                                    </div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-md border border-emerald-200">
                                        Home Practice
                                    </span>
                                </div>
                                <div class="m-0 leading-relaxed text-slate-800 font-medium">
                                    {!! $formatHtml($suggestedActivityText) !!}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- CARD COMMENTS -->
                <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-4 sm:pt-5 border-t border-slate-200 bg-slate-50/70">
                    @include('components.comment-thread', ['item' => $selectedLog])
                </div>

            </div>
        @else
            <!-- EMPTY STATE WHEN SELECTED DATE HAS NO REPORT -->
            <div class="w-full text-center py-12 sm:py-16 px-4 sm:px-6 bg-white rounded-2xl shadow-sm border-2 border-dashed border-slate-200">
                <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-4 text-slate-400 text-3xl">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>
                <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-1">
                    No Report Found for {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                </h5>
                <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 max-w-md mx-auto mb-5">
                    There are no daily notes or home activities recorded on this specific date. Pick another date using the selector above or return to the latest available report.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @if($availableDates->isNotEmpty())
                        <a href="{{ route('guardian.logs.archive', ['date' => $availableDates->first()]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition-colors shadow-sm">
                            <i class="fa-solid fa-clock-rotate-left"></i> View Latest Report ({{ \Carbon\Carbon::parse($availableDates->first())->format('M j, Y') }})
                        </a>
                    @endif
                    <a href="{{ route('guardian.recommendations') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl transition-colors">
                        <i class="fa-solid fa-sparkles text-amber-500"></i> Back to Today's Session
                    </a>
                </div>
            </div>
        @endif
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


