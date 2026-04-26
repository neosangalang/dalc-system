@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Notifications</h3>
        <p class="text-muted">Recent updates and system alerts.</p>
    </div>
    <button class="btn btn-sm btn-outline-secondary"><i class="fa fa-check-double me-1"></i> Mark all as read</button>
</div>

<div class="data-card p-0 overflow-hidden">
    <div class="list-group list-group-flush">
        
        <div class="list-group-item p-4 bg-light border-bottom" style="border-left: 4px solid var(--primary);">
            <div class="d-flex gap-3">
                <div class="text-primary mt-1"><i class="fa fa-file-signature fs-4"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">New Report Available</h6>
                    <p class="text-muted small mb-2">A new official progress report has been approved and is ready for your review.</p>
                    <a href="{{ route('guardian.reports') }}" class="btn btn-sm btn-primary">View Report</a>
                    <div class="text-muted small mt-2"><i class="fa fa-clock me-1"></i> Just now</div>
                </div>
            </div>
        </div>

        <div class="list-group-item p-4 bg-white border-bottom">
            <div class="d-flex gap-3">
                <div class="text-success mt-1"><i class="fa fa-bullseye fs-4"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">IEP Goal Achieved!</h6>
                    <p class="text-muted small mb-0">Great news! Your child has successfully achieved an IEP goal in the Perceptuo-Cognitive domain.</p>
                    <div class="text-muted small mt-2"><i class="fa fa-clock me-1"></i> 2 days ago</div>
                </div>
            </div>
        </div>

        <div class="list-group-item p-4 bg-white">
            <div class="d-flex gap-3">
                <div class="text-secondary mt-1"><i class="fa fa-user-shield fs-4"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Account Linked Successfully</h6>
                    <p class="text-muted small mb-0">Your guardian account has been securely linked to your child's profile.</p>
                    <div class="text-muted small mt-2"><i class="fa fa-clock me-1"></i> 1 week ago</div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection