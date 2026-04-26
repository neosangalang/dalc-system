@extends('layouts.app')

@section('title', 'User Management')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <strong><i class="fa fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(Auth::user()->role === 'admin')
<div class="data-card mb-4" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0"><i class="fa fa-chalkboard-teacher me-2" style="color: var(--primary);"></i>Teacher Accounts</h5>
        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#generateAccountModal">
            <i class="fa fa-plus me-1"></i> Generate Account
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead style="background: #f8f9fa; color: #6c757d; font-size: 12px; text-transform: uppercase;">
                <tr>
                    <th class="py-3">Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users->where('role', 'teacher') as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td style="color: #e83e8c;">{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success rounded-pill px-3">Active</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Suspended</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                
                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#resetCredentialsModal{{ $user->id }}">
                                    <i class="fa fa-key"></i> Reset
                                </button>

                                <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary btn-sm text-white" style="font-weight: 600;">
                                        <i class="fa fa-ban me-1"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>

                                <button type="button" class="btn btn-light btn-sm border shadow-sm" style="font-weight: 600;" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $user->id }}">
                                    <i class="fa fa-user-shield me-1"></i> Permissions
                                </button>

                                <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="modal fade" id="resetCredentialsModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 24px 64px rgba(0,0,0,0.15)">
                                        <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="fa fa-key text-warning me-2"></i> Edit Credentials for {{ $user->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body p-4 text-start">
                                                <div class="alert alert-info py-2" style="font-size: 13px; border-radius: 8px;">
                                                    <i class="fa fa-info-circle me-1"></i> You are modifying the login details for a <strong>Teacher</strong> account.
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-muted">Email Address</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-muted">System Username</label>
                                                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                </div>

                                                <div class="mb-2 border-top pt-3 mt-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label fw-bold small text-danger m-0"><i class="fa fa-lock me-1"></i> New Password</label>
                                                        <button type="button" class="btn btn-sm btn-link p-0 text-primary fw-bold text-decoration-none" onclick="generatePassword({{ $user->id }})" style="font-size: 12px;">
                                                            <i class="fa fa-magic"></i> Auto-Generate
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="input-group">
                                                        <input type="text" name="password" id="new_password_{{ $user->id }}" class="form-control border-danger fw-bold" placeholder="Leave blank to keep current password">
                                                        <button class="btn btn-outline-primary fw-bold" type="button" onclick="copyNewCredentials('{{ $user->username }}', {{ $user->id }})">
                                                            <i class="fa fa-copy"></i> Copy Login
                                                        </button>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 11px;">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</small>
                                                </div>

                                                <hr class="my-4">
                                                <div class="p-3 rounded" style="background-color: #fff3cd; border: 1px solid #ffe69c;">
                                                    <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                                                        <i class="fa fa-shield-alt text-warning me-1"></i> Admin Security Verification
                                                    </label>
                                                    <p class="small text-muted mb-2">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                    <input type="text" name="mfa_code" class="form-control fw-bold text-center" placeholder="123456" maxlength="6" required style="letter-spacing: 5px; font-size: 18px;">
                                                    
                                                    @error('mfa_code')
                                                        <div class="text-danger small mt-1 fw-bold"><i class="fa fa-exclamation-circle"></i> {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;">
                                                <button type="button" class="btn btn-light fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning fw-bold shadow-sm text-dark"><i class="fa fa-save me-1"></i> Save New Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="permissionsModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 24px 64px rgba(0,0,0,0.15)">
                                        <form action="{{ route('admin.accounts.permissions', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="fa fa-sliders text-primary me-2"></i> Module Permissions
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body p-4 text-start">
                                                <p class="text-muted small mb-4">Grant <strong>{{ $user->name }}</strong> access to specific Admin-level modules.</p>
                                                  
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="cred{{ $user->id }}" name="can_manage_credentials" value="1" {{ $user->can_manage_credentials ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="cred{{ $user->id }}">1. Login Credentials Configuration</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="cal{{ $user->id }}" name="can_manage_calendar" value="1" {{ $user->can_manage_calendar ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="cal{{ $user->id }}">2. Calendar Configuration</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="prof{{ $user->id }}" name="can_create_profiles" value="1" {{ $user->can_create_profiles ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="prof{{ $user->id }}">3. Initial Profile Creation</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="arch{{ $user->id }}" name="can_archive_students" value="1" {{ $user->can_archive_students ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="arch{{ $user->id }}">4. Archiving Module</label>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="appr{{ $user->id }}" name="can_approve_reports" value="1" {{ $user->can_approve_reports ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="appr{{ $user->id }}">5. Admin Approval for Summaries</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;">
                                                <button type="button" class="btn btn-light fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary fw-bold shadow-sm"><i class="fa fa-save me-1"></i> Save Permissions</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No teacher accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="data-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold m-0"><i class="fa fa-user-shield me-2" style="color: var(--success);"></i>Guardian Accounts</h5>
            <small class="text-muted">Guardians can only view profiles and leave feedback.</small>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead style="background: #f8f9fa; color: #6c757d; font-size: 12px; text-transform: uppercase;">
                <tr>
                    <th class="py-3">Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users->where('role', 'guardian') as $user)
                    <tr>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td style="color: #e83e8c;">{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success rounded-pill px-3">Active</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Suspended</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                
                                <button type="button" class="btn btn-sm btn-info text-white fw-bold shadow-sm" 
                                        onclick="copyDefaultGuardianLogin('{{ $user->username }}')"
                                        title="Copy Default Login">
                                    <i class="fa fa-copy"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#resetCredentialsModal{{ $user->id }}">
                                    <i class="fa fa-key"></i> Reset
                                </button>

                                <form action="{{ route('admin.accounts.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary btn-sm text-white" style="font-weight: 600;">
                                        <i class="fa fa-ban me-1"></i> {{ $user->is_active ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.accounts.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this guardian?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="modal fade" id="resetCredentialsModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 24px 64px rgba(0,0,0,0.15)">
                                        <form action="{{ route('admin.accounts.update-credentials', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                                <h5 class="modal-title fw-bold text-dark">
                                                    <i class="fa fa-key text-warning me-2"></i> Edit Credentials for {{ $user->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            
                                            <div class="modal-body p-4 text-start">
                                                <div class="alert alert-info py-2" style="font-size: 13px; border-radius: 8px;">
                                                    <i class="fa fa-info-circle me-1"></i> You are modifying the login details for a <strong>Guardian</strong> account.
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-muted">Email Address</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small text-muted">System Username</label>
                                                    <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                                                </div>

                                                <div class="mb-2 border-top pt-3 mt-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label fw-bold small text-danger m-0"><i class="fa fa-lock me-1"></i> New Password</label>
                                                        <button type="button" class="btn btn-sm btn-link p-0 text-primary fw-bold text-decoration-none" onclick="generatePassword('{{ $user->id }}')" style="font-size: 12px;">
                                                            <i class="fa fa-magic"></i> Auto-Generate
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="input-group">
                                                        <input type="text" name="password" id="new_password_{{ $user->id }}" class="form-control border-danger fw-bold" placeholder="Leave blank to keep current password">
                                                        <button class="btn btn-outline-primary fw-bold" type="button" onclick="copyNewCredentials('{{ $user->username }}', '{{ $user->id }}')">
                                                            <i class="fa fa-copy"></i> Copy Login
                                                        </button>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 11px;">Must be at least 8 characters. Generating or typing a new password instantly overwrites the old one.</small>
                                                </div>

                                                <hr class="my-4">
                                                <div class="p-3 rounded" style="background-color: #fff3cd; border: 1px solid #ffe69c;">
                                                    <label class="form-label fw-bold text-dark" style="font-size: 13px;">
                                                        <i class="fa fa-shield-alt text-warning me-1"></i> Admin Security Verification
                                                    </label>
                                                    <p class="small text-muted mb-2">To authorize these changes, please enter the 6-digit code from your Authenticator app.</p>
                                                    <input type="text" name="mfa_code" class="form-control fw-bold text-center" placeholder="123456" maxlength="6" required style="letter-spacing: 5px; font-size: 18px;">
                                                    
                                                    @error('mfa_code')
                                                        <div class="text-danger small mt-1 fw-bold"><i class="fa fa-exclamation-circle"></i> {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;">
                                                <button type="button" class="btn btn-light fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning fw-bold shadow-sm text-dark"><i class="fa fa-save me-1"></i> Save New Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No guardian accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(Auth::user()->role === 'admin')
<div class="modal fade" id="generateAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid #e9ecef; padding: 20px 24px;">
                    <h5 class="modal-title fw-bold"><i class="fa fa-user-plus me-2" style="color: var(--primary);"></i>Generate New Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
            
                    <div class="mb-3">
                        <label class="form-label fw-bold">Account Role</label>
                        <select name="role" class="form-select" required>
                            <option value="" disabled selected>Select Role...</option>
                            <option value="teacher">Teacher</option>
                            <option value="guardian">Guardian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., Juan Dela Cruz">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="e.g., juan@dalc.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="e.g., juandc">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Temporary Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirm Temporary Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Type it again">
                        <small class="text-muted" style="font-size: 12px;"><i class="fa fa-shield-alt me-1"></i> They will be forced to change this upon first login.</small>
                    </div>

                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 16px 24px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa fa-save me-2"></i>Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Automatically reopen the modal if there are validation errors on submission
    @if ($errors->any() && Auth::user()->role === 'admin')
        document.addEventListener("DOMContentLoaded", function() {
            var modalEl = document.getElementById('generateAccountModal');
            if (modalEl) {
                var myModal = new bootstrap.Modal(modalEl);
                myModal.show();
            }
        });
    @endif

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
        
        const textToCopy = `Dream Achievers Learning Center - Parent Portal\n\nUsername: ${username}\nNew Password: ${passField.value}\n\nPlease log in and change this password immediately.`;
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert('New login credentials copied to clipboard! You can now click "Save New Login".');
        }).catch(err => {
            alert('Failed to copy text. Please check your browser permissions.');
        });
    }
</script>
@endpush