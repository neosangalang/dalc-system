@extends('layouts.app')

@section('title', 'My Profile')

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
        <div class="data-card p-4 p-md-5">
            <h5 class="fw-bold mb-4"><i class="fa fa-id-card text-primary me-2"></i>Personal Information</h5>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label fw-bold" style="font-size: 13px;">Full Name</label>
                    <input type="text" name="name" class="form-control py-2 bg-light text-muted" value="{{ Auth::user()->name }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" style="font-size: 13px;">Email Address</label>
                    <input type="email" name="email" class="form-control py-2 bg-light text-muted" value="{{ Auth::user()->email }}" readonly>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold" style="font-size: 13px;">Username</label>
                <input type="text" class="form-control py-2 bg-light text-muted" value="{{ Auth::user()->username }}" disabled>
                
                <small class="text-muted mt-2 d-block"><i class="fa fa-info-circle me-1"></i> Please contact your System Administrator if you need to update your profile details.</small>
            </div>
            
        </div>
    </div>
</div>
@endsection