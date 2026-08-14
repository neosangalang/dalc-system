@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-[100dvh] py-6 sm:py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <!-- STATS GRID (Transforms to a compact 2x2 grid on mobile) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        
        <!-- Card 1: My Students -->
        <div class="p-4 sm:p-5 rounded-[16px] sm:rounded-2xl bg-white border border-slate-300 shadow-sm sm:shadow-md flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-500/10">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-lg sm:text-2xl shrink-0 bg-indigo-50 text-indigo-600">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 leading-none counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
                <p class="m-0 text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mt-1.5 sm:mt-0.5 leading-tight">My Students</p>
            </div>
        </div>

        <!-- Card 2: Logs This Week -->
        <div class="p-4 sm:p-5 rounded-[16px] sm:rounded-2xl bg-white border border-slate-300 shadow-sm sm:shadow-md flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/10">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-lg sm:text-2xl shrink-0 bg-emerald-50 text-emerald-500">
                <i class="fa fa-pencil"></i>
            </div>
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 leading-none counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
                <p class="m-0 text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mt-1.5 sm:mt-0.5 leading-tight">Logs This Week</p>
            </div>
        </div>

        <!-- Card 3: Active IEP Goals -->
        <div class="p-4 sm:p-5 rounded-[16px] sm:rounded-2xl bg-white border border-slate-300 shadow-sm sm:shadow-md flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-500/10">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-lg sm:text-2xl shrink-0 bg-purple-50 text-purple-600">
                <i class="fa fa-bullseye"></i>
            </div>
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 m-0 leading-none counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
                <p class="m-0 text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider mt-1.5 sm:mt-0.5 leading-tight">Active IEP Goals</p>
            </div>
        </div>

        <!-- Card 4: Reports Due -->
        @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
        <div class="p-4 sm:p-5 rounded-[16px] sm:rounded-2xl shadow-sm sm:shadow-md flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg hover:shadow-amber-500/10 {{ $hasReports ? 'bg-amber-50 border border-amber-400' : 'bg-white border border-slate-300' }}">
            <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center text-lg sm:text-2xl shrink-0 {{ $hasReports ? 'bg-amber-200/50 text-amber-600' : 'bg-slate-100 text-slate-400' }}">
                <i class="fa fa-file-invoice"></i>
            </div>
            <div>
                <h3 class="text-xl sm:text-2xl font-extrabold m-0 leading-none counter {{ $hasReports ? 'text-amber-600' : 'text-slate-800' }}" data-target="{{ $reportsDue ?? 0 }}">0</h3>
                <p class="m-0 text-[10px] sm:text-xs font-bold uppercase tracking-wider mt-1.5 sm:mt-0.5 leading-tight {{ $hasReports ? 'text-amber-600' : 'text-slate-500' }}">Reports Due</p>
            </div>
        </div>

    </div>

    <!-- ACADEMIC CALENDAR -->
    <div class="bg-transparent md:bg-white md:rounded-2xl md:shadow-md md:border border-slate-300 overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="px-4 sm:px-6 py-4 sm:py-5 border border-slate-200 md:border-none md:border-b flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white rounded-2xl md:rounded-none shadow-sm md:shadow-none mb-3 sm:mb-0 gap-3 sm:gap-0">
            <h6 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 flex items-center">
                <i class="fa fa-calendar-alt text-indigo-600 mr-2"></i> Academic Calendar Timeline
            </h6>
            @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
            @if($activeQ)
                <span class="inline-flex items-center px-3 sm:px-4 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-100 text-[11px] sm:text-xs font-extrabold rounded-full tracking-wide">
                    Current: {{ $activeQ->name }}
                </span>
            @endif
        </div>
        
        <!-- Swipeable Carousel on Mobile, Standard Flex Row on PC -->
        <div class="flex gap-3 sm:gap-4 overflow-x-auto snap-x snap-mandatory px-1 sm:px-6 pb-2 sm:pb-8 pt-1 sm:pt-6 [&::-webkit-scrollbar]:h-1.5 sm:[&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full">
            @forelse($quarters ?? [] as $quarter)
                @if($quarter->is_active)
                    <div class="snap-center shrink-0 w-[85vw] sm:w-auto sm:flex-1 min-w-[200px] p-5 rounded-[16px] sm:rounded-xl bg-gradient-to-br from-indigo-600 to-purple-500 text-white shadow-md sm:shadow-lg shadow-indigo-500/30 transform sm:scale-[1.02] transition-transform border border-indigo-400">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="text-base sm:text-lg font-extrabold m-0">{{ $quarter->name }}</h5>
                            <span class="bg-white text-indigo-600 text-[9px] sm:text-[10px] font-extrabold px-2.5 py-1 rounded-full tracking-widest">ACTIVE</span>
                        </div>
                        <p class="m-0 text-[11px] sm:text-xs font-bold text-white/80">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                        </p>
                    </div>
                @else
                    <div class="snap-center shrink-0 w-[80vw] sm:w-auto sm:flex-1 min-w-[200px] p-5 rounded-[16px] sm:rounded-xl border border-slate-300 bg-white sm:bg-slate-50 transition-colors hover:bg-slate-100 shadow-sm sm:shadow-none">
                        <h5 class="text-base sm:text-lg font-extrabold text-slate-800 m-0 mb-3">{{ $quarter->name }}</h5>
                        <p class="m-0 text-[11px] sm:text-xs font-bold text-slate-500">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                        </p>
                    </div>
                @endif
            @empty
                <div class="w-full text-center py-6 sm:py-4 text-[13px] sm:text-sm font-medium text-slate-500 bg-white sm:bg-transparent rounded-2xl sm:rounded-none border border-slate-200 sm:border-none shadow-sm sm:shadow-none">
                    No calendar data configured by the administrator yet.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 200;
                if (count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 15);
                } else {
                    counter.innerText = target;
                }
            };
            setTimeout(updateCount, 100);
        });
    });
</script>
@endpush