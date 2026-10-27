@extends('layouts.app')

@section('title', 'Manage Academic Calendar & School Years')

@section('content')

@php
    $baseYear = (int) date('Y');
    $existingNames = $schoolYears->pluck('name')->all();
    $existingNamesLower = array_map('strtolower', $existingNames);

    // Build candidate academic years (Years only: 2024-2025, 2025-2026, 2026-2027...)
    $candidateYears = [];
    for ($y = $baseYear - 2; $y <= $baseYear + 7; $y++) {
        $range = $y . '-' . ($y + 1);
        $isTaken = in_array($range, $existingNames)
                || in_array('SY ' . $range, $existingNames)
                || in_array(strtolower($range), $existingNamesLower)
                || in_array(strtolower('SY ' . $range), $existingNamesLower);
        $candidateYears[] = [
            'label'   => $range,
            'start'   => $y,
            'end'     => $y + 1,
            'isTaken' => $isTaken,
        ];
    }

    // Default candidate: first untaken candidate from current year onwards
    $defaultCandidateItem = collect($candidateYears)->first(function($item) use ($baseYear) {
        return !$item['isTaken'] && $item['start'] >= $baseYear;
    }) ?: collect($candidateYears)->firstWhere('isTaken', false);

    $defaultCandidate = $defaultCandidateItem ? $defaultCandidateItem['label'] : ($baseYear . '-' . ($baseYear + 1));
    $defaultStartYear = $defaultCandidateItem ? $defaultCandidateItem['start'] : $baseYear;
    $defaultEndYear   = $defaultCandidateItem ? $defaultCandidateItem['end'] : ($baseYear + 1);
@endphp

<script>
function academicCalendarManager() {
    return {
        openAddModal: false,
        selectedYearId: '{{ $selectedYearId }}',
        switchYear: function(id) {
            this.selectedYearId = id;
            var url = new URL(window.location);
            url.searchParams.set('year', id);
            window.history.replaceState({}, '', url);
        },

        // Modal creation state
        modalMode: 'dropdown', // 'dropdown' or 'selectors'
        selectedRange: '{{ $defaultCandidate }}',
        startYear: {{ $defaultStartYear }},
        endYear: {{ $defaultEndYear }},
        existingNames: {{ json_encode($existingNamesLower) }},

        get computedYearRange() {
            if (this.modalMode === 'dropdown') {
                return this.selectedRange;
            } else if (this.modalMode === 'selectors') {
                return this.startYear + '-' + this.endYear;
            }
            return '';
        },

        get computedRawName() {
            var range = this.computedYearRange;
            if (!range) return '';
            return 'SY ' + range;
        },

        get isDuplicate() {
            var target = this.computedRawName.toLowerCase();
            return target !== '' && this.existingNames.includes(target);
        },

        // Confirmation modal state for Set Active & Delete (Requires typing "CONFIRM")
        confirmModalOpen: false,
        confirmActionType: '', // 'set_active' or 'delete'
        confirmSyId: null,
        confirmSyName: '',
        confirmActionUrl: '',
        confirmInputText: '',

        openSetActiveConfirm: function(syId, syName, actionUrl) {
            this.confirmActionType = 'set_active';
            this.confirmSyId = syId;
            this.confirmSyName = syName;
            this.confirmActionUrl = actionUrl;
            this.confirmInputText = '';
            this.confirmModalOpen = true;
            this.$nextTick(function() {
                var el = document.getElementById('syConfirmInput');
                if (el) el.focus();
            });
        },

        openDeleteConfirm: function(syId, syName, actionUrl) {
            this.confirmActionType = 'delete';
            this.confirmSyId = syId;
            this.confirmSyName = syName;
            this.confirmActionUrl = actionUrl;
            this.confirmInputText = '';
            this.confirmModalOpen = true;
            this.$nextTick(function() {
                var el = document.getElementById('syConfirmInput');
                if (el) el.focus();
            });
        },

        closeConfirmModal: function() {
            this.confirmModalOpen = false;
            this.confirmInputText = '';
        }
    };
}
</script>

