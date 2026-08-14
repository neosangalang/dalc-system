@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Added large bottom padding (pb-24) on mobile to prevent the bottom from being cut off -->
<div class="font-['Quicksand'] bg-[#F4F8FE] min-h-[100dvh] text-[#3B4A6B] pt-6 pb-24 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-5 sm:mb-8 gap-4 sm:gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#5C8DF6] mb-1 m-0 leading-tight">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-[1rem] sm:text-[1.1rem] font-semibold text-[#7A89A8] m-0 leading-tight">Let's see what your little ones are up to today.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div x-data="{ open: false }" class="relative w-full md:w-auto">
                <!-- Dropdown Button: Full width on mobile -->
                <button @click="open = !open" @click.away="open = false" class="w-full md:w-auto flex items-center justify-between md:justify-center gap-2 px-5 py-3 sm:py-2.5 bg-[#5C8DF6] text-white font-bold rounded-2xl md:rounded-full border-[3px] border-[#4a7ae0] shadow-[0_4px_12px_rgba(92,141,246,0.3)] hover:bg-[#4a7ae0] transition-colors focus:outline-none">
                    <div class="flex items-center gap-2">
                        <i class="fa fa-child text-lg"></i>
                        <span>Viewing: {{ $activeChild->first_name }}</span>
                    </div>
                    <i class="fa fa-chevron-down ml-1 text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Dropdown Menu: Full width on mobile -->
                <ul x-show="open" x-transition.opacity x-transition:enter.duration.200ms x-transition:leave.duration.150ms x-cloak class="absolute right-0 mt-2 w-full md:w-64 bg-white rounded-2xl shadow-xl py-2 z-50 border-[3px] border-[#DCE6F9] list-none pl-0">
                    <li class="px-4 py-2 text-xs font-extrabold text-[#7A89A8] uppercase tracking-wider">MY CHILDREN</li>
                    @forelse($myChildren ?? [] as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center mx-2 px-3 py-3 md:py-2.5 mb-1 rounded-xl font-semibold transition-colors {{ (isset($activeChild) && $activeChild->id === $child->id) ? 'bg-[#E3EFFF] text-[#5C8DF6] border-[2px] border-[#5C8DF6]' : 'text-[#3B4A6B] hover:bg-slate-50 border-[2px] border-transparent hover:border-[#DCE6F9]' }}">
                                <i class="fa fa-check-circle mr-2 {{ (isset($activeChild) && $activeChild->id === $child->id) ? 'text-[#5C8DF6]' : 'text-transparent' }}"></i>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </a>
                        </li>
                    @empty
                        <li class="px-4 py-2 text-sm text-slate-500">No children linked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        @endif
    </div>

    <!-- STATS GRID: Compact Box-Only Design -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-5 mb-6 sm:mb-8">
        
        <!-- Enrolled Children Box -->
        <div class="p-4 sm:p-5 bg-white rounded-[16px] sm:rounded-[20px] border-[3px] border-[#A9C4F8] flex flex-col items-center justify-center text-center transition-transform duration-300 hover:-translate-y-1 shadow-sm">
            <h3 class="text-3xl sm:text-4xl font-extrabold text-[#5C8DF6] m-0 mb-1 leading-none">{{ isset($myChildren) ? $myChildren->count() : 0 }}</h3>
            <p class="text-[11px] sm:text-[12px] font-extrabold text-[#7A89A8] m-0 uppercase tracking-widest">Enrolled Children</p>
        </div>

        <!-- Active IEP Goals Box -->
        <div class="p-4 sm:p-5 bg-white rounded-[16px] sm:rounded-[20px] border-[3px] border-[#CBB3FF] flex flex-col items-center justify-center text-center transition-transform duration-300 hover:-translate-y-1 shadow-sm">
            <h3 class="text-3xl sm:text-4xl font-extrabold text-[#9D7CFF] m-0 mb-1 leading-none">{{ isset($activeGoals) ? $activeGoals->count() : 0 }}</h3> 
            <p class="text-[11px] sm:text-[12px] font-extrabold text-[#7A89A8] m-0 uppercase tracking-widest">Active IEP Goals</p>
        </div>

        <!-- Official Reports Box -->
        <div class="p-4 sm:p-5 bg-white rounded-[16px] sm:rounded-[20px] border-[3px] border-[#91E5B9] flex flex-col items-center justify-center text-center transition-transform duration-300 hover:-translate-y-1 shadow-sm">
            <h3 class="text-3xl sm:text-4xl font-extrabold text-[#4CD18E] m-0 mb-1 leading-none">{{ isset($recentReports) ? $recentReports->count() : 0 }}</h3>
            <p class="text-[11px] sm:text-[12px] font-extrabold text-[#7A89A8] m-0 uppercase tracking-widest">Official Reports</p>
        </div>

    </div>

    <!-- MAIN CONTENT CARDS -->
    <div class="flex flex-col gap-5 sm:gap-6">
        
        <!-- Goal Progress Card -->
        <div class="bg-white rounded-[20px] sm:rounded-[24px] border-[3px] border-[#DCE6F9] shadow-[0_10px_30px_rgba(92,141,246,0.08)] overflow-hidden flex flex-col">
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
        <div class="bg-white rounded-[20px] sm:rounded-[24px] border-[3px] border-[#DCE6F9] shadow-[0_10px_30px_rgba(92,141,246,0.08)] overflow-hidden flex flex-col">
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