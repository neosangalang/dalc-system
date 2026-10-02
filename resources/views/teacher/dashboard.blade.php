@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- 1. WELCOME HEADER -->
        <header class="mb-8">
            <!-- Dynamic Time Greeting Span -->
            <span id="dynamic-greeting" class="text-[#5B8DEF] text-[11px] font-extrabold tracking-widest uppercase mb-2 block">Good Morning</span>
            
            <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Welcome back, {{ Auth::user()->name ?? 'teacher' }}.</h1>
            <p class="text-[#6B7086] text-sm font-medium">Here is your classroom overview for today.</p>
        </header>

        <!-- 2. FIVE STAT CARDS -->
        <section>
            <!-- Updated to lg:grid-cols-5 so all 5 cards fit in one row -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-5 w-full">
                
                <!-- Card 1: My Students (Blue Theme) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#3B82F6] leading-none mb-2 counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">My Students</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Card 2: Logs This Week (Green Theme) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#10B981] leading-none mb-2 counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Logs This Week</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Card 3: Active IEP Goals (Purple Theme) -->
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)]">
                    <h3 class="font-extrabold text-[32px] text-[#8B5CF6] leading-none mb-2 counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Active IEP Goals</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </div>

                <!-- Card 4: Announcements Stat Card -->
                <a href="/teacher/announcements" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-center relative overflow-hidden h-[120px] block">
                    
                    @if(isset($newAnnouncementsCount) && $newAnnouncementsCount > 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center justify-center min-w-[24px] z-20">
                            {{ $newAnnouncementsCount }}
                        </div>
                    @endif

                    <h3 class="font-extrabold text-[32px] text-[#8B5CF6] group-hover:text-[#8B5CF6] transition-colors duration-300 leading-none mb-2">
                        <i class="fa-solid fa-bullhorn text-[28px]"></i>
                    </h3>
                    
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Announcements</p>

                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>

                <!-- Card 5: Reports Due (Orange/Amber Theme) -->
                @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
                <div class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-center h-[120px] relative overflow-hidden transition-all duration-300 ease-out hover:-translate-y-1 {{ $hasReports ? 'hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)]' : 'hover:border-black/30 hover:shadow-[0_8px_20px_-6px_rgba(0,0,0,0.15)]' }}">
                    <h3 class="font-extrabold text-[32px] {{ $hasReports ? 'text-[#F59E0B]' : 'text-[#D1D5DB]' }} leading-none mb-2 counter" data-target="{{ $reportsDue ?? 0 }}">0</h3>
                    <p class="text-[#9CA0AF] text-[10px] font-extrabold uppercase tracking-widest m-0 relative z-10">Reports Due</p>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 {{ $hasReports ? 'bg-[#F59E0B]' : 'bg-[#D1D5DB]' }} transition-all duration-500 ease-out group-hover:w-full"></div>
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

            <!-- TEACHER MODULES -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 w-full">
                
                <!-- My Students -->
                <a href="{{ route('teacher.students.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#3B82F6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#3B82F6] relative z-10">
                        <i class="fa-solid fa-users text-[#3B82F6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#3B82F6]">My Students</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Manage Class</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- IEP Goals (Updated with Notification Badge) -->
                <a href="{{ route('teacher.iep-goals.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    
                    @if(isset($unreadIepCommentsCount) && $unreadIepCommentsCount > 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center justify-center min-w-[24px] z-20 animate-pulse">
                            {{ $unreadIepCommentsCount }}
                        </div>
                    @endif

                    <div class="w-9 h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10">
                        <i class="fa-solid fa-bullseye text-[#8B5CF6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">IEP Goals</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Track Progress</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Daily Logs (Updated with Notification Badge) -->
                <a href="{{ route('teacher.daily-logs.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    
                    @if(isset($unreadDailyLogCommentsCount) && $unreadDailyLogCommentsCount > 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-sm flex items-center justify-center min-w-[24px] z-20 animate-pulse">
                            {{ $unreadDailyLogCommentsCount }}
                        </div>
                    @endif

                    <div class="w-9 h-9 rounded-full bg-[#10B981]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#10B981] relative z-10">
                        <i class="fa-solid fa-pencil text-[#10B981] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#10B981]">Daily Logs</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Daily Notes & List</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
                <!-- Reports -->
                <a href="{{ route('teacher.reports.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-[#F59E0B]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#F59E0B] relative z-10">
                        <i class="fa-solid fa-file-signature text-[#F59E0B] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#F59E0B]">Reports</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Report List</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F59E0B] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                
            </div>
        </section>

        <!-- 3.5 ADMIN MODULES (Only visible if teacher has any admin-granted permissions) -->
        @php
            $user = Auth::user();
            $hasAnyAdminPermission = $user->can_approve_reports || $user->can_manage_credentials || $user->can_manage_calendar || $user->can_create_profiles || $user->can_archive_students;
        @endphp

        @if($hasAnyAdminPermission)
        <section>
            <div class="flex items-center gap-4 mb-6">
                <span class="h-px w-8 bg-[#C4922E]/50"></span>
                <h2 class="text-[11px] font-extrabold text-[#C4922E] uppercase tracking-widest">Admin Modules</h2>
                <span class="text-[9px] font-bold text-[#C4922E]/60 uppercase tracking-wider bg-[#C4922E]/10 px-2.5 py-1 rounded-full border border-[#C4922E]/20">Granted by Administrator</span>
                <span class="h-px flex-1 bg-black/10"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-5 w-full">
                
                {{-- Report Approvals --}}
                @if($user->can_approve_reports)
                <a href="{{ route('admin.report-approval.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#10B981]/30 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    {{-- Admin badge --}}
                    <span class="absolute top-3 right-3 text-[8px] font-extrabold text-[#C4922E] bg-[#C4922E]/10 px-1.5 py-0.5 rounded uppercase tracking-widest border border-[#C4922E]/20 z-20">Admin</span>
                    <div class="w-9 h-9 rounded-full bg-[#10B981]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#10B981] relative z-10">
                        <i class="fa-solid fa-file-circle-check text-[#10B981] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#10B981]">Approvals</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Review Reports</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#10B981] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                @endif

                {{-- User Accounts --}}
                @if($user->can_manage_credentials)
                <a href="{{ route('admin.accounts.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#8B5CF6]/30 hover:shadow-[0_8px_20px_-6px_rgba(139,92,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <span class="absolute top-3 right-3 text-[8px] font-extrabold text-[#C4922E] bg-[#C4922E]/10 px-1.5 py-0.5 rounded uppercase tracking-widest border border-[#C4922E]/20 z-20">Admin</span>
                    <div class="w-9 h-9 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#8B5CF6] relative z-10">
                        <i class="fa-solid fa-user-shield text-[#8B5CF6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#8B5CF6]">User Accounts</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Manage Access</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#8B5CF6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                @endif

                {{-- Academic Calendar --}}
                @if($user->can_manage_calendar)
                <a href="{{ route('admin.quarters.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#F59E0B]/30 hover:shadow-[0_8px_20px_-6px_rgba(245,158,11,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <span class="absolute top-3 right-3 text-[8px] font-extrabold text-[#C4922E] bg-[#C4922E]/10 px-1.5 py-0.5 rounded uppercase tracking-widest border border-[#C4922E]/20 z-20">Admin</span>
                    <div class="w-9 h-9 rounded-full bg-[#F59E0B]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#F59E0B] relative z-10">
                        <i class="fa-solid fa-calendar-days text-[#F59E0B] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#F59E0B]">Calendar</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">System Terms</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#F59E0B] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                @endif

                {{-- Student Profiles (Create) --}}
                @if($user->can_create_profiles)
                <a href="{{ route('admin.students.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#3B82F6]/30 hover:shadow-[0_8px_20px_-6px_rgba(59,130,246,0.25)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <span class="absolute top-3 right-3 text-[8px] font-extrabold text-[#C4922E] bg-[#C4922E]/10 px-1.5 py-0.5 rounded uppercase tracking-widest border border-[#C4922E]/20 z-20">Admin</span>
                    <div class="w-9 h-9 rounded-full bg-[#3B82F6]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#3B82F6] relative z-10">
                        <i class="fa-solid fa-user-plus text-[#3B82F6] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#3B82F6]">Student Profiles</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Create & Manage</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#3B82F6] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                @endif

                {{-- Archive --}}
                @if($user->can_archive_students)
                <a href="{{ route('admin.archive.index') }}" class="group bg-white border border-black/15 rounded-2xl shadow-[0_4px_12px_-4px_rgba(0,0,0,0.05)] hover:border-[#6B7086]/30 hover:shadow-[0_8px_20px_-6px_rgba(107,112,134,0.2)] hover:-translate-y-1 transition-all duration-300 block p-5 h-[140px] flex flex-col justify-between relative overflow-hidden">
                    <span class="absolute top-3 right-3 text-[8px] font-extrabold text-[#C4922E] bg-[#C4922E]/10 px-1.5 py-0.5 rounded uppercase tracking-widest border border-[#C4922E]/20 z-20">Admin</span>
                    <div class="w-9 h-9 rounded-full bg-[#6B7086]/10 flex items-center justify-center transition-colors duration-300 group-hover:bg-[#6B7086] relative z-10">
                        <i class="fa-solid fa-box-archive text-[#6B7086] text-sm group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-[15px] font-extrabold text-[#1E2657] mb-0.5 transition-colors duration-300 group-hover:text-[#1E2657]">Archive</h3>
                        <p class="text-[12px] text-[#9CA0AF] font-medium m-0">Past Records</p>
                    </div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#6B7086] transition-all duration-500 ease-out group-hover:w-full"></div>
                </a>
                @endif

            </div>
        </section>
        @endif

        <!-- 4. ACADEMIC CALENDAR TIMELINE -->
        <section>
            <div class="bg-white border border-black/15 rounded-2xl shadow-sm overflow-hidden w-full mb-8">
                
                <!-- Header -->
                <div class="px-6 py-5 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gradient-to-r from-[#F4F6FA] to-white">
                    <h6 class="text-[14px] font-extrabold text-[#1E2657] m-0 flex items-center">
                        <div class="w-8 h-8 rounded-full bg-white border border-black/10 flex items-center justify-center mr-3 shadow-sm text-[#6B7086]">
                            <i class="fa fa-calendar-alt text-sm"></i>
                        </div>
                        Academic Calendar Timeline
                    </h6>
                    @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
                    @if($activeQ)
                        <span class="inline-flex items-center px-4 py-1.5 bg-white text-[#3B82F6] border border-[#3B82F6]/20 shadow-sm text-[10px] font-extrabold rounded-full uppercase tracking-widest mt-3 sm:mt-0 relative overflow-hidden group">
                            <!-- Soft animated background pulse -->
                            <span class="absolute inset-0 bg-[#3B82F6]/10 opacity-50"></span>
                            <!-- Live pinging dot -->
                            <span class="relative flex h-2 w-2 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#3B82F6] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#3B82F6]"></span>
                            </span>
                            Current: {{ $activeQ->name }}
                        </span>
                    @endif
                </div>
                
                <!-- Swipeable Carousel -->
                <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory px-6 pb-8 pt-8 [&::-webkit-scrollbar]:h-[6px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-[#D1D5DB] hover:[&::-webkit-scrollbar-thumb]:bg-[#9CA0AF] [&::-webkit-scrollbar-thumb]:rounded-full">
                    @forelse($quarters ?? [] as $index => $quarter)
                        @if($quarter->is_active)
                            <!-- Active Quarter -->
                            <div class="snap-center shrink-0 w-auto flex-1 min-w-[260px] p-5 rounded-2xl bg-gradient-to-br from-white to-[#10B981]/10 shadow-[0_4px_12px_-4px_rgba(16,185,129,0.15)] border border-[#10B981]/30 relative overflow-hidden transition-all duration-500 hover:-translate-y-1 hover:shadow-[0_8px_20px_-6px_rgba(16,185,129,0.25)] animate-fade-in-up" style="animation-delay: {{ $index * 100 }}ms;">
                                
                                <!-- Animated top green highlight -->
                                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#10B981] to-emerald-300"></div>

                                <div class="flex justify-between items-center mb-4 mt-1 relative z-10">
                                    <h5 class="text-[16px] font-extrabold text-[#1E2657] m-0">{{ $quarter->name }}</h5>
                                    <span class="bg-white text-[#10B981] text-[9px] font-extrabold px-2.5 py-1 rounded-md uppercase tracking-widest border border-[#10B981]/20 flex items-center shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] mr-1.5 animate-pulse"></span> Active
                                    </span>
                                </div>

                                <div class="flex items-center text-[12px] font-medium text-[#1E2657] bg-white p-3 rounded-xl border border-black/5 shadow-sm mt-5">
                                    <div class="w-7 h-7 rounded-full bg-[#10B981]/10 flex items-center justify-center mr-3 shrink-0">
                                        <i class="fa-regular fa-clock text-[#10B981]"></i>
                                    </div>
                                    <span>
                                        {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                                        {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <!-- Inactive Quarter -->
                            <div class="group/card snap-center shrink-0 w-auto flex-1 min-w-[260px] p-5 rounded-2xl border border-black/10 bg-slate-50/50 opacity-80 transition-all duration-300 hover:opacity-100 hover:border-black/20 hover:-translate-y-1 hover:shadow-sm hover:bg-white animate-fade-in-up relative overflow-hidden" style="animation-delay: {{ $index * 100 }}ms;">
                                
                                <div class="flex justify-between items-center mb-4 mt-1 relative z-10">
                                    <h5 class="text-[15px] font-extrabold text-[#6B7086] m-0 transition-colors duration-300 group-hover/card:text-[#1E2657]">{{ $quarter->name }}</h5>
                                </div>

                                <div class="flex items-center text-[12px] font-medium text-[#9CA0AF] bg-white/50 p-3 rounded-xl border border-black/5 transition-colors duration-300 group-hover/card:bg-[#F4F6FA] group-hover/card:text-[#6B7086] mt-5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center mr-3 shrink-0 transition-colors duration-300 group-hover/card:bg-white group-hover/card:shadow-sm">
                                        <i class="fa-regular fa-calendar text-[#D1D5DB] transition-colors duration-300 group-hover/card:text-[#9CA0AF]"></i>
                                    </div>
                                    <span>
                                        {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                                        {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                                    </span>
                                </div>

                                <!-- Expanding bottom line on hover -->
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 h-[3px] w-0 bg-[#6B7086]/30 transition-all duration-500 ease-out group-hover/card:w-full"></div>
                            </div>
                        @endif
                    @empty
                        <div class="w-full text-center py-6 text-sm font-medium text-[#9CA0AF]">
                            No calendar data configured by the administrator yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

    </div>
</div>

<style>
    /* CSS for the staggered fade-in-up animation on the calendar cards */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0; /* Ensures cards are hidden before the animation fires */
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        
        // 1. DYNAMIC TIME GREETING LOGIC
        const greetingElement = document.getElementById('dynamic-greeting');
        if (greetingElement) {
            const currentHour = new Date().getHours();
            
            if (currentHour < 12) {
                greetingElement.innerText = "Good Morning";
            } else if (currentHour < 18) {
                greetingElement.innerText = "Good Afternoon";
            } else {
                greetingElement.innerText = "Good Evening";
            }
        }

        // 2. COUNTER ANIMATION LOGIC
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 200;
                
                if (target === 0 || isNaN(target)) {
                    counter.innerText = '0';
                    return;
                }

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