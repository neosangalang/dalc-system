<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Dream Achievers Learning Center</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900" rel="stylesheet" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Mobile usability: Prevent iOS Safari auto-zoom on input focus */
        @media (max-width: 640px) {
            input[type="text"],
            input[type="password"] {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body class="font-['Nunito'] antialiased text-gray-900">

    <div class="min-h-[100dvh] flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5] p-4 sm:p-0">
        
        <!-- Mobile optimized padding: px-6 on mobile, px-10 on sm+ -->
        <div class="bg-white rounded-[20px] px-6 sm:px-10 py-6 sm:py-8 w-full max-w-[390px] shadow-[0_24px_64px_rgba(0,0,0,0.25)] relative z-10">
            
            <!-- REDUCED: Logo size scaled down -->
            <div class="w-32 h-32 mx-auto mb-2 flex items-center justify-center">
                <img src="/image/logo.png" alt="DALC Logo" class="w-full h-full object-contain drop-shadow-sm">
            </div>

            <!-- REDUCED: Font size and margin -->
            <h1 class="text-[18px] font-[800] text-[#1A1F4B] mb-5 text-center leading-[1.3]">
                Dream Achievers<br>Learning Center PH
            </h1>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if (session('status'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-[12px] py-2.5 px-3 rounded-lg mb-4 font-bold flex items-start gap-2 shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 text-sm shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-[12px] py-2 px-3 rounded-lg mb-4 font-medium flex items-start gap-2">
                        <span class="mt-0.5">❌</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1">Username</label>
                    <!-- REDUCED: Input padding (py-2.5) and text size -->
                    <input type="text" name="username" required autofocus placeholder="Enter your username"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10">
                </div>

                <div class="mb-4">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Enter your password"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10">
                </div>

                <div class="flex justify-end items-center mb-5">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[#4F6AF5] text-[12px] font-bold no-underline hover:underline transition-all">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Sign In Button -->
                <button type="submit" 
                    class="w-full bg-gradient-to-br from-[#4F6AF5] to-[#3751D7] text-white rounded-xl py-2.5 px-6 font-bold text-[14px] shadow-[0_4px_16px_rgba(79,106,245,0.3)] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(79,106,245,0.4)] hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa fa-sign-in-alt text-[13px]"></i> Sign In
                </button>

                <!-- Terms and Privacy Agreement Micro-Notice -->
                <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed m-0">
                        By signing in, you agree to our 
                        <a href="{{ route('legal.terms') }}" onclick="return openTermsModal(event)" class="text-[#4F6AF5] font-bold hover:underline cursor-pointer">Terms and Conditions</a> 
                        and 
                        <a href="{{ route('legal.privacy') }}" onclick="return openPrivacyModal(event)" class="text-[#4F6AF5] font-bold hover:underline cursor-pointer">Privacy Policy</a> (RA 10173).
                    </p>
                </div>
            </form>
        </div>

        <!-- Compliance & Security Badge -->
        <div class="absolute bottom-3 text-center text-white/70 text-[11px] font-semibold space-x-2">
            <span><i class="fa fa-shield-halved text-emerald-400"></i> 3-Gate MFA Protected</span>
        </div>
    </div>

    @include('components.terms-modal')
    @include('components.privacy-modal')

</body>
</html>