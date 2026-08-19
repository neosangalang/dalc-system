@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Added max-w-5xl wrapper to keep everything perfectly proportioned and card-like -->
<div class="font-['Inter'] min-h-[100dvh] py-6 sm:py-8 w-full max-w-5xl mx-auto flex flex-col items-center">
    
    <!-- 1. WELCOME HEADER (Fixed: Changed back to visible colors) -->
    <div class="flex flex-wrap justify-between items-end mb-8 gap-4 w-full">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#5B8DEF] mb-1">System Overview 🚀</h2>
            <p class="text-[#6B7086] text-xs sm:text-sm font-medium">Welcome to the Dream Achievers Learning Center PH control panel.</p>
        </div>
    </div>

    <!-- 2. FOUR STAT CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10 w-full">
        
        <!-- Active Students (Blue Theme) -->
        <div class="group bg-gradient-to-br from-[#5B8DEF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#5B8DEF]/40 hover:border-[#5B8DEF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(91,141,239,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#5B8DEF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ $totalStudents ?? 0 }}</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Active Students</p>
        </div>
        
        <!-- Active Teachers (Purple Theme) -->
        <div class="group bg-gradient-to-br from-[#A874FF]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#A874FF]/40 hover:border-[#A874FF] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(168,116,255,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#A874FF] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ $activeTeachers ?? 0 }}</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Active Teachers</p>
        </div>

        <!-- Registered Guardians (Green Theme) -->
        <div class="group bg-gradient-to-br from-[#48C78E]/15 to-white/80 backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 border-[#48C78E]/40 hover:border-[#48C78E] shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(72,199,142,0.4)] flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm text-[#48C78E] group-hover:scale-110 transition-transform duration-300">
                <i class="fa-solid fa-heart"></i>
            </div>
            <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ $guardians ?? 0 }}</h3>
            <p class="text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center">Registered Guardians</p>
        </div>

        <!-- Reports Status (Dynamic Rose/Gold Theme) -->
        @php $hasPending = ($pendingReports ?? 0) > 0; @endphp
        <a href="{{ route('admin.report-approval.index') }}" class="block group text-decoration-none h-full focus-visible:outline-none w-full">
            <div class="bg-gradient-to-br {{ $hasPending ? 'from-rose-400/20 to-white/80 border-rose-400 hover:border-rose-500 hover:shadow-[0_8px_25px_-8px_rgba(244,63,94,0.4)]' : 'from-[#C4922E]/15 to-white/80 border-[#C4922E]/40 hover:border-[#C4922E] hover:shadow-[0_8px_25px_-8px_rgba(196,146,46,0.4)]' }} backdrop-blur-md rounded-2xl p-4 sm:p-5 border-2 shadow-sm flex flex-col items-center justify-center hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out h-full relative overflow-hidden">
                @if($hasPending)
                    <div class="absolute top-0 left-0 w-full h-1 bg-rose-400"></div>
                @endif
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-2.5 bg-white shadow-sm {{ $hasPending ? 'text-rose-500' : 'text-[#C4922E]' }} group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <h3 class="font-extrabold text-2xl text-[#2C2F45] m-0 leading-none">{{ $pendingReports ?? 0 }}</h3>
                <p class="text-[9px] font-extrabold uppercase tracking-widest mt-1.5 m-0 text-center {{ $hasPending ? 'text-rose-600' : 'text-[#6B7086]' }}">
                    {{ $hasPending ? 'Needs Review' : 'Reports Cleared' }}
                </p>
            </div>
        </a>
        
    </div>

    <!-- 3. SYSTEM MODULES SITTING EXACTLY BELOW THE NUMBER CARDS -->
    <div class="w-full pt-2 mb-6">
        
        <!-- Header Text (Fixed: Changed back to visible colors) -->
        <div class="flex items-center gap-3 justify-center mb-6 w-full">
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <h3 class="text-[10px] font-extrabold text-[#6B7086] uppercase tracking-[0.2em] whitespace-nowrap">System Modules</h3>
            <span class="w-1 h-1 rounded-full bg-[#C4922E]"></span>
            <span class="h-px flex-1 max-w-[48px] bg-[#C4922E]/40"></span>
        </div>

        <!-- ADMIN MODULES (5 Columns) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 sm:gap-5 max-w-5xl mx-auto w-full">
            
            <a href="{{ route('admin.students.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#1E2657]/10 to-white/90 backdrop-blur-md border-2 border-[#1E2657]/30 hover:border-[#1E2657] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(30,38,87,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#1E2657] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#1E2657] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#1E2657] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-users"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Student Profiles</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Manage Roster</p>
            </a>
            
            <a href="{{ route('admin.report-approval.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#C4922E]/10 to-white/90 backdrop-blur-md border-2 border-[#C4922E]/40 hover:border-[#C4922E] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(196,146,46,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#C4922E] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#C4922E] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#C4922E] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-file-circle-check"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Approvals</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Review Reports</p>
            </a>
            
            <a href="{{ route('admin.accounts.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#0E7C86]/10 to-white/90 backdrop-blur-md border-2 border-[#0E7C86]/30 hover:border-[#0E7C86] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(14,124,134,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#0E7C86] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#0E7C86] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#0E7C86] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-user-shield"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Users</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Manage Access</p>
            </a>
            
            <a href="{{ route('admin.quarters.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#4A5AA8]/10 to-white/90 backdrop-blur-md border-2 border-[#4A5AA8]/30 hover:border-[#4A5AA8] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(74,90,168,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#4A5AA8] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#4A5AA8] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#4A5AA8] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-calendar-days"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Calendar</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">System Terms</p>
            </a>
            
            <a href="{{ route('admin.archive.index') }}" class="group relative overflow-hidden bg-gradient-to-br from-[#8B8374]/10 to-white/90 backdrop-blur-md border-2 border-[#8B8374]/30 hover:border-[#8B8374] rounded-2xl p-4 pt-5 shadow-sm hover:shadow-[0_8px_25px_-8px_rgba(139,131,116,0.3)] hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300 ease-out flex flex-col items-center text-center">
                <span class="absolute top-0 left-0 right-0 h-[3px] bg-[#8B8374] scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300"></span>
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm text-[#8B8374] flex items-center justify-center text-lg mb-2.5 group-hover:bg-[#8B8374] group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fa-solid fa-box-archive"></i></div>
                <h4 class="text-[11px] font-extrabold text-[#2C2F45] uppercase tracking-wide">Archive</h4>
                <p class="text-[9px] font-bold text-[#6B7086] mt-0.5">Past Records</p>
            </a>
        </div>
    </div>

</div>

@endsection