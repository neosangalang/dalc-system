@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<!-- Wrapper to keep everything perfectly proportioned -->
<div class="font-['Inter'] min-h-[100dvh] py-6 sm:py-8 w-full max-w-5xl mx-auto flex flex-col items-center">

    <!-- 1. WELCOME HEADER -->
    <div class="flex flex-wrap justify-between items-end mb-8 gap-4 w-full">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#5B8DEF] mb-1">Welcome back, {{ Auth::user()->name ?? 'Teacher' }}! 👩‍🏫</h2>
            <p class="text-[#6B7086] text-xs sm:text-sm font-medium">Here is your classroom overview for today.</p>
        </div>
    </div>

    <!-- 2. FOUR STAT CARDS (Now with themed gradients and colored borders) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10 w-full">
        
        <!-- Card 1: My Students (Blue Theme) -->
        <div class="group bg-gradient-to-br from-[#5B8DEF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#5B8DEF]/40 hover:border-[#5B8DEF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(91,141,239,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#5B8DEF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">My Students</p>
        </div>

        <!-- Card 2: Logs This Week (Green Theme) -->
        <div class="group bg-gradient-to-br from-[#48C78E]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#48C78E]/40 hover:border-[#48C78E] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(72,199,142,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#48C78E] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-pencil"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Logs This Week</p>
        </div>

        <!-- Card 3: Active IEP Goals (Purple Theme) -->
        <div class="group bg-gradient-to-br from-[#A874FF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#A874FF]/40 hover:border-[#A874FF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(168,116,255,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#A874FF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Active IEP Goals</p>
        </div>

        <!-- Card 4: Reports Due (Amber Theme) -->
        @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
        <div class="group bg-gradient-to-br {{ $hasReports ? 'from-amber-400/20 to-white/80 border-amber-400 hover:border-amber-500 hover:shadow-[0_8px_25px_-8px_rgba(251,191,36,0.4)]' : 'from-[#C4922E]/15 to-white/80 border-[#C4922E]/40 hover:border-[#C4922E] hover:shadow-[0_8px_25px_-8px_rgba(196,146,46,0.4)]' }} backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 shadow-sm flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out relative overflow-hidden w-full">
            @if($hasReports)
                <div class="absolute top-0 left-0 w-full h-1 bg-amber-400"></div>
            @endif
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm {{ $hasReports ? 'text-amber-500' : 'text-[#C4922E]' }} group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none counter {{ $hasReports ? 'text-amber-600' : 'text-[#2C2F45]' }}" data-target="{{ $reportsDue ?? 0 }}">0</h3>
            <p class="text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center {{ $hasReports ? 'text-amber-600' : 'text-[#6B7086]' }}">Reports Due</p>
        </div>

    </div>

    <!-- 3. MIDDLE: ACADEMIC CALENDAR (Light Indigo Theme Wrapper) -->
    <div class="bg-gradient-to-br from-[#5B8DEF]/5 to-white/90 backdrop-blur-md rounded-2xl border-2 border-[#5B8DEF]/30 shadow-sm overflow-hidden mb-10 w-full">
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-[#5B8DEF]/20 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white/50">
            <h6 class="text-sm font-extrabold text-[#2C2F45] m-0 flex items-center">
                <i class="fa fa-calendar-alt text-[#5B8DEF] mr-2"></i> Academic Calendar Timeline
            </h6>
            @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
            @if($activeQ)
                <span class="inline-flex items-center px-3 py-1 bg-white text-[#5B8DEF] border border-[#5B8DEF]/30 shadow-sm text-[10px] font-extrabold rounded-full tracking-wide mt-2 sm:mt-0">
                    Current: {{ $activeQ->name }}
                </span>
            @endif
        </div>
        
        <!-- Swipeable Carousel -->
        <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory px-5 pb-6 pt-5 [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-track]:bg-[#5B8DEF]/10 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[#5B8DEF]/40 hover:[&::-webkit-scrollbar-thumb]:bg-[#5B8DEF]/60 [&::-webkit-scrollbar-thumb]:rounded-full">
            @forelse($quarters ?? [] as $quarter)
                @if($quarter->is_active)
                    <div class="snap-center shrink-0 w-auto flex-1 min-w-[180px] p-4 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#4A5AA8] text-white shadow-md shadow-[#1E2657]/20 transform scale-[1.02] border border-[#5B8DEF]/50 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/10 rounded-full blur-xl"></div>
                        <div class="flex justify-between items-center mb-2 relative z-10">
                            <h5 class="text-base font-extrabold m-0">{{ $quarter->name }}</h5>
                            <span class="bg-[#C4922E] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full tracking-widest shadow-sm">ACTIVE</span>
                        </div>
                        <p class="m-0 text-[11px] font-bold text-white/90 relative z-10">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                        </p>
                    </div>
                @else
                    <div class="snap-center shrink-0 w-auto flex-1 min-w-[180px] p-4 rounded-xl border-2 border-[#E8E5DC] bg-white/70 transition-all hover:bg-white hover:border-[#5B8DEF]/40 shadow-sm hover:shadow-md">
                        <h5 class="text-base font-extrabold text-[#2C2F45] m-0 mb-2">{{ $quarter->name }}</h5>
                        <p class="m-0 text-[11px] font-bold text-[#6B7086]">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                        </p>
                    </div>
                @endif
            @empty
                <div class="w-full text-center py-4 text-xs font-medium text-[#6B7086]">
                    No calendar data configured by the administrator yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 4. BOTTOM: SYSTEM MODULES -->
    <div class="w-full pt-2 mb-6">
        <div class="flex items-center gap-3 justify-center mb-6">
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <h3 class="text-[10px] font-extrabold text-[#6B7086] uppercase tracking-[0.2em] whitespace-nowrap">System Modules</h3>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
        </div>

        <!-- TEACHER MODULES (4 Columns with Individual Theme Colors) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5 max-w-3xl mx-auto">
            
            <!-- My Students (Navy Theme) -->
            <a href="{{ route('teacher.students.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#1E2657]/10 to-white/90 backdrop-blur-md border-2 border-[#1E2657]/30 hover:border-[#1E2657] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(30,38,87,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#1E2657] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#1E2657] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#1E2657] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-users"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">My Students</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Manage Class</p>
            </a>
            
            <!-- IEP Goals (Gold Theme) -->
            <a href="{{ route('teacher.iep-goals.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#C4922E]/10 to-white/90 backdrop-blur-md border-2 border-[#C4922E]/40 hover:border-[#C4922E] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(196,146,46,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#C4922E] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#C4922E] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#C4922E] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-bullseye"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">IEP Goals</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Track Progress</p>
            </a>
            
            <!-- Daily Logs (Teal Theme) -->
            <a href="{{ route('teacher.daily-logs.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#0E7C86]/10 to-white/90 backdrop-blur-md border-2 border-[#0E7C86]/30 hover:border-[#0E7C86] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(14,124,134,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#0E7C86] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#0E7C86] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#0E7C86] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-pencil"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Daily Logs</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Class Notes</p>
            </a>
            
            <!-- Reports (Indigo Theme) -->
            <a href="{{ route('teacher.reports.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#4A5AA8]/10 to-white/90 backdrop-blur-md border-2 border-[#4A5AA8]/30 hover:border-[#4A5AA8] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(74,90,168,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#4A5AA8] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#4A5AA8] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#4A5AA8] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-file-signature"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Reports</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Grading & Evals</p>
            </a>
            
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
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 200;
                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target;
                }
            };
            setTimeout(updateCount, 100);
        });
    });
</script>
@endpush