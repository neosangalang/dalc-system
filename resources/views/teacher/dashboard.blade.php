@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --t-bg: #F8FAFC;
        --t-primary: #4F6AF5; 
        --t-primary-light: #EEF2FF;
        --t-success: #10B981; 
        --t-success-light: #ECFDF5;
        --t-warning: #F59E0B; 
        --t-warning-light: #FFFBEB;
        --t-accent: #8B5CF6; 
        --t-accent-light: #F5F3FF;
        --t-text-dark: #1E293B;
        --t-text-muted: #64748B;
        --t-border: #E2E8F0;
    }

    body {
        background-color: var(--t-bg);
        font-family: 'Inter', sans-serif;
        color: var(--t-text-dark);
    }

    /* Clean Workspace Cards */
    .workspace-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid var(--t-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .workspace-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--t-border);
        background: #FFFFFF;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Top Action Metrics with Bounce Hover */
    .metric-box {
        padding: 20px;
        border-radius: 16px;
        background: #FFFFFF;
        border: 1px solid var(--t-border);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .metric-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(79, 106, 245, 0.15);
    }
    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* Streamlined Calendar */
    .quarter-box {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--t-border);
        background: #F8FAFC;
        text-align: center;
        min-width: 200px;
        flex: 1;
        transition: all 0.2s;
    }
    .quarter-box.active {
        background: linear-gradient(135deg, var(--t-primary), var(--t-accent));
        color: white !important;
        border: none;
        box-shadow: 0 4px 12px rgba(79, 106, 245, 0.3);
        transform: scale(1.02);
    }
    .quarter-box.active .text-muted { color: rgba(255,255,255,0.8) !important; }

    /* Student List with Quick Action */
    .student-row {
        padding: 16px 24px;
        border-bottom: 1px solid var(--t-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s, transform 0.2s;
    }
    .student-row:hover { 
        background: #f8f9ff; 
        transform: translateX(4px);
    }
    .student-row:last-child { border-bottom: none; }
    
    .quick-log-btn {
        background: var(--t-primary-light);
        color: var(--t-primary);
        border: 1px solid transparent;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.2s;
    }
    .quick-log-btn:hover {
        background: var(--t-primary);
        color: white;
        box-shadow: 0 4px 6px rgba(79, 106, 245, 0.2);
    }

    /* Custom scrollbar for calendar */
    .custom-scroll::-webkit-scrollbar { height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="container-fluid py-2">
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="metric-box">
                <div class="metric-icon" style="background: var(--t-primary-light); color: var(--t-primary);">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <h3 class="fw-bold m-0 counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
                    <p class="m-0 small fw-bold text-muted text-uppercase">My Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-box">
                <div class="metric-icon" style="background: var(--t-success-light); color: var(--t-success);">
                    <i class="fa fa-pencil"></i>
                </div>
                <div>
                    <h3 class="fw-bold m-0 counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
                    <p class="m-0 small fw-bold text-muted text-uppercase">Logs This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-box">
                <div class="metric-icon" style="background: var(--t-accent-light); color: var(--t-accent);">
                    <i class="fa fa-bullseye"></i>
                </div>
                <div>
                    <h3 class="fw-bold m-0 counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
                    <p class="m-0 small fw-bold text-muted text-uppercase">Active IEP Goals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
            <div class="metric-box" style="{{ $hasReports ? 'border-color: var(--t-warning); background: var(--t-warning-light);' : '' }}">
                <div class="metric-icon" style="background: {{ $hasReports ? '#FDE68A' : '#F1F5F9' }}; color: {{ $hasReports ? 'var(--t-warning)' : 'var(--t-text-muted)' }};">
                    <i class="fa fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="fw-bold m-0 counter" data-target="{{ $reportsDue ?? 0 }}" style="color: {{ $hasReports ? 'var(--t-warning)' : 'inherit' }}">0</h3>
                    <p class="m-0 small fw-bold text-uppercase" style="color: {{ $hasReports ? 'var(--t-warning)' : 'var(--t-text-muted)' }}">Reports Due</p>
                </div>
            </div>
        </div>
    </div>

    <div class="workspace-card mb-4">
        <div class="workspace-header">
            <h6 class="fw-bold m-0"><i class="fa fa-calendar-alt text-primary me-2"></i> Academic Calendar Timeline</h6>
            @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
            @if($activeQ)
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Current: {{ $activeQ->name }}</span>
            @endif
        </div>
        <div class="p-4 d-flex gap-3 overflow-auto custom-scroll pb-4">
            @forelse($quarters ?? [] as $quarter)
                @if($quarter->is_active)
                    <div class="quarter-box active">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold m-0 text-white">{{ $quarter->name }}</h5>
                            <span class="badge bg-white text-primary rounded-pill px-2 py-1" style="font-size: 10px;">ACTIVE</span>
                        </div>
                        <p class="m-0 small text-white text-start opacity-75 fw-bold">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                        </p>
                    </div>
                @else
                    <div class="quarter-box">
                        <h5 class="fw-bold m-0 text-dark text-start mb-2">{{ $quarter->name }}</h5>
                        <p class="m-0 small text-muted text-start fw-bold">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                        </p>
                    </div>
                @endif
            @empty
                <div class="w-100 text-center py-3 text-muted">No calendar data configured by the administrator yet.</div>
            @endforelse
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="workspace-card h-100">
                <div class="workspace-header">
                    <h6 class="fw-bold m-0"><i class="fa fa-user-graduate text-success me-2"></i> My Students</h6>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-sm btn-light border fw-bold rounded-pill">View All</a>
                </div>
                <div class="d-flex flex-column">
                    @forelse($myStudents ?? [] as $student)
                        <div class="student-row">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" 
                                     style="width: 46px; height: 46px; background: linear-gradient(135deg, var(--t-primary), var(--t-accent)); color: white;">
                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="m-0 fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                    <p class="m-0 small text-muted"><i class="fa fa-bullseye text-primary me-1"></i> Assigned Student</p>
                                </div>
                            </div>
                            <a href="{{ route('teacher.daily-logs.index', ['student_id' => $student->id]) }}" class="quick-log-btn text-decoration-none">
                                <i class="fa fa-pen me-1"></i> Log
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-user-slash fs-1 mb-3 opacity-50"></i>
                            <p class="mb-0">No students are currently assigned to your roster.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="workspace-card h-100">
                <div class="workspace-header">
                    <h6 class="fw-bold m-0"><i class="fa fa-bullseye text-purple me-2"></i> IEP Goal Progress (Quick View)</h6>
                </div>
                
                <div class="p-4 h-100 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="p-5 w-100 rounded-4" style="background: #F8FAFC; border: 2px dashed var(--t-border);">
                        <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: var(--t-primary-light); color: var(--t-primary);">
                            <i class="fa fa-bullseye fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Track Your Students' Milestones</h5>
                        <p class="text-muted small mb-4 px-3">Assign IEP goals to your students to see their progress bars automatically populate here.</p>
                        <a href="{{ route('teacher.iep-goals.index') }}" class="btn fw-bold px-4" style="background: var(--t-primary); color: white; border-radius: 8px;">
                            Manage IEP Goals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 200;
                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target;
                }
            };
            setTimeout(updateCount, 100);
        });
    });
</script>

@endsection