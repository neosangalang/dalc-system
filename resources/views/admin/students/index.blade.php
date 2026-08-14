@extends('layouts.app')

@section('title', 'Student Profiling')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div class="mb-5 sm:mb-6 bg-emerald-50 border-[3px] border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-600 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 sm:mb-6 bg-red-50 border-[3px] border-red-300 text-red-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-red-600"></i> Please fix the following errors:
            </div>
            <ul class="list-disc pl-5 text-sm space-y-1 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ showCreateModal: false }">

        <div class="flex flex-wrap justify-between items-center mb-5 sm:mb-6 gap-4">
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mb-1">Student Profiles</h3>
                <p class="text-slate-500 text-[13px] sm:text-sm font-medium">Manage all enrolled students, assignments, and medical records.</p>
            </div>
            
            <button @click="showCreateModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-indigo-600 border-2 border-indigo-800 text-white text-sm font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-200">
                <i class="fa fa-user-plus mr-2"></i> Enroll Student
            </button>
        </div>

        <!-- FILTERS (Made slightly more compact on mobile) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border md:border-[3px] border-slate-200 md:border-slate-300 mb-5 sm:mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-end">
                
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                        <i class="fa fa-search mr-1.5 text-indigo-500"></i> Search Keyword
                    </label>
                    <div class="relative w-full shadow-sm rounded-xl overflow-hidden border-2 border-slate-200 md:border-slate-300 bg-white focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                            <i class="fa fa-font"></i>
                        </span>
                        <input type="text" id="filterSearchInput" class="w-full border-0 py-2 pl-9 pr-3 text-[13px] sm:text-sm font-bold text-slate-700 focus:ring-0 placeholder:text-slate-400 bg-transparent" placeholder="Name, ID, LRN...">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                        <i class="fa fa-school mr-1.5 text-indigo-500"></i> Filter by Class
                    </label>
                    <select id="filterClass" class="w-full rounded-xl border-2 border-slate-200 md:border-slate-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 shadow-sm bg-white">
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

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                        <i class="fa fa-notes-medical mr-1.5 text-indigo-500"></i> Filter by Diagnosis
                    </label>
                    <select id="filterDiagnosis" class="w-full rounded-xl border-2 border-slate-200 md:border-slate-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 shadow-sm bg-white">
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

                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5 flex items-center">
                            <i class="fa fa-toggle-on mr-1.5 text-indigo-500"></i> Status
                        </label>
                        <select id="filterStatus" class="w-full rounded-xl border-2 border-slate-200 md:border-slate-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold text-slate-700 focus:border-indigo-500 shadow-sm bg-white">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-transparent uppercase tracking-wider mb-1.5">Reset</label>
                        <button type="button" onclick="resetFilters()" class="inline-flex items-center justify-center px-3.5 py-2 bg-slate-100 border-2 border-slate-200 md:border-slate-300 text-slate-600 font-bold text-sm rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-colors shadow-sm" title="Reset Filters">
                            <i class="fa fa-rotate-left"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- STUDENT TABLE (Redesigned as Native Cards on Mobile) -->
        <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border-[3px] border-slate-300 overflow-hidden">
            <div class="w-full">
                <table class="w-full text-left border-collapse block md:table">
                    
                    <!-- TABLE HEADER (Hidden on mobile) -->
                    <thead class="hidden md:table-header-group bg-slate-50 border-b-[3px] border-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Student</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Class & Diagnosis</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Assigned Personnel</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    
                    <!-- TABLE BODY (Becomes a spaced list of cards on mobile) -->
                    <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y-[3px] divide-slate-200 bg-transparent md:bg-white">
                        @forelse($students ?? [] as $student)
                            <tr x-data="{ showEditModal: false }" 
                                class="block md:table-row bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150 student-row"
                                data-class="{{ strtolower($student->class_name ?? '') }}"
                                data-diagnosis="{{ strtolower($student->diagnosis ?? '') }}"
                                data-status="{{ strtolower($student->status ?? '') }}">
                                
                                <!-- 1. Profile Area -->
                                <td class="block md:table-cell p-4 md:px-6 md:py-4 border-b border-slate-100 md:border-none relative">
                                    <div class="flex items-start justify-between w-full">
                                        <div class="flex items-center gap-3 md:gap-4">
                                            <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-slate-100 border md:border-2 border-slate-300 flex items-center justify-center text-indigo-600 font-extrabold text-sm md:text-base shrink-0 shadow-sm">
                                                {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-[14px] md:text-sm font-extrabold text-slate-800 leading-tight">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                <div class="text-[11px] text-slate-500 font-medium mt-0.5 md:mt-1">
                                                    ID: {{ $student->student_id_number ?? $student->id }}
                                                    @if($student->learners_reference_number)
                                                        <span class="hidden md:inline mx-1 text-slate-300">|</span> 
                                                        <span class="md:hidden block"></span>
                                                        LRN: <span class="font-bold text-indigo-500">{{ $student->learners_reference_number }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Status Badge (Mobile Top Right) -->
                                        <div class="md:hidden shrink-0 mt-1">
                                            @if($student->status === 'active')
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded bg-emerald-100 border border-emerald-300 text-emerald-800">Active</span>
                                            @else
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded bg-slate-100 border border-slate-300 text-slate-600">Archived</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Class & Diagnosis -->
                                <td class="block md:table-cell px-4 py-1.5 md:px-6 md:py-4">
                                    <div class="flex items-start gap-2.5 md:gap-0">
                                        <i class="fa fa-school text-indigo-400 w-4 text-center text-[11px] mt-0.5 md:hidden"></i>
                                        <div>
                                            <div class="text-[12.5px] md:text-sm font-bold text-slate-700 leading-tight">{{ $student->class_name ?? 'Unassigned Class' }}</div>
                                            <div class="text-[11px] text-slate-500 font-medium leading-tight mt-0.5">{{ $student->diagnosis ?? 'No specified diagnosis' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- 3. Personnel Tags -->
                                <td class="block md:table-cell px-4 py-1.5 md:px-6 md:py-4">
                                    <div class="flex items-start gap-2.5 md:gap-0">
                                        <i class="fa fa-users text-emerald-500 w-4 text-center text-[11px] mt-1 md:hidden"></i>
                                        <div class="flex flex-wrap md:flex-col gap-1.5 md:gap-0">
                                            
                                            <div class="md:mb-1.5">
                                                <span class="hidden md:block text-[9px] font-extrabold tracking-widest text-slate-400 uppercase mb-0.5">Teacher</span>
                                                <div class="text-[11.5px] md:text-[13px] text-slate-700 font-semibold flex items-center bg-slate-100 md:bg-transparent px-2 py-1 md:p-0 rounded-md border border-slate-200 md:border-none">
                                                    <i class="fa fa-chalkboard-teacher text-indigo-500 mr-1.5"></i> {{ $student->teacher->name ?? 'Unassigned' }}
                                                </div>
                                            </div>
                                            
                                            @if($student->guardian)
                                            <div class="md:pt-1.5 md:border-t-2 md:border-slate-100">
                                                <span class="hidden md:block text-[9px] font-extrabold tracking-widest text-slate-400 uppercase mb-0.5">Guardian</span>
                                                <div class="text-[11.5px] md:text-[13px] text-slate-700 font-semibold flex items-center bg-slate-100 md:bg-transparent px-2 py-1 md:p-0 rounded-md border border-slate-200 md:border-none">
                                                    <i class="fa fa-user-shield text-emerald-600 mr-1.5"></i> {{ $student->guardian->name }}
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
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-emerald-100 border-2 border-emerald-300 text-emerald-800">Active</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-slate-100 border-2 border-slate-300 text-slate-600">Archived</span>
                                        @endif
                                        
                                        @if($student->medical_document)
                                            <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-3 py-1 inline-flex items-center text-[11px] font-bold rounded-full bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors border-2 border-rose-200">
                                                <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- 5. Actions Footer -->
                                <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 mt-3 md:mt-0 bg-slate-50 md:bg-transparent rounded-b-2xl md:rounded-none border-t border-slate-100 md:border-none">
                                    <div class="flex justify-between items-center w-full">
                                        
                                        <!-- Mobile Med Record Button -->
                                        <div class="md:hidden">
                                            @if($student->medical_document)
                                                <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-2.5 py-1.5 inline-flex items-center text-[10px] font-bold rounded-md bg-rose-50 text-rose-700 border border-rose-200 shadow-sm">
                                                    <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                                </a>
                                            @else
                                                <span></span>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex justify-end gap-1.5 md:gap-2 w-full md:w-auto">
                                            <a href="{{ route('admin.students.show', $student->id) }}" class="inline-flex items-center justify-center w-8 h-8 md:w-8 md:h-8 bg-white md:bg-slate-50 border md:border-2 border-slate-300 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-700 transition-colors shadow-sm" title="View Profile">
                                                <i class="fa fa-eye text-[11px] md:text-xs"></i>
                                            </a>

                                            <button @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 md:w-8 md:h-8 bg-white md:bg-slate-50 border md:border-2 border-slate-300 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-700 transition-colors shadow-sm" title="Edit">
                                                <i class="fa fa-edit text-[11px] md:text-xs"></i>
                                            </button>
                                            
                                            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure you want to permanently delete this student?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 md:w-8 md:h-8 bg-white md:bg-slate-50 border md:border-2 border-slate-300 text-rose-500 md:text-slate-600 rounded-full hover:bg-red-50 hover:text-red-700 transition-colors shadow-sm" title="Delete">
                                                    <i class="fa fa-trash text-[11px] md:text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- EDIT MODAL (Kept Responsive) -->
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                            <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-[3px] border-slate-300 text-left w-full relative z-10">
                                                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <input type="hidden" name="guardian_action" value="update">
                                                    
                                                    <div class="px-5 sm:px-6 py-4 border-b-[3px] border-slate-300 bg-slate-50 flex justify-between items-center">
                                                        <h3 class="text-base sm:text-lg font-extrabold text-slate-800 m-0" id="modal-title">Edit Student</h3>
                                                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                                                            <i class="fa fa-times text-xl"></i>
                                                        </button>
                                                    </div>

                                                    <div class="px-5 sm:px-6 py-5 sm:py-6 max-h-[70vh] overflow-y-auto space-y-6 custom-scrollbar text-left">
                                                        
                                                        <h4 class="text-indigo-700 font-extrabold mb-4 flex items-center text-[13px] sm:text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                                            <i class="fa fa-user-graduate mr-2"></i> 1. Info
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 mb-8">
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="first_name" value="{{ $student->first_name }}" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="last_name" value="{{ $student->last_name }}" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">LRN</label>
                                                                <input type="text" name="learners_reference_number" value="{{ $student->learners_reference_number }}" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" placeholder="e.g., 123456789012">
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                                                <select name="gender" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                                                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Date of Birth</label>
                                                                <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                                            </div>

                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Diagnosis <span class="text-red-500">*</span></label>
                                                                <select name="diagnosis" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                                                    <option value="Autism Spectrum Disorder (ASD)" {{ $student->diagnosis == 'Autism Spectrum Disorder (ASD)' ? 'selected' : '' }}>Autism Spectrum Disorder (ASD)</option>
                                                                    <option value="Attention Deficit Hyperactivity Disorder (ADHD)" {{ $student->diagnosis == 'Attention Deficit Hyperactivity Disorder (ADHD)' ? 'selected' : '' }}>Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                                                    <option value="Down Syndrome" {{ $student->diagnosis == 'Down Syndrome' ? 'selected' : '' }}>Down Syndrome</option>
                                                                    <option value="Intellectual Disability" {{ $student->diagnosis == 'Intellectual Disability' ? 'selected' : '' }}>Intellectual Disability</option>
                                                                    <option value="Specific Learning Disability" {{ $student->diagnosis == 'Specific Learning Disability' ? 'selected' : '' }}>Specific Learning Disability</option>
                                                                    <option value="Speech or Language Impairment" {{ $student->diagnosis == 'Speech or Language Impairment' ? 'selected' : '' }}>Speech or Language Impairment</option>
                                                                    <option value="Other" {{ $student->diagnosis == 'Other' ? 'selected' : '' }}>Other</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Class Name</label>
                                                                <select name="class_name" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
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
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Status</label>
                                                                <select name="status" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                                                    <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="archived" {{ $student->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                                                </select>
                                                            </div>
                                                            <div class="md:col-span-2">
                                                                <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Assigned Teacher</label>
                                                                <select name="teacher_id" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                                                    <option value="">-- Select Teacher --</option>
                                                                    @foreach($teachers ?? [] as $teacher)
                                                                        <option value="{{ $teacher->id }}" {{ $student->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <h4 class="text-emerald-700 font-extrabold mb-4 flex items-center text-[13px] sm:text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2 mt-6">
                                                            <i class="fa fa-user-shield mr-2"></i> 2. Guardian
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 gap-5 mb-4">
                                                            <div>
                                                                <select name="guardian_id" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-emerald-500 shadow-sm">
                                                                    <option value="">-- Select Existing Guardian --</option>
                                                                    @foreach($guardians as $guardian)
                                                                        <option value="{{ $guardian->id }}" {{ $student->guardian_id == $guardian->id ? 'selected' : '' }}>{{ $guardian->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t-[3px] border-slate-300 flex justify-end gap-3 w-full">
                                                        <button type="button" @click="showEditModal = false" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-white border-[3px] border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                                                        <button type="submit" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-indigo-600 border-2 border-indigo-800 rounded-xl text-[13px] sm:text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                                            <i class="fa fa-save mr-1.5 sm:mr-2"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        <tr id="no-search-results" class="block md:table-row" style="display: none;">
                            <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white rounded-2xl md:rounded-none">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 border-[3px] border-slate-200">
                                    <i class="fa fa-search text-2xl text-slate-400"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-800 text-lg mb-1">No matching students found</h3>
                                <p class="text-slate-500 text-sm font-medium m-0">Try adjusting your search filters above.</p>
                            </td>
                        </tr>

                        @if(!isset($students) || count($students) == 0)
                            <tr class="block md:table-row">
                                <td colspan="5" class="block md:table-cell px-6 py-16 text-center bg-white rounded-2xl md:rounded-none">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 border-[3px] border-slate-200">
                                        <i class="fa fa-user-graduate text-2xl text-slate-400"></i>
                                    </div>
                                    <h3 class="font-extrabold text-slate-800 text-lg mb-1">No students enrolled yet</h3>
                                    <p class="text-slate-500 text-sm font-medium m-0">Click "Enroll Student" to add your first profile to the system.</p>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

        <!-- CREATE MODAL REMAINS UNTOUCHED BELOW THIS POINT (Only padding adjusted for mobile) -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-[3px] border-slate-300 w-full relative z-10">
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="px-5 sm:px-6 py-4 border-b-[3px] border-slate-300 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 m-0" id="modal-title">Create Student</h3>
                            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="px-5 sm:px-6 py-5 sm:py-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            
                            <h4 class="text-indigo-700 font-extrabold mb-4 flex items-center text-[13px] sm:text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                <i class="fa fa-user-graduate mr-2"></i> 1. Info
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                </div>
                                
                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">LRN</label>
                                    <input type="text" name="learners_reference_number" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" placeholder="e.g., 123456789012">
                                </div>

                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                    <select name="gender" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Diagnosis <span class="text-red-500">*</span></label>
                                    <select name="diagnosis" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                        <option value="Autism Spectrum Disorder (ASD)" selected>Autism Spectrum Disorder (ASD)</option>
                                        <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                        <option value="Down Syndrome">Down Syndrome</option>
                                        <option value="Intellectual Disability">Intellectual Disability</option>
                                        <option value="Specific Learning Disability">Specific Learning Disability</option>
                                        <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Class Name</label>
                                    <select name="class_name" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
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
                                    <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Assign Teacher</label>
                                    <select name="teacher_id" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm">
                                        <option value="">-- Select a Teacher --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <h4 class="text-emerald-700 font-extrabold mb-4 mt-6 flex items-center text-[13px] sm:text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                <i class="fa fa-user-shield mr-2"></i> 2. Guardian
                            </h4>
                            
                            <div class="grid grid-cols-1 gap-5 mb-4">
                                <div class="md:col-span-2">
                                    <select name="guardian_id" class="w-full rounded-xl border-2 border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-emerald-500 shadow-sm">
                                        <option value="" disabled selected>-- Select Existing Guardian --</option>
                                        @foreach($guardians ?? [] as $guardian)
                                            <option value="{{ $guardian->id }}">{{ $guardian->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 m-0">Optional — guardian accounts are created under User Management, then linked to a student here.</p>
                                </div>
                            </div>

                        </div>

                        <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t-[3px] border-slate-300 flex justify-end gap-3 w-full">
                            <button type="button" @click="showCreateModal = false" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-white border-[3px] border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                            <button type="submit" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-indigo-600 border-2 border-indigo-800 rounded-xl text-[13px] sm:text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                <i class="fa fa-save mr-1.5 sm:mr-2"></i> Save & Enroll
                            </button>
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
            const matchesDiagnosis = selectedDiagnosis === '' || rowDiagnosis === selectedDiagnosis;
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