@extends('layouts.app')

@section('title', 'Daily Reports & Activities Archive')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]" x-data="{ enlargedPhotoUrl: null, enlargedPhotoTitle: '' }">

    <!-- TOP HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('guardian.recommendations') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Today's Session
                </a>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 leading-tight">Daily Reports & Activities Archive</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Complete historical archive of past daily reports and home activities for <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
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

    <!-- NAVIGATION TABS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b-2 border-slate-200 pb-3 mb-6 sm:mb-8">
        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar">
            <a href="{{ route('guardian.recommendations') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-bold text-slate-600 hover:text-indigo-600 hover:bg-slate-100 transition-all flex items-center gap-2 shrink-0 active:scale-95">
                <i class="fa-solid fa-sparkles text-amber-500"></i> Today's Active Session
            </a>
            <a href="{{ route('guardian.logs.archive') }}" class="px-3.5 sm:px-4 py-2 rounded-xl text-[12px] sm:text-[13px] font-extrabold bg-indigo-600 text-white shadow-sm flex items-center gap-2 shrink-0 active:scale-95">
                <i class="fa-solid fa-box-archive"></i> Archive & History ({{ $archivedLogs->count() }})
            </a>
        </div>

        <!-- Quarter Filter -->
        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 self-start sm:self-auto overflow-x-auto">
            <a href="{{ route('guardian.logs.archive') }}" class="px-3 py-1.5 sm:py-1 text-[11px] font-extrabold rounded-lg transition-colors {{ !request('quarter') ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} active:scale-95">
                All
            </a>
            @foreach(['Q1', 'Q2', 'Q3', 'Q4'] as $q)
                <a href="{{ route('guardian.logs.archive', ['quarter' => $q]) }}" class="px-3 py-1.5 sm:py-1 text-[11px] font-extrabold rounded-lg transition-colors {{ request('quarter') == $q ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} active:scale-95">
                    {{ $q }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- ARCHIVE CONTENT -->
    @if(isset($activeChild))
    <div class="flex flex-col gap-6 sm:gap-8">
        @forelse($archivedLogs as $log)
            <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm border-2 border-slate-300 overflow-hidden">
                
                <!-- CARD HEADER -->
                <div class="px-4 sm:px-6 py-4 border-b-2 border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-md bg-slate-200 text-slate-700 text-[10px] font-extrabold uppercase tracking-wider">
                            <i class="fa-solid fa-box-archive text-slate-500 mr-1"></i> Archived Log
                        </span>
                        <div>
                            <h5 class="font-extrabold text-slate-800 m-0 text-[15px] sm:text-base leading-tight">
                                Session on {{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }}
                            </h5>
                            <p class="text-[11px] font-bold text-slate-400 m-0">Posted {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }} by Teacher {{ $log->teacher->name ?? 'Staff' }}</p>
                        </div>
                    </div>
                    
                    <span class="self-start sm:self-auto inline-flex items-center px-3 py-1 rounded-full bg-white text-slate-700 font-bold text-[10px] sm:text-xs shadow-sm border-2 border-slate-300 uppercase tracking-wide">
                        {{ $log->quarter }}
                    </span>
                </div>
                
                <!-- CARD BODY -->
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6">
                    @php
                        $photoUrl = null;
                        if ($log->image_path) {
                            $photoUrl = route('secure.log-photo', $log->id);
                        } elseif (isset($log->daily_report) && $log->daily_report && $log->daily_report->image_path) {
                            $photoUrl = route('secure.report-photo', $log->daily_report->id);
                        }
                    @endphp

                    @if($photoUrl)
                        <div class="md:col-span-5 text-center">
                            <div class="p-2 sm:p-3 bg-slate-50 rounded-xl border-[2px] border-dashed border-slate-300 h-full flex flex-col items-center justify-center">
                                <div class="relative group cursor-pointer max-w-sm w-full overflow-hidden rounded-lg shadow-sm border border-slate-200"
                                     @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($log->student->first_name) }}'"
                                     title="Click to enlarge photo">
                                    <img src="{{ $photoUrl }}" alt="Session Photo" class="max-h-[220px] sm:max-h-[260px] w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-2">
                                        <i class="fa-solid fa-magnifying-glass-plus text-base"></i> Click to enlarge
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="enlargedPhotoUrl = '{{ $photoUrl }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($log->student->first_name) }}'"
                                        class="text-indigo-600 hover:text-indigo-800 text-[11px] sm:text-xs font-bold mt-2.5 mb-0 flex items-center justify-center gap-1.5 cursor-pointer bg-transparent border-0 p-0">
                                    <i class="fa fa-camera text-indigo-500"></i> View enlarged photo
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="{{ $photoUrl ? 'md:col-span-7' : 'md:col-span-12' }} flex flex-col gap-4">
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
                                    <i class="fa fa-house-user mr-2 text-emerald-500"></i> Home Activity:
                                </h6>
                                <p class="m-0 text-[13px] sm:text-[14px] leading-relaxed text-slate-700 whitespace-pre-wrap font-medium">{{ $log->home_recommendations }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CARD COMMENTS -->
                <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-4 sm:pt-5 border-t-2 border-slate-200 bg-slate-50/50">
                    @include('components.comment-thread', ['item' => $log])
                </div>

            </div>
        @empty
            <div class="w-full text-center py-12 sm:py-16 px-4 sm:px-6 bg-white rounded-[16px] sm:rounded-2xl shadow-sm border-2 border-dashed border-slate-300">
                <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto mb-4 text-slate-300 text-3xl">
                    <i class="fa fa-box-archive"></i>
                </div>
                <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-1">No archived logs found</h5>
                <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 max-w-md mx-auto">When daily logs become older than 24 hours, they will automatically be archived here.</p>
            </div>
        @endforelse
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

