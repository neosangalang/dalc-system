@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<div class="font-['Inter'] py-8 px-4 sm:px-6 lg:px-8 w-full max-w-7xl mx-auto block">

    <div class="flex flex-col xl:flex-row gap-6 items-start">
        
        <div class="w-full xl:w-1/3">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 text-center sticky top-[100px]">
                
                <div class="mx-auto mb-4 shadow-sm w-[120px] h-[120px] bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-[48px] text-white font-extrabold border-[6px] border-indigo-50">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                
                <h4 class="text-xl font-extrabold text-slate-800 mb-1">{{ Auth::user()->name }}</h4>
                <p class="text-sm font-medium text-slate-500 mb-4">{{ Auth::user()->email }}</p>
                
                <div class="flex justify-center gap-2 mb-6">
                    <span class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-full text-xs font-extrabold uppercase tracking-wider shadow-sm">
                        <i class="fa fa-user-shield mr-2"></i> {{ Auth::user()->role }}
                    </span>
                </div>
                
                <hr class="border-slate-100 mb-6">
                
                <div class="text-left px-2 space-y-3">
                    <p class="text-[13px] font-medium text-slate-500 m-0 flex items-center">
                        <i class="fa fa-id-badge w-5 text-slate-400 text-center mr-2"></i> 
                        <strong class="text-slate-700 mr-2">Username:</strong> {{ Auth::user()->username }}
                    </p>
                    <p class="text-[13px] font-medium text-slate-500 m-0 flex items-center">
                        <i class="fa fa-clock w-5 text-slate-400 text-center mr-2"></i> 
                        <strong class="text-slate-700 mr-2">Joined:</strong> {{ Auth::user()->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full xl:w-2/3">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-10">
                
                <h5 class="text-lg font-extrabold text-slate-800 mb-8 flex items-center m-0">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3 shadow-sm">
                        <i class="fa fa-id-card"></i>
                    </div>
                    Personal Information
                </h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Full Name</label>
                        <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 text-slate-500 px-4 py-3 rounded-xl text-sm cursor-not-allowed focus:outline-none" value="{{ Auth::user()->name }}" readonly>
                    </div>
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 text-slate-500 px-4 py-3 rounded-xl text-sm cursor-not-allowed focus:outline-none" value="{{ Auth::user()->email }}" readonly>
                    </div>
                </div>
                
                <div>
                    <label class="block text-[13px] font-bold text-slate-700 mb-2">Username</label>
                    <input type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-500 px-4 py-3 rounded-xl text-sm cursor-not-allowed focus:outline-none" value="{{ Auth::user()->username }}" disabled>
                    
                    <p class="text-[12px] font-medium text-slate-500 mt-3 m-0 flex items-center">
                        <i class="fa fa-info-circle mr-1.5 text-indigo-400"></i> 
                        Please contact your System Administrator if you need to update your profile details.
                    </p>
                </div>
                
            </div>
        </div>

    </div>
</div>

@endsection