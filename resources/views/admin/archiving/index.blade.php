@extends('layouts.app')

@section('title', 'System Archiving')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        
        <div class="data-card mb-4">
            <div class="data-card-header">
                <h5><i class="fa fa-box-archive me-2" style="color:var(--purple)"></i>Year-End Archiving</h5>
                <form action="{{ route('admin.archive.run') }}" method="POST" onsubmit="return confirm('Are you sure you want to run the year-end archive? This cannot be undone.');">
                    @csrf
                    <input type="hidden" name="school_year" value="2024-2025">
                    <button type="submit" class="btn-sm-custom btn-primary-sm" onclick="runArchiveDemo()">
                        <i class="fa fa-play"></i> Run Archive
                    </button>
                </form>
            </div>
            
            <div class="mb-3 p-3" style="background:#FFF7ED;border-radius:10px;border:1px solid #FFE4B5">
                <strong style="font-size:13px">⚠️ Year-end Archive for SY 2024-2025</strong>
                <p style="font-size:12px;color:var(--text-muted);margin:4px 0 0">
                    This will snapshot all student records into JSON format, generate master PDFs for safekeeping, and optionally deactivate teacher/guardian accounts.
                </p>
            </div>
            
            <div class="archive-timeline mt-4">
                <div class="archive-step done">
                    <p class="fw-bold" style="font-size:13px;margin-bottom:2px">Snapshot Student Data</p>
                    <p style="font-size:12px;color:var(--text-muted)">Capture all profiles, IEP goals, and progress notes</p>
                </div>
                <div class="archive-step done">
                    <p class="fw-bold" style="font-size:13px;margin-bottom:2px">Generate Master PDFs</p>
                    <p style="font-size:12px;color:var(--text-muted)">Create one comprehensive PDF file per student</p>
                </div>
                <div class="archive-step" id="archiveStep3">
                    <p class="fw-bold" style="font-size:13px;margin-bottom:2px">Soft-Delete Records</p>
                    <p style="font-size:12px;color:var(--text-muted)">Mark processed students as archived in the active database</p>
                </div>
            </div>
        </div>

        <div class="data-card mb-4" style="background: #f8fffb; border-radius: 12px; padding: 24px; border-left: 4px solid #34C97B; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
            <div class="d-flex align-items-center mb-2">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="fa fa-robot fs-5"></i>
                </div>
                <h5 class="fw-bold m-0 text-dark">Automated Quarterly Rollover Active</h5>
            </div>
            <p class="text-muted small mb-0 ms-5">
                This system is configured with automated background tasks (Cron Jobs). The system will securely archive all approved daily reports and clear active Teacher dashboards automatically at midnight on the closing date of the current quarter. <strong>No manual initialization is required.</strong>
            </p>
        </div>

    </div>
    
    <div class="col-lg-5">
        <div class="data-card" style="margin-bottom:0; height: 100%;">
            <div class="data-card-header">
                <h5>Archived Records</h5>
            </div>
            
            <div class="p-3">
                @forelse($archives as $archive)
                    <div class="notif-item mb-3">
                        <div class="notif-dot" style="background:var(--primary)"></div>
                        <div>
                            <h6>{{ $archive->student->first_name ?? 'Unknown' }} {{ $archive->student->last_name ?? 'Student' }} – SY {{ $archive->school_year }}</h6>
                            <p class="text-muted small mb-1">Archived on: {{ $archive->archived_at->format('M j, Y') }}</p>
                            @if($archive->master_pdf_path)
                                <a href="{{ asset('storage/' . $archive->master_pdf_path) }}" target="_blank" class="btn-sm-custom btn-outline-sm mt-1 text-decoration-none" style="font-size:11px">
                                    <i class="fa fa-file-pdf"></i> Download PDF
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-folder-open fs-1 mb-3 text-light"></i>
                        <p class="mb-0">No archived records found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Just a visual flair for the UI if they click the button (the real action happens via PHP reload)
    function runArchiveDemo() {
        setTimeout(() => { document.getElementById('archiveStep3').classList.add('done'); }, 800);
    }
</script>
@endpush