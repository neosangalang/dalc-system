@extends('layouts.app')

@section('title', 'Manage Academic Calendar')

@section('content')

<div class="font-['Inter'] bg-slate-50 min-h-screen py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-sm flex justify-between items-center animate-fade-in-down">
            <div class="flex items-center">
                <i class="fa fa-check-circle mr-3 text-emerald-500 text-lg"></i>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
    @endif

    <div class="mb-8">
        <h3 class="text-2xl font-extrabold text-slate-800 mb-1 flex items-center">
            <i class="fa fa-calendar-days text-indigo-600 mr-3"></i> Academic Calendar
        </h3>
        <p class="text-slate-500 text-sm font-medium m-0">Set the start and end dates for each quarter. These dates will automatically appear on the Teacher Dashboard.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-slate-300 overflow-hidden">
        <form action="{{ route('admin.quarters.update') }}" method="POST" class="m-0">
            @csrf

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider w-[15%]">Quarter</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider w-[30%]">Start Date</th>
                            <th scope="col" class="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase tracking-wider w-[30%]">End Date</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-extrabold text-slate-500 uppercase tracking-wider w-[25%]">Current Active Quarter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach($quarters as $quarter)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150 {{ $quarter->is_active ? 'bg-indigo-50/30' : '' }}">
                                
                                <td class="px-6 py-5">
                                    <span class="text-lg font-extrabold {{ $quarter->is_active ? 'text-indigo-600' : 'text-slate-700' }}">
                                        {{ $quarter->name }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-5">
                                    <input type="date" 
                                           name="quarters[{{ $quarter->id }}][start_date]" 
                                           value="{{ $quarter->start_date }}"
                                           class="w-full max-w-[200px] rounded-xl border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer transition-colors">
                                </td>
                                
                                <td class="px-6 py-5">
                                    <input type="date" 
                                           name="quarters[{{ $quarter->id }}][end_date]" 
                                           value="{{ $quarter->end_date }}"
                                           class="w-full max-w-[200px] rounded-xl border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer transition-colors">
                                </td>
                                
                                <td class="px-6 py-5 text-center">
                                    <label class="inline-flex items-center justify-center p-2 rounded-full cursor-pointer hover:bg-slate-100 transition-colors">
                                        <input type="radio" 
                                               name="active_quarter_id" 
                                               value="{{ $quarter->id }}" 
                                               {{ $quarter->is_active ? 'checked' : '' }} 
                                               required
                                               class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer transition-all">
                                    </label>
                                </td>
                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 hover:-translate-y-0.5">
                    <i class="fa fa-save mr-2"></i> Save Calendar Settings
                </button>
            </div>

        </form>
    </div>

</div>

@endsection