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

    <div x-data="{ showCreateModal: false, showReEnrollModal: false }">

        <!-- HEADER SECTION -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight m-0">Student Profiles</h1>
                <p class="text-[13px] font-medium text-[#6B7086] mt-1 m-0">Manage all enrolled students, assignments, and medical records.</p>
            </div>
            
            <div class="flex flex-wrap gap-2.5 w-full sm:w-auto">
                <button @click="showReEnrollModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-700 transition-all shadow-sm">
                    <i class="fa fa-user-check mr-2"></i> Re-Enroll Student
                </button>
                <button @click="showCreateModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-[#4F46E5] text-white text-[13px] font-extrabold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                    <i class="fa fa-user-plus mr-2"></i> Enroll Student
                </button>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end m-0">
                
                <!-- Search -->
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

                <!-- Class -->
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
                    </select>
                </div>

                <!-- Diagnosis -->
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
                        <option value="Other">Other / Not Listed</option>
                    </select>
                </div>

                <!-- Teacher -->
                <div>
                    <label class="block text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5 flex items-center">
                        <i class="fa fa-chalkboard-teacher mr-1.5 text-[#3B82F6]"></i> Filter by Teacher
                    </label>
                    <select id="filterTeacher" class="w-full rounded-xl border border-[#9CA0AF] py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white">
                        <option value="">All Teachers</option>
                        @foreach($teachers ?? [] as $teacher)
                            <option value="{{ strtolower($teacher->name) }}">{{ $teacher->name }}</option>
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
                            <option value="archived">Archived</option>
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
                        @forelse($students ?? [] as $student)
                            <!-- Added data-teacher attribute for JS filtering -->
                            <tr x-data="{ showEditModal: false }" 
                                class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-black/15 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row"
                                data-class="{{ strtolower($student->class_name ?? '') }}"
                                data-diagnosis="{{ strtolower($student->diagnosis ?? '') }}"
                                data-status="{{ strtolower($student->status ?? '') }}"
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
                                            @elseif($student->status === 'inactive')
                                                <span class="inline-flex px-2 py-0.5 text-[9px] font-extrabold rounded bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-widest">Inactive (Frozen)</span>
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
                                        @elseif($student->status === 'inactive')
                                            <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-widest shadow-sm">Inactive (Frozen)</span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-500 uppercase tracking-widest shadow-sm">Archived</span>
                                        @endif
                                        
                                        @if($student->medical_document)
                                            <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-2.5 py-1 inline-flex items-center text-[10px] font-extrabold rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors border border-rose-200 shadow-sm uppercase tracking-widest">
                                                <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- 5. Actions Footer -->
                                <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 mt-3 md:mt-0 bg-[#F4F6FA] md:bg-transparent rounded-b-2xl md:rounded-none border-t border-black/5 md:border-none">
                                    <div class="flex justify-between items-center w-full">
                                        
                                        <!-- Mobile Med Record Button -->
                                        <div class="md:hidden">
                                            @if($student->medical_document)
                                                <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-2.5 py-1.5 inline-flex items-center text-[10px] font-extrabold uppercase tracking-widest rounded-md bg-rose-50 text-rose-600 border border-rose-200 shadow-sm">
                                                    <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                                </a>
                                            @else
                                                <span></span>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex justify-end gap-2 w-full md:w-auto">
                                            <a href="{{ route('admin.students.show', $student->id) }}" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-slate-200 hover:text-[#3B82F6] transition-colors flex items-center justify-center shadow-sm" title="View Profile">
                                                <i class="fa fa-eye text-[12px]"></i>
                                            </a>

                                            <button @click="showEditModal = true" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-slate-200 hover:text-[#F59E0B] transition-colors flex items-center justify-center shadow-sm" title="Edit">
                                                <i class="fa fa-edit text-[12px]"></i>
                                            </button>
                                            
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure you want to permanently delete this student?');" class="m-0 flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-white md:bg-[#F4F6FA] border border-black/15 md:border-black/10 text-[#6B7086] hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors flex items-center justify-center shadow-sm" title="Delete">
                                                    <i class="fa fa-trash text-[12px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- EDIT MODAL -->
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="if(confirm('Do you want to cancel editing this student?')) showEditModal = false"></div>

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
                                                                    $initialDiag = $isCustom ? 'Other' : ($student->diagnosis ?? 'Autism Spectrum Disorder (ASD)');
                                                                    $customDiagVal = $isCustom ? $student->diagnosis : '';
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
                                                                        <input type="text" name="diagnosis_other" value="{{ $customDiagVal }}" placeholder="Please specify diagnosis..." class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Class Name</label>
                                                                <select name="class_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
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
                                                                    Medical Document Upload (Optional)
                                                                    @if($student->medical_document)
                                                                        <span class="text-[10px] text-[#10B981] font-extrabold uppercase tracking-widest"><i class="fa fa-check mr-1"></i> Document on file</span>
                                                                    @endif
                                                                </label>
                                                                @if($student->medical_document)
                                                                    <div class="mb-3 p-3 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between">
                                                                        <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="text-[12px] font-extrabold text-rose-700 hover:underline flex items-center">
                                                                            <i class="fa fa-file-medical mr-2 text-rose-500"></i> View Current Document
                                                                        </a>
                                                                        <button type="button" onclick="if(confirm('Are you sure you want to delete this medical document?')) { document.getElementById('delete-doc-form-{{ $student->id }}').submit(); }" class="px-2.5 py-1 bg-white border border-rose-300 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer">
                                                                            <i class="fa fa-trash-alt mr-1"></i> Delete Document
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                                <input type="file" name="medical_document" id="edit_med_doc_input_{{ $student->id }}" onchange="handleFileInputChange(this, 'edit_med_doc_preview_{{ $student->id }}', 'edit_med_doc_filename_{{ $student->id }}')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                                                <div id="edit_med_doc_preview_{{ $student->id }}" class="hidden mt-2 p-2.5 bg-blue-50/80 border border-blue-200 rounded-xl flex items-center justify-between shadow-sm">
                                                                    <div class="flex items-center gap-2 overflow-hidden">
                                                                        <i class="fa fa-file-lines text-[#3B82F6] text-sm"></i>
                                                                        <span id="edit_med_doc_filename_{{ $student->id }}" class="text-[12px] font-extrabold text-[#1E2657] truncate"></span>
                                                                    </div>
                                                                    <button type="button" onclick="clearSelectedFile('edit_med_doc_input_{{ $student->id }}', 'edit_med_doc_preview_{{ $student->id }}')" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer shrink-0 flex items-center gap-1">
                                                                        <i class="fa fa-times text-xs"></i> Remove
                                                                    </button>
                                                                </div>
                                                                <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Upload a new file to replace the existing one. Accepted formats: PDF, JPG, PNG (Max 5MB).</p>
                                                            </div>
                                                        </div>

                                                        <h4 class="text-[#1E2657] font-extrabold mb-4 mt-6 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                                            <i class="fa fa-user-shield text-[#10B981] mr-2"></i> 2. Guardian
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 gap-5 mb-4">
                                                            <div>
                                                                <div x-data="{ 
                                                                    search: '', 
                                                                    selectedId: '{{ $student->guardian_id ?? '' }}', 
                                                                    selectedName: '{{ addslashes($student->guardian->name ?? '') }}', 
                                                                    open: false,
                                                                    guardians: [
                                                                        @foreach($guardians ?? [] as $guardian)
                                                                            { id: '{{ $guardian->id }}', name: '{{ addslashes($guardian->name) }}' },
                                                                        @endforeach
                                                                    ],
                                                                    get filteredGuardians() {
                                                                        if (!this.search) return this.guardians;
                                                                        return this.guardians.filter(g => g.name.toLowerCase().includes(this.search.toLowerCase()));
                                                                    }
                                                                }" class="relative">
                                                                    <input type="hidden" name="guardian_id" :value="selectedId">
                                                                    <div class="relative">
                                                                        <input type="text" 
                                                                               x-model="search" 
                                                                               @focus="open = true" 
                                                                               placeholder="Search guardian by name..." 
                                                                               class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#10B981] shadow-sm bg-white"
                                                                               :value="selectedName && !search ? selectedName : search"
                                                                               @input="open = true">
                                                                        <button type="button" @click="open = !open" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA0AF]">
                                                                            <i class="fa fa-search text-xs"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white border border-black/15 rounded-xl shadow-lg max-h-48 overflow-y-auto custom-scrollbar">
                                                                        <div @click="selectedId = ''; selectedName = ''; search = ''; open = false" class="px-3.5 py-2 text-[12px] font-bold text-gray-400 hover:bg-slate-50 cursor-pointer border-b border-black/5">
                                                                            -- No Guardian --
                                                                        </div>
                                                                        <template x-for="g in filteredGuardians" :key="g.id">
                                                                            <div @click="selectedId = g.id; selectedName = g.name; search = ''; open = false" 
                                                                                 class="px-3.5 py-2 text-[13px] font-bold text-[#1E2657] hover:bg-[#10B981]/10 hover:text-[#10B981] cursor-pointer flex justify-between items-center">
                                                                                <span x-text="g.name"></span>
                                                                                <i x-show="selectedId == g.id" class="fa fa-check text-[#10B981] text-xs"></i>
                                                                            </div>
                                                                        </template>
                                                                        <div x-show="filteredGuardians.length === 0" class="px-3.5 py-3 text-[12px] text-[#9CA0AF] text-center">
                                                                            No matching guardian found
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                                                        <button type="button" @click="if(confirm('Do you want to cancel editing this student?')) showEditModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-[#4F46E5] rounded-xl text-[13px] font-extrabold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                                            <i class="fa fa-save mr-1.5"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                                <form id="delete-doc-form-{{ $student->id }}" action="{{ route('admin.students.delete-document', $student->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="m-0">
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
                                        <input type="text" name="diagnosis_other" placeholder="Please specify diagnosis..." class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Class Name</label>
                                    <select name="class_name" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm">
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
                                    <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Medical Document Upload (Optional)</label>
                                    <input type="file" name="medical_document" id="create_medical_doc_input" onchange="handleFileInputChange(this, 'create_med_doc_preview', 'create_med_doc_filename')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="create_med_doc_preview" class="hidden mt-2 p-2.5 bg-blue-50/80 border border-blue-200 rounded-xl flex items-center justify-between shadow-sm">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            <i class="fa fa-file-lines text-[#3B82F6] text-sm"></i>
                                            <span id="create_med_doc_filename" class="text-[12px] font-extrabold text-[#1E2657] truncate"></span>
                                        </div>
                                        <button type="button" onclick="clearSelectedFile('create_medical_doc_input', 'create_med_doc_preview')" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer shrink-0 flex items-center gap-1">
                                            <i class="fa fa-times text-xs"></i> Remove
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-[#9CA0AF] mt-1 m-0">Accepted formats: PDF, JPG, PNG (Max 5MB).</p>
                                </div>
                            </div>
                            
                            <h4 class="text-[#1E2657] font-extrabold mb-4 mt-6 flex items-center text-[12px] uppercase tracking-widest border-b border-black/5 pb-2">
                                <i class="fa fa-user-shield text-[#10B981] mr-2"></i> 2. Guardian
                            </h4>
                            
                            <div class="grid grid-cols-1 gap-5 mb-4">
                                <div class="md:col-span-2">
                                    <div x-data="{ 
                                        search: '', 
                                        selectedId: '', 
                                        selectedName: '', 
                                        open: false,
                                        guardians: [
                                            @foreach($guardians ?? [] as $guardian)
                                                { id: '{{ $guardian->id }}', name: '{{ addslashes($guardian->name) }}' },
                                            @endforeach
                                        ],
                                        get filteredGuardians() {
                                            if (!this.search) return this.guardians;
                                            return this.guardians.filter(g => g.name.toLowerCase().includes(this.search.toLowerCase()));
                                        }
                                    }" class="relative">
                                        <input type="hidden" name="guardian_id" :value="selectedId">
                                        <div class="relative">
                                            <input type="text" 
                                                   x-model="search" 
                                                   @focus="open = true" 
                                                   placeholder="Search guardian name..." 
                                                   class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] focus:border-[#10B981] shadow-sm bg-white"
                                                   :value="selectedName && !search ? selectedName : search"
                                                   @input="open = true">
                                            <button type="button" @click="open = !open" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9CA0AF]">
                                                <i class="fa fa-search text-xs"></i>
                                            </button>
                                        </div>
                                        <div x-show="open" @click.away="open = false" x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white border border-black/15 rounded-xl shadow-lg max-h-48 overflow-y-auto custom-scrollbar">
                                            <div @click="selectedId = ''; selectedName = ''; search = ''; open = false" class="px-3.5 py-2 text-[12px] font-bold text-gray-400 hover:bg-slate-50 cursor-pointer border-b border-black/5">
                                                -- No Guardian --
                                            </div>
                                            <template x-for="g in filteredGuardians" :key="g.id">
                                                <div @click="selectedId = g.id; selectedName = g.name; search = ''; open = false" 
                                                     class="px-3.5 py-2 text-[13px] font-bold text-[#1E2657] hover:bg-[#10B981]/10 hover:text-[#10B981] cursor-pointer flex justify-between items-center">
                                                    <span x-text="g.name"></span>
                                                    <i x-show="selectedId == g.id" class="fa fa-check text-[#10B981] text-xs"></i>
                                                </div>
                                            </template>
                                            <div x-show="filteredGuardians.length === 0" class="px-3.5 py-3 text-[12px] text-[#9CA0AF] text-center">
                                                No matching guardian found
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-[#6B7086] font-medium mt-1.5 m-0">Optional — guardian accounts are created under User Management, then linked to a student here.</p>
                                </div>
                            </div>

                        </div>

                        <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                            <button type="button" @click="if(confirm('Do you want to cancel student creation?')) showCreateModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save & Enroll</button>
                        </div>
                    </form>
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
                    <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl">
                        <label class="block text-[12px] font-extrabold text-emerald-900 uppercase tracking-wider mb-2 flex items-center">
                            <i class="fa fa-search text-emerald-600 mr-2"></i> Step 1 & 2: Search & Choose Existing Student Profile
                        </label>
                        <select id="reEnrollStudentSelect" onchange="autoPopulateReEnrollForm(this)" class="w-full rounded-xl border border-[#9CA0AF] px-3.5 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#10B981] shadow-sm bg-white cursor-pointer">
                            <option value="">-- Select an Existing / Frozen Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" data-student="{{ json_encode($st) }}">
                                    {{ $st->first_name }} {{ $st->last_name }} (LRN: {{ $st->learners_reference_number ?? 'N/A' }}) — Status: {{ $st->status === 'inactive' ? 'Inactive (Frozen)' : ucfirst($st->status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 3, 4 & 5: Form -->
                    <form id="reEnrollForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf

                        <div id="reEnrollFieldsNotice" class="text-center py-8 text-[#9CA0AF] font-bold text-sm bg-slate-50 border border-dashed border-slate-200 rounded-xl">
                            <i class="fa fa-hand-pointer text-xl mb-2 block text-[#10B981]"></i>
                            Please select a student from the dropdown above to automatically populate enrollment details.
                        </div>

                        <div id="reEnrollFormFields" class="hidden space-y-5">
                            <div class="bg-blue-50/70 border border-blue-200 p-3 rounded-xl text-[12px] font-bold text-[#1E2657] flex items-center">
                                <i class="fa fa-magic text-[#3B82F6] mr-2"></i> <span><strong>Step 3:</strong> Details automatically populated! Review and update any changed information for the new school year.</span>
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

                            <!-- File Upload with Remove Option -->
                            <div>
                                <label class="block text-[12px] font-extrabold text-[#1E2657] mb-1">Medical Document Upload (Optional)</label>
                                <input type="file" name="medical_document" id="re_medical_doc_input" onchange="handleFileInputChange(this, 're_med_doc_preview', 're_med_doc_filename')" class="block w-full text-[13px] font-medium text-[#6B7086] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[12px] file:font-extrabold file:bg-[#F4F6FA] file:text-[#3B82F6] hover:file:bg-blue-50 border border-[#9CA0AF] rounded-xl cursor-pointer shadow-sm bg-white" accept=".pdf,.jpg,.jpeg,.png">
                                <div id="re_med_doc_preview" class="hidden mt-2 p-2.5 bg-blue-50/80 border border-blue-200 rounded-xl flex items-center justify-between shadow-sm">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <i class="fa fa-file-lines text-[#3B82F6] text-sm"></i>
                                        <span id="re_med_doc_filename" class="text-[12px] font-extrabold text-[#1E2657] truncate"></span>
                                    </div>
                                    <button type="button" onclick="clearSelectedFile('re_medical_doc_input', 're_med_doc_preview')" class="px-2.5 py-1 bg-white border border-rose-200 text-rose-600 text-[11px] font-extrabold rounded-lg hover:bg-rose-600 hover:text-white transition-colors shadow-sm cursor-pointer shrink-0 flex items-center gap-1">
                                        <i class="fa fa-times text-xs"></i> Remove
                                    </button>
                                </div>
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

    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@endsection

@push('scripts')
<script>
function autoPopulateReEnrollForm(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (!selectEl.value || !selectedOption || !selectedOption.dataset.student) {
        document.getElementById('reEnrollFormFields').classList.add('hidden');
        document.getElementById('reEnrollFieldsNotice').classList.remove('hidden');
        return;
    }
    const student = JSON.parse(selectedOption.dataset.student);
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