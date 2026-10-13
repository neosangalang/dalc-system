@extends('layouts.app')

@section('title', 'My Assigned Students')

@section('content')

<!-- Main Container -->
<div class="w-full max-w-[1200px] mx-auto space-y-6">

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down mb-6">
        <div class="flex items-center">
            <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
            <span class="font-extrabold text-sm">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm animate-fade-in-down mb-6">
        <div class="flex items-center mb-2 font-extrabold text-sm">
            <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Please fix the following errors:
        </div>
        <ul class="list-disc pl-5 text-sm space-y-1 font-medium m-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- HEADER SECTION -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight m-0 flex items-center">
            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm shrink-0">
                <i class="fa fa-users text-lg"></i>
            </div>
            My Assigned Students
        </h1>
        <p class="text-[13px] font-medium text-[#6B7086] mt-1 sm:ml-13 m-0">Manage and monitor all students assigned to your class.</p>
    </div>

    <!-- FILTERS -->
    <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end m-0">

            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-extrabold text-[#3B82F6] uppercase tracking-widest mb-1.5 flex items-center">
                    <i class="fa fa-search mr-1.5"></i> Search Keyword
                </label>
                <div class="relative w-full shadow-sm rounded-xl overflow-hidden border border-[#9CA0AF] bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#9CA0AF] pointer-events-none">
                        <i class="fa fa-font"></i>
                    </span>
                    <input type="text" id="filterSearchInput" class="w-full border-0 py-2 pl-9 pr-3 text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Name, ID, LRN...">
                </div>
            </div>

            <!-- Filter by Class -->
            <div>
                <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5 flex items-center">
                    <i class="fa fa-school mr-1.5 text-[#3B82F6]"></i> Filter by Class
                </label>
                <select id="filterClass" class="w-full rounded-xl border border-[#9CA0AF] py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                    <option value="">All Classes</option>
                    <option value="Playgroup Morning (Love A)">Playgroup Morning (Love A)</option>
                    <option value="Playgroup Afternoon (Love B)">Playgroup Afternoon (Love B)</option>
                    <option value="Kinder Self-Control">Kinder Self-Control</option>
                    <option value="Grade 1 - Joy">Grade 1 - Joy</option>
                    <option value="Grade 2 - Peace">Grade 2 - Peace</option>
                    <option value="Grade 3 - Patience">Grade 3 - Patience</option>
                    <option value="Grade 4 - Kindness">Grade 4 - Kindness</option>
                    <option value="Grade 5 - Goodness">Grade 5 - Goodness</option>
                    <option value="Grade 6 - Faithfulness">Grade 6 - Faithfulness</option>
                    @foreach($students->pluck('class_name')->filter()->unique() as $customClass)
                        @if(!in_array($customClass, ['Playgroup Morning (Love A)', 'Playgroup Afternoon (Love B)', 'Kinder Self-Control', 'Grade 1 - Joy', 'Grade 2 - Peace', 'Grade 3 - Patience', 'Grade 4 - Kindness', 'Grade 5 - Goodness', 'Grade 6 - Faithfulness']))
                        <option value="{{ $customClass }}">{{ $customClass }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Filter by Diagnosis -->
            <div>
                <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5 flex items-center">
                    <i class="fa fa-notes-medical mr-1.5 text-[#3B82F6]"></i> Filter by Diagnosis
                </label>
                <select id="filterDiagnosis" class="w-full rounded-xl border border-[#9CA0AF] py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                    <option value="">All Diagnoses</option>
                    <option value="Autism Spectrum Disorder (ASD)">Autism Spectrum Disorder (ASD)</option>
                    <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                    <option value="Down Syndrome">Down Syndrome</option>
                    <option value="Intellectual Disability">Intellectual Disability</option>
                    <option value="Specific Learning Disability">Specific Learning Disability</option>
                    <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                    <option value="Rett Syndrome">Rett Syndrome</option>
                    <option value="Other">Other / Not Listed</option>
                    @foreach($students->pluck('diagnosis')->filter()->unique() as $customDiag)
                        @if(!in_array($customDiag, ['Autism Spectrum Disorder (ASD)', 'Attention Deficit Hyperactivity Disorder (ADHD)', 'Down Syndrome', 'Intellectual Disability', 'Specific Learning Disability', 'Speech or Language Impairment', 'Rett Syndrome', 'Other']))
                        <option value="{{ $customDiag }}">{{ $customDiag }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Status & Reset -->
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5 flex items-center">
                        <i class="fa fa-toggle-on mr-1.5 text-[#3B82F6]"></i> Status
                    </label>
                    <select id="filterStatus" class="w-full rounded-xl border border-[#9CA0AF] py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <a href="javascript:void(0)" onclick="resetFilters()" class="mt-auto h-[38px] w-[38px] shrink-0 inline-flex items-center justify-center bg-[#F4F6FA] border border-black/10 text-[#6B7086] rounded-xl hover:bg-slate-200 hover:text-[#1E2657] transition-colors shadow-sm" title="Reset Filters">
                    <i class="fa fa-undo"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- STUDENT TABLE -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-sm md:border border-black/15 overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">

                <!-- TABLE HEADER -->
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Student</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Class & Diagnosis</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Assigned Personnel</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-center text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>

                <!-- TABLE BODY -->
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-black/5 bg-transparent md:bg-white">
                    @forelse($students as $student)
                    <tr class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row"
                        data-class="{{ strtolower($student->class_name ?? '') }}"
                        data-diagnosis="{{ strtolower($student->diagnosis ?? '') }}"
                        data-status="{{ strtolower($student->status ?? '') }}">

                        <!-- 1. Profile Area -->
                        <td class="block md:table-cell p-4 md:px-6 md:py-4 border-b border-black/5 md:border-none relative">
                            <div class="flex items-start justify-between w-full">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div class="h-10 w-10 md:h-11 md:w-11 rounded-full bg-[#EEF2FF] border border-[#C7D2FE] flex items-center justify-center text-[#4F46E5] font-extrabold text-sm shrink-0 shadow-sm">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-extrabold text-[#1E2657] leading-tight">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <div class="text-[11px] text-[#6B7086] font-medium mt-0.5">
                                            @if($student->learners_reference_number)
                                            LRN: <span class="font-bold text-[#3B82F6]">{{ $student->learners_reference_number }}</span>
                                            @endif
                                            @if($student->date_of_birth)
                                            <span class="text-slate-400">•</span> {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge (Mobile Top Right) -->
                                <div class="md:hidden shrink-0 mt-1">
                                    @if($student->status === 'active')
                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest">Active</span>
                                    @elseif($student->status === 'inactive')
                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-widest">Inactive</span>
                                    @else
                                    <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-slate-100 text-slate-500 uppercase tracking-widest">Archived</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- 2. Class & Diagnosis -->
                        <td class="block md:table-cell px-4 py-1.5 md:px-6 md:py-4">
                            <div class="flex items-start gap-2.5 md:gap-0">
                                <i class="fa fa-school text-[#3B82F6] w-4 text-center text-[11px] mt-0.5 md:hidden"></i>
                                <div>
                                    <div class="text-[13px] font-extrabold text-[#1E2657] leading-tight">{{ $student->class_name ?? 'Unassigned Class' }}</div>
                                    <div class="text-[11px] text-[#6B7086] font-medium leading-tight mt-0.5">{{ $student->diagnosis ?? 'No specified diagnosis' }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- 3. Personnel Tags -->
                        <td class="block md:table-cell px-4 py-1.5 md:px-6 md:py-4">
                            <div class="flex items-start gap-2.5 md:gap-0">
                                <i class="fa fa-users text-[#10B981] w-4 text-center text-[11px] mt-1 md:hidden"></i>
                                <div class="flex flex-wrap md:flex-col gap-1.5 md:gap-2">

                                    <div>
                                        <span class="hidden md:block text-[9px] font-extrabold tracking-widest text-[#9CA0AF] uppercase mb-0.5">Teacher</span>
                                        <div class="text-[11px] md:text-[12px] text-[#1E2657] font-bold flex items-center bg-[#F4F6FA] md:bg-transparent px-2 py-1 md:p-0 rounded-md border border-black/5 md:border-none">
                                            <i class="fa fa-chalkboard-teacher text-[#4F46E5] mr-1.5 w-4 text-center"></i> {{ $student->teacher->name ?? Auth::user()->name }}
                                        </div>
                                    </div>

                                    @if($student->guardian)
                                    <div>
                                        <span class="hidden md:block text-[9px] font-extrabold tracking-widest text-[#9CA0AF] uppercase mb-0.5">Guardian</span>
                                        <div class="text-[11px] md:text-[12px] text-[#1E2657] font-bold flex items-center bg-[#F4F6FA] md:bg-transparent px-2 py-1 md:p-0 rounded-md border border-black/5 md:border-none">
                                            <i class="fa fa-user-shield text-[#10B981] mr-1.5 w-4 text-center"></i> {{ $student->guardian->name }}
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </td>

                        <!-- 4. Status & Documents (Desktop Only) -->
                        <td class="hidden md:table-cell px-6 py-4">
                            <div class="flex flex-col gap-2 items-start">
                                @if($student->status === 'active')
                                <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest shadow-sm">Active</span>
                                @elseif($student->status === 'inactive')
                                <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-widest shadow-sm">Inactive</span>
                                @else
                                <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-500 uppercase tracking-widest shadow-sm">Archived</span>
                                @endif

                                @php
                                    $docCount = $student->documents->count() + ($student->medical_document && $student->documents->isEmpty() ? 1 : 0);
                                    $firstDoc = $student->documents->first();
                                    $docUrl = $firstDoc ? route('secure.student-document', $firstDoc->id) : route('secure.student-medical', $student->id);
                                @endphp
                                @if($docCount > 0)
                                <a href="{{ $docCount === 1 ? $docUrl : route('teacher.students.show', $student->id) }}" target="{{ $docCount === 1 ? '_blank' : '_self' }}" class="px-2.5 py-1 inline-flex items-center text-[10px] font-extrabold rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-200 shadow-sm uppercase tracking-widest" title="{{ $docCount }} document(s) on file">
                                    <i class="fa fa-folder-open mr-1.5"></i> Documents ({{ $docCount }})
                                </a>
                                @endif
                            </div>
                        </td>

                        <!-- 5. Actions Footer -->
                        <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 mt-3 md:mt-0 bg-[#F4F6FA] md:bg-transparent rounded-b-2xl md:rounded-none border-t border-black/5 md:border-none">
                            <div class="flex justify-between items-center w-full">

                                <!-- Mobile Med Record Button -->
                                <div class="md:hidden">
                                    @php
                                        $mobileDocCount = $student->documents->count() + ($student->medical_document && $student->documents->isEmpty() ? 1 : 0);
                                        $mobileFirstDoc = $student->documents->first();
                                        $mobileDocUrl = $mobileFirstDoc ? route('secure.student-document', $mobileFirstDoc->id) : route('secure.student-medical', $student->id);
                                    @endphp
                                    @if($mobileDocCount > 0)
                                    <a href="{{ $mobileDocCount === 1 ? $mobileDocUrl : route('teacher.students.show', $student->id) }}" target="{{ $mobileDocCount === 1 ? '_blank' : '_self' }}" class="px-2.5 py-1.5 inline-flex items-center text-[10px] font-extrabold uppercase tracking-widest rounded-md bg-blue-50 text-blue-600 border border-blue-200 shadow-sm">
                                        <i class="fa fa-folder-open mr-1.5"></i> Docs ({{ $mobileDocCount }})
                                    </a>
                                    @else
                                    <span></span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-end gap-2 w-full md:w-auto">
                                    <!-- View Profile Button -->
                                    <a href="{{ route('teacher.students.show', $student->id) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-slate-200 hover:text-[#8B5CF6] transition-colors flex items-center justify-center shadow-sm" title="View Student Profile">
                                        <i class="fa fa-eye text-[12px]"></i>
                                    </a>

                                    <!-- IEP Goals Button -->
                                    <a href="{{ route('teacher.iep-goals.index', ['student_id' => $student->id]) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-emerald-50 hover:text-[#10B981] hover:border-[#10B981]/30 transition-colors flex items-center justify-center shadow-sm" title="IEP Goals">
                                        <i class="fa fa-bullseye text-[12px]"></i>
                                    </a>

                                    <!-- Print Full IEP Button -->
                                    <a href="{{ route('teacher.iep-goals.pdf', $student->id) }}" target="_blank" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors flex items-center justify-center shadow-sm" title="Print Full IEP">
                                        <i class="fa fa-file-pdf text-[12px]"></i>
                                    </a>

                                    <!-- Daily Observation Logs Button -->
                                    <a href="{{ route('teacher.daily-logs.index', ['student_id' => $student->id]) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-blue-50 hover:text-[#3B82F6] hover:border-blue-200 transition-colors flex items-center justify-center shadow-sm" title="Add Daily Log">
                                        <i class="fa fa-pencil text-[12px]"></i>
                                    </a>

                                    @if(Auth::user()->can_edit_students)
                                    <!-- Edit Student Button -->
                                    <a href="{{ route('teacher.students.edit', $student->id) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-amber-50 hover:text-[#F59E0B] hover:border-amber-200 transition-colors flex items-center justify-center shadow-sm" title="Edit Student">
                                        <i class="fa fa-edit text-[12px]"></i>
                                    </a>
                                    @endif
                                </div>
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

                    <!-- NO SEARCH RESULTS PLACEHOLDER -->
                    <tr id="no-search-results" style="display: none;" class="block md:table-row">
                        <td colspan="5" class="block md:table-cell px-6 py-12 text-center text-[#6B7086] font-medium text-sm">
                            <i class="fa fa-search text-2xl mb-2 text-[#9CA0AF] block"></i>
                            No students found matching your filters.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('filterSearchInput');
        const classFilter = document.getElementById('filterClass');
        const diagnosisFilter = document.getElementById('filterDiagnosis');
        const statusFilter = document.getElementById('filterStatus');
        const noResultsMsg = document.getElementById('no-search-results');

        function filterStudents() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedClass = classFilter ? classFilter.value.toLowerCase() : '';
            const selectedDiagnosis = diagnosisFilter ? diagnosisFilter.value.toLowerCase() : '';
            const selectedStatus = statusFilter ? statusFilter.value.toLowerCase() : '';

            const tableRows = document.querySelectorAll('table tbody tr.student-row');
            let visibleCount = 0;

            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowClass = (row.getAttribute('data-class') || '').toLowerCase();
                const rowDiagnosis = (row.getAttribute('data-diagnosis') || '').toLowerCase();
                const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

                const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
                const matchesClass = selectedClass === '' || rowClass === selectedClass;
                const matchesDiagnosis = selectedDiagnosis === '' || rowDiagnosis === selectedDiagnosis || (selectedDiagnosis === 'other' && !['autism spectrum disorder (asd)', 'attention deficit hyperactivity disorder (adhd)', 'down syndrome', 'intellectual disability', 'specific learning disability', 'speech or language impairment', 'rett syndrome'].includes(rowDiagnosis));
                const matchesStatus = selectedStatus === '' || rowStatus === selectedStatus;

                if (matchesSearch && matchesClass && matchesDiagnosis && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noResultsMsg) {
                if (visibleCount === 0 && tableRows.length > 0) {
                    noResultsMsg.style.display = '';
                } else {
                    noResultsMsg.style.display = 'none';
                }
            }
        }

        if (searchInput) searchInput.addEventListener('input', filterStudents);
        if (classFilter) classFilter.addEventListener('change', filterStudents);
        if (diagnosisFilter) diagnosisFilter.addEventListener('change', filterStudents);
        if (statusFilter) statusFilter.addEventListener('change', filterStudents);
    });

    function resetFilters() {
        const searchInput = document.getElementById('filterSearchInput');
        const classFilter = document.getElementById('filterClass');
        const diagnosisFilter = document.getElementById('filterDiagnosis');
        const statusFilter = document.getElementById('filterStatus');

        if (searchInput) searchInput.value = '';
        if (classFilter) classFilter.value = '';
        if (diagnosisFilter) diagnosisFilter.value = '';
        if (statusFilter) statusFilter.value = '';

        if (searchInput) searchInput.dispatchEvent(new Event('input'));
    }
</script>
@endpush

@endsection