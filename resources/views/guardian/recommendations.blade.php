@extends('layouts.app')

@section('title', 'Daily Reports & Home Recommendations')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]">

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
            <i class="fa-solid fa-box-archive text-slate-400"></i> Archive & History ({{ $archivedCount ?? 0 }})
        </a>
    </div>

    @if(isset($activeChild))
    <div class="flex flex-col gap-6 sm:gap-8">
        
        @forelse($recentRecommendations as $log)
            <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm border-2 border-slate-300 overflow-hidden border-t-4 border-t-emerald-500">
                
                <!-- CARD HEADER -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b-2 border-slate-200 bg-white flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <div>
                        <h5 class="font-extrabold text-slate-800 m-0 text-[15px] sm:text-lg flex items-center mb-1 leading-tight">
                            <i class="fa fa-star text-amber-400 mr-2"></i> {{ $log->student->first_name }}'s Daily Highlight
                        </h5>
                        <p class="text-[11px] sm:text-xs font-bold text-slate-500 m-0">From today's session: {{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }})</p>
                    </div>
                    <span class="self-start sm:self-auto inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] sm:text-xs shadow-sm border-2 border-emerald-200 uppercase tracking-wide">
                        {{ $log->quarter }}
                    </span>
                </div>
                
                <!-- CARD BODY -->
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6">
                    @if($log->image_path)
                        <div class="md:col-span-5 text-center">
                            <div class="p-2 sm:p-3 bg-slate-50 rounded-xl border-[3px] border-dashed border-slate-300 h-full flex flex-col items-center justify-center">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="max-h-[220px] sm:max-h-[280px] w-auto mx-auto rounded-lg shadow-sm border-2 border-slate-300 object-cover">
                                <p class="text-slate-500 text-[11px] sm:text-xs font-bold mt-2.5 sm:mt-3 mb-1 flex items-center">
                                    <i class="fa fa-camera text-slate-400 mr-1.5"></i> Photo from today's session
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="{{ $log->image_path ? 'md:col-span-7' : 'md:col-span-12' }} flex flex-col gap-4">
                        @if(isset($log->daily_report) && $log->daily_report)
                            <div class="p-4 sm:p-5 bg-indigo-50/60 rounded-xl border-2 border-indigo-200">
                                <h6 class="font-extrabold text-indigo-800 mb-2 flex items-center m-0 text-[13px] sm:text-[14px]">
                                    <i class="fa-solid fa-file-lines text-indigo-600 mr-2"></i> Daily Report:
                                </h6>
                                <div class="m-0 text-[13px] sm:text-[14px] leading-relaxed text-slate-700 whitespace-pre-wrap font-medium">{{ $log->daily_report->content }}</div>
                            </div>
                        @elseif($log->notes)
                            <div class="p-4 sm:p-5 bg-slate-50 rounded-xl border border-slate-200">
                                <h6 class="font-extrabold text-[#1E2657] mb-2 flex items-center m-0 text-[13px] sm:text-[14px]">
                                    <i class="fa-solid fa-file-lines text-[#3B82F6] mr-2"></i> Daily Report:
                                </h6>
                                <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed text-slate-700 whitespace-pre-wrap font-medium">{{ $log->notes }}</p>
                            </div>
                        @endif

                        @if($log->home_recommendations)
                            <div class="p-4 sm:p-5 flex flex-col justify-center bg-slate-50 rounded-xl border-y-2 border-r-2 border-l-4 border-slate-300 border-l-emerald-500">
                                <h6 class="font-extrabold text-emerald-600 mb-2 flex items-center m-0 text-[13px] sm:text-[14px]">
                                    <i class="fa fa-house-user mr-2 text-emerald-500"></i> Tonight's Suggested Activity:
                                </h6>
                                <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed text-slate-700 whitespace-pre-wrap font-medium">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CARD COMMENTS -->
                <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-4 sm:pt-5 border-t-2 border-slate-200 bg-slate-50">
                    @include('components.comment-thread', ['item' => $log])
                </div>

            </div>
        @empty
            <div class="w-full text-center py-12 sm:py-16 px-4 sm:px-6 bg-white rounded-[16px] sm:rounded-2xl shadow-sm border-2 border-dashed border-slate-300">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto mb-4 text-emerald-600 text-3xl">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-1">No Active Home Activity For Today</h5>
                <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 max-w-md mx-auto mb-5">When the teacher submits today's session, today's notes, photos, and suggested activities will appear here.</p>
                <a href="{{ route('guardian.logs.archive') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs rounded-xl border border-indigo-200 transition-colors shadow-sm">
                    <i class="fa-solid fa-box-archive"></i> Browse Past Log History & Archive ({{ $archivedCount ?? 0 }})
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

</div>

@endsection