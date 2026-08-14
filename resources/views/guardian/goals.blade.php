@extends('layouts.app')

@section('title', 'Student Goals')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="font-['Inter'] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block min-h-[100dvh]">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 sm:mb-8 gap-4 w-full">
        <div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mb-1 m-0 leading-tight">Learning Goals</h3>
            <p class="text-[13px] sm:text-sm font-medium text-slate-500 m-0 leading-tight">Track your child's progress across all educational domains.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div x-data="{ open: false }" class="relative w-full md:w-auto">
                <!-- Dropdown Button: Full width on mobile -->
                <button @click="open = !open" @click.away="open = false" class="w-full md:w-auto inline-flex items-center justify-between md:justify-start gap-2 px-4 py-2.5 sm:py-2 bg-white border-2 border-slate-300 rounded-xl md:rounded-full shadow-sm text-indigo-600 font-bold text-[13px] sm:text-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none transition-colors">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shrink-0">
                            <i class="fa fa-user"></i>
                        </div>
                        <span>Viewing: {{ $activeChild->first_name }}</span>
                    </div>
                    <i class="fa fa-chevron-down ml-1 text-xs text-slate-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Dropdown Menu: Full width on mobile -->
                <ul x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-full md:w-56 bg-white rounded-xl shadow-lg border-2 border-slate-300 py-2 z-50 list-none pl-0">
                    <li><h6 class="px-4 py-2 text-[10px] sm:text-[11px] font-extrabold text-slate-400 uppercase tracking-wider m-0">My Children</h6></li>
                    
                    @foreach($myChildren as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-2.5 sm:py-2 text-[13px] sm:text-sm font-medium transition-colors decoration-transparent {{ $activeChild->id === $child->id ? 'bg-indigo-600 text-white font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
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
        <div class="bg-white rounded-[16px] sm:rounded-2xl shadow-sm border-2 border-slate-300 overflow-hidden border-t-4 border-t-indigo-600 mb-6">
            
            <!-- MAIN CARD HEADER: Stacks on mobile -->
            <div class="px-4 sm:px-6 py-4 sm:py-5 border-b-2 border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
                <h5 class="font-extrabold text-slate-800 m-0 text-base sm:text-lg flex items-center leading-tight">
                    <i class="fa fa-chart-pie text-indigo-600 mr-2.5"></i> {{ $activeChild->first_name }}'s IEP Goal Progress
                </h5>
                
                <a href="{{ route('guardian.goals.download-iep', $activeChild->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 sm:py-2 bg-rose-500 text-white text-[13px] sm:text-sm font-bold rounded-xl border-2 border-rose-600 hover:bg-rose-600 transition-colors shadow-sm decoration-transparent focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">
                    <i class="fa fa-file-pdf mr-2"></i> Download Official IEP
                </a>
            </div>
            
            <div class="p-4 sm:p-6 grid grid-cols-1 gap-4 sm:gap-6 bg-slate-50/50">
                @forelse($activeChild->iepGoals as $goal)
                    <div class="bg-white rounded-[16px] sm:rounded-2xl border-2 border-slate-300 shadow-sm overflow-hidden transition-transform duration-200 hover:-translate-y-1 hover:shadow-md">
                        
                        <!-- GOAL HEADER -->
                        <div class="px-4 sm:px-6 py-3 sm:py-5 border-b-2 border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-2 sm:gap-0">
                            <h6 class="m-0 font-extrabold text-slate-800 flex items-center gap-2 sm:gap-3 text-[14px] sm:text-base">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-indigo-50 text-indigo-600 border-2 border-indigo-200 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fa fa-layer-group text-[12px] sm:text-sm"></i>
                                </div>
                                {{ $goal->domain }}
                            </h6>
                            
                            @if($goal->status == 'achieved')
                                <span class="inline-flex items-center px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px] sm:text-xs shadow-sm border-2 border-emerald-300 uppercase tracking-wide">
                                    <i class="fa fa-check-circle mr-1 sm:mr-1.5"></i> Achieved
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-indigo-50 text-indigo-700 font-bold text-[10px] sm:text-xs shadow-sm border-2 border-indigo-300 uppercase tracking-wide">
                                    <i class="fa fa-spinner fa-spin mr-1 sm:mr-1.5"></i> In Progress
                                </span>
                            @endif
                        </div>

                        <!-- GOAL BODY -->
                        <div class="p-4 sm:p-6">
                            @php
                                $text = $goal->goal_description ?? '';
                                $objectiveText = '';
                                $activitiesText = '';

                                if (strpos($text, 'ACTIVITIES:') !== false) {
                                    $parts = explode('ACTIVITIES:', $text);
                                    $objectiveText = trim(str_replace('OBJECTIVE:', '', $parts[0]));
                                    $activitiesText = trim($parts[1]);
                                } else {
                                    $objectiveText = trim(str_replace('OBJECTIVE:', '', $text));
                                }

                                // Mobile optimized checkbox sizing injected via PHP
                                if ($activitiesText) {
                                    $formattedActivities = str_replace('[]', '<div class="flex items-start gap-2.5 sm:gap-3 mb-2 p-2 rounded-lg transition-colors border-2 border-transparent hover:border-slate-200 hover:bg-slate-50 group"><div class="w-4 h-4 sm:w-5 sm:h-5 bg-slate-100 border-2 border-slate-400 rounded-md shrink-0 mt-0.5 sm:mt-[3px] flex items-center justify-center transition-all group-hover:border-slate-500 group-hover:bg-slate-200 shadow-sm"></div> <span class="text-[13px] sm:text-sm font-medium text-slate-700 leading-relaxed pt-[1px]">', $activitiesText);
                                    $formattedActivities = str_replace(' <div class="flex items-start', '</span></div><div class="flex items-start', $formattedActivities);
                                    
                                    if (strpos($formattedActivities, '<div') !== false) {
                                        $formattedActivities .= '</span></div>';
                                    }
                                } else {
                                    $formattedActivities = '<p class="text-slate-500 text-[13px] sm:text-sm font-medium mb-0 px-3">No specific activities listed.</p>';
                                }
                            @endphp

                            <div class="bg-slate-50 border-y-2 border-r-2 border-l-4 border-slate-300 border-l-indigo-600 rounded-r-xl p-4 sm:p-5 mb-5 sm:mb-6">
                                <h6 class="uppercase text-slate-500 font-extrabold text-[10px] sm:text-[11px] tracking-wider mb-1.5 sm:mb-2 flex items-center m-0">
                                    <i class="fa fa-bullseye mr-1.5 text-indigo-600"></i> Objective
                                </h6>
                                <p class="m-0 text-slate-800 text-[14px] sm:text-[15px] font-semibold leading-relaxed">
                                    {{ $objectiveText }}
                                </p>
                            </div>

                            <div>
                                <h6 class="uppercase text-slate-500 font-extrabold text-[10px] sm:text-[11px] tracking-wider mb-2.5 sm:mb-3 px-1 flex items-center m-0">
                                    <i class="fa fa-list-check mr-1.5 text-indigo-600"></i> Activities / Interventions
                                </h6>
                                <div class="max-h-[300px] overflow-y-auto pr-1 sm:pr-2 border-2 border-slate-200 rounded-xl p-2 sm:p-3 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-slate-50 [&::-webkit-scrollbar-track]:rounded-lg [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-lg hover:[&::-webkit-scrollbar-thumb]:bg-slate-400">
                                    {!! $formattedActivities !!}
                                </div>
                            </div>

                            <div class="bg-white p-4 sm:p-5 border-2 border-slate-300 rounded-xl mt-5 sm:mt-6 shadow-sm">
                                <div class="flex justify-between items-end mb-2 sm:mb-3">
                                    <span class="font-extrabold text-slate-800 uppercase text-[10px] sm:text-xs tracking-wide">Mastery Level</span>
                                    <span class="font-black text-base sm:text-lg leading-none {{ $goal->progress_percentage == 100 ? 'text-emerald-600' : 'text-indigo-600' }}">{{ $goal->progress_percentage }}%</span>
                                </div>
                                <div class="h-3 sm:h-4 rounded-full bg-indigo-50 border-2 border-indigo-100 shadow-inner overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 ease-in-out {{ $goal->progress_percentage == 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-indigo-500 to-purple-500 relative' }}" style="width: {{ $goal->progress_percentage }}%">
                                        @if($goal->progress_percentage != 100 && $goal->progress_percentage > 0)
                                            <div class="absolute inset-0 bg-[linear-gradient(45deg,rgba(255,255,255,.15)_25%,transparent_25%,transparent_50%,rgba(255,255,255,.15)_50%,rgba(255,255,255,.15)_75%,transparent_75%,transparent)] bg-[length:1rem_1rem]"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GOAL COMMENTS -->
                        <div class="bg-slate-50 border-t-2 border-slate-300 p-4 sm:p-6">
                            <h6 class="font-extrabold text-slate-800 mb-3 sm:mb-4 flex items-center m-0 text-[13px] sm:text-base">
                                <i class="fa fa-comments mr-2 text-indigo-600"></i> Discussion & Notes
                            </h6>
                            @include('components.comment-thread', ['item' => $goal])
                        </div>

                    </div>
                @empty
                    <div class="w-full text-center py-10 sm:py-12 bg-white rounded-2xl border-[3px] border-dashed border-slate-400">
                        <i class="fa fa-clipboard-list text-4xl sm:text-5xl mb-3 sm:mb-4 text-indigo-300"></i>
                        <p class="text-[14px] sm:text-base font-extrabold text-slate-700 m-0 mb-1">No IEP Goals Assigned Yet</p>
                        <p class="text-[12px] sm:text-sm font-medium text-slate-500 m-0">Goals will appear here once the teacher establishes them.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="bg-sky-50 border-2 border-sky-300 text-sky-800 px-4 sm:px-5 py-3 sm:py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-600 text-lg sm:text-xl shrink-0"></i> 
            <span class="text-[13px] sm:text-sm font-bold leading-snug">No children are currently linked to your account.</span>
        </div>
    @endif

</div>

@endsection