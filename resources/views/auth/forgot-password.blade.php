<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Forgot Password - Dream Achievers Learning Center</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900" rel="stylesheet" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-['Nunito'] antialiased text-gray-900">

    <div class="min-h-[100dvh] flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5] p-4 sm:p-0">
        
        <div class="bg-white rounded-[20px] px-8 sm:px-10 py-8 w-full max-w-[420px] shadow-[0_24px_64px_rgba(0,0,0,0.25)] relative z-10">
            
            <!-- Logo -->
            <div class="w-24 h-24 mx-auto mb-2 flex items-center justify-center">
                <img src="/image/logo.png" alt="DALC Logo" class="w-full h-full object-contain drop-shadow-sm">
            </div>

            <!-- Title -->
            <h1 class="text-[18px] font-[800] text-[#1A1F4B] mb-2 text-center leading-[1.3]">
                Forgot Your Password?
            </h1>
            <p class="text-[12px] font-medium text-[#7B8DB7] text-center mb-6 leading-relaxed">
                Enter your registered Gmail or email address and we'll send you a secure link to reset your password.
            </p>

            <!-- Session Status Alert -->
            @if (session('status'))
                <div x-data="{ 
                        show: true, 
                        duration: 4500, 
                        remaining: 4500, 
                        progress: 100, 
                        paused: false, 
                        interval: null,
                        init() {
                            const step = 25;
                            this.interval = setInterval(() => {
                                if (!this.paused) {
                                    this.remaining -= step;
                                    this.progress = Math.max(0, (this.remaining / this.duration) * 100);
                                    if (this.remaining <= 0) {
                                        if (this.interval) clearInterval(this.interval);
                                        this.show = false;
                                    }
                                }
                            }, step);
                        }
                    }"
                    x-show="show"
                    x-cloak
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    x-transition:leave="transition-all ease-in-out duration-300"
                    x-transition:leave-start="opacity-100 max-h-32 mb-4 py-2.5"
                    x-transition:leave-end="opacity-0 max-h-0 mb-0 py-0 overflow-hidden border-transparent"
                    class="relative overflow-hidden bg-emerald-50 border border-emerald-200 text-emerald-700 text-[12px] py-2.5 px-3.5 rounded-xl mb-4 font-bold flex items-start justify-between gap-2 shadow-sm">
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" @click="if (interval) clearInterval(interval); show = false" class="text-emerald-500 hover:text-emerald-700 p-0.5 cursor-pointer shrink-0">
                        <i class="fa fa-times text-xs"></i>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-200 overflow-hidden">
                        <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            @endif

            <!-- Errors Alert -->
            @if ($errors->any())
                <div x-data="{ 
                        show: true, 
                        duration: 6000, 
                        remaining: 6000, 
                        progress: 100, 
                        paused: false, 
                        interval: null,
                        init() {
                            const step = 25;
                            this.interval = setInterval(() => {
                                if (!this.paused) {
                                    this.remaining -= step;
                                    this.progress = Math.max(0, (this.remaining / this.duration) * 100);
                                    if (this.remaining <= 0) {
                                        if (this.interval) clearInterval(this.interval);
                                        this.show = false;
                                    }
                                }
                            }, step);
                        }
                    }"
                    x-show="show"
                    x-cloak
                    @mouseenter="paused = true"
                    @mouseleave="paused = false"
                    x-transition:leave="transition-all ease-in-out duration-300"
                    x-transition:leave-start="opacity-100 max-h-32 mb-4 py-2.5"
                    x-transition:leave-end="opacity-0 max-h-0 mb-0 py-0 overflow-hidden border-transparent"
                    class="relative overflow-hidden bg-rose-50 border border-rose-200 text-rose-600 text-[12px] py-2.5 px-3.5 rounded-xl mb-4 font-bold flex items-start justify-between gap-2 shadow-sm">
                    <div class="flex items-start gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5 text-sm shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                    <button type="button" @click="if (interval) clearInterval(interval); show = false" class="text-rose-400 hover:text-rose-600 p-0.5 cursor-pointer shrink-0">
                        <i class="fa fa-times text-xs"></i>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-rose-200 overflow-hidden">
                        <div class="h-full bg-rose-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-5">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1.5">Registered Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="e.g. yourname@gmail.com"
                            class="w-full border-2 border-[#E8ECF8] rounded-xl pl-9 pr-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10 font-medium">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-br from-[#4F6AF5] to-[#3751D7] text-white rounded-xl py-2.5 px-6 font-bold text-[14px] shadow-[0_4px_16px_rgba(79,106,245,0.3)] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(79,106,245,0.4)] hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer mb-4">
                    <i class="fa-solid fa-paper-plane text-[12px]"></i> Send Password Reset Link
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-[12px] font-extrabold text-[#7B8DB7] hover:text-[#4F6AF5] transition-colors inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Return to Sign In
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

