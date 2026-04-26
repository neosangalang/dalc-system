@extends('layouts.app')

@section('title', 'Feedback & Discussion')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Teacher Discussion</h3>
        <p class="text-muted">Communicate directly with your child's teachers.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="data-card d-flex flex-column" style="height: 600px; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            
            <div class="data-card-header border-bottom p-3 bg-white">
                <h5 class="fw-bold m-0"><i class="fa fa-comments text-primary me-2"></i> Conversation History</h5>
            </div>
            
            <div class="chat-history p-4 flex-grow-1" style="overflow-y: auto; background-color: #f8f9fa;">
                
                {{-- NOTE: Your Controller must pass a $comments variable to this view for this loop to work! --}}
                @forelse($comments ?? [] as $comment)
                    <div class="mb-3 {{ $comment->user->role == 'guardian' ? 'text-end' : 'text-start' }}">
                        
                        <div class="d-inline-block p-3 rounded shadow-sm" style="max-width: 75%; text-align: left; {{ $comment->user->role == 'guardian' ? 'background-color: #0d6efd; color: white;' : 'background-color: white; border: 1px solid #dee2e6;' }}">
                            <strong style="font-size: 0.85rem; {{ $comment->user->role == 'guardian' ? 'color: #e0e0e0;' : 'color: #6c757d;' }}">
                                {{ $comment->user->name }}
                            </strong><br>
                            {{ $comment->body }}
                        </div>
                        
                        <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                            {{ $comment->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted mt-5">
                        <i class="fa fa-comment-dots fa-3x mb-3 text-light"></i>
                        <p>No messages yet. Send a message to start the conversation!</p>
                    </div>
                @endforelse

            </div>

            <div class="border-top p-3 bg-white">
                <form action="{{ route('guardian.feedback.store') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="student_id" value="1"> 

                    <div class="input-group">
                        <input type="text" name="body" class="form-control border-end-0" placeholder="Type your message here..." required style="border-radius: 20px 0 0 20px;">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 0 20px 20px 0;">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection