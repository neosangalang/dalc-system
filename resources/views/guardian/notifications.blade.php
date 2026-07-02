@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-4xl mx-auto block">

    <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1 m-0">Notifications</h3>
            <p class="text-sm font-medium text-slate-500 m-0">Recent updates and system alerts.</p>
        </div>
        <button class="inline-flex items-center px-4 py-2 bg-white border-[3px] border-slate-300 rounded-xl text-slate-700 text-sm font-bold hover:bg-slate-50 hover:border-slate-400 transition-colors shadow-sm focus:outline-none">
            <i class="fa fa-check-double mr-2 text-slate-400"></i> Mark all as read
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-[3px] border-slate-300 overflow-hidden flex flex-col">
        <div class="flex flex-col divide-y-[3px] divide-slate-300">
            
            <!-- Unread Notification -->
            <div class="p-6 bg-indigo-50/40 relative hover:bg-indigo-50/80 transition-colors group">
                <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-600"></div>
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 border-2 border-indigo-200 text-indigo-600 flex items-center justify-center text-xl shrink-0 shadow-sm group-hover:border-indigo-300 transition-colors">
                        <i class="fa fa-file-signature"></i>
                    </div>
                    <div class="flex-1">
                        <h6 class="font-extrabold text-slate-800 text-base mb-1">New Report Available</h6>
                        <p class="text-sm font-medium text-slate-600 mb-4 leading-relaxed">A new official progress report has been approved and is ready for your review.</p>
                        <a href="{{ route('guardian.reports') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg border-2 border-indigo-800 hover:bg-indigo-700 transition-colors shadow-sm decoration-transparent">
                            View Report
                        </a>
                        <div class="text-xs font-bold text-slate-400 mt-4 flex items-center">
                            <i class="fa fa-clock mr-1.5"></i> Just now
                        </div>
                    </div>
                </div>
            </div>

            <!-- Read Notification -->
            <div class="p-6 bg-white hover:bg-slate-50 transition-colors group">
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border-2 border-emerald-200 text-emerald-600 flex items-center justify-center text-xl shrink-0 shadow-sm group-hover:border-emerald-300 transition-colors">
                        <i class="fa fa-bullseye"></i>
                    </div>
                    <div class="flex-1">
                        <h6 class="font-extrabold text-slate-800 text-base mb-1">IEP Goal Achieved!</h6>
                        <p class="text-sm font-medium text-slate-600 mb-0 leading-relaxed">Great news! Your child has successfully achieved an IEP goal in the Perceptuo-Cognitive domain.</p>
                        <div class="text-xs font-bold text-slate-400 mt-3 flex items-center">
                            <i class="fa fa-clock mr-1.5"></i> 2 days ago
                        </div>
                    </div>
                </div>
            </div>

            <!-- Read Notification -->
            <div class="p-6 bg-white hover:bg-slate-50 transition-colors group">
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 border-2 border-slate-300 text-slate-600 flex items-center justify-center text-xl shrink-0 shadow-sm group-hover:border-slate-400 transition-colors">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    <div class="flex-1">
                        <h6 class="font-extrabold text-slate-800 text-base mb-1">Account Linked Successfully</h6>
                        <p class="text-sm font-medium text-slate-600 mb-0 leading-relaxed">Your guardian account has been securely linked to your child's profile.</p>
                        <div class="text-xs font-bold text-slate-400 mt-3 flex items-center">
                            <i class="fa fa-clock mr-1.5"></i> 1 week ago
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection