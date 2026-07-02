@extends('layouts.app')

@section('title', 'Student Profiling')

@section('content')

<!-- ADDED: bg-slate-50 and min-h-screen for high contrast background -->
<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border-[3px] border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-600 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-[3px] border-red-300 text-red-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
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

        <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Student Profiles</h3>
                <p class="text-slate-500 text-sm font-medium">Manage all enrolled students, assignments, and medical records.</p>
            </div>
            
            <button @click="showCreateModal = true" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border-2 border-indigo-800 text-white text-sm font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow-md hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <i class="fa fa-user-plus mr-2"></i> Enroll Student
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-md border-[3px] border-slate-300 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50 border-b-[3px] border-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Student</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Class & Diagnosis</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Assigned Personnel</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-[3px] divide-slate-200 bg-white">
                        @forelse($students ?? [] as $student)
                            <tr x-data="{ showEditModal: false }" class="hover:bg-slate-50 transition-colors duration-150">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 border-2 border-slate-300 flex items-center justify-center text-indigo-600 font-extrabold shrink-0 shadow-sm">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-extrabold text-slate-800">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <div class="text-[11px] text-slate-500 font-medium mt-0.5">ID: {{ $student->student_id_number ?? $student->id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-700">{{ $student->class_name ?? 'Unassigned Class' }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">{{ $student->diagnosis ?? 'No specified diagnosis' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="mb-1.5">
                                        <span class="text-[9px] font-extrabold tracking-widest text-slate-400 uppercase block mb-0.5">Teacher</span>
                                        <div class="text-[13px] text-slate-800 font-semibold flex items-center gap-1.5">
                                            <i class="fa fa-chalkboard-teacher text-indigo-500 w-4"></i> {{ $student->teacher->name ?? 'Unassigned' }}
                                        </div>
                                    </div>
                                    @if($student->guardian)
                                    <div class="pt-1.5 border-t-2 border-slate-100">
                                        <span class="text-[9px] font-extrabold tracking-widest text-slate-400 uppercase block mb-0.5">Guardian</span>
                                        <div class="text-[13px] text-slate-800 font-semibold flex items-center gap-1.5">
                                            <i class="fa fa-user-shield text-emerald-600 w-4"></i> {{ $student->guardian->name }}
                                        </div>
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2 items-start">
                                        @if($student->status === 'active')
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-emerald-100 border-2 border-emerald-300 text-emerald-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-slate-100 border-2 border-slate-300 text-slate-600">
                                                Archived
                                            </span>
                                        @endif
                                        
                                        @if($student->medical_document)
                                            <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-3 py-1 inline-flex items-center text-[11px] font-bold rounded-full bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors border-2 border-rose-200">
                                                <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-normal">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border-2 border-slate-300 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-700 transition-colors hover:border-indigo-400 shadow-sm" title="View Profile">
                                            <i class="fa fa-eye text-xs"></i>
                                        </a>

                                        <button @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border-2 border-slate-300 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-700 transition-colors hover:border-indigo-400 shadow-sm" title="Edit">
                                            <i class="fa fa-edit text-xs"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure you want to permanently delete this student?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border-2 border-slate-300 text-slate-600 rounded-full hover:bg-red-50 hover:text-red-700 transition-colors hover:border-red-400 shadow-sm" title="Delete">
                                                <i class="fa fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- EDIT MODAL -->
                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                            <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-[3px] border-slate-300 text-left">
                                                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <!-- ADDED THIS TO PREVENT VALIDATION ERROR ON EDIT -->
                                                    <input type="hidden" name="guardian_action" value="update">
                                                    
                                                    <div class="px-6 py-4 border-b-[3px] border-slate-300 bg-slate-50 flex justify-between items-center">
                                                        <h3 class="text-lg font-extrabold text-slate-800 m-0" id="modal-title">Edit Student Profile</h3>
                                                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                                                            <i class="fa fa-times text-xl"></i>
                                                        </button>
                                                    </div>

                                                    <div class="px-6 py-6 max-h-[70vh] overflow-y-auto space-y-6 custom-scrollbar text-left">
                                                        
                                                        <h4 class="text-indigo-700 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                                            <i class="fa fa-user-graduate mr-2"></i> 1. Student Information
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="first_name" value="{{ $student->first_name }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="last_name" value="{{ $student->last_name }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                                                                <select name="gender" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Date of Birth</label>
                                                                <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                            </div>

                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Diagnosis / Exceptionality</label>
                                                                <select name="diagnosis" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="Autism Spectrum Disorder (ASD)" {{ $student->diagnosis == 'Autism Spectrum Disorder (ASD)' ? 'selected' : '' }}>Autism Spectrum Disorder (ASD)</option>
                                                                    <option value="Attention Deficit Hyperactivity Disorder (ADHD)" {{ $student->diagnosis == 'Attention Deficit Hyperactivity Disorder (ADHD)' ? 'selected' : '' }}>Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                                                    <option value="Down Syndrome" {{ $student->diagnosis == 'Down Syndrome' ? 'selected' : '' }}>Down Syndrome</option>
                                                                    <option value="Intellectual Disability" {{ $student->diagnosis == 'Intellectual Disability' ? 'selected' : '' }}>Intellectual Disability</option>
                                                                    <option value="Specific Learning Disability" {{ $student->diagnosis == 'Specific Learning Disability' ? 'selected' : '' }}>Specific Learning Disability</option>
                                                                    <option value="Speech or Language Impairment" {{ $student->diagnosis == 'Speech or Language Impairment' ? 'selected' : '' }}>Speech or Language Impairment</option>
                                                                    <option value="Other" {{ $student->diagnosis == 'Other' ? 'selected' : '' }}>Other / Not Listed</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Class Name</label>
                                                                <select name="class_name" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="">-- Select Class --</option>
                                                                    <option value="Playgroup Morning (Love A)" {{ $student->class_name == 'Playgroup Morning (Love A)' ? 'selected' : '' }}>Playgroup Morning (Love A)</option>
                                                                    <option value="Playgroup Afternoon (Love B)" {{ $student->class_name == 'Playgroup Afternoon (Love B)' ? 'selected' : '' }}>Playgroup Afternoon (Love B)</option>
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
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Status</label>
                                                                <select name="status" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="archived" {{ $student->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Assigned Teacher</label>
                                                                <select name="teacher_id" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="">-- Select Teacher --</option>
                                                                    @foreach($teachers ?? [] as $teacher)
                                                                        <option value="{{ $teacher->id }}" {{ $student->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        @if($student->guardian)
                                                        <h4 class="text-emerald-700 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                                            <i class="fa fa-user-shield mr-2"></i> 2. Guardian Details
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                                            <div class="md:col-span-2">
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Guardian Full Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="guardian_name" value="{{ $student->guardian->name }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Email Address</label>
                                                                <input type="email" name="guardian_email" value="{{ $student->guardian->email }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Contact Number</label>
                                                                <input type="text" name="guardian_phone" value="{{ $student->guardian->phone }}" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                                            </div>
                                                        </div>
                                                        @endif
                                                        
                                                    </div>

                                                    <div class="px-6 py-4 bg-slate-50 border-t-[3px] border-slate-300 flex justify-end gap-3">
                                                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 bg-white border-[3px] border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                                                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 border-2 border-indigo-800 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                                            <i class="fa fa-save mr-2"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-white">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4 border-[3px] border-slate-200">
                                        <i class="fa fa-user-graduate text-2xl text-slate-400"></i>
                                    </div>
                                    <h3 class="font-extrabold text-slate-800 text-lg mb-1">No students enrolled yet</h3>
                                    <p class="text-slate-500 text-sm font-medium m-0">Click "Enroll Student" to add your first profile to the system.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CREATE MODAL -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border-[3px] border-slate-300">
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="px-6 py-4 border-b-[3px] border-slate-300 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-lg font-extrabold text-slate-800 m-0" id="modal-title">Create Student</h3>
                            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-700 transition-colors">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            
                            <h4 class="text-indigo-700 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b-[3px] border-slate-200 pb-2">
                                <i class="fa fa-user-graduate mr-2"></i> Student Information
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                </div>
                                
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                                    <select name="gender" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Diagnosis / Exceptionality <span class="text-red-500">*</span></label>
                                    <select name="diagnosis" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                        <option value="Autism Spectrum Disorder (ASD)" selected>Autism Spectrum Disorder (ASD)</option>
                                        <option value="Attention Deficit Hyperactivity Disorder (ADHD)">Attention Deficit Hyperactivity Disorder (ADHD)</option>
                                        <option value="Down Syndrome">Down Syndrome</option>
                                        <option value="Intellectual Disability">Intellectual Disability</option>
                                        <option value="Specific Learning Disability">Specific Learning Disability</option>
                                        <option value="Speech or Language Impairment">Speech or Language Impairment</option>
                                        <option value="Other">Other / Not Listed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Class Name</label>
                                    <select name="class_name" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- Select Class --</option>
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
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Assign Teacher</label>
                                    <select name="teacher_id" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- Select a Teacher --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Assign Guardian</label>
                                    <select name="guardian_id" class="w-full rounded-xl border-2 border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- No Guardian Yet --</option>
                                        @foreach($guardians ?? [] as $guardian)
                                            <option value="{{ $guardian->id }}">{{ $guardian->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-slate-500 font-medium mt-1.5 m-0">Optional — guardian accounts are created under User Management, then linked to a student here.</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t-[3px] border-slate-300 flex justify-end gap-3">
                            <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 bg-white border-[3px] border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 border-2 border-indigo-800 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                                <i class="fa fa-save mr-2"></i> Save & Enroll
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