@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- WELCOME HEADER -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2">
                <span class="text-[#5B8DEF] text-[11px] font-extrabold tracking-widest uppercase mb-1 block">Guardian Portal</span>
                <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h1>
                <p class="text-[#6B7086] text-sm font-medium">Here's how {{ $activeChild->first_name ?? 'your child' }} is doing at the learning center today.</p>
            </div>
        </header>

        <!-- PORTAL QUICK LINKS -->
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5 w-full">
                
                <!-- Learning Goals (Purple Theme) -->
                <a href="{{ route('guardian.goals') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col items-start gap-4 relative overflow-hidden h-full">
                    <div class="w-10 h-10 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6] transition-colors duration-300 group-hover:bg-[#8B5CF6] group-hover:text-white relative z-10">
                        <i class="fa-solid fa-bullseye text-[16px]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#8B5CF6] transition-colors duration-300">IEP Goals</h3>
                        <p class="text-[12px] font-medium text-[#9CA0AF] mt-1">{{ isset($activeGoals) ? $activeGoals->count() : 0 }} objectives being tracked.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- School Reports Dropdown (Blue Theme) -->
                <div x-data="{ open: false }" class="relative w-full h-full">
                    <button @click="open = !open" @click.away="open = false" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col items-start gap-4 w-full text-left h-full focus-visible:outline-none relative overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] transition-colors duration-300 group-hover:bg-[#3B82F6] group-hover:text-white relative z-10">
                            <i class="fa-solid fa-file-signature text-[16px]"></i>
                        </div>
                        <div class="flex-1 w-full relative z-10">
                            <h3 class="text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#3B82F6] transition-colors duration-300 flex justify-between items-center w-full">
                                 Reports 
                                <i class="fa-solid fa-caret-down text-[12px] text-[#9CA0AF] transition-transform duration-300" :class="{'rotate-180': open}"></i>
                            </h3>
                            <p class="text-[12px] font-medium text-[#9CA0AF] mt-1">View official evaluations.</p>
                        </div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                    </button>
                    <!-- Reports Dropdown Links -->
                    <div x-show="open" x-transition x-cloak class="absolute top-[105%] left-0 w-full bg-white border border-black/15 shadow-lg rounded-2xl z-50 p-2 text-left overflow-hidden">
                        <a href="{{ route('guardian.reports', ['type' => 'daily']) }}" class="block px-4 py-2.5 text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Daily Summaries <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block px-4 py-2.5 text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 1 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block px-4 py-2.5 text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 2 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block px-4 py-2.5 text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 3 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block px-4 py-2.5 text-[13px] font-extrabold text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#3B82F6] rounded-xl transition-colors flex justify-between items-center">Quarter 4 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                    </div>
                </div>

                <!-- At-Home Support (Green Theme) -->
                <a href="{{ route('guardian.recommendations') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col items-start gap-4 relative overflow-hidden h-full">
                    <div class="w-10 h-10 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981] transition-colors duration-300 group-hover:bg-[#10B981] group-hover:text-white relative z-10">
                        <i class="fa-solid fa-house-chimney-medical text-[16px]"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[16px] font-extrabold text-[#1E2657] group-hover:text-[#10B981] transition-colors duration-300">Home Recommendation</h3>
                        <p class="text-[12px] font-medium text-[#9CA0AF] mt-1">See what you can do at home.</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
            </div>
        </section>

        <!-- MAIN CONTENT: PROGRESS & UPDATES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 mt-4">
            
            <!-- LEFT COLUMN: MILESTONES -->
            <section class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6]"><i class="fa-solid fa-chart-line text-sm"></i></div>
                    <h2 class="text-[15px] font-extrabold text-[#1E2657]">{{ $activeChild->first_name ?? 'Child' }}'s Current IEP Goals</h2>
                </div>

                <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-6 md:p-8 flex flex-col gap-6">
                    
                    @forelse($activeGoals ?? [] as $goal)
                        <div class="flex flex-col gap-3">
                            <!-- Category Badge -->
                            <span class="inline-block bg-[#8B5CF6]/10 text-[#8B5CF6] text-[9px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-md w-max">
                                {{ $goal->domain ?? 'Development Area' }}
                            </span>
                            
                            <!-- Progress Stats -->
                            <div class="flex justify-between items-end mt-1">
                                <h3 class="text-[14px] font-extrabold text-[#1E2657] line-clamp-1 pr-4">{{ $goal->goal_description }}</h3>
                                <span class="text-[16px] font-extrabold text-[#8B5CF6]">{{ $goal->progress_percentage ?? 0 }}%</span>
                            </div>
                            
                            <!-- Clean Progress Bar -->
                            <div class="h-[8px] w-full bg-[#F4F6FA] rounded-full overflow-hidden">
                                <div class="h-full bg-[#8B5CF6] rounded-full transition-all duration-1000 ease-out" style="width: {{ $goal->progress_percentage ?? 0 }}%;"></div>
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div class="w-full h-px bg-black/5 my-2"></div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-[#9CA0AF] flex flex-col items-center">
                            <div class="w-14 h-14 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-[20px] mb-3"><i class="fa-solid fa-star text-[#F59E0B]"></i></div>
                            <h4 class="text-[15px] font-extrabold text-[#1E2657]">Ready for New Challenges!</h4>
                            <p class="text-[12px] font-medium mt-1">The teachers are preparing the next set of goals.</p>
                        </div>
                    @endforelse

                </div>
            </section>

            <!-- RIGHT COLUMN: CLASSROOM MESSAGES & SUPPORT -->
            <div class="flex flex-col gap-8">
                
                <!-- Classroom Messages -->
                <section class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6]"><i class="fa-regular fa-comments text-sm"></i></div>
                        <h2 class="text-[15px] font-extrabold text-[#1E2657]">Daily Log</h2>
                    </div>

                    <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-6 md:p-8 flex flex-col gap-6">
                        
                        @forelse($recentLogs ?? [] as $log)
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white shadow-sm flex items-center justify-center text-[11px] font-extrabold">
                                        {{ strtoupper(substr($log->teacher->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <h4 class="text-[13px] font-extrabold text-[#1E2657]">Teacher {{ $log->teacher->name ?? 'Staff' }}</h4>
                                    <span class="text-[11px] font-medium text-[#9CA0AF] ml-auto"><i class="fa-regular fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}</span>
                                </div>
                                <div class="bg-[#F4F6FA] border border-black/5 rounded-2xl rounded-tl-sm p-4 mt-1">
                                    <p class="text-[13px] font-medium text-[#6B7086] leading-relaxed m-0">{{ $log->notes }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-[#9CA0AF] flex flex-col items-center">
                                <div class="w-14 h-14 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-[20px] mb-3"><i class="fa-regular fa-envelope-open"></i></div>
                                <h4 class="text-[15px] font-extrabold text-[#1E2657]">No New Notes</h4>
                                <p class="text-[12px] font-medium mt-1">We'll let you know when the teacher sends an update.</p>
                            </div>
                        @endforelse

                    </div>
                </section>

                <!-- Try this at Home -->
                <section class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981]"><i class="fa-solid fa-puzzle-piece text-sm"></i></div>
                        <h2 class="text-[15px] font-extrabold text-[#1E2657]">Try this at Home</h2>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($homeActivities ?? [] as $activity)
                            <div class="bg-white border border-black/15 rounded-2xl shadow-sm hover:shadow-md hover:border-black/30 transition-all p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[14px] font-extrabold text-[#1E2657] mb-1">{{ $activity->title ?? 'Practice Activity' }}</h3>
                                    <p class="text-[12px] font-medium text-[#9CA0AF] line-clamp-1">{{ $activity->description ?? 'Short description of the task.' }}</p>
                                </div>
                                <a href="{{ route('guardian.recommendations', $activity->id ?? '') }}" class="bg-white border border-black/15 rounded-xl shadow-sm hover:bg-slate-50 hover:border-[#10B981]/50 hover:text-[#10B981] px-4 py-2.5 text-[12px] font-extrabold text-[#6B7086] transition-all text-center whitespace-nowrap">
                                    View Activity
                                </a>
                            </div>
                        @empty
                            <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[14px] font-extrabold text-[#1E2657] mb-1">Check Recommendations</h3>
                                    <p class="text-[12px] font-medium text-[#9CA0AF]">Find engaging activities to do together.</p>
                                </div>
                                <a href="{{ route('guardian.recommendations') }}" class="bg-white border border-black/15 rounded-xl shadow-sm hover:bg-slate-50 hover:border-[#10B981]/50 hover:text-[#10B981] px-4 py-2.5 text-[12px] font-extrabold text-[#6B7086] transition-all text-center whitespace-nowrap">
                                    Browse Ideas
                                </a>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>
        </div>

    </div>
</div>

@endsection