@extends('layouts.app')

@section('title', 'Manage Academic Calendar')

@section('content')

<!-- Main Container (Background stripped to let app.blade.php gradient flow) -->
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

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#1E2657] tracking-tight mb-2 flex items-center">
            <div class="w-10 h-10 rounded-full bg-[#3B82F6]/10 flex items-center justify-center text-[#3B82F6] mr-3 shadow-sm border border-[#3B82F6]/20 shrink-0">
                <i class="fa fa-calendar-days text-lg"></i>
            </div>
            Academic Calendar
        </h1>
        <p class="text-[13px] font-medium text-[#6B7086] m-0 sm:ml-14">Set the start and end dates for each quarter. These dates will automatically appear on the Teacher Dashboard.</p>
    </div>

    <!-- Calendar Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-black/15 overflow-hidden">
        <form action="{{ route('admin.quarters.update') }}" method="POST" class="m-0">
            @csrf

            <!-- Mobile hides horizontal scrolling, PC restores it -->
            <div class="overflow-x-hidden md:overflow-x-auto w-full">
                <!-- Mobile transforms to block, PC restores to table -->
                <table class="w-full text-left border-collapse block md:table whitespace-normal md:whitespace-nowrap">
                    
                    <!-- Hidden on Mobile, Restored on PC -->
                    <thead class="hidden md:table-header-group bg-[#F4F6FA] border-b border-black/10">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[15%]">Quarter</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">Start Date</th>
                            <th scope="col" class="px-6 py-4 text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[30%]">End Date</th>
                            <th scope="col" class="px-6 py-4 text-center text-[11px] font-extrabold text-[#6B7086] uppercase tracking-widest w-[25%]">Current Active Quarter</th>
                        </tr>
                    </thead>
                    
                    <tbody class="block md:table-row-group divide-y divide-black/5 bg-white">
                        @foreach($quarters as $quarter)
                            
                            <!-- Mobile row behaves as a relative block with padding, PC is standard row -->
                            <tr class="block md:table-row relative md:static p-4 md:p-0 hover:bg-slate-50 transition-colors duration-150 {{ $quarter->is_active ? 'bg-[#3B82F6]/5' : '' }}">
                                
                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-4 md:mb-0">
                                    <span class="text-lg font-extrabold {{ $quarter->is_active ? 'text-[#3B82F6]' : 'text-[#1E2657]' }}">
                                        {{ $quarter->name }}
                                    </span>
                                    <!-- Mobile-only Active badge -->
                                    @if($quarter->is_active)
                                        <span class="md:hidden ml-2 px-2.5 py-1 rounded-md text-[9px] font-extrabold bg-[#3B82F6]/10 text-[#3B82F6] uppercase tracking-widest border border-[#3B82F6]/20">Active</span>
                                    @endif
                                </td>
                                
                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">Start Date</span>
                                    <input type="date" 
                                           name="quarters[{{ $quarter->id }}][start_date]" 
                                           value="{{ $quarter->start_date }}"
                                           class="w-full max-w-full md:max-w-[200px] rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors">
                                </td>
                                
                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 mb-3 md:mb-0">
                                    <span class="md:hidden block text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mb-1.5">End Date</span>
                                    <input type="date" 
                                           name="quarters[{{ $quarter->id }}][end_date]" 
                                           value="{{ $quarter->end_date }}"
                                           class="w-full max-w-full md:max-w-[200px] rounded-xl border-black/15 px-4 py-2.5 text-[13px] font-bold text-[#1E2657] focus:border-[#3B82F6] focus:ring-[#3B82F6] shadow-sm bg-white cursor-pointer transition-colors">
                                </td>
                                
                                <td class="block md:table-cell px-0 md:px-6 py-0 md:py-5 text-left md:text-center absolute md:static top-4 right-4">
                                    <label class="inline-flex items-center justify-end md:justify-center p-0 md:p-2 rounded-full cursor-pointer hover:bg-slate-100 transition-colors">
                                        <span class="md:hidden text-[10px] font-extrabold text-[#9CA0AF] uppercase tracking-widest mr-2">Set Active</span>
                                        <input type="radio" 
                                               name="active_quarter_id" 
                                               value="{{ $quarter->id }}" 
                                               {{ $quarter->is_active ? 'checked' : '' }} 
                                               required
                                               class="w-5 h-5 text-[#3B82F6] border-black/15 focus:ring-[#3B82F6] cursor-pointer transition-all shadow-sm">
                                    </label>
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

@endsection