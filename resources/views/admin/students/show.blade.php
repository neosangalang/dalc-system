@extends('layouts.app')

@section('title', "{$student->first_name}'s Master File")

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fa fa-user-graduate me-2 text-primary"></i> {{ $student->first_name }} {{ $student->last_name }}'s Master File</h4>
    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Back to Student List
    </a>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body text-center p-4">
                <div class="mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; background:linear-gradient(135deg,#4F6AF5,#9B6DFF); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 28px;">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h5>
                <p class="text-muted small mb-2">ID: {{ $student->student_id_number ?? 'N/A' }}</p>
                
                @if($student->status === 'active')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">Currently Enrolled</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">Archived</span>
                @endif
            </div>
            
            <div class="card-body border-top p-4">
                <h6 class="fw-bold text-muted mb-3" style="font-size: 11px; letter-spacing: 1px;">MEDICAL & ACADEMIC INFO</h6>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Diagnosis/Disability:</span>
                    <span class="fw-bold small text-end">{{ $student->exceptionality ?? 'Not Specified' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Date of Birth:</span>
                    <span class="fw-bold small">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M j, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Gender:</span>
                    <span class="fw-bold small">{{ $student->gender }}</span>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <span class="text-danger small fw-bold d-block mb-1"><i class="fa fa-notes-medical me-1"></i> Medical / Accommodations:</span>
                    <p class="small text-muted mb-0" style="background: #fff5f5; padding: 10px; border-radius: 8px; border: 1px solid #ffe3e3;">
                        {{ $student->medical_history ?: 'No specific medical history or accommodations recorded.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-3" style="font-size: 11px; letter-spacing: 1px;">ASSIGNED PERSONNEL</h6>
                
                <div class="d-flex align-items-center p-2 mb-2" style="background: #f0f4ff; border-radius: 8px;">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;"><i class="fa fa-chalkboard-teacher" style="font-size: 12px;"></i></div>
                    <div>
                        <div class="small fw-bold text-primary" style="font-size: 11px;">Special Ed Teacher</div>
                        <div class="small fw-bold text-dark">{{ $student->teacher->name ?? 'Unassigned' }}</div>
                    </div>
                </div>

                <div class="d-flex align-items-center p-2" style="background: #fff0f4; border-radius: 8px;">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px;"><i class="fa fa-heart" style="font-size: 12px;"></i></div>
                    <div>
                        <div class="small fw-bold text-danger" style="font-size: 11px;">Primary Guardian</div>
                        <div class="small fw-bold text-dark">{{ $student->guardian->name ?? 'Unassigned' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-primary"><i class="fa fa-bullseye me-2"></i> IEP Goal Progress</h6>
            </div>
            
            <div class="card-body p-4" style="max-height: 800px; overflow-y: auto;">
                @forelse($student->iepGoals as $goal)
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge" style="background-color: #e0e7ff; color: #4F6AF5; font-size: 13px; padding: 6px 12px;">
                                {{ strtoupper($goal->domain) }}
                            </span>
                            <span class="fw-bold text-primary small">Overall Progress: {{ $goal->progress_percentage }}%</span>
                        </div>

                        @php
                            // Parsing Logic (Just like the PDF!)
                            $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                        @endphp

                        <div class="d-flex flex-column gap-3">
                            @foreach($subGoals as $subGoalText)
                                @php
                                    $subGoalText = trim($subGoalText);
                                    if (empty($subGoalText)) continue;

                                    $objective = $subGoalText;
                                    $activitiesText = '';

                                    if (preg_match('/ACTIVITIES:/i', $subGoalText)) {
                                        $parts = preg_split('/ACTIVITIES:/i', $subGoalText);
                                        $objective = preg_replace('/OBJECTIVE:/i', '', $parts[0]);
                                        $activitiesText = $parts[1];
                                    }

                                    $objective = trim($objective);
                                    // Fix Names
                                    $objective = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $objective);
                                    $activitiesText = str_ireplace(['[Student Name]', '[Student\'s Name]'], $student->first_name, $activitiesText);
                                    
                                    // Turn activities string into an array of lines based on the checkboxes
                                    $activitiesArray = preg_split('/\[ \]|\[x\]|\[X\]|\[\]|•/', $activitiesText);
                                @endphp

                                @if(!empty($objective))
                                    <div class="p-3" style="background-color: #f8f9fa; border-left: 3px solid #4F6AF5; border-radius: 0 8px 8px 0;">
                                        <strong class="text-dark d-block mb-1" style="font-size: 13px;">Objective:</strong>
                                        <p class="text-muted small mb-2">{{ $objective }}</p>

                                        @if(count(array_filter($activitiesArray)) > 0)
                                            <strong class="text-dark d-block mb-1 mt-3" style="font-size: 13px;">Activities / Strategies:</strong>
                                            <ul class="text-muted small mb-0 ps-3">
                                                @foreach($activitiesArray as $activity)
                                                    @if(trim($activity))
                                                        <li class="mb-1">{{ trim($activity) }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fa fa-folder-open text-muted fs-1 mb-3 opacity-50"></i>
                        <p class="text-muted">No IEP Goals have been created for this student yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection