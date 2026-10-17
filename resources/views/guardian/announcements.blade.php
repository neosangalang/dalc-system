@extends('layouts.app')

@section('title', 'All Announcements')

@section('content')
<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2">
                <span class="text-[#8B5CF6] text-[11px] font-extrabold tracking-widest uppercase mb-1 block">Guardian Portal</span>
                <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Center Announcements</h1>
                <p class="text-[#6B7086] text-sm font-medium">Stay updated with the latest news and alerts from the center.</p>
            </div>
        </header>

        <!-- FULL ANNOUNCEMENTS FEED -->
        <section>
            <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-4 sm:p-6 md:p-8 flex flex-col gap-6">
                
                @forelse($announcements as $announcement)
                    <div class="flex flex-col gap-2">
                        <!-- Announcement Header -->
                        <div class="flex items-center gap-3 sm:gap-4 mb-1">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] text-white shadow-sm flex items-center justify-center text-[14px] font-bold shrink-0">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h4 class="text-[14px] sm:text-[15px] font-extrabold text-[#1E2657] truncate sm:whitespace-normal">{{ $announcement->title }}</h4>
                                <span class="text-[10px] sm:text-[11px] font-medium text-[#9CA3AF]">
                                    <i class="fa-regular fa-clock mr-1"></i> Posted {{ \Carbon\Carbon::parse($announcement->created_at)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        <!-- Content Bubble (Full width on mobile, indented on tablet/desktop) -->
                        <div class="bg-[#F4F6FA] border border-black/5 rounded-2xl sm:rounded-tl-sm p-4 sm:p-5 mt-1 sm:mt-2 ml-0 sm:ml-[56px]">
                            
                            <!-- Dynamic Category Badge -->
                            @php
                                $badgeIcon = match($announcement->type ?? 'Center Update') {
                                    'Class Suspension' => 'fa-triangle-exclamation',
                                    'Holiday'          => 'fa-calendar-star',
                                    'Event'            => 'fa-calendar-check',
                                    default            => 'fa-bullhorn',
                                };
                            @endphp
                            <span class="inline-flex items-center bg-[#1E2657] text-white px-2.5 py-1 rounded-md text-[10px] font-black tracking-widest uppercase mb-4 shadow-sm">
                                <i class="fa-solid {{ $badgeIcon }} mr-1.5 text-[#FFD700]"></i> {{ $announcement->type ?? 'Center Update' }}
                            </span>

                            <!-- Attached Image (Clean, uncropped container with click-to-view) -->
                            @if($announcement->image_path)
                                <div class="mb-5 rounded-2xl overflow-hidden bg-slate-900/5 border border-black/10 p-2 sm:p-3 flex items-center justify-center">
                                    <a href="{{ asset('storage/' . $announcement->image_path) }}" target="_blank" class="block group relative overflow-hidden rounded-xl w-full text-center">
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" class="max-h-[520px] w-auto max-w-full object-contain mx-auto rounded-lg shadow-sm transition-transform duration-300 group-hover:scale-[1.01]">
                                        <div class="absolute bottom-3 right-3 bg-black/75 hover:bg-black/90 text-white text-[11px] font-extrabold px-3 py-1.5 rounded-lg backdrop-blur-sm shadow flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition">
                                            <i class="fa-solid fa-expand text-xs"></i> View Full Size
                                        </div>
                                    </a>
                                </div>
                            @endif

                            <p class="text-[13px] sm:text-[14px] font-medium text-[#4B5563] leading-relaxed m-0 whitespace-pre-line">{{ $announcement->content }}</p>

                            <!-- FACEBOOK-STYLE COMMENTS -->
                            @include('partials.announcement-comments', ['announcement' => $announcement])
                        </div>
                    </div>

                    <!-- Divider -->
                    @if (!$loop->last)
                        <div class="w-full h-px bg-black/5 my-4 ml-0 sm:ml-[56px] w-full sm:w-[calc(100%-56px)]"></div>
                    @endif

                @empty
                    <!-- Empty State -->
                    <div class="text-center py-10 text-[#9CA3AF] flex flex-col items-center">
                        <div class="w-16 h-16 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-[24px] mb-3">
                            <i class="fa-regular fa-bell-slash"></i>
                        </div>
                        <h4 class="text-[16px] font-extrabold text-[#1E2657]">No Announcements Yet</h4>
                        <p class="text-[13px] font-medium mt-1">We'll let you know when the center posts an update.</p>
                    </div>
                @endforelse

            </div>
        </section>

    </div>
</div>
@endsection