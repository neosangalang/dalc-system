@extends('layouts.app')

@section('title', 'Feedback & Discussion')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1 m-0">Teacher Discussion</h3>
            <p class="text-sm font-medium text-slate-500 m-0">Communicate directly with your child's teachers.</p>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border-[3px] border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-600 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border-[3px] border-slate-300 overflow-hidden flex flex-col h-[600px]">
        
        <div class="px-6 py-5 border-b-[3px] border-slate-300 bg-white shrink-0">
            <h5 class="font-extrabold text-slate-800 m-0 flex items-center text-lg">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border-2 border-indigo-200 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                    <i class="fa fa-comments"></i>
                </div>
                Conversation History
            </h5>
        </div>
        
        <div class="flex-1 p-6 bg-slate-50 overflow-y-auto space-y-6 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-slate-400">
            
            {{-- NOTE: Your Controller must pass a $comments variable to this view for this loop to work! --}}
            @forelse($comments ?? [] as $comment)
                <div class="flex flex-col {{ $comment->user->role == 'guardian' ? 'items-end' : 'items-start' }}">
                    
                    <div class="max-w-[85%] md:max-w-[70%] p-4 shadow-sm border-2 {{ $comment->user->role == 'guardian' ? 'bg-indigo-600 border-indigo-800 text-white rounded-2xl rounded-tr-sm' : 'bg-white border-slate-300 text-slate-700 rounded-2xl rounded-tl-sm' }}">
                        <strong class="text-[11px] tracking-wider uppercase block mb-1.5 {{ $comment->user->role == 'guardian' ? 'text-indigo-200' : 'text-slate-500' }}">
                            {{ $comment->user->name }}
                        </strong>
                        <p class="m-0 text-sm leading-relaxed whitespace-pre-line">
                            {{ $comment->body }}
                        </p>
                    </div>
                    
                    <div class="text-[11px] font-bold text-slate-500 mt-2 mx-1">
                        {{ $comment->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-slate-500 border-[3px] border-dashed border-slate-300 rounded-2xl m-2 bg-slate-50/50">
                    <i class="fa fa-comment-dots text-6xl mb-4 text-slate-300"></i>
                    <p class="font-bold text-sm m-0">No messages yet. Send a message to start the conversation!</p>
                </div>
            @endforelse
            
        </div>

        <div class="p-4 bg-white border-t-[3px] border-slate-300 shrink-0">
            <form action="{{ route('guardian.feedback.store') }}" method="POST" class="m-0">
                @csrf
                
                {{-- Ensure this hidden input reflects your dynamic logic if needed --}}
                <input type="hidden" name="student_id" value="1"> 

                <div class="flex shadow-sm rounded-full overflow-hidden border-[3px] border-slate-300 bg-slate-50 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-200 transition-all">
                    <input type="text" name="body" class="w-full border-0 px-6 py-3 text-sm focus:ring-0 text-slate-700 placeholder:text-slate-500 bg-transparent" placeholder="Type your message here..." required autocomplete="off">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 border-l-[3px] border-slate-300 hover:bg-indigo-700 text-white text-sm font-bold transition-colors shrink-0 focus:outline-none">
                        <i class="fa fa-paper-plane mr-1.5"></i> Send
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection