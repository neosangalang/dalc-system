@extends('layouts.app')

@section('title', 'IEP & Goal Management')

@section('content')

<style>
    [x-cloak] { display: none !important; }
    /* Custom scrollbar for the text areas */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9CA0AF; }
</style>

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
<div class="w-full max-w-[1200px] mx-auto space-y-6 md:space-y-8" 
     x-data="{ 
        showAddModal: false, 
        activeAccordion: null, 
        activeEditModal: null 
     }">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex justify-between items-center animate-fade-in-down mb-6">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-extrabold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <!-- HEADER & SEARCH -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 w-full mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] m-0 flex items-center tracking-tight">
                <div class="w-10 h-10 rounded-full bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 flex items-center justify-center text-[#8B5CF6] mr-3 shadow-sm shrink-0">
                    <i class="fa fa-bullseye text-lg"></i>
                </div>
                IEP & Goal Management
            </h1>
            <p class="text-[13px] font-medium text-[#6B7086] mt-1 m-0 sm:ml-14 leading-tight">Manage individualized learning objectives for your students.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64 shadow-sm rounded-xl overflow-hidden border border-black/15 bg-white focus-within:border-[#3B82F6] focus-within:ring-1 focus-within:ring-[#3B82F6] transition-all">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#9CA0AF] pointer-events-none">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" id="studentSearchInput" class="w-full border-0 py-2.5 pl-10 pr-3 text-[13px] sm:text-sm font-bold text-[#1E2657] focus:ring-0 placeholder:text-[#9CA0AF] placeholder:font-medium bg-transparent" placeholder="Search students...">
            </div>

            <button @click="showAddModal = true" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-all shadow-sm focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-1 whitespace-nowrap">
                <i class="fa fa-plus mr-2"></i> Add Goal
            </button>
        </div>
    </div>

    <!-- ACCORDION SECTION -->
    <div class="space-y-4" id="iepAccordion">
        @forelse($myStudents as $student)
            @php
                $studentGoals = $goals->where('student_id', $student->id);
                $activeCount = $studentGoals->where('status', 'in_progress')->count();
            @endphp
            
            <div class="bg-white rounded-2xl border border-black/15 shadow-sm overflow-hidden transition-all duration-200 student-accordion-item" data-student-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
                
                <button @click="activeAccordion = activeAccordion === {{ $student->id }} ? null : {{ $student->id }}" 
                        class="w-full px-4 py-4 sm:px-6 sm:py-5 flex flex-wrap sm:flex-nowrap items-center justify-between text-left transition-colors focus:outline-none"
                        :class="activeAccordion === {{ $student->id }} ? 'bg-slate-50/50 border-b border-black/10' : 'hover:bg-slate-50'">
                    
                    <div class="flex items-center gap-3 sm:gap-4 mb-3 sm:mb-0 w-full sm:w-auto">
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center text-white font-extrabold text-sm sm:text-base shadow-sm bg-gradient-to-br from-[#1E2657] to-[#12163A] shrink-0 border border-black/10">
                            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="text-[15px] sm:text-[16px] font-extrabold text-[#1E2657] m-0">{{ $student->first_name }} {{ $student->last_name }}</h6>
                            <p class="text-[10px] sm:text-[11px] font-bold text-[#9CA0AF] m-0 mt-0.5"><i class="fa fa-id-badge text-[#3B82F6] mr-1"></i> Assigned Student</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-start sm:justify-end">
                        <span class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-md border border-black/10 bg-[#F4F6FA] text-[#6B7086] text-[9px] font-extrabold uppercase tracking-widest shadow-sm">{{ $studentGoals->count() }} Goals</span>
                        @if($activeCount > 0)
                            <span class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-md bg-[#F59E0B]/10 text-[#F59E0B] border border-[#F59E0B]/20 text-[9px] font-extrabold uppercase tracking-widest shadow-sm"><i class="fa fa-spinner fa-spin mr-1"></i> {{ $activeCount }} Active</span>
                        @endif
                        <i class="fa fa-chevron-down text-[#9CA0AF] transition-transform ml-auto sm:ml-2" :class="activeAccordion === {{ $student->id }} ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                
                <div x-show="activeAccordion === {{ $student->id }}" x-collapse x-cloak>
                    <div class="p-4 sm:p-0 bg-white border-t border-black/10">
                        
                        @if($studentGoals->count() > 0)
                            <div class="w-full overflow-x-auto">
                                <table class="w-full text-left border-collapse block sm:table whitespace-normal sm:whitespace-nowrap">
                                    <thead class="hidden sm:table-header-group bg-[#F4F6FA] border-b border-black/10">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest w-1/5">Domain</th>
                                            <th scope="col" class="px-6 py-4 text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest w-2/5">Goal Objective</th>
                                            <th scope="col" class="px-6 py-4 text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest w-1/5">Progress</th>
                                            <th scope="col" class="px-6 py-4 text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest">Status</th>
                                            <th scope="col" class="px-6 py-4 text-right text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="block sm:table-row-group space-y-4 sm:space-y-0 divide-y-0 sm:divide-y divide-black/5 bg-transparent sm:bg-white">
                                        @foreach($studentGoals as $goal)
                                            <tr class="block sm:table-row bg-white sm:bg-transparent rounded-[16px] sm:rounded-none border border-black/15 sm:border-none shadow-sm sm:shadow-none hover:bg-slate-50 transition-colors">
                                                
                                                <td class="block sm:table-cell px-4 py-3 sm:px-6 sm:py-5 border-b border-black/5 sm:border-none align-top">
                                                    <span class="sm:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Domain</span>
                                                    @php
                                                        $badgeClass = match($goal->domain) {
                                                            'Language-Communication' => 'bg-[#3B82F6]/10 text-[#3B82F6] border-[#3B82F6]/20',
                                                            'Self-Help'              => 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/20',
                                                            'Perceptuo-Cognitive'    => 'bg-[#8B5CF6]/10 text-[#8B5CF6] border-[#8B5CF6]/20',
                                                            'Socio-Emotional'        => 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/20',
                                                            'Psychomotor'            => 'bg-rose-50 text-rose-600 border-rose-200',
                                                            default                  => 'bg-[#F4F6FA] text-[#6B7086] border-black/10',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-[9px] font-extrabold uppercase tracking-widest border shadow-sm {{ $badgeClass }}">
                                                        {{ str_replace('-', ' - ', $goal->domain) }}
                                                    </span>
                                                </td>
                                                
                                                <td class="block sm:table-cell px-4 py-3 sm:px-6 sm:py-5 border-b border-black/5 sm:border-none align-top">
                                                    <span class="sm:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-2">Goal Description</span>
                                                    <div class="text-[12px] font-medium text-[#6B7086] whitespace-pre-line max-h-[180px] sm:max-h-[140px] overflow-y-auto pr-2 bg-[#F4F6FA] sm:bg-transparent p-3 sm:p-0 rounded-xl sm:rounded-none border border-black/5 sm:border-none leading-relaxed custom-scrollbar" title="Goal Description">
                                                        {{ $goal->goal_description }}
                                                    </div>
                                                </td>
                                                
                                                <td class="block sm:table-cell px-4 py-3 sm:px-6 sm:py-5 border-b border-black/5 sm:border-none align-top pt-5 sm:pt-4">
                                                    <span class="sm:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Progress</span>
                                                    <div class="min-w-[120px]">
                                                        <div class="w-full h-1.5 bg-[#E8E5DC] rounded-full mb-2 overflow-hidden shadow-inner">
                                                            <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $goal->progress_percentage == 100 ? 'bg-[#10B981]' : 'bg-[#3B82F6]' }}" style="width: {{ $goal->progress_percentage }}%;"></div>
                                                        </div>
                                                        <p class="text-[9px] font-extrabold m-0 tracking-widest uppercase {{ $goal->progress_percentage == 100 ? 'text-[#10B981]' : 'text-[#3B82F6]' }}">
                                                            {{ $goal->progress_percentage == 100 ? 'ACHIEVED ✓' : $goal->progress_percentage . '%' }}
                                                        </p>
                                                    </div>
                                                </td>
                                                
                                                <td class="block sm:table-cell px-4 py-3 sm:px-6 sm:py-5 border-b border-black/5 sm:border-none align-top pt-5 sm:pt-4">
                                                    <span class="sm:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</span>
                                                    @if($goal->status === 'in_progress')
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#F59E0B]/10 text-[#F59E0B] uppercase tracking-widest border border-[#F59E0B]/20">In Progress</span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#10B981]/10 text-[#10B981] uppercase tracking-widest border border-[#10B981]/20">Achieved</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="block sm:table-cell px-4 py-3 sm:px-6 sm:py-5 bg-[#F4F6FA] sm:bg-transparent rounded-b-2xl sm:rounded-none align-top pt-5 sm:pt-4">
                                                    <div class="flex justify-end gap-2 w-full sm:w-auto">
                                                        <button @click="activeEditModal = {{ $goal->id }}" class="flex-1 sm:flex-none inline-flex justify-center items-center px-3 py-2 bg-white border border-black/15 text-[#6B7086] text-[11px] font-extrabold rounded-xl hover:bg-slate-50 hover:text-[#3B82F6] hover:border-[#3B82F6]/50 transition-colors shadow-sm">
                                                            <i class="fa fa-edit mr-1.5"></i> Edit / Track
                                                        </button>
                                                        <form action="{{ route('teacher.iep-goals.destroy', $goal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this goal? This cannot be undone.');" class="m-0 sm:flex-none flex shrink-0">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center justify-center px-4 sm:px-0 sm:w-8 py-2 sm:py-1.5 bg-white border border-rose-200 text-rose-500 rounded-xl hover:bg-rose-50 transition-colors shadow-sm text-[12px]">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-10 sm:py-12 px-6 bg-white rounded-b-2xl">
                                <div class="w-14 h-14 rounded-full bg-[#F4F6FA] border border-black/5 shadow-sm flex items-center justify-center mx-auto mb-4">
                                    <i class="fa fa-clipboard-list text-xl text-[#9CA0AF]"></i>
                                </div>
                                <h6 class="text-[14px] font-extrabold text-[#1E2657] mb-1 m-0">No Active Goals</h6>
                                <p class="text-[12px] font-medium text-[#6B7086] mb-5 m-0">There are currently no IEP goals assigned to {{ $student->first_name }}.</p>
                                <button type="button" @click="activeAccordion = null; showAddModal = true; setTimeout(() => { $refs.studentSelect.value = '{{ $student->id }}'; }, 100);" class="inline-flex items-center px-4 py-2 bg-white border border-black/15 text-[#6B7086] hover:text-[#3B82F6] hover:border-[#3B82F6]/30 text-[12px] font-extrabold rounded-xl transition-colors shadow-sm">
                                    <i class="fa fa-plus mr-1.5"></i> Create First Goal
                                </button>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 sm:py-16 px-6 bg-white rounded-2xl shadow-sm border border-black/15">
                <i class="fa fa-users-slash text-4xl sm:text-5xl text-[#E8E5DC] mb-4 block"></i>
                <h5 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No Students Found</h5>
                <p class="text-[13px] font-medium text-[#6B7086] m-0">You have no students assigned to your roster yet.</p>
            </div>
        @endforelse
    </div>

    <div id="no-search-results" class="text-center py-12 sm:py-16 px-6 bg-white rounded-2xl shadow-sm border border-black/15 mt-4" style="display: none;">
        <i class="fa fa-search text-4xl sm:text-5xl text-[#E8E5DC] mb-4 block"></i>
        <h5 class="text-[15px] font-extrabold text-[#1E2657] m-0 mb-1">No matches found</h5>
        <p class="text-[13px] font-medium text-[#6B7086] m-0">We couldn't find any students matching that name.</p>
    </div>

    <!-- ========================================== -->
    <!-- EDIT/TRACK PROGRESS MODALS -->
    <!-- ========================================== -->
    @foreach($myStudents as $student)
        @foreach($goals->where('student_id', $student->id) as $goal)
            <div x-show="activeEditModal === {{ $goal->id }}" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="activeEditModal === {{ $goal->id }}" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="activeEditModal = null"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div x-show="activeEditModal === {{ $goal->id }}" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-black/15 relative z-10">
                        
                        <div class="flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh]">
                            
                            <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center shrink-0">
                                <h5 class="text-base font-extrabold text-[#1E2657] m-0 leading-tight">Track Progress: {{ $student->first_name }}</h5>
                                <button type="button" @click="activeEditModal = null" class="text-[#9CA0AF] hover:text-[#1E2657] ml-4"><i class="fa fa-times text-xl"></i></button>
                            </div>
                            
                            <div class="p-5 sm:p-6 flex-1 overflow-y-auto custom-scrollbar bg-white">
                                
                                <form action="{{ route('teacher.iep-goals.update', $goal->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-6 interactive-checklist-container">
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest m-0">Interactive Activity Tracker</label>
                                            <button type="button" class="text-[#6B7086] hover:text-[#3B82F6] text-[11px] font-extrabold transition-colors toggle-raw-text-btn flex items-center bg-white px-3 py-1.5 rounded-lg border border-black/15 shadow-sm">
                                                <i class="fa fa-pen mr-1.5"></i> Edit Text
                                            </button>
                                        </div>
                                        <div class="visual-checklist p-4 border border-black/15 rounded-2xl bg-white shadow-sm max-h-[400px] overflow-y-auto text-[13px] text-[#6B7086] custom-scrollbar"></div>
                                        <textarea name="goal_description" class="w-full rounded-2xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-[#F4F6FA] goal-raw-text hidden mt-3 custom-scrollbar" rows="10" required>{{ $goal->goal_description }}</textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 bg-[#F4F6FA] p-5 rounded-2xl border border-black/10">
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Calculated Progress (%)</label>
                                            <input type="number" name="progress_percentage" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm goal-progress-input bg-white" min="0" max="100" value="{{ $goal->progress_percentage }}" readonly required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Status</label>
                                            <select name="status" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm goal-status-select bg-white" required>
                                                <option value="in_progress" {{ $goal->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="achieved" {{ $goal->status == 'achieved' ? 'selected' : '' }}>Achieved</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0 pt-2 mb-2">
                                        <button type="button" @click="activeEditModal = null" class="w-full sm:w-auto px-4 py-2.5 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center justify-center"><i class="fa fa-save mr-1.5"></i> Save Progress</button>
                                    </div>
                                </form>
                                
                                <div class="mt-6 border-t border-dashed border-black/15 pt-6">
                                    @include('components.comment-thread', ['item' => $goal])
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    <!-- ========================================== -->
    <!-- CREATE IEP GOAL MODAL -->
    <!-- ========================================== -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] overflow-y-auto text-left" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="showAddModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showAddModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-black/15 relative z-10">
                
                <form action="{{ route('teacher.iep-goals.store') }}" method="POST" class="m-0 flex flex-col h-[90vh] sm:h-auto sm:max-h-[85vh]">
                    @csrf
                    
                    <div class="px-5 sm:px-6 py-4 border-b border-black/10 bg-[#F4F6FA] flex justify-between items-center shrink-0">
                        <h5 class="text-base font-extrabold text-[#1E2657] m-0"><i class="fa fa-plus-circle text-[#3B82F6] mr-2"></i> Assign New IEP Goal</h5>
                        <button type="button" @click="showAddModal = false" class="text-[#9CA0AF] hover:text-[#1E2657]"><i class="fa fa-times text-xl"></i></button>
                    </div>
                    
                    <div class="p-5 sm:p-6 flex-1 overflow-y-auto custom-scrollbar bg-white">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-6">
                            <div>
                                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Select Student</label>
                                <select x-ref="studentSelect" name="student_id" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm" required>
                                    <option value="" disabled selected>Choose a student...</option>
                                    @foreach($myStudents as $student)
                                        <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Development Domain</label>
                                <select id="domain-select" name="domain" class="w-full rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-extrabold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm" required>
                                    <option value="" disabled selected>Select Domain...</option>
                                    <option value="Perceptuo-Cognitive">Perceptuo - Cognitive</option>
                                    <option value="Socio-Emotional">Socio - Emotional</option>
                                    <option value="Psychomotor">Psychomotor</option>
                                    <option value="Language-Communication">Language - Communication</option>
                                    <option value="Self-Help">Self - Help</option>
                                </select>
                            </div>
                        </div>

                        <!-- AI BUILDER SECTION -->
                        <div class="p-5 mb-6 bg-[#F4F6FA] border border-dashed border-[#3B82F6]/30 rounded-2xl relative overflow-hidden shadow-inner">
                            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none hidden sm:block">
                                <i class="fa-solid fa-robot text-8xl text-[#1E2657]"></i>
                            </div>
                            <div class="relative z-10">
                                <h6 class="text-[13px] sm:text-[14px] font-extrabold text-[#8B5CF6] mb-1 flex items-center m-0">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> AI IEP Builder
                                </h6>
                                <p class="text-[12px] font-medium text-[#9CA0AF] mb-5 m-0">Input the student's current status to generate Objectives and Activities.</p>
                                
                                <label class="block text-[10px] font-extrabold text-[#6B7086] uppercase tracking-widest mb-1.5">Present Level of Performance</label>
                                <textarea name="plop" id="ai-present-level" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm mb-4 custom-scrollbar" rows="4" required placeholder="e.g., Can sit for 5-10 minutes but fidgety..."></textarea>
                                
                                <button type="button" id="generate-goals-btn" class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#8B5CF6] text-white text-[13px] font-extrabold rounded-xl hover:bg-purple-600 transition-colors shadow-sm">
                                    <i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities
                                </button>

                                <div id="ai-results-container" class="mt-5 hidden">
                                    <hr class="border-black/10 my-4">
                                    <p class="text-[10px] font-extrabold text-[#10B981] uppercase tracking-widest mb-3 m-0"><i class="fa-solid fa-check mr-1"></i> Suggested IEP Strategies:</p>
                                    
                                    <div class="w-full border border-black/15 rounded-2xl overflow-hidden shadow-sm">
                                        <table class="w-full text-left bg-transparent sm:bg-white block sm:table">
                                            <thead class="hidden sm:table-header-group bg-white border-b border-black/10">
                                                <tr>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[40%]">Objective</th>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[45%]">Activities</th>
                                                    <th class="px-5 py-3 text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest w-[15%] text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="goals-list-tbody" class="block sm:table-row-group divide-y-0 sm:divide-y divide-black/5 space-y-3 sm:space-y-0 p-2 sm:p-0 bg-[#F4F6FA] sm:bg-transparent"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold text-[#1E2657] uppercase tracking-widest mb-1.5">Final Goal Description</label>
                            <textarea id="final-goal-input" name="goal_description" class="w-full rounded-xl border-black/15 px-4 py-3 text-[13px] font-medium text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm custom-scrollbar" rows="10" required placeholder="The selected Objective and Activities will appear here..."></textarea>
                        </div>
                    </div>
                    
                    <div class="px-5 sm:px-6 py-4 bg-[#F4F6FA] border-t border-black/10 flex flex-col-reverse sm:flex-row justify-end gap-3 shrink-0">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-white border border-black/15 rounded-xl text-[13px] font-extrabold text-[#6B7086] hover:bg-slate-50 shadow-sm">Cancel</button>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#3B82F6] text-white text-[13px] font-extrabold rounded-xl hover:bg-blue-600 transition-colors shadow-sm flex items-center justify-center"><i class="fa fa-save mr-1.5"></i> Save IEP Goal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ==========================================
// 0. SEARCH FILTER LOGIC
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

            if (visibleCount === 0 && searchTerm !== '') {
                noResultsMsg.style.display = 'block';
            } else {
                noResultsMsg.style.display = 'none';
            }
        });
    }
});

// ==========================================
// 1. PLOP DOMAIN MEMORY LOGIC 
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    let domainSelect = document.getElementById('domain-select');
    let plopInput = document.getElementById('ai-present-level');
    
    let savedMemory = localStorage.getItem('iep_plop_memory');
    let plopMemory = savedMemory ? JSON.parse(savedMemory) : {};
    let previousDomain = domainSelect.value;

    if (previousDomain && plopMemory[previousDomain]) plopInput.value = plopMemory[previousDomain];

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
        plopInput.value = plopMemory[newDomain] ? plopMemory[newDomain] : ''; 
        previousDomain = newDomain;
    });

    let form = plopInput.closest('form');
    if (form) form.addEventListener('submit', () => localStorage.removeItem('iep_plop_memory'));
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

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Analyzing...';
    btn.disabled = true;

    fetch("{{ route('teacher.iep-goals.generate-ai') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ domain: domainValue, present_level: presentLevelText })
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = '<i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities';
        btn.disabled = false;

        if(data.success) {
            tbody.innerHTML = '';
            resultsContainer.classList.remove('hidden');
            data.goals.forEach((item) => {
                let activityList = item.activities.map(a => `<li class="mb-1.5">${a}</li>`).join('');
                let rawActivities = item.activities.map(a => `[ ] ${a}`).join('\n');
                 
                let tr = document.createElement('tr');
                tr.className = "block sm:table-row bg-white border border-black/10 sm:border-none rounded-2xl sm:rounded-none shadow-sm sm:shadow-none hover:bg-slate-50 transition-colors";
                tr.innerHTML = `
                    <td class="block sm:table-cell font-extrabold text-[#3B82F6] align-top py-4 px-5 border-b border-black/5 sm:border-none text-[13px]">
                        <span class="sm:hidden text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-wider block mb-1">Objective</span>
                        ${item.objective}
                    </td>
                    <td class="block sm:table-cell align-top py-4 px-5 border-b border-black/5 sm:border-none">
                        <span class="sm:hidden text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-wider block mb-1.5">Suggested Activities</span>
                        <ul class="mb-0 pl-4 list-disc text-[#6B7086] text-[12px] font-medium leading-relaxed">${activityList}</ul>
                    </td>
                    <td class="block sm:table-cell text-center align-middle py-4 px-5 bg-[#F4F6FA] sm:bg-transparent rounded-b-2xl sm:rounded-none">
                        <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 border border-[#10B981]/50 text-[#10B981] text-[12px] font-extrabold rounded-xl hover:bg-[#10B981]/10 transition-colors use-goal-btn shadow-sm">
                            <i class="fa fa-arrow-down mr-1.5"></i> Add to Goal
                        </button>
                    </td>
                `;

                let useBtn = tr.querySelector('.use-goal-btn');
                useBtn.onclick = function() {
                    let formattedText = `OBJECTIVE:\n${item.objective}\n\nACTIVITIES:\n${rawActivities}`;
                    let finalInput = document.getElementById('final-goal-input');
                    
                    if (finalInput.value.trim() === '') { finalInput.value = formattedText; } 
                    else { finalInput.value += `\n\n------------------------\n\n${formattedText}`; }
                    
                    this.innerHTML = '<i class="fa fa-check mr-1"></i> Added';
                    this.className = "w-full inline-flex items-center justify-center px-4 py-2 bg-[#10B981] text-white text-[12px] font-extrabold rounded-xl transition-colors use-goal-btn shadow-sm";
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fa fa-arrow-down mr-1"></i> Add Another';
                        this.className = "w-full inline-flex items-center justify-center px-4 py-2 border border-[#10B981]/50 text-[#10B981] text-[12px] font-extrabold rounded-xl hover:bg-[#10B981]/10 transition-colors use-goal-btn shadow-sm";
                    }, 1500);
                };

                tbody.appendChild(tr);
            });
        } else {
            alert("AI Error: " + data.message);
        }
    }).catch(error => {
        btn.innerHTML = '<i class="fa-solid fa-robot mr-2"></i> Generate Objectives & Activities';
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
        let form = container.closest('form');
        let progressInput = form.querySelector('.goal-progress-input');
        let statusSelect = form.querySelector('.goal-status-select');
        let toggleBtn = container.querySelector('.toggle-raw-text-btn');

        toggleBtn.addEventListener('click', () => {
            textarea.classList.toggle('hidden');
            visualList.classList.toggle('hidden');
        });

        textarea.addEventListener('input', renderChecklist);
        function renderChecklist() {
            let lines = textarea.value.split('\n');
            let html = '';
            let totalBoxes = 0;
            let checkedBoxes = 0;
            
            lines.forEach((line, index) => {
                if (line.trim().startsWith('- ')) { line = line.replace('- ', '[ ] '); }

                if (line.trim().match(/^\[[\s]*\]/)) {
                    totalBoxes++;
                    let text = line.trim().replace(/^\[[\s]*\]/, '').trim();
                    html += `
                        <div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <input class="dynamic-checkbox mt-0.5 rounded border-black/20 text-[#3B82F6] focus:ring-[#3B82F6] cursor-pointer w-4 h-4 shrink-0 shadow-sm" type="checkbox" data-line="${index}">
                            <label class="text-[13px] font-medium text-[#1E2657] cursor-pointer leading-tight pt-[1px]">${text}</label>
                        </div>`;
                } 
                else if (line.trim().match(/^\[x\]/i)) {
                    totalBoxes++;
                    checkedBoxes++;
                    let text = line.trim().replace(/^\[x\]/i, '').trim();
                    html += `
                        <div class="flex items-start gap-3 mb-2 px-3 py-2.5 rounded-xl bg-[#F4F6FA] transition-colors border border-black/5">
                            <input class="dynamic-checkbox mt-0.5 rounded border-[#10B981] bg-[#10B981] text-[#10B981] focus:ring-[#10B981] cursor-pointer w-4 h-4 shrink-0 shadow-sm" type="checkbox" data-line="${index}" checked>
                            <label class="text-[13px] font-medium text-[#9CA0AF] line-through cursor-pointer leading-tight pt-[1px]">${text}</label>
                        </div>`;
                } 
                else if (line.trim() !== '' && !line.includes('---')) {
                    html += `<div class="mt-4 mb-2 px-1 text-[11px] font-extrabold text-[#3B82F6] uppercase tracking-widest">${line}</div>`;
                } 
                else if (line.includes('---')) {
                    html += `<hr class="border-black/10 my-4">`;
                }
            });
            
            if(totalBoxes === 0 && textarea.value.trim() !== '') {
                html += `<div class="text-[#9CA0AF] text-[13px] italic px-1 mt-2">No trackable activities found. Click 'Edit Text' to see the raw text or add manual brackets [ ].</div>`;
            }

            visualList.innerHTML = html;
            
            if (totalBoxes > 0) {
                let percent = Math.round((checkedBoxes / totalBoxes) * 100);
                
                // BULLETPROOF FIX: Use standard readonly so the browser always submits the value
                progressInput.readOnly = true;
                progressInput.value = percent;
                progressInput.classList.add('bg-slate-100', 'text-slate-500', 'pointer-events-none'); 
                
                if (percent === 100) { statusSelect.value = 'achieved'; } 
                else { statusSelect.value = 'in_progress'; }
            } else {
                progressInput.readOnly = false;
                progressInput.classList.remove('bg-slate-100', 'text-slate-500', 'pointer-events-none');
            }

            visualList.querySelectorAll('.dynamic-checkbox').forEach(chk => {
                chk.addEventListener('change', function() {
                    let lineIdx = this.getAttribute('data-line');
                    let currentLines = textarea.value.split('\n');
                    
                    if (currentLines[lineIdx].match(/^-\s+/)) { currentLines[lineIdx] = currentLines[lineIdx].replace(/^-\s+/, '[ ] '); }
                    
                    if (this.checked) { 
                        currentLines[lineIdx] = currentLines[lineIdx].replace(/\[[\s]*\]/, '[x]'); 
                    } else { 
                        currentLines[lineIdx] = currentLines[lineIdx].replace(/\[x\]/i, '[ ]'); 
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
@endpush