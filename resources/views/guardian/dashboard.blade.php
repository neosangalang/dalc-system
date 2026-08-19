@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<!-- Wrapper matching the 5xl max-width of the other dashboards -->
<div class="font-['Inter'] min-h-[100dvh] py-6 sm:py-8 w-full max-w-5xl mx-auto flex flex-col items-center">

    <!-- 1. WELCOME HEADER (Fixed: Changed back to visible colors) -->
    <div class="flex flex-wrap justify-between items-end mb-8 gap-4 w-full">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#5B8DEF] mb-1">Welcome back, {{ Auth::user()->name ?? 'Guardian' }}! 👋</h2>
            <p class="text-[#6B7086] text-xs sm:text-sm font-medium">Let's see what your little ones are up to today.</p>
        </div>
    </div>

    <!-- 2. THREE STAT CARDS (Glassy design with colored borders on hover) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5 mb-10 w-full">
        
        <!-- Enrolled Children (Blue Theme) -->
        <div class="group bg-gradient-to-br from-[#5B8DEF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#5B8DEF]/40 hover:border-[#5B8DEF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(91,141,239,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#5B8DEF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-child"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ isset($myChildren) ? $myChildren->count() : 0 }}</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Enrolled Children</p>
        </div>

        <!-- Active IEP Goals (Purple Theme) -->
        <div class="group bg-gradient-to-br from-[#A874FF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#A874FF]/40 hover:border-[#A874FF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(168,116,255,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#A874FF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ isset($activeGoals) ? $activeGoals->count() : 0 }}</h3> 
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Active IEP Goals</p>
        </div>

        <!-- Official Reports (Green Theme) -->
        <div class="group bg-gradient-to-br from-[#48C78E]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#48C78E]/40 hover:border-[#48C78E] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(72,199,142,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#48C78E] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ isset($recentReports) ? $recentReports->count() : 0 }}</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Official Reports</p>
        </div>

    </div>

    <!-- 3. SYSTEM MODULES (Sandwiched directly in the middle) -->
    <div class="w-full pt-2 mb-10">
        
        <!-- Header Text (Fixed: Changed back to visible colors) -->
        <div class="flex items-center gap-3 justify-center mb-6 w-full">
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <h3 class="text-[10px] font-extrabold text-[#6B7086] uppercase tracking-[0.2em] whitespace-nowrap">System Modules</h3>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
        </div>

        <!-- GUARDIAN MODULES (3 Columns) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 max-w-3xl mx-auto w-full">
            
            <!-- IEP Goals (Gold Theme) -->
            <a href="{{ route('guardian.goals') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#C4922E]/10 to-white/90 backdrop-blur-md border-2 border-[#C4922E]/40 hover:border-[#C4922E] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(196,146,46,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#C4922E] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#C4922E] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#C4922E] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-bullseye"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">IEP Goals</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Track Progress</p>
            </a>
            
            <!-- Report Cards Dropdown (Indigo Theme) -->
            <div x-data="{ open: false }" class="relative h-full w-full">
                <button @click="open = !open" @click.away="open = false" class="group relative overflow-hidden bg-gradient-to-br from-[#4A5AA8]/10 to-white/90 backdrop-blur-md border-2 border-[#4A5AA8]/30 hover:border-[#4A5AA8] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(74,90,168,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center w-full h-full focus-visible:outline-none">
                    <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#4A5AA8] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#4A5AA8] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#4A5AA8] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-file-signature"></i></div>
                    <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Reports <i class="fa fa-caret-down ml-1 text-[#6B7086] text-[9px]"></i></h4>
                    <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">View Evaluations</p>
                </button>
                <div x-show="open" x-transition x-cloak class="absolute top-[105%] left-1/2 -translate-x-1/2 w-[115%] bg-white/95 backdrop-blur-md border border-[#E8E5DC] rounded-xl shadow-xl z-50 p-2 text-center text-xs font-bold text-[#6B7086]">
                    <a href="{{ route('guardian.reports', ['type' => 'daily']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Daily Summaries</a>
                    <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Quarter 1</a>
                    <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Quarter 2</a>
                    <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Quarter 3</a>
                    <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Quarter 4</a>
                    <a href="{{ route('guardian.reports', ['type' => 'year_end']) }}" class="block p-2.5 hover:bg-[#EEF4FE] hover:text-[#4A5AA8] rounded-lg transition-colors">Year-End</a>
                </div>
            </div>
            
            <!-- Home Support (Teal Theme) -->
            <a href="{{ route('guardian.recommendations') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#0E7C86]/10 to-white/90 backdrop-blur-md border-2 border-[#0E7C86]/30 hover:border-[#0E7C86] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(14,124,134,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#0E7C86] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#0E7C86] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#0E7C86] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-hand-holding-heart"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Home Recommendation</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Activities & Tips</p>
            </a>
        </div>
    </div>

    <!-- 4. BOTTOM CONTENT CARDS -->
    <div class="flex flex-col gap-5 sm:gap-6 w-full">
        
        <!-- Goal Progress Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-[20px] sm:rounded-[24px] border-2 border-[#DCE6F9] shadow-lg overflow-hidden flex flex-col">
            <div class="p-4 sm:p-6 bg-[#F0E8FF] border-b-[3px] border-[#CBB3FF] flex items-center gap-3 sm:gap-4 shrink-0">
                <div class="w-10 h-10 bg-white text-[#9D7CFF] border-[2px] border-[#CBB3FF] rounded-xl flex items-center justify-center text-xl shrink-0">
                    <i class="fa fa-rocket"></i>
                </div>
                <div>
                    <h5 class="text-base sm:text-lg font-extrabold text-[#9D7CFF] m-0 leading-tight">{{ $activeChild->first_name ?? 'Student' }}'s Goal Progress</h5>
                    <p class="text-[12px] sm:text-sm font-semibold text-[#3B4A6B]/70 m-0">Watch them grow!</p>
                </div>
            </div>
            
            <div class="p-4 sm:p-6 bg-white flex flex-col gap-4 sm:gap-6">
                @forelse($activeGoals ?? [] as $goal)
                    <div class="p-4 border-[3px] border-[#EAF0FA] rounded-[16px] sm:rounded-[20px] shrink-0">
                        <div class="flex justify-between items-center mb-3">
                            <span class="px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-lg sm:rounded-xl font-extrabold text-[10px] sm:text-xs bg-[#F0E8FF] border-[2px] border-[#CBB3FF] text-[#9D7CFF] tracking-wide uppercase">{{ $goal->domain }}</span>
                            <span class="font-black text-lg sm:text-xl text-[#9D7CFF]">{{ $goal->progress_percentage }}%</span>
                        </div>
                        
                        <p class="text-[13px] sm:text-sm font-bold text-[#3B4A6B] mb-3 line-clamp-2 leading-relaxed">
                            {{ $goal->goal_description }}
                        </p>
                        
                        <div class="h-4 sm:h-5 bg-[#EEF2F9] rounded-full overflow-hidden shadow-inner border-[2px] border-[#DCE6F9]">
                            <div class="h-full rounded-full bg-[#9D7CFF] bg-[linear-gradient(45deg,rgba(255,255,255,.15)_25%,transparent_25%,transparent_50%,rgba(255,255,255,.15)_50%,rgba(255,255,255,.15)_75%,transparent_75%,transparent)] bg-[length:1rem_1rem] transition-all duration-500 ease-out" style="width: {{ $goal->progress_percentage }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 sm:py-8 text-[#7A89A8] border-[3px] border-dashed border-[#DCE6F9] rounded-[16px] sm:rounded-[20px]">
                        <i class="fa fa-moon text-4xl sm:text-5xl mb-3 opacity-25"></i>
                        <p class="text-[13px] sm:text-base font-extrabold m-0">No active goals to track right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Teacher Updates Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-[20px] sm:rounded-[24px] border-2 border-[#DCE6F9] shadow-lg overflow-hidden flex flex-col">
            <div class="p-4 sm:p-6 bg-[#DDF7E9] border-b-[3px] border-[#91E5B9] flex items-center gap-3 sm:gap-4 shrink-0">
                <div class="w-10 h-10 bg-white text-[#4CD18E] border-[2px] border-[#91E5B9] rounded-xl flex items-center justify-center text-xl shrink-0">
                    <i class="fa fa-book-open"></i>
                </div>
                <div>
                    <h5 class="text-base sm:text-lg font-extrabold text-[#4CD18E] m-0 leading-tight">Teacher Updates</h5>
                    <p class="text-[12px] sm:text-sm font-semibold text-[#3B4A6B]/70 m-0">Recent notes from the classroom</p>
                </div>
            </div>
            
            <div class="p-4 sm:p-6 bg-white flex flex-col gap-4">
                @forelse($recentLogs ?? [] as $log)
                    <div class="bg-[#F8FBFF] border-[3px] border-[#DCE6F9] rounded-[16px] sm:rounded-[20px] rounded-bl-sm p-4 sm:p-5 relative shrink-0">
                        <div class="flex flex-wrap justify-between items-center mb-3 gap-2">
                            <h6 class="text-[13px] sm:text-sm font-extrabold text-[#5C8DF6] m-0">{{ \Carbon\Carbon::parse($log->log_date)->format('l, F j') }}</h6>
                            <span class="px-2.5 sm:px-3 py-1 bg-white border-[2px] border-[#DCE6F9] rounded-lg sm:rounded-xl text-[10px] sm:text-xs font-bold text-[#7A89A8] shadow-sm">
                                <i class="fa fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-[13px] sm:text-sm font-semibold text-[#3B4A6B] leading-relaxed mb-4 whitespace-pre-line">{{ $log->notes }}</p>
                        
                        <div class="flex items-center gap-2 pt-3 border-t-[3px] border-dashed border-[#DCE6F9]">
                            <div class="w-6 h-6 bg-[#FFC043] text-white border-[2px] border-[#FDE093] rounded-full flex items-center justify-center text-[10px] font-black shadow-sm shrink-0">
                                {{ substr($log->teacher->name ?? 'T', 0, 1) }}
                            </div>
                            <span class="text-[11px] sm:text-xs font-extrabold text-[#7A89A8]">Teacher {{ $log->teacher->name ?? 'Staff' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 sm:py-10 border-[3px] border-dashed border-[#DCE6F9] rounded-[16px] sm:rounded-[20px] text-[#7A89A8]">
                        <p class="text-[13px] sm:text-base font-extrabold m-0">No recent updates from the teacher.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection