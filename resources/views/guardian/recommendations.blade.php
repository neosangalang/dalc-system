@extends('layouts.app')

@section('title', 'Home-Based Recommendations')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Personalized Home Activities</h3>
        <p class="text-muted">Dynamic recommendations and daily highlights based on <strong>{{ $activeChild->first_name ?? 'your child' }}'s</strong> progress.</p>
    </div>

    @if(isset($activeChild) && isset($myChildren))
    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn btn-light border bg-white rounded-pill px-3 py-2 fw-bold text-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="width: 24px; height: 24px; background: #4F6AF5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                    <i class="fa fa-user"></i>
                </div>
                Viewing: {{ $activeChild->first_name }}
            </button>
            
            <ul class="dropdown-menu shadow border-0 mt-2 p-2" style="border-radius: 12px; min-width: 200px;">
                <li><h6 class="dropdown-header text-muted fw-bold" style="letter-spacing: 1px; font-size: 11px;">MY CHILDREN</h6></li>
                
                @foreach($myChildren as $child)
                    <li>
                        <a class="dropdown-item py-2 rounded {{ $activeChild->id === $child->id ? 'bg-primary text-white fw-bold' : '' }}" 
                           href="{{ route('guardian.switch-child', $child->id) }}">
                            {{ $child->first_name }} {{ $child->last_name }}
                            @if($activeChild->id === $child->id)
                                <i class="fa fa-check float-end mt-1"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>

@if(isset($activeChild))
<div class="row g-4">
    @forelse($recentRecommendations as $log)
        <div class="col-12">
            <div class="data-card border-top border-4 shadow-sm" style="border-color: var(--primary) !important; background: #ffffff; border-radius: 12px; overflow: hidden;">
                <div class="data-card-header border-bottom pb-3 mb-4 px-4 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold m-0 text-dark">
                                <i class="fa fa-star text-warning me-2"></i> {{ $log->student->first_name }}'s Daily Highlight
                            </h5>
                            <small class="text-muted fw-bold">From the daily log on: {{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }}</small>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 border border-primary-subtle rounded-pill">
                            {{ $log->quarter }}
                        </span>
                    </div>
                </div>
                
                <div class="row px-4 pb-3">
                    @if($log->image_path)
                        <div class="col-md-5 mb-4 mb-md-0 text-center">
                            <div class="p-2 bg-light rounded" style="border: 1px dashed #cbd5e1;">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="img-fluid rounded shadow-sm" style="max-height: 280px; width: auto; object-fit: cover;">
                                <p class="text-muted small mt-2 mb-1"><i class="fa fa-camera text-secondary me-1"></i> Photo from today's session</p>
                            </div>
                        </div>
                    @endif

                    <div class="{{ $log->image_path ? 'col-md-7' : 'col-12' }}">
                        <div class="h-100 p-4 d-flex flex-column justify-content-center" style="background: #f8f9fa; border-radius: 12px; border-left: 4px solid #34C97B;">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="fa fa-house-user me-2"></i> Tonight's Suggested Activity:
                            </h6>
                            <p class="mb-0" style="font-size: 1.05rem; line-height: 1.8; color: #475569; white-space: pre-wrap;">{{ $log->home_recommendations }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-4 pb-4 border-top pt-3 bg-white">
                    @include('components.comment-thread', ['item' => $log])
                </div>

            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted bg-white rounded shadow-sm border">
            <div class="mx-auto mb-3" style="width: 70px; height: 70px; background: #f8f9fa; color: #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px;">
                <i class="fa fa-bed"></i>
            </div>
            <h5 class="mb-1 fw-bold text-dark">No recommendations right now.</h5>
            <p class="small mb-0">When the teacher logs today's progress, photos and personalized activities for {{ $activeChild->first_name }} will appear here!</p>
        </div>
    @endforelse
</div>
@else
<div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
    <i class="fa fa-info-circle me-2"></i> No children are currently linked to your account.
</div>
@endif

@endsection