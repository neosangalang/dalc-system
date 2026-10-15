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
        <p class="text-[13px] font-medium text-[#6B7086] m-0 sm:ml-14">Set the start and end dates for each quarter. The active status is <strong>automatically determined</strong> <strong>based on today's date, and quarter dates <strong> </strong>.</p>
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
                        <tr class="block md:table-row relative md:static p-4 md:p-0 hover:bg-slate-50 transition-colors duration-150
                                {{ $status === 'active' ? 'bg-[#3B82F6]/5' : '' }}">

                            <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-4 md:mb-0">
                                <span class="text-lg font-extrabold {{ $status === 'active' ? 'text-[#3B82F6]' : 'text-[#1E2657]' }}">
                                    {{ $quarter->name }}
                                </span>
                                @if($status === 'active')
                                <span class="ml-2 px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#3B82F6]/10 text-[#3B82F6] uppercase tracking-widest border border-[#3B82F6]/20 inline-block">Active Now</span>
                                @endif
                            </td>

                            <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Start Date</span>
                                <input type="date"
                                    id="start_date_{{ $quarter->id }}"
                                    name="quarters[{{ $quarter->id }}][start_date]"
                                    value="{{ old('quarters.'.$quarter->id.'.start_date', $quarter->start_date_formatted) }}"
                                    class="start-date-input quarter-date-input w-full max-w-full md:max-w-[200px] rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                    data-quarter-id="{{ $quarter->id }}"
                                    data-quarter-index="{{ $index }}">
                            </td>

                            <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">End Date</span>
                                <input type="date"
                                    id="end_date_{{ $quarter->id }}"
                                    name="quarters[{{ $quarter->id }}][end_date]"
                                    value="{{ old('quarters.'.$quarter->id.'.end_date', $quarter->end_date_formatted) }}"
                                    class="end-date-input quarter-date-input w-full max-w-full md:max-w-[200px] rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors"
                                    data-quarter-id="{{ $quarter->id }}"
                                    data-quarter-index="{{ $index }}">
                            </td>

                            <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 text-left md:text-center absolute md:static top-4 right-4">
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
            <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex justify-end">
                <button type="submit" class="w-full md:w-auto inline-flex justify-center md:inline-flex items-center px-6 py-3 md:py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 hover:-translate-y-0.5">
                    <i class="fa fa-save md:mr-2 mr-1.5"></i> Save Calendar Settings
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const startInputs = Array.from(document.querySelectorAll('.start-date-input'));
        const endInputs = Array.from(document.querySelectorAll('.end-date-input'));

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

        function syncDateConstraints() {
            startInputs.forEach((startInput, idx) => {
                const endInput = endInputs[idx];

                // 1. Enforce End Date min >= Start Date
                if (startInput.value) {
                    endInput.min = startInput.value;
                    if (endInput.value && endInput.value < startInput.value) {
                        endInput.value = startInput.value;
                    }
                } else {
                    endInput.min = '';
                }

                // 2. Enforce Next Quarter Start Date min > Current Quarter End Date (prevents quarter date overlap)
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

        // Run constraint sync on load
        syncDateConstraints();

        // Listen for date input changes immediately (input and change)
        document.querySelectorAll('.quarter-date-input').forEach(input => {
            input.addEventListener('change', syncDateConstraints);
            input.addEventListener('input', syncDateConstraints);
        });
    });
</script>
@endpush

@endsection