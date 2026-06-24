@extends('layouts.app')

@section('title', "{$student->first_name}'s Master File")

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3 m-0">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                <i class="fa fa-user-graduate"></i>
            </div>
            {{ $student->first_name }} {{ $student->last_name }}'s Master File
        </h2>
        
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 shadow-sm transition-all duration-200 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
            <i class="fa fa-arrow-left mr-2"></i> Back to Student List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 w-full">
        
        <div class="lg:col-span-1 space-y-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-3xl font-extrabold shadow-inner mb-4">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                    <h3 class="font-extrabold text-xl text-slate-800 mb-1">{{ $student->first_name }} {{ $student->last_name }}</h3>
                    <p class="text-sm font-medium text-slate-500 mb-4">ID: {{ $student->student_id_number ?? 'N/A' }}</p>
                    
                    @if($student->status === 'active')
                        <span class="inline-flex px-4 py-1.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fa fa-check-circle mr-1.5 mt-0.5"></i> Currently Enrolled
                        </span>
                    @else
                        <span class="inline-flex px-4 py-1.5 text-xs font-bold rounded-full bg-slate-50 text-slate-600 border border-slate-200">
                            <i class="fa fa-archive mr-1.5 mt-0.5"></i> Archived
                        </span>
                    @endif
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <h6 class="font-extrabold text-slate-400 text-[10px] uppercase tracking-widest mb-4">Medical & Academic Info</h6>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium text-slate-500">Diagnosis:</span>
                            <span class="text-sm font-bold text-slate-800 text-right">{{ $student->exceptionality ?? 'Not Specified' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-slate-500">Date of Birth:</span>
                            <span class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-slate-500">Gender:</span>
                            <span class="text-sm font-bold text-slate-800">{{ $student->gender }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-slate-200 border-dashed">
                        <span class="flex items-center text-xs font-bold text-rose-500 mb-2 uppercase tracking-wide">
                            <i class="fa fa-notes-medical mr-2"></i> Medical / Accommodations:
                        </span>
                        <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
                            <p class="text-[13px] font-medium text-rose-800 m-0 leading-relaxed">
                                {{ $student->medical_history ?: 'No specific medical history or accommodations recorded.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h6 class="font-extrabold text-slate-400 text-[10px] uppercase tracking-widest mb-4">Assigned Personnel</h6>
                
                <div class="flex items-center p-3 mb-3 bg-indigo-50 border border-indigo-100 rounded-xl transition-colors hover:bg-indigo-100">
                    <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0 mr-3 shadow-sm">
                        <i class="fa fa-chalkboard-teacher text-sm"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-extrabold text-indigo-500 uppercase tracking-wider mb-0.5">Special Ed Teacher</div>
                        <div class="text-sm font-bold text-slate-800">{{ $student->teacher->name ?? 'Unassigned' }}</div>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-emerald-50 border border-emerald-100 rounded-xl transition-colors hover:bg-emerald-100">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 mr-3 shadow-sm">
                        <i class="fa fa-heart text-sm"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider mb-0.5">Primary Guardian</div>
                        <div class="text-sm font-bold text-slate-800">{{ $student->guardian->name ?? 'Unassigned' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 h-full flex flex-col overflow-hidden">
                
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h4 class="font-extrabold text-lg text-indigo-600 m-0 flex items-center">
                        <i class="fa fa-bullseye mr-2.5"></i> IEP Goal Progress
                    </h4>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[800px] custom-scrollbar bg-slate-50/30 flex-1">
                    @forelse($student->iepGoals as $goal)
                        <div class="mb-8 pb-6 border-b border-slate-200 last:border-0 last:pb-0 last:mb-0">
                            
                            <div class="flex flex-wrap justify-between items-center mb-5 gap-3">
                                <span class="inline-flex px-4 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-extrabold rounded-full uppercase tracking-wider border border-indigo-200">
                                    {{ strtoupper($goal->domain) }}
                                </span>
                                <span class="text-sm font-bold text-slate-700 bg-white px-3 py-1 border border-slate-200 rounded-lg shadow-sm">
                                    Overall Progress: <span class="text-indigo-600">{{ $goal->progress_percentage }}%</span>
                                </span>
                            </div>

                            @php
                                $subGoals = preg_split('/-{5,}/', $goal->goal_description);
                            @endphp

                            <div class="space-y-4">
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
                                        <div class="p-5 bg-white border-l-4 border-indigo-500 rounded-r-xl shadow-sm border-y border-r border-slate-100">
                                            <span class="text-[11px] font-extrabold tracking-widest text-slate-400 uppercase block mb-1.5">Objective:</span>
                                            <p class="text-sm font-bold text-slate-800 mb-0 leading-relaxed">{{ $objective }}</p>

                                            @if(count(array_filter($activitiesArray)) > 0)
                                                <div class="mt-4 pt-4 border-t border-slate-100">
                                                    <span class="text-[11px] font-extrabold tracking-widest text-slate-400 uppercase block mb-2">Activities / Strategies:</span>
                                                    <ul class="list-disc pl-5 space-y-1.5 text-[13px] font-medium text-slate-600 m-0">
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
                        <div class="text-center py-16 px-6 bg-white rounded-xl border border-slate-200 border-dashed">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                                <i class="fa fa-folder-open text-2xl text-slate-300"></i>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-lg mb-1">No IEP Goals Found</h3>
                            <p class="text-slate-500 text-sm font-medium m-0">No IEP Goals or objectives have been generated for this student yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection