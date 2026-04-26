<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - DALC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4F6AF5; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: #3b54d1; border-color: #3b54d1; }
    </style>
</head>
<body style="background: #f4f7fe; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div class="card" style="max-width: 450px; width: 100%; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="card-body p-5 text-center">
            
            <div style="width: 60px; height: 60px; background: #EEF1FF; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                <i class="fa fa-mobile-alt"></i>
            </div>
            
            <h4 class="fw-bold mb-3">Two-Factor Authentication</h4>
            <p class="text-muted mb-4">Please open your authenticator app and enter the 6-digit code to access your account.</p>

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('security.mfa.verify-login') }}" method="POST">
                @csrf
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Authentication Code</label>
                    <input type="text" name="one_time_password" class="form-control py-2 text-center fs-4 tracking-widest" required placeholder="• • • • • •" maxlength="6" style="letter-spacing: 8px;" autofocus>
                </div>
                
                <div class="form-check text-start mb-4">
                    <input class="form-check-input" type="checkbox" name="remember_device" id="remember_device">
                    <label class="form-check-label text-muted small" for="remember_device">
                        Remember this device for 30 days
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa fa-unlock-alt me-2"></i> Verify Code
                </button>
            </form>
            
            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-link text-muted" style="text-decoration: none; font-size: 14px;">Cancel and Log Out</button>
            </form>
        </div>
    </div>

</body>
</html>