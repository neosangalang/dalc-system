@extends('layouts.app')

@section('title', 'Official Reports')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Official Student Reports</h3>
        <p class="text-muted">Review progress summaries and official documentation for <strong>{{ $activeChild->first_name ?? 'your child' }}</strong>.</p>
    </div>
    
    @if(isset($activeChild) && isset($myChildren))
    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn btn-light border bg-white rounded-pill px-3 py-2 fw-bold text-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="width: 24px; height: 24px; background: #4F6AF5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                    <i class="fa fa-user"></i>
                </div>
                Viewing: {{ $activeChild->first_name }}
            </button>
            
            <ul class="dropdown-menu shadow border-0 mt-2 p-2" style="border-radius: 12px; min-width: 200px;">
                <li><h6 class="dropdown-header text-muted fw-bold" style="letter-spacing: 1px; font-size: 11px;">MY CHILDREN</h6></li>
                
                @foreach($myChildren as $child)
                    <li>
                        <a class="dropdown-item py-2 rounded {{ $activeChild->id === $child->id ? 'bg-primary text-white fw-bold' : '' }}" 
                           href="{{ route('guardian.switch-child', $child->id) }}">
                            {{ $child->first_name }} {{ $child->last_name }}
                            @if($activeChild->id === $child->id)
                                <i class="fa fa-check float-end mt-1"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>

@if(isset($activeChild))
<div class="data-card">
    <div class="data-card-header border-bottom pb-3 mb-0">
        <h5 class="fw-bold m-0"><i class="fa fa-file-signature text-primary me-2"></i> Document Library</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Report Type</th>
                    <th>Prepared By</th>
                    <th>Date Generated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td class="fw-bold text-dark">{{ $report->student->first_name }} {{ $report->student->last_name }}</td>
                        
                        <td>
                            <span class="badge-custom" style="background:#EEF1FF;color:var(--primary)">
                                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        
                        <td class="text-muted small">
                            Teacher {{ $report->teacher->name ?? 'N/A' }}
                        </td>

                        <td class="text-muted small">
                            @if($report->report_type === 'daily' && $report->report_date)
                                {{ \Carbon\Carbon::parse($report->report_date)->format('M d, Y') }}
                            @else
                                {{ $report->created_at->format('M d, Y') }}
                            @endif
                        </td>
                        
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn-sm-custom btn-primary-sm" data-bs-toggle="modal" data-bs-target="#viewReportModal{{ $report->id }}">
                                    <i class="fa fa-book-open"></i> Read
                                </button>
                                
                                <a href="{{ route('guardian.reports.pdf', $report->id) }}" class="btn-sm-custom text-decoration-none" style="background: #dc3545; color: white; border: none;" title="Download PDF">
                                    <i class="fa fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="viewReportModal{{ $report->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content text-start" style="border-radius:16px;border:none;">
                                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                    <div>
                                        <h5 class="modal-title fw-800 mb-0">{{ $report->student->first_name }}'s {{ ucwords(str_replace('_', ' ', $report->report_type)) }} Report</h5>
                                        <small class="text-muted">Generated on {{ $report->created_at->format('F j, Y') }}</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <div class="modal-body p-4">
                                    @if($report->report_type === 'daily')
                                        <p class="mb-3 text-dark fw-bold">Dear Parent/Guardian,</p>
                                        
                                        @php
                                            $reportDate = $report->report_date ? $report->report_date : $report->created_at->format('Y-m-d');
                                            $dailyLogWithPhoto = \App\Models\DailyLog::where('student_id', $report->student_id)
                                                ->whereDate('log_date', $reportDate)
                                                ->whereNotNull('image_path')
                                                ->latest()
                                                ->first();
                                        @endphp

                                        @if($dailyLogWithPhoto)
                                            <div class="text-center mb-4 p-2 bg-light rounded" style="border: 1px dashed #cbd5e1;">
                                                <img src="{{ asset('storage/' . $dailyLogWithPhoto->image_path) }}" alt="Student Progress Photo" class="img-fluid rounded shadow-sm" style="max-height: 300px; border: 3px solid #ffffff;">
                                            </div>
                                        @endif
                                        
                                        <div class="p-4 shadow-sm" style="background: #ffffff; border-radius: 12px; font-size: 1.05rem; line-height: 1.8; border-left: 4px solid var(--primary); color: #4a5568; white-space: pre-wrap;">{{ $report->content }}</div>
                                        
                                        <p class="mt-4 mb-0 text-muted fst-italic">Warmly,<br>Teacher {{ $report->teacher->name ?? 'Staff' }}</p>
                                    @else
                                        <div class="p-4" style="background: #f8f9fa; border-radius: 8px; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6; border: 1px solid #e2e8f0; color: #333;">{{ $report->content }}</div>
                                    @endif

                                    <div class="mt-5 border-top pt-4">
                                        @include('components.comment-thread', ['item' => $report])
                                    </div>

                                </div>
                                
                                <div class="modal-footer" style="padding:16px 24px;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Window</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa fa-folder-open fs-2 mb-3 text-light"></i><br>
                            No official reports have been published for {{ $activeChild->first_name }} yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
    <i class="fa fa-info-circle me-2"></i> No children are currently linked to your account.
</div>
@endif

@endsection