<!-- Main Container -->
<div x-data="academicCalendarManager()" class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

    <!-- Flash Messages -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
        <div class="flex items-center">
            <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
            <span class="font-extrabold text-sm">{{ session('success') }}</span>
        </div>
        <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
            <i class="fa fa-times text-lg"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down">
        <div class="flex items-center">
            <i class="fa fa-exclamation-triangle mr-3 text-rose-500 text-lg"></i>
            <span class="font-extrabold text-sm">{{ $errors->first() }}</span>
        </div>
        <button @click="show = false" type="button" class="text-rose-500 hover:text-rose-700 transition-colors">
            <i class="fa fa-times text-lg"></i>
        </button>
    </div>
    @endif

    <!-- Header Section with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight mb-2 flex items-center">
                <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm border border-[#3B82F6]/20 shrink-0">
                    <i class="fa fa-calendar-days text-lg"></i>
                </div>
                Academic Calendar & School Years
            </h1>
            <p class="text-[13px] font-medium text-[#6B7086] m-0 sm:ml-14">
                Hybrid Activation: Administrators manually set the live School Year. The system automatically switches quarters based on real-world dates within the active year.
            </p>
        </div>

        <div class="sm:self-end">
            <button @click="openAddModal = true" type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 hover:-translate-y-0.5">
                <i class="fa fa-plus mr-2 text-xs"></i> Add New School Year
            </button>
        </div>
    </div>

    <!-- Active School Year Summary Banner -->
    <div class="bg-gradient-to-r from-[#1E2657] to-[#2B3577] text-white rounded-2xl p-6 shadow-sm border border-[#1E2657]/20 relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 uppercase tracking-widest">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Currently Active School Year
                    </span>
                    <span class="text-xs text-slate-300">|</span>
                    <span class="text-xs font-semibold text-slate-300">Live for Teachers & Guardians</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white m-0">
                    {{ $activeSchoolYear ? $activeSchoolYear->name : 'No Active School Year Set' }}
                </h2>
            </div>

            <div class="flex items-center gap-3 bg-white/10 backdrop-blur-sm px-4 py-3 rounded-xl border border-white/10 self-start md:self-auto">
                <div class="w-8 h-8 rounded-lg bg-[#3B82F6]/30 flex items-center justify-center text-blue-200">
                    <i class="fa fa-clock"></i>
                </div>
                <div>
                    @php
                        $liveActiveQuarter = $activeSchoolYear ? $activeSchoolYear->quarters->where('is_active', true)->first() : null;
                    @endphp
                    <div class="text-[10px] uppercase font-bold text-slate-300 tracking-wider">Current Live Quarter</div>
                    <div class="text-sm font-extrabold text-white">
                        @if($liveActiveQuarter)
                            <span class="text-emerald-300">{{ $liveActiveQuarter->name }}</span>
                            <span class="text-[11px] font-medium text-slate-300">
                                ({{ $liveActiveQuarter->start_date ? \Carbon\Carbon::parse($liveActiveQuarter->start_date)->format('M d') : '' }} - {{ $liveActiveQuarter->end_date ? \Carbon\Carbon::parse($liveActiveQuarter->end_date)->format('M d, Y') : '' }})
                            </span>
                        @else
                            <span class="text-amber-300">Standby (Between Quarters)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Year Switcher Bar -->
    @if($schoolYears->isNotEmpty())
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-black/15 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 rounded-xl bg-[#3B82F6]/10 text-[#3B82F6] flex items-center justify-center font-bold text-sm">
                    <i class="fa fa-layer-group text-xs"></i>
                </div>
                <label for="schoolYearSwitcher" class="text-xs font-black text-[#1E2657] uppercase tracking-wider block">
                    Switch School Year:
                </label>
            </div>

            <div class="relative w-full sm:w-[320px]">
                <select id="schoolYearSwitcher"
                        x-model="selectedYearId"
                        @change="switchYear($event.target.value)"
                        class="w-full text-[13px] font-extrabold text-[#1E2657] bg-[#F4F6FA] hover:bg-slate-100 border border-black/15 rounded-xl pl-4 pr-10 py-2.5 focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition-all cursor-pointer shadow-sm appearance-none">
                    @foreach($schoolYears as $sy)
                        <option value="{{ $sy->id }}">
                            {{ $sy->name }} {{ $sy->is_active ? '— (Active Year ★)' : '— (Standby)' }}
                        </option>
                    @endforeach
                    @if($schoolYears->count() > 1)
                        <option value="all">— View All School Years ({{ $schoolYears->count() }}) —</option>
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-[#6B7086]">
                    <i class="fa fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Active Info & Quick Jump Button -->
        <div class="flex items-center gap-3 self-start md:self-auto">
            <span class="text-xs text-[#6B7086] font-medium hidden lg:inline">Viewing calendar schedule for:</span>
            <div class="flex items-center gap-2">
                @foreach($schoolYears as $sy)
                    <div x-show="selectedYearId == '{{ $sy->id }}'" class="flex items-center gap-2">
                        <span class="text-xs font-black text-[#1E2657]">{{ $sy->name }}</span>
                        @if($sy->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live in System
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                Standby
                            </span>
                        @endif
                    </div>
                @endforeach

                <div x-show="selectedYearId === 'all'" class="text-xs font-black text-[#1E2657]">
                    All School Years ({{ $schoolYears->count() }})
                </div>

                @if($activeSchoolYear)
                    <button type="button"
                            x-show="selectedYearId != '{{ $activeSchoolYear->id }}'"
                            @click="switchYear('{{ $activeSchoolYear->id }}')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-all shadow-sm ml-2">
                        <i class="fa fa-arrow-turn-up text-[10px]"></i>
                        Back to Active Year ({{ $activeSchoolYear->name }})
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- School Years List -->
    <div class="space-y-6">
        @forelse($schoolYears as $sy)
        <div x-show="selectedYearId === 'all' || selectedYearId == '{{ $sy->id }}'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white rounded-2xl shadow-sm border {{ $sy->is_active ? 'border-[#3B82F6]/40 ring-1 ring-[#3B82F6]/20' : 'border-black/15' }} overflow-hidden transition-all duration-200">
            
            <!-- School Year Header Card -->
            <div class="px-6 py-4 {{ $sy->is_active ? 'bg-gradient-to-r from-[#3B82F6]/5 via-white to-white border-b border-[#3B82F6]/20' : 'bg-[#F4F6FA] border-b border-black/10' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-lg sm:text-xl font-extrabold {{ $sy->is_active ? 'text-[#3B82F6]' : 'text-[#1E2657]' }}">
                        {{ $sy->name }}
                    </span>

                    @if($sy->is_active)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active Year
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                            <i class="fa fa-pause-circle text-[10px]"></i>
                            Standby / Inactive
                        </span>
                    @endif
                </div>

                <!-- Year Actions -->
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    @if(!$sy->is_active)
                        <button type="button" 
                                @click="openSetActiveConfirm('{{ $sy->id }}', '{{ addslashes($sy->name) }}', '{{ route('admin.school-years.set-active', $sy->id) }}')"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[12px] font-extrabold rounded-xl transition-all shadow-sm hover:-translate-y-0.5 cursor-pointer" 
                                title="Make this School Year the active one">
                            <i class="fa fa-check mr-1.5 text-xs"></i> Set Active Year
                        </button>

                        <button type="button" 
                                @click="openDeleteConfirm('{{ $sy->id }}', '{{ addslashes($sy->name) }}', '{{ route('admin.school-years.destroy', $sy->id) }}')"
                                class="inline-flex items-center px-3 py-2 bg-white hover:bg-rose-50 text-rose-600 hover:text-rose-700 border border-rose-200 hover:border-rose-300 text-[12px] font-bold rounded-xl transition-colors shadow-sm cursor-pointer" 
                                title="Delete School Year">
                            <i class="fa fa-trash-can mr-1.5 text-xs"></i> Delete
                        </button>
                    @else
                        <span class="inline-flex items-center text-[12px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
                            <i class="fa fa-circle-check mr-1.5"></i> Live in System
                        </span>
                    @endif
                </div>
            </div>

            <!-- Quarter Settings Form -->
            <form action="{{ route('admin.quarters.update', $sy->id) }}" method="POST" class="m-0 calendar-form" id="calendarForm_{{ $sy->id }}">
                @csrf

                <div class="overflow-x-hidden md:overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse block md:table whitespace-normal md:whitespace-nowrap">
                        <thead class="hidden md:table-header-group bg-slate-50/50 border-b border-black/5">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[18%]">Quarter</th>
                                <th scope="col" class="px-6 py-3.5 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">Start Date</th>
                                <th scope="col" class="px-6 py-3.5 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">End Date</th>
                                <th scope="col" class="px-6 py-3.5 text-center text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[22%]">Status</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group divide-y divide-black/5 bg-white">
                            @foreach($sy->quarters as $index => $quarter)
                            @php
                                $status = $quarter->computed_status;
                                $isLiveQuarter = $sy->is_active && $quarter->is_active;
                            @endphp
                            <tr class="block md:table-row relative md:static p-4 md:p-0 hover:bg-slate-50/80 transition-colors duration-150 {{ $isLiveQuarter ? 'bg-[#3B82F6]/5' : '' }}">
                                
                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-4 mb-3 md:mb-0">
                                    <span class="text-base font-extrabold {{ $isLiveQuarter ? 'text-[#3B82F6]' : 'text-[#1E2657]' }}">
                                        {{ $quarter->name }}
                                    </span>
                                    @if($isLiveQuarter)
                                    <span class="ml-2 px-2 py-0.5 rounded text-[9px] font-extrabold bg-[#3B82F6]/10 text-[#3B82F6] uppercase tracking-widest border border-[#3B82F6]/20 inline-block">Active Now</span>
                                    @endif
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-4 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Start Date</span>
                                    <input type="date"
                                        id="start_date_{{ $quarter->id }}"
                                        name="quarters[{{ $quarter->id }}][start_date]"
                                        value="{{ old('quarters.'.$quarter->id.'.start_date', $quarter->start_date_formatted) }}"
                                        class="start-date-input quarter-date-input-{{ $sy->id }} w-full max-w-full md:max-w-[210px] rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                        data-quarter-id="{{ $quarter->id }}"
                                        data-sy-id="{{ $sy->id }}"
                                        data-quarter-index="{{ $index }}">
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-4 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">End Date</span>
                                    <input type="date"
                                        id="end_date_{{ $quarter->id }}"
                                        name="quarters[{{ $quarter->id }}][end_date]"
                                        value="{{ old('quarters.'.$quarter->id.'.end_date', $quarter->end_date_formatted) }}"
                                        class="end-date-input quarter-date-input-{{ $sy->id }} w-full max-w-full md:max-w-[210px] rounded-xl border-black/15 px-3.5 py-2 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                        data-quarter-id="{{ $quarter->id }}"
                                        data-sy-id="{{ $sy->id }}"
                                        data-quarter-index="{{ $index }}">
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-4 text-left md:text-center absolute md:static top-4 right-4">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</span>
                                    @if($isLiveQuarter)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active Now
                                    </span>
                                    @elseif(!$sy->is_active && $quarter->start_date && $quarter->end_date)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-slate-50 text-slate-500 border border-slate-200" title="This year is not currently active">
                                        <i class="fa fa-pause text-[9px]"></i>
                                        Standby
                                    </span>
                                    @elseif($status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                        <i class="fa fa-check-circle text-[10px]"></i>
                                        Completed
                                    </span>
                                    @elseif($status === 'upcoming')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-600 border border-amber-200">
                                        <i class="fa fa-clock text-[10px]"></i>
                                        Upcoming
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-gray-50 text-gray-400 border border-gray-200">
                                        <i class="fa fa-minus-circle text-[10px]"></i>
                                        Not Set
                                    </span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="px-5 sm:px-6 py-3.5 bg-[#F4F6FA] border-t border-black/10 flex justify-between items-center">
                    <span class="text-xs text-[#6B7086] font-medium hidden sm:inline">
                        Saving commits the date range. Quarters will automatically activate once this School Year is live.
                    </span>
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 hover:-translate-y-0.5">
                        <i class="fa fa-save mr-2 text-xs"></i> Save Calendar Settings
                    </button>
                </div>
            </form>

        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 text-center border border-black/15 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-blue-50 text-[#3B82F6] flex items-center justify-center mx-auto mb-4">
                <i class="fa fa-calendar-plus text-2xl"></i>
            </div>
            <h3 class="text-lg font-extrabold text-[#1E2657] mb-1">No School Years Found</h3>
            <p class="text-sm text-[#6B7086] max-w-md mx-auto mb-6">Create a School Year to provision Q1–Q4 and configure your academic schedule.</p>
            <button @click="openAddModal = true" type="button" class="inline-flex items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm">
                <i class="fa fa-plus mr-2 text-xs"></i> Add School Year
            </button>
        </div>
        @endforelse
    </div>

    <!-- Modal: Add New School Year -->
    <div x-show="openAddModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="openAddModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openAddModal = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="openAddModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-black/10">

                <form action="{{ route('admin.school-years.store') }}" method="POST" class="m-0">
                    @csrf

                    <!-- Modal Header -->
                    <div class="px-6 pt-6 pb-4 border-b border-black/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-[#3B82F6] flex items-center justify-center">
                                <i class="fa fa-calendar-plus text-base"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#1E2657] m-0" id="modal-title">Add New School Year</h3>
                                <p class="text-xs text-[#6B7086] m-0">Provisions a fresh template for Q1–Q4.</p>
                            </div>
                        </div>
                        <button @click="openAddModal = false" type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fa fa-times text-base"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-5 space-y-4">

                        <!-- Mode Switcher Tabs -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-extrabold text-[#1E2657] uppercase tracking-wider m-0">
                                    Academic Year <span class="text-rose-500">*</span>
                                </label>
                                <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-[11px] font-bold">
                                    <button type="button"
                                            @click="modalMode = 'dropdown'"
                                            :class="modalMode === 'dropdown' ? 'bg-white text-[#3B82F6] shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                                        Choose Years
                                    </button>
                                    <button type="button"
                                            @click="modalMode = 'selectors'"
                                            :class="modalMode === 'selectors' ? 'bg-white text-[#3B82F6] shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                                        Start / End
                                    </button>
                                </div>
                            </div>

                            <!-- Option 1: Dropdown Choose Years Only -->
                            <div x-show="modalMode === 'dropdown'" x-cloak>
                                <div class="relative">
                                    <select id="modal_year_preset"
                                            x-model="selectedRange"
                                            class="w-full text-sm font-extrabold text-[#1E2657] bg-[#F4F6FA] hover:bg-slate-100 border border-black/15 rounded-xl pl-4 pr-10 py-2.5 focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 transition-all cursor-pointer shadow-sm appearance-none">
                                        @foreach($candidateYears as $cand)
                                            <option value="{{ $cand['label'] }}" {{ $cand['isTaken'] ? 'disabled' : '' }}>
                                                SY {{ $cand['label'] }} {{ $cand['isTaken'] ? '— (Already exists)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-[#6B7086]">
                                        <i class="fa fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                <p class="text-[11px] text-[#6B7086] mt-1.5 mb-0">
                                    Select the school year dates. Existing years are disabled to avoid duplication.
                                </p>
                            </div>

                            <!-- Option 2: Choose Start & End Years -->
                            <div x-show="modalMode === 'selectors'" x-cloak>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-[#6B7086] mb-1">Start Year</label>
                                        <select x-model="startYear"
                                                @change="endYear = parseInt(startYear) + 1"
                                                class="w-full text-sm font-extrabold text-[#1E2657] bg-[#F4F6FA] border border-black/15 rounded-xl px-3.5 py-2.5 focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 shadow-sm cursor-pointer">
                                            @for($y = $baseYear - 2; $y <= $baseYear + 10; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-[#6B7086] mb-1">End Year</label>
                                        <select x-model="endYear"
                                                class="w-full text-sm font-extrabold text-[#1E2657] bg-[#F4F6FA] border border-black/15 rounded-xl px-3.5 py-2.5 focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 shadow-sm cursor-pointer">
                                            @for($y = $baseYear - 1; $y <= $baseYear + 11; $y++)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <p class="text-[11px] text-[#6B7086] mt-1.5 mb-0">
                                    End year auto-advances to the subsequent year when Start Year changes.
                                </p>
                            </div>
                        </div>

                        <!-- Live Name Preview -->
                        <div class="bg-blue-50/70 border border-blue-200/70 rounded-xl p-3 flex items-center justify-between">
                            <span class="text-xs font-bold text-[#1E2657] flex items-center gap-1.5">
                                <i class="fa fa-tag text-[#3B82F6]"></i>
                                School Year Label:
                            </span>
                            <span class="text-sm font-black text-[#1E2657] font-mono bg-white px-3 py-1 rounded-lg border border-blue-200 shadow-2xs"
                                  x-text="computedRawName || '—'"></span>
                        </div>

                        <!-- Duplicate Warning -->
                        <div x-show="isDuplicate" x-cloak class="bg-rose-50 border border-rose-200 text-rose-700 px-3.5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2">
                            <i class="fa fa-triangle-exclamation text-rose-500 text-sm"></i>
                            <span>A School Year with this name already exists in the system.</span>
                        </div>

                        <!-- Hidden Input bound to computedRawName -->
                        <input type="hidden" name="school_year_name" :value="computedRawName">

                        <div class="bg-blue-50/60 border border-blue-100 rounded-xl p-3.5 flex items-start gap-2.5">
                            <i class="fa fa-info-circle text-[#3B82F6] mt-0.5 text-xs"></i>
                            <p class="text-xs text-slate-600 leading-relaxed m-0">
                                The new school year is created in <strong>Standby mode</strong>. It will not become live until you input quarter dates and explicitly click <strong>"Set Active Year"</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-[#F4F6FA] border-t border-black/5 flex justify-end gap-2.5">
                        <button @click="openAddModal = false" type="button" class="px-4 py-2 text-xs font-bold text-[#6B7086] hover:text-[#1E2657] hover:bg-slate-200/50 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="!computedRawName || isDuplicate"
                                :class="(!computedRawName || isDuplicate) ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-[#3B82F6] hover:bg-blue-600 shadow-sm hover:-translate-y-0.5 cursor-pointer'"
                                class="px-5 py-2 text-white text-xs font-extrabold rounded-xl transition-all">
                            Create School Year
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Set Active & Delete (Requires typing CONFIRM) -->
    <div x-show="confirmModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="confirm-action-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="confirmModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" 
                 @click="closeConfirmModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="confirmModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-black/10">

                <form :action="confirmActionUrl" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmActionType === 'set_active' ? 'PATCH' : 'DELETE'">

                    <div class="p-6 sm:p-7 space-y-5">
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-xs"
                                 :class="confirmActionType === 'delete' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                                <template x-if="confirmActionType === 'delete'">
                                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                                </template>
                                <template x-if="confirmActionType === 'set_active'">
                                    <i class="fa-solid fa-calendar-check text-xl"></i>
                                </template>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-extrabold text-[#1E2657] m-0" id="confirm-action-modal-title">
                                    <span x-show="confirmActionType === 'set_active'">Set Active School Year</span>
                                    <span x-show="confirmActionType === 'delete'">Delete School Year</span>
                                </h4>
                                <p class="text-xs text-[#6B7086] mt-1.5 font-medium leading-relaxed">
                                    <template x-if="confirmActionType === 'set_active'">
                                        <span>
                                            Are you sure you want to make <strong class="text-emerald-700" x-text="confirmSyName"></strong> the active School Year? This will switch active quarters and live reporting across the school.
                                        </span>
                                    </template>
                                    <template x-if="confirmActionType === 'delete'">
                                        <span>
                                            Are you sure you want to delete <strong class="text-rose-600" x-text="confirmSyName"></strong> and all associated quarter dates? This action cannot be undone.
                                        </span>
                                    </template>
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl p-3 text-[11px] font-semibold leading-relaxed"
                             :class="confirmActionType === 'delete' ? 'bg-rose-50/80 border border-rose-200/80 text-rose-700' : 'bg-emerald-50/80 border border-emerald-200/80 text-emerald-700'">
                            <template x-if="confirmActionType === 'delete'">
                                <div><i class="fa-solid fa-shield-halved mr-1 text-rose-500"></i> Deleting this school year permanently removes its academic quarters schedule.</div>
                            </template>
                            <template x-if="confirmActionType === 'set_active'">
                                <div><i class="fa-solid fa-info-circle mr-1 text-emerald-500"></i> Teachers and guardians will immediately begin viewing records for this school year.</div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-wider mb-1.5">
                                Type <span class="text-rose-600 font-mono font-black">CONFIRM</span> to verify authorization:
                            </label>
                            <input type="text"
                                   id="syConfirmInput"
                                   x-model="confirmInputText"
                                   class="w-full text-center text-base font-mono font-black tracking-widest py-2.5 px-3.5 rounded-xl border border-black/20 focus:border-[#3B82F6] focus:ring-2 focus:ring-[#3B82F6]/20 shadow-sm bg-white"
                                   required
                                   placeholder="Type CONFIRM"
                                   autocomplete="off">
                            <p class="text-[10px] text-[#9CA0AF] mt-1.5 font-medium">Please type <strong>CONFIRM</strong> in capital letters to enable the button below.</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-[#F4F6FA] border-t border-black/5 flex justify-end gap-2.5">
                        <button type="button"
                                @click="closeConfirmModal()"
                                class="px-4 py-2.5 text-xs font-bold text-[#6B7086] hover:text-[#1E2657] hover:bg-slate-200/50 rounded-xl transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="confirmInputText.trim() !== 'CONFIRM'"
                                :class="confirmInputText.trim() === 'CONFIRM'
                                    ? (confirmActionType === 'delete' ? 'bg-rose-600 hover:bg-rose-700 cursor-pointer shadow-sm hover:-translate-y-0.5' : 'bg-emerald-600 hover:bg-emerald-700 cursor-pointer shadow-sm hover:-translate-y-0.5')
                                    : 'bg-slate-300 opacity-50 cursor-not-allowed'"
                                class="px-5 py-2.5 text-white text-xs font-extrabold rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <template x-if="confirmActionType === 'set_active'">
                                <span><i class="fa fa-check mr-1.5"></i> Confirm Set Active</span>
                            </template>
                            <template x-if="confirmActionType === 'delete'">
                                <span><i class="fa fa-trash-can mr-1.5"></i> Confirm Delete</span>
                            </template>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function getNextDay(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return '';
            const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
            date.setDate(date.getDate() + 1);
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        // Apply constraints per school year form
        document.querySelectorAll('.calendar-form').forEach(form => {
            const startInputs = Array.from(form.querySelectorAll('.start-date-input'));
            const endInputs = Array.from(form.querySelectorAll('.end-date-input'));

            function syncDateConstraints() {
                startInputs.forEach((startInput, idx) => {
                    const endInput = endInputs[idx];
                    if (!endInput) return;

                    // 1. Enforce End Date min >= Start Date
                    if (startInput.value) {
                        endInput.min = startInput.value;
                        if (endInput.value && endInput.value < startInput.value) {
                            endInput.value = startInput.value;
                        }
                    } else {
                        endInput.min = '';
                    }

                    // 2. Enforce Next Quarter Start Date min > Current Quarter End Date
                    if (idx < startInputs.length - 1) {
                        const nextStartInput = startInputs[idx + 1];
                        if (endInput.value) {
                            const minNextStart = getNextDay(endInput.value);
                            nextStartInput.min = minNextStart;
                            if (nextStartInput.value && nextStartInput.value < minNextStart) {
                                nextStartInput.value = minNextStart;
                            }
                        } else if (startInput.value) {
                            const minNextStart = getNextDay(startInput.value);
                            nextStartInput.min = minNextStart;
                            if (nextStartInput.value && nextStartInput.value < minNextStart) {
                                nextStartInput.value = minNextStart;
                            }
                        } else {
                            nextStartInput.min = '';
                        }
                    }
                });
            }

            syncDateConstraints();

            form.querySelectorAll('input[type="date"]').forEach(input => {
                input.addEventListener('change', syncDateConstraints);
                input.addEventListener('input', syncDateConstraints);
            });
        });
    });
</script>
@endpush

@endsection