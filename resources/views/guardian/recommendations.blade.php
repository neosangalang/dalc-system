@extends('layouts.app')

@section('title', 'Daily Reports & Home Recommendations')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]" x-data="{ enlargedPhotoUrl: null, enlargedPhotoTitle: '' }">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mb-1 m-0 leading-tight">Daily Reports & Home Activities</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Dynamic recommendations and daily highlights based on <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}'s</strong> progress.</p>
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

    <!-- NAVIGATION TABS -->
    <div class="flex items-center gap-2 border-b-2 border-slate-200 pb-3 mb-6 sm:mb-8 overflow-x-auto custom-scrollbar">
        <a href="{{ route('guardian.recommendations') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold bg-emerald-600 text-white shadow-sm flex items-center gap-2 shrink-0 active:scale-95">
            <i class="fa-solid fa-sparkles text-amber-300"></i> Today's Active Session
        </a>
        <a href="{{ route('guardian.logs.archive') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-bold text-slate-600 hover:text-indigo-600 hover:bg-slate-100 transition-all flex items-center gap-2 shrink-0 active:scale-95">
            <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Previous Reports ({{ $archivedCount ?? 0 }})
        </a>
    </div>

    @if(isset($activeChild))
    <div class="flex flex-col gap-6 sm:gap-8">
        
        @forelse($recentRecommendations as $log)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                
                <!-- CARD HEADER -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200/80 bg-gradient-to-r from-slate-50/80 via-white to-white flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <div>
                        <h5 class="font-extrabold text-slate-800 m-0 text-[15px] sm:text-lg flex items-center mb-1 leading-tight">
                            <i class="fa fa-star text-amber-400 mr-2"></i> {{ $log->student->first_name }}'s Daily Highlight
                        </h5>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 m-0 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md font-bold text-[10px]">
                                <i class="fa-solid fa-circle-check text-[9px]"></i> Active Today
                            </span>
                            <span>{{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }}</span>
                        </p>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center px-3 sm:px-4 py-1.5 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[10px] sm:text-xs border border-slate-200 uppercase tracking-wider">
                        {{ $log->quarter }}
                    </span>
                </div>
                
                <!-- CARD BODY -->
                <div class="p-4 sm:p-6 flex flex-col gap-5 sm:gap-6">
                    @php
                        $photoUrl = null;
                        if ($log->image_path) {
                            $photoUrl = route('secure.log-photo', $log->id);
                        } elseif (isset($log->daily_report) && $log->daily_report && $log->daily_report->image_path) {
                            $photoUrl = route('secure.report-photo', $log->daily_report->id);
                        }

                        // Parse content and extract "Tonight's Suggested Activity" if nested inside report narrative
                        $fullContent = (isset($log->daily_report) && $log->daily_report && !empty($log->daily_report->content)) 
                            ? $log->daily_report->content 
                            : ($log->notes ?? '');

                        $dailyReportText = $fullContent;
                        $suggestedActivityText = $log->home_recommendations ?? '';

                        // Check if narrative embeds "Tonight's Suggested Activity"
                        if (preg_match('/^(.*?)(?:\r?\n)+(?:Tonight\'s Suggested Activity|Tonight\'s Suggested Home Activity):?\s*(.*)$/si', $fullContent, $matches)) {
                            $dailyReportText = trim($matches[1]);
                            if (empty($suggestedActivityText) && !empty($matches[2])) {
                                $suggestedActivityText = trim($matches[2]);
                            }
                        }

                        // Format markdown bold (**word**) and bullet lists cleanly
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

                    <!-- TOP SECTION: DAILY REPORT (LEFT) + OBSERVATION PHOTO (RIGHT) -->
                    @if($photoUrl)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-6 items-stretch">
                            
                            <!-- LEFT: DAILY REPORT CARD (Dark Blue / Indigo Accent) -->
                            @if(!empty($dailyReportText))
                                <div class="md:col-span-7 lg:col-span-8 flex flex-col">
                                    <div class="p-5 sm:p-6 bg-white rounded-2xl border-2 border-indigo-200/90 shadow-sm relative overflow-hidden h-full flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-indigo-100">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs shrink-0">
                                                        <i class="fa-solid fa-file-lines"></i>
                                                    </div>
                                                    <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                                        Daily Report:
                                                    </h6>
                                                </div>
                                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200/70">
                                                    Session Log
                                                </span>
                                            </div>
                                            <div class="m-0 leading-relaxed text-slate-700 font-medium">
                                                {!! $formatHtml($dailyReportText) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- RIGHT: OBSERVATION PHOTO (Light Blue Accent) -->
                            <div class="{{ !empty($dailyReportText) ? 'md:col-span-5 lg:col-span-4' : 'md:col-span-12' }} flex flex-col">
                                <div class="bg-slate-50/90 rounded-2xl border-2 border-indigo-200/80 p-3 sm:p-4 shadow-sm flex flex-col justify-between h-full">
                                    <div>
                                        <div class="flex items-center justify-between mb-2.5 px-0.5">
                                            <span class="text-[11px] font-extrabold text-indigo-700 uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fa fa-camera text-indigo-500"></i> Observation Photo
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline-block">
                                                Click to enlarge
                                            </span>
                                        </div>

                                        <div class="relative group cursor-pointer w-full overflow-hidden rounded-xl border border-slate-300 shadow-xs bg-slate-200 flex items-center justify-center min-h-[180px] max-h-[260px] sm:max-h-[300px]"
                                             @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($log->student->first_name) }}'"
                                             title="Click to view full image">
                                            
                                            <img src="{{ $photoUrl }}" 
                                                 alt="Activity Photo" 
                                                 class="w-full h-full max-h-[260px] sm:max-h-[300px] object-cover group-hover:scale-105 transition-transform duration-300 block"
                                                 loading="lazy">
                                            
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2 backdrop-blur-[1px]">
                                                <i class="fa-solid fa-magnifying-glass-plus text-base"></i> View full size
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" 
                                            @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($log->student->first_name) }}'"
                                            class="w-full mt-3 py-2 px-3 bg-white hover:bg-indigo-50 text-indigo-600 hover:text-indigo-800 font-extrabold text-[11px] sm:text-xs rounded-xl border border-indigo-200 shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                                        <i class="fa-solid fa-expand text-indigo-500 text-xs"></i> View enlarged photo
                                    </button>
                                </div>
                            </div>

                        </div>
                    @else
                        <!-- NO PHOTO: DAILY REPORT SPANS FULL WIDTH -->
                        @if(!empty($dailyReportText))
                            <div class="w-full">
                                <div class="p-5 sm:p-6 bg-white rounded-2xl border-2 border-indigo-200/90 shadow-sm relative overflow-hidden">
                                    <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-indigo-100">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shadow-xs shrink-0">
                                                <i class="fa-solid fa-file-lines"></i>
                                            </div>
                                            <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                                Daily Report:
                                            </h6>
                                        </div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200/70">
                                            Session Log
                                        </span>
                                    </div>
                                    <div class="m-0 leading-relaxed text-slate-700 font-medium">
                                        {!! $formatHtml($dailyReportText) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- BOTTOM SECTION: TONIGHT'S SUGGESTED ACTIVITY (SPANS FULL WIDTH, Green Accent) -->
                    @if(!empty($suggestedActivityText))
                        <div class="w-full">
                            <div class="p-5 sm:p-6 bg-emerald-50/40 rounded-2xl border-2 border-emerald-300/80 shadow-sm relative overflow-hidden">
                                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-emerald-100/90">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs shrink-0">
                                            <i class="fa-solid fa-house-chimney-user"></i>
                                        </div>
                                        <h6 class="font-extrabold text-slate-800 m-0 text-[14px] sm:text-[15px]">
                                            Tonight's Suggested Activity:
                                        </h6>
                                    </div>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-100/70 px-2.5 py-1 rounded-md border border-emerald-300">
                                        Home Recommendation
                                    </span>
                                </div>
                                <div class="m-0 leading-relaxed text-slate-800 font-medium">
                                    {!! $formatHtml($suggestedActivityText) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- CARD COMMENTS -->
                <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-4 sm:pt-5 border-t border-slate-200 bg-slate-50/70">
                    @include('components.comment-thread', ['item' => $log])
                </div>

            </div>
        @empty
            <div class="w-full text-center py-12 sm:py-16 px-4 sm:px-6 bg-white rounded-2xl shadow-sm border-2 border-dashed border-slate-200">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-4 text-emerald-600 text-3xl">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-1">No Active Home Activity For Today</h5>
                <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 max-w-md mx-auto mb-5">When the teacher submits today's session, today's notes, photos, and suggested activities will appear here.</p>
                <a href="{{ route('guardian.logs.archive') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs rounded-xl border border-indigo-200 transition-colors shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i> Browse Previous Reports ({{ $archivedCount ?? 0 }})
                </a>
            </div>
        @endforelse

    </div>
    @else
        <div class="bg-sky-50 border-2 border-sky-300 text-sky-800 px-4 sm:px-5 py-3 sm:py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa-info-circle text-sky-600 text-lg sm:text-xl shrink-0"></i> 
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