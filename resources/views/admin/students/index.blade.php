@extends('layouts.app')

@section('title', 'Student Profiling')

@section('content')

<!-- Main Container -->
<div class="w-full max-w-[1200px] mx-auto space-y-6">

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
        <div class="flex items-center">
            <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
            <span class="font-extrabold text-sm">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm animate-fade-in-down">
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

    <div x-data="{ 
        showCreateModal: false, 
        showReEnrollModal: false,
        showDeleteModal: false,
        deleteStudentId: null,
        deleteStudentName: '',
        deleteActionUrl: '',
        confirmText: '',
        openDeleteModal(id, name, url) {
            this.deleteStudentId = id;
            this.deleteStudentName = name;
            this.deleteActionUrl = url || ('/admin/students/' + id);
            this.confirmText = '';
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.deleteStudentId = null;
            this.deleteStudentName = '';
            this.deleteActionUrl = '';
            this.confirmText = '';
        }
    }"
    @open-delete-student.window="openDeleteModal($event.detail.id, $event.detail.name, $event.detail.url)">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div>
                <h1 class="text-xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight m-0">Student Profiles</h1>
                <p class="text-xs sm:text-[13px] font-medium text-[#6B7086] mt-0.5 m-0">Manage all enrolled students, assignments, and medical records.</p>
            </div>

            <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 w-full sm:w-auto">
                <button @click="showReEnrollModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-3 sm:px-4 py-2 sm:py-2.5 bg-[#10B981] text-white text-xs sm:text-[13px] font-extrabold rounded-xl hover:bg-emerald-700 transition-all shadow-sm active:scale-95">
                    <i class="fa fa-user-check mr-1.5 sm:mr-2"></i> Re-Enroll
                </button>
                <button @click="showCreateModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-3 sm:px-4 py-2 sm:py-2.5 bg-[#4F46E5] text-white text-xs sm:text-[13px] font-extrabold rounded-xl hover:bg-indigo-700 transition-all shadow-sm active:scale-95">
                    <i class="fa fa-user-plus mr-1.5 sm:mr-2"></i> Enroll Student
                </button>
            </div>
        </div>

        <!-- FILTERS -->
        <div x-data="{ 
                showAdvanced: false, 
                hasActiveFilters: false,
                checkActive() {
                    this.hasActiveFilters = Boolean(
                        document.getElementById('filterClass')?.value || 
                        document.getElementById('filterDiagnosis')?.value || 
                        document.getElementById('filterTeacher')?.value || 
                        document.getElementById('filterStatus')?.value
                    );
                }
             }" 
             class="bg-white border border-black/15 rounded-2xl shadow-sm p-3 sm:p-5 mb-4 sm:mb-6">
            
            <!-- Primary Search Row (Compact & Fast) -->
            <div class="flex items-center gap-2">
                <div class="flex-1 relative shadow-sm rounded-xl overflow-hidden border border-[#9CA0AF] bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 sm:pl-3 text-[#9CA0AF] pointer-events-none">
                        <i class="fa fa-search text-xs"></i>
                    </span>
                    <input type="text" id="filterSearchInput" class="w-full border-0 py-1.5 sm:py-2 pl-8 sm:pl-9 pr-3 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent" placeholder="Search student name, ID, LRN...">
                </div>

                <!-- Mobile Filter Drawer Toggle Button -->
                <button type="button" 
                        @click="showAdvanced = !showAdvanced" 
                        class="md:hidden px-3 py-2 rounded-xl border border-black/15 bg-[#F4F6FA] text-[11px] font-extrabold text-[#1E2657] flex items-center gap-1.5 shrink-0 hover:bg-slate-200 active:scale-95 transition-all">
                    <i class="fa-solid fa-sliders text-xs text-[#3B82F6]"></i>
                    <span x-text="showAdvanced ? 'Hide' : 'Filters'">Filters</span>
                    <span x-show="hasActiveFilters" x-cloak class="w-2 h-2 rounded-full bg-[#3B82F6]"></span>
                </button>

                <!-- Reset Button -->
                <button type="button" 
                        onclick="resetFilters()" 
                        @click="hasActiveFilters = false; showAdvanced = false" 
                        class="h-[34px] w-[34px] sm:h-[38px] sm:w-[38px] shrink-0 inline-flex items-center justify-center bg-[#F4F6FA] border border-black/10 text-[#6B7086] rounded-xl hover:bg-slate-200 hover:text-[#1E2657] transition-colors shadow-sm active:scale-95" 
                        title="Reset Filters">
                    <i class="fa fa-undo text-xs sm:text-sm"></i>
                </button>
            </div>

            <!-- Secondary Filters: Collapsible on Mobile, Grid on Desktop -->
            <div :class="showAdvanced ? 'grid' : 'hidden md:grid'" class="grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 pt-2.5 sm:pt-4 border-t border-black/10 mt-2.5 sm:mt-4">

                <!-- Class -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1 flex items-center">
                        <i class="fa fa-school mr-1.5 text-[#3B82F6]"></i> Class
                    </label>
                    <select id="filterClass" @change="checkActive()" class="w-full rounded-xl border border-[#9CA0AF] py-1.5 sm:py-2 px-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
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
                    </select>
                </div>

                <!-- Diagnosis -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1 flex items-center">
                        <i class="fa fa-notes-medical mr-1.5 text-[#3B82F6]"></i> Diagnosis
                    </label>
                    <select id="filterDiagnosis" @change="checkActive()" class="w-full rounded-xl border border-[#9CA0AF] py-1.5 sm:py-2 px-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                        <option value="">All Diagnoses</option>
                        <option value="Autism Spectrum Disorder (ASD)">Autism Spectrum Disorder (ASD)</option>
                        <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                        <option value="Down Syndrome">Down Syndrome</option>
                        <option value="Intellectual Disability">Intellectual Disability</option>
                        <option value="Specific Learning Disability">Specific Learning Disability</option>
                        <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                        <option value="Other">Other / Not Listed</option>
                    </select>
                </div>

                <!-- Teacher -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1 flex items-center">
                        <i class="fa fa-chalkboard-teacher mr-1.5 text-[#3B82F6]"></i> Teacher
                    </label>
                    <select id="filterTeacher" @change="checkActive()" class="w-full rounded-xl border border-[#9CA0AF] py-1.5 sm:py-2 px-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                        <option value="">All Teachers</option>
                        @foreach($teachers ?? [] as $teacher)
                        <option value="{{ strtolower($teacher->name) }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1 flex items-center">
                        <i class="fa fa-toggle-on mr-1.5 text-[#3B82F6]"></i> Status
                    </label>
                    <select id="filterStatus" @change="checkActive()" class="w-full rounded-xl border border-[#9CA0AF] py-1.5 sm:py-2 px-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
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
                        @forelse($students ?? [] as $student)
                        <!-- Added data-teacher attribute for JS filtering -->
                        <tr x-data="{ showEditModal: false, deletedDocIds: [] }"
                            class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row"
                            data-class="{{ strtolower($student->class_name ?? '') }}"
                            data-diagnosis="{{ strtolower($student->diagnosis ?? '') }}"
                            data-status="{{ strtolower(($student->status === 'inactive' ? 'archived' : $student->status) ?? '') }}"
                            data-teacher="{{ strtolower($student->teacher->name ?? 'unassigned') }}">

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
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge (Mobile Top Right) -->
                                    <div class="md:hidden shrink-0 mt-1">
                                        @if($student->status === 'active')
                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest">Active</span>
                                        @else
                                        <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-slate-100 text-slate-600 border border-black/10 uppercase tracking-widest">Archived</span>
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
                                                <i class="fa fa-chalkboard-teacher text-[#4F46E5] mr-1.5 w-4 text-center"></i> {{ $student->teacher->name ?? 'Unassigned' }}
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

                            <!-- 4. Status (Desktop Only) -->
                            <td class="hidden md:table-cell px-6 py-4">
                                <div class="flex flex-col gap-2 items-start">
                                    @if($student->status === 'active')
                                    <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest shadow-sm">Active</span>
                                    @else
                                    <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-600 border border-black/10 uppercase tracking-widest shadow-sm">Archived</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 5. Actions Footer -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 mt-3 md:mt-0 bg-[#F4F6FA] md:bg-transparent rounded-b-2xl md:rounded-none border-t border-black/5 md:border-none">
                                <div class="flex justify-between items-center w-full">

                                    <div class="md:hidden"></div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-2 w-full md:w-auto">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-slate-200 hover:text-[#3B82F6] transition-colors flex items-center justify-center shadow-sm" title="View Profile">
                                            <i class="fa fa-eye text-[12px]"></i>
                                        </a>

                                        @if($student->status === 'archived')
                                        <button type="button" disabled class="w-8 h-8 rounded-lg bg-slate-100 border border-black/10 text-slate-300 cursor-not-allowed flex items-center justify-center shadow-sm opacity-50" title="Archived student is uneditable">
                                            <i class="fa fa-edit text-[12px]"></i>
                                        </button>
                                        @else
                                        <button @click="showEditModal = true; deletedDocIds = [];" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-slate-200 hover:text-[#F59E0B] transition-colors flex items-center justify-center shadow-sm" title="Edit">
                                            <i class="fa fa-edit text-[12px]"></i>
                                        </button>
                                        @endif

                                        <button type="button" 
                                                onclick="window.triggerDeleteStudentModal({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', '{{ route('admin.students.destroy', $student->id) }}')" 
                                                class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors flex items-center justify-center shadow-sm cursor-pointer" 
                                                title="Delete Profile">
                                            <i class="fa fa-trash text-[12px]"></i>
                                        </button>
                                    </div>
                                </div>

                                @if($student->status !== 'archived')
                                <!-- EDIT MODAL -->
                                <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="if(confirm('Do you want to cancel editing this student?')) { showEditModal = false; deletedDocIds = []; }"></div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-black/15 w-full relative z-10">
                                            <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="m-0">
                                                @csrf
                                                @method('PUT')

                                                <input type="hidden" name="guardian_action" value="update">

                                                <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                                                    <h3 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-user-edit text-[#3B82F6] mr-2"></i> Edit Student</h3>
                                                    <button type="button" @click="if(confirm('Do you want to cancel editing this student?')) showEditModal = false" class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                                                        <i class="fa fa-times text-xl"></i>
                                                    </button>
                                                </div>

                                                <div class="px-5 sm:px-6 py-5 sm:py-6 max-h-[70vh] overflow-y-auto space-y-6 custom-scrollbar text-left">

                                                    <h4 class="text-[#1E2657] font-extrabold mb-4 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                                        <i class="fa fa-user-graduate text-[#3B82F6] mr-2"></i> 1. Info
                                                    </h4>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-8">
                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">First Name <span class="text-rose-500">*</span></label>
                                                            <input type="text" name="first_name" value="{{ $student->first_name }}" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Last Name <span class="text-rose-500">*</span></label>
                                                            <input type="text" name="last_name" value="{{ $student->last_name }}" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                        </div>

                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">LRN <span class="text-rose-500">*</span></label>
                                                            <input type="text" name="learners_reference_number" value="{{ $student->learners_reference_number }}" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" placeholder="e.g., 123456789012" pattern="\d{12}" maxlength="12" title="Must be a unique 12-digit number" required>
                                                            <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Must be a unique 12-digit number.</p>
                                                        </div>

                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Gender <span class="text-rose-500">*</span></label>
                                                            <select name="gender" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                                <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                                <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                                                            <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                        </div>

                                                        <div>
                                                            @php
                                                            $stdDiagnoses = [
                                                            'Autism Spectrum Disorder (ASD)',
                                                            'Attention Deficit Hyperactivity Disorder (ADHD)',
                                                            'Down Syndrome',
                                                            'Intellectual Disability',
                                                            'Specific Learning Disability',
                                                            'Speech or Language Impairment',
                                                            ];
                                                            $isCustom = !empty($student->diagnosis) && !in_array($student->diagnosis, $stdDiagnoses);
                                                            $initialDiag = ($isCustom || $student->diagnosis === 'Other') ? 'Other' : ($student->diagnosis ?? 'Autism Spectrum Disorder (ASD)');
                                                            $customDiagVal = ($isCustom && $student->diagnosis !== 'Other') ? $student->diagnosis : '';
                                                            @endphp
                                                            <div x-data="{ selectedDiagnosis: '{{ $initialDiag }}' }">
                                                                <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Diagnosis <span class="text-rose-500">*</span></label>
                                                                <select name="diagnosis" x-model="selectedDiagnosis" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                                    <option value="Autism Spectrum Disorder (ASD)">Autism Spectrum Disorder (ASD)</option>
                                                                    <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                                                    <option value="Down Syndrome">Down Syndrome</option>
                                                                    <option value="Intellectual Disability">Intellectual Disability</option>
                                                                    <option value="Specific Learning Disability">Specific Learning Disability</option>
                                                                    <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                                                                    <option value="Other">Other</option>
                                                                </select>
                                                                <div x-show="selectedDiagnosis === 'Other'" x-cloak class="mt-2">
                                                                    <input type="text" name="diagnosis_other" :required="selectedDiagnosis === 'Other'" value="{{ $customDiagVal }}" placeholder="Please specify diagnosis..." class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Class Name <span class="text-rose-500">*</span></label>
                                                            <select name="class_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                                <option value="">-- Select Class --</option>
                                                                <option value="Playgroup Morning (Love A)" {{ $student->class_name == 'Playgroup Morning (Love A)' ? 'selected' : '' }}>Playgroup Morning</option>
                                                                <option value="Playgroup Afternoon (Love B)" {{ $student->class_name == 'Playgroup Afternoon (Love B)' ? 'selected' : '' }}>Playgroup Afternoon</option>
                                                                <option value="Kinder Self-Control" {{ $student->class_name == 'Kinder Self-Control' ? 'selected' : '' }}>Kinder Self-Control</option>
                                                                <option value="Grade 1 - Joy" {{ $student->class_name == 'Grade 1 - Joy' ? 'selected' : '' }}>Grade 1 - Joy</option>
                                                                <option value="Grade 2 - Peace" {{ $student->class_name == 'Grade 2 - Peace' ? 'selected' : '' }}>Grade 2 - Peace</option>
                                                                <option value="Grade 3 - Patience" {{ $student->class_name == 'Grade 3 - Patience' ? 'selected' : '' }}>Grade 3 - Patience</option>
                                                                <option value="Grade 4 - Kindness" {{ $student->class_name == 'Grade 4 - Kindness' ? 'selected' : '' }}>Grade 4 - Kindness</option>
                                                                <option value="Grade 5 - Goodness" {{ $student->class_name == 'Grade 5 - Goodness' ? 'selected' : '' }}>Grade 5 - Goodness</option>
                                                                <option value="Grade 6 - Faithfulness" {{ $student->class_name == 'Grade 6 - Faithfulness' ? 'selected' : '' }}>Grade 6 - Faithfulness</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Status</label>
                                                            <select name="status" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
                                                                <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                <option value="archived" {{ $student->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                                            </select>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Assigned Teacher <span class="text-rose-500">*</span></label>
                                                            <select name="teacher_id" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                                                <option value="">-- Select Teacher --</option>
                                                                @foreach($teachers ?? [] as $teacher)
                                                                <option value="{{ $teacher->id }}" {{ $student->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="md:col-span-2">
                                                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1 flex justify-between items-end">
                                                                Documents (Optional)
                                                                @php
                                                                    $totalDocs = $student->documents->count();
                                                                @endphp
                                                                @if($totalDocs > 0)
                                                                <span class="text-[10px] text-[#10B981] font-extrabold uppercase tracking-widest">
                                                                    <i class="fa fa-check mr-1"></i> 
                                                                    <span x-text="Math.max(0, {{ $totalDocs }} - deletedDocIds.length)">{{ $totalDocs }}</span> document(s) on file
                                                                </span>
                                                                @endif
                                                            </label>

                                                            {{-- Current Documents List --}}
                                                            @if($totalDocs > 0)
                                                            <div class="mb-3 space-y-2">
                                                                @foreach($student->documents as $doc)
                                                                <div x-show="!deletedDocIds.includes({{ $doc->id }})" class="p-2.5 bg-blue-50/50 border border-blue-200 rounded-xl flex items-center justify-between shadow-sm">
                                                                    <a href="{{ route('secure.student-document', $doc->id) }}" target="_blank" class="text-[12px] font-extrabold text-[#3B82F6] hover:underline flex items-center gap-2 truncate">
                                                                        <i class="fa fa-file-lines text-[#3B82F6] shrink-0"></i>
                                                                        <span class="truncate">{{ $doc->original_name }}</span>
                                                                    </a>
                                                                    <button type="button" @click="deletedDocIds.push({{ $doc->id }})" class="px-2.5 py-1 bg-white border border-rose-300 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer shrink-0 ml-2">
                                                                        <i class="fa fa-trash-alt mr-1"></i> Delete
                                                                    </button>
                                                                </div>
                                                                <div x-show="deletedDocIds.includes({{ $doc->id }})" x-cloak class="p-2.5 bg-rose-50/70 border border-rose-200 border-dashed rounded-xl flex items-center justify-between shadow-sm">
                                                                    <span class="text-[12px] font-bold text-rose-500 line-through flex items-center gap-2 truncate opacity-75">
                                                                        <i class="fa fa-file-lines text-rose-400 shrink-0"></i>
                                                                        <span class="truncate">{{ $doc->original_name }}</span>
                                                                        <span class="text-[10px] uppercase font-extrabold text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded ml-1 no-underline">(Pending Deletion)</span>
                                                                    </span>
                                                                    <button type="button" @click="deletedDocIds = deletedDocIds.filter(id => id !== {{ $doc->id }})" class="px-2.5 py-1 bg-white border border-slate-300 text-slate-600 text-[11px] font-extrabold rounded-lg hover:bg-slate-100 transition-colors shadow-sm cursor-pointer shrink-0 ml-2">
                                                                        <i class="fa fa-undo mr-1"></i> Undo
                                                                    </button>
                                                                </div>
                                                                <template x-if="deletedDocIds.includes({{ $doc->id }})">
                                                                    <input type="hidden" name="deleted_documents[]" value="{{ $doc->id }}">
                                                                </template>
                                                                @endforeach
                                                            </div>
                                                            @endif

                                                            <input type="file" name="documents[]" multiple id="edit_med_doc_input_{{ $student->id }}" onchange="handleMultiFileInputChange(this, 'edit_med_doc_preview_{{ $student->id }}')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                                            <div id="edit_med_doc_preview_{{ $student->id }}" class="hidden mt-2 space-y-1.5"></div>
                                                            <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Upload one or multiple files to attach to this student. Accepted formats: PDF, JPG, PNG (Max 5MB each).</p>
                                                        </div>
                                                    </div>

                                                    <h4 class="text-[#1E2657] font-extrabold mb-4 mt-6 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                                        <i class="fa fa-user-shield text-[#10B981] mr-2"></i> 2. Guardian
                                                    </h4>

                                                    <div class="space-y-4" x-data="{ 
                                                        search: '', 
                                                        selectedId: '{{ $student->guardian_id ?? '' }}', 
                                                        selectedName: '{{ addslashes($student->guardian->name ?? '') }}', 
                                                        guardians: [
                                                            @if($student->guardian && !$guardians->contains('id', $student->guardian_id))
                                                                { id: '{{ $student->guardian->id }}', name: '{{ addslashes($student->guardian->name) }}', username: '{{ addslashes($student->guardian->username ?? '') }}' },
                                                            @endif
                                                            @foreach($guardians ?? [] as $guardian)
                                                                { id: '{{ $guardian->id }}', name: '{{ addslashes($guardian->name) }}', username: '{{ addslashes($guardian->username ?? '') }}' },
                                                            @endforeach
                                                        ],
                                                        get filteredGuardians() {
                                                            if (!this.search.trim()) return this.guardians;
                                                            const q = this.search.toLowerCase().trim();
                                                            return this.guardians.filter(g => 
                                                                g.name.toLowerCase().includes(q) || 
                                                                (g.username && g.username.toLowerCase().includes(q))
                                                            );
                                                        },
                                                        selectGuardian(g) {
                                                            this.selectedId = g.id;
                                                            this.selectedName = g.name;
                                                        },
                                                        clearGuardian() {
                                                            this.selectedId = '';
                                                            this.selectedName = '';
                                                        }
                                                    }">
                                                        <input type="hidden" name="guardian_id" id="edit_guardian_id_{{ $student->id }}" :value="selectedId">

                                                        <!-- Dynamic Search Input -->
                                                        <div>
                                                            <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-wider mb-1.5">
                                                                Search Guardian Directory
                                                            </label>
                                                            <div class="relative">
                                                                <input type="text" 
                                                                       x-model="search" 
                                                                       placeholder="Search guardian name or username..." 
                                                                       class="w-full rounded-xl border border-[#9CA0AF] pl-10 pr-9 py-2.5 text-[13px] font-medium text-[#1E2657] focus:border-[#10B981] focus:ring-[#10B981] shadow-sm bg-white transition-colors">
                                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9CA0AF]">
                                                                    <i class="fa fa-search text-xs"></i>
                                                                </span>
                                                                <button type="button" 
                                                                        x-show="search" 
                                                                        @click="search = ''" 
                                                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                                                                    <i class="fa fa-times text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Selected Guardian Banner / Chip -->
                                                        <div x-show="selectedId" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between shadow-sm animate-fade-in">
                                                            <div class="flex items-center gap-2.5 overflow-hidden">
                                                                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                                                    <i class="fa fa-user-check"></i>
                                                                </div>
                                                                <div class="truncate">
                                                                    <div class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider">Selected Guardian</div>
                                                                    <div class="text-[13px] font-extrabold text-[#1E2657] truncate" x-text="selectedName"></div>
                                                                </div>
                                                            </div>
                                                            <button type="button" 
                                                                    @click="clearGuardian()" 
                                                                    class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm shrink-0 flex items-center gap-1 cursor-pointer">
                                                                <i class="fa fa-times text-xs"></i> Clear Selection
                                                            </button>
                                                        </div>

                                                        <!-- Proper List View Container -->
                                                        <div class="border border-black/15 rounded-xl overflow-hidden shadow-sm bg-white">
                                                            <div class="px-3.5 py-2 bg-[#F4F6FA] border-b border-black/10 flex items-center justify-between">
                                                                <span class="text-[11px] font-extrabold text-[#6B7086] uppercase tracking-wider">
                                                                    Guardian List (<span x-text="filteredGuardians.length"></span>)
                                                                </span>
                                                                <span class="text-[10px] text-[#9CA0AF] font-bold">Click row to select</span>
                                                            </div>

                                                            <div class="max-h-52 overflow-y-auto divide-y divide-black/5 custom-scrollbar">
                                                                <template x-for="g in filteredGuardians" :key="g.id">
                                                                    <div @click="selectGuardian(g)"
                                                                         :class="selectedId == g.id ? 'bg-[#10B981]/10 border-l-4 border-l-[#10B981]' : 'hover:bg-slate-50 border-l-4 border-l-transparent'"
                                                                         class="px-3.5 py-2.5 flex items-center justify-between cursor-pointer transition-colors duration-150">
                                                                        <div class="flex items-center gap-3">
                                                                            <div :class="selectedId == g.id ? 'bg-[#10B981] text-white' : 'bg-slate-100 text-[#6B7086]'" 
                                                                                 class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors">
                                                                                <i class="fa fa-user"></i>
                                                                            </div>
                                                                            <div>
                                                                                <span class="text-[13px] font-extrabold text-[#1E2657] block leading-tight" x-text="g.name"></span>
                                                                                <span class="text-[11px] text-[#6B7086] font-medium" x-show="g.username" x-text="'@' + g.username"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="shrink-0 flex items-center">
                                                                            <span x-show="selectedId == g.id" class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-[#10B981] text-white flex items-center gap-1 shadow-sm">
                                                                                <i class="fa fa-check text-[9px]"></i> Selected
                                                                            </span>
                                                                            <div x-show="selectedId != g.id" class="w-5 h-5 rounded-full border-2 border-[#9CA0AF]/50 flex items-center justify-center">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>

                                                                <div x-show="filteredGuardians.length === 0" class="px-4 py-8 text-center text-[#9CA0AF]">
                                                                    <i class="fa fa-user-slash text-2xl mb-2 text-[#9CA0AF]/60 block"></i>
                                                                    <p class="text-[12px] font-bold m-0" x-text="search ? 'No guardians matching &quot;' + search + '&quot;' : 'No active guardian accounts found'"></p>
                                                                    <p class="text-[11px] m-0 mt-1 text-[#9CA0AF]">Guardian accounts are managed under User Management.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="if(confirm('Do you want to cancel editing this student?')) { showEditModal = false; deletedDocIds = []; }" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-[#4F46E5] rounded-xl text-[13px] font-extrabold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                                        <i class="fa fa-save mr-1.5"></i> Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        @endforelse

                        <!-- NO SEARCH RESULTS -->
                        <tr id="no-search-results" class="block md:table-row" style="display: none;">
                            <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white md:bg-transparent rounded-2xl md:rounded-none">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F4F6FA] mb-4 border border-black/5">
                                    <i class="fa fa-search text-2xl text-[#9CA0AF]"></i>
                                </div>
                                <h3 class="font-extrabold text-[#1E2657] text-lg mb-1">No matching students found</h3>
                                <p class="text-[#6B7086] text-[13px] font-medium m-0">Try adjusting your search filters above.</p>
                            </td>
                        </tr>

                        <!-- NO STUDENTS ENROLLED -->
                        @if(!isset($students) || count($students) == 0)
                        <tr class="block md:table-row">
                            <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white md:bg-transparent rounded-2xl md:rounded-none">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F4F6FA] mb-4 border border-black/5">
                                    <i class="fa fa-user-graduate text-2xl text-[#9CA0AF]"></i>
                                </div>
                                <h3 class="font-extrabold text-[#1E2657] text-lg mb-1">No students enrolled yet</h3>
                                <p class="text-[#6B7086] text-[13px] font-medium m-0">Click "Enroll Student" to add your first profile to the system.</p>
                            </td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="if(confirm('Do you want to cancel student creation?')) showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-black/15 w-full relative z-10">
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="m-0" onsubmit="if(!document.getElementById('create_guardian_id').value){ alert('Please select a guardian before submitting.'); return false; }">
                        @csrf

                        <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                            <h3 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-user-plus text-[#3B82F6] mr-2"></i> Enroll Student</h3>
                            <button type="button" @click="if(confirm('Do you want to cancel student creation?')) showCreateModal = false" class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="px-5 sm:px-6 py-5 sm:py-6 max-h-[70vh] overflow-y-auto custom-scrollbar">

                            <h4 class="text-[#1E2657] font-extrabold mb-4 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                <i class="fa fa-user-graduate text-[#3B82F6] mr-2"></i> 1. Info
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">First Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="first_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Last Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="last_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                </div>

                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">LRN <span class="text-rose-500">*</span></label>
                                    <input type="text" name="learners_reference_number" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" placeholder="e.g., 123456789012" pattern="\d{12}" maxlength="12" title="Must be a unique 12-digit number" required>
                                    <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Must be a unique 12-digit number.</p>
                                </div>

                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Gender <span class="text-rose-500">*</span></label>
                                    <select name="gender" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                </div>

                                <div x-data="{ createDiagnosis: 'Autism Spectrum Disorder (ASD)' }">
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Diagnosis <span class="text-rose-500">*</span></label>
                                    <select name="diagnosis" x-model="createDiagnosis" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                        <option value="Autism Spectrum Disorder (ASD)">Autism Spectrum Disorder (ASD)</option>
                                        <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                        <option value="Down Syndrome">Down Syndrome</option>
                                        <option value="Intellectual Disability">Intellectual Disability</option>
                                        <option value="Specific Learning Disability">Specific Learning Disability</option>
                                        <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <div x-show="createDiagnosis === 'Other'" x-cloak class="mt-2">
                                        <input type="text" name="diagnosis_other" :required="createDiagnosis === 'Other'" placeholder="Please specify diagnosis..." class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Class Name <span class="text-rose-500">*</span></label>
                                    <select name="class_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                        <option value="">-- Select Class --</option>
                                        <option value="Playgroup Morning (Love A)">Playgroup Morning</option>
                                        <option value="Playgroup Afternoon (Love B)">Playgroup Afternoon</option>
                                        <option value="Kinder Self-Control">Kinder Self-Control</option>
                                        <option value="Grade 1 - Joy">Grade 1 - Joy</option>
                                        <option value="Grade 2 - Peace">Grade 2 - Peace</option>
                                        <option value="Grade 3 - Patience">Grade 3 - Patience</option>
                                        <option value="Grade 4 - Kindness">Grade 4 - Kindness</option>
                                        <option value="Grade 5 - Goodness">Grade 5 - Goodness</option>
                                        <option value="Grade 6 - Faithfulness">Grade 6 - Faithfulness</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Assign Teacher <span class="text-rose-500">*</span></label>
                                    <select name="teacher_id" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                        <option value="">-- Select a Teacher --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Documents (Optional)</label>
                                    <input type="file" name="documents[]" multiple id="create_medical_doc_input" onchange="handleMultiFileInputChange(this, 'create_med_doc_preview')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="create_med_doc_preview" class="hidden mt-2 space-y-1.5"></div>
                                    <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Optional. You may upload one or multiple files. Accepted formats: PDF, JPG, PNG (Max 5MB each).</p>
                                </div>
                            </div>

                            <h4 class="text-[#1E2657] font-extrabold mb-4 mt-6 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                <i class="fa fa-user-shield text-[#10B981] mr-2"></i> 2. Guardian <span class="text-rose-500">*</span>
                            </h4>

                            <div class="space-y-4" x-data="{ 
                                search: '', 
                                selectedId: '', 
                                selectedName: '', 
                                guardians: [
                                    @foreach($guardians ?? [] as $guardian)
                                        { id: '{{ $guardian->id }}', name: '{{ addslashes($guardian->name) }}', username: '{{ addslashes($guardian->username ?? '') }}' },
                                    @endforeach
                                ],
                                get filteredGuardians() {
                                    if (!this.search.trim()) return this.guardians;
                                    const q = this.search.toLowerCase().trim();
                                    return this.guardians.filter(g => 
                                        g.name.toLowerCase().includes(q) || 
                                        (g.username && g.username.toLowerCase().includes(q))
                                    );
                                },
                                selectGuardian(g) {
                                    this.selectedId = g.id;
                                    this.selectedName = g.name;
                                },
                                clearGuardian() {
                                    this.selectedId = '';
                                    this.selectedName = '';
                                }
                            }">
                                <input type="hidden" name="guardian_id" id="create_guardian_id" :value="selectedId">

                                <!-- Dynamic Search Input -->
                                <div>
                                    <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-wider mb-1.5">
                                        Search Guardian Directory
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               x-model="search" 
                                               placeholder="Search guardian name or username..." 
                                               class="w-full rounded-xl border border-[#9CA0AF] pl-10 pr-9 py-2.5 text-[13px] font-medium text-[#1E2657] focus:border-[#10B981] focus:ring-[#10B981] shadow-sm bg-white transition-colors">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#9CA0AF]">
                                            <i class="fa fa-search text-xs"></i>
                                        </span>
                                        <button type="button" 
                                                x-show="search" 
                                                @click="search = ''" 
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                                            <i class="fa fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Selected Guardian Banner / Chip -->
                                <div x-show="selectedId" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between shadow-sm animate-fade-in">
                                    <div class="flex items-center gap-2.5 overflow-hidden">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                            <i class="fa fa-user-check"></i>
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider">Selected Guardian</div>
                                            <div class="text-[13px] font-extrabold text-[#1E2657] truncate" x-text="selectedName"></div>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="clearGuardian()" 
                                            class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm shrink-0 flex items-center gap-1 cursor-pointer">
                                        <i class="fa fa-times text-xs"></i> Clear Selection
                                    </button>
                                </div>

                                <!-- Proper List View Container -->
                                <div class="border border-black/15 rounded-xl overflow-hidden shadow-sm bg-white">
                                    <div class="px-3.5 py-2 bg-[#F4F6FA] border-b border-black/10 flex items-center justify-between">
                                        <span class="text-[11px] font-extrabold text-[#6B7086] uppercase tracking-wider">
                                            Guardian List (<span x-text="filteredGuardians.length"></span>)
                                        </span>
                                        <span class="text-[10px] text-[#9CA0AF] font-bold">Click row to select</span>
                                    </div>

                                    <div class="max-h-52 overflow-y-auto divide-y divide-black/5 custom-scrollbar">
                                        <template x-for="g in filteredGuardians" :key="g.id">
                                            <div @click="selectGuardian(g)"
                                                 :class="selectedId == g.id ? 'bg-[#10B981]/10 border-l-4 border-l-[#10B981]' : 'hover:bg-slate-50 border-l-4 border-l-transparent'"
                                                 class="px-3.5 py-2.5 flex items-center justify-between cursor-pointer transition-colors duration-150">
                                                <div class="flex items-center gap-3">
                                                    <div :class="selectedId == g.id ? 'bg-[#10B981] text-white' : 'bg-slate-100 text-[#6B7086]'" 
                                                         class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors">
                                                        <i class="fa fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-[13px] font-extrabold text-[#1E2657] block leading-tight" x-text="g.name"></span>
                                                        <span class="text-[11px] text-[#6B7086] font-medium" x-show="g.username" x-text="'@' + g.username"></span>
                                                    </div>
                                                </div>
                                                <div class="shrink-0 flex items-center">
                                                    <span x-show="selectedId == g.id" class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-[#10B981] text-white flex items-center gap-1 shadow-sm">
                                                        <i class="fa fa-check text-[9px]"></i> Selected
                                                    </span>
                                                    <div x-show="selectedId != g.id" class="w-5 h-5 rounded-full border-2 border-[#9CA0AF]/50 flex items-center justify-center">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="filteredGuardians.length === 0" class="px-4 py-8 text-center text-[#9CA0AF]">
                                            <i class="fa fa-user-slash text-2xl mb-2 text-[#9CA0AF]/60 block"></i>
                                            <p class="text-[12px] font-bold m-0" x-text="search ? 'No guardians matching &quot;' + search + '&quot;' : 'No active guardian accounts found'"></p>
                                            <p class="text-[11px] m-0 mt-1 text-[#9CA0AF]">Guardian accounts are managed under User Management.</p>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[11px] text-[#6B7086] font-medium m-0">
                                    <span class="text-rose-500 font-bold">Required</span> — Select an active guardian account to link with this student profile.
                                </p>
                            </div>

                            <!-- 3. DATA PRIVACY & PARENTAL CONSENT VERIFICATION (RA 10173) -->
                            <h4 class="text-[#1E2657] font-extrabold mb-3 mt-6 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                <i class="fa fa-shield-halved text-[#4F6AF5] mr-2"></i> 3. Data Privacy & Consent Verification (RA 10173) <span class="text-rose-500">*</span>
                            </h4>

                            <div class="p-4 bg-blue-50/60 border border-blue-200/80 rounded-xl space-y-2">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" id="verify_parental_consent" name="parental_consent_verified" value="1" required checked class="mt-0.5 rounded border-[#9CA0AF] text-[#4F6AF5] focus:ring-[#4F6AF5]">
                                    <label for="verify_parental_consent" class="text-[12px] text-[#1E2657] font-bold leading-relaxed cursor-pointer m-0">
                                        Parental / Guardian Consent &amp; Media Release Form verified on institutional record
                                    </label>
                                </div>
                                <p class="text-[11px] text-[#6B7086] leading-relaxed pl-6 m-0">
                                    In compliance with the <strong>Data Privacy Act of 2012 (RA 10173)</strong> and DepEd Child Protection guidelines, enrolling a minor certifies that the parent/guardian has formally consented to digital IEP tracking, clinical developmental logging, and DALC's <a href="{{ route('legal.privacy') }}" target="_blank" class="text-[#4F6AF5] underline font-bold">Privacy Policy</a> and <a href="{{ route('legal.parental-consent') }}" target="_blank" class="text-[#4F6AF5] underline font-bold">Parental Consent Agreement</a>.
                                </p>
                            </div>

                        </div>

                        <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                            <button type="button" @click="if(confirm('Do you want to cancel student creation?')) showCreateModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save & Enroll</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ========================================== -->
        <!-- RE-ENROLL STUDENT MODAL                    -->
        <!-- ========================================== -->
        <div x-show="showReEnrollModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showReEnrollModal = false" class="bg-white rounded-2xl shadow-xl border border-black/15 w-full max-w-3xl overflow-hidden my-8 max-h-[90vh] flex flex-col">
                <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-b border-black/10 flex justify-between items-center">
                    <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                        <i class="fa fa-user-check text-[#10B981] mr-2.5"></i> Re-Enroll Student for New School Year
                    </h3>
                    <button type="button" @click="showReEnrollModal = false" class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                        <i class="fa fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5">
                    <!-- Step 1 & 2: Select Student -->
                    <div x-data="{
                            search: '',
                            isOpen: false,
                            selectedStudent: null,
                            students: {{ json_encode($archivedStudents ?? []) }},
                            get filteredStudents() {
                                if (!this.search.trim()) {
                                    return this.students;
                                }
                                const q = this.search.toLowerCase().trim();
                                return this.students.filter(s => {
                                    const fullName = ((s.first_name || '') + ' ' + (s.last_name || '')).toLowerCase();
                                    const lrn = String(s.learners_reference_number || '').toLowerCase();
                                    const id = String(s.id);
                                    const className = String(s.class_name || '').toLowerCase();
                                    return fullName.includes(q) || lrn.includes(q) || id.includes(q) || className.includes(q);
                                });
                            },
                            selectStudent(student) {
                                this.selectedStudent = student;
                                this.isOpen = false;
                                this.search = '';
                                const hiddenSelect = document.getElementById('reEnrollStudentSelect');
                                if (hiddenSelect) {
                                    hiddenSelect.value = student.id;
                                }
                                autoPopulateReEnrollForm(student);
                            },
                            clearSelection() {
                                this.selectedStudent = null;
                                this.search = '';
                                const hiddenSelect = document.getElementById('reEnrollStudentSelect');
                                if (hiddenSelect) {
                                    hiddenSelect.value = '';
                                }
                                autoPopulateReEnrollForm(null);
                            }
                        }"
                        @click.outside="isOpen = false"
                        class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl space-y-3">
                        
                        <div class="flex items-center justify-between">
                            <label class="block text-[12px] font-extrabold text-emerald-900 uppercase tracking-wider flex items-center m-0">
                                <i class="fa fa-search text-emerald-600 mr-2"></i> Step 1 & 2: Search & Choose Existing Student Profile
                            </label>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Archived Students Only
                            </span>
                        </div>

                        <!-- Selected Student Display Card -->
                        <div x-show="selectedStudent" x-cloak class="p-3 bg-white border border-emerald-300 rounded-xl shadow-xs flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-200">
                                    <span x-text="(selectedStudent?.first_name?.charAt(0) || '') + (selectedStudent?.last_name?.charAt(0) || '')"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs sm:text-[13px] font-extrabold text-[#1E2657] truncate m-0" x-text="(selectedStudent?.first_name || '') + ' ' + (selectedStudent?.last_name || '')"></h4>
                                        <span class="px-1.5 py-0.5 text-[9px] font-black uppercase rounded bg-slate-100 text-slate-700 border border-slate-200">Archived</span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-[#6B7086] mt-0.5 font-medium">
                                        <span>LRN: <strong class="text-[#3B82F6]" x-text="selectedStudent?.learners_reference_number || 'N/A'"></strong></span>
                                        <span class="hidden sm:inline">•</span>
                                        <span class="truncate">Class: <span x-text="selectedStudent?.class_name || 'Unassigned'"></span></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" @click="clearSelection()" class="shrink-0 px-2.5 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border border-rose-200 flex items-center gap-1.5 cursor-pointer">
                                <i class="fa fa-sync-alt text-[10px]"></i>
                                <span>Change</span>
                            </button>
                        </div>

                        <!-- Searchable Input & Dropdown -->
                        <div x-show="!selectedStudent" class="relative">
                            <div class="relative shadow-xs rounded-xl overflow-hidden border border-[#9CA0AF] bg-white focus-within:border-[#10B981] focus-within:ring-2 focus-within:ring-emerald-100 transition-all">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#9CA0AF] pointer-events-none">
                                    <i class="fa fa-search text-xs"></i>
                                </span>
                                <input type="text" 
                                       x-model="search" 
                                       @focus="isOpen = true"
                                       @input="isOpen = true"
                                       @keydown.escape="isOpen = false"
                                       placeholder="Search student name, ID, LRN..." 
                                       class="w-full border-0 py-2.5 pl-9 pr-9 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] bg-transparent">
                                <button type="button" 
                                        x-show="search.length > 0" 
                                        @click="search = ''; isOpen = true" 
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#9CA0AF] hover:text-[#1E2657] transition-colors">
                                    <i class="fa fa-times text-xs"></i>
                                </button>
                            </div>

                            <!-- Dropdown Menu -->
                            <div x-show="isOpen" 
                                 x-transition.opacity.duration.150ms
                                 x-cloak
                                 class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-black/15 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto custom-scrollbar divide-y divide-black/5">
                                
                                <template x-if="students.length === 0">
                                    <div class="p-4 text-center text-[#9CA0AF] text-xs font-bold">
                                        <i class="fa fa-folder-open mb-1 block text-slate-300 text-base"></i>
                                        No archived students available to re-enroll.
                                    </div>
                                </template>

                                <template x-if="students.length > 0 && filteredStudents.length === 0">
                                    <div class="p-4 text-center text-[#9CA0AF] text-xs font-bold">
                                        <i class="fa fa-search-minus mb-1 block text-slate-300 text-base"></i>
                                        No archived student found matching "<span class="text-[#1E2657]" x-text="search"></span>".
                                    </div>
                                </template>

                                <template x-for="st in filteredStudents" :key="st.id">
                                    <button type="button" 
                                            @click="selectStudent(st)" 
                                            class="w-full p-2.5 sm:px-3.5 sm:py-2.5 text-left hover:bg-emerald-50/70 transition-colors flex items-center justify-between gap-2 group cursor-pointer">
                                        <div class="min-w-0 flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-emerald-100 group-hover:text-emerald-700 text-slate-600 flex items-center justify-center text-[10px] font-black shrink-0 transition-colors">
                                                <span x-text="(st.first_name?.charAt(0) || '') + (st.last_name?.charAt(0) || '')"></span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs sm:text-[13px] font-extrabold text-[#1E2657] group-hover:text-emerald-900 truncate" x-text="st.first_name + ' ' + st.last_name"></div>
                                                <div class="text-[10px] text-[#6B7086] font-medium flex items-center gap-2 mt-0.5">
                                                    <span>LRN: <span class="font-bold text-[#3B82F6]" x-text="st.learners_reference_number || 'N/A'"></span></span>
                                                    <span>•</span>
                                                    <span class="truncate" x-text="st.class_name || 'Unassigned'"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md bg-slate-100 text-slate-600 uppercase tracking-wider group-hover:bg-emerald-100 group-hover:text-emerald-800">
                                                Archived
                                            </span>
                                            <i class="fa fa-chevron-right text-[10px] text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Hidden select for fallback / form inspection compatibility -->
                        <select id="reEnrollStudentSelect" class="hidden" onchange="autoPopulateReEnrollForm(this)">
                            <option value="">-- Select an Archived Student --</option>
                            @foreach($archivedStudents ?? [] as $st)
                            <option value="{{ $st->id }}" data-student="{{ json_encode($st) }}">
                                {{ $st->first_name }} {{ $st->last_name }} (LRN: {{ $st->learners_reference_number ?? 'N/A' }}) — Status: Archived
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 3, 4 & 5: Form -->
                    <form id="reEnrollForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf

                        <div id="reEnrollFieldsNotice" class="text-center py-8 text-[#9CA0AF] font-bold text-sm bg-slate-50 border border-dashed border-slate-200 rounded-xl">
                            <i class="fa fa-hand-pointer text-xl mb-2 block text-[#10B981]"></i>
                            Please search and select an archived student from above to automatically populate enrollment details.
                        </div>

                        <div id="reEnrollFormFields" class="hidden space-y-5">
                            <div class="bg-blue-50/70 border border-blue-200 p-3 rounded-xl text-[12px] font-bold text-[#1E2657] flex items-center">
                                <i class="fa fa-magic text-[#3B82F6] mr-2"></i> <span><strong>Step 3:</strong> Details automatically populated! Review and update any changed information for the new school year.</span>
                            </div>

                            <div class="bg-emerald-50/70 border border-emerald-200 p-3 rounded-xl text-[12px] font-semibold text-emerald-900 flex items-start">
                                <i class="fa fa-bullseye text-[#10B981] mr-2.5 mt-0.5 text-sm shrink-0"></i>
                                <span><strong>New School Year IEP:</strong> Re-enrolling initializes a fresh IEP slate so the newly assigned teacher can formulate new baseline PLOP and goals. Previous IEP achievements remain safely preserved in the Year-End Archive.</span>
                            </div>

                            <!-- Student Personal Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">First Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="first_name" id="re_first_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Last Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="last_name" id="re_last_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">LRN (12 Digits) <span class="text-rose-500">*</span></label>
                                    <input type="text" name="learners_reference_number" id="re_lrn" maxlength="12" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                                    <input type="date" name="date_of_birth" id="re_dob" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Gender <span class="text-rose-500">*</span></label>
                                    <select name="gender" id="re_gender" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Diagnosis <span class="text-rose-500">*</span></label>
                                <input type="text" name="diagnosis" id="re_diagnosis" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                            </div>

                            <!-- Class & Teacher Assignment -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">New Target Class / Grade <span class="text-rose-500">*</span></label>
                                    <select name="class_name" id="re_class_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                        <option value="Playgroup Morning (Love A)">Playgroup Morning</option>
                                        <option value="Playgroup Afternoon (Love B)">Playgroup Afternoon</option>
                                        <option value="Kinder Self-Control">Kinder Self-Control</option>
                                        <option value="Grade 1 - Joy">Grade 1 - Joy</option>
                                        <option value="Grade 2 - Peace">Grade 2 - Peace</option>
                                        <option value="Grade 3 - Patience">Grade 3 - Patience</option>
                                        <option value="Grade 4 - Kindness">Grade 4 - Kindness</option>
                                        <option value="Grade 5 - Goodness">Grade 5 - Goodness</option>
                                        <option value="Grade 6 - Faithfulness">Grade 6 - Faithfulness</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Assigned Teacher <span class="text-rose-500">*</span></label>
                                    <select name="teacher_id" id="re_teacher_id" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]" required>
                                        <option value="">-- Select Teacher --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Guardian Link -->
                            <div>
                                <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Link Guardian (Optional)</label>
                                <select name="guardian_id" id="re_guardian_id" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2 text-[13px] font-bold text-[#1E2657]">
                                    <option value="">-- No Guardian Linked --</option>
                                    @foreach($guardians ?? [] as $guardian)
                                    <option value="{{ $guardian->id }}">{{ $guardian->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Attached Records from Latest Archive Snapshot Preview -->
                            <div id="re_enroll_archive_docs_card" class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                                            <i class="fa fa-folder-open"></i>
                                        </span>
                                        <div>
                                            <h4 class="text-[12px] font-extrabold text-[#1E2657] m-0" id="re_archive_docs_title">Attached Records from Latest Snapshot</h4>
                                            <p class="text-[10px] text-[#6B7086] m-0" id="re_archive_docs_subtitle">Loading previous documents...</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full flex items-center gap-1">
                                        <i class="fa fa-shield-alt"></i> Auto-Cloned & Decoupled
                                    </span>
                                </div>

                                <!-- Document Items Container -->
                                <div id="re_archive_docs_list" class="space-y-2">
                                    <div class="text-center py-3 text-[11px] text-[#9CA0AF] italic">
                                        <i class="fa fa-spinner fa-spin mr-1"></i> Fetching attached snapshot records...
                                    </div>
                                </div>

                                <div class="bg-blue-50/70 border border-blue-200/80 rounded-lg p-2.5 text-[11px] text-blue-900 flex items-start gap-2">
                                    <i class="fa fa-info-circle text-blue-500 mt-0.5 shrink-0"></i>
                                    <span><strong>Decoupled Archiving:</strong> The records listed above are preserved in the historical archive. Upon re-enrollment, they are automatically duplicated into new, independent files for the new academic cycle. Modifying or deleting documents during the year will never break historical snapshot links.</span>
                                </div>
                            </div>

                            <!-- File Upload with Remove Option -->
                            <div>
                                <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Upload Additional Documents (Optional)</label>
                                <input type="file" name="documents[]" multiple id="re_medical_doc_input" onchange="handleMultiFileInputChange(this, 're_med_doc_preview')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                <div id="re_med_doc_preview" class="hidden mt-2 space-y-1.5"></div>
                                <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Optional. Upload newly received evaluations, assessments, or medical clearances for the new school year (PDF, JPG, PNG, max 5MB each).</p>
                            </div>

                            <div class="pt-4 border-t border-black/10 flex justify-end gap-3">
                                <button type="button" @click="showReEnrollModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 transition-colors">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
                                    <i class="fa fa-check-circle"></i> Step 5: Confirm & Re-Enroll Student
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- END RE-ENROLL STUDENT MODAL -->

        <!-- DELETE PROFILE CONFIRMATION MODAL -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[110] overflow-y-auto text-left" aria-labelledby="delete-profile-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="closeDeleteModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDeleteModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-black/15 p-6 relative z-50">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100 shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-extrabold text-[#1E2657] m-0" id="delete-profile-modal-title">Delete Student Profile</h4>
                            <p class="text-xs text-[#6B7086] mt-1.5 font-medium leading-relaxed">
                                Are you sure you want to permanently delete the profile of <strong class="text-rose-600" x-text="deleteStudentName"></strong>? This will permanently delete all associated progress records, IEP goals, daily logs, and attached documents. This action <strong class="text-rose-600">cannot be undone</strong>.
                            </p>
                        </div>
                    </div>

                    <form :action="deleteActionUrl" method="POST" class="mt-5 space-y-4 m-0">
                        @csrf
                        @method('DELETE')

                        <div class="bg-rose-50/50 border border-rose-200/60 rounded-xl p-3.5">
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-wider mb-1.5">
                                Type <span class="text-rose-600 font-black">confirm</span> to proceed:
                            </label>
                            <input type="text" 
                                   x-model="confirmText" 
                                   placeholder="Type 'confirm' here" 
                                   autocomplete="off"
                                   autocapitalize="off"
                                   spellcheck="false"
                                   class="w-full rounded-xl border border-rose-300 px-3.5 py-2.5 text-xs sm:text-[13px] font-bold text-[#1E2657] focus:border-rose-600 focus:ring-2 focus:ring-rose-200 bg-white transition-all shadow-xs">
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-black/5">
                            <button type="button" @click="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#6B7086] hover:text-[#1E2657] text-xs font-extrabold rounded-xl transition-colors cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    :disabled="confirmText.trim().toLowerCase() !== 'confirm'"
                                    :class="confirmText.trim().toLowerCase() === 'confirm' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm cursor-pointer active:scale-95' : 'bg-rose-300 text-white/70 cursor-not-allowed opacity-60 pointer-events-none'"
                                    class="px-4 py-2 text-xs font-extrabold rounded-xl transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-trash-can text-[11px]"></i> Permanently Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<form id="global-delete-doc-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    window.triggerDeleteStudentModal = function(id, name, url) {
        window.dispatchEvent(new CustomEvent('open-delete-student', {
            detail: { id: id, name: name, url: url }
        }));
    };

    function confirmDeleteDocument(actionUrl) {
        if (confirm('Are you sure you want to delete this document?')) {
            const form = document.getElementById('global-delete-doc-form');
            if (form) {
                form.action = actionUrl;
                form.submit();
            }
        }
    }
    function autoPopulateReEnrollForm(target) {
        let student = null;
        if (target && target.options) {
            const selectedOption = target.options[target.selectedIndex];
            if (target.value && selectedOption && selectedOption.dataset.student) {
                student = JSON.parse(selectedOption.dataset.student);
            }
        } else if (target && typeof target === 'object' && target.id) {
            student = target;
        }

        if (!student) {
            document.getElementById('reEnrollFormFields').classList.add('hidden');
            document.getElementById('reEnrollFieldsNotice').classList.remove('hidden');
            return;
        }
        document.getElementById('reEnrollForm').action = '/admin/students/' + student.id + '/re-enroll';
        document.getElementById('re_first_name').value = student.first_name || '';
        document.getElementById('re_last_name').value = student.last_name || '';
        document.getElementById('re_lrn').value = student.learners_reference_number || '';
        document.getElementById('re_dob').value = student.date_of_birth ? student.date_of_birth.substring(0, 10) : '';
        document.getElementById('re_gender').value = student.gender || 'Male';
        document.getElementById('re_diagnosis').value = student.diagnosis || '';
        if (student.class_name) {
            document.getElementById('re_class_name').value = student.class_name;
        }
        if (student.teacher_id) {
            document.getElementById('re_teacher_id').value = student.teacher_id;
        }
        if (student.guardian_id) {
            document.getElementById('re_guardian_id').value = student.guardian_id;
        }

        // Fetch and preview latest archived snapshot documents
        const docsTitle = document.getElementById('re_archive_docs_title');
        const docsSubtitle = document.getElementById('re_archive_docs_subtitle');
        const docsList = document.getElementById('re_archive_docs_list');

        if (docsList) {
            docsList.innerHTML = '<div class="text-center py-3 text-[11px] text-[#9CA0AF] italic"><i class="fa fa-spinner fa-spin mr-1"></i> Fetching attached snapshot records...</div>';
            if (docsSubtitle) docsSubtitle.textContent = 'Loading previous records...';

            fetch('/admin/students/' + student.id + '/archived-documents')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.documents && data.documents.length > 0) {
                        if (docsTitle) {
                            docsTitle.textContent = 'Attached Records from Previous Snapshot' + (data.school_year ? ' (SY ' + data.school_year + ')' : '');
                        }
                        if (docsSubtitle) {
                            docsSubtitle.textContent = (data.archived_at ? 'Archived on ' + data.archived_at + ' • ' : '') + data.documents.length + ' file(s) found';
                        }
                        
                        let html = '';
                        data.documents.forEach((doc) => {
                            const isPdf = (doc.mime_type && doc.mime_type.includes('pdf')) || (doc.original_name && doc.original_name.toLowerCase().endsWith('.pdf'));
                            const iconClass = isPdf ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-blue-500';
                            
                            html += `
                                <div class="flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-lg hover:border-slate-300 transition-colors shadow-sm">
                                    <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                                            <i class="fa ${iconClass} text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-bold text-[#1E2657] truncate m-0" title="${doc.original_name}">${doc.original_name}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] text-[#9CA0AF] font-bold">${doc.file_size}</span>
                                                <span class="text-[9px] bg-slate-100 text-slate-600 font-extrabold px-1.5 py-0.5 rounded">Historical Snapshot</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="${doc.view_url}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-extrabold text-[#3B82F6] hover:bg-blue-50 border border-blue-200 transition-colors">
                                            <i class="fa fa-external-link-alt text-[10px]"></i> View / Preview
                                        </a>
                                    </div>
                                </div>
                            `;
                        });
                        docsList.innerHTML = html;
                    } else {
                        if (docsTitle) docsTitle.textContent = 'Attached Records from Previous Snapshot';
                        if (docsSubtitle) docsSubtitle.textContent = data.school_year ? 'Latest Archive SY ' + data.school_year : 'No previous archive';
                        docsList.innerHTML = `
                            <div class="text-center py-4 bg-white rounded-lg border border-dashed border-slate-200 text-[#9CA0AF]">
                                <i class="fa fa-folder-open text-slate-300 text-xl mb-1 block"></i>
                                <span class="text-[11px] font-bold">No documents attached to previous archive snapshot.</span>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    console.error('Error fetching archived documents:', err);
                    docsList.innerHTML = '<div class="text-center py-3 text-[11px] text-rose-500 font-bold">Failed to load archived documents. You may still upload new documents below.</div>';
                });
        }

        document.getElementById('reEnrollFormFields').classList.remove('hidden');
        document.getElementById('reEnrollFieldsNotice').classList.add('hidden');
    }

    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('filterSearchInput');
        const classFilter = document.getElementById('filterClass');
        const diagnosisFilter = document.getElementById('filterDiagnosis');
        const teacherFilter = document.getElementById('filterTeacher');
        const statusFilter = document.getElementById('filterStatus');
        const noResultsMsg = document.getElementById('no-search-results');

        function filterStudents() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedClass = classFilter ? classFilter.value.toLowerCase() : '';
            const selectedDiagnosis = diagnosisFilter ? diagnosisFilter.value.toLowerCase() : '';
            const selectedTeacher = teacherFilter ? teacherFilter.value.toLowerCase() : '';
            const selectedStatus = statusFilter ? statusFilter.value.toLowerCase() : '';

            const tableRows = document.querySelectorAll('table tbody tr.student-row');
            let visibleCount = 0;

            tableRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                const rowClass = (row.getAttribute('data-class') || '').toLowerCase();
                const rowDiagnosis = (row.getAttribute('data-diagnosis') || '').toLowerCase();
                const rowTeacher = (row.getAttribute('data-teacher') || '').toLowerCase();
                const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

                const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
                const matchesClass = selectedClass === '' || rowClass === selectedClass;
                const matchesDiagnosis = selectedDiagnosis === '' || rowDiagnosis === selectedDiagnosis;
                const matchesTeacher = selectedTeacher === '' || rowTeacher === selectedTeacher;
                const matchesStatus = selectedStatus === '' || rowStatus === selectedStatus;

                if (matchesSearch && matchesClass && matchesDiagnosis && matchesTeacher && matchesStatus) {
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
        if (teacherFilter) teacherFilter.addEventListener('change', filterStudents);
        if (statusFilter) statusFilter.addEventListener('change', filterStudents);
    });

    function resetFilters() {
        const searchInput = document.getElementById('filterSearchInput');
        const classFilter = document.getElementById('filterClass');
        const diagnosisFilter = document.getElementById('filterDiagnosis');
        const teacherFilter = document.getElementById('filterTeacher');
        const statusFilter = document.getElementById('filterStatus');

        if (searchInput) searchInput.value = '';
        if (classFilter) classFilter.value = '';
        if (diagnosisFilter) diagnosisFilter.value = '';
        if (teacherFilter) teacherFilter.value = '';
        if (statusFilter) statusFilter.value = '';

        if (searchInput) searchInput.dispatchEvent(new Event('input'));
    }
</script>
@endpush