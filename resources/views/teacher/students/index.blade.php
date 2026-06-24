@extends('layouts.app')

@section('title', 'My Students')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h4 class="text-2xl font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                    <i class="fa fa-users"></i>
                </div>
                My Assigned Students
            </h4>
        </div>
        
        <div class="w-full sm:w-auto">
            <div class="relative w-full sm:w-80 shadow-sm rounded-xl overflow-hidden border border-slate-200 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="studentSearchInput" class="w-full border-0 py-2.5 pl-10 pr-3 text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 placeholder:font-medium bg-transparent" placeholder="Search by name or exceptionality...">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student Name</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Age</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Exceptionality</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50/50 transition-colors duration-150 student-row">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-extrabold text-sm shadow-sm bg-gradient-to-br from-indigo-500 to-purple-500 shrink-0">
                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-extrabold text-slate-800">{{ $student->last_name }}, {{ $student->first_name }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-sm font-bold text-slate-600">
                                {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 uppercase tracking-widest border border-indigo-100 shadow-sm">
                                    {{ $student->exceptionality ?? 'Unspecified' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($student->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 uppercase tracking-widest">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-800 uppercase tracking-widest">Inactive</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    
                                    <a href="{{ route('teacher.iep-goals.index') }}" title="View IEP Goals" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-600 text-xs font-bold rounded-lg hover:bg-emerald-100 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-bullseye mr-1.5"></i> IEP
                                    </a>
                                    
                                    <a href="{{ route('teacher.iep-goals.pdf', $student->id) }}" title="Print Full IEP" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-rose-500 text-white text-xs font-bold rounded-lg hover:bg-rose-600 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-file-pdf mr-1.5"></i> Print IEP
                                    </a>

                                    <a href="{{ route('teacher.daily-logs.index') }}" title="Add Daily Log" class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-bold rounded-lg hover:bg-purple-700 transition-colors shadow-sm decoration-transparent">
                                        <i class="fa fa-pencil mr-1.5"></i> Log
                                    </a>

                                    @if(Auth::user()->can_edit_students)
                                        <button title="Edit Student Profile" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-100 transition-colors shadow-sm">
                                            <i class="fa fa-edit mr-1.5"></i> Edit
                                        </button>
                                    @else
                                        <button title="Editing locked by Admin" disabled class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-lg cursor-not-allowed">
                                            <i class="fa fa-lock mr-1.5"></i> Locked
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fa fa-user-graduate text-3xl text-slate-300"></i>
                                </div>
                                <h6 class="text-base font-extrabold text-slate-800 m-0 mb-1">No Assigned Students</h6>
                                <p class="text-sm font-medium text-slate-500 m-0 mb-1">No students are currently assigned to your class.</p>
                                <p class="text-xs font-medium text-slate-400 m-0 italic">If this is a mistake, please contact your Administrator.</p>
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
            
            // Target all rows that have the class 'student-row'
            const tableRows = document.querySelectorAll('table tbody tr.student-row');

            tableRows.forEach(row => {
                // Grab the text of the entire row (Name, Age, Exceptionality)
                const rowText = row.textContent.toLowerCase();

                // If the text includes the search term, show it. Otherwise, hide it.
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