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
    </style>
</head>
<body style="background: #f4f7fe; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div class="card" style="max-width: 500px; width: 100%; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div style="width: 60px; height: 60px; background: #EEF1FF; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px;">
                    <i class="fa fa-lock"></i>
                </div>
                <h4 class="fw-bold">Welcome to DALC!</h4>
                <p class="text-muted">For the security of our students, please replace your temporary password with a private one.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: 8px; font-size: 14px;">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('security.setup.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">New Private Password</label>
                    <input type="password" name="password" class="form-control py-2" required placeholder="Minimum 8 characters">
                    <small class="text-muted" style="font-size: 12px;">Must contain at least one uppercase letter and one number.</small>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control py-2" required placeholder="Type it again">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3" style="border-radius: 8px; font-weight: 600;">
                    <i class="fa fa-shield-alt me-2"></i> Set Secure Password
                </button>
            </form>
            
            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="btn btn-link text-muted" style="text-decoration: none; font-size: 14px;">Log Out for Now</button>
            </form>
        </div>
    </div>

</body>
</html>