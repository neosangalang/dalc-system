<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MFA Setup - DALC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4F6AF5; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: #3b54d1; border-color: #3b54d1; }
    </style>
</head>
<body style="background: #f4f7fe; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div class="card" style="max-width: 500px; width: 100%; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="card-body p-5 text-center">
            
            <div style="width: 60px; height: 60px; background: #EEF1FF; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                <i class="fa fa-qrcode"></i>
            </div>
            
            <h4 class="fw-bold mb-3">Secure Your Account</h4>
            <p class="text-muted mb-4">Open <strong>Google Authenticator</strong> on your phone and scan the QR code below.</p>

            <div class="mb-4 p-3 bg-white" style="display: inline-block; border-radius: 12px; border: 2px solid #e9ecef;">
                {!! $qrCodeImage !!}
            </div>

            <p class="text-muted small mb-4">Can't scan the code? Enter this setup key manually:<br> <code class="fs-6">{{ $secretKey }}</code></p>

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('security.mfa.verify') }}" method="POST">
                @csrf
                <div class="mb-4 text-start">
                    <label class="form-label fw-bold">Enter the 6-digit code</label>
                    <input type="text" name="one_time_password" class="form-control py-2 text-center fs-4 tracking-widest" required placeholder="• • • • • •" maxlength="6" style="letter-spacing: 8px;">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa fa-check-circle me-2"></i> Verify & Complete Setup
                </button>
            </form>
            
        </div>
    </div>

</body>
</html>