@extends('layouts.app')

@section('title', 'View Archive')

@section('content')

<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    {{-- ===== BACK BUTTON & ACTIONS ===== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <a href="{{ route('admin.archive.index') }}" class="inline-flex items-center text-[13px] font-extrabold text-[#6B7086] hover:text-[#1E2657] transition-colors decoration-transparent">
            <i class="fa fa-arrow-left mr-2"></i> Back to Archives
        </a>

        <div class="flex flex-wrap gap-3 w-full sm:w-auto">
            {{-- Download PDF --}}
            @if($archive->master_pdf_path)
                <a href="{{ asset('storage/' . $archive->master_pdf_path) }}" target="_blank" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-white border border-black/15 text-[#6B7086] text-[13px] font-extrabold rounded-xl hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm decoration-transparent">
                    <i class="fa fa-file-pdf mr-2 text-rose-500"></i> Download PDF
                </a>
            @endif

            {{-- Restore Student --}}
            <form action="{{ route('admin.archive.restore', $archive->id) }}" method="POST" class="m-0 w-full sm:w-auto" onsubmit="return confirm('Are you sure you want to restore this student? They will be set back to active status and this archive record will be removed.');">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#10B981] text-white text-[13px] font-extrabold rounded-xl hover:bg-emerald-600 transition-colors shadow-sm">
                    <i class="fa fa-rotate-left mr-2"></i> Restore Student
                </button>
            </form>
        </div>
    </div>

    {{-- ===== STUDENT INFO CARD ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-black/10 bg-[#F4F6FA] flex items-center">
            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm border border-black/5 shrink-0">
                <i class="fa fa-user"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 leading-tight">
                    {{ $archive->student_snapshot['first_name'] ?? 'Unknown' }} {{ $archive->student_snapshot['last_name'] ?? 'Student' }}
                </h3>
                <p class="text-[12px] font-medium text-[#9CA0AF] m-0">SY {{ $archive->school_year }} · Archived on {{ $archive->archived_at->format('M j, Y') }}</p>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

                @php
                    $snapshot = $archive->student_snapshot;
                    $infoFields = [
                        ['label' => 'LRN', 'value' => $snapshot['learners_reference_number'] ?? 'N/A', 'icon' => 'fa-id-card'],
                        ['label' => 'Class', 'value' => $snapshot['class_name'] ?? 'N/A', 'icon' => 'fa-chalkboard'],
                        ['label' => 'Date of Birth', 'value' => isset($snapshot['date_of_birth']) ? \Carbon\Carbon::parse($snapshot['date_of_birth'])->format('M j, Y') : 'N/A', 'icon' => 'fa-cake-candles'],
                        ['label' => 'Gender', 'value' => ucfirst($snapshot['gender'] ?? 'N/A'), 'icon' => 'fa-venus-mars'],
                        ['label' => 'Diagnosis', 'value' => $snapshot['diagnosis'] ?? ($snapshot['exceptionality'] ?? 'N/A'), 'icon' => 'fa-stethoscope'],
                        ['label' => 'Status at Archive', 'value' => ucfirst($snapshot['status'] ?? 'N/A'), 'icon' => 'fa-circle-check'],
                    ];
                @endphp

                @foreach($infoFields as $field)
                    <div class="bg-[#F4F6FA] rounded-xl p-4 border border-black/5">
                        <p class="text-[11px] font-extrabold text-[#9CA0AF] uppercase tracking-wider m-0 mb-1">
                            <i class="fa {{ $field['icon'] }} mr-1"></i> {{ $field['label'] }}
                        </p>
                        <p class="text-[14px] font-extrabold text-[#1E2657] m-0">{{ $field['value'] }}</p>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- ===== IEP GOALS (Full Width) ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-black/10 bg-[#F4F6FA] flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-10 h-10 rounded-full bg-[#8B5CF6]/10 flex items-center justify-center text-[#8B5CF6] mr-3 shadow-sm border border-black/5 shrink-0">
                    <i class="fa fa-bullseye"></i>
                </div>
                IEP Goals
            </h3>
            <span class="text-[12px] font-extrabold text-[#9CA0AF] bg-white px-3 py-1 rounded-full border border-black/10">
                {{ count($archive->iep_snapshot ?? []) }} goals
            </span>
        </div>

        <div class="p-5 sm:p-6 space-y-4">
            @forelse($archive->iep_snapshot ?? [] as $index => $goal)
                <div class="bg-white border border-black/10 rounded-xl overflow-hidden hover:border-[#8B5CF6]/30 hover:shadow-md transition-all">
                    
                    {{-- Goal Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 bg-[#F4F6FA] border-b border-black/5">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-[#8B5CF6] text-white text-[11px] font-extrabold flex items-center justify-center shrink-0 shadow-sm">
                                {{ $index + 1 }}
                            </span>
                            <h4 class="text-[14px] font-extrabold text-[#1E2657] m-0">{{ $goal['domain'] ?? 'General' }}</h4>
                        </div>

                        <div class="flex items-center gap-3 ml-10 sm:ml-0">
                            {{-- Progress --}}
                            @if(isset($goal['progress_percentage']))
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-black/5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ ($goal['progress_percentage'] ?? 0) >= 100 ? 'bg-[#10B981]' : 'bg-[#8B5CF6]' }}" style="width: {{ min($goal['progress_percentage'] ?? 0, 100) }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-[#6B7086]">{{ $goal['progress_percentage'] ?? 0 }}%</span>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            @php $goalStatus = $goal['status'] ?? 'in_progress'; @endphp
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full {{ $goalStatus === 'achieved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $goalStatus === 'achieved' ? 'Achieved' : 'In Progress' }}
                            </span>
                        </div>
                    </div>

                    {{-- Goal Description --}}
                    <div class="px-5 py-4">
                        @php
                            $description = $goal['goal_description'] ?? 'No description';
                            // Split by objective markers
                            $parts = preg_split('/(-{3,}\s*OBJECTIVE:|(?<=\.)[\s]*OBJECTIVE:)/', $description, -1, PREG_SPLIT_NO_EMPTY);
                        @endphp

                        @if(count($parts) > 1)
                            @foreach($parts as $partIndex => $part)
                                @php
                                    $part = trim($part);
                                    // Separate objectives from activities
                                    $sections = preg_split('/(ACTIVITIES:\s*)/', $part, 2, PREG_SPLIT_DELIM_CAPTURE);
                                    $objectiveText = trim($sections[0] ?? '');
                                    $activitiesText = isset($sections[2]) ? trim($sections[2]) : '';
                                    // Remove "OBJECTIVE:" prefix if present
                                    $objectiveText = preg_replace('/^OBJECTIVE:\s*/i', '', $objectiveText);
                                @endphp

                                @if($partIndex > 0)
                                    <div class="border-t border-dashed border-black/10 my-4"></div>
                                @endif

                                @if($objectiveText)
                                    <div class="mb-3">
                                        <p class="text-[11px] font-extrabold text-[#8B5CF6] uppercase tracking-wider m-0 mb-1.5">
                                            <i class="fa fa-crosshairs mr-1"></i> Objective {{ count($parts) > 1 ? $partIndex + 1 : '' }}
                                        </p>
                                        <p class="text-[13px] font-medium text-[#1E2657] m-0 leading-relaxed">{{ $objectiveText }}</p>
                                    </div>
                                @endif

                                @if($activitiesText)
                                    <div class="ml-0 sm:ml-4 mt-2">
                                        <p class="text-[11px] font-extrabold text-[#F59E0B] uppercase tracking-wider m-0 mb-2">
                                            <i class="fa fa-list-check mr-1"></i> Activities
                                        </p>
                                        @php
                                            // Split activities by [ ] or [x] markers
                                            $activities = preg_split('/\[\s*[x]?\s*\]/', $activitiesText, -1, PREG_SPLIT_NO_EMPTY);
                                            preg_match_all('/\[(\s*x?\s*)\]/', $activitiesText, $checkmarks);
                                        @endphp
                                        <div class="space-y-2">
                                            @foreach($activities as $actIndex => $activity)
                                                @php $activity = trim($activity); @endphp
                                                @if($activity)
                                                    @php $isChecked = isset($checkmarks[1][$actIndex]) && str_contains($checkmarks[1][$actIndex], 'x'); @endphp
                                                    <div class="flex items-start gap-2">
                                                        <i class="fa {{ $isChecked ? 'fa-square-check text-[#10B981]' : 'fa-square text-[#D1D5DB]' }} text-[13px] mt-0.5 shrink-0"></i>
                                                        <p class="text-[12px] font-medium text-[#6B7086] m-0 leading-relaxed">{{ $activity }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Simple description without structure --}}
                            <p class="text-[13px] font-medium text-[#6B7086] m-0 leading-relaxed whitespace-pre-line">{{ $description }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-full bg-[#F4F6FA] flex items-center justify-center mb-3 border border-black/5">
                        <i class="fa fa-bullseye text-xl text-[#9CA0AF]"></i>
                    </div>
                    <h4 class="text-[14px] font-extrabold text-[#1E2657] m-0 mb-1">No IEP goals recorded</h4>
                    <p class="text-[12px] font-medium text-[#6B7086] m-0">No goals were found in this archive snapshot.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ===== ACADEMIC & PROGRESS SUMMARIES (Quarterly, Mid-Year, Annual) ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-black/10 bg-[#F4F6FA] flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 flex items-center">
                <div class="w-10 h-10 rounded-full bg-[#10B981]/10 flex items-center justify-center text-[#10B981] mr-3 shadow-sm border border-black/5 shrink-0">
                    <i class="fa fa-file-contract"></i>
                </div>
                Academic & Progress Summaries
            </h3>
            <span class="text-[12px] font-extrabold text-[#9CA0AF] bg-white px-3 py-1 rounded-full border border-black/10">
                {{ count($archive->progress_snapshot ?? []) }} summaries
            </span>
        </div>

        <div class="p-5 sm:p-6 space-y-4 max-h-[600px] overflow-y-auto custom-scrollbar">
            @forelse($archive->progress_snapshot ?? [] as $report)
                @php
                    $isReportFormat = isset($report['report_type']) || isset($report['content']);
                @endphp
                @if($isReportFormat)
                    @php
                        $typeLabels = [
                            'q1' => '1st Quarter (Q1) Progress Summary',
                            'q2' => '2nd Quarter (Q2) Progress Summary',
                            'q3' => '3rd Quarter (Q3) Progress Summary',
                            'q4' => '4th Quarter (Q4) Progress Summary',
                            'quarterly' => 'Quarterly Progress Summary',
                            'mid_year' => 'Mid-Year Comprehensive Summary',
                            'year_end' => 'Annual / Year-End Comprehensive Summary',
                            'annual' => 'Annual / Year-End Comprehensive Summary',
                        ];
                        $reportType = $report['report_type'] ?? 'summary';
                        $reportTitle = $typeLabels[$reportType] ?? ucwords(str_replace('_', ' ', $reportType)) . ' Summary';
                        $reportDate = isset($report['report_date']) && $report['report_date'] 
                            ? \Carbon\Carbon::parse($report['report_date'])->format('M j, Y') 
                            : (isset($report['created_at']) ? \Carbon\Carbon::parse($report['created_at'])->format('M j, Y') : 'N/A');
                    @endphp
                    <div class="bg-white border border-black/10 rounded-xl p-5 hover:border-[#10B981]/30 hover:shadow-sm transition-all">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-3 border-b border-black/5">
                            <span class="text-[13px] font-extrabold text-[#1E2657] uppercase tracking-wide flex items-center gap-2">
                                <i class="fa fa-file-lines text-[#10B981]"></i> {{ $reportTitle }}
                            </span>
                            <span class="text-[11px] font-extrabold text-[#6B7086] bg-[#F4F6FA] px-2.5 py-1 rounded-md">
                                <i class="fa fa-calendar-alt mr-1 text-[#9CA0AF]"></i> {{ $reportDate }}
                            </span>
                        </div>
                        <p class="text-[13px] font-medium text-[#1E2657] m-0 leading-relaxed whitespace-pre-wrap">{{ $report['content'] ?? 'No content' }}</p>
                    </div>
                @else
                    {{-- Fallback display for legacy archives that stored daily logs --}}
                    <div class="bg-white border border-black/10 rounded-xl p-4 hover:border-[#F59E0B]/30 hover:shadow-sm transition-all">
                        <div class="flex items-start gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B] mt-1.5 shrink-0 shadow-sm"></span>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <p class="text-[13px] font-extrabold text-[#1E2657] m-0">
                                        {{ isset($report['log_date']) ? \Carbon\Carbon::parse($report['log_date'])->format('M j, Y') : 'Unknown date' }}
                                    </p>
                                    @if(isset($report['quarter']))
                                        <span class="text-[10px] font-extrabold text-[#6B7086] bg-[#F4F6FA] px-2 py-0.5 rounded-full">{{ $report['quarter'] }}</span>
                                    @endif
                                </div>
                                <p class="text-[12px] font-medium text-[#6B7086] m-0 leading-relaxed">{{ $report['notes'] ?? 'No notes' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-full bg-[#F4F6FA] flex items-center justify-center mb-3 border border-black/5">
                        <i class="fa fa-file-contract text-xl text-[#9CA0AF]"></i>
                    </div>
                    <h4 class="text-[14px] font-extrabold text-[#1E2657] m-0 mb-1">No summary reports recorded</h4>
                    <p class="text-[12px] font-medium text-[#6B7086] m-0">No summary reports were found in this archive snapshot.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
