@extends('layouts.app')

@section('title', 'My Students')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h4 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mr-3 shadow-sm shrink-0">
                    <i class="fa fa-users"></i>
                </div>
                My Assigned Students
            </h4>
        </div>
        
        <div class="w-full sm:w-auto">
            <div class="relative w-full sm:w-80 shadow-sm rounded-xl overflow-hidden border border-slate-300 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="studentSearchInput" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] sm:text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 placeholder:font-medium bg-transparent" placeholder="Search by name or exceptionality...">
            </div>
        </div>
    </div>

    <!-- TABLE AREA -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border border-slate-300 overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <thead class="hidden md:table-header-group bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student Name</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-extrabold text-slate-500 uppercase tracking-wider">Age</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Exceptionality</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-slate-200 bg-transparent md:bg-white">
                    @forelse($students as $student)
                        <tr class="block md:table-row relative bg-white md:bg-transparent rounded-[16px] md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row pt-2 md:pt-0">
                            
                            <!-- 1. Name & Avatar -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center text-white font-extrabold text-sm sm:text-base shadow-sm bg-gradient-to-br from-indigo-500 to-purple-500 shrink-0 border border-indigo-200">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <span class="text-[14px] sm:text-sm font-extrabold text-slate-800 leading-tight">{{ $student->last_name }}, {{ $student->first_name }}</span>
                                </div>
                            </td>

                            <!-- 2. Age -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-slate-100 md:border-none text-[13px] sm:text-sm font-bold text-slate-600 mt-1 md:mt-0 md:text-center">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Age</span>
                                {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs
                            </td>
                            
                            <!-- 3. Exceptionality -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-100 md:border-none">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider md:hidden">Exceptionality</span>
                                <span class="inline-flex items-center px-2 sm:px-2.5 py-1 rounded-md text-[10px] sm:text-[11px] font-extrabold bg-indigo-50 text-indigo-700 uppercase tracking-widest border border-indigo-200 shadow-sm text-right md:text-left leading-tight">
                                    {{ $student->exceptionality ?? 'Unspecified' }}
                                </span>
                            </td>

                            <!-- 4. Status Badge (Correctly placed 4th in DOM, but visually pinned top-right on mobile via CSS) -->
                            <td class="absolute top-4 right-4 md:static md:table-cell md:px-6 md:py-4">
                                @if($student->status === 'active')
                                    <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-widest border border-emerald-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 uppercase tracking-widest border border-amber-200">Inactive</span>
                                @endif
                            </td>
                            
                            <!-- 5. Actions Footer -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-[16px] md:rounded-none">
                                <div class="grid grid-cols-2 md:flex md:justify-end gap-2 w-full md:w-auto">
                                    
                                    <a href="{{ route('teacher.iep-goals.index') }}" title="View IEP Goals" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-white md:bg-emerald-50 border border-slate-200 md:border-emerald-300 text-slate-700 md:text-emerald-600 text-[11px] md:text-xs font-bold rounded-lg hover:bg-slate-100 md:hover:bg-emerald-100 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-bullseye mr-1.5 md:text-emerald-500 text-indigo-500"></i> IEP
                                    </a>
                                    
                                    <a href="{{ route('teacher.iep-goals.pdf', $student->id) }}" title="Print Full IEP" target="_blank" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-rose-50 md:bg-rose-500 border border-rose-200 md:border-none text-rose-600 md:text-white text-[11px] md:text-xs font-bold rounded-lg hover:bg-rose-100 md:hover:bg-rose-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                                        <i class="fa fa-file-pdf mr-1.5 text-rose-500 md:text-white"></i> <span class="hidden md:inline">Print IEP</span><span class="md:hidden">Print</span>
                                    </a>

                                    <a href="{{ route('teacher.daily-logs.index') }}" title="Add Daily Log" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-indigo-50 md:bg-purple-600 border border-indigo-200 md:border-none text-indigo-700 md:text-white text-[11px] md:text-xs font-bold rounded-lg hover:bg-indigo-100 md:hover:bg-purple-700 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-purple-500 focus:ring-offset-1">
                                        <i class="fa fa-pencil mr-1.5 text-indigo-500 md:text-white"></i> Log
                                    </a>

                                    @if(Auth::user()->can_edit_students)
                                        <button title="Edit Student Profile" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-white md:bg-indigo-50 border border-slate-200 md:border-indigo-300 text-slate-700 md:text-indigo-700 text-[11px] md:text-xs font-bold rounded-lg hover:bg-slate-100 md:hover:bg-indigo-100 transition-colors shadow-sm">
                                            <i class="fa fa-edit mr-1.5 md:text-indigo-500 text-slate-500"></i> Edit
                                        </button>
                                    @else
                                        <button title="Editing locked by Admin" disabled class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 md:py-1.5 bg-slate-50 md:bg-slate-100 border border-slate-200 text-slate-400 text-[11px] md:text-xs font-bold rounded-lg cursor-not-allowed shadow-sm md:shadow-none">
                                            <i class="fa fa-lock mr-1.5"></i> Locked
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-12 sm:py-16 text-center bg-white rounded-2xl md:rounded-none">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-4">
                                    <i class="fa fa-user-graduate text-2xl sm:text-3xl text-slate-300"></i>
                                </div>
                                <h6 class="text-[15px] sm:text-base font-extrabold text-slate-800 m-0 mb-1">No Assigned Students</h6>
                                <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 mb-1">No students are currently assigned to your class.</p>
                                <p class="text-[11px] sm:text-xs font-medium text-slate-400 m-0 italic">If this is a mistake, please contact your Administrator.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// --- SEARCH FILTER LOGIC ---
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('studentSearchInput');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            const tableRows = document.querySelectorAll('table tbody tr.student-row');

            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();

                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush

@endsection