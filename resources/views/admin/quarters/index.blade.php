@extends('layouts.app')

@section('title', 'Manage Academic Calendar')

@section('content')

<!-- Main Container -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8">

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

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight mb-2 flex items-center">
            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm border border-[#3B82F6]/20 shrink-0">
                <i class="fa fa-calendar-days text-lg"></i>
            </div>
            Academic Calendar
        </h1>
        <p class="text-[13px] font-medium text-[#6B7086] m-0 sm:ml-14">Set the start and end dates for each quarter. The active status is <strong>automatically determined</strong> based on today's date, and quarter dates <strong>cannot overlap</strong>.</p>
    </div>

    <!-- Calendar Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        <form action="{{ route('admin.quarters.update') }}" method="POST" class="m-0" id="calendarForm">
            @csrf

            <div class="overflow-x-hidden md:overflow-x-auto w-full">
                <table class="w-full text-left border-collapse block md:table whitespace-normal md:whitespace-nowrap">

                    <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[15%]">Quarter</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">Start Date</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">End Date</th>
                            <th scope="col" class="px-6 py-4 text-center text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[25%]">Status</th>
                        </tr>
                    </thead>

                    <tbody class="block md:table-row-group divide-y divide-black/5 bg-white">
                        @foreach($quarters as $index => $quarter)
                            @php
                                $status = $quarter->computed_status;
                            @endphp
                            <tr id="quarter_row_{{ $quarter->id }}"
                                data-quarter-id="{{ $quarter->id }}"
                                data-quarter-name="{{ $quarter->name }}"
                                class="quarter-row block md:table-row relative md:static p-4 md:p-0 hover:bg-slate-50 transition-colors duration-150
                                {{ $status === 'active' ? 'bg-[#3B82F6]/5' : '' }}">

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-4 md:mb-0">
                                    <span class="quarter-name-label text-lg font-extrabold {{ $status === 'active' ? 'text-[#3B82F6]' : 'text-[#1E2657]' }}">
                                        {{ $quarter->name }}
                                    </span>
                                    <span id="active_indicator_{{ $quarter->id }}" class="ml-2 px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#3B82F6]/10 text-[#3B82F6] uppercase tracking-widest border border-[#3B82F6]/20 {{ $status === 'active' ? 'inline-block' : 'hidden' }}">
                                        Active Now
                                    </span>
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Start Date</span>
                                    <input type="date"
                                           id="start_date_{{ $quarter->id }}"
                                           name="quarters[{{ $quarter->id }}][start_date]"
                                           value="{{ old('quarters.'.$quarter->id.'.start_date', $quarter->start_date_formatted) }}"
                                           class="start-date-input quarter-date-input w-full max-w-full md:max-w-[200px] rounded-xl border border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                           data-quarter-id="{{ $quarter->id }}"
                                           data-quarter-name="{{ $quarter->name }}"
                                           data-quarter-index="{{ $index }}">
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">End Date</span>
                                    <input type="date"
                                           id="end_date_{{ $quarter->id }}"
                                           name="quarters[{{ $quarter->id }}][end_date]"
                                           value="{{ old('quarters.'.$quarter->id.'.end_date', $quarter->end_date_formatted) }}"
                                           class="end-date-input quarter-date-input w-full max-w-full md:max-w-[200px] rounded-xl border border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                           data-quarter-id="{{ $quarter->id }}"
                                           data-quarter-name="{{ $quarter->name }}"
                                           data-quarter-index="{{ $index }}">
                                    <div id="error_msg_{{ $quarter->id }}" class="hidden text-[11px] font-bold text-rose-600 mt-1"></div>
                                </td>

                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 text-left md:text-center absolute md:static top-4 right-4" id="status_cell_{{ $quarter->id }}">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</span>
                                    @if($status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active Now
                                        </span>
                                    @elseif($status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                            <i class="fa fa-check-circle text-[10px]"></i>
                                            Completed
                                        </span>
                                    @elseif($status === 'upcoming')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-600 border border-amber-200">
                                            <i class="fa fa-clock text-[10px]"></i>
                                            Upcoming
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-gray-50 text-gray-400 border border-gray-200">
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
            <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div id="calendar_validation_summary" class="text-[12px] font-bold text-rose-600 hidden flex items-center">
                    <i class="fa fa-circle-exclamation mr-1.5"></i>
                    <span id="calendar_validation_text"></span>
                </div>
                <button type="submit" id="saveCalendarBtn" class="w-full md:w-auto inline-flex justify-center md:inline-flex items-center px-6 py-3 md:py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 hover:-translate-y-0.5 cursor-pointer ml-auto">
                    <i class="fa fa-save md:mr-2 mr-1.5"></i> Save Calendar Settings
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const rows = Array.from(document.querySelectorAll('.quarter-row'));
        const form = document.getElementById('calendarForm');
        const validationSummary = document.getElementById('calendar_validation_summary');
        const validationText = document.getElementById('calendar_validation_text');
        const saveBtn = document.getElementById('saveCalendarBtn');

        // Helper to add or subtract calendar days without timezone shifts
        function addDays(dateStr, numDays) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            d.setDate(d.getDate() + numDays);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        // Get local today string YYYY-MM-DD
        function getLocalTodayString() {
            const now = new Date();
            const y = now.getFullYear();
            const m = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function validateAndSyncCalendar() {
            const todayStr = getLocalTodayString();
            let hasGlobalError = false;
            let firstErrorMessage = '';

            // Reset validation states
            rows.forEach(row => {
                const qId = row.dataset.quarterId;
                const startInput = document.getElementById(`start_date_${qId}`);
                const endInput = document.getElementById(`end_date_${qId}`);
                const errorDiv = document.getElementById(`error_msg_${qId}`);

                startInput.classList.remove('border-rose-500', 'focus:border-rose-500', 'bg-rose-50/50');
                endInput.classList.remove('border-rose-500', 'focus:border-rose-500', 'bg-rose-50/50');
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('hidden');
                }
            });

            // 1. Dynamic min / max constraints between consecutive quarters
            for (let i = 0; i < rows.length; i++) {
                const qId = rows[i].dataset.quarterId;
                const startInput = document.getElementById(`start_date_${qId}`);
                const endInput = document.getElementById(`end_date_${qId}`);

                // End date min >= Start date
                if (startInput.value) {
                    endInput.min = startInput.value;
                } else {
                    endInput.min = '';
                }

                // If previous quarter exists with end date, min start date is previous end date + 1 day
                if (i > 0) {
                    const prevId = rows[i - 1].dataset.quarterId;
                    const prevEndInput = document.getElementById(`end_date_${prevId}`);
                    if (prevEndInput.value) {
                        startInput.min = addDays(prevEndInput.value, 1);
                    } else {
                        startInput.min = '';
                    }
                } else {
                    startInput.min = '';
                }

                // If next quarter exists with start date, max end date is next start date - 1 day
                if (i < rows.length - 1) {
                    const nextId = rows[i + 1].dataset.quarterId;
                    const nextStartInput = document.getElementById(`start_date_${nextId}`);
                    if (nextStartInput.value) {
                        endInput.max = addDays(nextStartInput.value, -1);
                    } else {
                        endInput.max = '';
                    }
                } else {
                    endInput.max = '';
                }
            }

            // 2. Validate individual quarter integrity & detect overlaps
            const configuredRanges = [];

            for (let i = 0; i < rows.length; i++) {
                const qId = rows[i].dataset.quarterId;
                const qName = rows[i].dataset.quarterName;
                const startInput = document.getElementById(`start_date_${qId}`);
                const endInput = document.getElementById(`end_date_${qId}`);
                const errorDiv = document.getElementById(`error_msg_${qId}`);
                const sVal = startInput.value;
                const eVal = endInput.value;

                let rowError = '';

                if (sVal && !eVal) {
                    rowError = `Please select an end date for ${qName}.`;
                } else if (!sVal && eVal) {
                    rowError = `Please select a start date for ${qName}.`;
                } else if (sVal && eVal) {
                    if (sVal > eVal) {
                        rowError = `Start date cannot be after end date.`;
                    } else {
                        // Check against previously collected ranges
                        for (let prev of configuredRanges) {
                            if (sVal <= prev.end && eVal >= prev.start) {
                                rowError = `Overlaps with ${prev.name} (${prev.start} to ${prev.end}). Dates must not overlap.`;
                                break;
                            }
                            if (sVal <= prev.end) {
                                rowError = `Must start after ${prev.name} ends (${prev.end}).`;
                                break;
                            }
                        }

                        if (!rowError) {
                            configuredRanges.push({ id: qId, name: qName, start: sVal, end: eVal });
                        }
                    }
                }

                if (rowError) {
                    hasGlobalError = true;
                    if (!firstErrorMessage) firstErrorMessage = `${qName}: ${rowError}`;
                    startInput.classList.add('border-rose-500', 'bg-rose-50/50');
                    endInput.classList.add('border-rose-500', 'bg-rose-50/50');
                    if (errorDiv) {
                        errorDiv.textContent = rowError;
                        errorDiv.classList.remove('hidden');
                    }
                }
            }

            // 3. Dynamically update live Status badges on every row
            rows.forEach(row => {
                const qId = row.dataset.quarterId;
                const startInput = document.getElementById(`start_date_${qId}`);
                const endInput = document.getElementById(`end_date_${qId}`);
                const statusCell = document.getElementById(`status_cell_${qId}`);
                const activeIndicator = document.getElementById(`active_indicator_${qId}`);
                const nameLabel = row.querySelector('.quarter-name-label');
                const sVal = startInput.value;
                const eVal = endInput.value;
                const hasError = startInput.classList.contains('border-rose-500');

                let badgeHtml = '';
                let isActiveNow = false;

                if (hasError) {
                    badgeHtml = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                            <i class="fa fa-triangle-exclamation text-[10px]"></i>
                            Date Issue
                        </span>`;
                } else if (!sVal || !eVal) {
                    badgeHtml = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-gray-50 text-gray-400 border border-gray-200">
                            <i class="fa fa-minus-circle text-[10px]"></i>
                            Not Set
                        </span>`;
                } else if (todayStr >= sVal && todayStr <= eVal) {
                    isActiveNow = true;
                    badgeHtml = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active Now
                        </span>`;
                } else if (todayStr > eVal) {
                    badgeHtml = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                            <i class="fa fa-check-circle text-[10px]"></i>
                            Completed
                        </span>`;
                } else {
                    badgeHtml = `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-600 border border-amber-200">
                            <i class="fa fa-clock text-[10px]"></i>
                            Upcoming
                        </span>`;
                }

                if (statusCell) {
                    statusCell.innerHTML = `<span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</span>` + badgeHtml;
                }

                if (isActiveNow) {
                    row.classList.add('bg-[#3B82F6]/5');
                    if (activeIndicator) activeIndicator.classList.remove('hidden');
                    if (nameLabel) {
                        nameLabel.classList.add('text-[#3B82F6]');
                        nameLabel.classList.remove('text-[#1E2657]');
                    }
                } else {
                    row.classList.remove('bg-[#3B82F6]/5');
                    if (activeIndicator) activeIndicator.classList.add('hidden');
                    if (nameLabel) {
                        nameLabel.classList.remove('text-[#3B82F6]');
                        nameLabel.classList.add('text-[#1E2657]');
                    }
                }
            });

            // Update footer validation summary
            if (hasGlobalError) {
                if (validationSummary) {
                    validationText.textContent = firstErrorMessage;
                    validationSummary.classList.remove('hidden');
                }
            } else {
                if (validationSummary) {
                    validationSummary.classList.add('hidden');
                    validationText.textContent = '';
                }
            }

            return !hasGlobalError;
        }

        // Run validation and sync on initial page load
        validateAndSyncCalendar();

        // Listen for input changes across all quarter date pickers
        document.querySelectorAll('.quarter-date-input').forEach(input => {
            input.addEventListener('change', validateAndSyncCalendar);
            input.addEventListener('input', validateAndSyncCalendar);
        });

        // Intercept form submission if date conflicts exist
        if (form) {
            form.addEventListener('submit', function(e) {
                const isValid = validateAndSyncCalendar();
                if (!isValid) {
                    e.preventDefault();
                    alert('Cannot save Academic Calendar: Please resolve overlapping or invalid quarter dates before saving.');
                    const firstErrorInput = document.querySelector('.quarter-date-input.border-rose-500');
                    if (firstErrorInput) firstErrorInput.focus();
                    return false;
                }
            });
        }
    });
</script>
@endpush

@endsection