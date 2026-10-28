<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Setup - DALC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4F6AF5; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: #3b54d1; border-color: #3b54d1; }
        
        /* Checklist icon sizing */
        .req-icon { width: 16px; text-align: center; }
        .checklist-container { background-color: #f8f9fa; border: 1px solid #e9ecef; }
        
        /* Input focus outline match */
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 0.25rem rgba(79, 106, 245, 0.25); }
    </style>
</head>
<body style="background: #f4f7fe; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">

    <div class="card" style="max-width: 450px; width: 100%; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div style="width: 60px; height: 60px; background: #EEF1FF; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                    <i class="fa fa-lock"></i>
                </div>
                <h4 class="fw-bold">Welcome to DALC!</h4>
                <p class="text-muted" style="font-size: 14px;">For the security of our users, please replace your temporary password with a private one.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px; font-size: 13px; background-color: #fdf2f2; border-color: #fbc4c4; color: #c81e1e;">
                    <div class="fw-bold mb-1"><i class="fa fa-exclamation-circle me-1"></i> Please fix the following errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('security.setup.store') }}" method="POST" id="passwordForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 14px;">New Private Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="newPassword" class="form-control py-2" required placeholder="Enter new password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                    
                    <!-- Dynamic Validation Checklist -->
                    <div class="mt-2 p-3 rounded checklist-container" style="font-size: 12px; font-weight: 500;">
                        <div class="mb-1 text-danger" id="req-upper"><i class="fa fa-times req-icon me-1"></i> One uppercase letter</div>
                        <div class="mb-1 text-danger" id="req-lower"><i class="fa fa-times req-icon me-1"></i> One lowercase letter</div>
                        <div class="mb-1 text-danger" id="req-num"><i class="fa fa-times req-icon me-1"></i> One number</div>
                        <div class="mb-1 text-danger" id="req-spec"><i class="fa fa-times req-icon me-1"></i> One special character (!@#$%^&*)</div>
                        <div class="text-danger" id="req-length"><i class="fa fa-times req-icon me-1"></i> Minimum 8 characters</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size: 14px;">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="confirmPassword" class="form-control py-2" required placeholder="Type it again">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    </div>
                    <!-- Real-time match indicator -->
                    <div id="match-indicator" class="mt-2 d-none" style="font-size: 12px; font-weight: 600;">
                        <i class="fa fa-times req-icon me-1 text-danger"></i> <span class="text-danger">Passwords do not match</span>
                    </div>
                <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="agree_terms" id="agreeTerms" value="1" required>
                        <label class="form-check-label text-secondary" for="agreeTerms" style="font-size: 12.5px; line-height: 1.5;">
                            I confirm that I have read, understood, and agree to the 
                            <a href="{{ route('legal.terms') }}" onclick="return openTermsModal(event)" class="fw-bold text-primary text-decoration-none" role="button">Terms and Conditions</a>, 
                            <a href="{{ route('legal.privacy') }}" onclick="return openPrivacyModal(event)" class="fw-bold text-primary text-decoration-none" role="button">Privacy Policy (RA 10173)</a>, and 
                            <a href="{{ route('legal.ai-ethics') }}" target="_blank" class="fw-bold text-primary text-decoration-none">AI Ethics & Clinical Guidelines</a>.
                        </label>
                    </div>
                </div>
                
                <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-2 mb-3" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa fa-shield-alt me-2"></i> Set Secure Password
                </button>
            </form>
            
            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-link text-muted" style="text-decoration: none; font-size: 13px; font-weight: 500;">Log Out for Now</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('newPassword');
            const confirmInput = document.getElementById('confirmPassword');
            const matchIndicator = document.getElementById('match-indicator');
            const submitBtn = document.getElementById('submitBtn');

            // Requirement Elements
            const reqUpper = document.getElementById('req-upper');
            const reqLower = document.getElementById('req-lower');
            const reqNum = document.getElementById('req-num');
            const reqSpec = document.getElementById('req-spec');
            const reqLength = document.getElementById('req-length');

            // Toggle Password Visibility
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetInput = document.getElementById(this.getAttribute('data-target'));
                    const icon = this.querySelector('i');
                    
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                });
            });

            // Password Validation Logic
            function validatePassword() {
                const val = passwordInput.value;

                // Test conditions
                const isUpper = /[A-Z]/.test(val);
                const isLower = /[a-z]/.test(val);
                const isNum = /[0-9]/.test(val);
                const isSpec = /[^A-Za-z0-9]/.test(val);
                const isLength = val.length >= 8;

                // Update UI for each condition
                toggleRequirement(reqUpper, isUpper);
                toggleRequirement(reqLower, isLower);
                toggleRequirement(reqNum, isNum);
                toggleRequirement(reqSpec, isSpec);
                toggleRequirement(reqLength, isLength);

                checkMatch();
            }

            function toggleRequirement(el, isValid) {
                const icon = el.querySelector('i');
                if (isValid) {
                    el.classList.remove('text-danger');
                    el.classList.add('text-success');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-check');
                } else {
                    el.classList.remove('text-success');
                    el.classList.add('text-danger');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-times');
                }
            }

            // Confirm Password Match Logic
            function checkMatch() {
                const val1 = passwordInput.value;
                const val2 = confirmInput.value;

                if (val2.length > 0) {
                    matchIndicator.classList.remove('d-none');
                    const icon = matchIndicator.querySelector('i');
                    const text = matchIndicator.querySelector('span');

                    if (val1 === val2) {
                        icon.classList.remove('fa-times', 'text-danger');
                        icon.classList.add('fa-check', 'text-success');
                        text.classList.remove('text-danger');
                        text.classList.add('text-success');
                        text.textContent = 'Passwords match';
                    } else {
                        icon.classList.remove('fa-check', 'text-success');
                        icon.classList.add('fa-times', 'text-danger');
                        text.classList.remove('text-success');
                        text.classList.add('text-danger');
                        text.textContent = 'Passwords do not match';
                    }
                } else {
                    matchIndicator.classList.add('d-none');
                }
            }

            // Event Listeners
            passwordInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', checkMatch);
        });
    </script>

    @include('components.terms-modal')
    @include('components.privacy-modal')
</body>
</html>