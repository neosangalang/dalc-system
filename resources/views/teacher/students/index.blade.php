@extends('layouts.app')

@section('title', 'My Students')

@section('content')

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <!-- HEADER & SEARCH -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full mb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] m-0 flex items-center tracking-tight">
                <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm shrink-0">
                    <i class="fa fa-users text-lg"></i>
                </div>
                My Assigned Students
            </h1>
        </div>
        
        <div class="w-full sm:w-auto">
            <div class="relative w-full sm:w-80 shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#9CA0AF] pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="studentSearchInput" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] sm:text-sm font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] placeholder:font-medium bg-transparent" placeholder="Search by name or exceptionality...">
            </div>
        </div>
    </div>

    <!-- TABLE AREA -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border border-black/15 overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER -->
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Student Name</th>
                        <th scope="col" class="px-6 py-4 text-center text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Age</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Exceptionality</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-black/5 bg-transparent md:bg-white">
                    @forelse($students as $student)
                        <tr class="block md:table-row relative bg-white md:bg-transparent rounded-[16px] md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row pt-2 md:pt-0">
                            
                            <!-- 1. Name & Avatar -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center text-white font-extrabold text-sm sm:text-base shadow-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shrink-0 border border-black/10">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <span class="text-[14px] sm:text-[15px] font-extrabold text-[#1E2657] leading-tight">{{ $student->last_name }}, {{ $student->first_name }}</span>
                                </div>
                            </td>

                            <!-- 2. Age -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-2 md:px-6 md:py-4 border-b border-black/5 md:border-none text-[13px] font-bold text-[#6B7086] mt-1 md:mt-0 md:text-center">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Age</span>
                                {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs
                            </td>
                            
                            <!-- 3. Exceptionality -->
                            <td class="flex justify-between items-center md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest md:hidden">Exceptionality</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#8B5CF6]/10 text-[#8B5CF6] uppercase tracking-widest border border-[#8B5CF6]/20 shadow-sm text-right md:text-left leading-tight">
                                    {{ $student->exceptionality ?? 'Unspecified' }}
                                </span>
                            </td>

                            <!-- 4. Status Badge -->
                            <td class="absolute top-4 right-4 md:static md:table-cell md:px-6 md:py-4">
                                @if($student->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#10B981]/10 text-[#10B981] uppercase tracking-widest border border-[#10B981]/20 shadow-sm">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-slate-100 text-[#6B7086] uppercase tracking-widest border border-black/10 shadow-sm">Inactive</span>
                                @endif
                            </td>
                            
                            <!-- 5. Actions Footer -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 bg-[#F4F6FA] md:bg-transparent rounded-b-[16px] md:rounded-none">
                                <div class="grid grid-cols-2 md:flex md:justify-end gap-2 w-full md:w-auto">
                                    
                                    <a href="{{ route('teacher.iep-goals.index') }}" title="View IEP Goals" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#10B981] hover:border-[#10B981]/30 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-bullseye mr-1.5 text-[#10B981]"></i> IEP
                                    </a>
                                    
                                    <a href="{{ route('teacher.iep-goals.pdf', $student->id) }}" title="Print Full IEP" target="_blank" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-file-pdf mr-1.5 text-rose-500"></i> <span class="hidden md:inline">Print IEP</span><span class="md:hidden">Print</span>
                                    </a>

                                    <a href="{{ route('teacher.daily-logs.index') }}" title="Add Daily Log" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 bg-[#3B82F6] border border-transparent text-white text-[12px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
                                        <i class="fa fa-pencil mr-1.5"></i> Log
                                    </a>

                                    @if(Auth::user()->can_edit_students)
                                        <button title="Edit Student Profile" class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#1E2657] transition-colors shadow-sm">
                                            <i class="fa fa-edit mr-1.5 text-[#1E2657]"></i> Edit
                                        </button>
                                    @else
                                        <button title="Editing locked by Admin" disabled class="w-full md:w-auto inline-flex justify-center md:justify-start items-center px-3 py-2 bg-[#F4F6FA] border border-black/5 text-[#9CA0AF] text-[12px] font-extrabold rounded-xl cursor-not-allowed shadow-sm md:shadow-none">
                                            <i class="fa fa-lock mr-1.5"></i> Locked
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-12 sm:py-16 text-center bg-white md:bg-transparent rounded-2xl md:rounded-none">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-[#F4F6FA] border border-black/5 flex items-center justify-center mx-auto mb-4">
                                    <i class="fa fa-user-graduate text-2xl sm:text-3xl text-[#9CA0AF]"></i>
                                </div>
                                <h6 class="text-[15px] sm:text-base font-extrabold text-[#1E2657] m-0 mb-1">No Assigned Students</h6>
                                <p class="text-[13px] font-medium text-[#6B7086] m-0 mb-1">No students are currently assigned to your class.</p>
                                <p class="text-[11px] font-medium text-[#9CA0AF] m-0 italic">If this is a mistake, please contact your Administrator.</p>
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