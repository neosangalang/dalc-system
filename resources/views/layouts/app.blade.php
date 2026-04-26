<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Dream Achievers Learning Center</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ── COMPLETE CUSTOM CSS ── */
        :root {
            --primary: #4F6AF5;
            --primary-dark: #3751D7;
            --primary-light: #EEF1FF;
            --secondary: #FF6B8A;
            --accent: #FFB347;
            --success: #34C97B;
            --warning: #FFCC00;
            --danger: #FF4C6A;
            --teal: #00C2BD;
            --purple: #9B6DFF;
            --bg: #F5F7FF;
            --sidebar-bg: #1E2657;
            --sidebar-text: rgba(255,255,255,0.75);
            --sidebar-active: rgba(255,255,255,0.12);
            --card-bg: #FFFFFF;
            --text-dark: #1A1F4B;
            --text-muted: #7B8DB7;
            --border: #E8ECF8;
            --shadow: 0 4px 24px rgba(79,106,245,0.08);
            --shadow-hover: 0 8px 32px rgba(79,106,245,0.16);
            --radius: 16px;
            --radius-sm: 10px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Nunito', sans-serif; background: var(--bg); color: var(--text-dark); overflow-x: hidden; }

        /* ── LAYOUT ── */
        #appScreen { display: flex; flex-direction: row; min-height: 100vh; }
        .sidebar { width: 260px; min-width: 260px; background: var(--sidebar-bg); height: 100vh; position: sticky; top: 0; display: flex; flex-direction: column; overflow-y: auto; transition: all 0.3s; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand h5 { color: white; font-weight: 800; font-size: 14px; line-height: 1.4; margin: 0; }
        .sidebar-brand span { color: var(--accent); }
        .sidebar-user { padding: 16px 20px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; color: white; text-transform: uppercase; }
        .sidebar-user-info h6 { color: white; font-size: 13px; font-weight: 700; margin: 0; }
        .sidebar-user-info span { color: var(--text-muted); font-size: 11px; text-transform: capitalize; }
        
        .sidebar-nav { padding: 12px; flex: 1; }
        .nav-section-label { color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; padding: 12px 8px 6px; }
        .nav-item-custom { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; color: var(--sidebar-text); cursor: pointer; transition: all 0.2s; font-size: 13px; font-weight: 600; margin-bottom: 2px; text-decoration: none; }
        .nav-item-custom:hover { background: var(--sidebar-active); color: white; }
        .nav-item-custom.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79,106,245,0.4); }
        .nav-item-custom i { width: 18px; text-align: center; font-size: 14px; }
        
        .sidebar-footer { padding: 16px; border-top: 1px solid rgba(255,255,255,0.06); }
        .logout-btn { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; color: rgba(255,100,100,0.8); cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s; background: transparent; border: none; width: 100%; text-align: left; }
        .logout-btn:hover { background: rgba(255,100,100,0.1); color: #FF6B6B; }

        .main-content { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }
        .topbar { background: white; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .topbar-title { font-size: 18px; font-weight: 800; color: var(--text-dark); }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .icon-btn { width: 38px; height: 38px; border-radius: 10px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-muted); transition: all 0.2s; position: relative; }
        .icon-btn:hover { border-color: var(--primary); color: var(--primary); }
        .badge-dot { position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; border-radius: 50%; background: var(--secondary); border: 2px solid white; }
        .page-content { padding: 28px; flex: 1; overflow-y: auto; }

        /* ── STAT CARDS ── */
        .stat-card { background: white; border-radius: var(--radius); padding: 22px 24px; box-shadow: var(--shadow); border: 1px solid var(--border); transition: all 0.25s; position: relative; overflow: hidden; }
        .stat-card::after { content: ''; position: absolute; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; top: -20px; right: -20px; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); }
        .stat-card .icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 14px; }
        .stat-card h3 { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .stat-card p { font-size: 13px; color: var(--text-muted); font-weight: 600; margin: 0; }
        .stat-card .change { font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 4px; margin-top: 6px; }

        .stat-blue .icon { background: #EEF1FF; color: var(--primary); } .stat-blue h3 { color: var(--primary); }
        .stat-pink .icon { background: #FFF0F3; color: var(--secondary); } .stat-pink h3 { color: var(--secondary); }
        .stat-green .icon { background: #EDFAF4; color: var(--success); } .stat-green h3 { color: var(--success); }
        .stat-orange .icon { background: #FFF7ED; color: var(--accent); } .stat-orange h3 { color: var(--accent); }
        .stat-purple .icon { background: #F3EEFF; color: var(--purple); } .stat-purple h3 { color: var(--purple); }

        /* ── TABLES AND DATA CARDS ── */
        .data-card { background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }
        .data-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .data-card-header h5 { font-size: 16px; font-weight: 800; color: var(--text-dark); margin: 0; }
        
        .table-custom { width: 100%; }
        .table-custom th { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; padding: 8px 14px; border-bottom: 2px solid var(--border); }
        .table-custom td { padding: 14px 14px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .table-custom tr:last-child td { border-bottom: none; }
        .table-custom tr:hover td { background: var(--primary-light); }

        .badge-custom { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
        .badge-active { background: #EDFAF4; color: var(--success); }
        .badge-archived { background: #F5F5F5; color: #999; }
        
        .btn-sm-custom { padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
        .btn-primary-sm { background: var(--primary); color: white; }
        .btn-primary-sm:hover { background: var(--primary-dark); color: white; }

        .student-avatar { width: 34px; height: 34px; min-width: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: white; }
        
        .quick-action { background: white; border-radius: var(--radius-sm); padding: 18px 16px; text-align: center; cursor: pointer; border: 2px solid var(--border); transition: all 0.2s; display: block; text-decoration: none; }
        .quick-action:hover { border-color: var(--primary); background: var(--primary-light); text-decoration: none; }
        .quick-action i { font-size: 24px; margin-bottom: 8px; display: block; }
        .quick-action span { font-size: 12px; font-weight: 700; color: var(--text-dark); }
        
        /* ── MOBILE RESPONSIVENESS (NEW) ── */
        .mobile-toggle-btn { display: none; background: transparent; border: none; font-size: 22px; color: var(--text-dark); cursor: pointer; }

        @media (max-width: 768px) { 
            /* Slide the sidebar off the screen by default */
            .sidebar { position: fixed; left: -280px; z-index: 1040; width: 260px; box-shadow: 4px 0 24px rgba(0,0,0,0.15); transition: left 0.3s ease; }
            
            /* Create an "active" class to slide it back in */
            .sidebar.active-mobile { left: 0; }
            
            /* Show the hamburger button */
            .mobile-toggle-btn { display: block; margin-right: 15px; }
            
            /* Reduce padding for smaller screens */
            .page-content { padding: 16px; } 
            .topbar { padding: 14px 16px; }
        }
    </style>
</head>
<body>

<div id="appScreen">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div style="font-size:24px;margin-bottom:6px">🎓</div>
            <h5>Dream Achievers <span>Learning Center</span></h5>
        </div>

        <div class="sidebar-user">
            <div class="avatar" style="background:linear-gradient(135deg,#4F6AF5,#9B6DFF)">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <h6>{{ Auth::user()->name ?? 'Guest User' }}</h6>
                <span>{{ Auth::user()->role ?? 'Role' }}</span>
            </div>
        </div>

        <nav class="sidebar-nav" id="sidebarNav">
    
    @if(Auth::check() && Auth::user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="nav-item-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie"></i>Dashboard
        </a>
        <a href="{{ route('admin.students.index') }}" class="nav-item-custom {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>Student Profiling
        </a>
        <a href="{{ route('admin.report-approval.index') }}" class="nav-item-custom {{ request()->routeIs('admin.report-approval.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-circle-check"></i>Report Approval
        </a>
        <a href="{{ route('admin.accounts.index') }}" class="nav-item-custom {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-shield"></i>User Management
        </a>
        <a href="{{ route('admin.quarters.index') }}" class="nav-item-custom {{ request()->routeIs('admin.quarters.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i>Calendar Setup
        </a>
        <a href="{{ route('admin.archive.index') }}" class="nav-item-custom {{ request()->routeIs('admin.archive.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box-archive"></i>Archiving
        </a>
    
    @elseif(Auth::check() && Auth::user()->role === 'teacher')
        <a href="{{ route('teacher.dashboard') }}" class="nav-item-custom {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie"></i>Dashboard
        </a>
        <a href="{{ route('teacher.iep-goals.index') }}" class="nav-item-custom {{ request()->routeIs('teacher.iep-goals.*') ? 'active' : '' }}">
            <i class="fa-solid fa-bullseye"></i>IEP Goals
        </a>
        <a href="{{ route('teacher.daily-logs.index') }}" class="nav-item-custom {{ request()->routeIs('teacher.daily-logs.*') ? 'active' : '' }}">
            <i class="fa-solid fa-pencil"></i>Daily Logging
        </a>
        <a href="{{ route('teacher.students.index') }}" class="nav-item-custom {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>My Students
        </a>
        
        <a href="{{ route('teacher.reports.index') }}" class="nav-item-custom {{ request()->routeIs('teacher.reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-signature"></i> Reports
        </a>

        @if(Auth::user()->can_approve_reports || Auth::user()->can_create_profiles || Auth::user()->can_manage_calendar || Auth::user()->can_archive_students || Auth::user()->can_manage_credentials)
            <div class="nav-section-label mt-3" style="color: #FFB347;">Admin Privileges</div>
        @endif

        @if(Auth::user()->can_manage_credentials)
            <a href="{{ route('admin.accounts.index') }}" class="nav-item-custom {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}" style="color: #FFB347;">
                <i class="fa-solid fa-key"></i>Manage Logins
            </a>
        @endif

        @if(Auth::user()->can_manage_calendar)
            <a href="{{ route('admin.quarters.index') }}" class="nav-item-custom {{ request()->routeIs('admin.quarters.*') ? 'active' : '' }}" style="color: #FFB347;">
                <i class="fa-solid fa-calendar-days"></i>Calendar Setup
            </a>
        @endif
        
        @if(Auth::user()->can_create_profiles)
            <a href="{{ route('admin.students.index') }}" class="nav-item-custom {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" style="color: #FFB347;">
                <i class="fa-solid fa-user-plus"></i>Create Profiles
            </a>
        @endif

        @if(Auth::user()->can_archive_students)
            <a href="{{ route('admin.archive.index') }}" class="nav-item-custom {{ request()->routeIs('admin.archive.*') ? 'active' : '' }}" style="color: #FFB347;">
                <i class="fa-solid fa-box-archive"></i>Archiving
            </a>
        @endif

        @if(Auth::user()->can_approve_reports)
            <a href="{{ route('admin.report-approval.index') }}" class="nav-item-custom {{ request()->routeIs('admin.report-approval.*') ? 'active' : '' }}" style="color: #FFB347;">
                <i class="fa-solid fa-file-circle-check"></i>Report Approval
            </a>
        @endif

    @elseif(Auth::check() && Auth::user()->role === 'guardian') 
    
    <li class="nav-item mb-1" style="list-style-type: none;">
        <a class="nav-link nav-item-custom {{ request()->routeIs('guardian.dashboard') ? 'active' : '' }}" href="{{ route('guardian.dashboard') }}">
            <i class="fa fa-home"></i> Dashboard
        </a>
    </li>

    <li class="nav-item mb-1" style="list-style-type: none;">
        <a class="nav-link nav-item-custom {{ request()->routeIs('guardian.goals') ? 'active' : '' }}" href="{{ route('guardian.goals') }}">
            <i class="fa fa-bullseye"></i> Goals Module
        </a>
    </li>
    
    <li class="nav-item mb-1" style="list-style-type: none;">
    <a class="nav-link nav-item-custom {{ request()->routeIs('guardian.reports') ? 'active' : '' }}" data-bs-toggle="collapse" href="#reportSubMenu" role="button">
        <i class="fa fa-file-signature"></i> Report Module 
        <i class="fa fa-caret-down ms-auto mt-1"></i>
    </a>
    <div class="collapse {{ request()->routeIs('guardian.reports') ? 'show' : '' }}" id="reportSubMenu">
        <ul class="nav flex-column ms-3 mt-1" style="border-left: 2px solid #4F6AF5; padding-left: 10px;">
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'daily']) }}">Daily Summaries</a></li>
            
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'q1']) }}">Q1 Reports</a></li>
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'q2']) }}">Q2 Reports</a></li>
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'q3']) }}">Q3 Reports</a></li>
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'q4']) }}">Q4 Reports</a></li>
            
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'mid_year']) }}">Mid-Year Reports</a></li>
            <li class="nav-item"><a class="nav-link nav-item-custom py-1" href="{{ route('guardian.reports', ['type' => 'year_end']) }}">Year-End Reports</a></li>
        </ul>
    </div>
    </li>

    <li class="nav-item mb-1" style="list-style-type: none;">
        <a class="nav-link nav-item-custom {{ request()->routeIs('guardian.recommendations') ? 'active' : '' }}" href="{{ route('guardian.recommendations') }}">
            <i class="fa fa-house-user"></i> Home Recommendations
        </a>
    </li>

    @endif 
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" 
               class="logout-btn text-decoration-none">
                <i class="fa fa-sign-out-alt"></i> Log Out
            </a>

            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        
    </aside>

    <div class="main-content">
        <header class="topbar">
            
            <div class="d-flex align-items-center">
                <button class="mobile-toggle-btn" id="mobileMenuBtn">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="topbar-title ms-2">@yield('title', 'Dashboard')</div>
            </div>
            
            <div class="topbar-right">
                
                @if(Auth::check() && Auth::user()->role === 'guardian')
                    @php
                        // Grab all children belonging to this parent securely
                        $myChildren = Auth::user()->children ?? collect();
                        
                        // Figure out who the active child is (Session ID, or default to the first child)
                        $activeChildId = session('active_child_id', $myChildren->first()->id ?? null);
                        $activeChild = $myChildren->where('id', $activeChildId)->first();
                    @endphp

                    @if($myChildren->count() > 0 && $activeChild)
                        <div class="dropdown me-2">
                            <button class="btn btn-sm dropdown-toggle fw-bold d-flex align-items-center gap-2 px-3 py-2" 
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                                    style="background: #EEF1FF; color: var(--primary); border: 1px solid #d0d7ff; border-radius: 10px;">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                    <i class="fa fa-child"></i>
                                </div>
                                Viewing: {{ $activeChild->first_name }}
                            </button>
                            
                            <ul class="dropdown-menu shadow mt-2" style="border-radius: 10px; border: none; min-width: 200px;">
                                <li class="px-3 py-2 border-bottom bg-light">
                                    <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.5px;">My Children</span>
                                </li>
                                
                                @foreach($myChildren as $child)
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center justify-content-between {{ $activeChild->id === $child->id ? 'bg-primary text-white' : '' }}" 
                                           href="{{ route('guardian.switch-child', $child->id) }}">
                                            <span>{{ $child->first_name }} {{ $child->last_name }}</span>
                                            @if($activeChild->id === $child->id)
                                                <i class="fa fa-check small"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif
                <div class="dropdown">
                    <div class="icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                        <i class="fa fa-bell"></i>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em; border: 2px solid white;">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px; border-radius: 12px; border: none; padding: 0; margin-top: 10px;">
                        <li class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light" style="border-radius: 12px 12px 0 0;">
                            <span class="fw-bold m-0" style="color: var(--text-dark);">Notifications</span>
                            <span class="badge bg-primary rounded-pill">{{ Auth::check() ? Auth::user()->unreadNotifications->count() : 0 }} New</span>
                        </li>
                        
                        @if(Auth::check())
                            @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                <li>
                                    <a class="dropdown-item py-3 border-bottom" href="{{ $notification->data['url'] ?? '#' }}" style="white-space: normal;">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fa {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 fw-bold" style="font-size: 14px; color: var(--text-dark);">{{ $notification->data['title'] ?? 'New Alert' }}</p>
                                                <p class="mb-0 text-muted" style="font-size: 12px;">{{ $notification->data['message'] ?? 'You have a new system notification.' }}</p>
                                                <small class="text-primary" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li>
                                    <div class="p-4 text-center text-muted">
                                        <i class="fa fa-bell-slash fs-3 mb-2 opacity-50"></i>
                                        <p class="mb-0 small">No new notifications</p>
                                    </div>
                                </li>
                            @endforelse
                        @endif
                        
                        <li class="p-2 text-center border-top">
                            @if(Auth::check() && Auth::user()->role === 'guardian')
                                <a href="{{ route('guardian.notifications') }}" class="text-decoration-none small text-primary fw-bold">View All Notifications</a>
                            @else
                                <a href="#" class="text-decoration-none small text-primary fw-bold">View All Notifications</a>
                            @endif
                        </li>
                    </ul>
                </div>

                <div class="dropdown">
                    <div class="avatar" data-bs-toggle="dropdown" aria-expanded="false" style="background:linear-gradient(135deg,#4F6AF5,#9B6DFF);width:36px;height:36px;font-size:14px;cursor:pointer;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2" style="border-radius: 12px; border: none; min-width: 220px; padding: 8px 0;">
                        <li class="px-3 py-2 border-bottom mb-2 bg-light" style="margin-top: -8px;">
                            <span class="fw-bold d-block text-truncate" style="color: var(--text-dark);">{{ Auth::user()->name ?? 'Guest User' }}</span>
                            <small class="text-muted text-uppercase" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">{{ Auth::user()->role ?? 'Role' }} Account</small>
                        </li>
                        
                        <li>
                            <a class="dropdown-item px-3 py-2" href="{{ route('profile.index') ?? '#' }}">
                                <i class="fa fa-user me-2"></i> My Profile
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider my-2"></li>
                        
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger fw-bold" style="background:transparent; border:none; width:100%; text-align:left;">
                                    <i class="fa fa-sign-out-alt me-2" style="width: 16px;"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');

        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevents click from immediately hiding it again
                sidebar.classList.toggle('active-mobile');
            });

            // Close sidebar if user clicks outside of it on a mobile phone
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnButton = mobileBtn.contains(event.target);

                    // If they click on the content area (not the menu or the button), slide it closed
                    if (!isClickInsideSidebar && !isClickOnButton && sidebar.classList.contains('active-mobile')) {
                        sidebar.classList.remove('active-mobile');
                    }
                }
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>