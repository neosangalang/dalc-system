<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Password - Dream Achievers Learning Center</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900" rel="stylesheet" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
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
                Create New Password
            </h1>
            <p class="text-[12px] font-medium text-[#7B8DB7] text-center mb-6 leading-relaxed">
                Please enter your registered email and choose a strong new password for your account.
            </p>

            <!-- Errors Alert -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-600 text-[12px] py-2.5 px-3.5 rounded-xl mb-4 font-bold flex items-start gap-2 shadow-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 mt-0.5 text-sm shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="mb-4">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10 font-medium">
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1">New Password</label>
                    <input type="password" name="password" required placeholder="Minimum 8 characters"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10 font-medium">
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block font-bold text-[12px] text-[#1A1F4B] mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Re-type new password"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-3.5 py-2.5 text-[13px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10 font-medium">
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-br from-[#4F6AF5] to-[#3751D7] text-white rounded-xl py-2.5 px-6 font-bold text-[14px] shadow-[0_4px_16px_rgba(79,106,245,0.3)] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(79,106,245,0.4)] hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer mb-4">
                    <i class="fa-solid fa-key text-[12px]"></i> Reset & Update Password
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

