@extends('layouts.app')

@section('title', 'My Reports')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="data-card mb-4 shadow-sm" style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden;">
    <div class="p-3 bg-light border-bottom">
        <h6 class="fw-bold m-0 text-dark"><i class="fa fa-filter me-2 text-primary"></i>Filter Reports</h6>
    </div>
    <div class="p-3">
        <form action="{{ route('teacher.reports.index') }}" method="GET" class="row g-3 align-items-end">
            
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted mb-1"><i class="fa fa-search me-1"></i> Search Keyword</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-font text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search name or type..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted mb-1"><i class="fa fa-user-graduate me-1"></i> Specific Student</label>
                <select name="student_id" class="form-select border-primary" style="background-color: #f8f9ff;">
                    <option value="">All My Students</option>
                    @if(isset($myStudents))
                        @foreach($myStudents as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted mb-1"><i class="fa fa-file-alt me-1"></i> Report Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="daily" {{ request('type') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="quarterly" {{ request('type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                    Filter
                </button>
                
                @if(request('search') || request('student_id') || request('type'))
                    <a href="{{ route('teacher.reports.index') }}" class="btn btn-outline-danger px-3" title="Clear Filters" style="border-radius: 8px;">
                        <i class="fa fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="data-card">
    <div class="data-card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0"><i class="fa fa-file-signature me-2" style="color:var(--primary)"></i> My Generated Reports</h5>
        
        <a href="{{ route('teacher.reports.create') }}" class="btn-sm-custom btn-primary-sm text-decoration-none shadow-sm">
            <i class="fa fa-wand-magic-sparkles me-1"></i> Generate New Report
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Report Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="fw-bold">{{ $report->student->first_name }} {{ $report->student->last_name }}</td>
                        
                        <td>
                            <span class="badge-custom" style="background:#EEF1FF;color:var(--primary)">
                                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        
                        <td class="text-muted small">
                            @if($report->report_type === 'daily' && $report->report_date)
                                {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                            @else
                                {{ $report->created_at->format('M d, Y') }}
                            @endif
                        </td>
                        
                        <td>
                            @php
                                $statusColors = [
                                    'draft' => 'background:#e2e8f0;color:#475569',
                                    'pending_approval' => 'background:#fef3c7;color:#d97706',
                                    'approved' => 'background:#dcfce7;color:#15803d',
                                    'rejected' => 'background:#fee2e2;color:#b91c1c',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'pending_approval' => 'Pending Approval',
                                    'approved' => 'Approved',
                                    'rejected' => 'Needs Revision',
                                ];
                            @endphp
                            <span class="badge-custom" style="{{ $statusColors[$report->status] ?? 'background:#e2e8f0;color:#475569' }}">
                                {{ $statusLabels[$report->status] ?? 'Unknown' }}
                            </span>
                        </td>
                        
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#viewReportModal{{ $report->id }}">
                                    <i class="fa fa-eye me-1"></i> View
                                </button>
                                
                                <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="btn btn-sm btn-danger text-white">
                                    <i class="fa fa-file-pdf me-1"></i> PDF
                                </a>

                                <form action="{{ route('teacher.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this report? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary text-white" title="Delete Report">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="viewReportModal{{ $report->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content text-start" style="border-radius:16px;border:none;">
                                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                    <div>
                                        <h5 class="modal-title fw-800 mb-0">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }}</h5>
                                        <small class="text-muted">Generated on {{ $report->created_at->format('F j, Y') }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <div class="modal-body p-4">
                                    <div class="p-4" style="background: #f8f9fa; border-radius: 8px; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6; border: 1px solid #e2e8f0;">{{ $report->content }}</div>
                                    
                                    <div class="mt-4 border-top pt-4">
                                        @include('components.comment-thread', ['item' => $report])
                                    </div>
                                </div>
                                
                                <div class="modal-footer" style="padding:16px 24px; display: flex; justify-content: space-between;">
                                    <a href="{{ route('teacher.reports.pdf', $report->id) }}" class="btn btn-outline-danger">
                                        <i class="fa fa-file-pdf me-2"></i> Download as PDF
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa fa-file-alt fs-2 mb-3 text-light"></i><br>
                            No reports generated yet.<br>
                            <a href="{{ route('teacher.reports.create') }}" class="btn btn-sm btn-primary mt-3">Generate Your First Report</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($reports, 'links'))
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $reports->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection