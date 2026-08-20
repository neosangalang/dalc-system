@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- 1. WELCOME HEADER -->
        <header class="mb-8">
            <span class="text-[#5B8DEF] text-[11px] font-extrabold tracking-widest uppercase mb-2 block">Good Morning</span>
            <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Welcome back, admin.</h1>
            <p class="text-[#6B7086] text-sm font-medium">Here is your system overview for today.</p>
        </header>

        <!-- 2. STAT CARDS -->
        <section>
            <!-- Desktop: 4 columns, Mobile: 2 columns -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 w-full">
                
                <!-- Stat Card 1: Active Students (Blue) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#3B82F6] leading-none mb-2 counter">{{ $totalStudents ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Active Students</p>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 2: Active Teachers (Green) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#10B981] leading-none mb-2 counter">{{ $activeTeachers ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Active Teachers</p>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 3: Registered Guardians (Purple) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#8B5CF6] leading-none mb-2 counter">{{ $guardians ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Guardians</p>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 4: Reports Status (Orange/Gray) -->
                @php $hasPending = ($pendingReports ?? 0) > 0; @endphp
                <a href="{{ route('admin.report-approval.index') }}" class="block text-decoration-none focus-visible:outline-none w-full group">
                    <div class="bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 {{ $hasPending ? 'hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)]' : 'hover:border-black/30 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.15)]' }}">
                        <h3 class="font-extrabold text-[32px] {{ $hasPending ? 'text-[#F59E0B]' : 'text-[#D1D5DB]' }} leading-none mb-2 counter">{{ $pendingReports ?? 0 }}</h3>
                        <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">
                            {{ $hasPending ? 'Reports Due' : 'Reports Cleared' }}
                        </p>
                        <!-- Animated Center Line -->
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 {{ $hasPending ? 'bg-[#F59E0B]' : 'bg-[#D1D5DB]' }} transition-all duration-500 ease-out group-hover:w-full"></div>
                    </div>
                </a>

            </div>
        </section>

        <!-- 3. SYSTEM MODULES -->
        <section>
            <!-- Section Label -->
            <div class="flex items-center gap-4 mb-6">
                <span class="h-px w-8 bg-[#5B8DEF]/50"></span>
                <h2 class="text-[11px] font-extrabold text-[#5B8DEF] uppercase tracking-widest">System Modules</h2>
                <span class="h-px flex-1 bg-black/10"></span>
            </div>

            <!-- ADMIN MODULES -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 md:gap-5 w-full">
                
                <!-- Student Profiles -->
                <a href="{{ route('admin.students.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#3B82F6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#3B82F6] relative z-10">
                        <i class="fa-solid fa-users text-[#3B82F6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#3B82F6]">Student Profiles</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Manage Roster</p>
                    </div>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Approvals -->
                <a href="{{ route('admin.report-approval.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#10B981]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#10B981] relative z-10">
                        <i class="fa-solid fa-file-circle-check text-[#10B981] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#10B981]">Approvals</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Review Reports</p>
                    </div>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Users -->
                <a href="{{ route('admin.accounts.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10">
                        <i class="fa-solid fa-user-shield text-[#8B5CF6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">Users</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Manage Access</p>
                    </div>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Calendar -->
                <a href="{{ route('admin.quarters.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#F59E0B]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#F59E0B] relative z-10">
                        <i class="fa-solid fa-calendar-days text-[#F59E0B] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#F59E0B]">Calendar</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">System Terms</p>
                    </div>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F59E0B] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Archive -->
                <a href="{{ route('admin.archive.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#6B7086]/30 hover:shadow-[0_8px_20px_-6px_rgba(107,112,134,0.2)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#6B7086]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#6B7086] relative z-10">
                        <i class="fa-solid fa-box-archive text-[#6B7086] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#1E2657]">Archive</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Past Records</p>
                    </div>
                    <!-- Animated Center Line -->
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#6B7086] transition-all duration-500 ease-out group-hover:w-full"></div>
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
                if(isNaN(target)) return;
                
                let count = 0;
                const inc = Math.max(1, Math.ceil(target / 100)); 
                
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