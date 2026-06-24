@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="p-5 rounded-2xl bg-white border border-slate-200 flex items-center gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-indigo-50 text-indigo-600">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 counter" data-target="{{ $myStudentsCount ?? ($myStudents ? $myStudents->count() : 0) }}">0</h3>
                <p class="m-0 text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">My Students</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 flex items-center gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-emerald-50 text-emerald-500">
                <i class="fa fa-pencil"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 counter" data-target="{{ $logsThisWeek ?? 0 }}">0</h3>
                <p class="m-0 text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Logs This Week</p>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 flex items-center gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/10">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl shrink-0 bg-purple-50 text-purple-600">
                <i class="fa fa-bullseye"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 m-0 counter" data-target="{{ $activeGoals ?? 0 }}">0</h3>
                <p class="m-0 text-xs font-bold text-slate-500 uppercase tracking-wider mt-0.5">Active IEP Goals</p>
            </div>
        </div>

        @php $hasReports = ($reportsDue ?? 0) > 0; @endphp
        <div class="p-5 rounded-2xl border flex items-center gap-4 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 {{ $hasReports ? 'bg-amber-50 border-amber-400' : 'bg-white border-slate-200' }}">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-2xl shrink-0 {{ $hasReports ? 'bg-amber-200/50 text-amber-600' : 'bg-slate-100 text-slate-400' }}">
                <i class="fa fa-file-invoice"></i>
            </div>
            <div>
                <h3 class="text-2xl font-extrabold m-0 counter {{ $hasReports ? 'text-amber-600' : 'text-slate-800' }}" data-target="{{ $reportsDue ?? 0 }}">0</h3>
                <p class="m-0 text-xs font-bold uppercase tracking-wider mt-0.5 {{ $hasReports ? 'text-amber-600' : 'text-slate-500' }}">Reports Due</p>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
            <h6 class="text-base font-extrabold text-slate-800 m-0 flex items-center">
                <i class="fa fa-calendar-alt text-indigo-600 mr-2"></i> Academic Calendar Timeline
            </h6>
            @php $activeQ = isset($quarters) ? $quarters->where('is_active', true)->first() : null; @endphp
            @if($activeQ)
                <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-extrabold rounded-full tracking-wide">
                    Current: {{ $activeQ->name }}
                </span>
            @endif
        </div>
        
        <div class="p-6 flex gap-4 overflow-x-auto pb-8 [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-track]:bg-slate-100 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-thumb]:bg-slate-300 [&::-webkit-scrollbar-thumb]:rounded-full">
            @forelse($quarters ?? [] as $quarter)
                @if($quarter->is_active)
                    <div class="flex-1 min-w-[200px] p-5 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-500 text-white shadow-lg shadow-indigo-500/30 transform scale-[1.02] transition-transform">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="text-lg font-extrabold m-0">{{ $quarter->name }}</h5>
                            <span class="bg-white text-indigo-600 text-[10px] font-extrabold px-2.5 py-1 rounded-full tracking-widest">ACTIVE</span>
                        </div>
                        <p class="m-0 text-xs font-bold text-white/80">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j, Y') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j, Y') : 'TBD' }}
                        </p>
                    </div>
                @else
                    <div class="flex-1 min-w-[200px] p-5 rounded-xl border border-slate-200 bg-slate-50 transition-colors hover:bg-slate-100">
                        <h5 class="text-lg font-extrabold text-slate-800 m-0 mb-3">{{ $quarter->name }}</h5>
                        <p class="m-0 text-xs font-bold text-slate-500">
                            {{ $quarter->start_date ? \Carbon\Carbon::parse($quarter->start_date)->format('M j') : 'TBD' }} - 
                            {{ $quarter->end_date ? \Carbon\Carbon::parse($quarter->end_date)->format('M j') : 'TBD' }}
                        </p>
                    </div>
                @endif
            @empty
                <div class="w-full text-center py-4 text-sm font-medium text-slate-500">
                    No calendar data configured by the administrator yet.
                </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
                    <h6 class="text-base font-extrabold text-slate-800 m-0 flex items-center">
                        <i class="fa fa-user-graduate text-emerald-500 mr-2"></i> My Students
                    </h6>
                    <a href="{{ route('teacher.students.index') }}" class="inline-flex items-center px-4 py-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-full hover:bg-slate-50 transition-colors shadow-sm">
                        View All
                    </a>
                </div>
                
                <div class="flex-1 overflow-y-auto">
                    @forelse($myStudents ?? [] as $student)
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between hover:bg-indigo-50/30 transition-all hover:translate-x-1 group last:border-0">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center font-extrabold text-white text-lg bg-gradient-to-br from-indigo-500 to-purple-500 shadow-sm shrink-0">
                                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="m-0 text-sm font-extrabold text-slate-800 group-hover:text-indigo-700 transition-colors">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                    <p class="m-0 text-[11px] font-bold text-slate-500 mt-0.5"><i class="fa fa-bullseye text-indigo-400 mr-1"></i> Assigned Student</p>
                                </div>
                            </div>
                            <a href="{{ route('teacher.daily-logs.index', ['student_id' => $student->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm shrink-0 whitespace-nowrap">
                                <i class="fa fa-pen mr-1.5"></i> Log
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-12 px-6">
                            <i class="fa fa-user-slash text-4xl text-slate-300 mb-4 block"></i>
                            <p class="text-sm font-medium text-slate-500 m-0">No students are currently assigned to your roster.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-full flex flex-col min-h-[400px]">
                <div class="px-6 py-5 border-b border-slate-100 bg-white shrink-0">
                    <h6 class="text-base font-extrabold text-slate-800 m-0 flex items-center">
                        <i class="fa fa-bullseye text-purple-500 mr-2"></i> IEP Goal Progress (Quick View)
                    </h6>
                </div>
                
                <div class="flex-1 p-6 flex items-center justify-center text-center">
                    <div class="w-full h-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center p-8">
                        <div class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-5">
                            <i class="fa fa-bullseye text-4xl"></i>
                        </div>
                        <h5 class="text-xl font-extrabold text-slate-800 mb-2 m-0">Track Your Students' Milestones</h5>
                        <p class="text-sm font-medium text-slate-500 mb-6 max-w-md">Assign IEP goals to your students to see their progress bars automatically populate here.</p>
                        <a href="{{ route('teacher.iep-goals.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                            Manage IEP Goals
                        </a>
                    </div>
                </div>
            </div>
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