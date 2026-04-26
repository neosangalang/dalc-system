@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="row align-items-start">
    <div class="col-12 col-xl-4 mb-4">
        <div class="data-card text-center" style="position: sticky; top: 100px;">
            <div class="mx-auto mb-3 shadow-sm" style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary), var(--purple)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px; color: white; font-weight: 800; border: 6px solid #EEF1FF;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <h4 class="fw-bold mb-1 text-dark">{{ Auth::user()->name }}</h4>
            <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge-custom badge-active px-3 py-2 text-uppercase"><i class="fa fa-user-shield"></i> {{ Auth::user()->role }}</span>
            </div>
            <hr style="border-color: var(--border);">
            <div class="text-start mt-4 px-2">
                <p class="mb-2 text-muted" style="font-size: 13px;"><i class="fa fa-id-badge me-2" style="width: 16px;"></i> <strong>Username:</strong> {{ Auth::user()->username }}</p>
                <p class="mb-2 text-muted" style="font-size: 13px;"><i class="fa fa-clock me-2" style="width: 16px;"></i> <strong>Joined:</strong> {{ Auth::user()->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="data-card p-4 p-md-5 mb-4">
            <h5 class="fw-bold mb-4"><i class="fa fa-shield-alt text-primary me-2"></i>Change Password</h5>
            <form action="#" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 13px;">Current Password</label>
                    <input type="password" name="current_password" class="form-control py-2" required>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold" style="font-size: 13px;">New Password</label>
                        <input type="password" name="password" class="form-control py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control py-2" required>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 8px;">Update Password</button>
                </div>
            </form>
        </div>

        <div class="data-card p-4 p-md-5">
            <h5 class="fw-bold mb-3"><i class="fa fa-lock text-primary me-2"></i>Two-Factor Authentication (MFA)</h5>
            <div class="d-flex align-items-center justify-content-between p-3" style="background: #F8FAFC; border: 1px solid var(--border); border-radius: 12px;">
                <div>
                    <p class="mb-1 fw-bold text-dark">Authenticator App</p>
                    <p class="mb-0 text-muted" style="font-size: 13px;">Secure your account using a mobile app like Google Authenticator.</p>
                </div>
                <div>
                    @if(Auth::user()->two_factor_secret)
                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="fa fa-check me-1"></i> Enabled</span>
                    @else
                        <a href="{{ route('security.mfa.setup') }}" class="btn btn-sm btn-outline-primary fw-bold">Enable MFA</a>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection