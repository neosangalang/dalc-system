@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block" x-data="{ showCreateModal: {{ ($errors->any() && old('name')) ? 'true' : 'false' }} }">

    <!-- ALERTS -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-[13px] sm:text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-5 sm:mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-4 rounded-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center mb-2 font-bold text-sm">
                <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Please fix the following errors:
            </div>
            <ul class="list-disc pl-5 text-[13px] sm:text-sm space-y-1 font-medium m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(Auth::user()->role === 'admin')
    
    <!-- ================= TEACHER ACCOUNTS ================= -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border border-slate-300 overflow-hidden mb-6 sm:mb-8">
        
        <!-- Header -->
        <div class="p-4 sm:px-6 sm:py-5 border border-slate-200 md:border-none md:border-b flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0 gap-4 sm:gap-0">
            <h3 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm border border-indigo-100">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                Teacher Accounts
            </h3>
            <button @click="showCreateModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 sm:py-2 bg-indigo-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                <i class="fa fa-plus mr-2"></i> Generate Account
            </button>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER (Consolidated to 4 clean columns) -->
                <thead class="hidden md:table-header-group bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">User Profile</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Contact Information</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-slate-200 bg-transparent md:bg-white">
                    @forelse($users->where('role', 'teacher') as $user)
                        
                        <!-- MOBILE CARD / DESKTOP ROW -->
                        <tr x-data="{ showResetModal: false, showPermModal: false }" class="block md:table-row relative bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150 pt-2 md:pt-0">
                            
                            <!-- 1. Avatar, Name & Username -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-50 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-700 font-extrabold text-sm md:text-base shrink-0 shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] md:text-sm font-extrabold text-slate-800 leading-tight">{{ $user->name }}</div>
                                        <div class="text-[11.5px] md:text-xs text-pink-500 font-bold mt-0.5">
                                            {{ $user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status Badge (Pinned absolute top-right on mobile, static on desktop) -->
                            <td class="absolute top-4 right-4 md:static md:table-cell md:px-6 md:py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-2 sm:px-3 py-1 text-[10px] sm:text-[11px] font-bold rounded-md md:rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm md:shadow-none">Active</span>
                                @else
                                    <span class="inline-flex px-2 sm:px-3 py-1 text-[10px] sm:text-[11px] font-bold rounded-md md:rounded-full bg-slate-100 text-slate-600 border border-slate-200 shadow-sm md:shadow-none">Suspended</span>
                                @endif
                            </td>

                            <!-- 3. Contact Info (Icons instead of labels) -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-50 md:border-none">
                                <div class="space-y-1.5 md:space-y-1">
                                    <div class="flex items-center text-[12px] sm:text-[13px] font-medium text-slate-600">
                                        <i class="fa fa-envelope w-4 text-center text-slate-400 mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="flex items-center text-[12px] sm:text-[13px] font-medium text-slate-600">
                                        <i class="fa fa-phone w-4 text-center text-slate-400 mr-2"></i> {{ $user->contact_number ?? 'No contact number' }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 4. Actions Footer (2x2 Grid on mobile, Row on desktop) -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-2xl md:rounded-none">
                                <div class="grid grid-cols-2 gap-2 w-full md:flex md:justify-end">
                                    
                                    <button @click="showResetModal = true" class="inline-flex justify-center md:justify-start items-center px-2 md:px-3 py-2 md:py-1.5 bg-white md:bg-amber-100 border border-slate-200 md:border-none text-slate-600 md:text-amber-800 text-[11px] md:text-xs font-bold rounded-lg hover:bg-amber-50 md:hover:bg-amber-200 transition-colors shadow-sm">
                                        <i class="fa fa-key mr-1.5 md:text-amber-600"></i> Reset
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0 flex">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-2 md:px-3 py-2 md:py-1.5 bg-slate-600 md:bg-white text-white md:text-slate-700 border border-slate-600 md:border-slate-300 text-[11px] md:text-xs font-bold rounded-lg hover:bg-slate-700 md:hover:bg-slate-50 transition-colors shadow-sm">
                                            <i class="fa fa-ban mr-1.5"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    <button @click="showPermModal = true" class="inline-flex justify-center md:justify-start items-center px-2 md:px-3 py-2 md:py-1.5 bg-white border border-slate-200 md:border-slate-300 text-slate-700 text-[11px] md:text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                                        <i class="fa fa-user-shield mr-1.5 text-indigo-500"></i> <span class="hidden md:inline">Permissions</span><span class="md:hidden">Access</span>
                                    </button>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher?');" class="m-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex justify-center md:justify-start items-center px-2 md:px-3 py-2 md:py-1.5 bg-white md:bg-rose-50 border border-slate-200 md:border-rose-200 text-rose-500 md:text-rose-600 text-[11px] md:text-xs font-bold rounded-lg md:w-8 md:h-8 hover:bg-rose-50 md:hover:bg-rose-100 transition-colors shadow-sm">
                                            <i class="fa fa-trash md:mr-0 mr-1.5"></i><span class="md:hidden">Delete</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- RESET MODAL (Responsive text inputs) -->
                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-300">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-key text-amber-500 mr-2"></i> Edit Credentials</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6 space-y-4">
                                                    <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 px-4 py-3 rounded-xl text-[12px] sm:text-[13px] font-medium leading-snug">
                                                        <i class="fa fa-info-circle mr-1.5"></i> You are modifying the login details for <strong class="text-indigo-900">{{ $user->name }}</strong> (Teacher).
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Contact Number</label>
                                                        <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="09XX-XXX-XXXX">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div class="border-t border-slate-200 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[12px] sm:text-[13px] font-bold text-rose-600 m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-indigo-600 hover:text-indigo-800 text-[11px] sm:text-[12px] font-bold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="w-full sm:flex-1 rounded-xl border-rose-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold focus:border-rose-500 focus:ring-rose-500" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="w-full sm:w-auto px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[13px] sm:text-sm rounded-xl hover:bg-indigo-100 transition-colors shrink-0 flex items-center justify-center">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy Login
                                                            </button>
                                                        </div>
                                                        <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-2 m-0 leading-tight">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</p>
                                                    </div>
                                                    <div class="bg-amber-50 border border-amber-300 p-4 rounded-xl mt-4 shadow-sm">
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-800 mb-2"><i class="fa fa-shield-alt text-amber-500 mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[11px] sm:text-[12px] text-slate-600 mb-3 leading-tight">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-amber-400 px-4 py-2.5 text-center text-base sm:text-lg font-bold tracking-[0.25em] focus:border-amber-500 focus:ring-amber-500" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 sm:py-2.5 bg-amber-500 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Login</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- PERMISSIONS MODAL -->
                                <div x-show="showPermModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showPermModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showPermModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showPermModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-300">
                                            <form action="{{ route('admin.accounts.permissions', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PATCH')
                                                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-sliders text-indigo-600 mr-2"></i> Permissions</h5>
                                                    <button type="button" @click="showPermModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6">
                                                    <p class="text-[12px] sm:text-[13px] text-slate-600 mb-5 leading-snug">Grant <strong class="text-slate-800">{{ $user->name }}</strong> access to specific Admin-level modules.</p>
                                                    
                                                    <div class="space-y-4">
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_manage_credentials" value="1" class="sr-only" {{ $user->can_manage_credentials ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_credentials ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_manage_credentials ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] sm:text-sm font-bold text-slate-800">1. Login Credentials Configuration</div>
                                                        </label>
                                                        <!-- Remaining checkboxes are the same -->
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_manage_calendar" value="1" class="sr-only" {{ $user->can_manage_calendar ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_manage_calendar ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_manage_calendar ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] sm:text-sm font-bold text-slate-800">2. Calendar Configuration</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_create_profiles" value="1" class="sr-only" {{ $user->can_create_profiles ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_create_profiles ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_create_profiles ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] sm:text-sm font-bold text-slate-800">3. Initial Profile Creation</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_archive_students" value="1" class="sr-only" {{ $user->can_archive_students ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_archive_students ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_archive_students ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] sm:text-sm font-bold text-slate-800">4. Archiving Module</div>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer">
                                                            <div class="relative shrink-0">
                                                                <input type="checkbox" name="can_approve_reports" value="1" class="sr-only" {{ $user->can_approve_reports ? 'checked' : '' }}>
                                                                <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors {{ $user->can_approve_reports ? 'bg-indigo-500' : '' }}"></div>
                                                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $user->can_approve_reports ? 'transform translate-x-4' : '' }}"></div>
                                                            </div>
                                                            <div class="ml-3 text-[13px] sm:text-sm font-bold text-slate-800">5. Admin Approval for Summaries</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showPermModal = false" class="px-4 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 sm:py-2.5 bg-indigo-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Access</button>
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
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3 text-slate-400 border border-slate-200"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-slate-500 text-[13px] sm:text-sm font-bold m-0">No teacher accounts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= GUARDIAN ACCOUNTS ================= -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border border-slate-300 overflow-hidden">
        
        <!-- Header -->
        <div class="p-4 sm:px-6 sm:py-5 border border-slate-200 md:border-none md:border-b flex justify-between items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-4 md:mb-0">
            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 mr-3 shadow-sm border border-emerald-100 shrink-0">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    Guardian Accounts
                </h3>
                <p class="text-[11px] sm:text-[13px] font-medium text-slate-500 mt-1 sm:ml-[52px] leading-tight">Guardians can only view profiles and leave feedback.</p>
            </div>
        </div>
        
        <div class="w-full">
            <table class="w-full text-left border-collapse block md:table">
                <!-- DESKTOP HEADER (Consolidated to 4 clean columns) -->
                <thead class="hidden md:table-header-group bg-slate-50 border-b border-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">User Profile</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Contact Information</th>
                        <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-extrabold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y-0 md:divide-y divide-slate-200 bg-transparent md:bg-white">
                    @forelse($users->where('role', 'guardian') as $user)
                        
                        <!-- MOBILE CARD / DESKTOP ROW -->
                        <tr x-data="{ showResetModal: false }" class="block md:table-row relative bg-white md:bg-transparent rounded-2xl md:rounded-none shadow-sm md:shadow-none border border-slate-200 md:border-none hover:bg-slate-50 transition-colors duration-150 pt-2 md:pt-0">
                            
                            <!-- 1. Avatar, Name & Username -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-50 md:border-none pr-20 md:pr-6">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 font-extrabold text-sm md:text-base shrink-0 shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-[14px] md:text-sm font-extrabold text-slate-800 leading-tight">{{ $user->name }}</div>
                                        <div class="text-[11.5px] md:text-xs text-pink-500 font-bold mt-0.5">
                                            {{ $user->username }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Status Badge (Pinned absolute top-right on mobile) -->
                            <td class="absolute top-4 right-4 md:static md:table-cell md:px-6 md:py-4">
                                @if($user->is_active)
                                    <span class="inline-flex px-2 sm:px-3 py-1 text-[10px] sm:text-[11px] font-bold rounded-md md:rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm md:shadow-none">Active</span>
                                @else
                                    <span class="inline-flex px-2 sm:px-3 py-1 text-[10px] sm:text-[11px] font-bold rounded-md md:rounded-full bg-slate-100 text-slate-600 border border-slate-200 shadow-sm md:shadow-none">Suspended</span>
                                @endif
                            </td>

                            <!-- 3. Contact Info -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 border-b border-slate-50 md:border-none">
                                <div class="space-y-1.5 md:space-y-1">
                                    <div class="flex items-center text-[12px] sm:text-[13px] font-medium text-slate-600">
                                        <i class="fa fa-envelope w-4 text-center text-slate-400 mr-2"></i> {{ $user->email }}
                                    </div>
                                    <div class="flex items-center text-[12px] sm:text-[13px] font-medium text-slate-600">
                                        <i class="fa fa-phone w-4 text-center text-slate-400 mr-2"></i> {{ $user->contact_number ?? 'No contact number' }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- 4. Actions Footer (3 buttons span evenly on mobile) -->
                            <td class="block md:table-cell px-4 py-3 md:px-6 md:py-4 bg-slate-50 md:bg-transparent rounded-b-2xl md:rounded-none">
                                <div class="grid grid-cols-3 gap-2 w-full md:flex md:justify-end">

                                    <button @click="showResetModal = true" class="inline-flex justify-center items-center px-2 py-2 md:py-1.5 bg-white md:bg-amber-100 border border-slate-200 md:border-none text-slate-600 md:text-amber-800 text-[11px] md:text-xs font-bold rounded-lg hover:bg-amber-50 md:hover:bg-amber-200 transition-colors shadow-sm">
                                        <i class="fa fa-key md:mr-1.5 text-amber-500 md:text-amber-600"></i> <span class="hidden md:inline">Reset</span>
                                    </button>

                                    <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST" class="m-0 flex">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 md:px-3 py-2 md:py-1.5 bg-slate-600 md:bg-white text-white md:text-slate-700 border border-slate-600 md:border-slate-300 text-[11px] md:text-xs font-bold rounded-lg hover:bg-slate-700 md:hover:bg-slate-50 transition-colors shadow-sm">
                                            <i class="fa fa-ban md:mr-1.5"></i> <span class="hidden md:inline">{{ $user->is_active ? 'Suspend' : 'Activate' }}</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guardian?');" class="m-0 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-2 py-2 md:py-1.5 bg-white md:bg-rose-50 border border-slate-200 md:border-rose-200 text-rose-500 md:text-rose-600 rounded-lg md:w-8 md:h-8 hover:bg-rose-50 md:hover:bg-rose-100 transition-colors shadow-sm text-[11px] md:text-xs font-bold md:font-normal">
                                            <i class="fa fa-trash md:mr-0 mr-1.5"></i><span class="md:hidden">Delete</span>
                                        </button>
                                    </form>
                                </div>

                                <!-- RESET MODAL (GUARDIAN) -->
                                <div x-show="showResetModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showResetModal = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="showResetModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-300">
                                            
                                            <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                                                    <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-key text-amber-500 mr-2"></i> Edit Credentials</h5>
                                                    <button type="button" @click="showResetModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                                                </div>
                                                <div class="p-5 sm:p-6 space-y-4">
                                                    <div class="bg-sky-50 border border-sky-200 text-sky-800 px-4 py-3 rounded-xl text-[12px] sm:text-[13px] font-medium leading-snug">
                                                        <i class="fa fa-info-circle mr-1.5"></i> You are modifying the login details for <strong class="text-sky-900">{{ $user->name }}</strong> (Guardian).
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                                                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Contact Number</label>
                                                        <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="09XX-XXX-XXXX">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">System Username</label>
                                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2 text-[13px] sm:text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                    </div>
                                                    <div class="border-t border-slate-200 pt-4 mt-2">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <label class="block text-[12px] sm:text-[13px] font-bold text-rose-600 m-0"><i class="fa fa-lock mr-1.5"></i> New Password</label>
                                                            <button type="button" onclick="generatePassword({{ $user->id }})" class="text-indigo-600 hover:text-indigo-800 text-[11px] sm:text-[12px] font-bold transition-colors">
                                                                <i class="fa fa-magic mr-1"></i> Auto-Generate
                                                            </button>
                                                        </div>
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <input type="text" name="password" id="new_password_{{ $user->id }}" class="w-full sm:flex-1 rounded-xl border-rose-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold focus:border-rose-500 focus:ring-rose-500" placeholder="Leave blank to keep current">
                                                            <button type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})" class="w-full sm:w-auto px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[13px] sm:text-sm rounded-xl hover:bg-indigo-100 transition-colors shrink-0 flex items-center justify-center">
                                                                <i class="fa fa-copy mr-1.5"></i> Copy Login
                                                            </button>
                                                        </div>
                                                        <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-2 m-0 leading-tight">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</p>
                                                    </div>
                                                    <div class="bg-amber-50 border border-amber-300 p-4 rounded-xl mt-4 shadow-sm">
                                                        <label class="block text-[12px] sm:text-[13px] font-bold text-slate-800 mb-2"><i class="fa fa-shield-alt text-amber-500 mr-1.5"></i> Admin Security Verification</label>
                                                        <p class="text-[11px] sm:text-[12px] text-slate-600 mb-3 leading-tight">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                        <input type="text" name="mfa_code" class="w-full rounded-xl border-amber-400 px-4 py-2.5 text-center text-base sm:text-lg font-bold tracking-[0.25em] focus:border-amber-500 focus:ring-amber-500" placeholder="123456" maxlength="6" required>
                                                    </div>
                                                </div>
                                                <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 w-full">
                                                    <button type="button" @click="showResetModal = false" class="px-4 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 sm:py-2.5 bg-amber-500 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Save Login</button>
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
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3 text-slate-400 border border-slate-200"><i class="fa fa-user-slash text-xl"></i></div>
                                <p class="text-slate-500 text-[13px] sm:text-sm font-bold m-0">No guardian accounts found.</p>
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
            <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showCreateModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showCreateModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-300 relative w-full">
                <form action="{{ route('admin.accounts.store') }}" method="POST" class="m-0">
                    @csrf
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                        <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0"><i class="fa fa-user-plus text-indigo-600 mr-2"></i> Generate Account</h5>
                        <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times text-xl"></i></button>
                    </div>
                    <div class="p-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Account Role</label>
                            <select name="role" id="create_role" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required>
                                <option value="" disabled selected>Select Role...</option>
                                <option value="teacher">Teacher</option>
                                <option value="guardian">Guardian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Full Name</label>
                            <input type="text" name="name" id="create_name" value="{{ old('name') }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required placeholder="e.g., Juan Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Email Address</label>
                            <input type="email" name="email" id="create_email" value="{{ old('email') }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required placeholder="e.g., juan@dalc.com">
                        </div>
                        <div>
                            <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" id="create_contact" value="{{ old('contact_number') }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required placeholder="09XX-XXX-XXXX">
                        </div>
                        <div>
                            <label class="block text-[12px] sm:text-[13px] font-bold text-slate-700 mb-1">Username</label>
                            <input type="text" name="username" id="create_username" value="{{ old('username') }}" class="w-full rounded-xl border-slate-300 px-3.5 py-2 sm:py-2.5 text-[13px] sm:text-sm focus:border-indigo-500 shadow-sm" required placeholder="e.g., juandc">
                        </div>
                        
                        <div class="border-t border-slate-100 pt-4 mt-2">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-[12px] sm:text-[13px] font-bold text-rose-600 m-0"><i class="fa fa-lock mr-1.5"></i> Temporary Password</label>
                                
                                <button type="button" onclick="generateCreatePassword()" class="text-indigo-600 hover:text-indigo-800 text-[11px] sm:text-[12px] font-bold transition-colors">
                                    <i class="fa fa-magic mr-1"></i> Auto-Generate
                                </button>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2 mb-3">
                                <input type="text" name="password" id="create_password" class="w-full sm:flex-1 rounded-xl border-rose-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold focus:border-rose-500" placeholder="Min. 8 characters" required>
                                <button type="button" onclick="copyCreateCredentials()" class="w-full sm:w-auto px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[13px] sm:text-sm rounded-xl hover:bg-indigo-100 transition-colors shrink-0 flex justify-center items-center">
                                    <i class="fa fa-copy mr-1.5"></i> Copy Login
                                </button>
                            </div>
                            
                            <label class="block text-[12px] sm:text-[13px] font-bold text-rose-600 mb-1">Confirm Password</label>
                            <input type="text" name="password_confirmation" id="create_password_confirmation" class="w-full rounded-xl border-rose-300 px-3.5 py-2 text-[13px] sm:text-sm font-bold focus:border-rose-500 shadow-sm" required placeholder="Type it again">
                            <p class="text-[10.5px] sm:text-[11px] text-slate-500 mt-2 m-0 leading-tight"><i class="fa fa-shield-alt mr-1"></i> They will be forced to change this upon first login.</p>
                        </div>
                    </div>
                    <div class="px-5 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 w-full">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-xl text-[13px] sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 sm:py-2.5 bg-indigo-600 text-white text-[13px] sm:text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center"><i class="fa fa-save mr-1.5"></i> Create</button>
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