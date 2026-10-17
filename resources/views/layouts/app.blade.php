<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>@yield('title', 'System') - Dream Achievers Learning Center PH</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ddd8ca; border-radius: 10px; }

        /* Mobile-first usability enhancements */
        html {
            -webkit-tap-highlight-color: transparent;
            scroll-behavior: smooth;
        }

        /* Prevent iOS Safari auto-zoom on input focus by enforcing 16px font-size on mobile */
        @media (max-width: 640px) {
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            input[type="date"],
            input[type="search"],
            select,
            textarea {
                font-size: 16px !important;
            }
        }

        .touch-press:active {
            transform: scale(0.97);
        }
    </style>
</head>

<body x-data="{ mobileDrawerOpen: false }" class="font-['Nunito'] bg-gradient-to-br from-[#F4F1EA] via-[#EDF0F8] to-[#F4F1EA] text-[#2C2F45] antialiased min-h-screen bg-fixed overflow-x-hidden">

    <!-- ========================================== -->
    <!-- 1. TOP HEADER NAVIGATION                   -->
    <!-- ========================================== -->
    <header class="sticky top-0 z-50 h-16 bg-white/80 backdrop-blur-lg border-b border-[#1E2657]/20 flex items-center justify-between px-3 sm:px-6 lg:px-8 shadow-sm">

        <!-- Logo, Brand & Mobile Menu Button -->
        <div class="flex items-center gap-2.5 sm:gap-6 min-w-0">
            @if(Auth::check())
                <!-- Mobile Hamburger Button -->
                <button @click="mobileDrawerOpen = true" 
                        type="button" 
                        class="md:hidden relative w-10 h-10 rounded-xl bg-[#F0F3FA] border border-[#1E2657]/20 flex items-center justify-center text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-sm transition active:scale-95" 
                        aria-label="Open Navigation Menu">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
            @endif

            <a href="{{ Auth::check() ? route(strtolower(Auth::user()->role) . '.dashboard') : route('home') }}" class="flex items-center gap-2.5 sm:gap-3 shrink-0 group decoration-transparent">
                <div class="relative w-9 h-9 hidden sm:block">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#12163A] flex items-center justify-center text-white font-black text-sm shadow-md group-hover:scale-105 transition-transform">DA</div>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-[#C4922E] border-2 border-white flex items-center justify-center">
                        <i class="fa-solid fa-star text-white text-[6px]"></i>
                    </div>
                </div>
                <div class="leading-none">
                    <h1 class="text-[#1E2657] font-extrabold text-[14px] sm:text-[15px] m-0 tracking-tight group-hover:text-[#3B82F6] transition-colors">Dream Achievers</h1>
                    <span class="text-[#C4922E] font-bold text-[9px] sm:text-[10px] uppercase tracking-widest">Learning Center PH</span>
                </div>
            </a>

            <div class="h-6 w-px bg-[#E8E5DC] hidden md:block shrink-0"></div>
            <h2 class="text-sm font-extrabold text-[#6B7086] m-0 hidden md:block uppercase tracking-wider truncate">@yield('title', 'System Module')</h2>
        </div>

        <div class="flex items-center gap-2 sm:gap-4 shrink-0">

            <!-- Notifications Menu (Real-Time Poller) -->
            <div x-data="{ 
                    notifOpen: false, 
                    unreadCount: 0, 
                    notifications: [],
                    fetchNotifs() {
                        fetch('{{ route('notifications.fetch') }}')
                            .then(res => res.json())
                            .then(data => {
                                this.unreadCount = data.count;
                                this.notifications = data.items;
                            });
                    },
                    markAllRead() {
                        if(this.unreadCount > 0) {
                            this.unreadCount = 0;
                            this.notifications.forEach(n => n.is_read = true);
                            fetch('{{ route('notifications.markAllRead') }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });
                        }
                    }
                }" 
                x-init="fetchNotifs(); setInterval(() => fetchNotifs(), 15000);" 
                class="relative">
                
                <!-- Bell Button -->
                <button @click="notifOpen = !notifOpen; if(notifOpen) { markAllRead(); }" @click.away="notifOpen = false" class="relative w-10 h-10 rounded-xl bg-[#F0F3FA] border border-[#1E2657]/20 flex items-center justify-center text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-sm transition active:scale-95">
                    <i class="fa fa-bell"></i>
                    
                    <!-- Dynamic Red Dot Indicator -->
                    <span x-show="unreadCount > 0" x-text="unreadCount" x-transition x-cloak class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 bg-rose-500 text-white text-[9px] font-black flex items-center justify-center rounded-full border-2 border-white shadow-sm animate-pulse min-w-[18px]"></span>
                </button>

                <!-- Dropdown (Mobile-Optimized Width) -->
                <div x-show="notifOpen" x-transition x-cloak class="absolute right-0 mt-2 w-[calc(100vw-2rem)] sm:w-80 max-w-sm bg-white border border-[#1E2657]/15 rounded-2xl shadow-xl overflow-hidden z-[100]">
                    <div class="bg-[#F8F9FD] px-4 py-3 border-b border-[#E8E5DC] flex justify-between items-center">
                        <span class="font-extrabold text-[13px] tracking-widest uppercase text-[#1E2657]">Notifications</span>
                    </div>
                    
                    <!-- Empty State -->
                    <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-[#9CA0AF]">
                        <i class="fa-regular fa-bell-slash text-2xl mb-2 block text-[#E8E5DC]"></i>
                        <p class="text-[12px] font-bold m-0">You're all caught up!</p>
                    </div>

                    <!-- List State -->
                    <div x-show="notifications.length > 0" class="max-h-[300px] overflow-y-auto custom-scrollbar flex flex-col bg-white">
                        <template x-for="notif in notifications" :key="notif.id">
                            <!-- UPDATED HREF TO HIT THE MARK-AS-READ ROUTE -->
                            <a :href="'/notifications/' + notif.id + '/read'" class="px-4 py-3.5 border-b border-black/5 hover:bg-[#F0F3FA] transition flex flex-col gap-1 decoration-transparent"
                               :class="notif.is_read ? 'bg-white' : 'bg-blue-50/50'">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-[13px] font-extrabold leading-tight" :class="notif.is_read ? 'text-[#6B7086]' : 'text-[#1E2657]'" x-text="notif.message"></span>
                                    <span x-show="!notif.is_read" class="w-2 h-2 rounded-full bg-[#3B82F6] shrink-0 mt-1"></span>
                                </div>
                                <span class="text-[10px] text-[#9CA0AF] font-bold uppercase tracking-wider" x-text="notif.time"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Profile Menu -->
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="w-10 h-10 rounded-xl flex items-center justify-center font-extrabold text-white text-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shadow-md border border-[#1E2657]/20 hover:ring-2 hover:ring-[#5B8DEF]/50 transition active:scale-95">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </button>
                <div x-show="profileOpen" x-transition x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-[#1E2657]/15 rounded-2xl shadow-xl py-2 z-50">
                    <div class="px-4 py-3 border-b border-[#E8E5DC] bg-[#F8F9FD]">
                        <span class="block text-sm font-extrabold text-[#1E2657]">{{ Auth::user()->name ?? 'Guest User' }}</span>
                        <span class="block text-[10px] font-bold text-[#C4922E] uppercase tracking-wide mt-0.5">{{ Auth::user()->role ?? 'Role' }} Account</span>
                    </div>
                    <a href="{{ route('profile.security') }}" class="flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] hover:bg-slate-50 transition">
                        <i class="fa fa-shield-halved w-4 text-center text-[#5B8DEF]"></i> Security & Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-1 border-t border-black/5">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer">
                            <i class="fa fa-sign-out-alt w-4 text-center"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- MOBILE SLIDE-OUT DRAWER                   -->
    <!-- ========================================== -->
    @if(Auth::check())
    @php
        $authUser = Auth::user();
        $authRole = strtolower($authUser->role ?? 'guardian');
        $guardianChildren = ($authRole === 'guardian') ? ($authUser->children()->where('status', 'active')->get()) : collect();
        $activeChildId = session('active_child_id', $guardianChildren->first()->id ?? null);
        $activeChild = $guardianChildren->where('id', $activeChildId)->first() ?? $guardianChildren->first();
    @endphp
    
    <div x-show="mobileDrawerOpen" 
         x-cloak 
         class="fixed inset-0 z-[120] md:hidden" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="mobileDrawerOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileDrawerOpen = false" 
             class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Drawer Panel -->
        <div x-show="mobileDrawerOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-[85%] max-w-xs bg-white h-full shadow-2xl flex flex-col justify-between overflow-y-auto custom-scrollbar z-10">
            
            <div class="p-5 flex flex-col gap-4">
                <!-- Top Header & Close -->
                <div class="flex items-center justify-between border-b border-[#E8E5DC] pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1E2657] to-[#12163A] flex items-center justify-center text-white font-black text-xs shadow-sm">DA</div>
                        <div>
                            <h2 class="text-[#1E2657] font-extrabold text-[14px] leading-tight m-0">Dream Achievers</h2>
                            <span class="text-[#C4922E] font-bold text-[9px] uppercase tracking-widest block">Learning Center PH</span>
                        </div>
                    </div>
                    <button @click="mobileDrawerOpen = false" type="button" class="w-8 h-8 rounded-lg bg-[#F0F3FA] text-[#6B7086] hover:text-[#1E2657] flex items-center justify-center text-sm transition" aria-label="Close Drawer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- User Profile Summary -->
                <div class="bg-[#F8F9FD] border border-[#1E2657]/10 rounded-2xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white flex items-center justify-center font-black text-sm shadow-sm shrink-0">
                        {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="block text-[13px] font-extrabold text-[#1E2657] truncate leading-tight">{{ $authUser->name }}</span>
                        <span class="inline-flex px-2 py-0.5 mt-1 text-[9px] font-extrabold rounded bg-[#C4922E]/10 text-[#C4922E] uppercase tracking-wider border border-[#C4922E]/20">
                            {{ ucfirst($authRole) }} Portal
                        </span>
                    </div>
                </div>

                <!-- GUARDIAN ONLY: Child Switcher in Mobile Drawer -->
                @if($authRole === 'guardian' && $guardianChildren->isNotEmpty())
                    <div class="bg-indigo-50/60 border border-indigo-200/80 rounded-2xl p-3 flex flex-col gap-2">
                        <span class="text-[10px] font-extrabold text-indigo-700 uppercase tracking-widest flex items-center justify-between">
                            <span><i class="fa-solid fa-graduation-cap mr-1"></i> Student Selection</span>
                            <span class="text-[9px] text-indigo-500 font-bold">({{ $guardianChildren->count() }} child{{ $guardianChildren->count() > 1 ? 'ren' : '' }})</span>
                        </span>
                        <div class="flex flex-col gap-1.5 mt-0.5">
                            @foreach($guardianChildren as $child)
                                @php $isCurrent = ($activeChild && $activeChild->id === $child->id); @endphp
                                <a href="{{ route('guardian.switch-child', $child->id) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-[12px] font-extrabold transition-all {{ $isCurrent ? 'bg-[#3B82F6] text-white shadow-sm' : 'bg-white text-[#1E2657] hover:bg-white/80 border border-indigo-100' }}">
                                    <span class="truncate">{{ $child->first_name }} {{ $child->last_name }}</span>
                                    @if($isCurrent)
                                        <i class="fa-solid fa-check text-[11px] shrink-0"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Navigation Links List -->
                <nav class="flex flex-col gap-1 pt-1">
                    <span class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest px-2 mb-1">Navigation Menu</span>
                    
                    @if($authRole === 'guardian')
                        <a href="{{ route('guardian.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('guardian.dashboard') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('guardian.dashboard') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-house"></i></div>
                            <span>Dashboard Home</span>
                        </a>

                        <a href="{{ route('guardian.recommendations') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ (request()->routeIs('guardian.recommendations') || request()->routeIs('guardian.logs.archive')) ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ (request()->routeIs('guardian.recommendations') || request()->routeIs('guardian.logs.archive')) ? 'text-white' : 'text-[#10B981] bg-emerald-50' }}"><i class="fa-solid fa-book-open-reader"></i></div>
                            <span>Daily Reports & Home</span>
                        </a>

                        <a href="{{ route('guardian.goals') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('guardian.goals*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('guardian.goals*') ? 'text-white' : 'text-[#8B5CF6] bg-purple-50' }}"><i class="fa-solid fa-bullseye"></i></div>
                            <span>IEP Learning Goals</span>
                        </a>

                        <a href="{{ route('guardian.reports') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('guardian.reports*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('guardian.reports*') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-file-signature"></i></div>
                            <span>Quarterly Reports</span>
                        </a>

                        <a href="{{ route('guardian.announcements.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('guardian.announcements*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('guardian.announcements*') ? 'text-white' : 'text-[#F59E0B] bg-amber-50' }}"><i class="fa-solid fa-bullhorn"></i></div>
                            <span>Announcements</span>
                        </a>
                    @elseif($authRole === 'teacher')
                        <a href="{{ route('teacher.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.dashboard') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.dashboard') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-house"></i></div>
                            <span>Dashboard Home</span>
                        </a>

                        <a href="{{ route('teacher.daily-logs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.daily-logs*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.daily-logs*') ? 'text-white' : 'text-[#10B981] bg-emerald-50' }}"><i class="fa-solid fa-pencil"></i></div>
                            <span>Daily Logging</span>
                        </a>

                        <a href="{{ route('teacher.iep-goals.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.iep-goals*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.iep-goals*') ? 'text-white' : 'text-[#8B5CF6] bg-purple-50' }}"><i class="fa-solid fa-bullseye"></i></div>
                            <span>IEP Goals</span>
                        </a>

                        <a href="{{ route('teacher.reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.reports*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.reports*') ? 'text-white' : 'text-[#F59E0B] bg-amber-50' }}"><i class="fa-solid fa-file-lines"></i></div>
                            <span>Student Reports</span>
                        </a>

                        <a href="{{ route('teacher.students.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.students*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.students*') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-users"></i></div>
                            <span>My Students</span>
                        </a>

                        <a href="{{ route('teacher.announcements') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('teacher.announcements*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('teacher.announcements*') ? 'text-white' : 'text-[#8B5CF6] bg-purple-50' }}"><i class="fa-solid fa-bullhorn"></i></div>
                            <span>Announcements</span>
                        </a>
                    @elseif($authRole === 'admin')
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-house"></i></div>
                            <span>Dashboard Home</span>
                        </a>

                        <a href="{{ route('admin.students.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.students*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.students*') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-users"></i></div>
                            <span>Student Profiles</span>
                        </a>

                        <a href="{{ route('admin.accounts.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.accounts*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.accounts*') ? 'text-white' : 'text-[#10B981] bg-emerald-50' }}"><i class="fa-solid fa-shield-halved"></i></div>
                            <span>User Accounts</span>
                        </a>

                        <a href="{{ route('admin.report-approval.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.report-approval*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.report-approval*') ? 'text-white' : 'text-[#F59E0B] bg-amber-50' }}"><i class="fa-solid fa-file-circle-check"></i></div>
                            <span>Report Approval</span>
                        </a>

                        <a href="{{ route('admin.announcements.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.announcements*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.announcements*') ? 'text-white' : 'text-[#8B5CF6] bg-purple-50' }}"><i class="fa-solid fa-bullhorn"></i></div>
                            <span>Announcements</span>
                        </a>

                        <a href="{{ route('admin.quarters.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.quarters*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.quarters*') ? 'text-white' : 'text-[#3B82F6] bg-blue-50' }}"><i class="fa-solid fa-calendar-days"></i></div>
                            <span>Academic Quarters</span>
                        </a>

                        <a href="{{ route('admin.archive.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-extrabold transition-colors {{ request()->routeIs('admin.archive*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.archive*') ? 'text-white' : 'text-[#6B7086] bg-slate-100' }}"><i class="fa-solid fa-box-archive"></i></div>
                            <span>Student Archive</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Drawer Footer (Settings & Logout) -->
            <div class="p-4 border-t border-[#E8E5DC] bg-[#F8F9FD] flex flex-col gap-2">
                <a href="{{ route('profile.security') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-[12px] font-extrabold text-[#6B7086] hover:text-[#1E2657] hover:bg-white transition">
                    <i class="fa-solid fa-gear w-4 text-center"></i>
                    <span>Security & Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-[12px] font-extrabold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer">
                        <i class="fa-solid fa-sign-out-alt w-4 text-center"></i>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- 2. MAIN PAGE WRAPPER                       -->
    <!-- ========================================== -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 pb-28 md:pb-12 font-['Inter']">

        @php
            $isDashboard = request()->is('*dashboard*');
        @endphp

        <!-- BACK BUTTON: Shows only when inside a system module (not on the main dashboard) -->
        @if(!$isDashboard && Auth::check())
            <div class="mb-5 sm:mb-6 flex">
                <a href="{{ route(strtolower(Auth::user()->role) . '.dashboard') }}" id="back-to-dashboard-btn" class="inline-flex items-center gap-2 px-4 py-2 bg-[#F0F3FA] border border-[#1E2657]/20 rounded-xl text-sm font-bold text-[#1E2657] hover:text-white hover:bg-[#1E2657] shadow-sm transition-all active:scale-95">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- YIELD CONTENT: Injects your specific files -->
        <!-- ========================================== -->
        <div id="injected-dashboard-content" class="w-full">
            @yield('content')
        </div>

    </main>

    <!-- ========================================== -->
    <!-- 3. MOBILE BOTTOM NAVIGATION BAR           -->
    <!-- ========================================== -->
    @if(Auth::check())
    @php
        $authUser = Auth::user();
        $authRole = strtolower($authUser->role ?? 'guardian');
    @endphp

    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-[#1E2657]/15 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-2 pt-1.5 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
        <div class="grid grid-cols-5 items-center max-w-md mx-auto">
            
            @if($authRole === 'guardian')
                <!-- 1. Home -->
                @php $isHome = request()->routeIs('guardian.dashboard'); @endphp
                <a href="{{ route('guardian.dashboard') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isHome ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-house text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Home</span>
                </a>

                <!-- 2. Daily Logs -->
                @php $isLogs = request()->routeIs('guardian.recommendations') || request()->routeIs('guardian.logs.archive'); @endphp
                <a href="{{ route('guardian.recommendations') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isLogs ? 'text-[#10B981]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isLogs ? 'bg-[#10B981]/10' : '' }}">
                        <i class="fa-solid fa-book-open-reader text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isLogs ? 'text-[#10B981]' : 'text-[#6B7086]' }}">Activity</span>
                </a>

                <!-- 3. Goals -->
                @php $isGoals = request()->routeIs('guardian.goals*'); @endphp
                <a href="{{ route('guardian.goals') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isGoals ? 'text-[#8B5CF6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isGoals ? 'bg-[#8B5CF6]/10' : '' }}">
                        <i class="fa-solid fa-bullseye text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isGoals ? 'text-[#8B5CF6]' : 'text-[#6B7086]' }}">IEP Goals</span>
                </a>

                <!-- 4. Reports -->
                @php $isReports = request()->routeIs('guardian.reports*'); @endphp
                <a href="{{ route('guardian.reports') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isReports ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isReports ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-file-signature text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isReports ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Reports</span>
                </a>

                <!-- 5. Alerts -->
                @php $isAlerts = request()->routeIs('guardian.announcements*'); @endphp
                <a href="{{ route('guardian.announcements.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isAlerts ? 'text-[#F59E0B]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isAlerts ? 'bg-[#F59E0B]/10' : '' }}">
                        <i class="fa-solid fa-bullhorn text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isAlerts ? 'text-[#F59E0B]' : 'text-[#6B7086]' }}">Alerts</span>
                </a>

            @elseif($authRole === 'teacher')
                <!-- 1. Home -->
                @php $isHome = request()->routeIs('teacher.dashboard'); @endphp
                <a href="{{ route('teacher.dashboard') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isHome ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-house text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Home</span>
                </a>

                <!-- 2. Daily Logs -->
                @php $isLogs = request()->routeIs('teacher.daily-logs*'); @endphp
                <a href="{{ route('teacher.daily-logs.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isLogs ? 'text-[#10B981]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isLogs ? 'bg-[#10B981]/10' : '' }}">
                        <i class="fa-solid fa-pencil text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isLogs ? 'text-[#10B981]' : 'text-[#6B7086]' }}">Logging</span>
                </a>

                <!-- 3. Goals -->
                @php $isGoals = request()->routeIs('teacher.iep-goals*'); @endphp
                <a href="{{ route('teacher.iep-goals.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isGoals ? 'text-[#8B5CF6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isGoals ? 'bg-[#8B5CF6]/10' : '' }}">
                        <i class="fa-solid fa-bullseye text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isGoals ? 'text-[#8B5CF6]' : 'text-[#6B7086]' }}">IEP</span>
                </a>

                <!-- 4. Reports -->
                @php $isReports = request()->routeIs('teacher.reports*'); @endphp
                <a href="{{ route('teacher.reports.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isReports ? 'text-[#F59E0B]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isReports ? 'bg-[#F59E0B]/10' : '' }}">
                        <i class="fa-solid fa-file-lines text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isReports ? 'text-[#F59E0B]' : 'text-[#6B7086]' }}">Reports</span>
                </a>

                <!-- 5. Students -->
                @php $isStudents = request()->routeIs('teacher.students*'); @endphp
                <a href="{{ route('teacher.students.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isStudents ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isStudents ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-users text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isStudents ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Students</span>
                </a>

            @elseif($authRole === 'admin')
                <!-- 1. Home -->
                @php $isHome = request()->routeIs('admin.dashboard'); @endphp
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isHome ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-house text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isHome ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Home</span>
                </a>

                <!-- 2. Students -->
                @php $isStudents = request()->routeIs('admin.students*'); @endphp
                <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isStudents ? 'text-[#3B82F6]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isStudents ? 'bg-[#3B82F6]/10' : '' }}">
                        <i class="fa-solid fa-users text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isStudents ? 'text-[#3B82F6]' : 'text-[#6B7086]' }}">Students</span>
                </a>

                <!-- 3. Accounts -->
                @php $isAccounts = request()->routeIs('admin.accounts*'); @endphp
                <a href="{{ route('admin.accounts.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isAccounts ? 'text-[#10B981]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isAccounts ? 'bg-[#10B981]/10' : '' }}">
                        <i class="fa-solid fa-shield-halved text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isAccounts ? 'text-[#10B981]' : 'text-[#6B7086]' }}">Accounts</span>
                </a>

                <!-- 4. Approvals -->
                @php $isApprovals = request()->routeIs('admin.report-approval*'); @endphp
                <a href="{{ route('admin.report-approval.index') }}" class="flex flex-col items-center justify-center py-1 transition-colors {{ $isApprovals ? 'text-[#F59E0B]' : 'text-[#6B7086] hover:text-[#1E2657]' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl {{ $isApprovals ? 'bg-[#F59E0B]/10' : '' }}">
                        <i class="fa-solid fa-file-circle-check text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight {{ $isApprovals ? 'text-[#F59E0B]' : 'text-[#6B7086]' }}">Approvals</span>
                </a>

                <!-- 5. More / Menu -->
                <button type="button" @click="mobileDrawerOpen = true" class="flex flex-col items-center justify-center py-1 transition-colors text-[#6B7086] hover:text-[#1E2657] active:scale-95">
                    <div class="relative flex items-center justify-center w-7 h-7 rounded-xl">
                        <i class="fa-solid fa-bars text-[15px]"></i>
                    </div>
                    <span class="text-[10px] font-extrabold mt-0.5 tracking-tight text-[#6B7086]">More</span>
                </button>
            @endif

        </div>
    </nav>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                const backBtn = e.target.closest('#back-to-dashboard-btn, a[href*="dashboard"]');
                if (backBtn && window.hasUnsavedAiChanges) {
                    if (!confirm("You have unsaved changes. Are you sure you want to leave?")) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
            }, true);
        });

        function handleFileInputChange(inputEl, previewContainerId, fileNameId) {
            if (inputEl && inputEl.files && inputEl.files[0]) {
                const file = inputEl.files[0];
                const fileNameEl = document.getElementById(fileNameId);
                const containerEl = document.getElementById(previewContainerId);
                if (fileNameEl) fileNameEl.textContent = file.name;
                if (containerEl) containerEl.classList.remove('hidden');
            }
        }

        function clearSelectedFile(inputId, previewContainerId) {
            const inputEl = document.getElementById(inputId);
            if (inputEl) {
                inputEl.value = '';
            }
            const containerEl = document.getElementById(previewContainerId);
            if (containerEl) {
                containerEl.classList.add('hidden');
            }
        }

        function handleMultiFileInputChange(inputEl, previewContainerId) {
            const containerEl = document.getElementById(previewContainerId);
            if (!containerEl) return;
            containerEl.innerHTML = '';
            if (!inputEl || !inputEl.files || inputEl.files.length === 0) {
                containerEl.classList.add('hidden');
                return;
            }
            containerEl.classList.remove('hidden');

            const wrapper = document.createElement('div');
            wrapper.className = 'p-2.5 bg-blue-50/80 border border-blue-200 rounded-xl space-y-1.5 shadow-sm';

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between text-[11px] font-extrabold text-[#3B82F6] pb-1 border-b border-blue-200/60';
            header.innerHTML = `<span><i class="fa fa-paperclip mr-1"></i> ${inputEl.files.length} file(s) selected</span>`;

            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'text-rose-600 hover:text-rose-800 text-[10px] font-bold cursor-pointer';
            clearBtn.innerHTML = '<i class="fa fa-times mr-0.5"></i> Clear all';
            clearBtn.onclick = function() {
                inputEl.value = '';
                containerEl.innerHTML = '';
                containerEl.classList.add('hidden');
            };
            header.appendChild(clearBtn);
            wrapper.appendChild(header);

            const list = document.createElement('div');
            list.className = 'space-y-1 max-h-36 overflow-y-auto pr-1';
            Array.from(inputEl.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between text-[11px] font-semibold text-[#1E2657] bg-white px-2 py-1 rounded-lg border border-blue-100';
                const sizeStr = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                item.innerHTML = `<span class="truncate max-w-[240px]"><i class="fa fa-file text-[#3B82F6] mr-1.5"></i>${file.name}</span><span class="text-[10px] text-[#9CA0AF] font-bold">${sizeStr}</span>`;
                list.appendChild(item);
            });
            wrapper.appendChild(list);
            containerEl.appendChild(wrapper);
        }
    </script>

@stack('scripts')
</body>
</html>