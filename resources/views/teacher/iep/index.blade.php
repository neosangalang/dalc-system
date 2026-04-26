@extends('layouts.app')

@section('title', 'IEP & Goal Management')

@section('content')

<style>
    /* Clean styles for the Student Accordion */
    .student-accordion .accordion-item {
        border: 1px solid var(--border);
        border-radius: 16px !important;
        overflow: hidden;
        margin-bottom: 16px;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: opacity 0.2s ease-in-out;
    }
    .student-accordion .accordion-button {
        padding: 20px 24px;
        font-weight: 600;
    }
    .student-accordion .accordion-button:focus {
        box-shadow: none;
    }
    .student-accordion .accordion-button:not(.collapsed) {
        background-color: #FAFCFF;
        color: inherit;
        box-shadow: inset 0 -1px 0 var(--border);
    }
    .student-accordion .accordion-body {
        padding: 0; /* Remove padding so the table touches the edges */
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold m-0 text-dark"><i class="fa fa-bullseye me-2" style="color:var(--purple)"></i>IEP & Goal Management</h4>
        <p class="text-muted small m-0 mt-1">Manage individualized learning objectives for your students.</p>
    </div>
    
    <div class="d-flex gap-3 align-items-center">
        <div class="input-group shadow-sm" style="width: 260px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border); background: white;">
            <span class="input-group-text bg-white border-0 text-muted" id="search-icon">
                <i class="fa fa-search"></i>
            </span>
            <input type="text" id="studentSearchInput" class="form-control border-0 ps-0" placeholder="Search students..." aria-label="Search students" aria-describedby="search-icon" style="box-shadow: none;">
        </div>

        <button class="btn btn-primary fw-bold shadow-sm" style="border-radius: 10px; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#addGoalModal">
            <i class="fa fa-plus me-1"></i> Add Goal
        </button>
    </div>
</div>

<div class="accordion student-accordion" id="iepAccordion">
    @forelse($myStudents as $student)
        @php
            // Filter the global goals collection for this specific student
            $studentGoals = $goals->where('student_id', $student->id);
            $activeCount = $studentGoals->where('status', 'in_progress')->count();
        @endphp
        
        <div class="accordion-item student-accordion-item" data-student-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
            <h2 class="accordion-header" id="heading{{ $student->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $student->id }}" aria-expanded="false" aria-controls="collapse{{ $student->id }}">
                    
                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold fs-5 shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary), var(--purple));">
                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                            </div>
                            <div class="text-start">
                                <h6 class="mb-0 fw-bold text-dark fs-5">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                <small class="text-muted"><i class="fa fa-id-badge me-1"></i> Assigned Student</small>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">{{ $studentGoals->count() }} Total Goals</span>
                            @if($activeCount > 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill"><i class="fa fa-spinner fa-spin me-1"></i>{{ $activeCount }} In Progress</span>
                            @endif
                        </div>
                    </div>

                </button>
            </h2>
            
            <div id="collapse{{ $student->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $student->id }}" data-bs-parent="#iepAccordion">
                <div class="accordion-body">
                    
                    @if($studentGoals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background: #F8FAFC; color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="ps-4 py-3">Domain</th>
                                        <th>Goal Objective</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentGoals as $goal)
                                        <tr>
                                            <td class="ps-4">
                                                @php
                                                    $color = match($goal->domain) {
                                                        'Language-Communication' => 'background:#EEF1FF;color:var(--primary)',
                                                        'Self-Help'              => 'background:#EDFAF4;color:var(--success)',
                                                        'Perceptuo-Cognitive'    => 'background:#F3EEFF;color:var(--purple)',
                                                        'Socio-Emotional'        => 'background:#FFF4E5;color:#D97706',
                                                        'Psychomotor'            => 'background:#FFE4E6;color:#E11D48',
                                                        default                  => 'background:#F1F5F9;color:#475569',
                                                    };
                                                @endphp
                                                <span class="badge-custom" style="{{ $color }}">{{ str_replace('-', ' - ', $goal->domain) }}</span>
                                            </td>
                                            <td style="font-size:13px; max-width:250px;">
                                                <div class="text-truncate" title="{{ $goal->goal_description }}">
                                                    {{ $goal->goal_description }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="min-width:120px">
                                                    <div class="progress" style="height: 6px; border-radius: 3px; background-color: #E2E8F0; margin-bottom: 4px;">
                                                        <div class="progress-bar" style="width:{{ $goal->progress_percentage }}%; background:{{ $goal->progress_percentage == 100 ? 'var(--success)' : 'var(--primary)' }}"></div>
                                                    </div>
                                                    <small style="color:{{ $goal->progress_percentage == 100 ? 'var(--success)' : 'var(--primary)' }};font-weight:700; font-size: 11px;">
                                                        {{ $goal->progress_percentage == 100 ? 'Achieved ✓' : $goal->progress_percentage . '%' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($goal->status === 'in_progress')
                                                    <span class="badge-custom badge-pending">In Progress</span>
                                                @else
                                                    <span class="badge-custom badge-approved">Achieved</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn btn-sm btn-light border fw-bold text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#editGoalModal{{ $goal->id }}">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                            
                                                    <form action="{{ route('teacher.iep-goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this goal? This cannot be undone.');" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm border fw-bold shadow-sm" style="background-color: #ef4444; color: white;">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                        
                                                <div class="modal fade" id="editGoalModal{{ $goal->id }}" tabindex="-1">
                                                    <div class="modal-dialog modal-lg text-start">
                                                        <div class="modal-content" style="border-radius:16px;border:none;">
                                                            <form action="{{ route('teacher.iep-goals.update', $goal->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                                                    <h5 class="modal-title fw-800">Track IEP Progress for {{ $student->first_name }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <div class="mb-3 interactive-checklist-container">
                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                            <label class="form-label fw-bold m-0">Interactive Activity Tracker</label>
                                                                            <button type="button" class="btn btn-sm btn-link text-muted p-0 toggle-raw-text-btn" style="font-size: 11px; text-decoration: none;">
                                                                                <i class="fa fa-pen me-1"></i>Edit Text
                                                                            </button>
                                                                        </div>
                                                                        <div class="visual-checklist p-3 border rounded shadow-sm" style="background: #fff; max-height: 250px; overflow-y: auto; font-size: 14px;"></div>
                                                                        
                                                                        <textarea name="goal_description" class="form-control goal-raw-text d-none mt-2" rows="6" required>{{ $goal->goal_description }}</textarea>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-6 mb-3">
                                                                            <label class="form-label fw-bold">Calculated Progress (%)</label>
                                                                            <input type="number" name="progress_percentage" class="form-control goal-progress-input" min="0" max="100" value="{{ $goal->progress_percentage }}" required>
                                                                        </div>
                                                                        <div class="col-6 mb-3">
                                                                            <label class="form-label fw-bold">Status</label>
                                                                            <select name="status" class="form-control goal-status-select" required>
                                                                                <option value="in_progress" {{ $goal->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                                                <option value="achieved" {{ $goal->status == 'achieved' ? 'selected' : '' }}>Achieved</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer" style="padding:16px 24px;">
                                                                    <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                                                                        <i class="fa fa-save me-2"></i> Save Progress
                                                                    </button>
                                                                </div>
                                                            </form> 
                                                            <div class="px-4 pb-4 bg-light" style="border-radius: 0 0 16px 16px; border-top: 2px dashed #cbd5e1;">
                                                                @include('components.comment-thread', ['item' => $goal])
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 bg-light">
                            <i class="fa fa-clipboard-list fs-2 mb-3 text-muted opacity-50"></i>
                            <h6 class="fw-bold text-dark mb-1">No Active Goals</h6>
                            <p class="text-muted small mb-3">There are currently no IEP goals assigned to {{ $student->first_name }}.</p>
                            <button class="btn btn-sm btn-outline-primary fw-bold" onclick="$('#collapse{{ $student->id }}').collapse('hide'); $('#addGoalModal').modal('show'); setTimeout(() => { document.querySelector('#addGoalModal select[name=student_id]').value = '{{ $student->id }}'; }, 500);">
                                <i class="fa fa-plus me-1"></i> Create First Goal
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border">
            <i class="fa fa-users-slash fs-1 mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark">No Students Found</h5>
            <p>You have no students assigned to your roster yet.</p>
        </div>
    @endforelse
</div>

<div id="no-search-results" class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border" style="display: none;">
    <i class="fa fa-search fs-1 mb-3 opacity-50"></i>
    <h5 class="fw-bold text-dark">No matches found</h5>
    <p>We couldn't find any students matching that name.</p>
</div>

<div class="modal fade" id="addGoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content" style="border-radius:16px;border:none;">
            <form action="{{ route('teacher.iep-goals.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                    <h5 class="modal-title fw-800">Assign New IEP Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="" disabled selected>Choose a student...</option>
                                @foreach($myStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Development Domain</label>
                            <select id="domain-select" name="domain" class="form-control" required>
                                <option value="" disabled selected>Select Domain...</option>
                                <option value="Perceptuo-Cognitive">Perceptuo - Cognitive</option>
                                <option value="Socio-Emotional">Socio - Emotional</option>
                                <option value="Psychomotor">Psychomotor</option>
                                <option value="Language-Communication">Language - Communication</option>
                                <option value="Self-Help">Self - Help</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 mb-3" style="background-color: #f8f9fa; border: 1px dashed #4F6AF5; border-radius: 8px;">
                        <h6 class="fw-bold text-primary mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> AI IEP Builder
                        </h6>
                        <p class="small text-muted mb-2">Input the student's current status to generate Objectives and Activities.</p>
                        
                        <label class="form-label fw-bold small text-dark">Present Level of Performance</label>
                        <textarea name="plop" id="ai-present-level" class="form-control mb-2" rows="2" required placeholder="e.g., Can sit for 5-10 minutes but fidgety. Follows 1-step commands with maximum prompts..."></textarea>
                        
                        <button type="button" id="generate-goals-btn" class="btn btn-sm btn-primary w-100 fw-bold">
                            <i class="fa-solid fa-robot"></i> Generate Objectives & Activities
                        </button>

                        <div id="ai-results-container" class="mt-3 d-none">
                            <hr class="my-2">
                            <p class="fw-bold small mb-2 text-success"><i class="fa-solid fa-check"></i> Suggested IEP Strategies:</p>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" style="font-size: 0.85rem; background: white;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%;">Objective</th>
                                            <th style="width: 45%;">Activities</th>
                                            <th style="width: 15%; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="goals-list-tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Final Goal Description</label>
                        <textarea id="final-goal-input" name="goal_description" class="form-control" rows="5" required placeholder="The selected Objective and Activities will appear here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px;">
                    <button type="submit" class="btn btn-primary fw-bold w-100 py-2"><i class="fa fa-plus me-2"></i> Save IEP Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ==========================================
// 0. NEW: SEARCH FILTER LOGIC
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('studentSearchInput');
    const noResultsMsg = document.getElementById('no-search-results');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const accordionItems = document.querySelectorAll('.student-accordion-item');
            let visibleCount = 0;

            accordionItems.forEach(item => {
                const studentName = item.getAttribute('data-student-name');
                if (studentName.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide the "No matches found" message
            if (visibleCount === 0 && searchTerm !== '') {
                noResultsMsg.style.display = 'block';
            } else {
                noResultsMsg.style.display = 'none';
            }
        });
    }
});

// ==========================================
// 1. PLOP DOMAIN MEMORY LOGIC (WITH BROWSER AUTO-SAVE)
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    let domainSelect = document.getElementById('domain-select');
    let plopInput = document.getElementById('ai-present-level');
    
    let savedMemory = localStorage.getItem('iep_plop_memory');
    let plopMemory = savedMemory ? JSON.parse(savedMemory) : {};
    
    let previousDomain = domainSelect.value;

    if (previousDomain && plopMemory[previousDomain]) {
        plopInput.value = plopMemory[previousDomain];
    }

    plopInput.addEventListener('input', function() {
        if (previousDomain) {
            plopMemory[previousDomain] = this.value;
            localStorage.setItem('iep_plop_memory', JSON.stringify(plopMemory));
        }
    });

    domainSelect.addEventListener('change', function() {
        let newDomain = this.value;

        if (previousDomain) {
            plopMemory[previousDomain] = plopInput.value;
            localStorage.setItem('iep_plop_memory', JSON.stringify(plopMemory));
        }

        if (plopMemory[newDomain]) {
            plopInput.value = plopMemory[newDomain];
        } else {
            plopInput.value = ''; 
        }

        previousDomain = newDomain;
    });

    let form = plopInput.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            localStorage.removeItem('iep_plop_memory');
        });
    }
});

