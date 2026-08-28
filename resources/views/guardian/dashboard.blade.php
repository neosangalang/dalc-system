@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

@php
    $myChildren = Auth::user()->children ?? collect();
    $activeChildId = session('active_child_id', $myChildren->first()->id ?? null);
    $activeChild = $myChildren->where('id', $activeChildId)->first();
@endphp

<!-- Main Container (Updated Background for Smooth Desktop Blend) -->
<div class="w-full bg-[#F8FAFC] md:bg-transparent min-h-screen">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 md:space-y-10">

        <!-- WELCOME HEADER & PILL STUDENT SELECTOR -->
        <header class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div class="flex flex-col gap-2">
                <span class="text-[#5B8DEF] text-[11px] font-extrabold tracking-widest uppercase mb-1 block">Guardian Portal</span>
                <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h1>
                <p class="text-[#6B7086] text-sm font-medium leading-relaxed">Here's how {{ $activeChild->first_name ?? 'your child' }} is doing at the learning center today.</p>
            </div>

            <!-- Sleek Pill Student Selector -->
            @if($myChildren->count() > 0 && $activeChild)
                <div x-data="{ switchOpen: false }" class="relative z-40 self-start md:self-auto shrink-0 w-full md:w-auto mt-2 md:mt-0">
                    <button @click="switchOpen = !switchOpen" @click.away="switchOpen = false" 
                            class="flex items-center justify-between md:justify-start gap-2.5 px-5 py-3 md:py-2 w-full md:w-auto bg-white border border-black/10 rounded-full hover:border-[#6366F1]/50 hover:shadow-sm transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#6366F1]">
                        <div class="flex items-center gap-2.5">
                            <div class="bg-[#6366F1] text-white rounded-full flex items-center justify-center w-[24px] h-[24px] text-[10px] shadow-sm">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="text-[14px] font-extrabold text-[#6366F1]">Viewing: {{ $activeChild->first_name }}</span>
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
                                    <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-3.5 bg-[#6366F1] text-white text-[13px] font-extrabold cursor-default pointer-events-none">
                                        <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                        <i class="fa-solid fa-check text-[13px]"></i>
                                    </a>
                                @else
                                    <!-- Inactive Student State -->
                                    <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-3.5 text-[#6B7086] text-[13px] font-extrabold hover:bg-slate-50 hover:text-[#1E2657] transition-colors">
                                        <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </header>

        <!-- PORTAL QUICK LINKS (2x2 Grid) -->
        <section>
            <div class="grid grid-cols-2 gap-4 md:gap-5 w-full">
                
                <!-- Learning Goals -->
                <a href="{{ route('guardian.goals') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:-translate-y-1 transition-all duration-300 p-5 sm:p-6 flex flex-col items-start gap-4 relative overflow-hidden h-full">
                    <div class="w-12 h-12 sm:w-10 sm:h-10 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6] transition-colors duration-300 group-hover:bg-[#8B5CF6] group-hover:text-white relative z-10">
                        <i class="fa-solid fa-bullseye text-[18px] sm:text-[16px]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[17px] sm:text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#8B5CF6] transition-colors duration-300">IEP Goals</h3>
                        <p class="text-[13px] sm:text-[12px] font-medium text-[#9CA0AF] mt-1">{{ isset($activeGoals) ? $activeGoals->count() : 0 }} tracking.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- School Reports Dropdown -->
                <div x-data="{ open: false }" class="relative w-full h-full">
                    <button @click="open = !open" @click.away="open = false" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:-translate-y-1 transition-all duration-300 p-5 sm:p-6 flex flex-col items-start gap-4 w-full text-left h-full focus-visible:outline-none relative overflow-hidden">
                        <div class="w-12 h-12 sm:w-10 sm:h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] transition-colors duration-300 group-hover:bg-[#3B82F6] group-hover:text-white relative z-10">
                            <i class="fa-solid fa-file-signature text-[18px] sm:text-[16px]"></i>
                        </div>
                        <div class="flex-1 w-full relative z-10">
                            <h3 class="text-[17px] sm:text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#3B82F6] transition-colors duration-300 flex justify-between items-center w-full">
                                 Reports 
                                <i class="fa-solid fa-caret-down text-[14px] sm:text-[12px] text-[#9CA0AF] transition-transform duration-300" :class="{'rotate-180': open}"></i>
                            </h3>
                            <p class="text-[13px] sm:text-[12px] font-medium text-[#9CA0AF] mt-1">Official evaluations.</p>
                        </div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                    </button>
                    <div x-show="open" x-transition x-cloak class="absolute top-[105%] left-0 w-full bg-white border border-black/15 shadow-xl rounded-2xl z-[60] p-3 text-left overflow-hidden">
                        <a href="{{ route('guardian.reports', ['type' => 'daily']) }}" class="block px-4 py-3.5 sm:py-2.5 text-[14px] sm:text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Daily Summaries <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block px-4 py-3.5 sm:py-2.5 text-[14px] sm:text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 1 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block px-4 py-3.5 sm:py-2.5 text-[14px] sm:text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 2 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block px-4 py-3.5 sm:py-2.5 text-[14px] sm:text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 3 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block px-4 py-3.5 sm:py-2.5 text-[14px] sm:text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 4 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                    </div>
                </div>

                <!-- At-Home Support -->
                <a href="{{ route('guardian.recommendations') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:-translate-y-1 transition-all duration-300 p-5 sm:p-6 flex flex-col items-start gap-4 relative overflow-hidden h-full">
                    <div class="w-12 h-12 sm:w-10 sm:h-10 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981] transition-colors duration-300 group-hover:bg-[#10B981] group-hover:text-white relative z-10">
                        <i class="fa-solid fa-house-chimney-medical text-[18px] sm:text-[16px]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[17px] sm:text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#10B981] transition-colors duration-300">Home Recommendation</h3>
                        <p class="text-[13px] sm:text-[12px] font-medium text-[#9CA0AF] mt-1">Activities at home.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- Announcements Module Card -->
                <a href="{{ route('guardian.announcements.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:-translate-y-1 transition-all duration-300 p-5 sm:p-6 flex flex-col items-start gap-4 relative overflow-hidden h-full">
                    @if($newAnnouncementsCount > 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full border-2 border-white shadow-sm flex items-center justify-center min-w-[24px] z-20">
                            {{ $newAnnouncementsCount }}
                        </div>
                    @endif

                    <div class="w-12 h-12 sm:w-10 sm:h-10 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6] transition-colors duration-300 group-hover:bg-[#8B5CF6] group-hover:text-white relative z-10">
                        <i class="fa-solid fa-bullhorn text-[18px] sm:text-[16px]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[17px] sm:text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#8B5CF6] transition-colors duration-300">Announcement</h3>
                        <p class="text-[13px] sm:text-[12px] text-[#9CA0AF] font-medium m-0 mt-1">Center alerts.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

            </div>
        </section>

        <!-- FEATURED ANNOUNCEMENT HERO BANNER -->
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
            class="w-full relative rounded-2xl md:rounded-[2rem] overflow-hidden shadow-lg border border-black/5 group aspect-square sm:aspect-[16/9] lg:aspect-[21/9] min-h-[340px] md:min-h-[260px] max-h-[450px]"
        >
            <!-- Slider Container -->
            <div 
                x-ref="slider"
                @scroll.passive="updateActive"
                class="flex h-full w-full overflow-x-auto snap-x snap-mandatory scroll-smooth hide-scrollbar touch-pan-x"
                style="-ms-overflow-style: none; scrollbar-width: none;"
            >
                @foreach($featuredAnnouncements as $announcement)
                <div class="w-full h-full flex-shrink-0 snap-center relative flex items-center justify-center bg-[#0052cc]">
                    
                    @if($announcement->image_path)
                        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" class="absolute inset-0 w-full h-full object-cover object-center">
                    @else
                        <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
                        <div class="absolute top-6 left-12 text-[#FFD700] opacity-90"><i class="fa-solid fa-star text-2xl drop-shadow-md"></i></div>
                        <div class="absolute top-1/3 right-1/4 text-[#FFD700] opacity-60"><i class="fa-solid fa-star text-sm drop-shadow-md"></i></div>
                    @endif

                    <div class="absolute inset-0 z-10 bg-gradient-to-b from-[#0F172A]/90 via-[#0F172A]/40 md:via-[#0F172A]/20 to-transparent flex flex-col justify-start p-5 sm:p-8 pointer-events-none">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-5 sm:gap-6 w-full h-full pointer-events-auto">
                            
                            <div class="flex flex-col items-start gap-3 sm:gap-3 max-w-2xl">
                                @php
                                    $badgeIcon = match($announcement->type ?? 'Center Update') {
                                        'Class Suspension' => 'fa-triangle-exclamation',
                                        'Holiday'          => 'fa-calendar-star',
                                        'Event'            => 'fa-calendar-check',
                                        default            => 'fa-bullhorn',
                                    };
                                @endphp

                                <span class="inline-flex items-center bg-white/20 backdrop-blur-md text-white border border-white/30 px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase shadow-sm">
                                    <i class="fa-solid {{ $badgeIcon }} mr-2 text-[#FFD700]"></i> {{ $announcement->type ?? 'Center Update' }}
                                </span>
                                
                                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black leading-tight text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)] tracking-tight text-left">
                                    {{ strtoupper($announcement->title) }}
                                </h2>
                            </div>
                            
                            <div class="shrink-0 mt-auto md:mt-0 w-full md:w-auto pb-6 md:pb-0">
                                <a href="{{ route('guardian.announcements.index') }}" class="inline-flex justify-center items-center w-full md:w-auto px-6 py-4 md:py-3.5 bg-[#FFD700] text-[#1E2657] text-[14px] font-black rounded-full hover:bg-yellow-400 hover:scale-[1.02] transition-all shadow-xl">
                                    View Details <i class="fa-solid fa-arrow-right ml-2"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($featuredAnnouncements->count() > 1)
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2.5 z-20 pointer-events-auto">
                <template x-for="i in slides" :key="i">
                    <button 
                        @click="scrollTo(i - 1)" 
                        :class="activeSlide === (i - 1) ? 'w-8 bg-white shadow-md' : 'w-2.5 bg-white/50 cursor-pointer hover:bg-white'" 
                        class="h-2.5 rounded-full transition-all duration-300"
                        aria-label="Go to slide"
                    ></button>
                </template>
            </div>
            @endif
        </section>

        <style>
            .hide-scrollbar::-webkit-scrollbar { display: none; }
        </style>
        @endif

        <!-- MAIN CONTENT: PROGRESS & UPDATES (3-COLUMN LAYOUT) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mt-4 items-start">
            
            <!-- COLUMN 1: IEP Goals Section -->
            <section class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-8 md:h-8 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6]"><i class="fa-solid fa-chart-line text-sm md:text-xs"></i></div>
                    <h2 class="text-[17px] md:text-[15px] font-extrabold text-[#1E2657]">{{ $activeChild->first_name ?? 'Child' }}'s Current IEP Goals</h2>
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

            <!-- COLUMN 2: Classroom Messages / Daily Log -->
            <section class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-8 md:h-8 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6]"><i class="fa-regular fa-comments text-sm md:text-xs"></i></div>
                    <h2 class="text-[17px] md:text-[15px] font-extrabold text-[#1E2657]">Daily Log</h2>
                </div>

                <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col gap-6">
                    @forelse($recentLogs ?? [] as $log)
                        <div class="flex flex-col gap-3 md:gap-2">
                            <div class="flex items-center gap-3 mb-1">
                                <div class="w-9 h-9 md:w-8 md:h-8 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white shadow-sm flex items-center justify-center text-[12px] md:text-[11px] font-extrabold">
                                    {{ strtoupper(substr($log->teacher->name ?? 'T', 0, 1)) }}
                                </div>
                                <h4 class="text-[14px] md:text-[13px] font-extrabold text-[#1E2657]">Teacher {{ $log->teacher->name ?? 'Staff' }}</h4>
                                <span class="text-[12px] md:text-[11px] font-medium text-[#9CA0AF] ml-auto"><i class="fa-regular fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}</span>
                            </div>
                            <div class="bg-[#F4F6FA] border border-black/5 rounded-2xl rounded-tl-sm p-5 md:p-4 mt-1">
                                <p class="text-[14px] md:text-[13px] font-medium text-[#6B7086] leading-relaxed m-0">{{ $log->notes }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-[#9CA0AF] flex flex-col items-center">
                            <div class="w-14 h-14 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-[22px] mb-4"><i class="fa-regular fa-envelope-open"></i></div>
                            <h4 class="text-[16px] md:text-[14px] font-extrabold text-[#1E2657]">No New Notes</h4>
                            <p class="text-[14px] md:text-[12px] font-medium mt-2">We'll let you know when the teacher sends an update.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- COLUMN 3: Try this at Home Section -->
            <section class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-8 md:h-8 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981]"><i class="fa-solid fa-puzzle-piece text-sm md:text-xs"></i></div>
                    <h2 class="text-[17px] md:text-[15px] font-extrabold text-[#1E2657]">Try this at Home</h2>
                </div>

                <div class="flex flex-col gap-4">
                    @forelse($homeActivities ?? [] as $activity)
                        <div class="bg-white border border-black/15 rounded-2xl shadow-sm hover:shadow-md hover:border-black/30 transition-all p-5 sm:p-6 flex flex-col gap-5 md:gap-4">
                            <div>
                                <h3 class="text-[15px] md:text-[14px] font-extrabold text-[#1E2657] mb-2 md:mb-1">{{ $activity->title ?? 'Practice Activity' }}</h3>
                                <p class="text-[14px] md:text-[12px] font-medium text-[#9CA0AF] leading-relaxed line-clamp-2">{{ $activity->description ?? 'Short description of the task.' }}</p>
                            </div>
                            <a href="{{ route('guardian.recommendations', $activity->id ?? '') }}" class="bg-white border border-black/15 rounded-xl shadow-sm hover:bg-slate-50 hover:border-[#10B981]/50 hover:text-[#10B981] px-4 py-3.5 md:py-2.5 text-[14px] md:text-[12px] font-extrabold text-[#6B7086] transition-all text-center mt-2 md:mt-0 w-full block">
                                View Activity
                            </a>
                        </div>
                    @empty
                        <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 sm:p-6 flex flex-col gap-5 md:gap-4">
                            <div>
                                <h3 class="text-[15px] md:text-[14px] font-extrabold text-[#1E2657] mb-2 md:mb-1">Check Recommendations</h3>
                                <p class="text-[14px] md:text-[12px] font-medium text-[#9CA0AF] leading-relaxed">Find engaging activities to do together.</p>
                            </div>
                            <a href="{{ route('guardian.recommendations') }}" class="bg-white border border-black/15 rounded-xl shadow-sm hover:bg-slate-50 hover:border-[#10B981]/50 hover:text-[#10B981] px-4 py-3.5 md:py-2.5 text-[14px] md:text-[12px] font-extrabold text-[#6B7086] transition-all text-center mt-2 md:mt-0 w-full block">
                                Browse Ideas
                            </a>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

    </div>
</div>

@endsection