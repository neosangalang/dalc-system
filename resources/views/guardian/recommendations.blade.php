@extends('layouts.app')

@section('title', 'Home-Based Recommendations')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1 m-0">Personalized Home Activities</h3>
            <p class="text-sm font-medium text-slate-500 m-0">Dynamic recommendations and daily highlights based on <strong class="text-slate-700">{{ $activeChild->first_name ?? 'your child' }}'s</strong> progress.</p>
        </div>

        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full shadow-sm text-indigo-600 font-bold text-sm hover:bg-slate-50 focus:outline-none transition-colors">
                    <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shrink-0">
                        <i class="fa fa-user"></i>
                    </div>
                    Viewing: {{ $activeChild->first_name }}
                    <i class="fa fa-chevron-down ml-1 text-xs text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <ul x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 list-none pl-0">
                    <li><h6 class="px-4 py-2 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider m-0">My Children</h6></li>
                    
                    @foreach($myChildren as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-2 text-sm font-medium transition-colors decoration-transparent {{ $activeChild->id === $child->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                @if($activeChild->id === $child->id)
                                    <i class="fa fa-check text-white text-xs mt-0.5"></i>
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
    <div class="flex flex-col gap-8">
        @forelse($recentRecommendations as $log)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden border-t-4 border-t-indigo-600">
                
                <div class="px-6 py-5 border-b border-slate-100 bg-white flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h5 class="font-extrabold text-slate-800 m-0 text-lg flex items-center mb-1">
                            <i class="fa fa-star text-amber-400 mr-2"></i> {{ $log->student->first_name }}'s Daily Highlight
                        </h5>
                        <p class="text-xs font-bold text-slate-500 m-0">From the daily log on: {{ \Carbon\Carbon::parse($log->log_date)->format('F j, Y') }}</p>
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs shadow-sm border border-indigo-100">
                        {{ $log->quarter }}
                    </span>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6">
                    @if($log->image_path)
                        <div class="md:col-span-5 text-center">
                            <div class="p-3 bg-slate-50 rounded-xl border border-dashed border-slate-300 h-full flex flex-col items-center justify-center">
                                <img src="{{ route('secure.log-photo', $log->id) }}" alt="Activity Photo" class="max-h-[280px] w-auto mx-auto rounded-lg shadow-sm border border-slate-200 object-cover">
                                <p class="text-slate-500 text-xs font-bold mt-3 mb-1 flex items-center">
                                    <i class="fa fa-camera text-slate-400 mr-1.5"></i> Photo from today's session
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="{{ $log->image_path ? 'md:col-span-7' : 'md:col-span-12' }}">
                        <div class="h-full p-6 flex flex-col justify-center bg-slate-50 rounded-xl border-l-4 border-emerald-500">
                            <h6 class="font-extrabold text-emerald-600 mb-4 flex items-center m-0 text-base">
                                <i class="fa fa-house-user mr-2 text-emerald-500"></i> Tonight's Suggested Activity:
                            </h6>
                            <p class="m-0 text-[15px] leading-relaxed text-slate-700 whitespace-pre-wrap font-medium">{{ $log->home_recommendations }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-5 border-t border-slate-100 bg-slate-50">
                    @include('components.comment-thread', ['item' => $log])
                </div>

            </div>
        @empty
            <div class="w-full text-center py-16 px-6 bg-white rounded-2xl shadow-sm border border-slate-200">
                <div class="w-20 h-20 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-5 text-slate-300 text-4xl">
                    <i class="fa fa-bed"></i>
                </div>
                <h5 class="text-lg font-extrabold text-slate-800 m-0 mb-2">No recommendations right now.</h5>
                <p class="text-sm font-medium text-slate-500 m-0 max-w-md mx-auto">When the teacher logs today's progress, photos and personalized activities for {{ $activeChild->first_name }} will appear here!</p>
            </div>
        @endforelse
    </div>
    @else
        <div class="bg-sky-50 border border-sky-200 text-sky-800 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-500 text-xl"></i> 
            <span class="text-sm font-bold">No children are currently linked to your account.</span>
        </div>
    @endif

</div>

@endsection