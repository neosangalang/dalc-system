<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Dream Achievers Learning Center PH</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-['Nunito'] bg-slate-50 text-slate-800 antialiased overflow-hidden" x-data="{ sidebarOpen: false }">

<div class="flex h-screen w-full">
    
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1E2657] flex flex-col transition-transform duration-300 lg:static lg:translate-x-0 shadow-2xl lg:shadow-none">
        
        <div class="p-6 border-b border-white/10 flex items-center gap-3">
            
            <div class="leading-tight">
                <h5 class="text-white font-extrabold text-sm m-0">Dream Achievers</h5>
                <span class="text-amber-400 font-bold text-xs tracking-wide">Learning Center PH</span>
            </div>
        </div>

        <div class="p-5 border-b border-white/10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-white text-lg bg-gradient-to-br from-indigo-500 to-purple-500 shadow-inner">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h6 class="text-white font-bold text-sm m-0">{{ Auth::user()->name ?? 'Guest User' }}</h6>
                <span class="text-slate-400 text-xs font-semibold capitalize">{{ Auth::user()->role ?? 'Role' }}</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
    
            @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i> Student Profiling
                </a>
                <a href="{{ route('admin.report-approval.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.report-approval.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-file-circle-check w-5 text-center"></i> Report Approval
                </a>
                <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.accounts.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-user-shield w-5 text-center"></i> User Management
                </a>
                <a href="{{ route('admin.quarters.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.quarters.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-days w-5 text-center"></i> Calendar Setup
                </a>
                <a href="{{ route('admin.archive.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.archive.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-5 text-center"></i> Archiving
                </a>
            
            @elseif(Auth::check() && Auth::user()->role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('teacher.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('teacher.iep-goals.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('teacher.iep-goals.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-bullseye w-5 text-center"></i> IEP Goals
                </a>
                <a href="{{ route('teacher.daily-logs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('teacher.daily-logs.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-pencil w-5 text-center"></i> Daily Logging
                </a>
                <a href="{{ route('teacher.students.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('teacher.students.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i> My Students
                </a>
                <a href="{{ route('teacher.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('teacher.reports.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-file-signature w-5 text-center"></i> Reports
                </a>

                @if(Auth::user()->can_approve_reports || Auth::user()->can_create_profiles || Auth::user()->can_manage_calendar || Auth::user()->can_archive_students || Auth::user()->can_manage_credentials)
                    <div class="px-4 py-2 mt-4 text-[10px] font-extrabold tracking-widest uppercase text-amber-400 opacity-80">Admin Privileges</div>
                @endif

                @if(Auth::user()->can_manage_credentials)
                    <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.accounts.*') ? 'bg-indigo-600 text-white' : 'text-amber-400 hover:bg-white/10' }}">
                        <i class="fa-solid fa-key w-5 text-center"></i> Manage Logins
                    </a>
                @endif

                @if(Auth::user()->can_manage_calendar)
                    <a href="{{ route('admin.quarters.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.quarters.*') ? 'bg-indigo-600 text-white' : 'text-amber-400 hover:bg-white/10' }}">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i> Calendar Setup
                    </a>
                @endif
                
                @if(Auth::user()->can_create_profiles)
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.students.*') ? 'bg-indigo-600 text-white' : 'text-amber-400 hover:bg-white/10' }}">
                        <i class="fa-solid fa-user-plus w-5 text-center"></i> Create Profiles
                    </a>
                @endif

                @if(Auth::user()->can_archive_students)
                    <a href="{{ route('admin.archive.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.archive.*') ? 'bg-indigo-600 text-white' : 'text-amber-400 hover:bg-white/10' }}">
                        <i class="fa-solid fa-box-archive w-5 text-center"></i> Archiving
                    </a>
                @endif

                @if(Auth::user()->can_approve_reports)
                    <a href="{{ route('admin.report-approval.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('admin.report-approval.*') ? 'bg-indigo-600 text-white' : 'text-amber-400 hover:bg-white/10' }}">
                        <i class="fa-solid fa-file-circle-check w-5 text-center"></i> Report Approval
                    </a>
                @endif

            @elseif(Auth::check() && Auth::user()->role === 'guardian') 
            
                <a href="{{ route('guardian.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('guardian.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa fa-home w-5 text-center"></i> Dashboard
                </a>

                <a href="{{ route('guardian.goals') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('guardian.goals') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa fa-bullseye w-5 text-center"></i> Goals Module
                </a>
                
                <div x-data="{ reportOpen: {{ request()->routeIs('guardian.reports') ? 'true' : 'false' }} }">
                    <button @click="reportOpen = !reportOpen" class="w-full flex justify-between items-center px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('guardian.reports') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <div class="flex items-center gap-3"><i class="fa fa-file-signature w-5 text-center"></i> Report Module</div>
                        <i class="fa fa-caret-down transition-transform" :class="reportOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="reportOpen" x-collapse class="pl-8 pr-2 py-2 space-y-1 border-l-2 border-indigo-500 ml-4 mt-2">
                        <a href="{{ route('guardian.reports', ['type' => 'daily']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Daily Summaries</a>
                        <a href="{{ route('guardian.reports', ['type' => 'q1']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Q1 Reports</a>
                        <a href="{{ route('guardian.reports', ['type' => 'q2']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Q2 Reports</a>
                        <a href="{{ route('guardian.reports', ['type' => 'q3']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Q3 Reports</a>
                        <a href="{{ route('guardian.reports', ['type' => 'q4']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Q4 Reports</a>
                        <a href="{{ route('guardian.reports', ['type' => 'mid_year']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Mid-Year Reports</a>
                        <a href="{{ route('guardian.reports', ['type' => 'year_end']) }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-white/60 hover:text-white hover:bg-white/5">Year-End Reports</a>
                    </div>
                </div>

                <a href="{{ route('guardian.recommendations') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[13px] font-bold transition-all {{ request()->routeIs('guardian.recommendations') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa fa-house-user w-5 text-center"></i> Home Recommendations
                </a>
            @endif 
        </nav>

        
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-slate-50 overflow-hidden">
        
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm">
            
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-indigo-600 transition p-2">
                    <i class="fa fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg font-extrabold text-slate-800 m-0">@yield('title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-3 sm:gap-4">
                
                @if(Auth::check() && Auth::user()->role === 'guardian')
                    @php
                        $myChildren = Auth::user()->children ?? collect();
                        $activeChildId = session('active_child_id', $myChildren->first()->id ?? null);
                        $activeChild = $myChildren->where('id', $activeChildId)->first();
                    @endphp

                    @if($myChildren->count() > 0 && $activeChild)
                        <div x-data="{ switchOpen: false }" class="relative">
                            <button @click="switchOpen = !switchOpen" @click.away="switchOpen = false" class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-sm rounded-xl hover:bg-indigo-100 transition">
                                <div class="bg-indigo-600 text-white rounded-full flex items-center justify-center w-6 h-6 text-[10px]">
                                    <i class="fa fa-child"></i>
                                </div>
                                <span class="hidden sm:inline">Viewing:</span> {{ $activeChild->first_name }}
                            </button>
                            
                            <div x-show="switchOpen" x-transition.opacity.scale.95 class="absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-xl shadow-xl z-50 py-2">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">My Children</span>
                                </div>
                                @foreach($myChildren as $child)
                                    <a href="{{ route('guardian.switch-child', $child->id) }}" class="flex items-center justify-between px-4 py-2.5 text-sm font-bold transition hover:bg-slate-50 {{ $activeChild->id === $child->id ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-700' }}">
                                        <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                        @if($activeChild->id === $child->id) <i class="fa fa-check text-indigo-500"></i> @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div x-data="{ notifOpen: false }" class="relative">
                    <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="w-10 h-10 rounded-xl border-2 border-slate-200 flex items-center justify-center text-slate-500 hover:border-indigo-500 hover:text-indigo-600 transition relative">
                        <i class="fa fa-bell"></i>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white text-[9px] font-bold text-white items-center justify-center">{{ Auth::user()->unreadNotifications->count() }}</span>
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="notifOpen" x-transition.opacity.scale.95 class="absolute right-0 mt-2 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-800">Notifications</span>
                            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[10px] font-bold rounded-full">{{ Auth::check() ? Auth::user()->unreadNotifications->count() : 0 }} New</span>
                        </div>
                        <div class="max-h-[60vh] overflow-y-auto">
                            @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                @foreach(Auth::user()->unreadNotifications->take(5) as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                                <i class="fa {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                            </div>
                                            <div>
                                                <p class="text-[13px] font-bold text-slate-800 mb-0.5">{{ $notification->data['title'] ?? 'New Alert' }}</p>
                                                <p class="text-xs text-slate-500 mb-1 line-clamp-2">{{ $notification->data['message'] ?? 'You have a new system notification.' }}</p>
                                                <span class="text-[10px] font-bold text-indigo-500">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center text-slate-400">
                                    <i class="fa fa-bell-slash text-3xl mb-2 opacity-50"></i>
                                    <p class="text-xs font-medium m-0">No new notifications</p>
                                </div>
                            @endif
                        </div>
                        <div class="bg-slate-50 border-t border-slate-100 text-center p-2">
                            <a href="{{ Auth::check() && Auth::user()->role === 'guardian' ? route('guardian.notifications') : '#' }}" class="text-[11px] font-extrabold text-indigo-600 hover:text-indigo-700 uppercase tracking-wide">View All</a>
                        </div>
                    </div>
                </div>

                <div x-data="{ profileOpen: false }" class="relative">
                    <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-white text-sm bg-gradient-to-br from-indigo-500 to-purple-500 shadow-sm ring-2 ring-transparent hover:ring-indigo-300 transition">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </button>
                    
                    <div x-show="profileOpen" x-transition.opacity.scale.95 class="absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 py-2">
                        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/50 mt(-2)">
                            <span class="block text-sm font-extrabold text-slate-800 truncate">{{ Auth::user()->name ?? 'Guest User' }}</span>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ Auth::user()->role ?? 'Role' }} Account</span>
                        </div>
                        <a href="{{ route('profile.index') ?? '#' }}" class="flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition mt-1">
                            <i class="fa fa-user w-4 text-center"></i> My Profile
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-red-500 hover:text-red-600 hover:bg-red-50 transition text-left">
                                <i class="fa fa-sign-out-alt w-4 text-center"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <main class="flex-1 overflow-y-auto w-full">
            @yield('content')
        </main>
        
    </div>
</div>

@stack('scripts')
</body>
</html>