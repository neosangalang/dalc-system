@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block" x-data="{ showCreateModal: {{ ($errors->any() && old('name')) ? 'true' : 'false' }} }">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
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
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <h3 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                Teacher Accounts
            </h3>
            <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                <i class="fa fa-plus mr-2"></i> Generate Account
            </button>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Full Name</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Username</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($users->where('role', 'teacher') as $user)
                        <tr x-data="{ showResetModal: false, showPermModal: false }" class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-extrabold text-slate-800">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-pink-500">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full bg-slate-100 text-slate-600">Suspended</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right whitespace-normal">
                                <div class="flex justify-end gap-2">
                                    
                                    <button @click="showResetModal = true" class="inline-flex items-center px-3 py-1.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg hover:bg-amber-200 transition-colors shadow-sm">
                                        <i class="fa fa-key mr-1.5"></i> Reset
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-600 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition-colors shadow-sm">
                                            <i class="fa fa-ban mr-1.5"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    <button @click="showPermModal = true" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                                        <i class="fa fa-user-shield mr-1.5 text-indigo-500"></i> Permissions
                                    </button>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-rose-50 border border-rose-200 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-key text-amber-500 mr-2"></i> Edit Credentials for {{ $user->name }}</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-6 space-y-4">
                                                    <div class="bg-indigo-50 border border-indigo-100 text-indigo-800 px-4 py-3 rounded-xl text-[13px] font-medium">
                                                        <i class="fa fa-info-circle mr-1.5"></i> You are modifying the login details for a <strong>Teacher</strong> account.
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-bold text-slate-700 mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div class="border-t border-slate-100 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[13px] font-bold text-rose-600 m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-indigo-600 hover:text-indigo-800 text-[12px] font-bold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="flex-1 rounded-xl border-rose-300 px-4 py-2 text-sm font-bold focus:border-rose-500 focus:ring-rose-500" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-100 transition-colors shrink-0">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy Login
                                                            </button>
                                                        </div>
                                                        <p class="text-[11px] text-slate-500 mt-2 m-0">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</p>
                                                    </div>
                                                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl mt-4">
                                                        <label class="block text-[13px] font-bold text-slate-800 mb-2"><i class="fa fa-shield-alt text-amber-500 mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[12px] text-slate-600 mb-3">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-amber-300 px-4 py-3 text-center text-lg font-bold tracking-[0.25em] focus:border-amber-500 focus:ring-amber-500" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-sm"><i class="fa fa-save mr-1.5"></i> Save New Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="showPermModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showPermModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showPermModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showPermModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                                            <form action="{{ route('admin.accounts.permissions', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-sliders text-indigo-600 mr-2"></i> Module Permissions</h5>
                                                    <button type="button" @click="showPermModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-6">
                                                    <p class="text-[13px] text-slate-600 mb-5">Grant <strong class="text-slate-800">{{ $user->name }}</strong> access to specific Admin-level modules.</p>
                                                    
                                                    <div class="space-y-4">
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative">
                                                                <input type="checkbox" name="can_manage_credentials" value="1" class="sr-only" {{ $user->can_manage_credentials ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_credentials ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_manage_credentials ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-bold text-slate-800">1. Login Credentials Configuration</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative">
                                                                <input type="checkbox" name="can_manage_calendar" value="1" class="sr-only" {{ $user->can_manage_calendar ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_calendar ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_manage_calendar ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-bold text-slate-800">2. Calendar Configuration</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative">
                                                                <input type="checkbox" name="can_create_profiles" value="1" class="sr-only" {{ $user->can_create_profiles ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_create_profiles ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_create_profiles ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-bold text-slate-800">3. Initial Profile Creation</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative">
                                                                <input type="checkbox" name="can_archive_students" value="1" class="sr-only" {{ $user->can_archive_students ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_archive_students ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_archive_students ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-bold text-slate-800">4. Archiving Module</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative">
                                                                <input type="checkbox" name="can_approve_reports" value="1" class="sr-only" {{ $user->can_approve_reports ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_approve_reports ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_approve_reports ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-sm font-bold text-slate-800">5. Admin Approval for Summaries</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                                                    <button type="button" @click="showPermModal = false" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm"><i class="fa fa-save mr-1.5"></i> Save Permissions</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center bg-white">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3 text-slate-400"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-slate-500 text-sm font-bold m-0">No teacher accounts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-lg font-extrabold text-slate-800 m-0 flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 mr-3 shadow-sm">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    Guardian Accounts
                </h3>
                <p class="text-xs font-medium text-slate-500 mt-1 ml-[52px]">Guardians can only view profiles and leave feedback.</p>
            </div>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Full Name</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Username</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($users->where('role', 'guardian') as $user)
                        <tr x-data="{ showResetModal: false }" class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-extrabold text-slate-800">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-pink-500">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-700">Active</span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-[11px] font-bold rounded-full bg-slate-100 text-slate-600">Suspended</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right whitespace-normal">
                                <div class="flex justify-end gap-2">
                                    
                                    <button type="button" onclick="copyDefaultGuardianLogin('{{ $user->username }}')" title="Copy Default Login" class="inline-flex items-center justify-center w-8 h-8 bg-sky-50 border border-sky-200 text-sky-600 rounded-lg hover:bg-sky-100 transition-colors shadow-sm">
                                        <i class="fa fa-copy text-xs"></i>
                                    </button>

                                    <button @click="showResetModal = true" class="inline-flex items-center px-3 py-1.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg hover:bg-amber-200 transition-colors shadow-sm">
                                        <i class="fa fa-key mr-1.5"></i> Reset
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-600 text-white text-xs font-bold rounded-lg hover:bg-slate-700 transition-colors shadow-sm">
                                            <i class="fa fa-ban mr-1.5"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guardian?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-rose-50 border border-rose-200 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors">
                                            <i class="fa fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-key text-amber-500 mr-2"></i> Edit Credentials for {{ $user->name }}</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-6 space-y-4">
                                                    <div class="bg-sky-50 border border-sky-100 text-sky-800 px-4 py-3 rounded-xl text-[13px] font-medium">
                                                        <i class="fa fa-info-circle mr-1.5"></i> You are modifying the login details for a <strong>Guardian</strong> account.
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[13px] font-bold text-slate-700 mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div class="border-t border-slate-100 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[13px] font-bold text-rose-600 m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-indigo-600 hover:text-indigo-800 text-[12px] font-bold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="flex-1 rounded-xl border-rose-300 px-4 py-2 text-sm font-bold focus:border-rose-500 focus:ring-rose-500" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-100 transition-colors shrink-0">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy Login
                                                            </button>
                                                        </div>
                                                        <p class="text-[11px] text-slate-500 mt-2 m-0">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</p>
                                                    </div>
                                                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl mt-4">
                                                        <label class="block text-[13px] font-bold text-slate-800 mb-2"><i class="fa fa-shield-alt text-amber-500 mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[12px] text-slate-600 mb-3">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-amber-300 px-4 py-3 text-center text-lg font-bold tracking-[0.25em] focus:border-amber-500 focus:ring-amber-500" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2.5 bg-amber-500 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-sm"><i class="fa fa-save mr-1.5"></i> Save New Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center bg-white">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3 text-slate-400"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-slate-500 text-sm font-bold m-0">No guardian accounts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form action="{{ route('admin.accounts.store') }}" method="POST" class="m-0">
                    @csrf
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <h5 class="text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-user-plus text-indigo-600 mr-2"></i> Generate New Account</h5>
                        <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Account Role</label>
                            <select name="role" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                                <option value="" disabled selected>Select Role...</option>
                                <option value="teacher">Teacher</option>
                                <option value="guardian">Guardian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required placeholder="e.g., Juan Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required placeholder="e.g., juan@dalc.com">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required placeholder="e.g., juandc">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Temporary Password</label>
                            <input type="password" name="password" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-slate-700 mb-1">Confirm Temporary Password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required placeholder="Type it again">
                            <p class="text-[11px] text-slate-500 font-medium mt-1.5 m-0"><i class="fa fa-shield-alt mr-1"></i> They will be forced to change this upon first login.</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm"><i class="fa fa-save mr-1.5"></i> Create Account</button>
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
    input:checked + .block { background-color: #6366f1; }
    input:checked + .block + .dot { transform: translateX(100%); }
    .dot { transition: all 0.3s ease-in-out; }
</style>

@endsection

@push('scripts')
<script>
    // Copy Default credentials directly from the table
    function copyDefaultGuardianLogin(username) {
        let defaultPassword = '12345678';
      
        let text = `Dream Achievers Learning Center - Parent Portal\n\nUsername: ${username}\nDefault Password: ${defaultPassword}\n\n*Note: If you have already personalized your password, this default password will no longer work. Please contact the admin for a reset.*`;
        
        navigator.clipboard.writeText(text).then(() => { 
            alert('Default login credentials copied to clipboard!'); 
        }).catch(err => {
            alert('Failed to copy text. Please check your browser permissions.');
        });
    }

    // Auto-Generate a random password inside the Reset Modal
    function generatePassword(userId) {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
        let pass = "";
        for (let i = 0; i < 8; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('new_password_' + userId).value = pass;
    }

    // Copy the newly generated/typed password from the Reset Modal
    function copyNewCredentials(username, userId) {
        const passField = document.getElementById('new_password_' + userId);
        if(passField.value === '') {
            alert("Please type or auto-generate a New Password first before copying!");
            return;
        }
        
        const textToCopy = `Dream Achievers Learning Center PH - Parent Portal\n\nUsername: ${username}\nNew Password: ${passField.value}\n\nPlease log in and change this password immediately.`;
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert('New login credentials copied to clipboard! You can now click "Save New Login".');
        }).catch(err => {
            alert('Failed to copy text. Please check your browser permissions.');
        });
    }
</script>
@endpush