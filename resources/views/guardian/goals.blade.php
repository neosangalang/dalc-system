@extends('layouts.app')

@section('title', 'Student Goals')

@section('content')

<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Main Container -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <!-- HEADER SECTION -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 gap-4 w-full">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1E2657] mb-1 m-0 tracking-tight">Learning Goals</h1>
            <p class="text-[13px] sm:text-sm font-medium text-[#6B7086] m-0 leading-tight">Track your child's progress across all educational domains.</p>
        </div>
        
        @if(isset($activeChild) && isset($myChildren))
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div x-data="{ open: false }" class="relative w-full md:w-auto">
                <!-- Dropdown Button -->
                <button @click="open = !open" @click.away="open = false" class="w-full md:w-auto inline-flex items-center justify-between md:justify-start gap-2 px-5 py-2.5 bg-white border border-black/15 rounded-full shadow-sm text-[#3B82F6] font-extrabold text-[13px] hover:bg-slate-50 transition-all focus:outline-none">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-[#3B82F6] text-white flex items-center justify-center text-[10px] shrink-0">
                            <i class="fa fa-user"></i>
                        </div>
                        <span>Viewing: {{ $activeChild->first_name }}</span>
                    </div>
                    <i class="fa fa-chevron-down ml-1 text-xs text-[#9CA0AF] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <ul x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-full md:w-56 bg-white rounded-2xl shadow-lg border border-black/15 py-2 z-50 list-none pl-0 overflow-hidden">
                    <li><h6 class="px-5 py-2 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest m-0">My Children</h6></li>
                    
                    @foreach($myChildren as $child)
                        <li>
                            <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-5 py-2.5 text-[13px] font-extrabold transition-colors decoration-transparent {{ $activeChild->id === $child->id ? 'bg-[#3B82F6]/10 text-[#3B82F6]' : 'text-[#6B7086] hover:bg-[#F4F6FA] hover:text-[#1E2657]' }}">
                                <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                @if($activeChild->id === $child->id)
                                    <i class="fa fa-check text-[#3B82F6] text-xs mt-0.5"></i>
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
        <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden border-t-[6px] border-t-[#8B5CF6] mb-8">
            
            <!-- MAIN CARD HEADER -->
            <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-black/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4 bg-white">
                <h5 class="font-extrabold text-[#1E2657] m-0 text-base sm:text-lg flex items-center leading-tight">
                    <i class="fa fa-chart-pie text-[#8B5CF6] mr-2.5"></i> {{ $activeChild->first_name }}'s IEP Goal Progress
                </h5>
                
                <a href="{{ route('guardian.goals.download-iep', $activeChild->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-[#EF4444] text-white text-[13px] font-extrabold rounded-xl hover:bg-red-600 transition-colors shadow-sm decoration-transparent uppercase tracking-widest">
                    <i class="fa fa-file-pdf mr-2"></i> Download Official IEP
                </a>
            </div>
            
            <div class="p-5 sm:p-6 grid grid-cols-1 gap-5 sm:gap-6 bg-[#F4F6FA]/50">
                @forelse($activeChild->iepGoals as $goal)
                    
                    @php
                        // Check if the teacher has replied and it is unread by the guardian
                        $goalUnreadCount = $goal->comments->filter(function($comment) {
                            return $comment->user_id != Auth::id() && !$comment->is_guardian_read;
                        })->count();
                    @endphp

                    <!-- GOAL CARD: Reactive Alpine State Wrapper -->
                    <div x-data="{ 
                            goalUnread: {{ $goalUnreadCount > 0 ? 'true' : 'false' }},
                            markAsRead() {
                                if (this.goalUnread) {
                                    this.goalUnread = false;
                                    fetch('{{ route('guardian.goals.mark-read', $goal->id) }}', {
                                        method: 'POST',
                                        headers: { 
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        }
                                    }).catch(err => console.error('Error marking as read', err));
                                }
                            }
                         }" 
                         class="bg-white rounded-2xl border transition-all duration-300 shadow-sm overflow-hidden hover:shadow-md"
                         :class="goalUnread ? 'border-red-400 ring-2 ring-red-400/20' : 'border-black/15'">
                        
                        <!-- GOAL HEADER -->
                        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-black/10 bg-white flex flex-wrap justify-between items-center gap-2 sm:gap-0 transition-colors duration-500"
                             :class="goalUnread ? 'bg-red-50/30' : 'bg-white'">
                            
                            <h6 class="m-0 font-extrabold text-[#1E2657] flex items-center gap-3 text-[14px] sm:text-base">
                                <div class="w-8 h-8 bg-[#F4F6FA] text-[#8B5CF6] border border-black/5 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fa fa-layer-group text-[13px]"></i>
                                </div>
                                {{ $goal->domain }}
                                
                                <!-- Reactive New Reply Badge -->
                                <span x-show="goalUnread" 
                                      x-transition.opacity.duration.500ms
                                      class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-500 text-white font-extrabold text-[9px] shadow-sm uppercase tracking-widest animate-pulse ml-1">
                                    <i class="fa fa-bell mr-1.5"></i> New Reply
                                </span>
                            </h6>
                            
                            @if($goal->status == 'achieved')
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#10B981]/10 text-[#10B981] font-extrabold text-[10px] shadow-sm border border-[#10B981]/20 uppercase tracking-widest">
                                    <i class="fa fa-check-circle mr-1.5"></i> Achieved
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-[#3B82F6]/10 text-[#3B82F6] font-extrabold text-[10px] shadow-sm border border-[#3B82F6]/20 uppercase tracking-widest">
                                    <i class="fa fa-spinner fa-spin mr-1.5"></i> In Progress
                                </span>
                            @endif
                        </div>

                        <!-- GOAL BODY -->
                        <div class="p-5 sm:p-6">
                            @php
                                $text = $goal->goal_description ?? '';
                                
                                // Split multiple objectives if separated by AI block dividers
                                $sections = explode('------------------------', $text);
                                $parsedHtml = '';

                                foreach ($sections as $section) {
                                    $section = trim($section);
                                    if (empty($section)) continue;

                                    $objText = '';
                                    $actText = '';

                                    if (strpos($section, 'ACTIVITIES:') !== false) {
                                        $subParts = explode('ACTIVITIES:', $section);
                                        $objText = trim(str_replace('OBJECTIVE:', '', $subParts[0]));
                                        $actText = trim($subParts[1]);
                                    } else {
                                        $objText = trim(str_replace('OBJECTIVE:', '', $section));
                                    }

                                    // Build objective card box
                                    $parsedHtml .= '<div class="bg-white border-y border-r border-l-4 border-black/15 border-l-[#8B5CF6] rounded-r-xl p-5 mb-4 shadow-sm">';
                                    $parsedHtml .= '<h6 class="uppercase text-[#8B5CF6] font-extrabold text-[10px] tracking-widest mb-2 flex items-center m-0"><i class="fa fa-bullseye mr-1.5 text-[#8B5CF6]"></i> Objective</h6>';
                                    $parsedHtml .= '<p class="m-0 text-[#1E2657] text-[14px] font-extrabold leading-relaxed">' . htmlspecialchars($objText) . '</p>';
                                    $parsedHtml .= '</div>';

                                    // Build activity checklist box for this objective
                                    $parsedHtml .= '<div class="mb-6">';
                                    $parsedHtml .= '<h6 class="uppercase text-[#6B7086] font-extrabold text-[10px] tracking-widest mb-3 px-1 flex items-center m-0"><i class="fa fa-list-check mr-1.5 text-[#9CA0AF]"></i> Activities / Interventions</h6>';
                                    $parsedHtml .= '<div class="max-h-[300px] overflow-y-auto pr-2 border border-black/15 bg-slate-50/30 rounded-2xl p-3 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-[#D1D5DB] [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-[#9CA0AF]">';

                                    if ($actText) {
                                        $actText = str_replace(["\r\n", "\r"], "\n", $actText);
                                        $actText = preg_replace('/(?<=\S)\s*(\[[xX\s]\])/', "\n$1", $actText);
                                        $lines = explode("\n", trim($actText));

                                        foreach ($lines as $line) {
                                            $line = trim($line);
                                            if (empty($line)) continue;

                                            if (str_starts_with($line, '- ')) {
                                                $line = preg_replace('/^- /', '[ ] ', $line);
                                            }

                                            // Unchecked
                                            if (preg_match('/^\[[\s]*\]/', $line)) {
                                                $itemText = trim(preg_replace('/^\[[\s]*\]/', '', $line));
                                                $parsedHtml .= '<div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl border border-black/15 bg-white"><div class="w-4 h-4 bg-white border border-black/20 rounded shrink-0 mt-0.5 shadow-sm"></div><span class="text-[13px] font-medium text-[#1E2657] leading-tight pt-[1px]">' . htmlspecialchars($itemText) . '</span></div>';
                                            } 
                                            // Checked
                                            elseif (preg_match('/^\[[xX]\]/', $line)) {
                                                $itemText = trim(preg_replace('/^\[[xX]\]/', '', $line));
                                                $parsedHtml .= '<div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl bg-[#F4F6FA] border border-black/5"><div class="w-4 h-4 bg-[#10B981] border border-[#10B981] rounded shrink-0 mt-0.5 flex items-center justify-center shadow-sm"><i class="fa fa-check text-white text-[10px]"></i></div><span class="text-[13px] font-medium text-[#9CA0AF] line-through leading-tight pt-[1px]">' . htmlspecialchars($itemText) . '</span></div>';
                                            }
                                            else {
                                                $parsedHtml .= '<div class="mt-3 mb-2 px-1 text-[11px] font-extrabold text-[#3B82F6] uppercase tracking-widest">' . htmlspecialchars($line) . '</div>';
                                            }
                                        }
                                    } else {
                                        $parsedHtml .= '<p class="text-[#9CA0AF] text-[13px] font-medium mb-0 px-3">No specific activities listed.</p>';
                                    }

                                    $parsedHtml .= '</div></div>';
                                }
                            @endphp

                            {!! $parsedHtml !!}

                            <div class="bg-white p-5 border border-black/15 rounded-2xl mt-6 shadow-sm">
                                <div class="flex justify-between items-end mb-3">
                                    <span class="font-extrabold text-[#1E2657] uppercase text-[10px] tracking-widest">Mastery Level</span>
                                    <span class="font-extrabold text-lg leading-none {{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#8B5CF6]' }}">{{ $goal->progress_percentage }}%</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-[#E8E5DC] shadow-inner overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $goal->progress_percentage == 100 ? 'bg-[#10B981]' : 'bg-gradient-to-r from-[#8B5CF6] to-[#A78BFA] relative' }}" style="width: {{ $goal->progress_percentage }}%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GOAL COMMENTS: Interactive Read Trigger on Hover/Tap -->
                        <div class="border-t border-black/10 p-5 sm:p-6 transition-colors duration-700 rounded-b-2xl cursor-pointer"
                             :class="goalUnread ? 'bg-red-50/70 hover:bg-[#F4F6FA]' : 'bg-[#F4F6FA]'"
                             @mouseenter.once="markAsRead()"
                             @touchstart.once="markAsRead()">
                            
                            <div class="flex justify-between items-center mb-4">
                                <h6 class="font-extrabold transition-colors duration-500 flex items-center m-0 text-[11px] uppercase tracking-widest"
                                    :class="goalUnread ? 'text-red-500' : 'text-[#6B7086]'">
                                    <i class="fa fa-comments mr-2" :class="goalUnread ? 'text-red-500 animate-bounce' : 'text-[#9CA0AF]'"></i> Discussion & Notes
                                </h6>
                                
                                <span x-show="goalUnread" x-transition class="text-[9px] font-extrabold text-red-500 uppercase tracking-widest">
                                    Hover here to clear notification
                                </span>
                            </div>

                            @include('components.comment-thread', ['item' => $goal])
                        </div>

                    </div>
                @empty
                    <div class="w-full text-center py-12 bg-white rounded-2xl border border-black/15 shadow-sm">
                        <i class="fa fa-clipboard-list text-5xl mb-4 text-[#E8E5DC]"></i>
                        <p class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No IEP Goals Assigned Yet</p>
                        <p class="text-[13px] font-medium text-[#6B7086] m-0">Goals will appear here once the teacher establishes them.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="bg-sky-50 border border-sky-200 text-sky-800 px-5 py-4 rounded-xl shadow-sm flex items-center gap-3">
            <i class="fa fa-info-circle text-sky-500 text-xl shrink-0"></i> 
            <span class="text-[13px] font-extrabold leading-snug">No children are currently linked to your account.</span>
        </div>
    @endif

</div>

@endsection