@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-[#1E2657] mb-1 m-0">Notifications</h3>
            <p class="text-sm font-medium text-[#6B7086] m-0">Recent classroom updates, IEP milestones, and center announcements.</p>
        </div>
        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 rounded-xl text-[#1E2657] text-[13px] font-extrabold hover:bg-slate-50 transition-colors shadow-sm focus:outline-none cursor-pointer">
                <i class="fa fa-check-double mr-2 text-[#3B82F6]"></i> Mark all as read
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden flex flex-col">
        <div class="flex flex-col divide-y divide-black/10">
            
            @forelse($notifications as $notif)
                @php
                    $isRead = $notif->read_at !== null;
                    $message = $notif->data['message'] ?? ($notif->data['title'] ?? 'New Notification');
                    $url = $notif->data['url'] ?? '#';
                @endphp
                
                <div class="p-5 sm:p-6 transition-colors flex items-start gap-4 {{ $isRead ? 'bg-white hover:bg-slate-50' : 'bg-blue-50/40 hover:bg-blue-50/70 relative' }}">
                    @if(!$isRead)
                        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#3B82F6]"></div>
                    @endif

                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 shadow-sm {{ $isRead ? 'bg-[#F4F6FA] text-[#6B7086] border border-black/10' : 'bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20' }}">
                        <i class="fa-solid fa-bell"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-[14px] sm:text-[15px] font-extrabold text-[#1E2657] mb-1.5 leading-snug">{{ $message }}</p>
                        
                        <div class="flex items-center gap-4 flex-wrap mt-2">
                            @if($url && $url !== '#')
                                <a href="{{ route('notifications.read', $notif->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-[#3B82F6] text-white text-[12px] font-extrabold rounded-lg hover:bg-blue-600 transition-colors shadow-sm decoration-transparent">
                                    View Details <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
                                </a>
                            @endif

                            <span class="text-[11px] font-medium text-[#9CA0AF] flex items-center">
                                <i class="fa-regular fa-clock mr-1.5"></i> {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-[#9CA0AF] flex flex-col items-center">
                    <div class="w-14 h-14 bg-[#F4F6FA] border border-black/5 rounded-full flex items-center justify-center text-2xl mb-3 text-[#9CA0AF]">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <h5 class="text-[16px] font-extrabold text-[#1E2657] mb-1">No Notifications Yet</h5>
                    <p class="text-[13px] font-medium m-0">When teachers or admins post updates, they will appear here.</p>
                </div>
            @endforelse

        </div>
    </div>

    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@endsection