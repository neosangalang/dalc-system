@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">
    
    <div class="flex flex-wrap justify-between items-center mb-8 gap-4 w-full">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 m-0">System Overview</h2>
            <p class="text-sm font-medium text-slate-500 mt-1 mb-0">Welcome to the EduTrack SPED control panel.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 w-full">
        
        <div class="bg-white rounded-2xl p-6 border border-slate-300 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-indigo-50 text-indigo-600">
                <i class="fa fa-users"></i>
            </div>
            <h3 class="font-extrabold text-3xl text-slate-800 m-0">{{ $totalStudents ?? 0 }}</h3>
            <p class="text-slate-500 text-sm font-semibold mt-1">Active Students</p>
        </div>
        
        <div class="bg-white rounded-2xl p-6 border border-slate-300 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-emerald-50 text-emerald-500">
                <i class="fa fa-chalkboard-user"></i>
            </div>
            <h3 class="font-extrabold text-3xl text-slate-800 m-0">{{ $activeTeachers ?? 0 }}</h3>
            <p class="text-slate-500 text-sm font-semibold mt-1">Active Teachers</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-300 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-pink-50 text-pink-500">
                <i class="fa fa-heart"></i>
            </div>
            <h3 class="font-extrabold text-3xl text-slate-800 m-0">{{ $guardians ?? 0 }}</h3>
            <p class="text-slate-500 text-sm font-semibold mt-1">Registered Guardians</p>
        </div>

        <a href="{{ route('admin.report-approval.index') }}" class="block group text-decoration-none">
            <div class="bg-white rounded-2xl p-6 border-l-4 {{ ($pendingReports ?? 0) > 0 ? 'border-l-red-500' : 'border-l-emerald-500' }} border-t border-r border-b border-slate-300 shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 {{ ($pendingReports ?? 0) > 0 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-500' }}">
                    <i class="fa fa-file-signature"></i>
                </div>
                <h3 class="font-extrabold text-3xl text-slate-800 m-0">{{ $pendingReports ?? 0 }}</h3>
                <p class="text-sm font-bold mt-1 {{ ($pendingReports ?? 0) > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                    {{ ($pendingReports ?? 0) > 0 ? 'Needs Review' : 'Reports Cleared' }}
                </p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 w-full">
        
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-300 overflow-hidden shadow-md">
                <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                    <h5 class="font-bold text-lg text-slate-800 m-0 flex items-center">
                        <i class="fa fa-history mr-3 text-slate-400"></i> Recent Enrollment
                    </h5>
                    <a href="{{ route('admin.students.index') }}" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-full hover:bg-indigo-700 transition shadow-sm decoration-transparent">View All</a>
                </div>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Assigned Teacher</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentStudents ?? [] as $student)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-100 text-indigo-600 font-extrabold text-sm border border-slate-200">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-[11px] text-slate-500 mt-0.5">ID: {{ $student->student_id_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-700">{{ $student->teacher->name ?? 'Unassigned' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Guardian: {{ $student->guardian->name ?? 'None' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border border-slate-200 text-slate-600 rounded-full hover:bg-slate-100 hover:text-indigo-600 transition-colors">
                                        <i class="fa fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm">
                                    <i class="fa fa-folder-open text-3xl text-slate-300 mb-3 block"></i>
                                    <span class="font-bold">No recent student enrollments found.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-slate-300 shadow-md">
                <h6 class="font-extrabold text-slate-800 mb-6 text-xs uppercase tracking-widest border-b border-slate-100 pb-3">Admin Toolbox</h6>
                
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.accounts.index') }}" class="group bg-white border border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-indigo-600 hover:bg-indigo-50 hover:shadow-md hover:-translate-y-1 block decoration-transparent">
                        <i class="fa fa-user-plus text-2xl text-indigo-500 block mb-2 group-hover:text-indigo-600"></i>
                        <span class="font-bold text-[13px] text-slate-800">Accounts</span>
                    </a>
                    
                    <a href="{{ route('admin.students.index') }}" class="group bg-white border border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-emerald-500 hover:bg-emerald-50 hover:shadow-md hover:-translate-y-1 block decoration-transparent">
                        <i class="fa fa-user-graduate text-2xl text-emerald-500 block mb-2 group-hover:text-emerald-600"></i>
                        <span class="font-bold text-[13px] text-slate-800">Students</span>
                    </a>
                    
                    <a href="{{ route('admin.quarters.index') }}" class="group bg-white border border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-amber-500 hover:bg-amber-50 hover:shadow-md hover:-translate-y-1 block decoration-transparent">
                        <i class="fa fa-calendar-check text-2xl text-amber-500 block mb-2 group-hover:text-amber-600"></i>
                        <span class="font-bold text-[13px] text-slate-800">Calendar</span>
                    </a>
                    
                    <a href="{{ route('admin.archive.index') }}" class="group bg-white border border-slate-200 rounded-xl p-4 text-center transition-all duration-200 hover:border-purple-500 hover:bg-purple-50 hover:shadow-md hover:-translate-y-1 block decoration-transparent">
                        <i class="fa fa-box-archive text-2xl text-purple-500 block mb-2 group-hover:text-purple-600"></i>
                        <span class="font-bold text-[13px] text-slate-800">Archives</span>
                    </a>
                </div>

                <div class="mt-6 p-4 rounded-xl bg-slate-100 border-2 border-dashed border-slate-300">
                    <h6 class="font-bold text-sm mb-2 text-slate-800 flex items-center">
                        <i class="fa fa-lightbulb text-amber-500 mr-2 text-lg"></i> Quick Fact
                    </h6>
                    <p class="text-slate-500 text-[13px] leading-relaxed mb-0">
                        You have <strong class="text-slate-800">{{ $pendingReports ?? 0 }}</strong> reports waiting for your signature. Reports stay hidden from guardians until approved.
                    </p>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection