@extends('layouts.app')

@section('title', 'Student Goals')

@section('content')

<style>
    /* Custom sleek scrollbar for the text areas */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Learning Goals</h3>
        <p class="text-muted">Track your child's progress across all educational domains.</p>
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
    <div class="data-card mb-4 border-top border-4 border-primary">
        
        <div class="data-card-header border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="fw-bold m-0 text-dark">
                <i class="fa fa-chart-pie text-primary me-2"></i> {{ $activeChild->first_name }}'s IEP Goal Progress
            </h5>
            
            <a href="{{ route('guardian.goals.download-iep', $activeChild->id) }}" class="btn btn-danger fw-bold shadow-sm" style="border-radius: 8px;">
                <i class="fa fa-file-pdf me-2"></i> Download Official IEP
            </a>
        </div>
        
        <div class="row g-4">
            @forelse($activeChild->iepGoals as $goal)
                <div class="col-lg-6">
                    <div class="p-4 border rounded shadow-sm bg-white d-flex flex-column h-100" style="transition: transform 0.2s;">
                        
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 13px;">
                                <i class="fa fa-layer-group text-primary me-1"></i> {{ $goal->domain }}
                            </span>
                            @if($goal->status == 'achieved')
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2"><i class="fa fa-check-circle me-1"></i> Achieved</span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2"><i class="fa fa-spinner fa-spin me-1"></i> In Progress</span>
                            @endif
                        </div>
                        
                        <div class="custom-scrollbar flex-grow-1 mb-4 pe-3" style="max-height: 220px; overflow-y: auto; font-size: 0.95rem; line-height: 1.7; color: #475569;">
                            @php
                                // Magic Text Cleaner: Turns raw AI text into beautiful HTML
                                $cleanText = $goal->goal_description;
                                
                                // Make 'OBJECTIVE:' a bold header with a target icon
                                $cleanText = preg_replace('/(OBJECTIVE:)/', '<strong class="text-dark d-block mt-2 mb-1" style="font-size:14px;"><i class="fa fa-bullseye text-primary me-1"></i> $1</strong>', $cleanText);
                                
                                // Make 'ACTIVITIES:' a bold header with a task icon
                                $cleanText = preg_replace('/(ACTIVITIES:)/', '<strong class="text-dark d-block mt-3 mb-1" style="font-size:14px;"><i class="fa fa-tasks text-secondary me-1"></i> $1</strong>', $cleanText);
                                
                                // Turn those dashed lines into clean HTML dividers
                                $cleanText = preg_replace('/-{5,}/', '<hr class="my-3 opacity-25">', $cleanText);
                            @endphp
                            
                            {!! $cleanText !!}
                        </div>
                        
                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Mastery Level</span>
                                <span class="small fw-bold" style="color: {{ $goal->progress_percentage == 100 ? 'var(--success)' : 'var(--primary)' }}; font-size: 14px;">{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 10px; background-color: #EEF1FF; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                <div class="progress-bar progress-bar-striped {{ $goal->progress_percentage == 100 ? 'bg-success' : 'progress-bar-animated bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4">
                            @include('components.comment-thread', ['item' => $goal])
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted bg-light rounded" style="border: 2px dashed #ced4da;">
                    <i class="fa fa-clipboard-list fs-1 mb-3 opacity-50 text-primary"></i>
                    <p class="mb-0 fw-bold">No IEP Goals Assigned Yet</p>
                    <small>Goals will appear here once the teacher establishes them.</small>
                </div>
            @endforelse
        </div>
    </div>
@else
    <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
        <i class="fa fa-info-circle me-2"></i> No children are currently linked to your account.
    </div>
@endif

@endsection