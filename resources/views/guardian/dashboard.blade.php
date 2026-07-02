@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Quicksand'] bg-[#F4F8FE] min-h-screen text-[#3B4A6B] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-[#5C8DF6] mb-1 m-0">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-[1.1rem] font-semibold text-[#7A89A8] m-0">Let's see what your little ones are up to today.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-5 py-2.5 bg-[#5C8DF6] text-white font-bold rounded-full border-[3px] border-[#4a7ae0] shadow-[0_4px_12px_rgba(92,141,246,0.3)] hover:bg-[#4a7ae0] transition-colors focus:outline-none">
                    <i class="fa fa-child text-lg"></i>
                    <span>Viewing: {{ $activeChild->first_name }}</span>
                    <i class="fa fa-chevron-down ml-1 text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <ul x-show="open" x-transition.opacity x-transition:enter.duration.200ms x-transition:leave.duration.150ms x-cloak class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl py-2 z-50 border-[3px] border-[#DCE6F9] list-none pl-0">
                    <li class="px-4 py-2 text-xs font-extrabold text-[#7A89A8] uppercase tracking-wider">MY CHILDREN</li>
                    @forelse($myChildren ?? [] as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center mx-2 px-3 py-2.5 mb-1 rounded-xl font-semibold transition-colors {{ (isset($activeChild) && $activeChild->id === $child->id) ? 'bg-[#E3EFFF] text-[#5C8DF6] border-[2px] border-[#5C8DF6]' : 'text-[#3B4A6B] hover:bg-slate-50 border-[2px] border-transparent hover:border-[#DCE6F9]' }}">
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="p-6 bg-white rounded-[24px] border-[3px] border-[#A9C4F8] flex items-center gap-5 transition-transform duration-300 hover:-translate-y-2 shadow-sm">
            <div class="w-16 h-16 rounded-[20px] bg-[#E3EFFF] text-[#5C8DF6] border-[2px] border-[#A9C4F8] flex items-center justify-center text-3xl shrink-0">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-[#5C8DF6] m-0">{{ isset($myChildren) ? $myChildren->count() : 0 }}</h3>
                <p class="text-[15px] font-bold text-[#7A89A8] m-0">Enrolled Children</p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-[24px] border-[3px] border-[#CBB3FF] flex items-center gap-5 transition-transform duration-300 hover:-translate-y-2 shadow-sm">
            <div class="w-16 h-16 rounded-[20px] bg-[#F0E8FF] text-[#9D7CFF] border-[2px] border-[#CBB3FF] flex items-center justify-center text-3xl shrink-0">
                <i class="fa fa-star"></i>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-[#9D7CFF] m-0">{{ isset($activeGoals) ? $activeGoals->count() : 0 }}</h3> 
                <p class="text-[15px] font-bold text-[#7A89A8] m-0">Active IEP Goals</p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-[24px] border-[3px] border-[#91E5B9] flex items-center gap-5 transition-transform duration-300 hover:-translate-y-2 shadow-sm">
            <div class="w-16 h-16 rounded-[20px] bg-[#DDF7E9] text-[#4CD18E] border-[2px] border-[#91E5B9] flex items-center justify-center text-3xl shrink-0">
                <i class="fa fa-certificate"></i>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-[#4CD18E] m-0">{{ isset($recentReports) ? $recentReports->count() : 0 }}</h3>
                <p class="text-[15px] font-bold text-[#7A89A8] m-0">Official Reports</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-5">
            <div class="bg-white rounded-[24px] border-[3px] border-[#DCE6F9] shadow-[0_10px_30px_rgba(92,141,246,0.08)] overflow-hidden h-full flex flex-col">
                <div class="p-6 bg-white border-b-[3px] border-dashed border-[#DCE6F9] flex items-center gap-4 shrink-0">
                    <div class="w-10 h-10 bg-[#FFF4D9] text-[#FFC043] border-[2px] border-[#FDE093] rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-id-badge"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-extrabold text-[#3B4A6B] m-0">Student Profiles</h5>
                        <p class="text-sm font-semibold text-[#7A89A8] m-0">Select a child to view their progress</p>
                    </div>
                </div>
                
                <div class="p-6 flex-1 flex flex-col gap-3 overflow-y-auto">
                    @forelse($myChildren ?? [] as $child)
                        @php
                            $isActive = isset($activeChild) && $activeChild->id === $child->id;
                        @endphp
                        
                        <a href="{{ route('guardian.switch-child', $child->id) }}" class="block decoration-transparent outline-none">
                            <div class="flex items-center gap-4 p-4 rounded-[20px] border-[3px] transition-all duration-300 cursor-pointer group {{ $isActive ? 'bg-[#5C8DF6] border-[#4a7ae0] text-white shadow-[0_8px_20px_rgba(92,141,246,0.3)]' : 'border-[#DCE6F9] hover:bg-[#E3EFFF] hover:border-[#5C8DF6]' }}">
                                
                                <div class="w-14 h-14 rounded-full flex items-center justify-center font-extrabold text-xl shadow-sm shrink-0 border-[2px] transition-colors {{ $isActive ? 'bg-white text-[#5C8DF6] border-[#4a7ae0]' : 'bg-[#FFC043] text-white border-[#FDE093]' }}">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                </div>
                                
                                <div class="flex-1">
                                    <h5 class="text-base font-extrabold m-0 {{ $isActive ? 'text-white' : 'text-[#3B4A6B]' }}">{{ $child->first_name }} {{ $child->last_name }}</h5>
                                    <div class="flex gap-4 text-[13px] font-bold mt-1 {{ $isActive ? 'text-white/90' : 'text-[#7A89A8]' }}">
                                        <span><i class="fa fa-birthday-cake mr-1"></i> {{ \Carbon\Carbon::parse($child->date_of_birth)->age }} yrs old</span>
                                        <span><i class="fa fa-chalkboard-teacher mr-1"></i> {{ $child->teacher->name ?? 'Unassigned' }}</span>
                                    </div>
                                </div>
                                
                                @if($isActive) 
                                    <div class="w-8 h-8 rounded-full bg-white/20 border-[2px] border-white/30 flex items-center justify-center shrink-0">
                                        <i class="fa fa-chevron-right text-white text-sm"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-10 text-[#7A89A8] border-[3px] border-dashed border-[#DCE6F9] rounded-[20px]">
                            <i class="fa fa-ghost text-5xl mb-3 opacity-25"></i>
                            <p class="text-base font-extrabold m-0">No children linked yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 flex flex-col gap-6">
            
            <div class="bg-white rounded-[24px] border-[3px] border-[#DCE6F9] shadow-[0_10px_30px_rgba(92,141,246,0.08)] overflow-hidden">
                <div class="p-6 bg-[#F0E8FF] border-b-[3px] border-[#CBB3FF] flex items-center gap-4">
                    <div class="w-10 h-10 bg-white text-[#9D7CFF] border-[2px] border-[#CBB3FF] rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-rocket"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-extrabold text-[#9D7CFF] m-0">{{ $activeChild->first_name ?? 'Student' }}'s Goal Progress</h5>
                        <p class="text-sm font-semibold text-[#3B4A6B]/70 m-0">Watch them grow!</p>
                    </div>
                </div>
                
                <div class="p-6 bg-white flex flex-col gap-6">
                    @forelse($activeGoals ?? [] as $goal)
                        <div class="p-4 border-[3px] border-[#EAF0FA] rounded-[20px]">
                            <div class="flex justify-between items-center mb-3">
                                <span class="px-3.5 py-1.5 rounded-xl font-extrabold text-xs bg-[#F0E8FF] border-[2px] border-[#CBB3FF] text-[#9D7CFF] tracking-wide">{{ $goal->domain }}</span>
                                <span class="font-black text-xl text-[#9D7CFF]">{{ $goal->progress_percentage }}%</span>
                            </div>
                            
                            <p class="text-sm font-bold text-[#3B4A6B] mb-3 line-clamp-2 leading-relaxed">
                                {{ $goal->goal_description }}
                            </p>
                            
                            <div class="h-5 bg-[#EEF2F9] rounded-full overflow-hidden shadow-inner border-[2px] border-[#DCE6F9]">
                                <div class="h-full rounded-full bg-[#9D7CFF] bg-[linear-gradient(45deg,rgba(255,255,255,.15)_25%,transparent_25%,transparent_50%,rgba(255,255,255,.15)_50%,rgba(255,255,255,.15)_75%,transparent_75%,transparent)] bg-[length:1rem_1rem] transition-all duration-500 ease-out" style="width: {{ $goal->progress_percentage }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-[#7A89A8] border-[3px] border-dashed border-[#DCE6F9] rounded-[20px]">
                            <i class="fa fa-moon text-5xl mb-3 opacity-25"></i>
                            <p class="text-base font-extrabold m-0">No active goals to track right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-[24px] border-[3px] border-[#DCE6F9] shadow-[0_10px_30px_rgba(92,141,246,0.08)] overflow-hidden">
                <div class="p-6 bg-[#DDF7E9] border-b-[3px] border-[#91E5B9] flex items-center gap-4">
                    <div class="w-10 h-10 bg-white text-[#4CD18E] border-[2px] border-[#91E5B9] rounded-xl flex items-center justify-center text-xl shrink-0">
                        <i class="fa fa-book-open"></i>
                    </div>
                    <div>
                        <h5 class="text-lg font-extrabold text-[#4CD18E] m-0">Teacher Updates</h5>
                        <p class="text-sm font-semibold text-[#3B4A6B]/70 m-0">Recent notes from the classroom</p>
                    </div>
                </div>
                
                <div class="p-6 bg-white flex flex-col gap-4">
                    @forelse($recentLogs ?? [] as $log)
                        <div class="bg-[#F8FBFF] border-[3px] border-[#DCE6F9] rounded-[20px] rounded-bl-sm p-5 relative">
                            <div class="flex justify-between items-center mb-3">
                                <h6 class="text-sm font-extrabold text-[#5C8DF6] m-0">{{ \Carbon\Carbon::parse($log->log_date)->format('l, F j') }}</h6>
                                <span class="px-3 py-1 bg-white border-[2px] border-[#DCE6F9] rounded-xl text-xs font-bold text-[#7A89A8] shadow-sm">
                                    <i class="fa fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-[#3B4A6B] leading-relaxed mb-4 whitespace-pre-line">{{ $log->notes }}</p>
                            
                            <div class="flex items-center gap-2 pt-3 border-t-[3px] border-dashed border-[#DCE6F9]">
                                <div class="w-6 h-6 bg-[#FFC043] text-white border-[2px] border-[#FDE093] rounded-full flex items-center justify-center text-[10px] font-black shadow-sm shrink-0">
                                    {{ substr($log->teacher->name ?? 'T', 0, 1) }}
                                </div>
                                <span class="text-xs font-extrabold text-[#7A89A8]">Teacher {{ $log->teacher->name ?? 'Staff' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 border-[3px] border-dashed border-[#DCE6F9] rounded-[20px] text-[#7A89A8]">
                            <p class="text-base font-extrabold m-0">No recent updates from the teacher.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>

@endsection