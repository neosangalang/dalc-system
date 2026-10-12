@extends('layouts.app')

@section('title', 'All Announcements')

@section('content')
<!-- Main Container -->
<div class="w-full">
    <div class="max-w-[1200px] mx-auto space-y-10">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div class="flex flex-col gap-2">
                <span class="text-[#8B5CF6] text-[11px] font-extrabold tracking-widest uppercase mb-1 block">Teacher Portal</span>
                <h1 class="text-[#1E2657] text-3xl sm:text-4xl font-extrabold tracking-tight mb-2">Center Announcements</h1>
                <p class="text-[#6B7086] text-sm font-medium">Stay updated with the latest news and alerts from the center.</p>
            </div>
        </header>

        <!-- FULL ANNOUNCEMENTS FEED -->
        <section>
            <div class="bg-white border border-black/15 rounded-2xl shadow-sm p-6 md:p-8 flex flex-col gap-6">
                
                @forelse($announcements as $announcement)
                    <div class="flex flex-col gap-2">
                        <!-- Announcement Header -->
                        <div class="flex items-center gap-4 mb-1">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] text-white shadow-sm flex items-center justify-center text-[14px] font-bold shrink-0">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div class="flex flex-col">
                                <h4 class="text-[15px] font-extrabold text-[#1E2657]">{{ $announcement->title }}</h4>
                                <span class="text-[11px] font-medium text-[#9CA3AF]">
                                    <i class="fa-regular fa-clock mr-1"></i> Posted {{ \Carbon\Carbon::parse($announcement->created_at)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        <!-- Content Bubble -->
                        <div class="bg-[#F4F6FA] border border-black/5 rounded-2xl rounded-tl-sm p-5 mt-2 ml-[56px]">
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

                            @if($announcement->image_path)
                                <div class="mb-5 rounded-xl overflow-hidden shadow-sm border border-black/5">
                                    <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="{{ $announcement->title }}" class="w-full h-auto max-h-[450px] object-cover object-center">
                                </div>
                            @endif
                            <p class="text-[14px] font-medium text-[#4B5563] leading-relaxed m-0 whitespace-pre-line">{{ $announcement->content }}</p>

                            <!-- FACEBOOK-STYLE COMMENTS -->
                            @include('partials.announcement-comments', ['announcement' => $announcement])
                        </div>
                    </div>

                    @if (!$loop->last)
                        <div class="w-full h-px bg-black/5 my-4 ml-[56px] w-[calc(100%-56px)]"></div>
                    @endif

                @empty
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