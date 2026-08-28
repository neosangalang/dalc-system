<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    </style>
</head>

<!-- Restored Original Colors -->
<body class="font-['Nunito'] bg-gradient-to-br from-[#F4F1EA] via-[#EDF0F8] to-[#F4F1EA] text-[#2C2F45] antialiased min-h-screen bg-fixed">

    <!-- ========================================== -->
    <!-- 1. TOP HEADER NAVIGATION                   -->
    <!-- ========================================== -->
    <header class="sticky top-0 z-50 h-16 bg-white/80 backdrop-blur-lg border-b border-[#1E2657]/20 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">

        <!-- Logo and Brand -->
        <div class="flex items-center gap-4 sm:gap-6 min-w-0">
            <div class="flex items-center gap-3 shrink-0">
                <div class="relative w-9 h-9 hidden sm:block">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#12163A] flex items-center justify-center text-white font-black text-sm shadow-md">DA</div>
                    <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full bg-[#C4922E] border-2 border-white flex items-center justify-center">
                        <i class="fa-solid fa-star text-white text-[6px]"></i>
                    </div>
                </div>
                <div class="leading-none">
                    <h1 class="text-[#1E2657] font-extrabold text-[15px] m-0 tracking-tight">Dream Achievers</h1>
                    <span class="text-[#C4922E] font-bold text-[10px] uppercase tracking-widest">Learning Center PH</span>
                </div>
            </div>

            <div class="h-6 w-px bg-[#E8E5DC] hidden md:block shrink-0"></div>
            <h2 class="text-sm font-extrabold text-[#6B7086] m-0 hidden md:block uppercase tracking-wider truncate">@yield('title', 'System Module')</h2>
        </div>

        <div class="flex items-center gap-3 sm:gap-4 shrink-0">

            <!-- Notifications Menu -->
            <div x-data="{ notifOpen: false }" class="relative">
                <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="w-10 h-10 rounded-xl bg-[#F0F3FA] border border-[#1E2657]/20 flex items-center justify-center text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-sm transition">
                    <i class="fa fa-bell"></i>
                </button>
                <div x-show="notifOpen" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-[#1E2657]/15 rounded-2xl shadow-xl overflow-hidden z-50">
                    <div class="bg-[#F8F9FD] px-4 py-3 border-b border-[#E8E5DC] flex justify-between items-center">
                        <span class="font-bold text-sm text-[#1E2657]">Notifications</span>
                    </div>
                    <div class="px-4 py-8 text-center text-[#9CA0AF]">
                        <i class="fa-regular fa-bell-slash text-lg mb-2 block"></i>
                        <p class="text-xs font-medium m-0">You're all caught up!</p>
                    </div>
                </div>
            </div>

            <!-- Profile Menu -->
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="w-10 h-10 rounded-xl flex items-center justify-center font-extrabold text-white text-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shadow-md border border-[#1E2657]/20 hover:ring-2 hover:ring-[#5B8DEF]/50 transition">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </button>
                <div x-show="profileOpen" x-transition x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-[#1E2657]/15 rounded-2xl shadow-xl py-2 z-50">
                    <div class="px-4 py-3 border-b border-[#E8E5DC] bg-[#F8F9FD]">
                        <span class="block text-sm font-extrabold text-[#1E2657]">{{ Auth::user()->name ?? 'Guest User' }}</span>
                        <span class="block text-[10px] font-bold text-[#C4922E] uppercase tracking-wide mt-0.5">{{ Auth::user()->role ?? 'Role' }} Account</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-rose-600 hover:bg-rose-50 transition text-left">
                            <i class="fa fa-sign-out-alt w-4 text-center"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- 2. MAIN PAGE WRAPPER                       -->
    <!-- ========================================== -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12 font-['Inter']">

        @php
            $isDashboard = request()->is('*dashboard*');
        @endphp

        <!-- BACK BUTTON: Shows only when inside a system module (not on the main dashboard) -->
        @if(!$isDashboard && Auth::check())
            <div class="mb-6 flex">
                <a href="{{ route(strtolower(Auth::user()->role) . '.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#F0F3FA] border border-[#1E2657]/20 rounded-xl text-sm font-bold text-[#1E2657] hover:text-white hover:bg-[#1E2657] shadow-sm transition-all">
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

@stack('scripts')
</body>
</html>