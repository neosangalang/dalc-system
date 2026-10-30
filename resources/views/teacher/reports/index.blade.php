@extends('layouts.app')

@section('title', 'My Reports')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8" x-data="{ activeViewModal: null, enlargedPhotoUrl: null, enlargedPhotoTitle: '' }">


    <!-- FILTER SECTION -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden mb-4 sm:mb-6">
        <div class="px-5 sm:px-6 py-3.5 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
            <h6 class="text-[13px] sm:text-sm font-extrabold text-[#1E2657] m-0 flex items-center">
                <i class="fa fa-filter text-[#3B82F6] mr-2"></i> Filter Reports
            </h6>
            @if(request('search') || request('student_id') || request('type') || request('status'))
                <a href="{{ route('teacher.reports.index') }}" title="Clear Filters" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-rose-300 text-rose-500 text-xs font-bold rounded-lg hover:bg-rose-50 transition-colors shadow-sm decoration-transparent active:scale-95">
                    <i class="fa fa-times text-xs"></i> <span>Clear Filters</span>
                </a>
            @endif
        </div>
        <div class="p-3 sm:p-5">
            <form action="{{ route('teacher.reports.index') }}" method="GET" class="m-0" id="reportFilterForm">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                    
                    <!-- Search Keyword -->
                    <div class="sm:col-span-3">
                        <label class="hidden sm:flex text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5 items-center">
                            <i class="fa fa-search mr-1.5 text-[#9CA0AF]"></i> Search Keyword
                        </label>
                        <div class="relative w-full shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#9CA0AF] pointer-events-none">
                                <i class="fa fa-search text-xs"></i>
                            </span>
                            <input type="text" name="search" id="reportSearchInput" class="w-full border-0 py-1.5 sm:py-2.5 pl-8 sm:pl-10 pr-3 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Search name or type..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Specific Student -->
                    <div class="sm:col-span-3">
                        <label class="hidden sm:flex text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5 items-center">
                            <i class="fa fa-user-graduate mr-1.5 text-[#9CA0AF]"></i> Specific Student
                        </label>
                        <select name="student_id" onchange="this.form.submit()" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3.5 py-1.5 sm:py-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-[#F4F6FA] cursor-pointer">
                            <option value="">All My Students</option>
                            @if(isset($myStudents))
                                @foreach($myStudents as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Report Type -->
                    <div class="sm:col-span-3">
                        <label class="hidden sm:flex text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5 items-center">
                            <i class="fa fa-file-alt mr-1.5 text-[#9CA0AF]"></i> Report Type
                        </label>
                        <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3.5 py-1.5 sm:py-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                            <option value="">All Report Types</option>
                            <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily Report</option>
                            <option value="q1" {{ request('type') == 'q1' ? 'selected' : '' }}>1st Quarter (Q1) Report</option>
                            <option value="q2" {{ request('type') == 'q2' ? 'selected' : '' }}>2nd Quarter (Q2) Report</option>
                            <option value="q3" {{ request('type') == 'q3' ? 'selected' : '' }}>3rd Quarter (Q3) Report</option>
                            <option value="q4" {{ request('type') == 'q4' ? 'selected' : '' }}>4th Quarter (Q4) Report</option>
                            <option value="mid_year" {{ request('type') == 'mid_year' ? 'selected' : '' }}>Mid-Year Report</option>
                            <option value="year_end" {{ request('type') == 'year_end' ? 'selected' : '' }}>Year-End Report</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-3">
                        <label class="hidden sm:flex text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5 items-center">
                            <i class="fa fa-circle-check mr-1.5 text-[#9CA0AF]"></i> Status
                        </label>
                        <select name="status" onchange="this.form.submit()" class="w-full rounded-xl border-black/15 px-2.5 sm:px-3.5 py-1.5 sm:py-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Needs Revision</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>

    <!-- REPORTS LIST -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border border-black/15 overflow-hidden">
        
        <div class="p-3.5 sm:px-6 py-3.5 sm:py-5 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-3 md:mb-0 gap-3 sm:gap-0">
            <h5 class="text-sm sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] mr-2.5 sm:mr-3 shadow-sm shrink-0">
                    <i class="fa fa-file-signature text-xs sm:text-base"></i>
                </div>
                My Generated Reports
            </h5>
            
            <a href="{{ route('teacher.reports.create') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 decoration-transparent">
                <i class="fa fa-wand-magic-sparkles mr-2"></i> Generate New Report
            </a>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Student Name</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Report Type</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Date</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-black/5 bg-transparent md:bg-white">
                    @forelse($reports as $report)
                        <!-- MOBILE CARD / DESKTOP ROW -->
                        <tr class="block md:table-row relative bg-white md:bg-transparent rounded-[16px] md:rounded-none shadow-sm md:shadow-none border md:border-none hover:bg-slate-50 transition-colors duration-150 pt-2 md:pt-0 {{ $report->status === 'rejected' ? 'border-l-4 border-l-rose-500 border-rose-200 md:border-l-4 md:border-l-rose-500' : 'border-black/15' }}">
                            
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-5 border-b border-black/5 md:border-none pr-28 md:pr-6">
                                <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1">Student Name</span>
                                <div class="text-[14px] font-extrabold text-[#1E2657] leading-tight">
                                    {{ $report->student->first_name }} {{ $report->student->last_name }}
                                </div>
                                @if($report->status === 'rejected' && $report->revision_notes)
                                    <div class="mt-1.5 flex items-start gap-1.5 md:max-w-[280px]">
                                        <i class="fa-solid fa-comment-dots text-rose-500 text-[10px] mt-0.5 shrink-0"></i>
                                        <p class="text-[11px] font-bold text-rose-600 m-0 leading-snug line-clamp-2">{{ \Illuminate\Support\Str::limit($report->revision_notes, 80) }}</p>
                                    </div>
                                @endif
                            </td>
                            
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-5 border-b border-black/5 md:border-none mt-1 md:mt-0">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Report Type</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20 uppercase tracking-widest shadow-sm">
                                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                                </span>
                            </td>
                            
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-5 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Date</span>
                                <span class="text-[12px] font-bold text-[#6B7086]">
                                    @if($report->report_type === 'daily' && $report->report_date)
                                        {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                                    @else
                                        {{ $report->created_at->format('M d, Y') }}
                                    @endif
                                </span>
                            </td>
                            
                            <!-- Status absolute pinned on Mobile -->
                            <td class="absolute top-4 right-4 md:static md:table-cell md:px-6 md:py-5">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-slate-100 text-[#6B7086] border-black/10',
                                        'pending_approval' => 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/20',
                                        'approved' => 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20',
                                        'rejected' => 'bg-rose-50 text-rose-600 border-rose-200',
                                        'archived' => 'bg-slate-100 text-slate-600 border-black/10',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending_approval' => 'Pending Approval',
                                        'approved' => 'Approved',
                                        'rejected' => 'Needs Revision',
                                        'archived' => 'Archived',
                                    ];
                                    $statusIcons = [
                                        'draft' => 'fa-solid fa-file-pen',
                                        'pending_approval' => 'fa-solid fa-clock',
                                        'approved' => 'fa-solid fa-circle-check',
                                        'rejected' => 'fa-solid fa-triangle-exclamation',
                                        'archived' => 'fa-solid fa-box-archive',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[9px] font-extrabold uppercase tracking-widest border shadow-sm {{ $statusClasses[$report->status] ?? 'bg-slate-100 text-[#6B7086] border-black/10' }}">
                                    <i class="{{ $statusIcons[$report->status] ?? '' }} text-[8px]"></i>
                                    {{ $statusLabels[$report->status] ?? 'Unknown' }}
                                </span>
                            </td>
                            
                            <!-- ACTIONS FOOTER -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-5 {{ $report->status === 'rejected' ? 'bg-rose-50/50' : 'bg-[#F4F6FA]' }} md:bg-transparent rounded-b-[16px] md:rounded-none">
                                <div class="flex flex-wrap sm:flex-nowrap md:justify-end gap-2 w-full md:w-auto">
                                    
                                    <button @click="activeViewModal = {{ $report->id }}" class="flex-1 md:flex-none inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#3B82F6] hover:border-[#3B82F6]/30 transition-colors shadow-sm">
                                        <i class="fa fa-eye mr-1.5"></i> View
                                    </button>

                                    <!-- CONDITIONAL EDIT BUTTON FOR REJECTED REPORTS -->
                                    @if($report->status === 'rejected')
                                        <a href="{{ route('teacher.reports.edit', $report->id) }}" class="flex-1 md:flex-none inline-flex justify-center md:justify-start items-center px-3 py-2 bg-rose-500 border border-rose-600 text-white text-[12px] font-extrabold rounded-xl hover:bg-rose-600 transition-colors shadow-sm decoration-transparent">
                                            <i class="fa fa-pen-to-square mr-1.5"></i> Revise Now
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="flex-1 md:flex-none inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-file-pdf mr-1.5"></i> PDF
                                    </a>

                                    <form action="{{ route('teacher.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report? This cannot be undone.');" class="m-0 flex shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete Report" class="inline-flex items-center justify-center w-[42px] md:w-8 py-2 bg-white border border-black/15 text-[#9CA0AF] rounded-xl hover:bg-rose-50 hover:border-rose-200 hover:text-rose-500 transition-colors shadow-sm">
                                            <i class="fa fa-trash text-[12px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- VIEW MODAL (Teleported to root to fix table rendering issues) -->
                        <template x-teleport="body">
                            <div x-show="activeViewModal === {{ $report->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="activeViewModal === {{ $report->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="activeViewModal = null"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="activeViewModal === {{ $report->id }}" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-black/15 relative z-[101]">
                                        
                                        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-start rounded-t-2xl">
                                            <div>
                                                <h5 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 mb-0.5 leading-tight">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }}</h5>
                                                <p class="text-[11px] font-extrabold text-[#9CA0AF] uppercase tracking-widest m-0">Generated on {{ $report->created_at->format('F j, Y') }}</p>
                                            </div>
                                            <button type="button" @click="activeViewModal = null" class="text-[#9CA0AF] hover:text-[#1E2657] ml-4 transition-colors"><i class="fa fa-times text-xl"></i></button>
                                        </div>
                                        
                                        <div class="p-5 sm:p-6">
                                            @if($report->status === 'rejected' && $report->revision_notes)
                                                <div class="mb-5 p-4 bg-rose-50 rounded-xl border border-rose-200 flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 text-xs">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-xs font-extrabold text-rose-900 uppercase tracking-wider mb-1 m-0">Admin Revision Request:</h6>
                                                        <p class="text-xs font-bold text-slate-800 m-0 leading-relaxed">{{ $report->revision_notes }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($report->image_path)
                                                <div class="mb-4 p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="relative group cursor-pointer shrink-0" 
                                                             @click="enlargedPhotoUrl = '{{ route('secure.report-photo', $report->id) }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($report->student->first_name) }}'"
                                                             title="Click to enlarge photo">
                                                            <img src="{{ route('secure.report-photo', $report->id) }}" alt="Attached photo" class="w-16 h-16 object-cover rounded-lg border border-slate-300 shadow-sm transition-all group-hover:ring-2 group-hover:ring-[#3B82F6] group-hover:opacity-90">
                                                            <div class="absolute inset-0 bg-black/40 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                            </div>
                                                        </div>
                                                        <div class="cursor-pointer" @click="enlargedPhotoUrl = '{{ route('secure.report-photo', $report->id) }}'; enlargedPhotoTitle = 'Observation Photo - {{ addslashes($report->student->first_name) }}'">
                                                            <h6 class="text-xs font-extrabold text-slate-800 m-0 hover:text-[#3B82F6] transition-colors flex items-center gap-1.5">
                                                                Attached Observation Photo <i class="fa-solid fa-up-right-and-down-left-from-center text-[10px] text-slate-400"></i>
                                                            </h6>
                                                            <p class="text-[11px] font-medium text-slate-500 m-0">Click photo to enlarge & view properly.</p>
                                                        </div>
                                                    </div>
                                                    <form action="{{ route('teacher.reports.delete-attachment', $report->id) }}" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to delete this attached photo?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 border border-rose-300 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer flex items-center">
                                                            <i class="fa fa-trash-alt mr-1.5"></i> Delete Photo
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                            <div class="p-5 bg-[#F4F6FA] rounded-xl border border-black/5 shadow-inner whitespace-pre-wrap text-[13px] font-medium text-[#1E2657] leading-relaxed max-h-[50vh] sm:max-h-[400px] overflow-y-auto">
                                                {{ $report->content }}
                                            </div>
                                            
                                            <div class="mt-6 pt-6 border-t border-dashed border-black/10 relative z-50">
                                                @include('components.comment-thread', ['item' => $report])
                                            </div>
                                        </div>
                                        
                                        <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex flex-col-reverse sm:flex-row justify-between sm:items-center gap-3 w-full rounded-b-2xl">
                                            <button type="button" @click="activeViewModal = null" class="w-full sm:w-auto px-6 py-2.5 bg-white text-[#6B7086] text-[13px] font-extrabold rounded-xl hover:bg-slate-50 transition-colors shadow-sm border border-black/15">
                                                Close
                                            </button>
                                            <div class="flex items-center gap-2">
                                                @if($report->status === 'rejected')
                                                    <a href="{{ route('teacher.reports.edit', $report->id) }}" class="inline-flex items-center px-4 py-2.5 bg-amber-500 text-white text-[13px] font-extrabold rounded-xl hover:bg-amber-600 transition-colors shadow-sm">
                                                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edit & Revise Report
                                                    </a>
                                                @endif
                                                <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-rose-50 sm:bg-white border border-rose-200 text-rose-600 text-[13px] font-extrabold rounded-xl hover:bg-rose-100 sm:hover:bg-rose-50 transition-colors shadow-sm decoration-transparent uppercase tracking-widest">
                                                    <i class="fa fa-file-pdf mr-2"></i> Download PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-12 sm:py-16 text-center bg-white rounded-2xl md:rounded-none">
                                <div class="w-14 h-14 rounded-full bg-[#F4F6FA] border border-black/5 flex items-center justify-center mx-auto mb-4 shadow-sm">
                                    <i class="fa fa-file-alt text-2xl text-[#9CA0AF]"></i>
                                </div>
                                <h6 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No reports generated yet.</h6>
                                <p class="text-[13px] font-medium text-[#6B7086] m-0 mb-4">Create quarterly or annual summaries for your students.</p>
                                <a href="{{ route('teacher.reports.create') }}" class="inline-flex items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm decoration-transparent">
                                    Generate Your First Report
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($reports, 'links'))
            <div class="px-5 sm:px-6 py-4 border-t border-black/10 bg-[#F4F6FA] flex justify-center sm:justify-end">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Enlarge Photo Lightbox Modal -->
    <div x-show="enlargedPhotoUrl" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 sm:p-6" @keydown.escape.window="enlargedPhotoUrl = null">
        <div x-show="enlargedPhotoUrl" x-transition.opacity class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="enlargedPhotoUrl = null"></div>
        <div x-show="enlargedPhotoUrl" x-transition class="relative max-w-4xl w-full max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col z-10 border border-slate-200">
            <div class="px-5 py-3.5 bg-[#1E2657] text-white flex items-center justify-between">
                <h4 class="font-extrabold text-sm sm:text-base truncate pr-4 m-0" x-text="enlargedPhotoTitle"></h4>
                <button type="button" @click="enlargedPhotoUrl = null" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto flex items-center justify-center bg-slate-900/5 max-h-[calc(90vh-60px)]">
                <img :src="enlargedPhotoUrl" :alt="enlargedPhotoTitle" class="max-h-[75vh] w-auto max-w-full object-contain rounded-lg shadow-md">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('reportSearchInput');
    const form = document.getElementById('reportFilterForm');
    let debounceTimer;

    if (searchInput && form) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                form.submit();
            }, 600);
        });
    }
});
</script>
@endpush