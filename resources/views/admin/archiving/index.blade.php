@extends('layouts.app')

@section('title', 'System Archiving')

@section('content')

@php
    $curY = (int) date('Y');
    $curM = (int) date('n');
    $activeSchoolYear = ($curM >= 6) ? $curY . '-' . ($curY + 1) : ($curY - 1) . '-' . $curY;
@endphp

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down mb-6">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
        
        <!-- LEFT COLUMN -->
        <div class="lg:col-span-7 space-y-5 sm:space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
                
                <!-- Header -->
                <div class="p-5 sm:p-6 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4">
                    <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                        <div class="w-10 h-10 rounded-full bg-[#6B7086]/10 flex items-center justify-center text-[#6B7086] mr-3 shadow-sm border border-black/5 shrink-0">
                            <i class="fa fa-box-archive"></i>
                        </div>
                        Year-End Archiving
                    </h3>
                    
                    <form action="{{ route('admin.archive.run') }}" method="POST" class="m-0 w-full sm:w-auto" onsubmit="return confirm('Are you sure you want to run the real-time year-end archive for SY {{ $activeSchoolYear }}? This cannot be undone.');">
                        @csrf
                        <input type="hidden" name="school_year" value="{{ $activeSchoolYear }}">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] sm:text-sm font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1" onclick="runArchiveDemo()">
                            <i class="fa fa-play mr-2"></i> Run Archive
                        </button>
                    </form>
                </div>
                
                <div class="p-5 sm:p-6">
                    <!-- Warning Notice -->
                    <div class="bg-[#F59E0B]/10 border border-[#F59E0B]/20 p-4 rounded-xl mb-5 sm:mb-6 shadow-sm">
                        <strong class="flex items-center text-[13px] font-extrabold text-[#1E2657] mb-1">
                            <i class="fa fa-triangle-exclamation text-[#F59E0B] mr-2"></i> Real-Time Year-End Archive (SY {{ $activeSchoolYear }})
                        </strong>
                        <p class="text-[12px] font-medium text-[#6B7086] m-0 leading-relaxed ml-0 sm:ml-6 mt-1.5 sm:mt-0">
                            This will capture real-time student snapshots, generate master PDFs for safekeeping with live digital audit timestamps, and archive active records.
                        </p>
                    </div>
                    
                    <!-- Timeline Steps -->
                    <div class="relative border-l-2 border-[#3B82F6]/20 ml-2 sm:ml-4 space-y-6 sm:space-y-8 mt-6 sm:mt-8">
                        
                        <div class="relative pl-6 sm:pl-8">
                            <span class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-[#3B82F6] border-4 border-white shadow-sm"></span>
                            <p class="font-extrabold text-[13px] sm:text-[14px] text-[#1E2657] m-0 mb-0.5">Snapshot Student Data</p>
                            <p class="text-[12px] font-medium text-[#9CA0AF] m-0">Capture all profiles, IEP goals, and progress notes</p>
                        </div>
                        
                        <div class="relative pl-6 sm:pl-8">
                            <span class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-[#3B82F6] border-4 border-white shadow-sm"></span>
                            <p class="font-extrabold text-[13px] sm:text-[14px] text-[#1E2657] m-0 mb-0.5">Generate Master PDFs</p>
                            <p class="text-[12px] font-medium text-[#9CA0AF] m-0">Create one comprehensive PDF file per student</p>
                        </div>
                        
                        <div class="relative pl-6 sm:pl-8">
                            <span id="step3Dot" class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-[#E8E5DC] border-4 border-white shadow-sm transition-colors duration-500"></span>
                            <p class="font-extrabold text-[13px] sm:text-[14px] text-[#1E2657] m-0 mb-0.5">Soft-Delete Records</p>
                            <p class="text-[12px] font-medium text-[#9CA0AF] m-0">Mark processed students as archived in the active database</p>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Automated Rollover Box -->
            <div class="bg-white rounded-2xl shadow-sm border border-black/15 border-l-4 border-l-[#10B981] p-5 sm:p-6 flex gap-4 items-start">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center shrink-0 border border-[#10B981]/20">
                    <i class="fa fa-robot text-lg sm:text-xl"></i>
                </div>
                <div>
                    <h5 class="font-extrabold text-[14px] sm:text-[15px] text-[#1E2657] mb-1 sm:mb-1.5 m-0 leading-tight">Automated Quarterly Rollover Active</h5>
                    <p class="text-[12px] font-medium text-[#6B7086] m-0 leading-relaxed">
                        This system is configured with automated background tasks (Cron Jobs). The system will securely archive all approved daily reports and clear active Teacher dashboards automatically at midnight on the closing date of the current quarter. <strong class="text-[#1E2657]">No manual initialization is required.</strong>
                    </p>
                </div>
            </div>

        </div>
        
        <!-- RIGHT COLUMN -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden h-full sm:min-h-[500px]">
                
                <div class="p-5 sm:p-6 border-b border-black/10 bg-[#F4F6FA]">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3">
                        <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                            <i class="fa fa-folder-open text-[#6B7086] mr-2"></i> Archived Records
                        </h3>

                        {{-- Year Filter --}}
                        <form method="GET" action="{{ route('admin.archive.index') }}" class="m-0 flex items-center gap-2" id="yearFilterForm">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <select name="school_year" onchange="this.form.submit()" class="text-[12px] font-extrabold text-[#1E2657] bg-white border border-black/15 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent cursor-pointer shadow-sm appearance-none pr-8" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7086%22 stroke-width=%222%22><polyline points=%226 9 12 15 18 9%22/></svg>'); background-repeat: no-repeat; background-position: right 8px center;">
                                <option value="">All Years</option>
                                @foreach($schoolYears as $year)
                                    <option value="{{ $year }}" {{ request('school_year') == $year ? 'selected' : '' }}>
                                        SY {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    {{-- Search Bar --}}
                    <form method="GET" action="{{ route('admin.archive.index') }}" class="m-0 relative">
                        <input type="hidden" name="school_year" value="{{ request('school_year') }}">
                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA0AF] text-[12px]"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name..."
                            class="w-full text-[12px] font-medium text-[#1E2657] bg-white border border-black/15 rounded-lg pl-9 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent shadow-sm placeholder-[#9CA0AF]">
                        @if(request('search'))
                            <a href="{{ route('admin.archive.index', ['school_year' => request('school_year')]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA0AF] hover:text-[#1E2657] transition-colors decoration-transparent">
                                <i class="fa fa-times text-[12px]"></i>
                            </a>
                        @endif
                    </form>
                </div>
                
                <div class="p-5 sm:p-6 space-y-4">
                    @forelse($archives as $archive)
                        <div class="flex flex-col gap-3 p-4 rounded-xl bg-white border border-black/15 hover:border-black/30 hover:shadow-md transition-all shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="hidden sm:block w-2.5 h-2.5 rounded-full bg-[#6B7086] mt-1.5 shrink-0 shadow-sm"></div>
                                <div class="flex-1">
                                    <h6 class="font-extrabold text-[13px] sm:text-sm text-[#1E2657] m-0 mb-1 leading-tight">
                                        <span class="sm:hidden inline-block w-2 h-2 rounded-full bg-[#6B7086] mr-1.5 mb-[1px]"></span>
                                        {{ $archive->student->first_name ?? 'Unknown' }} {{ $archive->student->last_name ?? 'Student' }} – SY {{ $archive->school_year }}
                                    </h6>
                                    <p class="text-[11px] sm:text-[12px] font-medium text-[#9CA0AF] m-0">
                                        <i class="fa-regular fa-clock mr-1"></i> Archived on: {{ ($archive->archived_at ?? $archive->created_at)->format('M j, Y h:i A') }} ({{ ($archive->archived_at ?? $archive->created_at)->diffForHumans() }})
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 ml-0 sm:ml-[22px]">
                                {{-- View Button --}}
                                <a href="{{ route('admin.archive.show', $archive->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 text-[#6B7086] text-[11px] font-extrabold rounded-lg hover:bg-blue-50 hover:text-[#3B82F6] hover:border-blue-200 transition-colors shadow-sm decoration-transparent uppercase tracking-widest">
                                    <i class="fa fa-eye mr-1.5 text-[#3B82F6]"></i> View
                                </a>

                                {{-- Download PDF --}}
                                @if($archive->master_pdf_path)
                                    <a href="{{ asset('storage/' . $archive->master_pdf_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 text-[#6B7086] text-[11px] font-extrabold rounded-lg hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent uppercase tracking-widest">
                                        <i class="fa fa-file-pdf mr-1.5 text-rose-500"></i> PDF
                                    </a>
                                @endif

                                {{-- Restore Button --}}
                                <form action="{{ route('admin.archive.restore', $archive->id) }}" method="POST" class="m-0" onsubmit="return confirm('Restore this student to active status? The archive record will be removed.');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 text-[#6B7086] text-[11px] font-extrabold rounded-lg hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-colors shadow-sm uppercase tracking-widest">
                                        <i class="fa fa-rotate-left mr-1.5 text-emerald-500"></i> Restore
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 sm:py-16 text-center">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#F4F6FA] flex items-center justify-center mb-3 sm:mb-4 border border-black/5">
                                <i class="fa fa-folder-open text-xl sm:text-2xl text-[#9CA0AF]"></i>
                            </div>
                            <h4 class="text-[14px] font-extrabold text-[#1E2657] m-0 mb-1">No archived records found</h4>
                            <p class="text-[12px] font-medium text-[#6B7086] m-0">Past student records will appear here.</p>
                        </div>
                    @endforelse
                </div>
                
            </div>
        </div>
        
    </div>
</div>

@endsection

@push('scripts')
<script>
    function runArchiveDemo() {
        setTimeout(() => { 
            const step3 = document.getElementById('step3Dot');
            if (step3) {
                step3.classList.remove('bg-[#E8E5DC]');
                step3.classList.add('bg-[#3B82F6]');
            }
        }, 800);
    }
</script>
@endpush