// ==========================================
// 2. AI BUILDER LOGIC
// ==========================================
document.getElementById('generate-goals-btn').addEventListener('click', function() {
    let btn = this;
    let presentLevelText = document.getElementById('ai-present-level').value;
    let domainValue = document.getElementById('domain-select').value;
    let resultsContainer = document.getElementById('ai-results-container');
    let tbody = document.getElementById('goals-list-tbody');

    if(domainValue === '') { alert('Please select a Development Domain first!'); return; }
    if(presentLevelText.trim() === '') { alert('Please enter the Present Level of Performance first!'); return; }

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyzing...';
    btn.disabled = true;

    fetch("{{ route('teacher.iep-goals.generate-ai') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ domain: domainValue, present_level: presentLevelText })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = '<i class="fa-solid fa-robot"></i> Generate Objectives & Activities';
        btn.disabled = false;

        if(data.success) {
            tbody.innerHTML = '';
            resultsContainer.classList.remove('d-none');
            data.goals.forEach((item) => {
                let activityList = item.activities.map(a => `<li style="margin-bottom: 2px;">${a}</li>`).join('');
                
                let rawActivities = item.activities.map(a => `[ ] ${a}`).join('\n');
                 
                let tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold text-primary align-middle">${item.objective}</td>
                    <td class="align-middle"><ul class="mb-0 ps-3" style="list-style-type: disc;">${activityList}</ul></td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-success use-goal-btn w-100">
                            <i class="fa fa-arrow-down"></i> Add
                        </button>
                    </td>
                `;

                let useBtn = tr.querySelector('.use-goal-btn');
                useBtn.onclick = function() {
                    let formattedText = `OBJECTIVE:\n${item.objective}\n\nACTIVITIES:\n${rawActivities}`;
                    let finalInput = document.getElementById('final-goal-input');
                    
                    if (finalInput.value.trim() === '') {
                        finalInput.value = formattedText;
                    } else {
                        finalInput.value += `\n\n------------------------\n\n${formattedText}`;
                    }
                    
                    this.innerHTML = '<i class="fa fa-check"></i> Added';
                    this.classList.replace('btn-outline-success', 'btn-success');
                    this.classList.add('text-white');
                    setTimeout(() => {
                        this.innerHTML = '<i class="fa fa-arrow-down"></i> Add Another';
                        this.classList.replace('btn-success', 'btn-outline-success');
                        this.classList.remove('text-white');
                    }, 1500);
                };

                tbody.appendChild(tr);
            });
        } else {
            alert("AI Error: " + data.message);
        }
    })
    .catch(error => {
        btn.innerHTML = '<i class="fa-solid fa-robot"></i> Generate Objectives & Activities';
        btn.disabled = false;
        alert("Something went wrong communicating with the AI.");
    });
});

// ==========================================
// 3. INTERACTIVE CHECKLIST LOGIC
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    
    document.querySelectorAll('.interactive-checklist-container').forEach(container => {
        let textarea = container.querySelector('.goal-raw-text');
        let visualList = container.querySelector('.visual-checklist');
        let progressInput = container.closest('form').querySelector('.goal-progress-input');
        let statusSelect = container.closest('form').querySelector('.goal-status-select');
        let toggleBtn = container.querySelector('.toggle-raw-text-btn');

        toggleBtn.addEventListener('click', () => {
            textarea.classList.toggle('d-none');
            visualList.classList.toggle('d-none');
        });

        textarea.addEventListener('input', renderChecklist);
        function renderChecklist() {
            let lines = textarea.value.split('\n');
            let html = '';
            let totalBoxes = 0;
            let checkedBoxes = 0;
            lines.forEach((line, index) => {
                if (line.trim().startsWith('- ')) { line = line.replace('- ', '[ ] '); }

                if (line.trim().startsWith('[ ] ')) {
                    totalBoxes++;
                    let text = line.trim().replace('[ ] ', '');
                    html += `
                        <div class="form-check mb-2 px-3 py-1 rounded" style="transition: 0.2s;">
                            <input class="form-check-input dynamic-checkbox mt-1" type="checkbox" data-line="${index}" style="cursor:pointer; width:1.3em; height:1.3em; border-color: #4F6AF5;">
                            <label class="form-check-label ps-2" style="cursor:pointer; font-size: 14px;">${text}</label>
                        </div>`;
                } 
                else if (line.trim().startsWith('[x] ') || line.trim().startsWith('[X] ')) {
                    totalBoxes++;
                    checkedBoxes++;
                    let text = line.trim().replace(/\[x\] /i, '');
                    html += `
                        <div class="form-check mb-2 px-3 py-1 rounded" style="background: #f1f3f5; transition: 0.2s;">
                            <input class="form-check-input dynamic-checkbox mt-1" type="checkbox" data-line="${index}" checked style="cursor:pointer; width:1.3em; height:1.3em; background-color: var(--success); border-color: var(--success);">
                            <label class="form-check-label ps-2 text-muted text-decoration-line-through" style="cursor:pointer; font-size: 14px;">${text}</label>
                        </div>`;
                } 
                else if (line.trim() !== '' && !line.includes('---')) {
                    html += `<div class="mt-3 mb-2 px-1" style="color: var(--primary); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">${line}</div>`;
                } 
                else if (line.includes('---')) {
                    html += `<hr style="border-color: #dee2e6; margin: 15px 0;">`;
                }
            });
            if(totalBoxes === 0 && textarea.value.trim() !== '') {
                html += `<div class="text-muted small px-1 mt-2">No trackable activities found. Use the 'Edit Text' button to manually track progress.</div>`;
            }

            visualList.innerHTML = html;
            
            if (totalBoxes > 0) {
                progressInput.readOnly = true;
                progressInput.style.backgroundColor = '#e9ecef';
                
                let percent = Math.round((checkedBoxes / totalBoxes) * 100);
                progressInput.value = percent;
                if (percent === 100) {
                    statusSelect.value = 'achieved';
                } else {
                    statusSelect.value = 'in_progress';
                }
            } else {
                progressInput.readOnly = false;
                progressInput.style.backgroundColor = '#fff';
            }

            visualList.querySelectorAll('.dynamic-checkbox').forEach(chk => {
                chk.addEventListener('change', function() {
                    let lineIdx = this.getAttribute('data-line');
                    let currentLines = textarea.value.split('\n');
                    
                    if (currentLines[lineIdx].trim().startsWith('- ')) {
                        currentLines[lineIdx] = currentLines[lineIdx].replace('- ', '[ ] ');
                    }

                    if (this.checked) {
                        currentLines[lineIdx] = currentLines[lineIdx].replace('[ ] ', '[x] ');
                    } else {
                        currentLines[lineIdx] = currentLines[lineIdx].replace(/\[x\] /i, '[ ] ');
                    }
                    
                    textarea.value = currentLines.join('\n');
                    renderChecklist();
                });
            });
        }

        renderChecklist();
    });
});
</script>

@endsection