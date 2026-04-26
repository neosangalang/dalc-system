@extends('layouts.app')

@section('title', 'Report Approval')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-exclamation-triangle me-2"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <h5><i class="fa fa-file-circle-check me-2" style="color:var(--success)"></i> Pending Reports for Approval</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th class="py-3 px-4">Student</th>
                    <th class="py-3">Report Type</th>
                    <th class="py-3">Submitted By (Teacher)</th>
                    <th class="py-3">Date Submitted</th>
                    <th class="py-3 text-end px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingReports as $report)
                    <tr>
                        <td class="px-4 fw-bold text-dark">{{ $report->student->first_name }} {{ $report->student->last_name }}</td>
                        <td>
                            <span class="badge-custom" style="background:#EEF1FF;color:var(--primary)">
                                {{ strtoupper(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        <td>{{ $report->teacher->name ?? 'Unknown Teacher' }}</td>
                        <td class="text-muted small">{{ $report->updated_at->format('M d, Y - h:i A') }}</td>
                        <td class="text-end px-4">
                            
                            <button class="btn btn-sm btn-outline-primary me-2">
                                <i class="fa fa-eye"></i> View
                            </button>

                            <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-sm btn-success text-white fw-bold shadow-sm" onclick="return confirm('Approve this report? This will immediately email the parents.');">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                            </form>

                            <form action="{{ route('admin.report-approval.update-status', $report->id) }}" method="POST" class="d-inline ms-1">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-sm btn-danger text-white fw-bold shadow-sm">
                                    <i class="fa fa-xmark"></i> Reject
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa fa-check-double fs-1 mb-3 text-success opacity-50"></i><br>
                            All caught up! There are no reports waiting for your approval.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection