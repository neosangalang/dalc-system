@extends('layouts.app')

@section('title', 'My Students')

@section('content')

<div class="data-card">
    <div class="data-card-header">
        <h5><i class="fa fa-users me-2" style="color:var(--primary)"></i>My Assigned Students</h5>
    </div>
    
    <div class="mb-3 mt-2">
        <input type="text" id="studentSearchInput" class="form-control" placeholder="🔍 Search by student name or exceptionality..." style="max-width:340px">
    </div>
    
    <table class="table-custom">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Age</th>
                <th>Exceptionality</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr class="student-row">
                    <td class="fw-bold">{{ $student->last_name }}, {{ $student->first_name }}</td>
                    
                    <td>{{ \Carbon\Carbon::parse($student->date_of_birth)->age }} yrs</td>
                    
                    <td>
                        <span class="badge-custom" style="background:#EEF1FF;color:var(--primary)">
                            {{ $student->exceptionality ?? 'Unspecified' }}
                        </span>
                    </td>
                    
                    <td>
                        @if($student->status === 'active')
                            <span class="badge-custom badge-approved">Active</span>
                        @else
                            <span class="badge-custom badge-pending">Inactive</span>
                        @endif
                    </td>
                    
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('teacher.iep-goals.index') }}" class="btn-sm-custom btn-success-sm" title="View IEP Goals">
                                <i class="fa fa-bullseye"></i> IEP
                            </a>
                            
                            <a href="{{ route('teacher.iep-goals.pdf', $student->id) }}" class="btn-sm-custom text-decoration-none" style="background: #dc3545; color: white; border: none;" title="Print Full IEP" target="_blank">
                                <i class="fa fa-file-pdf"></i> Print IEP
                            </a>

                            <a href="{{ route('teacher.daily-logs.index') }}" class="btn-sm-custom" style="background: var(--purple); color: white; border: none;" title="Add Daily Log">
                                <i class="fa fa-pencil"></i> Log
                            </a>

                            @if(Auth::user()->can_edit_students)
                                <button class="btn-sm-custom btn-primary-sm" title="Edit Student Profile">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            @else
                                <button class="btn-sm-custom" style="background: #e9ecef; color: #adb5bd; border: none; cursor: not-allowed;" title="Editing locked by Admin" disabled>
                                    <i class="fa fa-lock"></i> Locked
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fa fa-user-graduate fs-1 mb-3 text-light"></i>
                        <p class="mb-0">No students are currently assigned to your class.</p>
                        <small>If this is a mistake, please contact your Administrator.</small>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('studentSearchInput');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Target all rows that have the class 'student-row'
            const tableRows = document.querySelectorAll('table tbody tr.student-row');

            tableRows.forEach(row => {
                // Grab the text of the entire row (Name, Age, Exceptionality)
                const rowText = row.textContent.toLowerCase();

                // If the text includes the search term, show it. Otherwise, hide it.
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

@endsection