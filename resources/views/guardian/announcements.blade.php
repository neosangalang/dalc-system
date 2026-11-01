@extends('layouts.app')

@section('title', 'All Announcements')

@section('content')
<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-6">

        <!-- HEADER & DATE SELECTOR COMPONENT (Matching Daily Reports) -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b-2 border-slate-200 pb-3 mb-6 sm:mb-8">
            
            <!-- Left: Title & Subtitle -->
            <div class="flex flex-col gap-1">
                <span class="text-[#8B5CF6] text-[11px] font-extrabold tracking-widest uppercase block">Guardian Portal</span>
                <h1 class="text-[#1E2657] text-2xl sm:text-3xl font-extrabold tracking-tight">Center Announcements</h1>
                <p class="text-[#6B7086] text-xs sm:text-sm font-medium m-0">Stay updated with the latest news and alerts from the center.</p>
            </div>

            <!-- Right: Dedicated Date Selector Component -->
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto w-full lg:w-auto">
                
                <!-- Step to Older Announcement -->
                @if(!empty($prevDate))
                    <a href="{{ route('guardian.announcements.index', ['date' => $prevDate]) }}" 
                       title="Previous announcement: {{ \Carbon\Carbon::parse($prevDate)->format('M d, Y') }}"
                       class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95">
                        <i class="fa-solid fa-chevron-left text-[11px] text-slate-500"></i>
                        <span class="hidden sm:inline">Previous</span>
                    </a>
                @else
                    <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-chevron-left text-[11px] text-slate-300"></i>
                        <span class="hidden sm:inline">Previous</span>
                    </span>
                @endif

                <!-- Date Picker Form -->
                <form method="GET" action="{{ route('guardian.announcements.index') }}" class="flex items-center gap-1.5 m-0">
                    <div class="relative flex items-center">
                        <div class="absolute left-3 pointer-events-none text-indigo-600 text-xs">
                            <i class="fa-regular fa-calendar-days"></i>
                        </div>
                        <input type="date" 
                               name="date" 
                               id="announcement_date_selector"
                               value="{{ $selectedDate !== 'all' ? $selectedDate : '' }}" 
                               onchange="this.form.submit()" 
                               class="pl-8 pr-2.5 py-1.5 sm:py-2 bg-white border-2 border-indigo-200 hover:border-indigo-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-slate-800 text-xs sm:text-sm font-bold rounded-xl shadow-xs transition-colors cursor-pointer"
                               title="Select specific announcement date">
                    </div>

                    @if($availableDates->count() > 1)
                        <!-- Quick Dropdown of Available Dates -->
                        <select onchange="if(this.value) window.location.href='{{ route('guardian.announcements.index') }}?date=' + this.value"
                                class="py-1.5 sm:py-2 px-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition-colors cursor-pointer max-w-[130px] sm:max-w-[170px] truncate"
                                title="Jump to announcement date">
                            <option value="" disabled>All Dates...</option>
                            @foreach($availableDates as $d)
                                <option value="{{ $d }}" {{ $selectedDate === $d ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($d)->format('M d, Y') }}
                                </option>
                            @endforeach
                            <option value="all" {{ $selectedDate === 'all' ? 'selected' : '' }}>All Dates ({{ $allAnnouncements->count() }})</option>
                        </select>
                    @endif
                </form>

                <!-- Step to Newer Announcement -->
                @if(!empty($nextDate))
                    <a href="{{ route('guardian.announcements.index', ['date' => $nextDate]) }}" 
                       title="Next announcement: {{ \Carbon\Carbon::parse($nextDate)->format('M d, Y') }}"
                       class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 hover:border-slate-400 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-xs active:scale-95">
                        <span class="hidden sm:inline">Next</span>
                        <i class="fa-solid fa-chevron-right text-[11px] text-slate-500"></i>
                    </a>
                @else
                    <span class="px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 text-xs font-bold cursor-not-allowed flex items-center gap-1.5 shadow-xs">
                        <span class="hidden sm:inline">Next</span>
                        <i class="fa-solid fa-chevron-right text-[11px] text-slate-300"></i>
                    </span>
                @endif

            </div>
        </div>

        <!-- SPECIFIED DATE ANNOUNCEMENTS DISPLAY -->
        <section>
            <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-4 sm:p-6 md:p-8 flex flex-col gap-6">
                
                @forelse($announcements as $announcement)
                    <div id="announcement-{{ $announcement->id }}" class="flex flex-col gap-2">
                        <!-- Announcement Header -->
                        <div class="flex items-center gap-3 sm:gap-4 mb-1">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] text-white shadow-sm flex items-center justify-center text-[14px] font-bold shrink-0">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h4 class="text-[14px] sm:text-[15px] font-extrabold text-[#1E2657] truncate sm:whitespace-normal">{{ $announcement->title }}</h4>
                                <span class="text-[10px] sm:text-[11px] font-medium text-[#9CA3AF]">
                                    <i class="fa-regular fa-clock mr-1"></i> Posted {{ \Carbon\Carbon::parse($announcement->created_at)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        <!-- Content Bubble (Full width on mobile, indented on tablet/desktop) -->
                        <div class="bg-[#F4F6FA] border border-black/5 rounded-2xl sm:rounded-tl-sm p-4 sm:p-5 mt-1 sm:mt-2 ml-0 sm:ml-[56px]">
                            
                            <!-- Dynamic Category Badge -->
                            @php
                                $badgeIcon = match($announcement->type ?? 'Center Update') {
                                    'Class Suspension' => 'fa-triangle-exclamation',
                                    'Holiday'          => 'fa-calendar-star',
                                    'Event'            => 'fa-calendar-check',
                                    default            => 'fa-bullhorn',
                                };
                            @endphp
                            <span class="inline-flex items-center bg-[#1E2657] text-white px-2.5 py-1 rounded-md text-[10px] font-black tracking-widest uppercase mb-4 shadow-sm">
                                <i class="fa-solid {{ $badgeIcon }} mr-1.5 text-[#FFD700]"></i> {{ $announcement->type ?? 'Center Update' }}
                            </span>

                            <!-- Attached Image (Clean, uncropped container with click-to-view) -->
                            @if($announcement->image_path)
                                <div class="mb-5 rounded-2xl overflow-hidden bg-slate-900/5 border border-black/10 p-2 sm:p-3 flex items-center justify-center">
                                    <a href="{{ asset('storage/' . $announcement->image_path) }}" target="_blank" class="block group relative overflow-hidden rounded-xl w-full text-center">
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" class="max-h-[520px] w-auto max-w-full object-contain mx-auto rounded-lg shadow-sm transition-transform duration-300 group-hover:scale-[1.01]">
                                        <div class="absolute bottom-3 right-3 bg-black/75 hover:bg-black/90 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg backdrop-blur-sm shadow flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition">
                                            <i class="fa-solid fa-expand text-xs"></i> View Full Size
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <p class="text-[13px] sm:text-[14px] font-medium text-[#4B5563] leading-relaxed m-0 whitespace-pre-line">{{ $announcement->content }}</p>

                            <!-- FACEBOOK-STYLE COMMENTS -->
                            @include('partials.announcement-comments', ['announcement' => $announcement])
                        </div>
                    </div>

                    <!-- Divider -->
                    @if (!$loop->last)
                        <div class="w-full h-px bg-black/5 my-4 ml-0 sm:ml-[56px] w-full sm:w-[calc(100%-56px)]"></div>
                    @endif

                @empty
                    <!-- Empty State (Matching Daily Reports Archive) -->
                    <div class="bg-white rounded-2xl p-8 sm:p-12 text-center text-slate-500">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
                            <i class="fa-regular fa-calendar-xmark"></i>
                        </div>
                        @if($selectedDate !== 'all')
                            <h4 class="text-base sm:text-lg font-bold text-slate-700 mb-1">
                                No Announcement Found for {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                            </h4>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">
                                There are no announcements posted on this specific date. Pick another date using the selector above or return to the latest announcement.
                            </p>
                            @if($availableDates->isNotEmpty())
                                <a href="{{ route('guardian.announcements.index', ['date' => $availableDates->first()]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition-colors shadow-sm">
                                    <i class="fa-solid fa-clock-rotate-left"></i> View Latest Announcement ({{ \Carbon\Carbon::parse($availableDates->first())->format('M j, Y') }})
                                </a>
                            @endif
                        @else
                            <h4 class="text-base sm:text-lg font-bold text-slate-700 mb-1">No Announcements Yet</h4>
                            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">We'll let you know when the center posts an update.</p>
                        @endif
                    </div>
                @endforelse

            </div>
        </section>

    </div>
</div>
@endsection
