@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Main Background and Typography Container -->
<div class="bg-[#F5F5F5] min-h-[100dvh] py-8 md:py-10 w-full font-['Inter',sans-serif] text-[#111111]">
    <div class="max-w-[1200px] mx-auto px-4 md:px-6 lg:px-8 space-y-10">

        <!-- 1. WELCOME HEADER -->
        <header>
            <h2 class="text-2xl md:text-3xl font-[800] tracking-tight mb-1">System Overview 🚀</h2>
            <p class="text-[#555555] font-[500] text-[15px]">Welcome to the Dream Achievers Learning Center PH control panel.</p>
        </header>

        <!-- 2. STAT CARDS -->
        <section>
            <!-- Desktop: 4 columns, Mobile: 2 columns -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6 w-full">
                
                <!-- Stat Card 1: Active Students (Blue) -->
                <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                    <h3 class="font-[800] text-[36px] text-[#3B82F6] leading-none mb-2 counter">{{ $totalStudents ?? 0 }}</h3>
                    <p class="text-[#555555] text-[12px] font-[700] uppercase tracking-[0.08em] m-0 text-center">Active Students</p>
                </div>

                <!-- Stat Card 2: Active Teachers (Purple) -->
                <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                    <h3 class="font-[800] text-[36px] text-[#8B5CF6] leading-none mb-2 counter">{{ $activeTeachers ?? 0 }}</h3>
                    <p class="text-[#555555] text-[12px] font-[700] uppercase tracking-[0.08em] m-0 text-center">Active Teachers</p>
                </div>

                <!-- Stat Card 3: Registered Guardians (Green) -->
                <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                    <h3 class="font-[800] text-[36px] text-[#10B981] leading-none mb-2 counter">{{ $guardians ?? 0 }}</h3>
                    <p class="text-[#555555] text-[12px] font-[700] uppercase tracking-[0.08em] m-0 text-center">Registered Guardians</p>
                </div>

                <!-- Stat Card 4: Reports Status (Red if Pending, Orange if Clear) -->
                @php $hasPending = ($pendingReports ?? 0) > 0; @endphp
                <a href="{{ route('admin.report-approval.index') }}" class="block text-decoration-none h-full focus-visible:outline-none w-full">
                    <div class="bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out p-6 flex flex-col items-center justify-center h-[130px]">
                        <h3 class="font-[800] text-[36px] {{ $hasPending ? 'text-[#EF4444]' : 'text-[#F59E0B]' }} leading-none mb-2 counter">{{ $pendingReports ?? 0 }}</h3>
                        <p class="text-[#555555] text-[12px] font-[700] uppercase tracking-[0.08em] m-0 text-center">
                            {{ $hasPending ? 'Reports Pending Review' : 'Reports Cleared' }}
                        </p>
                    </div>
                </a>

            </div>
        </section>

        <!-- 3. SYSTEM MODULES -->
        <section>
            <!-- Section Label -->
            <div class="flex items-center gap-4 mb-6">
                <span class="h-[2px] w-8 bg-[#000000]"></span>
                <h2 class="text-[12px] font-[800] text-[#555555] uppercase tracking-[0.1em]">System Modules</h2>
                <span class="h-[2px] flex-1 bg-[#E5E5E5]"></span>
            </div>

            <!-- ADMIN MODULES (5 Columns mapping perfectly to the 5 distinct roles) -->
            <!-- Desktop: 5 cols, Tablet: 3 cols, Mobile: 2 cols -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 md:gap-6 w-full">
                
                <!-- Student Profiles (Blue) -->
                <a href="{{ route('admin.students.index') }}" class="group bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block h-[160px] p-5 relative overflow-hidden">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#3B82F6]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#3B82F6]">
                        <i class="fa-solid fa-users text-[#3B82F6] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Student Profiles</h3>
                    <p class="text-[13px] text-[#555555] font-[500]">Manage Roster</p>
                </a>
                
                <!-- Approvals (Green) -->
                <a href="{{ route('admin.report-approval.index') }}" class="group bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block h-[160px] p-5 relative overflow-hidden">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#10B981]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#10B981]">
                        <i class="fa-solid fa-file-circle-check text-[#10B981] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Approvals</h3>
                    <p class="text-[13px] text-[#555555] font-[500]">Review Reports</p>
                </a>
                
                <!-- Users (Purple) -->
                <a href="{{ route('admin.accounts.index') }}" class="group bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block h-[160px] p-5 relative overflow-hidden">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#8B5CF6]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#8B5CF6]">
                        <i class="fa-solid fa-user-shield text-[#8B5CF6] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Users</h3>
                    <p class="text-[13px] text-[#555555] font-[500]">Manage Access</p>
                </a>
                
                <!-- Calendar (Orange) -->
                <a href="{{ route('admin.quarters.index') }}" class="group bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block h-[160px] p-5 relative overflow-hidden">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#F59E0B]/10 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#F59E0B]">
                        <i class="fa-solid fa-calendar-days text-[#F59E0B] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Calendar</h3>
                    <p class="text-[13px] text-[#555555] font-[500]">System Terms</p>
                </a>
                
                <!-- Archive (Dark Gray) -->
                <a href="{{ route('admin.archive.index') }}" class="group bg-[#FFFFFF] border-[2px] border-[#000000] rounded-[16px] shadow-[4px_4px_0_0_rgba(0,0,0,0.20)] hover:shadow-[6px_6px_0_0_rgba(0,0,0,0.20)] hover:-translate-y-[2px] transition-all duration-150 ease-in-out block h-[160px] p-5 relative overflow-hidden">
                    <div class="w-[42px] h-[42px] rounded-lg bg-[#111111]/5 border-[2px] border-[#000000] flex items-center justify-center mb-4 transition-colors group-hover:bg-[#111111]">
                        <i class="fa-solid fa-box-archive text-[#111111] text-[18px] group-hover:text-[#FFFFFF]"></i>
                    </div>
                    <h3 class="text-[15px] font-[800] text-[#111111] mb-1">Archive</h3>
                    <p class="text-[13px] text-[#555555] font-[500]">Past Records</p>
                </a>
            </div>
        </section>

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
                if(isNaN(target)) return; // Only animate numbers
                
                let count = 0;
                const inc = Math.max(1, Math.ceil(target / 100)); // Ensure it increments even for small numbers
                
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