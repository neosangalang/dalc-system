@extends('layouts.app')

@section('title', 'Student Profiling')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-red-500"></i> Please fix the following errors:
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
            
            <button @click="showCreateModal = true" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow-md hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <i class="fa fa-user-plus mr-2"></i> Enroll Student
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Student</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Class & Diagnosis</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Assigned Personnel</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($students ?? [] as $student)
                            <tr x-data="{ showEditModal: false }" class="hover:bg-slate-50 transition-colors duration-150">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-indigo-600 font-extrabold shrink-0">
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
                                            <i class="fa fa-chalkboard-teacher text-indigo-400 w-4"></i> {{ $student->teacher->name ?? 'Unassigned' }}
                                        </div>
                                    </div>
                                    @if($student->guardian)
                                    <div class="pt-1.5 border-t border-slate-100">
                                        <span class="text-[9px] font-extrabold tracking-widest text-slate-400 uppercase block mb-0.5">Guardian</span>
                                        <div class="text-[13px] text-slate-800 font-semibold flex items-center gap-1.5">
                                            <i class="fa fa-user-shield text-emerald-500 w-4"></i> {{ $student->guardian->name }}
                                        </div>
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2 items-start">
                                        @if($student->status === 'active')
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-[11px] font-bold rounded-full bg-slate-100 text-slate-600">
                                                Archived
                                            </span>
                                        @endif
                                        
                                        @if($student->medical_document)
                                            <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="px-3 py-1 inline-flex items-center text-[11px] font-bold rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                                <i class="fa fa-file-medical mr-1.5"></i> Med Record
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-normal">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border border-slate-200 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-600 transition-colors hover:border-indigo-200" title="View Profile">
                                            <i class="fa fa-eye text-xs"></i>
                                        </a>

                                        <button @click="showEditModal = true" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border border-slate-200 text-slate-600 rounded-full hover:bg-indigo-50 hover:text-indigo-600 transition-colors hover:border-indigo-200" title="Edit">
                                            <i class="fa fa-edit text-xs"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure you want to permanently delete this student?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-slate-50 border border-slate-200 text-slate-600 rounded-full hover:bg-red-50 hover:text-red-600 transition-colors hover:border-red-200" title="Delete">
                                                <i class="fa fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                            <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200 text-left">
                                                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                                        <h3 class="text-lg font-extrabold text-slate-800 m-0" id="modal-title">Edit Student Profile</h3>
                                                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                                            <i class="fa fa-times text-xl"></i>
                                                        </button>
                                                    </div>

                                                    <div class="px-6 py-6 max-h-[70vh] overflow-y-auto space-y-6 custom-scrollbar text-left">
                                                        
                                                        <h4 class="text-indigo-600 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b border-slate-100 pb-2">
                                                            <i class="fa fa-user-graduate mr-2"></i> 1. Student Information
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="first_name" value="{{ $student->first_name }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="last_name" value="{{ $student->last_name }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                                                                <select name="gender" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                                                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Date of Birth</label>
                                                                <input type="date" name="date_of_birth" value="{{ $student->date_of_birth }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                            </div>

                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Diagnosis / Exceptionality</label>
                                                                <input type="text" name="diagnosis" value="{{ $student->diagnosis }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Class Name</label>
                                                                <input type="text" name="class_name" value="{{ $student->class_name }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Status</label>
                                                                <select name="status" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="archived" {{ $student->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Assigned Teacher</label>
                                                                <select name="teacher_id" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                                                    <option value="">-- Select Teacher --</option>
                                                                    @foreach($teachers ?? [] as $teacher)
                                                                        <option value="{{ $teacher->id }}" {{ $student->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        @if($student->guardian)
                                                        <h4 class="text-emerald-600 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b border-slate-100 pb-2">
                                                            <i class="fa fa-user-shield mr-2"></i> 2. Guardian Details
                                                        </h4>
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                                                            <div class="md:col-span-2">
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Guardian Full Name <span class="text-red-500">*</span></label>
                                                                <input type="text" name="guardian_name" value="{{ $student->guardian->name }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Email Address</label>
                                                                <input type="email" name="guardian_email" value="{{ $student->guardian->email }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Contact Number</label>
                                                                <input type="text" name="guardian_phone" value="{{ $student->guardian->phone }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                                            </div>
                                                        </div>
                                                        @endif
                                                        
                                                    </div>

                                                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                                                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                                                        <button type="submit" class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
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
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                        <i class="fa fa-user-graduate text-2xl text-slate-300"></i>
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

        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200">
                    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                            <h3 class="text-lg font-extrabold text-slate-800 m-0" id="modal-title">Create Student + Guardian</h3>
                            <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa fa-times text-xl"></i>
                            </button>
                        </div>

                        <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            
                            <h4 class="text-indigo-600 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b border-slate-100 pb-2">
                                <i class="fa fa-user-graduate mr-2"></i> 1. Student Information
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="first_name" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="last_name" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                </div>
                                
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Gender <span class="text-red-500">*</span></label>
                                    <select name="gender" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>

                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Diagnosis / Exceptionality</label>
                                    <input type="text" name="diagnosis" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="e.g. Autism Spectrum Disorder">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Initial Class/Group</label>
                                    <input type="text" name="class_name" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="e.g. SPED Block A">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Assign Teacher</label>
                                    <select name="teacher_id" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                        <option value="">-- Select a Teacher --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <h4 class="text-emerald-600 font-extrabold mb-4 flex items-center text-sm uppercase tracking-wider border-b border-slate-100 pb-2">
                                <i class="fa fa-user-shield mr-2"></i> 2. Guardian Details & Access
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="guardian_name" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="First and Last Name" required>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Email Address</label>
                                    <input type="email" name="guardian_email" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-slate-700 mb-1.5">Contact Number</label>
                                    <input type="text" name="guardian_phone" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" placeholder="09XX-XXX-XXXX">
                                </div>
                                
                                <div class="md:col-span-2 mt-2 p-4 bg-emerald-50 border border-emerald-200 rounded-xl grid grid-cols-1 md:grid-cols-2 gap-4 relative"
                                     x-data="{
                                        generateCredentials() {
                                            let nameInput = document.querySelector('input[name=\'guardian_name\']').value;
                                            if(!nameInput) { 
                                                alert('Please enter the Guardian\'s Full Name first.'); 
                                                return; 
                                            }
                                            let cleanName = nameInput.toLowerCase().replace(/[^a-z]/g, '');
                                            let randNum = Math.floor(Math.random() * 900) + 100;
                                            document.querySelector('input[name=\'guardian_username\']').value = cleanName + randNum;

                                            let chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                                            let pass = 'DALC-';
                                            for(let i=0; i<5; i++) {
                                                pass += chars.charAt(Math.floor(Math.random() * chars.length));
                                            }
                                            let passInput = document.querySelector('input[name=\'guardian_password\']');
                                            passInput.value = pass;
                                            passInput.type = 'text';
                                        }
                                     }">

                                    <div class="md:col-span-2 flex justify-between items-center mb-1">
                                        <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-widest">Portal Login Credentials</span>
                                        <button type="button" @click="generateCredentials()" class="px-3 py-1.5 bg-emerald-600 text-white text-[11px] font-extrabold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-1.5 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                            <i class="fa fa-magic"></i> Auto-Generate
                                        </button>
                                    </div>

                                    <div>
                                        <label class="block text-[13px] font-bold text-emerald-900 mb-1.5">Username <span class="text-red-500">*</span></label>
                                        <input type="text" name="guardian_username" class="w-full rounded-xl border-emerald-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white" required readonly>
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-bold text-emerald-900 mb-1.5">Temporary Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="guardian_password" class="w-full rounded-xl border-emerald-300 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white" required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                            <button type="button" @click="showCreateModal = false" class="px-5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-bold text-white hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
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