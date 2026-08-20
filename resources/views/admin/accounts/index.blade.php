@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-8" x-data="{ showCreateModal: {{ ($errors->any() && old('name')) ? 'true' : 'false' }} }">

    <!-- ALERTS -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm animate-fade-in-down">
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

    @if(Auth::user()->role === 'admin')
    
    <!-- ================= TEACHER ACCOUNTS ================= -->
    <div class="bg-white border border-black/15 rounded-2xl shadow-sm overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="px-5 sm:px-6 py-5 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4 sm:gap-0">
            <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm border border-[#3B82F6]/20">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                Teacher Accounts
            </h3>
            <button @click="showCreateModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
                <i class="fa fa-plus mr-2"></i> Generate Account
            </button>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER -->
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">User Profile</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Contact Information</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group divide-y border-black/5 bg-white">
                    @forelse($users->where('role', 'teacher') as $user)
                        
                        <!-- ROW -->
                        <tr x-data="{ showResetModal: false, showPermModal: false }" class="block md:table-row relative border-b border-black/5 hover:bg-slate-50 transition-colors duration-150 p-4 md:p-0">
                            
                            <!-- 1. Avatar, Name & Username -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 md:h-11 md:w-11 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white flex items-center justify-center font-extrabold text-sm shadow-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-extrabold text-[#1E2657] leading-tight">{{ $user->name }}</div>
                                        <div class="text-[12px] text-[#3B82F6] font-bold mt-0.5">
                                            {{ $user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status Badge -->
                            <td class="absolute top-6 right-6 md:static md:table-cell md:px-6 md:py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-extrabold rounded-md bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest shadow-sm">Active</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-widest shadow-sm">Suspended</span>
                                @endif
                            </td>

                            <!-- 3. Contact Info -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <div class="space-y-1.5">
                                    <div class="flex items-center text-[12px] font-medium text-[#6B7086]">
                                        <i class="fa fa-envelope w-4 text-center text-[#9CA0AF] mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="flex items-center text-[12px] font-medium text-[#6B7086]">
                                        <i class="fa fa-phone w-4 text-center text-[#9CA0AF] mr-2"></i> {{ $user->contact_number ?? 'No contact number' }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 4. Actions Footer -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 md:bg-transparent">
                                <div class="grid grid-cols-2 gap-2 w-full md:flex md:justify-end">
                                    
                                    <button @click="showResetModal = true" class="inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#F59E0B] hover:border-[#F59E0B]/50 transition-colors shadow-sm">
                                        <i class="fa fa-key mr-1.5 text-[#F59E0B]"></i> Reset
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0 flex">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#1E2657] hover:border-black/30 transition-colors shadow-sm">
                                            <i class="fa fa-ban mr-1.5"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    <button @click="showPermModal = true" class="inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#8B5CF6] hover:border-[#8B5CF6]/50 transition-colors shadow-sm">
                                        <i class="fa fa-user-shield mr-1.5 text-[#8B5CF6]"></i> <span class="hidden md:inline">Permissions</span><span class="md:hidden">Access</span>
                                    </button>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher?');" class="m-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-rose-500 text-[12px] font-extrabold rounded-xl hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm md:w-10">
                                            <i class="fa fa-trash md:mr-0 mr-1.5"></i><span class="md:hidden">Delete</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- RESET MODAL (TEACHER) -->
                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-black/15">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                                                    <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-key text-[#F59E0B] mr-2"></i> Edit Credentials</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6 space-y-4">
                                                    <div class="bg-[#3B82F6]/10 border border-[#3B82F6]/20 text-[#1E2657] px-4 py-3 rounded-xl text-[13px] font-medium leading-snug">
                                                        <i class="fa fa-info-circle text-[#3B82F6] mr-1.5"></i> Modifying login details for <strong>{{ $user->name }}</strong> (Teacher).
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#3B82F6] focus:ring-[#3B82F6]" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Contact Number</label>
                                                        <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#3B82F6] focus:ring-[#3B82F6]" placeholder="09XX-XXX-XXXX">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#3B82F6] focus:ring-[#3B82F6]" required>
                                                    </div>
                                                    <div class="border-t border-black/5 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[13px] font-extrabold text-[#F59E0B] m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-[#3B82F6] hover:text-blue-800 text-[12px] font-extrabold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="w-full sm:flex-1 rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-extrabold focus:border-[#F59E0B] focus:ring-[#F59E0B]" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="w-full sm:w-auto px-4 py-2 bg-white border border-black/15 text-[#6B7086] font-extrabold text-[13px] rounded-xl hover:bg-slate-50 transition-colors shrink-0 shadow-sm flex items-center justify-center">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="bg-[#F4F6FA] border border-black/10 p-4 rounded-xl mt-4 shadow-sm">
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-2"><i class="fa fa-shield-alt text-[#10B981] mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[12px] text-[#6B7086] mb-3 leading-tight">Authorize these changes with your 6-digit MFA code.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-center text-lg font-extrabold tracking-[0.25em] focus:border-[#10B981] focus:ring-[#10B981]" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-[#F59E0B] text-white text-[13px] font-extrabold rounded-xl hover:bg-amber-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- PERMISSIONS MODAL -->
                                <div x-show="showPermModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showPermModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showPermModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showPermModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-black/15">
                                            <form action="{{ route('admin.accounts.permissions', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                                                    <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-sliders text-[#8B5CF6] mr-2"></i> Permissions</h5>
                                                    <button type="button" @click="showPermModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6">
                                                    <p class="text-[13px] text-[#6B7086] mb-5 leading-snug">Grant <strong class="text-[#1E2657]">{{ $user->name }}</strong> access to specific Admin-level modules.</p>
                                                    
                                                    <div class="space-y-4">
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_manage_credentials" value="1" class="sr-only" {{ $user->can_manage_credentials ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_credentials ? 'bg-[#8B5CF6]' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform shadow-sm {{ $user->can_manage_credentials ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] font-extrabold text-[#1E2657]">1. Login Credentials Configuration</div>
                                                        </label>
                                                        <!-- Remaining checkboxes are the same -->
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_manage_calendar" value="1" class="sr-only" {{ $user->can_manage_calendar ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_calendar ? 'bg-[#8B5CF6]' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform shadow-sm {{ $user->can_manage_calendar ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] font-extrabold text-[#1E2657]">2. Calendar Configuration</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_create_profiles" value="1" class="sr-only" {{ $user->can_create_profiles ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_create_profiles ? 'bg-[#8B5CF6]' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform shadow-sm {{ $user->can_create_profiles ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] font-extrabold text-[#1E2657]">3. Initial Profile Creation</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_archive_students" value="1" class="sr-only" {{ $user->can_archive_students ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_archive_students ? 'bg-[#8B5CF6]' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform shadow-sm {{ $user->can_archive_students ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] font-extrabold text-[#1E2657]">4. Archiving Module</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_approve_reports" value="1" class="sr-only" {{ $user->can_approve_reports ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_approve_reports ? 'bg-[#8B5CF6]' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform shadow-sm {{ $user->can_approve_reports ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] font-extrabold text-[#1E2657]">5. Admin Approval for Summaries</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showPermModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-[#8B5CF6] text-white text-[13px] font-extrabold rounded-xl hover:bg-purple-700 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Access</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="4" class="block md:table-cell px-6 py-12 text-center bg-white rounded-2xl md:rounded-none">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#F4F6FA] mb-3 text-[#9CA0AF] border border-black/5"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-[#6B7086] text-[13px] font-extrabold m-0">No teacher accounts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= GUARDIAN ACCOUNTS ================= -->
    <div class="bg-white border border-black/15 rounded-2xl shadow-sm overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="px-5 sm:px-6 py-5 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white gap-4 sm:gap-0">
            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                    <div class="w-10 h-10 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981] mr-3 shadow-sm border border-[#10B981]/20 shrink-0">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    Guardian Accounts
                </h3>
                <p class="text-[12px] font-medium text-[#9CA0AF] mt-1 sm:ml-[52px] leading-tight">Guardians can only view profiles and leave feedback.</p>
            </div>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER -->
                <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">User Profile</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Contact Information</th>
                        <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group divide-y border-black/5 bg-white">
                    @forelse($users->where('role', 'guardian') as $user)
                        
                        <!-- ROW -->
                        <tr x-data="{ showResetModal: false }" class="block md:table-row relative border-b border-black/5 hover:bg-slate-50 transition-colors duration-150 p-4 md:p-0">
                            
                            <!-- 1. Avatar, Name & Username -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 md:h-11 md:w-11 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white flex items-center justify-center font-extrabold text-sm shadow-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-extrabold text-[#1E2657] leading-tight">{{ $user->name }}</div>
                                        <div class="text-[12px] text-[#10B981] font-bold mt-0.5">
                                            {{ $user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status Badge -->
                            <td class="absolute top-6 right-6 md:static md:table-cell md:px-6 md:py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-extrabold rounded-md bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest shadow-sm">Active</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 text-[10px] font-extrabold rounded-md bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-widest shadow-sm">Suspended</span>
                                @endif
                            </td>

                            <!-- 3. Contact Info -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-black/5 md:border-none">
                                <div class="space-y-1.5">
                                    <div class="flex items-center text-[12px] font-medium text-[#6B7086]">
                                        <i class="fa fa-envelope w-4 text-center text-[#9CA0AF] mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="flex items-center text-[12px] font-medium text-[#6B7086]">
                                        <i class="fa fa-phone w-4 text-center text-[#9CA0AF] mr-2"></i> {{ $user->contact_number ?? 'No contact number' }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 4. Actions Footer -->
                            <td class="block md:table-cell px-4 py-4 md:px-6 md:py-4 md:bg-transparent">
                                <div class="grid grid-cols-3 gap-2 w-full md:flex md:justify-end">

                                    <button @click="showResetModal = true" class="inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#F59E0B] hover:border-[#F59E0B]/50 transition-colors shadow-sm">
                                        <i class="fa fa-key md:mr-1.5 text-[#F59E0B]"></i> <span class="hidden md:inline">Reset</span>
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0 flex">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[12px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#1E2657] hover:border-black/30 transition-colors shadow-sm">
                                            <i class="fa fa-ban md:mr-1.5"></i> <span class="hidden md:inline">{{ $user->is_active ? 'Suspend' : 'Activate' }}</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guardian?');" class="m-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-3 py-2 bg-white border border-black/15 text-rose-500 text-[12px] font-extrabold rounded-xl hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm md:w-10">
                                            <i class="fa fa-trash md:mr-0 mr-1.5"></i><span class="md:hidden">Delete</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- RESET MODAL (GUARDIAN) -->
                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-black/15">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                                                    <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-key text-[#F59E0B] mr-2"></i> Edit Credentials</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6 space-y-4">
                                                    <div class="bg-[#10B981]/10 border border-[#10B981]/20 text-[#1E2657] px-4 py-3 rounded-xl text-[13px] font-medium leading-snug">
                                                        <i class="fa fa-info-circle text-[#10B981] mr-1.5"></i> Modifying login details for <strong>{{ $user->name }}</strong> (Guardian).
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#10B981] focus:ring-[#10B981]" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Contact Number</label>
                                                        <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#10B981] focus:ring-[#10B981]" placeholder="09XX-XXX-XXXX">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] focus:border-[#10B981] focus:ring-[#10B981]" required>
                                                    </div>
                                                    <div class="border-t border-black/5 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[13px] font-extrabold text-[#F59E0B] m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-[#3B82F6] hover:text-blue-800 text-[12px] font-extrabold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="w-full sm:flex-1 rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-extrabold focus:border-[#F59E0B] focus:ring-[#F59E0B]" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="w-full sm:w-auto px-4 py-2 bg-white border border-black/15 text-[#6B7086] font-extrabold text-[13px] rounded-xl hover:bg-slate-50 transition-colors shrink-0 shadow-sm flex items-center justify-center">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="bg-[#F4F6FA] border border-black/10 p-4 rounded-xl mt-4 shadow-sm">
                                                        <label class="block text-[13px] font-extrabold text-[#1E2657] mb-2"><i class="fa fa-shield-alt text-[#10B981] mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[12px] text-[#6B7086] mb-3 leading-tight">Authorize these changes with your 6-digit MFA code.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-center text-lg font-extrabold tracking-[0.25em] focus:border-[#10B981] focus:ring-[#10B981]" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-[#F59E0B] text-white text-[13px] font-extrabold rounded-xl hover:bg-amber-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="4" class="block md:table-cell px-6 py-12 text-center bg-white rounded-2xl md:rounded-none">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-[#F4F6FA] mb-3 text-[#9CA0AF] border border-black/5"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-[#6B7086] text-[13px] font-extrabold m-0">No guardian accounts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= CREATE ACCOUNT MODAL ================= -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-black/15 relative w-full">
                <form action="{{ route('admin.accounts.store') }}" method="POST" class="m-0">
                    @csrf
                    <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center">
                        <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-user-plus text-[#3B82F6] mr-2"></i> Generate Account</h5>
                        <button type="button" @click="showCreateModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                    </div>
                    <div class="p-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Account Role</label>
                            <select name="role" id="create_role" class="w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required>
                                <option value="" disabled selected>Select Role...</option>
                                <option value="teacher">Teacher</option>
                                <option value="guardian">Guardian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Full Name</label>
                            <input type="text" name="name" id="create_name" value="{{ old('name') }}" class="w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required placeholder="e.g., Juan Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Email Address</label>
                            <input type="email" name="email" id="create_email" value="{{ old('email') }}" class="w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required placeholder="e.g., juan@dalc.com">
                        </div>
                        <div>
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Contact Number</label>
                            <input type="text" name="contact_number" id="create_contact" value="{{ old('contact_number') }}" class="w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required placeholder="09XX-XXX-XXXX">
                        </div>
                        <div>
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Username</label>
                            <input type="text" name="username" id="create_username" value="{{ old('username') }}" class="w-full rounded-xl border-black/15 px-3.5 py-2.5 text-[13px] focus:border-[#3B82F6] shadow-sm" required placeholder="e.g., juandc">
                        </div>
                        
                        <div class="border-t border-black/5 pt-4 mt-2">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-[13px] font-extrabold text-[#F59E0B] m-0"><i class="fa fa-lock mr-1.5"></i> Temporary Password</label>
                                
                                <button type="button" onclick="generateCreatePassword()" class="text-[#3B82F6] hover:text-blue-800 text-[12px] font-extrabold transition-colors">
                                    <i class="fa fa-magic mr-1"></i> Auto-Generate
                                </button>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 mb-3">
                                <input type="text" name="password" id="create_password" class="w-full sm:flex-1 rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-extrabold focus:border-[#F59E0B]" placeholder="Min. 8 characters" required>
                                <button type="button" onclick="copyCreateCredentials()" class="w-full sm:w-auto px-4 py-2 bg-white border border-black/15 text-[#6B7086] font-extrabold text-[13px] rounded-xl hover:bg-slate-50 transition-colors shrink-0 shadow-sm flex justify-center items-center">
                                    <i class="fa fa-copy mr-1.5"></i> Copy Login
                                </button>
                            </div>
                            
                            <label class="block text-[13px] font-extrabold text-[#1E2657] mb-1">Confirm Password</label>
                            <input type="text" name="password_confirmation" id="create_password_confirmation" class="w-full rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-extrabold focus:border-[#3B82F6] shadow-sm" required placeholder="Type it again">
                            <p class="text-[11px] text-[#9CA0AF] mt-2 m-0 leading-tight"><i class="fa fa-shield-alt mr-1"></i> They will be forced to change this upon first login.</p>
                        </div>
                    </div>
                    <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end gap-3 w-full">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @endif

</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Toggle Checkbox Styling */
    input:checked + .block { background-color: #8B5CF6; }
    input:checked + .block + .dot { transform: translateX(100%); }
    .dot { transition: all 0.3s ease-in-out; }
</style>

@endsection

@push('scripts')
<script>
    function generatePassword(userId) {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
        let pass = "";
        for (let i = 0; i < 8; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('new_password_' + userId).value = pass;
    }

    function copyNewCredentials(username, userId) {
        const passField = document.getElementById('new_password_' + userId);
        if(passField.value === '') {
            alert("Please type or auto-generate a New Password first before copying!");
            return;
        }
        
        const textToCopy = `Dream Achievers Learning Center PH - Portal Access\n\nUsername: ${username}\nNew Password: ${passField.value}\n\nPlease log in and change this password immediately.`;
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert('Login credentials copied to clipboard! You can now click "Save New Login".');
        }).catch(err => {
            alert('Failed to copy text. Please check your browser permissions.');
        });
    }

    function generateCreatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
        let pass = "DALC-";
        for (let i = 0; i < 6; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        
        document.getElementById('create_password').value = pass;
        document.getElementById('create_password_confirmation').value = pass;
        
        let nameInput = document.getElementById('create_name').value;
        let usernameInput = document.getElementById('create_username');
        if(nameInput && !usernameInput.value) {
            let cleanName = nameInput.toLowerCase().replace(/[^a-z]/g, '');
            let randNum = Math.floor(Math.random() * 900) + 100;
            usernameInput.value = cleanName + randNum;
        }
    }

    function copyCreateCredentials() {
        let role = document.getElementById('create_role').value;
        let name = document.getElementById('create_name').value;
        let username = document.getElementById('create_username').value;
        let passField = document.getElementById('create_password');
        
        if(!username || passField.value === '') {
            alert("Please fill out the username and password (or click Auto-Generate) first before copying!");
            return;
        }
        
        const textToCopy = `Dream Achievers Learning Center PH - Portal Access\n\nRole: ${role ? role.toUpperCase() : 'USER'}\nName: ${name}\nUsername: ${username}\nTemporary Password: ${passField.value}\n\nPlease log in and change this password immediately.`;
        
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert('New account credentials copied to clipboard! You can now paste this to the user and click "Create".');
        }).catch(err => {
            alert('Failed to copy text. Please check your browser permissions.');
        });
    }
</script>
@endpush