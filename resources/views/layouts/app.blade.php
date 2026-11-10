<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <title>@yield('title', 'System') - Dream Achievers Learning Center PH</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

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

<body class="font-['Nunito'] bg-gradient-to-br from-[#F4F1EA] via-[#EDF0F8] to-[#F4F1EA] text-[#2C2F45] antialiased min-h-screen bg-fixed overflow-x-hidden" x-data="{ adminSidebarOpen: false }">

    @if(Auth::check() && Auth::user()->role === 'admin')
        <!-- Admin Sidebar Backdrop -->
        <div x-show="adminSidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             @click="adminSidebarOpen = false" 
             class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50">
        </div>

        <!-- Admin Sidebar Drawer -->
        <aside x-show="adminSidebarOpen"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               x-cloak
               class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white shadow-2xl border-r border-[#1E2657]/15 flex flex-col justify-between">
            
            <!-- Sidebar Header -->
            <div>
                <div class="h-16 px-5 border-b border-black/10 flex items-center justify-between bg-[#F8F9FD]">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#12163A] flex items-center justify-center text-white font-black text-xs shadow-md">
                            DA
                        </div>
                        <div>
                            <span class="block font-black text-sm text-[#1E2657] leading-tight">Admin Console</span>
                            <span class="block text-[10px] font-bold text-[#C4922E] uppercase tracking-wider">Dream Achievers</span>
                        </div>
                    </div>
                    <button type="button" @click="adminSidebarOpen = false" class="w-8 h-8 rounded-lg bg-slate-200/60 hover:bg-slate-200 text-[#1E2657] flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Navigation Links List -->
                <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-140px)] custom-scrollbar">
                    <span class="block px-3 text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-2">Core Modules</span>

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-gauge-high w-4 text-center"></i>
                        <span>Dashboard Overview</span>
                    </a>

                    <!-- Student Profiles -->
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.students.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-users w-4 text-center text-[#3B82F6]"></i>
                        <span>Student Profiles</span>
                    </a>

                    <!-- Approvals -->
                    <a href="{{ route('admin.report-approval.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.report-approval.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-circle-check w-4 text-center text-[#10B981]"></i>
                            <span>Report Approvals</span>
                        </div>
                    </a>

                    <!-- User Management -->
                    <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.accounts.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-user-shield w-4 text-center text-[#8B5CF6]"></i>
                        <span>User Management</span>
                    </a>

                    <!-- Calendar / Terms -->
                    <a href="{{ route('admin.quarters.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.quarters.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-calendar-days w-4 text-center text-[#F59E0B]"></i>
                        <span>Calendar & Terms</span>
                    </a>

                    <!-- Announcements -->
                    <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.announcements.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-bullhorn w-4 text-center text-[#8B5CF6]"></i>
                        <span>Announcements</span>
                    </a>

                    <!-- Archive Snapshots -->
                    <a href="{{ route('admin.archive.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.archive.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA]' }}">
                        <i class="fa-solid fa-box-archive w-4 text-center text-[#6B7086]"></i>
                        <span>Year-End Archive</span>
                    </a>

                    <div class="pt-3 pb-1 border-t border-black/10 my-2">
                        <span class="block px-3 text-[10px] font-extrabold uppercase tracking-widest text-[#9CA0AF] mb-2">Audit & Compliance</span>
                    </div>

                    <!-- STANDALONE AUDIT LOGS MODULE -->
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#1E2657] hover:bg-[#F0F3FA] border border-[#1E2657]/15 bg-blue-50/40' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-shield-halved w-4 text-center text-[#3B82F6]"></i>
                            <span>Audit Logs</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ request()->routeIs('admin.audit-logs.*') ? 'bg-white/20 text-white' : 'bg-[#3B82F6]/10 text-[#3B82F6]' }}">
                            Real-Time
                        </span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-black/10 bg-[#F8F9FD]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-xs text-[#1E2657] truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] font-bold text-[#C4922E] uppercase tracking-wider">System Administrator</div>
                    </div>
                </div>
            </div>

        </aside>
    @endif

    <!-- ========================================== -->
    <!-- 1. TOP HEADER NAVIGATION                   -->
    <!-- ========================================== -->
    <header class="sticky top-0 z-40 h-16 bg-white/80 backdrop-blur-lg border-b border-[#1E2657]/20 flex items-center justify-between px-3 sm:px-6 lg:px-8 shadow-sm">

        <!-- Logo & Brand -->
        <div class="flex items-center gap-2 sm:gap-4 min-w-0">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <!-- Sidebar Toggle Button -->
                <button type="button" @click="adminSidebarOpen = !adminSidebarOpen" class="w-10 h-10 rounded-xl bg-[#F0F3FA] border border-[#1E2657]/20 flex items-center justify-center text-[#1E2657] hover:bg-[#1E2657] hover:text-white shadow-xs transition active:scale-95 shrink-0 cursor-pointer" title="Open Sidebar Navigation">
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

                <!-- Dropdown (Mobile-Optimized Width & Viewport-Centered on Mobile) -->
                <div x-show="notifOpen" x-transition x-cloak class="fixed sm:absolute left-3 right-3 sm:left-auto sm:right-0 top-16 sm:top-auto mt-1 sm:mt-2 w-auto sm:w-80 max-w-none sm:max-w-sm bg-white border border-[#1E2657]/15 rounded-2xl shadow-xl overflow-hidden z-[100]">
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
                    <div class="py-1 border-b border-slate-100">
                        <a href="{{ route('legal.terms') }}" @click="profileOpen = false" onclick="return openTermsModal(event)" class="w-full flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-[#4B5563] hover:text-[#4F6AF5] hover:bg-slate-50 transition text-left decoration-transparent cursor-pointer">
                            <i class="fa fa-file-contract w-4 text-center text-[#9CA3AF]"></i> Terms & Conditions
                        </a>
                        <a href="{{ route('legal.privacy') }}" @click="profileOpen = false" onclick="return openPrivacyModal(event)" class="w-full flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-[#4B5563] hover:text-[#4F6AF5] hover:bg-slate-50 transition text-left decoration-transparent cursor-pointer">
                            <i class="fa fa-shield-halved w-4 text-center text-[#9CA3AF]"></i> Privacy Policy
                        </a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 pt-1">
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
    <!-- 2. MAIN PAGE WRAPPER                       -->
    <!-- ========================================== -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 pb-8 sm:pb-12 font-['Inter']">

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

        <!-- Dynamic Action Alert Notifications (Auto-dismissing) -->
        <div class="w-full max-w-[1200px] mx-auto">
            @include('components.action-alert')
        </div>

        <!-- ========================================== -->
        <!-- YIELD CONTENT: Injects your specific files -->
        <!-- ========================================== -->
        <div id="injected-dashboard-content" class="w-full">
            @yield('content')
        </div>

    </main>


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

        function handleFileInputChange(inputEl, previewContainerId, fileNameId, maxMb = 5) {
            if (inputEl && inputEl.files && inputEl.files[0]) {
                const file = inputEl.files[0];
                if (file.size > maxMb * 1024 * 1024) {
                    const msg = `"${file.name}" (${(file.size / 1024 / 1024).toFixed(1)}MB) exceeds the maximum allowed size of ${maxMb}MB.`;
                    if (typeof window.showToast === 'function') {
                        window.showToast(msg, 'error');
                    } else {
                        alert(msg);
                    }
                    inputEl.value = '';
                    const containerEl = document.getElementById(previewContainerId);
                    if (containerEl) containerEl.classList.add('hidden');
                    return;
                }
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

        function handleMultiFileInputChange(inputEl, previewContainerId, maxMbPerFile = 5) {
            const containerEl = document.getElementById(previewContainerId);
            if (!containerEl) return;
            containerEl.innerHTML = '';
            if (!inputEl || !inputEl.files || inputEl.files.length === 0) {
                containerEl.classList.add('hidden');
                return;
            }

            const maxBytes = maxMbPerFile * 1024 * 1024;
            const oversized = Array.from(inputEl.files).filter(f => f.size > maxBytes);
            if (oversized.length > 0) {
                const names = oversized.map(f => `"${f.name}" (${(f.size / 1024 / 1024).toFixed(1)}MB)`).join(', ');
                const msg = `File size limit is ${maxMbPerFile}MB per file. The following file(s) are too large: ${names}`;
                if (typeof window.showToast === 'function') {
                    window.showToast(msg, 'error');
                } else {
                    alert(msg);
                }
                inputEl.value = '';
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

    @include('components.terms-modal')
    @include('components.privacy-modal')
    @include('components.confirm-modal')
    @include('components.toast-notification')
    @include('components.dynamic-validation')

@stack('scripts')
</body>
</html>