@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- 1. WELCOME HERO BANNER (SCOPED BLUE DESIGN) -->
        <header class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5] p-6 sm:p-8 text-white shadow-lg border border-white/10 mb-8 sm:mb-10" x-data="{
            greeting: '',
            currentTime: '',
            updateGreeting() {
                const now = new Date();
                const hrs = now.getHours();
                if (hrs < 12) {
                    this.greeting = 'Good Morning';
                } else if (hrs < 18) {
                    this.greeting = 'Good Afternoon';
                } else {
                    this.greeting = 'Good Evening';
                }
                this.currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }" x-init="updateGreeting(); setInterval(() => updateGreeting(), 1000)">
            <!-- Ambient Decorative Background Glows -->
            <div class="absolute -right-10 -top-10 w-60 h-60 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-blue-100 text-[11px] font-extrabold tracking-widest uppercase mb-3">
                        <i class="fa-regular fa-sun text-[#F59E0B]" x-show="greeting === 'Good Morning'"></i>
                        <i class="fa-regular fa-sun text-[#EF4444]" x-show="greeting === 'Good Afternoon'"></i>
                        <i class="fa-regular fa-moon text-[#A78BFA]" x-show="greeting === 'Good Evening'"></i>
                        <span x-text="greeting">Good Morning</span>
                    </span>
                    <h1 class="text-white text-2xl sm:text-4xl font-extrabold tracking-tight mb-2">Welcome back, {{ Auth::user()->name ?? 'Admin' }}.</h1>
                    <p class="text-blue-100/85 text-xs sm:text-sm font-medium m-0">Here is your system overview for today.</p>
                </div>
            </div>
        </header>

        <!-- 2. STAT CARDS -->
        <section>
            <!-- Desktop: 4 columns, Mobile: 2 columns -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 w-full">
                
                <!-- Stat Card 1: Active Students (Blue) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#3B82F6] leading-none mb-1.5 sm:mb-2 counter">{{ $totalStudents ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Active Students</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 2: Active Teachers (Green) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#10B981] leading-none mb-1.5 sm:mb-2 counter">{{ $activeTeachers ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Active Teachers</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 3: Registered Guardians (Purple) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] active:scale-[0.98]">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] text-[#8B5CF6] leading-none mb-1.5 sm:mb-2 counter">{{ $guardians ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Guardians</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Stat Card 4: Reports Due -->
                @php $hasPending = ($pendingReports ?? 0) > 0; @endphp
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-4 sm:p-6 flex flex-col justify-center h-[110px] sm:h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 active:scale-[0.98] {{ $hasPending ? 'hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)]' : 'hover:border-black/30 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.15)]' }}">
                    <h3 class="font-extrabold text-[28px] sm:text-[32px] {{ $hasPending ? 'text-[#F59E0B]' : 'text-[#D1D5DB]' }} leading-none mb-1.5 sm:mb-2 counter">{{ $pendingReports ?? 0 }}</h3>
                    <p class="text-[#9CA0AF] text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Reports Due</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 {{ $hasPending ? 'bg-[#F59E0B]' : 'bg-[#D1D5DB]' }} transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

            </div>
        </section>

        <!-- 3. SYSTEM MODULES -->
        <section>
            <div class="flex items-center gap-4 mb-6">
                <span class="h-px w-8 bg-[#5B8DEF]/50"></span>
                <h2 class="text-[11px] font-extrabold text-[#5B8DEF] uppercase tracking-widest">System Modules</h2>
                <span class="h-px flex-1 bg-black/10"></span>
            </div>

            <!-- ADMIN MODULES (2 cols on mobile, 3 on md, 6 on xl) -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-5 w-full">
                
                <!-- Student Profiles -->
                <a href="{{ route('admin.students.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#3B82F6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#3B82F6] relative z-10">
                        <i class="fa-solid fa-users text-[#3B82F6] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#3B82F6]">Student Profiles</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">Manage Roster</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Approvals (WITH NEW RED INDICATOR) -->
                <a href="{{ route('admin.report-approval.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    
                    @if($hasPending)
                        <!-- Pulsating Red Indicator -->
                        <span class="absolute top-3 sm:top-4 right-3 sm:right-4 flex h-3 w-3 z-20" title="{{ $pendingReports }} Pending Reports">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                        </span>
                    @endif

                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#10B981]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#10B981] relative z-10">
                        <i class="fa-solid fa-file-circle-check text-[#10B981] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#10B981]">Approvals</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">Review Reports</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Users -->
                <a href="{{ route('admin.accounts.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10">
                        <i class="fa-solid fa-user-shield text-[#8B5CF6] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">Users</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">Manage Access</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Calendar -->
                <a href="{{ route('admin.quarters.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#F59E0B]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#F59E0B] relative z-10">
                        <i class="fa-solid fa-calendar-days text-[#F59E0B] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#F59E0B]">Calendar</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">System Terms</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F59E0B] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Announcements -->
                <a href="{{ route('admin.announcements.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10">
                        <i class="fa-solid fa-bullhorn text-[#8B5CF6] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">Announcements</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">Post Center Updates</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Archive -->
                <a href="{{ route('admin.archive.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#6B7086]/30 hover:shadow-[0_8px_20px_-6px_rgba(107,112,134,0.2)] hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 block p-4 sm:p-5 h-[125px] sm:h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-8 sm:w-9 h-8 sm:h-9 rounded-full bg-[#6B7086]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#6B7086] relative z-10">
                        <i class="fa-solid fa-box-archive text-[#6B7086] text-xs sm:text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[13px] sm:text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#1E2657]">Archive</h3>
                        <p class="text-[11px] sm:text-[12px] text-[#9CA0AF] font-medium m-0">Past Records</p>
                    </div>
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