@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

@php
    $myChildren = Auth::user()->children ?? collect();
    $activeChildId = session('active_child_id', $myChildren->first()->id ?? null);
    $activeChild = $myChildren->where('id', $activeChildId)->first();
@endphp

<!-- Main Container (Updated Background for Smooth Desktop Blend) -->
<div class="w-full bg-transparent min-h-screen">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 md:space-y-10">

        <!-- WELCOME HEADER & PILL STUDENT SELECTOR -->
        <header class="flex flex-col md:flex-row md:items-start justify-between gap-4 sm:gap-6">
            <div class="flex flex-col gap-1 sm:gap-2">
                <span class="text-[#5B8DEF] text-[11px] font-extrabold tracking-widest uppercase mb-0.5 sm:mb-1 block">Guardian Portal</span>
                <h1 class="text-white text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight mb-1 sm:mb-2">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h1>
                <p class="text-blue-100/80 text-[13px] sm:text-sm font-medium leading-relaxed m-0">Here's how {{ $activeChild->first_name ?? 'your child' }} is doing at the learning center today.</p>
            </div>

            <!-- Sleek Pill Student Selector -->
            @if($myChildren->count() > 0 && $activeChild)
                <div x-data="{ switchOpen: false }" class="relative z-40 self-start md:self-auto shrink-0 w-full md:w-auto mt-1 md:mt-0">
                    <button @click="switchOpen = !switchOpen" @click.away="switchOpen = false" 
                            class="flex items-center justify-between md:justify-start gap-2.5 px-4 sm:px-5 py-2.5 sm:py-2 w-full md:w-auto bg-white border border-black/10 rounded-full hover:border-[#6366F1]/50 hover:shadow-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1] active:scale-95 shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-[#6366F1] text-white rounded-full flex items-center justify-center w-[24px] h-[24px] text-[10px] shadow-sm">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="text-[13px] sm:text-[14px] font-extrabold text-[#6366F1]">Viewing: {{ $activeChild->first_name }}</span>
                        </div>
                        <i class="fa-solid fa-angle-down text-[#6366F1] text-[12px] ml-1 transition-transform duration-300" :class="{'rotate-180': switchOpen}"></i>
                    </button>
                    
                    <!-- Dropdown Options -->
                    <div x-show="switchOpen" x-transition x-cloak class="absolute left-0 md:left-auto md:right-0 mt-2 w-full md:w-56 bg-white border border-black/10 rounded-xl shadow-[0_8px_24px_-8px_rgba(0,0,0,0.15)] pb-1 pt-1 z-50 overflow-hidden">
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

        <!-- PORTAL QUICK LINKS (2x2 Grid matching reference design) -->
        <section x-data="{ unreadAnnouncements: {{ $newAnnouncementsCount }} }">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 md:gap-6 w-full">
                
                <!-- 1. IEP Goals -->
                <a href="{{ route('guardian.goals') }}" class="group bg-white border border-black/10 rounded-2xl sm:rounded-3xl shadow-[0_2px_12px_-4px_rgba(0,0,0,0.06)] hover:border-[#8B5CF6]/40 hover:shadow-[0_8px_24px_-6px_rgba(139,92,246,0.18)] hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 md:p-6 flex flex-row items-center gap-4 sm:gap-5 md:gap-6 relative overflow-hidden h-full active:scale-[0.99]">
                    
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-[70px] md:h-[70px] rounded-full bg-[#8B5CF6]/15 flex items-center justify-center text-[#8B5CF6] transition-colors duration-300 group-hover:bg-[#8B5CF6] group-hover:text-white">
                            <i class="fa-solid fa-bullseye text-[24px] sm:text-[28px] md:text-[32px]"></i>
                        </div>
                        @if(isset($unreadIepCommentsCount) && $unreadIepCommentsCount > 0)
                            <div class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] sm:text-[11px] font-extrabold w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 border-white shadow-sm flex items-center justify-center z-20 animate-pulse">
                                {{ $unreadIepCommentsCount }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-[17px] sm:text-[19px] md:text-[21px] font-extrabold text-[#1E2657] group-hover:text-[#8B5CF6] transition-colors duration-300 leading-tight">IEP Goals</h3>
                        <p class="text-[12px] sm:text-[13px] md:text-[14px] font-semibold text-[#6B7086] mt-0.5 sm:mt-1 truncate">{{ isset($activeGoals) ? $activeGoals->count() : 0 }} tracking.</p>
                    </div>

                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- 2. School Reports Dropdown -->
                <div x-data="{ open: false }" class="relative w-full h-full">
                    <button @click="open = !open" @click.away="open = false" class="group bg-white border border-black/10 rounded-2xl sm:rounded-3xl shadow-[0_2px_12px_-4px_rgba(0,0,0,0.06)] hover:border-[#3B82F6]/40 hover:shadow-[0_8px_24px_-6px_rgba(59,130,246,0.18)] hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 md:p-6 flex flex-row items-center gap-4 sm:gap-5 md:gap-6 w-full text-left h-full focus-visible:outline-none relative overflow-hidden active:scale-[0.99]">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-[70px] md:h-[70px] rounded-full bg-[#3B82F6]/15 flex items-center justify-center text-[#3B82F6] transition-colors duration-300 group-hover:bg-[#3B82F6] group-hover:text-white shrink-0">
                            <i class="fa-solid fa-file-lines text-[24px] sm:text-[28px] md:text-[32px]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-[17px] sm:text-[19px] md:text-[21px] font-extrabold text-[#1E2657] group-hover:text-[#3B82F6] transition-colors duration-300 flex items-center justify-between leading-tight">
                                <span>Reports</span>
                                <i class="fa-solid fa-caret-down text-[12px] sm:text-[14px] text-[#9CA0AF] group-hover:text-[#3B82F6] transition-all duration-300 ml-2" :class="{'rotate-180': open}"></i>
                            </h3>
                            <p class="text-[12px] sm:text-[13px] md:text-[14px] font-semibold text-[#6B7086] mt-0.5 sm:mt-1 truncate">Official evaluations.</p>
                        </div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                    </button>
                    <div x-show="open" x-transition x-cloak class="absolute top-[105%] left-0 w-full bg-white border border-black/10 shadow-xl rounded-2xl z-[60] p-2 sm:p-3 text-left overflow-hidden">
                        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block px-4 py-2.5 sm:py-2 text-[13px] sm:text-[14px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 1 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block px-4 py-2.5 sm:py-2 text-[13px] sm:text-[14px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 2 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block px-4 py-2.5 sm:py-2 text-[13px] sm:text-[14px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 3 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block px-4 py-2.5 sm:py-2 text-[13px] sm:text-[14px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 4 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                    </div>
                </div>

                <!-- 3. Daily Activity -->
                <a href="{{ route('guardian.recommendations') }}" class="group bg-white border border-black/10 rounded-2xl sm:rounded-3xl shadow-[0_2px_12px_-4px_rgba(0,0,0,0.06)] hover:border-[#10B981]/40 hover:shadow-[0_8px_24px_-6px_rgba(16,185,129,0.18)] hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 md:p-6 flex flex-row items-center gap-4 sm:gap-5 md:gap-6 relative overflow-hidden h-full active:scale-[0.99]">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-[70px] md:h-[70px] rounded-full bg-[#10B981]/15 flex items-center justify-center text-[#10B981] transition-colors duration-300 group-hover:bg-[#10B981] group-hover:text-white shrink-0">
                        <i class="fa-solid fa-id-badge text-[24px] sm:text-[28px] md:text-[32px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[17px] sm:text-[19px] md:text-[21px] font-extrabold text-[#1E2657] group-hover:text-[#10B981] transition-colors duration-300 leading-tight">Daily Activity</h3>
                        <p class="text-[12px] sm:text-[13px] md:text-[14px] font-semibold text-[#6B7086] mt-0.5 sm:mt-1 truncate">Home activities.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- 4. Announcements -->
                <a href="{{ route('guardian.announcements.index') }}" 
                   @click="unreadAnnouncements = 0; fetch('{{ route('guardian.announcements.mark-seen') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })"
                   class="group bg-white border border-black/10 rounded-2xl sm:rounded-3xl shadow-[0_2px_12px_-4px_rgba(0,0,0,0.06)] hover:border-[#F97316]/40 hover:shadow-[0_8px_24px_-6px_rgba(249,115,22,0.18)] hover:-translate-y-1 transition-all duration-300 p-4 sm:p-5 md:p-6 flex flex-row items-center gap-4 sm:gap-5 md:gap-6 relative overflow-hidden h-full active:scale-[0.99]">
                    
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-[70px] md:h-[70px] rounded-full bg-[#F97316]/15 flex items-center justify-center text-[#F97316] transition-colors duration-300 group-hover:bg-[#F97316] group-hover:text-white">
                            <i class="fa-solid fa-bullhorn text-[24px] sm:text-[28px] md:text-[32px]"></i>
                        </div>
                        <div x-show="unreadAnnouncements > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] sm:text-[11px] font-black w-5 h-5 sm:w-6 sm:h-6 rounded-full border-2 border-white shadow-sm flex items-center justify-center z-20">
                            <span x-text="unreadAnnouncements"></span>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="text-[17px] sm:text-[19px] md:text-[21px] font-extrabold text-[#1E2657] group-hover:text-[#F97316] transition-colors duration-300 leading-tight">Announcements</h3>
                        <p class="text-[12px] sm:text-[13px] md:text-[14px] font-semibold text-[#6B7086] mt-0.5 sm:mt-1 truncate">Center alerts.</p>
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
                                
                                <!-- 1. Daily Report Block -->
                                @if(isset($log->daily_report) && $log->daily_report)
                                    <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl p-4 sm:p-5">
                                        <h5 class="text-[11px] font-extrabold uppercase tracking-widest text-indigo-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-file-lines text-indigo-600"></i> Daily Report:
                                        </h5>
                                        <p class="text-[14px] md:text-[13px] font-medium text-slate-800 leading-relaxed m-0 whitespace-pre-line">{{ $log->daily_report->content }}</p>
                                    </div>
                                @elseif($log->notes)
                                    <div class="bg-[#F8FAFC] border border-black/10 rounded-2xl p-4 sm:p-5">
                                        <h5 class="text-[11px] font-extrabold uppercase tracking-widest text-[#3B82F6] mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-file-lines text-[#3B82F6]"></i> Daily Report:
                                        </h5>
                                        <p class="text-[14px] md:text-[13px] font-medium text-[#1E2657] leading-relaxed m-0 whitespace-pre-line">{{ $log->notes }}</p>
                                    </div>
                                @endif

                                <!-- 2. Tonight's Suggested Home Activity Block (if present) -->
                                @if($log->home_recommendations)
                                    <div class="bg-emerald-50/70 border border-emerald-200/80 border-l-4 border-l-emerald-500 rounded-2xl p-4 sm:p-5">
                                        <h5 class="text-[11px] font-extrabold uppercase tracking-widest text-emerald-700 mb-2 flex items-center gap-2">
                                            <i class="fa-solid fa-house-user text-emerald-600"></i> Tonight's Suggested Activity:
                                        </h5>
                                        <p class="text-[14px] md:text-[13px] font-medium text-emerald-950 leading-relaxed m-0 whitespace-pre-line">{{ $log->home_recommendations }}</p>
                                    </div>
                                @endif

                                <!-- 3. Attached Session Photo (if present) -->
                                @if($log->image_path)
                                    <div class="mt-1 flex items-center gap-2">
                                        <a href="{{ route('secure.log-photo', $log->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-[#1E2657] text-[12px] font-extrabold rounded-xl transition-colors border border-black/10 shadow-sm">
                                            <i class="fa-solid fa-camera text-[#3B82F6]"></i> View Today's Activity Photo
                                        </a>
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
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#3B82F6] text-white text-[12px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm cursor-pointer">
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
    class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6"
>
    <div @click.away="open = false" class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
            <h4 class="font-extrabold text-sm sm:text-base truncate pr-4" x-text="title"></h4>
            <button @click="open = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-4 overflow-auto flex items-center justify-center bg-slate-900/5 max-h-[calc(90vh-60px)]">
            <img :src="src" :alt="title" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-md">
        </div>
    </div>
</div>

@endsection