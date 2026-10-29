@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

@php
    $myChildren = Auth::user()->children ?? collect();
    $activeChildId = session('active_child_id', $myChildren->first()->id ?? null);
    $activeChild = $myChildren->where('id', $activeChildId)->first();
@endphp

<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-8 md:space-y-10">

        <!-- WELCOME HERO BANNER (SCOPED BLUE DESIGN) & PILL STUDENT SELECTOR -->
        <header class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5] p-6 sm:p-8 text-white shadow-lg border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-5 mb-8 sm:mb-10">
            <!-- Ambient Decorative Background Glows -->
            <div class="absolute -right-10 -top-10 w-60 h-60 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col gap-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-blue-100 text-[11px] font-extrabold tracking-widest uppercase mb-2 self-start">Guardian Portal</span>
                <h1 class="text-white text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight mb-1 sm:mb-2">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h1>
                <p class="text-blue-100/85 text-[13px] sm:text-sm font-medium leading-relaxed m-0">Here's how {{ $activeChild->first_name ?? 'your child' }} is doing at the learning center today.</p>
            </div>

            <!-- Sleek Pill Student Selector -->
            @if($myChildren->count() > 0 && $activeChild)
                <div x-data="{ switchOpen: false }" class="relative z-40 self-start md:self-auto shrink-0 w-full md:w-auto">
                    <button @click="switchOpen = !switchOpen" @click.away="switchOpen = false" 
                            class="flex items-center justify-between md:justify-start gap-2.5 px-4 sm:px-5 py-2.5 w-full md:w-auto bg-white/95 backdrop-blur-md border border-white/30 rounded-full hover:bg-white hover:shadow-md transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] active:scale-95 shadow-sm cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-[#1E2657] text-white rounded-full flex items-center justify-center w-[24px] h-[24px] text-[10px] shadow-sm">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="text-[13px] sm:text-[14px] font-extrabold text-[#1E2657]">Viewing: {{ $activeChild->first_name }}</span>
                        </div>
                        <i class="fa-solid fa-angle-down text-[#1E2657] text-[12px] ml-1 transition-transform duration-300" :class="{'rotate-180': switchOpen}"></i>
                    </button>
                    
                    <!-- Dropdown Options -->
                    <div x-show="switchOpen" x-transition x-cloak class="absolute left-0 md:left-auto md:right-0 mt-2 w-full md:w-56 bg-white border border-black/10 rounded-xl shadow-[0_8px_24px_-8px_rgba(0,0,0,0.15)] pb-1 pt-1 z-50 overflow-hidden text-left">
                        <div class="px-4 py-3 border-b border-black/5">
                            <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest">My Children</span>
                        </div>
                        
                        <div class="flex flex-col">
                            @foreach($myChildren as $child)
                                @if($activeChild->id === $child->id)
                                    <!-- Active Student State -->
                                    <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-3 bg-[#6366F1] text-white text-[13px] font-extrabold cursor-default pointer-events-none">
                                        <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                        <i class="fa-solid fa-check text-[13px]"></i>
                                    </a>
                                @else
                                    <!-- Inactive Student State -->
                                    <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-3 text-[#6B7086] text-[13px] font-extrabold hover:bg-slate-50 hover:text-[#1E2657] transition-colors">
                                        <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </header>

        <!-- ALERTS -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
                <div class="flex items-center">
                    <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                    <span class="font-extrabold text-sm">{{ session('success') }}</span>
                </div>
                <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                    <i class="fa fa-times text-lg"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-rose-50 border border-rose-200 text-rose-800 px-4 sm:px-5 py-3.5 sm:py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
                <div class="flex items-center">
                    <i class="fa fa-exclamation-triangle mr-3 text-rose-500 text-lg"></i>
                    <span class="font-extrabold text-sm">{{ session('error') }}</span>
                </div>
                <button @click="show = false" type="button" class="text-rose-500 hover:text-rose-700 transition-colors">
                    <i class="fa fa-times text-lg"></i>
                </button>
            </div>
        @endif

        <!-- 2. STAT CARDS (4 Columns on Desktop, 2 Columns on Mobile) -->
        <section>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 w-full">
                
                <!-- Stat Card 1: Active IEP Goals (Purple) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#8B5CF6] leading-none mb-1.5 sm:mb-2 counter">{{ isset($activeGoals) ? $activeGoals->count() : 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10 truncate">Active IEP Goals</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 2: Today's Logs (Green) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#10B981] leading-none mb-1.5 sm:mb-2 counter">{{ isset($dailyReports) ? $dailyReports->count() : 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10 truncate">Today's Logs</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 3: Reports on File (Blue) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#3B82F6] leading-none mb-1.5 sm:mb-2 counter">{{ isset($recentReports) ? $recentReports->count() : 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10 truncate">Reports on File</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 4: Announcements (Amber) -->
                <a href="{{ route('guardian.announcements.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)] hover:-translate-y-1 transition-all duration-300 p-4 sm:p-6 flex flex-col justify-center relative overflow-hidden h-[110px] sm:h-[120px] block active:scale-[0.98]">
                    @if(isset($newAnnouncementsCount) && $newAnnouncementsCount > 0)
                        <div class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-red-500 text-white text-[10px] sm:text-[11px] font-extrabold px-1.5 sm:px-2 py-0.5 rounded-full shadow-sm flex items-center justify-center min-w-[20px] sm:min-w-[24px] z-20">
                            {{ $newAnnouncementsCount }}
                        </div>
                    @endif
                    <h3 class="font-extrabold text-[24px] sm:text-[32px] text-[#F59E0B] leading-none mb-1.5 sm:mb-2">
                        <i class="fa-solid fa-bullhorn text-[22px] sm:text-[28px]"></i>
                    </h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10 truncate">Announcements</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F59E0B] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

            </div>
        </section>

        <!-- 3. SYSTEM MODULES -->
        <section x-data="{ unreadAnnouncements: {{ $newAnnouncementsCount ?? 0 }} }">
            <div class="flex items-center gap-4 mb-4 sm:mb-6">
                <span class="h-px w-8 bg-[#5B8DEF]/50"></span>
                <h2 class="text-[11px] font-extrabold text-[#5B8DEF] uppercase tracking-widest">System Modules</h2>
                <span class="h-px flex-1 bg-black/10"></span>
            </div>

            <!-- GUARDIAN MODULES (2 cols on mobile, 4 on lg) -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 w-full">
                
                <!-- IEP Goals -->
                <a href="{{ route('guardian.goals') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    @if(isset($unreadIepCommentsCount) && $unreadIepCommentsCount > 0)
                        <span class="absolute top-3 sm:top-4 right-3 sm:right-4 flex h-3 w-3 z-20" title="{{ $unreadIepCommentsCount }} Unread Replies">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                    @endif
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10 shrink-0">
                        <i class="fa-solid fa-bullseye text-[#8B5CF6] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">IEP Goals</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0 truncate">Track Progress</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- Reports -->
                <a href="{{ route('guardian.reports') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#3B82F6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#3B82F6] relative z-10 shrink-0">
                        <i class="fa-solid fa-file-lines text-[#3B82F6] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#3B82F6]">Reports</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0 truncate">Evaluations</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- Daily Activity -->
                <a href="{{ route('guardian.recommendations') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#10B981]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#10B981] relative z-10 shrink-0">
                        <i class="fa-solid fa-book-open-reader text-[#10B981] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#10B981]">Daily Activity</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0 truncate">Class Notes</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- Announcements -->
                <a href="{{ route('guardian.announcements.index') }}" 
                   @click="unreadAnnouncements = 0; fetch('{{ route('guardian.announcements.mark-seen') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })"
                   class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F97316]/30 hover:shadow-[0_8px_20px_-6px_rgba(249,115,22,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div x-show="unreadAnnouncements > 0" class="absolute top-3 sm:top-4 right-3 sm:right-4 flex h-3 w-3 z-20">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                    </div>
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#F97316]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#F97316] relative z-10 shrink-0">
                        <i class="fa-solid fa-bullhorn text-[#F97316] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#F97316]">Announcements</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0 truncate">Center Alerts</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F97316] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

            </div>
        </section>

        <!-- FEATURED ANNOUNCEMENT SHOWCASE BANNER -->
        @if(isset($featuredAnnouncements) && $featuredAnnouncements->count() > 0)
        <section 
            x-data="{ 
                activeSlide: 0, 
                slides: {{ $featuredAnnouncements->count() }},
                scrollTo(index) {
                    this.activeSlide = index;
                    this.$refs.slider.children[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                },
                updateActive() {
                    const scrollLeft = this.$refs.slider.scrollLeft;
                    const width = this.$refs.slider.clientWidth;
                    this.activeSlide = Math.round(scrollLeft / width);
                }
            }" 
            class="w-full relative rounded-2xl md:rounded-[2rem] overflow-hidden shadow-sm border border-black/10 bg-white group"
        >
            <!-- Slider Container -->
            <div 
                x-ref="slider"
                @scroll.passive="updateActive"
                class="flex h-full w-full overflow-x-auto snap-x snap-mandatory scroll-smooth hide-scrollbar touch-pan-x"
                style="-ms-overflow-style: none; scrollbar-width: none;"
            >
                @foreach($featuredAnnouncements as $announcement)
                @php
                    $type = $announcement->type ?? 'Center Update';
                    $badgeConfig = match($type) {
                        'Class Suspension' => [
                            'icon' => 'fa-triangle-exclamation',
                            'badge_class' => 'bg-amber-500/15 text-amber-800 border-amber-300',
                            'card_bg' => 'bg-gradient-to-br from-amber-500/5 via-white to-orange-500/5',
                            'placeholder_gradient' => 'from-[#9A3412] to-[#C2410C]',
                        ],
                        'Holiday' => [
                            'icon' => 'fa-calendar-star',
                            'badge_class' => 'bg-indigo-500/15 text-indigo-800 border-indigo-300',
                            'card_bg' => 'bg-gradient-to-br from-indigo-500/5 via-white to-purple-500/5',
                            'placeholder_gradient' => 'from-[#3730A3] to-[#581C87]',
                        ],
                        'Event' => [
                            'icon' => 'fa-calendar-check',
                            'badge_class' => 'bg-emerald-500/15 text-emerald-800 border-emerald-300',
                            'card_bg' => 'bg-gradient-to-br from-emerald-500/5 via-white to-teal-500/5',
                            'placeholder_gradient' => 'from-[#065F46] to-[#047857]',
                        ],
                        default => [
                            'icon' => 'fa-bullhorn',
                            'badge_class' => 'bg-[#1E2657]/10 text-[#1E2657] border-[#1E2657]/20',
                            'card_bg' => 'bg-gradient-to-br from-[#1E2657]/5 via-white to-blue-500/5',
                            'placeholder_gradient' => 'from-[#1E2657] to-[#111827]',
                        ],
                    };
                @endphp

                <div class="w-full h-full flex-shrink-0 snap-center relative flex flex-col md:flex-row items-stretch {{ $badgeConfig['card_bg'] }}">
                    
                    <!-- LEFT COLUMN: Content & Details -->
                    <div class="flex-1 p-6 sm:p-8 flex flex-col justify-between gap-4 z-10">
                        <div>
                            <!-- Header: Category & Date -->
                            <div class="flex flex-wrap items-center gap-2.5 mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border shadow-xs {{ $badgeConfig['badge_class'] }}">
                                    <i class="fa-solid {{ $badgeConfig['icon'] }} mr-1.5 text-xs"></i> {{ $type }}
                                </span>
                                <span class="text-xs font-semibold text-[#8B93A7] flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock"></i> 
                                    {{ \Carbon\Carbon::parse($announcement->created_at)->format('M j, Y') }} 
                                    <span class="opacity-70">({{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }})</span>
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 class="text-2xl sm:text-3xl font-black text-[#1E2657] tracking-tight leading-snug mb-3">
                                {{ $announcement->title }}
                            </h2>

                            <!-- Content preview -->
                            <p class="text-slate-600 text-[13px] sm:text-[14px] font-medium leading-relaxed line-clamp-3 md:line-clamp-4 m-0 whitespace-pre-line">
                                {{ $announcement->content }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="{{ route('guardian.announcements.index') }}" 
                               @click="unreadAnnouncements = 0; fetch('{{ route('guardian.announcements.mark-seen') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#1E2657] hover:bg-[#2A3575] text-white text-[13px] font-extrabold rounded-xl shadow-md transition-all duration-200 transform hover:-translate-y-0.5">
                                <span>View Details & Comments</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>

                            @if($announcement->image_path)
                            <button type="button" 
                                    @click="$dispatch('open-image-preview', { src: '{{ asset('storage/' . $announcement->image_path) }}', title: '{{ addslashes($announcement->title) }}' })"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white hover:bg-slate-100 text-[#1E2657] text-[13px] font-bold rounded-xl border border-slate-200 shadow-xs transition">
                                <i class="fa-solid fa-expand text-xs text-[#8B5CF6]"></i>
                                <span>Enlarge Picture</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Picture Showcase Frame -->
                    <div class="w-full md:w-[42%] lg:w-[40%] bg-slate-50/80 border-t md:border-t-0 md:border-l border-slate-200/80 p-4 sm:p-6 flex items-center justify-center relative overflow-hidden shrink-0">
                        
                        @if($announcement->image_path)
                            <!-- Real Announcement Image / Flyer Frame (Uncropped, clear, beautifully presented) -->
                            <div 
                                @click="$dispatch('open-image-preview', { src: '{{ asset('storage/' . $announcement->image_path) }}', title: '{{ addslashes($announcement->title) }}' })"
                                class="w-full h-full min-h-[200px] max-h-[280px] bg-white rounded-2xl border border-slate-200/90 shadow-sm p-2 flex items-center justify-center relative group/img cursor-pointer overflow-hidden"
                            >
                                <img 
                                    src="{{ asset('storage/' . $announcement->image_path) }}" 
                                    alt="{{ $announcement->title }}" 
                                    class="max-h-[240px] sm:max-h-[260px] w-auto max-w-full object-contain mx-auto rounded-lg transition-transform duration-300 group-hover/img:scale-[1.02]"
                                >
                                
                                <!-- Hover prompt -->
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center rounded-2xl backdrop-blur-[2px]">
                                    <span class="bg-white/95 text-[#1E2657] text-xs font-black px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-expand text-xs"></i> Click to enlarge
                                    </span>
                                </div>
                            </div>
                        @else
                            <!-- Appropriate Center Emblem / Announcement Card Graphic (NO awkward blueprint grid!) -->
                            <div class="w-full h-full min-h-[200px] max-h-[280px] rounded-2xl bg-gradient-to-br {{ $badgeConfig['placeholder_gradient'] }} p-6 flex flex-col justify-between text-white relative overflow-hidden shadow-inner">
                                <!-- Background Watermark -->
                                <div class="absolute -right-4 -bottom-4 text-white/10 text-9xl pointer-events-none select-none">
                                    <i class="fa-solid {{ $badgeConfig['icon'] }}"></i>
                                </div>

                                <!-- Center Badge Top -->
                                <div class="relative z-10 flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-black shadow-xs">
                                        DA
                                    </div>
                                    <span class="text-[11px] font-bold tracking-wider uppercase text-white/90">
                                        Dream Achievers PH
                                    </span>
                                </div>

                                <!-- Center Icon & Type -->
                                <div class="relative z-10 text-center my-auto py-3">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white text-2xl shadow-md mb-2">
                                        <i class="fa-solid {{ $badgeConfig['icon'] }}"></i>
                                    </div>
                                    <p class="text-xs font-black uppercase tracking-widest text-white/95 m-0">
                                        Official Center Notice
                                    </p>
                                </div>

                                <!-- Footer Tag -->
                                <div class="relative z-10 flex items-center justify-between text-[11px] text-white/75 font-semibold border-t border-white/20 pt-2.5">
                                    <span>Center Advisory</span>
                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
                @endforeach
            </div>

            <!-- Slide Pagination Controls -->
            @if($featuredAnnouncements->count() > 1)
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20 pointer-events-auto bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20">
                <template x-for="i in slides" :key="i">
                    <button 
                        @click="scrollTo(i - 1)" 
                        :class="activeSlide === (i - 1) ? 'w-6 bg-white shadow-xs' : 'w-2 bg-white/50 hover:bg-white/80'" 
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                        aria-label="Go to slide"
                    ></button>
                </template>
            </div>

            <!-- Left / Right Chevron Arrows -->
            <button 
                @click="scrollTo(activeSlide > 0 ? activeSlide - 1 : slides - 1)" 
                class="absolute left-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#1E2657] shadow-md border border-slate-200 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                aria-label="Previous slide"
            >
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button 
                @click="scrollTo(activeSlide < slides - 1 ? activeSlide + 1 : 0)" 
                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-[#1E2657] shadow-md border border-slate-200 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20"
                aria-label="Next slide"
            >
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
            @endif

        </section>

        <style>
            .hide-scrollbar::-webkit-scrollbar { display: none; }
        </style>
        @endif

        <!-- MAIN CONTENT: PROGRESS & UPDATES (2-COLUMN UNIFIED LAYOUT) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 mt-4 items-start">
            
            <!-- COLUMN 1: IEP Goals Section (lg:col-span-5) -->
            <section class="lg:col-span-5 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 md:w-8 md:h-8 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6]"><i class="fa-solid fa-chart-line text-sm md:text-xs"></i></div>
                        <h2 class="text-[17px] md:text-[15px] font-extrabold text-[#1E2657]">{{ $activeChild->first_name ?? 'Child' }}'s Current IEP Goals</h2>
                    </div>
                    @if(isset($activeGoals) && $activeGoals->count() > 0)
                        <a href="{{ route('guardian.goals') }}" class="text-[12px] font-extrabold text-[#8B5CF6] hover:underline">View All</a>
                    @endif
                </div>

                <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col gap-6">
                    @forelse($activeGoals ?? [] as $goal)
                        <div class="flex flex-col gap-3">
                            <span class="inline-block bg-[#8B5CF6]/10 text-[#8B5CF6] text-[10px] md:text-[9px] font-extrabold uppercase tracking-widest px-3 py-1.5 rounded-md w-max">
                                {{ $goal->domain ?? 'Development Area' }}
                            </span>
                            
                            <div class="flex justify-between items-end mt-1">
                                <h3 class="text-[15px] md:text-[14px] font-extrabold text-[#1E2657] line-clamp-2 pr-4">{{ $goal->goal_description }}</h3>
                                <span class="text-[17px] md:text-[16px] font-extrabold text-[#8B5CF6] shrink-0">{{ $goal->progress_percentage ?? 0 }}%</span>
                            </div>
                            
                            <div class="h-[10px] md:h-[8px] w-full bg-[#F4F6FA] rounded-full overflow-hidden">
                                <div class="h-full bg-[#8B5CF6] rounded-full transition-all duration-1000 ease-out" style="width: {{ $goal->progress_percentage ?? 0 }}%;"></div>
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div class="w-full h-px bg-black/5 my-2"></div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-[#9CA0AF] flex flex-col items-center">
                            <div class="w-14 h-14 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-[22px] mb-4"><i class="fa-solid fa-star text-[#F59E0B]"></i></div>
                            <h4 class="text-[16px] md:text-[14px] font-extrabold text-[#1E2657]">Ready for New Challenges!</h4>
                            <p class="text-[14px] md:text-[12px] font-medium mt-2">The teachers are preparing the next set of goals.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- COLUMN 2: Today's Daily Log & Home Recommendations (UNIFIED CARD) (lg:col-span-7) -->
            <section class="lg:col-span-7 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 md:w-8 md:h-8 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6]"><i class="fa-solid fa-calendar-day text-sm md:text-xs"></i></div>
                        <h2 class="text-[17px] md:text-[15px] font-extrabold text-[#1E2657]">Today's Session & Home Activity</h2>
                    </div>

                    <a href="{{ route('guardian.logs.archive') }}" class="inline-flex items-center gap-1.5 text-[12px] font-extrabold text-[#6B7086] hover:text-[#3B82F6] transition-colors">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i> History & Archive
                    </a>
                </div>

                <div class="flex flex-col gap-5">
                    @forelse($recentLogs ?? [] as $log)
                        <div x-data="{ showComments: false }" class="bg-white border border-black/15 rounded-2xl shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md">
                            
                            <!-- Unified Card Header -->
                            <div class="px-5 py-4 sm:px-6 sm:py-5 border-b border-black/10 bg-gradient-to-r from-[#F8FAFC] to-white flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white shadow-sm flex items-center justify-center text-[13px] font-extrabold shrink-0">
                                        {{ strtoupper(substr($log->teacher->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-[15px] font-extrabold text-[#1E2657] leading-tight truncate">Teacher {{ $log->teacher->name ?? 'Staff' }}</h4>
                                        <span class="text-[11px] text-[#9CA0AF] font-medium block mt-0.5">
                                            <i class="fa-regular fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }})
                                        </span>
                                    </div>
                                </div>
                                
                                @if($log->quarter)
                                    <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-full bg-[#3B82F6]/10 text-[#3B82F6] uppercase tracking-wider shrink-0 border border-[#3B82F6]/20">
                                        {{ strtoupper($log->quarter) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Unified Card Body -->
                            <div class="p-5 sm:p-6 flex flex-col gap-4">
                                
                                <!-- 1. Daily Report Block (Strictly from approved Daily Report) -->
                                @php
                                    $dashReportText = (isset($log->daily_report) && $log->daily_report) ? $log->daily_report->content : '';
                                    $dashHomeRec = $log->home_recommendations ?? '';

                                    if (preg_match('/^(.*?)(?:\r?\n)+(?:Tonight\'s Suggested Activity|Tonight\'s Suggested Home Activity|Home Activity):?\s*(.*)$/si', $dashReportText, $matches)) {
                                        $dashReportText = trim($matches[1]);
                                        if (empty($dashHomeRec) && !empty($matches[2])) {
                                            $dashHomeRec = trim($matches[2]);
                                        }
                                    }
                                @endphp

                                @if(!empty($dashReportText))
                                    <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl p-4 sm:p-5">
                                        <h5 class="text-[11px] font-extrabold uppercase tracking-widest text-indigo-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-file-lines text-indigo-600"></i> Daily Report:
                                        </h5>
                                        <p class="text-[14px] md:text-[13px] font-medium text-slate-800 leading-relaxed m-0 whitespace-pre-line">{{ $dashReportText }}</p>
                                    </div>
                                @endif

                                <!-- 2. Tonight's Suggested Home Activity Block (if present) -->
                                @if(!empty($dashHomeRec))
                                    <div class="bg-emerald-50/70 border border-emerald-200/80 border-l-4 border-l-emerald-500 rounded-2xl p-4 sm:p-5">
                                        <h5 class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-house-user text-emerald-600"></i> Tonight's Suggested Activity:
                                        </h5>
                                        <p class="text-[14px] md:text-[13px] font-medium text-emerald-950 leading-relaxed m-0 whitespace-pre-line">{{ $dashHomeRec }}</p>
                                    </div>
                                @endif

                                @php
                                    $logPhotoUrl = null;
                                    if ($log->image_path) {
                                        $logPhotoUrl = route('secure.log-photo', $log->id);
                                    } elseif (isset($log->daily_report) && $log->daily_report && $log->daily_report->image_path) {
                                        $logPhotoUrl = route('secure.report-photo', $log->daily_report->id);
                                    }
                                @endphp

                                <!-- 3. Attached Session Photo (if present) -->
                                @if($logPhotoUrl)
                                    <div class="mt-1 flex items-center gap-2">
                                        <button type="button" 
                                                @click="$dispatch('open-image-preview', { src: '{{ $logPhotoUrl }}', title: 'Observation Photo - {{ addslashes($log->student->first_name) }}' })"
                                                class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#1E2657] text-[12px] font-extrabold rounded-xl transition-colors border border-black/10 shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-camera text-[#3B82F6]"></i> View Today's Activity Photo
                                        </button>
                                    </div>
                                @endif

                                <!-- Actions Bar: Reply / Discussion Button -->
                                <div class="flex items-center justify-between pt-3 border-t border-black/10 mt-1">
                                    <button @click="showComments = !showComments" type="button" class="inline-flex items-center gap-2 text-[13px] font-extrabold text-[#3B82F6] hover:text-blue-700 transition-colors py-1 focus:outline-none cursor-pointer">
                                        <i class="fa-solid fa-reply text-xs"></i>
                                        <span x-text="showComments ? 'Hide Replies' : 'Reply / Comments ({{ $log->comments->count() }})'"></span>
                                    </button>

                                    @if($log->comments->count() > 0)
                                        <span class="text-[12px] text-[#9CA0AF] font-bold flex items-center gap-1.5">
                                            <i class="fa-regular fa-comment-dots"></i> {{ $log->comments->count() }} {{ Str::plural('reply', $log->comments->count()) }}
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <!-- Expandable Comment / Reply Box -->
                            <div x-show="showComments" x-cloak x-transition class="bg-[#F8FAFC] border-t border-black/10 p-5 sm:p-6 flex flex-col gap-4">
                                
                                <!-- Comments history -->
                                @if($log->comments->count() > 0)
                                    <div class="space-y-3 max-h-[280px] overflow-y-auto pr-1">
                                        @foreach($log->comments->reverse() as $comment)
                                            <div class="flex flex-col {{ $comment->user_id === Auth::id() ? 'items-end' : 'items-start' }}">
                                                <div class="max-w-[88%] p-3.5 rounded-2xl text-[13px] shadow-sm {{ $comment->user_id === Auth::id() ? 'bg-[#3B82F6] text-white rounded-tr-none' : 'bg-white border border-black/10 text-[#1E2657] rounded-tl-none' }}">
                                                    <div class="text-[10px] font-extrabold uppercase tracking-wider mb-1 {{ $comment->user_id === Auth::id() ? 'text-blue-100' : 'text-[#6B7086]' }}">
                                                        {{ $comment->user->name }} ({{ ucfirst($comment->user->role) }})
                                                    </div>
                                                    <div class="m-0 leading-relaxed font-medium whitespace-pre-line text-[13px]">
                                                        {!! strip_tags($comment->body, '<p><br><b><strong><i><em><a>') !!}
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-[#9CA0AF] font-medium mt-1 px-1">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-2 text-[#9CA0AF]">
                                        <p class="text-[12px] font-medium m-0">No comments yet. Send a message to Teacher {{ $log->teacher->name ?? 'Staff' }} below.</p>
                                    </div>
                                @endif

                                <!-- Direct Comment Form -->
                                <form action="{{ route('comments.store') }}" method="POST" class="flex flex-col gap-2.5 pt-2 border-t border-black/5">
                                    @csrf
                                    <input type="hidden" name="commentable_type" value="App\Models\DailyLog">
                                    <input type="hidden" name="commentable_id" value="{{ $log->id }}">
                                    
                                    <div class="relative">
                                        <textarea name="body" rows="2" class="w-full rounded-xl border border-black/15 bg-white p-3.5 text-[13px] font-medium text-[#1E2657] placeholder:text-[#9CA0AF] focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 focus:outline-none transition-all resize-none shadow-sm" placeholder="Write a reply to the teacher..." required></textarea>
                                    </div>

                                    <div class="flex justify-end items-center gap-2">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#3B82F6] text-white text-[12px] font-extrabold rounded-xl hover:bg-blue-600 transition-all active:scale-95 shadow-sm cursor-pointer">
                                            <i class="fa-solid fa-paper-plane text-[11px]"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-8 sm:p-10 text-center text-[#9CA0AF] flex flex-col items-center">
                            <div class="w-16 h-16 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-2xl mb-4 text-[#3B82F6]">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <h4 class="text-[17px] font-extrabold text-[#1E2657]">All Caught Up for Today!</h4>
                            <p class="text-[13px] font-medium text-[#6B7086] mt-2 max-w-md leading-relaxed">Yesterday's notes and recommendations have been safely archived. Today's session update will appear here when the teacher posts.</p>
                            <a href="{{ route('guardian.logs.archive') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-[#F4F6FA] hover:bg-slate-100 text-[#1E2657] border border-black/10 rounded-xl text-[12px] font-extrabold transition-colors shadow-sm">
                                <i class="fa-solid fa-clock-rotate-left text-[#3B82F6]"></i> View Past Logs & Activities History
                            </a>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

    </div>
</div>

<!-- Announcement Image Enlarge Lightbox Modal -->
<div 
    x-data="{ open: false, src: '', title: '' }" 
    @open-image-preview.window="open = true; src = $event.detail.src; title = $event.detail.title;" 
    x-show="open" 
    x-cloak 
    @keydown.escape.window="open = false" 
    class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-2 sm:p-6"
>
    <div @click.away="open = false" class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
            <h4 class="font-extrabold text-sm sm:text-base truncate pr-4" x-text="title"></h4>
            <button @click="open = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 active:scale-90 flex items-center justify-center text-white transition cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-4 overflow-auto flex items-center justify-center bg-slate-900/5 max-h-[calc(90vh-60px)]">
            <img :src="src" :alt="title" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-md">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.innerText;
                if(isNaN(target)) return;
                
                let count = 0;
                const inc = Math.max(1, Math.ceil(target / 100)); 
                
                const timer = setInterval(() => {
                    count += inc;
                    if (count >= target) {
                        counter.innerText = target;
                        clearInterval(timer);
                    } else {
                        counter.innerText = count;
                    }
                }, 15);
            };
            setTimeout(updateCount, 100);
        });
    });
</script>
@endpush
