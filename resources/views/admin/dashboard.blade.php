@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --admin-bg: #F1F5F9; 
        --admin-primary: #1E293B; 
        --admin-accent: #4F46E5; 
        --admin-success: #10B981;
        --admin-warning: #F59E0B;
        --admin-danger: #EF4444;
        --admin-border: #E2E8F0;
    }

    body {
        background-color: var(--admin-bg);
        font-family: 'Inter', sans-serif;
    }

    /* Command Metric Cards */
    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--admin-border);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .metric-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }

    /* Action Grid */
    .action-btn {
        background: white;
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 20px 10px;
        text-align: center;
        transition: all 0.2s;
        height: 100%;
    }
    .action-btn:hover {
        border-color: var(--admin-accent);
        background: #F5F3FF;
        transform: scale(1.02);
    }
    .action-btn i {
        font-size: 24px;
        margin-bottom: 8px;
        display: block;
    }
    .action-btn span {
        font-weight: 700;
        font-size: 13px;
        color: var(--admin-primary);
    }

    /* Professional Table */
    .admin-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--admin-border);
    }
    .admin-table thead th {
        background: #F8FAFC;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.05em;
        color: #64748B;
        padding: 16px 20px;
        border-bottom: 1px solid var(--admin-border);
    }
</style>

<div class="container-fluid py-2">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon-box" style="background: #EEF2FF; color: var(--admin-accent);">
                    <i class="fa fa-users"></i>
                </div>
                <h3 class="fw-800 m-0">{{ $totalStudents }}</h3>
                <p class="text-muted small fw-600 mb-0">Active Students</p>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon-box" style="background: #F0FDF4; color: var(--admin-success);">
                    <i class="fa fa-chalkboard-user"></i>
                </div>
                <h3 class="fw-800 m-0">{{ $activeTeachers }}</h3>
                <p class="text-muted small fw-600 mb-0">Active Teachers</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-icon-box" style="background: #FDF2F8; color: #EC4899;">
                    <i class="fa fa-heart"></i>
                </div>
                <h3 class="fw-800 m-0">{{ $guardians }}</h3>
                <p class="text-muted small fw-600 mb-0">Registered Guardians</p>
            </div>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.report-approval.index') }}" class="text-decoration-none">
                <div class="metric-card" style="border-left: 4px solid {{ $pendingReports > 0 ? 'var(--admin-danger)' : 'var(--admin-success)' }}">
                    <div class="metric-icon-box" style="background: {{ $pendingReports > 0 ? '#FEF2F2' : '#F0FDF4' }}; color: {{ $pendingReports > 0 ? 'var(--admin-danger)' : 'var(--admin-success)' }};">
                        <i class="fa fa-file-signature"></i>
                    </div>
                    <h3 class="fw-800 m-0">{{ $pendingReports }}</h3>
                    <p class="small fw-bold mb-0 {{ $pendingReports > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $pendingReports > 0 ? 'Needs Review' : 'Reports Cleared' }}
                    </p>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-table">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="fa fa-history me-2 text-muted"></i>Recent Student Enrollment</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Assigned Teacher</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentStudents as $student)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="student-avatar" style="width:38px; height:38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #F1F5F9; color: var(--admin-accent); font-weight: 800; border: 1px solid var(--admin-border)">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-800 text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                            <small class="text-muted">ID: {{ $student->student_id_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 small">{{ $student->teacher->name ?? 'Unassigned' }}</div>
                                    <div class="text-muted" style="font-size: 11px;">Guardian: {{ $student->guardian->name ?? 'None' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $student->status === 'active' ? 'text-success' : 'text-secondary' }} px-3 py-2 rounded-pill fw-bold" style="font-size: 11px;">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-light border rounded-circle" style="width:32px; height:32px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border">
                <h6 class="fw-800 text-dark mb-4 text-uppercase" style="letter-spacing: 1px; font-size: 12px;">Admin Toolbox</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('admin.accounts.index') }}" class="action-btn d-block text-decoration-none">
                            <i class="fa fa-user-plus text-primary"></i>
                            <span>Accounts</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.students.index') }}" class="action-btn d-block text-decoration-none">
                            <i class="fa fa-user-graduate text-success"></i>
                            <span>Students</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.quarters.index') }}" class="action-btn d-block text-decoration-none">
                            <i class="fa fa-calendar-check text-warning"></i>
                            <span>Calendar</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.archive.index') }}" class="action-btn d-block text-decoration-none">
                            <i class="fa fa-box-archive text-purple"></i>
                            <span>Archives</span>
                        </a>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-3" style="background: #F8FAFC; border: 1px dashed var(--admin-border);">
                    <h6 class="fw-bold small mb-2"><i class="fa fa-lightbulb text-warning me-2"></i>Quick Fact</h6>
                    <p class="text-muted mb-0" style="font-size: 12px;">You have <strong>{{ $pendingReports }}</strong> reports waiting for your signature. Reports stay hidden from guardians until approved.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection