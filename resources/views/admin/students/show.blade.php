@extends('layouts.app')

@section('title', "{$student->first_name}'s Master File")

@section('content')

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] flex items-center gap-3 m-0 tracking-tight">
            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 border border-[#3B82F6]/20 flex items-center justify-center text-[#3B82F6] shadow-sm shrink-0">
                <i class="fa fa-user-graduate text-lg"></i>
            </div>
            {{ $student->first_name }} {{ $student->last_name }}'s Master File
        </h2>
        
        <a href="{{ route('admin.students.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-white border border-[#9CA0AF] rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 hover:text-[#1E2657] shadow-sm transition-all focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1">
            <i class="fa fa-arrow-left mr-2"></i> Back to Student List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 w-full">
        
        <!-- LEFT COLUMN: Profile & Details -->
        <div class="lg:col-span-1 space-y-6 lg:space-y-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-[#1E2657] to-[#12163A] flex items-center justify-center text-white text-3xl font-extrabold shadow-sm border border-black/10 mb-4 shrink-0">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                    <h3 class="font-extrabold text-xl text-[#1E2657] mb-1 leading-tight">{{ $student->first_name }} {{ $student->last_name }}</h3>
                    <p class="text-[13px] font-medium text-[#6B7086] mb-5">ID: {{ $student->student_id_number ?? 'N/A' }}</p>
                    
                    @if($student->status === 'active')
                        <span class="inline-flex px-4 py-1.5 text-[11px] font-extrabold rounded-md bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20 uppercase tracking-widest">
                            <i class="fa fa-check-circle mr-1.5 mt-0.5"></i> Currently Enrolled
                        </span>
                    @else
                        <span class="inline-flex px-4 py-1.5 text-[11px] font-extrabold rounded-md bg-slate-100 text-[#6B7086] border border-black/10 uppercase tracking-widest">
                            <i class="fa fa-archive mr-1.5 mt-0.5"></i> Archived
                        </span>
                    @endif
                </div>
                
                <div class="p-6 border-t border-black/10 bg-[#F4F6FA]/50">
                    <h6 class="font-extrabold text-[#9CA0AF] text-[10px] uppercase tracking-widest mb-4">Medical & Academic Info</h6>
                    
                    <div class="space-y-3.5">
                        <div class="flex justify-between items-start">
                            <span class="text-[13px] font-extrabold text-[#6B7086] uppercase tracking-widest">Diagnosis:</span>
                            <span class="text-[13px] font-bold text-[#1E2657] text-right">{{ $student->diagnosis ?? 'Not Specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-extrabold text-[#6B7086] uppercase tracking-widest">Date of Birth:</span>
                            <span class="text-[13px] font-bold text-[#1E2657]">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[13px] font-extrabold text-[#6B7086] uppercase tracking-widest">Gender:</span>
                            <span class="text-[13px] font-bold text-[#1E2657]">{{ $student->gender }}</span>
                        </div>
                        @if($student->learners_reference_number)
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-[13px] font-extrabold text-[#6B7086] uppercase tracking-widest">LRN:</span>
                            <span class="text-[13px] font-bold text-[#3B82F6]">{{ $student->learners_reference_number }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-5 border-t border-black/10 border-dashed">
                        @php
                            $allDocs = $student->documents;
                            $hasDocs = $allDocs->isNotEmpty();
                        @endphp
                        <span class="flex items-center text-[11px] font-extrabold text-[#1E2657] mb-3 uppercase tracking-widest">
                            <i class="fa fa-folder-open mr-2 text-[#3B82F6]"></i> Documents
                            @if($allDocs->count() > 0)
                                <span class="ml-auto text-[10px] text-[#3B82F6] font-bold">({{ $allDocs->count() }})</span>
                            @endif
                        </span>
                        
                        @if($hasDocs)
                            <div class="space-y-2">
                                @foreach($allDocs as $doc)
                                <a href="{{ route('secure.student-document', $doc->id) }}" target="_blank" class="w-full inline-flex items-center justify-between p-3 bg-white border border-[#9CA0AF] rounded-xl text-[12px] font-extrabold text-[#3B82F6] hover:bg-blue-50 transition-colors shadow-sm decoration-transparent">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa fa-file-lines shrink-0"></i>
                                        <span class="truncate">{{ $doc->original_name }}</span>
                                    </div>
                                    <i class="fa fa-arrow-down shrink-0 ml-2 text-slate-400"></i>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-[#F4F6FA] border border-black/5 rounded-xl p-4 text-center">
                                <p class="text-[12px] font-medium text-[#9CA0AF] m-0">No documents uploaded.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-black/15 p-6">
                <h6 class="font-extrabold text-[#9CA0AF] text-[10px] uppercase tracking-widest mb-4">Assigned Personnel</h6>
                
                <div class="flex items-center p-3.5 mb-3 bg-[#F4F6FA] border border-black/5 rounded-xl">
                    <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center shrink-0 mr-3 border border-[#3B82F6]/20">
                        <i class="fa fa-chalkboard-teacher text-sm"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5">Special Ed Teacher</div>
                        <div class="text-[13px] font-bold text-[#1E2657]">{{ $student->teacher->name ?? 'Unassigned' }}</div>
                    </div>
                </div>

                <div class="p-3.5 bg-[#F4F6FA] border border-black/5 rounded-xl">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-[#10B981]/10 text-[#10B981] flex items-center justify-center shrink-0 mr-3 border border-[#10B981]/20">
                            <i class="fa fa-heart text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-0.5">Primary Guardian</div>
                            <div class="text-[13px] font-bold text-[#1E2657] truncate">{{ $student->guardian->name ?? 'Unassigned' }}</div>
                        </div>
                    </div>
                    @if($student->guardian)
                    <div class="mt-3 pt-2.5 border-t border-black/5 space-y-1.5 pl-1">
                        <div class="text-[11px] font-medium text-[#6B7086] flex items-center gap-2 truncate">
                            <i class="fa fa-envelope text-[#10B981] w-3.5 text-center shrink-0 text-[10px]"></i>
                            @if($student->guardian->email)
                            <a href="mailto:{{ $student->guardian->email }}" class="text-[#3B82F6] hover:underline truncate" title="{{ $student->guardian->email }}">{{ $student->guardian->email }}</a>
                            @else
                            <span class="text-[#9CA0AF] italic">No email on file</span>
                            @endif
                        </div>
                        <div class="text-[11px] font-medium text-[#6B7086] flex items-center gap-2">
                            <i class="fa fa-phone text-[#10B981] w-3.5 text-center shrink-0 text-[10px]"></i>
                            @if($student->guardian->contact_number)
                            <a href="tel:{{ $student->guardian->contact_number }}" class="text-[#1E2657] font-bold hover:underline">{{ $student->guardian->contact_number }}</a>
                            @else
                            <span class="text-[#9CA0AF] italic">No phone number on file</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: IEP Goals -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-black/15 h-full flex flex-col overflow-hidden">
                
                <div class="p-6 border-b border-black/10 flex flex-wrap justify-between items-center gap-2 bg-[#F4F6FA]">
                    <h4 class="font-extrabold text-base sm:text-lg text-[#1E2657] m-0 flex items-center">
                        <i class="fa fa-bullseye mr-2.5 text-[#8B5CF6]"></i> IEP Goal Progress
                    </h4>
                    @if(isset($pastArchives) && $pastArchives->isNotEmpty())
                        <div class="flex items-center gap-2">
                            @foreach($pastArchives as $archive)
                                <a href="{{ route('admin.archive.pdf', $archive->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white border border-[#8B5CF6]/30 text-[#8B5CF6] hover:bg-[#8B5CF6]/10 text-[11px] font-bold rounded-lg shadow-sm transition-all" title="View SY {{ $archive->school_year }} Archive PDF">
                                    <i class="fa fa-file-pdf mr-1.5 text-rose-500"></i> SY {{ $archive->school_year }} Archive
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[800px] custom-scrollbar bg-white flex-1">
                    @forelse($student->iepGoals as $goal)
                        <div class="mb-8 pb-8 border-b border-black/10 last:border-0 last:pb-0 last:mb-0">
                            
                            <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
                                <span class="inline-flex px-3.5 py-1.5 bg-[#8B5CF6]/10 text-[#8B5CF6] text-[10px] font-extrabold rounded-md uppercase tracking-widest border border-[#8B5CF6]/20 shadow-sm">
                                    {{ str_replace('-', ' - ', strtoupper($goal->domain)) }}
                                </span>
                                <span class="text-[12px] font-extrabold text-[#6B7086] bg-[#F4F6FA] px-3.5 py-1.5 border border-black/5 rounded-md shadow-sm uppercase tracking-widest">
                                    Overall Progress: <span class="{{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#8B5CF6]' }}">{{ $goal->progress_percentage }}%</span>
                                </span>
                            </div>

                            @php
                                $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                            @endphp

                            <div class="space-y-5">
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
                                        
                                        // Turn activities string into an array of lines based on checkboxes or bullets
                                        $activitiesArray = preg_split('/\[ \]|\[x\]|\[X\]|\[\]|•/', $activitiesText);
                                    @endphp

                                    @if(!empty($objective))
                                        <div class="p-5 bg-white border-y border-r border-l-4 border-black/15 border-l-[#8B5CF6] rounded-r-xl shadow-sm">
                                            <span class="text-[10px] font-extrabold tracking-widest text-[#8B5CF6] uppercase block mb-2 flex items-center">
                                                <i class="fa fa-bullseye mr-1.5"></i> Objective:
                                            </span>
                                            <p class="text-[14px] font-extrabold text-[#1E2657] mb-0 leading-relaxed">{{ $objective }}</p>

                                            @if(count(array_filter($activitiesArray)) > 0)
                                                <div class="mt-5 pt-5 border-t border-black/10 border-dashed">
                                                    <span class="text-[10px] font-extrabold tracking-widest text-[#9CA0AF] uppercase block mb-3 flex items-center">
                                                        <i class="fa fa-list-check mr-1.5 text-[#3B82F6]"></i> Activities / Strategies:
                                                    </span>
                                                    <ul class="list-disc pl-5 space-y-2 text-[13px] font-medium text-[#6B7086] m-0 leading-relaxed">
                                                        @foreach($activitiesArray as $activity)
                                                            @if(trim($activity))
                                                                <li>{{ trim($activity) }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 px-6 bg-white rounded-2xl border border-black/15 shadow-sm">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F4F6FA] border border-black/5 mb-4 shadow-sm">
                                <i class="fa fa-folder-open text-2xl text-[#9CA0AF]"></i>
                            </div>
                            <h3 class="font-extrabold text-[#1E2657] text-[15px] mb-1">No Active IEP Goals Found</h3>
                            <p class="text-[#6B7086] text-[13px] font-medium m-0 max-w-md mx-auto">
                                @if($student->status === 'active')
                                    This student is actively enrolled with a fresh IEP slate. Their assigned teacher can now establish new goals for the current school year.
                                @else
                                    No IEP Goals or objectives have been generated for this student yet.
                                @endif
                            </p>
                            @if(isset($pastArchives) && $pastArchives->isNotEmpty())
                                <div class="mt-5 pt-5 border-t border-black/10 inline-flex flex-col items-center">
                                    <span class="text-[11px] font-extrabold text-[#9CA0AF] uppercase tracking-wider mb-2">Previous Enrollment Records</span>
                                    <div class="flex flex-wrap justify-center gap-2">
                                        @foreach($pastArchives as $archive)
                                            <a href="{{ route('admin.archive.pdf', $archive->id) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-black/15 text-[#1E2657] text-[12px] font-bold rounded-xl shadow-sm transition-all">
                                                <i class="fa fa-file-pdf mr-2 text-rose-500"></i> View SY {{ $archive->school_year }} Archived IEP
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection