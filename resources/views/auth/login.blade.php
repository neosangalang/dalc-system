<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dream Achievers Learning Center</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* Basic styles needed just for the login screen */
        :root { --primary: #4F6AF5; --primary-dark: #3751D7; --text-dark: #1A1F4B; --text-muted: #7B8DB7; --border: #E8ECF8; }
        body { font-family: 'Nunito', sans-serif; }
        #loginScreen {
            background: linear-gradient(135deg, #1E2657 0%, #2E3D8F 50%, #4F6AF5 100%);
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; position: relative; overflow: hidden;
        }
        .login-card { background: white; border-radius: 24px; padding: 48px 44px; width: 100%; max-width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,0.25); z-index: 2; }
        .login-logo { width: 72px; height: 72px; background: linear-gradient(135deg, var(--primary), #9B6DFF); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 20px; box-shadow: 0 8px 24px rgba(79,106,245,0.35); }
        .login-card h1 { font-size: 22px; font-weight: 800; color: var(--text-dark); text-align: center; line-height: 1.3; }
        .login-card p.subtitle { font-size: 13px; color: var(--text-muted); text-align: center; margin-bottom: 28px; }
        .form-label { font-weight: 700; font-size: 13px; color: var(--text-dark); margin-bottom: 6px; }
        .form-control { border: 2px solid var(--border); border-radius: 12px; padding: 12px 16px; font-size: 14px; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,106,245,0.1); }
        .btn-primary-custom { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 12px; padding: 13px 24px; font-weight: 700; font-size: 15px; width: 100%; transition: all 0.2s; box-shadow: 0 4px 16px rgba(79,106,245,0.3); }
    </style>
</head>
<body>

<div id="loginScreen">
    <div class="login-card">
        <div class="login-logo">🎓</div>
        <h1>Dream Achievers<br>Learning Center</h1>
        <p class="subtitle">AI-Assisted IEP Management System</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3" style="font-size:13px;border-radius:10px">
                    ❌ {{ $errors->first() }}
                </div>
            @endif
        <form method="POST" action="{{ route('login') }}">
        @csrf

    <div class="mb-3">
        <label class="form-label fw-bold">Username</label>
        <input type="text" name="username" class="form-control py-2" required autofocus placeholder="Enter your username">
    </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control py-2" required placeholder="Enter your password">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label text-muted small" for="remember_me">
                    Remember me
                </label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-primary text-decoration-none small fw-bold">
                    Forgot password?
                 </a>
                    @endif

                    </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 ...">
                <i class="fa fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
    </div>
</div>

</body>
</html>