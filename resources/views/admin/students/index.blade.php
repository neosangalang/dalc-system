@extends('layouts.app')

@section('title', 'Student Profiling')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <strong><i class="fa fa-exclamation-triangle me-2"></i>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <h5><i class="fa fa-user-graduate me-2" style="color:var(--success)"></i>Student Profiles</h5>
        <button class="btn-sm-custom btn-primary-sm" data-bs-toggle="modal" data-bs-target="#createStudentModal">
            <i class="fa fa-plus"></i> Create Profile + Guardian
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th scope="col" class="py-3 px-4 rounded-start">Student</th>
                    <th scope="col" class="py-3">Class & Details</th>
                    <th scope="col" class="py-3">Assigned Personnel</th>
                    <th scope="col" class="py-3">Status</th>
                    <th scope="col" class="py-3 text-end rounded-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 42px; height: 42px; border-radius: 50%; background:linear-gradient(135deg,#4F6AF5,#9B6DFF); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="fw-bold d-block text-dark">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <small class="text-muted">ID: {{ $student->student_id_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="d-block fw-semibold text-dark">{{ $student->class_name }}</span>
                            <small class="text-muted">{{ $student->diagnosis ?? 'Unspecified' }}</small>
                        </td>
                        
                        <td>
                            <div class="mb-1">
                                <small class="text-muted d-block" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Teacher</small>
                                <span class="text-dark"><i class="fa fa-chalkboard-teacher text-primary me-1"></i> {{ $student->teacher->name ?? 'Unassigned' }}</span>
                            </div>
                            @if($student->guardian)
                            <div class="mt-2 pt-2 border-top">
                                <small class="text-muted d-block" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Guardian</small>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-dark fw-bold"><i class="fa fa-user-shield text-success me-1"></i> {{ $student->guardian->name }}</span>
                                    
                                    <button type="button" class="btn btn-sm btn-light border text-info py-0 px-2 shadow-sm" 
                                            onclick="copyDefaultGuardianLogin('{{ $student->guardian->username }}')" 
                                            title="Copy Default Login">
                                        <i class="fa fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if($student->status === 'active')
                                    <span class="badge-custom badge-active">Active</span>
                                @else
                                    <span class="badge-custom badge-archived">Archived</span>
                                @endif
                                
                                @if($student->medical_document)
                                    <a href="{{ asset('storage/' . $student->medical_document) }}" target="_blank" class="badge-custom text-decoration-none shadow-sm" style="background:#fee2e2;color:#b91c1c;">
                                        <i class="fa fa-file-medical me-1"></i>View Medical File
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('⚠️ WARNING: Are you sure you want to permanently delete this student? All their IEP Goals and Daily Logs will be erased!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger text-white">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 24px 64px rgba(0,0,0,0.15)">
                                <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                                        <h5 class="modal-title fw-800">Edit Student Profile</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start p-4" style="max-height: 70vh; overflow-y: auto;">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name</label>
                                                <input type="text" name="first_name" class="form-control" required value="{{ $student->first_name }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" name="last_name" class="form-control" required value="{{ $student->last_name }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Class</label>
                                                <input type="text" name="class_name" class="form-control" required value="{{ $student->class_name }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" name="date_of_birth" class="form-control" required value="{{ \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Gender</label>
                                                <select name="gender" class="form-control" required>
                                                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Diagnosis</label>
                                                <select name="diagnosis" class="form-control" required>
                                                    <option value="Autism Spectrum Disorder (ASD)" {{ $student->diagnosis == 'Autism Spectrum Disorder (ASD)' ? 'selected' : '' }}>Autism Spectrum Disorder (ASD)</option>
                                                    <option value="ADHD" {{ $student->diagnosis == 'ADHD' ? 'selected' : '' }}>ADHD</option>
                                                    <option value="Down Syndrome" {{ $student->diagnosis == 'Down Syndrome' ? 'selected' : '' }}>Down Syndrome</option>
                                                    <option value="Intellectual Disability" {{ $student->diagnosis == 'Intellectual Disability' ? 'selected' : '' }}>Intellectual Disability</option>
                                                    <option value="Specific Learning Disability" {{ $student->diagnosis == 'Specific Learning Disability' ? 'selected' : '' }}>Specific Learning Disability</option>
                                                    <option value="Speech/Language Impairment" {{ $student->diagnosis == 'Speech/Language Impairment' ? 'selected' : '' }}>Speech/Language Impairment</option>
                                                    <option value="Other" {{ $student->diagnosis == 'Other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label fw-bold"><i class="fa fa-file-medical me-1 text-danger"></i> Update Medical Document</label>
                                                <input type="file" name="medical_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->medical_document)
                                                    <small class="text-success d-block mt-1"><i class="fa fa-check-circle"></i> Document currently attached. Uploading a new one will overwrite it.</small>
                                                @else
                                                    <small class="text-muted d-block mt-1">No document attached.</small>
                                                @endif
                                            </div>

                                            <div class="col-md-12 mt-4">
                                                <label class="form-label">Assign Teacher</label>
                                                <select name="teacher_id" class="form-control" required>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ $student->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label">Assign Guardian</label>
                                                <select name="guardian_id" class="form-control border-secondary" required>
                                                    <option value="" disabled>Select a Guardian...</option>
                                                    @isset($guardians)
                                                        @foreach($guardians as $guardian)
                                                            <option value="{{ $guardian->id }}" {{ $student->guardian_id == $guardian->id ? 'selected' : '' }}>
                                                                {{ $guardian->name }} ({{ $guardian->username }})
                                                            </option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                                <small class="text-muted"><i class="fa fa-info-circle"></i> Update this if custody changes or the wrong account was linked.</small>
                                            </div>

                                            <div class="col-md-12 mt-4">
                                                <label class="form-label fw-bold text-primary">Account Status</label>
                                                <select name="status" class="form-control" style="border: 2px solid var(--primary);">
                                                    <option value="active" {{ $student->status == 'active' ? 'selected' : '' }}>🟢 Active Student</option>
                                                    <option value="archived" {{ $student->status == 'archived' ? 'selected' : '' }}>🔴 Archived (No longer attending)</option>
                                                </select>
                                                <small class="text-muted">Archiving a student hides them from the active teacher roster but preserves their records.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;gap:8px">
                                        <button type="button" class="btn-sm-custom" style="background:var(--border);color:var(--text-dark);border-radius:8px;padding:8px 20px;font-size:13px;font-weight:700;border:none;cursor:pointer" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn-sm-custom btn-primary-sm" style="padding:8px 24px;font-size:13px"><i class="fa fa-save"></i> Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa fa-user-graduate fs-1 mb-3 text-light"></i><br>
                            No students enrolled yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 24px 64px rgba(0,0,0,0.15)">
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--border);padding:20px 24px">
                    <h5 class="modal-title fw-800">Create Student Profile + Guardian Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    
                    <div class="form-section-title mb-3" style="color: var(--primary);"><i class="fa fa-user-graduate me-2"></i>1. Student Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Child's First Name</label>
                            <input type="text" name="first_name" class="form-control" required placeholder="First Name" value="{{ old('first_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Child's Last Name</label>
                            <input type="text" name="last_name" class="form-control" required placeholder="Last Name" value="{{ old('last_name') }}">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Class</label>
                            <input type="text" name="class_name" class="form-control" required placeholder="e.g., SPED - Block A" value="{{ old('class_name') }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="dob_input" class="form-control" required value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Age</label>
                            <input type="text" id="age_display" class="form-control" disabled style="background:#f8f9fa;" placeholder="Auto-calculates from DOB">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="" disabled selected>Select Gender...</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Diagnosis</label>
                            <select name="diagnosis" class="form-control" required>
                                <option value="" disabled {{ old('diagnosis') ? '' : 'selected' }}>Select Category...</option>
                                <option value="Autism Spectrum Disorder (ASD)" {{ old('diagnosis') == 'Autism Spectrum Disorder (ASD)' ? 'selected' : '' }}>Autism Spectrum Disorder (ASD)</option>
                                <option value="ADHD" {{ old('diagnosis') == 'ADHD' ? 'selected' : '' }}>ADHD</option>
                                <option value="Down Syndrome" {{ old('diagnosis') == 'Down Syndrome' ? 'selected' : '' }}>Down Syndrome</option>
                                <option value="Intellectual Disability" {{ old('diagnosis') == 'Intellectual Disability' ? 'selected' : '' }}>Intellectual Disability</option>
                                <option value="Specific Learning Disability" {{ old('diagnosis') == 'Specific Learning Disability' ? 'selected' : '' }}>Specific Learning Disability</option>
                                <option value="Speech/Language Impairment" {{ old('diagnosis') == 'Speech/Language Impairment' ? 'selected' : '' }}>Speech/Language Impairment</option>
                                <option value="Other" {{ old('diagnosis') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold"><i class="fa fa-file-medical me-1 text-danger"></i> Attach Medical Document</label>
                            <input type="file" name="medical_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload a scanned PDF or photo of the official diagnosis (Optional).</small>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Assign Teacher</label>
                            <select name="teacher_id" class="form-control" required>
                                <option value="" disabled selected>Select a Teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <hr style="border-color: var(--border);">
                    
                    <div class="form-section-title mb-3 mt-4" style="color: var(--purple);"><i class="fa fa-user-shield me-2"></i>2. Guardian Linking</div>
                    
                    <div class="p-3 mb-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e9ecef;">
                        <div class="d-flex gap-4 mb-3 pb-3 border-bottom">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="guardian_action" id="guard_existing" value="existing" checked>
                                <label class="form-check-label fw-bold text-dark" for="guard_existing">
                                    Assign Existing Guardian
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="guardian_action" id="guard_new" value="new">
                                <label class="form-check-label fw-bold text-primary" for="guard_new">
                                    <i class="fa fa-plus-circle me-1"></i> Auto-Generate New Account
                                </label>
                            </div>
                        </div>

                        <div id="existingGuardianPanel">
                            <label class="form-label fw-bold small">Select Parent/Guardian</label>
                            <select name="existing_guardian_id" class="form-select border-secondary" id="existing_select" required>
                                <option value="" disabled selected>-- Search & Select Guardian --</option>
                                @isset($guardians)
                                    @foreach($guardians as $guardian)
                                        <option value="{{ $guardian->id }}">{{ $guardian->name }} ({{ $guardian->username }})</option>
                                    @endforeach
                                @endisset
                            </select>
                            <small class="text-muted mt-1 d-block"><i class="fa fa-info-circle"></i> Use this if the parent already has a child enrolled.</small>
                        </div>

                        <div id="newGuardianPanel" class="d-none">
                            <div class="alert py-3 mb-3" style="background-color: #EEF1FF; border: 1px dashed #4F6AF5; border-radius: 10px;">
                                <h6 class="fw-bold mb-1 text-primary"><i class="fa fa-robot me-1"></i> Automated Account Generation</h6>
                                <p class="small text-muted mb-2">The system will automatically generate the login credentials based on the Guardian's Last Name. <i>(e.g., If last name is 'Smith', username will be 'smith12')</i></p>
                                <ul class="small text-dark fw-bold mb-0 ps-3">
                                    <li>Format: <span class="text-primary">[lastname][number]</span></li>
                                    <li>Default Password: <span class="text-danger">12345678</span></li>
                                </ul>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Guardian First Name</label>
                                    <input type="text" name="guardian_first_name" id="new_guard_first_name" class="form-control" placeholder="e.g., Thomas" value="{{ old('guardian_first_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Guardian Last Name</label>
                                    <input type="text" name="guardian_last_name" id="new_guard_last_name" class="form-control" placeholder="e.g., Anderson" value="{{ old('guardian_last_name') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--border);padding:16px 24px;gap:8px">
                    <button type="button" class="btn-sm-custom" style="background:var(--border);color:var(--text-dark);border-radius:8px;padding:8px 20px;font-size:13px;font-weight:700;border:none;cursor:pointer" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm-custom btn-primary-sm" style="padding:8px 24px;font-size:13px"><i class="fa fa-save"></i> Save & Link Guardian</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        // Keeps modal open if there are validation errors
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('createStudentModal'));
                myModal.show();
            });
        @endif

        // Auto Age Calculator
        document.getElementById('dob_input').addEventListener('change', function() {
            let dob = new Date(this.value);
            let today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            let m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }
            
            if (!isNaN(age)) {
                document.getElementById('age_display').value = age + " years old";
            } else {
                document.getElementById('age_display').value = "";
            }
        });

        // Guardian Panels Toggle Logic
        document.addEventListener('DOMContentLoaded', function() {
            const radioExisting = document.getElementById('guard_existing');
            const radioNew = document.getElementById('guard_new');
            
            const panelExisting = document.getElementById('existingGuardianPanel');
            const panelNew = document.getElementById('newGuardianPanel');

            const selectExisting = document.getElementById('existing_select');
            const inputNewFirstName = document.getElementById('new_guard_first_name');
            const inputNewLastName = document.getElementById('new_guard_last_name');

            function toggleGuardianPanels() {
                if (radioExisting && radioExisting.checked) {
                    // Show Existing, Hide New
                    if(panelExisting) panelExisting.classList.remove('d-none');
                    if(panelNew) panelNew.classList.add('d-none');
                    
                    // Add required to existing, remove from new
                    if(selectExisting) selectExisting.setAttribute('required', 'required');
                    if(inputNewFirstName) inputNewFirstName.removeAttribute('required');
                    if(inputNewLastName) inputNewLastName.removeAttribute('required');
                } else if (radioNew && radioNew.checked) {
                    // Show New, Hide Existing
                    if(panelExisting) panelExisting.classList.add('d-none');
                    if(panelNew) panelNew.classList.remove('d-none');
                    
                    // Remove required from existing, add to new
                    if(selectExisting) selectExisting.removeAttribute('required');
                    if(inputNewFirstName) inputNewFirstName.setAttribute('required', 'required');
                    if(inputNewLastName) inputNewLastName.setAttribute('required', 'required');
                }
            }

            if (radioExisting && radioNew) {
                radioExisting.addEventListener('change', toggleGuardianPanels);
                radioNew.addEventListener('change', toggleGuardianPanels);
                toggleGuardianPanels(); // run once on load
            }
        });

        // THE NEW COPY FUNCTION FOR THE TABLE
        function copyDefaultGuardianLogin(username) {
            let defaultPassword = '12345678';
            let text = `Dream Achievers Learning Center - Parent Portal\n\nUsername: ${username}\nDefault Password: ${defaultPassword}\n\n*Note: If you have already personalized your password, this default password will no longer work. Please contact the admin for a reset.*`;
            
            navigator.clipboard.writeText(text).then(() => {
                alert('Default login credentials copied to clipboard!');
            }).catch(err => {
                alert('Failed to copy text. Please check your browser permissions.');
            });
        }
    </script>
@endpush