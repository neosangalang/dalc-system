@extends('layouts.app')

@section('title', 'Guardian Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --kid-bg: #F4F8FE;
        --kid-blue: #5C8DF6;
        --kid-blue-light: #E3EFFF;
        --kid-yellow: #FFC043;
        --kid-yellow-light: #FFF4D9;
        --kid-green: #4CD18E;
        --kid-green-light: #DDF7E9;
        --kid-purple: #9D7CFF;
        --kid-purple-light: #F0E8FF;
        --kid-pink: #FF84A1;
        --text-main: #3B4A6B;
        --text-soft: #7A89A8;
    }

    body {
        background-color: var(--kid-bg);
        font-family: 'Quicksand', sans-serif;
        color: var(--text-main);
    }

    /* Playful Soft Cards */
    .fun-card {
        background: #FFFFFF;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(92, 141, 246, 0.08);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .fun-card-header {
        padding: 20px 24px;
        border-bottom: 2px dashed #EAF0FA;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Bouncy Stat Cards */
    .stat-box {
        padding: 24px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .stat-box:hover {
        transform: translateY(-8px);
    }
    .stat-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    /* Interactive Profile Buttons */
    .kid-profile-item {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 20px;
        padding: 16px;
    }
    .kid-profile-item:hover {
        background-color: var(--kid-blue-light);
        border-color: var(--kid-blue);
    }
    .kid-profile-item.active-profile {
        background-color: var(--kid-blue);
        color: white !important;
        box-shadow: 0 8px 20px rgba(92, 141, 246, 0.3);
    }
    .kid-profile-item.active-profile .text-muted {
        color: #E3EFFF !important;
    }

    /* Playful Progress Bars */
    .fun-progress-track {
        height: 16px;
        background-color: #EEF2F9;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    .fun-progress-bar {
        height: 100%;
        border-radius: 10px;
        background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }

    /* Daily Log Chat Bubbles */
    .log-bubble {
        background: #F8FBFF;
        border: 2px solid #EAF0FA;
        border-radius: 20px;
        border-bottom-left-radius: 4px;
        padding: 16px;
        position: relative;
    }
    
    .badge-fun {
        padding: 6px 12px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 12px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--kid-blue);">Welcome back, {{ Auth::user()->name }}! 👋</h2>
        <p class="mb-0" style="color: var(--text-soft); font-size: 1.1rem;">Let's see what your little ones are up to today.</p>
    </div>
    
    @if(isset($activeChild) && isset($myChildren))
    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn border-0 rounded-pill px-4 py-2 fw-bold dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="background: var(--kid-blue); color: white; box-shadow: 0 4px 12px rgba(92, 141, 246, 0.3);">
                <i class="fa fa-child fs-5"></i>
                <span class="ms-1">Viewing: {{ $activeChild->first_name }}</span>
            </button>
            
            <ul class="dropdown-menu shadow-lg border-0 mt-2 p-2" style="border-radius: 20px; min-width: 220px;">
                <li><h6 class="dropdown-header fw-bold" style="color: var(--text-soft);">MY CHILDREN</h6></li>
                @foreach($myChildren as $child)
                    <li>
                        <a class="dropdown-item py-2 px-3 mb-1 rounded-4 {{ $activeChild->id === $child->id ? 'fw-bold' : '' }}" 
                           href="{{ route('guardian.switch-child', $child->id) }}"
                           style="{{ $activeChild->id === $child->id ? 'background: var(--kid-blue-light); color: var(--kid-blue);' : 'color: var(--text-main);' }}">
                            <i class="fa {{ $activeChild->id === $child->id ? 'fa-check-circle text-primary' : 'fa-user text-muted' }} me-2"></i> 
                            {{ $child->first_name }} {{ $child->last_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-box" style="background: white; border: 2px solid var(--kid-blue-light);">
            <div class="stat-icon-wrap" style="background: var(--kid-blue-light); color: var(--kid-blue);">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <h3 class="fw-bold m-0" style="color: var(--kid-blue); font-size: 2rem;">{{ $myChildren->count() ?? 0 }}</h3>
                <p class="m-0 fw-bold" style="color: var(--text-soft);">Enrolled Children</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box" style="background: white; border: 2px solid var(--kid-purple-light);">
            <div class="stat-icon-wrap" style="background: var(--kid-purple-light); color: var(--kid-purple);">
                <i class="fa fa-star"></i>
            </div>
            <div>
                <h3 class="fw-bold m-0" style="color: var(--kid-purple); font-size: 2rem;">{{ $activeGoals->count() ?? 0 }}</h3> 
                <p class="m-0 fw-bold" style="color: var(--text-soft);">Active IEP Goals</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box" style="background: white; border: 2px solid var(--kid-green-light);">
            <div class="stat-icon-wrap" style="background: var(--kid-green-light); color: var(--kid-green);">
                <i class="fa fa-certificate"></i>
            </div>
            <div>
                <h3 class="fw-bold m-0" style="color: var(--kid-green); font-size: 2rem;">{{ $recentReports->count() ?? 0 }}</h3>
                <p class="m-0 fw-bold" style="color: var(--text-soft);">Official Reports</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        
        <div class="fun-card">
            <div class="fun-card-header bg-white">
                <div style="width: 40px; height: 40px; background: var(--kid-yellow-light); color: var(--kid-yellow); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa fa-id-badge"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0" style="color: var(--text-main);">Student Profiles</h5>
                    <small style="color: var(--text-soft);">Select a child to view their progress</small>
                </div>
            </div>
            
            <div class="p-4 d-flex flex-column gap-2">
                @forelse($myChildren as $child)
                    @php
                        $isActive = isset($activeChild) && $activeChild->id === $child->id;
                    @endphp
                    
                    <a href="{{ route('guardian.switch-child', $child->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="d-flex align-items-center gap-3 kid-profile-item {{ $isActive ? 'active-profile' : '' }}">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" 
                                 style="width: 60px; height: 60px; background: {{ $isActive ? '#ffffff' : 'var(--kid-yellow)' }}; color: {{ $isActive ? 'var(--kid-blue)' : '#ffffff' }}; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                {{ strtoupper(substr($child->first_name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="m-0 fw-bold {{ $isActive ? 'text-white' : 'text-dark' }}">{{ $child->first_name }} {{ $child->last_name }}</h5>
                                <div class="d-flex gap-3 small mt-1 {{ $isActive ? 'text-white' : 'text-muted' }}" style="opacity: 0.9;">
                                    <span><i class="fa fa-birthday-cake me-1"></i> {{ \Carbon\Carbon::parse($child->date_of_birth)->age }} yrs old</span>
                                    <span><i class="fa fa-chalkboard-teacher me-1"></i> {{ $child->teacher->name ?? 'Unassigned' }}</span>
                                </div>
                            </div>
                            @if($isActive) 
                                <div style="background: rgba(255,255,255,0.2); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa fa-chevron-right text-white"></i>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4" style="color: var(--text-soft);">
                        <i class="fa fa-ghost fs-1 mb-2 opacity-25"></i>
                        <p class="mb-0 fw-bold">No children linked yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <div class="col-lg-7">
        
        <div class="fun-card mb-4">
            <div class="fun-card-header" style="background: var(--kid-purple-light);">
                <div style="width: 40px; height: 40px; background: white; color: var(--kid-purple); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa fa-rocket"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0" style="color: var(--kid-purple);">{{ $activeChild->first_name ?? '' }}'s Goal Progress</h5>
                    <small style="color: var(--text-main); opacity: 0.7;">Watch them grow!</small>
                </div>
            </div>
            
            <div class="p-4 d-flex flex-column gap-4 bg-white">
                @forelse($activeGoals as $goal)
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge-fun" style="background: var(--kid-purple-light); color: var(--kid-purple);">{{ $goal->domain }}</span>
                            <span class="fw-bold fs-5" style="color: var(--kid-purple);">{{ $goal->progress_percentage }}%</span>
                        </div>
                        
                        <p class="mb-2" style="color: var(--text-main); font-weight: 600; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $goal->goal_description }}
                        </p>
                        
                        <div class="fun-progress-track">
                            <div class="fun-progress-bar" style="width: {{ $goal->progress_percentage }}%; background-color: var(--kid-purple);"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color: var(--text-soft);">
                        <i class="fa fa-moon fs-1 mb-2 opacity-25"></i>
                        <p class="mb-0 fw-bold">No active goals to track right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="fun-card">
            <div class="fun-card-header" style="background: var(--kid-green-light);">
                <div style="width: 40px; height: 40px; background: white; color: var(--kid-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa fa-book-open"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0" style="color: var(--kid-green);">Teacher Updates</h5>
                    <small style="color: var(--text-main); opacity: 0.7;">Recent notes from the classroom</small>
                </div>
            </div>
            
            <div class="p-4 bg-white d-flex flex-column gap-3">
                @forelse($recentLogs as $log)
                    <div class="log-bubble">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold m-0" style="color: var(--kid-blue);">{{ \Carbon\Carbon::parse($log->log_date)->format('l, F j') }}</h6>
                            <span class="badge-fun bg-white" style="color: var(--text-soft); border: 1px solid #EAF0FA;">
                                <i class="fa fa-clock me-1"></i> {{ \Carbon\Carbon::parse($log->log_date)->diffForHumans() }}
                            </span>
                        </div>
                        <p class="mb-2" style="color: var(--text-main); font-weight: 500; line-height: 1.6;">{{ $log->notes }}</p>
                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top" style="border-color: #EAF0FA !important;">
                            <div style="width: 24px; height: 24px; background: var(--kid-yellow); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">
                                {{ substr($log->teacher->name ?? 'T', 0, 1) }}
                            </div>
                            <small class="fw-bold" style="color: var(--text-soft);">Teacher {{ $log->teacher->name ?? 'Staff' }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 border border-2 border-dashed rounded-4" style="border-color: var(--kid-blue-light) !important; color: var(--text-soft);">
                        <p class="mb-0 fw-bold">No recent updates from the teacher.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection