@extends('layouts.app')

@section('title', 'System Archiving')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">
                
                <div class="px-6 py-5 border-b border-slate-200 flex flex-wrap justify-between items-center bg-white gap-4">
                    <h3 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 mr-3 shadow-sm border border-purple-100">
                            <i class="fa fa-box-archive"></i>
                        </div>
                        Year-End Archiving
                    </h3>
                    
                    <form action="{{ route('admin.archive.run') }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to run the year-end archive? This cannot be undone.');">
                        @csrf
                        <input type="hidden" name="school_year" value="2024-2025">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1" onclick="runArchiveDemo()">
                            <i class="fa fa-play mr-2"></i> Run Archive
                        </button>
                    </form>
                </div>
                
                <div class="p-6">
                    <div class="bg-orange-50 border border-orange-200 p-4 rounded-xl mb-6 shadow-sm">
                        <strong class="flex items-center text-[13px] font-extrabold text-orange-800 mb-1">
                            <i class="fa fa-triangle-exclamation text-orange-500 mr-2"></i> Year-end Archive for SY 2024-2025
                        </strong>
                        <p class="text-xs font-medium text-orange-700 m-0 leading-relaxed ml-6">
                            This will snapshot all student records into JSON format, generate master PDFs for safekeeping, and optionally deactivate teacher/guardian accounts.
                        </p>
                    </div>
                    
                    <div class="relative border-l-2 border-indigo-100 ml-4 space-y-6 mt-8">
                        
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-indigo-500 border-4 border-white shadow-sm"></span>
                            <p class="font-extrabold text-[13px] text-slate-800 m-0 mb-0.5">Snapshot Student Data</p>
                            <p class="text-xs font-medium text-slate-500 m-0">Capture all profiles, IEP goals, and progress notes</p>
                        </div>
                        
                        <div class="relative pl-6">
                            <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-indigo-500 border-4 border-white shadow-sm"></span>
                            <p class="font-extrabold text-[13px] text-slate-800 m-0 mb-0.5">Generate Master PDFs</p>
                            <p class="text-xs font-medium text-slate-500 m-0">Create one comprehensive PDF file per student</p>
                        </div>
                        
                        <div class="relative pl-6">
                            <span id="step3Dot" class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-slate-300 border-4 border-white shadow-sm transition-colors duration-500"></span>
                            <p class="font-extrabold text-[13px] text-slate-800 m-0 mb-0.5">Soft-Delete Records</p>
                            <p class="text-xs font-medium text-slate-500 m-0">Mark processed students as archived in the active database</p>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-200 border-l-4 border-l-emerald-500 p-6 flex gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                    <i class="fa fa-robot text-xl"></i>
                </div>
                <div>
                    <h5 class="font-extrabold text-[15px] text-emerald-900 mb-1.5 m-0">Automated Quarterly Rollover Active</h5>
                    <p class="text-xs font-medium text-emerald-700 m-0 leading-relaxed">
                        This system is configured with automated background tasks (Cron Jobs). The system will securely archive all approved daily reports and clear active Teacher dashboards automatically at midnight on the closing date of the current quarter. <strong class="text-emerald-900">No manual initialization is required.</strong>
                    </p>
                </div>
            </div>

        </div>
        
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden h-full min-h-[500px]">
                
                <div class="px-6 py-5 border-b border-slate-200 bg-white">
                    <h3 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                        <i class="fa fa-folder-open text-indigo-500 mr-2"></i> Archived Records
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    @forelse($archives as $archive)
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors shadow-sm">
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-1.5 shrink-0 shadow-sm"></div>
                            <div class="flex-1">
                                <h6 class="font-extrabold text-sm text-slate-800 m-0 mb-1">
                                    {{ $archive->student->first_name ?? 'Unknown' }} {{ $archive->student->last_name ?? 'Student' }} – SY {{ $archive->school_year }}
                                </h6>
                                <p class="text-xs font-medium text-slate-500 m-0 mb-3">Archived on: {{ $archive->archived_at->format('M j, Y') }}</p>
                                
                                @if($archive->master_pdf_path)
                                    <a href="{{ asset('storage/' . $archive->master_pdf_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 text-slate-600 text-[11px] font-bold rounded-lg hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-file-pdf mr-1.5 text-rose-500"></i> Download PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 border border-slate-200">
                                <i class="fa fa-folder-open text-2xl text-slate-300"></i>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-700 m-0 mb-1">No archived records found</h4>
                            <p class="text-xs font-medium text-slate-500 m-0">Past student records will appear here.</p>
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
    // Just a visual flair for the UI if they click the button
    function runArchiveDemo() {
        setTimeout(() => { 
            const step3 = document.getElementById('step3Dot');
            if (step3) {
                step3.classList.remove('bg-slate-300');
                step3.classList.add('bg-indigo-500');
            }
        }, 800);
    }
</script>
@endpush