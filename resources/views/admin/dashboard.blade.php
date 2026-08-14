@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">
    
    <div class="flex flex-wrap justify-between items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0">System Overview</h2>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 mt-1 mb-0">Welcome to the Dream Achievers Learning Center PH control panel.</p>
        </div>
    </div>

    <!-- CHANGED: grid-cols-2 forces the 2x2 layout on mobile. gap-3 prevents them from touching -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8 w-full">
        
        <!-- CARD 1 -->
        <div class="bg-white rounded-[16px] sm:rounded-2xl p-4 sm:p-6 border border-slate-300 shadow-sm sm:shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 bg-indigo-50 text-indigo-600">
                <i class="fa fa-users"></i>
            </div>
            <h3 class="font-extrabold text-2xl sm:text-3xl text-slate-800 m-0 leading-none">{{ $totalStudents ?? 0 }}</h3>
            <p class="text-slate-500 text-[11px] sm:text-sm font-semibold mt-1.5 sm:mt-1 m-0 leading-tight">Active Students</p>
        </div>
        
        <!-- CARD 2 -->
        <div class="bg-white rounded-[16px] sm:rounded-2xl p-4 sm:p-6 border border-slate-300 shadow-sm sm:shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 bg-emerald-50 text-emerald-500">
                <i class="fa fa-chalkboard-user"></i>
            </div>
            <h3 class="font-extrabold text-2xl sm:text-3xl text-slate-800 m-0 leading-none">{{ $activeTeachers ?? 0 }}</h3>
            <p class="text-slate-500 text-[11px] sm:text-sm font-semibold mt-1.5 sm:mt-1 m-0 leading-tight">Active Teachers</p>
        </div>

        <!-- CARD 3 -->
        <div class="bg-white rounded-[16px] sm:rounded-2xl p-4 sm:p-6 border border-slate-300 shadow-sm sm:shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 bg-pink-50 text-pink-500">
                <i class="fa fa-heart"></i>
            </div>
            <h3 class="font-extrabold text-2xl sm:text-3xl text-slate-800 m-0 leading-none">{{ $guardians ?? 0 }}</h3>
            <p class="text-slate-500 text-[11px] sm:text-sm font-semibold mt-1.5 sm:mt-1 m-0 leading-tight">Registered Guardians</p>
        </div>

        <!-- CARD 4 -->
        <a href="{{ route('admin.report-approval.index') }}" class="block group text-decoration-none h-full">
            <div class="bg-white rounded-[16px] sm:rounded-2xl p-4 sm:p-6 border-l-4 {{ ($pendingReports ?? 0) > 0 ? 'border-l-red-500' : 'border-l-emerald-500' }} border-t border-r border-b border-slate-300 shadow-sm sm:shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 h-full">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-lg sm:text-xl mb-3 sm:mb-4 {{ ($pendingReports ?? 0) > 0 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-500' }}">
                    <i class="fa fa-file-signature"></i>
                </div>
                <h3 class="font-extrabold text-2xl sm:text-3xl text-slate-800 m-0 leading-none">{{ $pendingReports ?? 0 }}</h3>
                <p class="text-[11px] sm:text-sm font-bold mt-1.5 sm:mt-1 m-0 leading-tight {{ ($pendingReports ?? 0) > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                    {{ ($pendingReports ?? 0) > 0 ? 'Needs Review' : 'Reports Cleared' }}
                </p>
            </div>
        </a>
        
    </div>

</div>

@endsection