@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<div class="bg-[#F5F5F5] min-h-[100dvh] py-8 md:py-10 w-full font-['Inter',sans-serif] text-[#111111]">
    <div class="max-w-[1200px] mx-auto px-4 md:px-6 lg:px-8 space-y-8 md:space-y-10">

        <!-- WELCOME & CHILD SELECTOR (School-Friendly Tone) -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex flex-col gap-2">
                <h2 class="text-2xl md:text-3xl font-[800] tracking-tight text-[#111111]">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h2>
                <p class="text-[#555555] font-[500] text-[15px] md:text-[16px]">Here's how {{ $activeChild->first_name ?? 'your child' }} is doing at the learning center today.</p>
            </div>

            

        </header>

        <!-- PORTAL QUICK LINKS (Replaces "System Modules") -->
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 w-full">
                
                <!-- Learning Goals -->
                <a href="{{ route('guardian.goals') }}" class="group bg-[#FFFFFF] hover:bg-[#FAF5FF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all p-5 flex flex-col items-start gap-3">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#8B5CF6]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#8B5CF6] transition-colors group-hover:bg-[#8B5CF6] group-hover:text-[#FFFFFF]"><i class="fa-solid fa-bullseye text-[18px]"></i></div>
                    <div>
                        <h3 class="text-[16px] font-[800] text-[#111111]">Learning Goals</h3>
                        <p class="text-[13px] font-[500] text-[#555555] mt-1">{{ isset($activeGoals) ? $activeGoals->count() : 0 }} objectives being tracked.</p>
                    </div>
                </a>

                <!-- School Reports Dropdown -->
                <div x-data="{ open: false }" class="relative w-full h-full">
                    <button @click="open = !open" @click.away="open = false" class="group bg-[#FFFFFF] hover:bg-[#F8FAFC] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all p-5 flex flex-col items-start gap-3 w-full text-left h-full focus-visible:outline-none">
                        <div class="w-[42px] h-[42px] rounded-lg bg-[#3B82F6]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#3B82F6] transition-colors group-hover:bg-[#3B82F6] group-hover:text-[#FFFFFF]"><i class="fa-solid fa-file-signature text-[18px]"></i></div>
                        <div class="flex-1 w-full">
                            <h3 class="text-[16px] font-[800] text-[#111111] flex justify-between items-center w-full">School Reports <i class="fa-solid fa-caret-down text-[12px] transition-transform duration-200" :class="{'rotate-180': open}"></i></h3>
                            <p class="text-[13px] font-[500] text-[#555555] mt-1">View official evaluations.</p>
                        </div>
                    </button>
                    <!-- Reports Dropdown Links -->
                    <div x-show="open" x-transition x-cloak class="absolute top-[105%] left-0 w-full bg-[#FFFFFF] border-[2px] border-[#000000] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] rounded-xl z-50 p-2 text-left text-[14px] font-[600] text-[#555555]">
                        <a href="{{ route('guardian.reports', ['type' => 'daily']) }}" class="block px-4 py-3 hover:bg-[#F8FAFC] hover:text-[#111111] rounded-lg transition-colors flex justify-between items-center">Daily Summaries <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block px-4 py-3 hover:bg-[#F8FAFC] hover:text-[#111111] rounded-lg transition-colors flex justify-between items-center">Quarter 1 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block px-4 py-3 hover:bg-[#F8FAFC] hover:text-[#111111] rounded-lg transition-colors flex justify-between items-center">Quarter 2 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block px-4 py-3 hover:bg-[#F8FAFC] hover:text-[#111111] rounded-lg transition-colors flex justify-between items-center">Quarter 3 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block px-4 py-3 hover:bg-[#F8FAFC] hover:text-[#111111] rounded-lg transition-colors flex justify-between items-center">Quarter 4 <i class="fa-solid fa-angle-right opacity-50"></i></a>
                    </div>
                </div>

                <!-- At-Home Support -->
                <a href="{{ route('guardian.recommendations') }}" class="group bg-[#FFFFFF] hover:bg-[#F0FDF4] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all p-5 flex flex-col items-start gap-3">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#10B981]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#10B981] transition-colors group-hover:bg-[#10B981] group-hover:text-[#FFFFFF]"><i class="fa-solid fa-house-chimney-medical text-[18px]"></i></div>
                    <div>
                        <h3 class="text-[16px] font-[800] text-[#111111]">Home Recommendation</h3>
                        <p class="text-[13px] font-[500] text-[#555555] mt-1">See what you can do at home.</p>
                    </div>
                </a>
            </div>
        </section>

        <!-- MAIN CONTENT: PROGRESS & UPDATES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-10">
            
            <!-- LEFT COLUMN: MILESTONES (Rebranded Goal Progress) -->
            <section class="flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-[32px] h-[32px] rounded-full bg-[#8B5CF6]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#8B5CF6]"><i class="fa-solid fa-chart-line"></i></div>
                    <h2 class="text-[16px] font-[800] text-[#111111]">{{ $activeChild->first_name ?? 'Child' }}'s Current IEP Goals</h2>
                </div>

                <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] p-6 md:p-8 flex flex-col gap-6">
                    
                    @forelse($activeGoals ?? [] as $goal)
                        <div class="flex flex-col gap-3">
                            <!-- Category Badge -->
                            <span class="inline-block bg-[#8B5CF6]/10 text-[#8B5CF6] text-[11px] font-[800] uppercase tracking-[0.08em] px-3 py-1 rounded-full border-[2px] border-[#000000] w-max">
                                {{ $goal->domain ?? 'Development Area' }}
                            </span>
                            
                            <!-- Progress Stats -->
                            <div class="flex justify-between items-end mt-1">
                                <h3 class="text-[16px] font-[800] text-[#111111] line-clamp-1 pr-4">{{ $goal->goal_description }}</h3>
                                <span class="text-[18px] font-[800] text-[#111111]">{{ $goal->progress_percentage ?? 0 }}%</span>
                            </div>
                            
                            <!-- Thicker, Friendlier Progress Bar -->
                            <div class="h-[16px] w-full bg-[#F5F5F5] border-[2px] border-[#000000] rounded-full overflow-hidden relative">
                                <div class="absolute top-0 left-0 h-full bg-[#8B5CF6] border-r-[2px] border-[#000000]" style="width: {{ $goal->progress_percentage ?? 0 }}%;"></div>
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div class="w-full h-[2px] bg-[#E5E5E5] my-2"></div>
                        @endif
                    @empty
                        <div class="text-center py-8 text-[#555555] flex flex-col items-center">
                            <div class="w-[56px] h-[56px] bg-[#F5F5F5] border-[2px] border-[#000000] rounded-full flex items-center justify-center text-[20px] mb-3"><i class="fa-solid fa-star text-[#F59E0B]"></i></div>
                            <h4 class="text-[16px] font-[800] text-[#111111]">Ready for New Challenges!</h4>
                            <p class="text-[13px] font-[500] mt-1">The teachers are preparing the next set of goals.</p>
                        </div>
                    @endforelse

                </div>
            </section>

            <!-- RIGHT COLUMN: CLASSROOM MESSAGES & SUPPORT -->
            <div class="flex flex-col gap-8">
                
                <!-- Classroom Messages -->
                <section class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-[32px] h-[32px] rounded-full bg-[#3B82F6]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#3B82F6]"><i class="fa-regular fa-comments"></i></div>
                        <h2 class="text-[16px] font-[800] text-[#111111]">Daily Log</h2>
                    </div>

                    <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] p-6 md:p-8 flex flex-col gap-6">
                        
                        @forelse($recentLogs ?? [] as $log)
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-[28px] h-[28px] rounded-full bg-[#F5F5F5] border-[2px] border-[#000000] flex items-center justify-center text-[11px] font-[800] text-[#111111]">
                                        {{ substr($log->teacher->name ?? 'T', 0, 1) }}
                                    </div>
                                    <h4 class="text-[14px] font-[800] text-[#111111]">Teacher {{ $log->teacher->name ?? 'Staff' }}</h4>
                                    <span class="text-[12px] font-[500] text-[#555555] ml-auto"><i class="fa-regular fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}</span>
                                </div>
                                <div class="bg-[#F8FAFC] border-[2px] border-[#000000] rounded-[12px] rounded-tl-none p-4 mt-1">
                                    <p class="text-[14px] font-[500] text-[#111111] leading-relaxed">{{ $log->notes }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-[#555555] flex flex-col items-center">
                                <div class="w-[56px] h-[56px] bg-[#F5F5F5] border-[2px] border-[#000000] rounded-full flex items-center justify-center text-[20px] mb-3"><i class="fa-regular fa-envelope-open"></i></div>
                                <h4 class="text-[16px] font-[800] text-[#111111]">No New Notes</h4>
                                <p class="text-[13px] font-[500] mt-1">We'll let you know when the teacher sends an update.</p>
                            </div>
                        @endforelse

                    </div>
                </section>

                <!-- Try this at Home -->
                <section class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-[32px] h-[32px] rounded-full bg-[#10B981]/10 border-[2px] border-[#000000] flex items-center justify-center text-[#10B981]"><i class="fa-solid fa-puzzle-piece"></i></div>
                        <h2 class="text-[16px] font-[800] text-[#111111]">Try this at Home</h2>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($homeActivities ?? [] as $activity)
                            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">{{ $activity->title ?? 'Practice Activity' }}</h3>
                                    <p class="text-[13px] font-[500] text-[#555555]">{{ $activity->description ?? 'Short description of the task.' }}</p>
                                </div>
                                <a href="{{ route('guardian.recommendations', $activity->id ?? '') }}" class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[8px] shadow-[2px_2px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-px hover:shadow-[3px_3px_0_0_rgba(0,0,0,0.20)] px-4 py-2 text-[13px] font-[800] text-[#111111] transition-all text-center whitespace-nowrap">
                                    View Activity
                                </a>
                            </div>
                        @empty
                            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Check Recommendations</h3>
                                    <p class="text-[13px] font-[500] text-[#555555]">Find engaging activities to do together.</p>
                                </div>
                                <a href="{{ route('guardian.recommendations') }}" class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[8px] shadow-[2px_2px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-px hover:shadow-[3px_3px_0_0_rgba(0,0,0,0.20)] px-4 py-2 text-[13px] font-[800] text-[#111111] transition-all text-center whitespace-nowrap">
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

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush

@endsection