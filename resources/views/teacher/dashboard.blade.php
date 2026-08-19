@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<!-- Main Background and Typography Container -->
<div class="bg-[#F5F5F5] min-h-[100dvh] py-8 md:py-10 w-full font-['Inter',sans-serif] text-[#111111]">
    <div class="max-w-[1200px] mx-auto px-4 md:px-6 lg:px-8 space-y-8 md:space-y-10">

        <!-- 1. WELCOME HEADER (Original Text & Variables Restored) -->
        <div class="flex flex-wrap justify-between items-end gap-4 w-full">
            <div>
                <h2 class="text-2xl md:text-3xl font-[800] tracking-tight mb-1 text-[#111111]">Welcome back, {{ Auth::user()->name ?? 'Teacher' }}! 👩‍🏫</h2>
                <p class="text-[#555555] font-[500] text-[15px]">Here is your classroom overview for today.</p>
            </div>
        </div>

        <!-- 2. FOUR STAT CARDS (Original Variables & Order Restored) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 w-full">
            
            <!-- Card 1: My Students (Blue Theme) -->
            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                <h3 class="font-[800] text-[36px] text-[#3B82F6] leading-none mb-2 counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
                <p class="text-[#555555] text-[12px] font-[800] uppercase tracking-[0.08em] m-0 text-center">My Students</p>
            </div>

            <!-- Card 2: Logs This Week (Green Theme) -->
            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                <h3 class="font-[800] text-[36px] text-[#10B981] leading-none mb-2 counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
                <p class="text-[#555555] text-[12px] font-[800] uppercase tracking-[0.08em] m-0 text-center">Logs This Week</p>
            </div>

            <!-- Card 3: Active IEP Goals (Purple Theme) -->
            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                <h3 class="font-[800] text-[36px] text-[#8B5CF6] leading-none mb-2 counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
                <p class="text-[#555555] text-[12px] font-[800] uppercase tracking-[0.08em] m-0 text-center">Active IEP Goals</p>
            </div>

            <!-- Card 4: Reports Due (Orange/Amber Theme) -->
            @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                <h3 class="font-[800] text-[36px] {{ $hasReports ? 'text-[#F59E0B]' : 'text-[#111111]' }} leading-none mb-2 counter" data-target="{{ $reportsDue ?? 0 }}">0</h3>
                <p class="text-[#555555] text-[12px] font-[800] uppercase tracking-[0.08em] m-0 text-center">Reports Due</p>
            </div>

        </div>

        <!-- 3. ACADEMIC CALENDAR (Original Logic & Structure Restored) -->
        <div>
            <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] overflow-hidden w-full mb-8">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b-[2px] border-[#000000] flex flex-col sm:flex-row justify-between items-start sm:items-center bg-[#F5F5F5]">
                    <h6 class="text-[14px] font-[800] text-[#111111] m-0 flex items-center">
                        <i class="fa fa-calendar-alt text-[#555555] mr-2"></i> Academic Calendar Timeline
                    </h6>
                    @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
                    @if($activeQ)
                        <span class="inline-flex items-center px-3 py-1.5 bg-[#3B82F6]/10 text-[#3B82F6] border-[2px] border-[#000000] shadow-[2px_2px_0_0_rgba(0,0,0,0.20)] text-[11px] font-[800] rounded-full uppercase tracking-[0.08em] mt-2 sm:mt-0">
                            Current: {{ $activeQ->name }}
                        </span>
                    @endif
                </div>
                
                <!-- Swipeable Carousel -->
                <div class="flex gap-5 overflow-x-auto snap-x snap-mandatory px-6 pb-8 pt-6 [&::-webkit-scrollbar]:h-[8px] [&::-webkit-scrollbar-track]:bg-[#F5F5F5] [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[#000000] hover:[&::-webkit-scrollbar-thumb]:bg-[#555555] [&::-webkit-scrollbar-thumb]:rounded-full">
                    @forelse($quarters ?? [] as $quarter)
                        @if($quarter->is_active)
                            <!-- Active Quarter -->
                            <div class="snap-center shrink-0 w-auto flex-1 min-w-[220px] p-5 rounded-[12px] bg-[#FFFFFF] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] border-[2px] border-[#000000] relative overflow-hidden transition-all hover:-translate-y-[2px] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)]">
                                <div class="flex justify-between items-center mb-4 relative z-10">
                                    <h5 class="text-[16px] font-[800] text-[#111111] m-0">{{ $quarter->name }}</h5>
                                    <span class="bg-[#10B981] text-[#FFFFFF] text-[10px] font-[800] px-2 py-1 rounded-full uppercase tracking-[0.08em] border-[2px] border-[#000000]">Active</span>
                                </div>
                                <p class="m-0 text-[13px] font-[600] text-[#555555]">
                                    {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                                    {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                                </p>
                            </div>
                        @else
                            <!-- Inactive Quarter -->
                            <div class="snap-center shrink-0 w-auto flex-1 min-w-[220px] p-5 rounded-[12px] border-[2px] border-[#E5E5E5] bg-[#FFFFFF] transition-all hover:border-[#000000]">
                                <h5 class="text-[16px] font-[800] text-[#555555] m-0 mb-4">{{ $quarter->name }}</h5>
                                <p class="m-0 text-[13px] font-[500] text-[#555555]">
                                    {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                                    {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                                </p>
                            </div>
                        @endif
                    @empty
                        <div class="w-full text-center py-6 text-[14px] font-[500] text-[#555555]">
                            No calendar data configured by the administrator yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. SYSTEM MODULES (Original 4 Modules Restored) -->
        <div>
            <div class="flex items-center gap-4 mb-6">
                <span class="h-[2px] w-8 bg-[#000000]"></span>
                <h2 class="text-[12px] font-[800] text-[#555555] uppercase tracking-[0.1em]">System Modules</h2>
                <span class="h-[2px] flex-1 bg-[#E5E5E5]"></span>
            </div>

            <!-- TEACHER MODULES (4 Columns) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 w-full">
                
                <!-- My Students -->
                <a href="{{ route('teacher.students.index') }}" class="group bg-[#FFFFFF] hover:bg-[#F5F5F5] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block p-6 relative overflow-hidden flex flex-col items-center text-center">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#3B82F6]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#3B82F6]">
                        <i class="fa-solid fa-users text-[#3B82F6] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[16px] font-[800] text-[#111111] mb-1">My Students</h3>
                    <p class="text-[14px] text-[#555555] font-[500]">Manage Class</p>
                </a>
                
                <!-- IEP Goals -->
                <a href="{{ route('teacher.iep-goals.index') }}" class="group bg-[#FFFFFF] hover:bg-[#F5F5F5] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block p-6 relative overflow-hidden flex flex-col items-center text-center">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#8B5CF6]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#8B5CF6]">
                        <i class="fa-solid fa-bullseye text-[#8B5CF6] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[16px] font-[800] text-[#111111] mb-1">IEP Goals</h3>
                    <p class="text-[14px] text-[#555555] font-[500]">Track Progress</p>
                </a>
                
                <!-- Daily Logs -->
                <a href="{{ route('teacher.daily-logs.index') }}" class="group bg-[#FFFFFF] hover:bg-[#F5F5F5] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block p-6 relative overflow-hidden flex flex-col items-center text-center">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#10B981]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#10B981]">
                        <i class="fa-solid fa-pencil text-[#10B981] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[16px] font-[800] text-[#111111] mb-1">Daily Logs</h3>
                    <p class="text-[14px] text-[#555555] font-[500]">Class Notes</p>
                </a>
                
                <!-- Reports -->
                <a href="{{ route('teacher.reports.index') }}" class="group bg-[#FFFFFF] hover:bg-[#F5F5F5] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block p-6 relative overflow-hidden flex flex-col items-center text-center">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#F59E0B]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#F59E0B]">
                        <i class="fa-solid fa-file-signature text-[#F59E0B] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[16px] font-[800] text-[#111111] mb-1">Reports</h3>
                    <p class="text-[14px] text-[#555555] font-[500]">Grading & Evals</p>
                </a>
                
            </div>
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