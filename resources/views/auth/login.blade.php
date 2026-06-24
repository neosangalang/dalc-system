<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Dream Achievers Learning Center</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900" rel="stylesheet" />
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-['Nunito'] antialiased text-gray-900">

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5]">
        
        <div class="bg-white rounded-[24px] px-11 py-12 w-full max-w-[440px] shadow-[0_24px_64px_rgba(0,0,0,0.25)] relative z-10 m-4">
            
            <div class="w-32 h-32 mx-auto mb-1 flex items-center justify-center">
                <img src="/image/logo.png" alt="DALC Logo" class="w-full h-full object-contain drop-shadow-sm">
            </div>

            <h1 class="text-[22px] font-[800] text-[#1A1F4B] mb-6 text-center leading-[1.3]">
                Dream Achievers<br>Learning Center PH
            </h1>
            

            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 text-[13px] py-2 px-3 rounded-[10px] mb-4 font-medium flex items-start gap-2">
                        <span class="mt-0.5">❌</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="block font-bold text-[13px] text-[#1A1F4B] mb-1.5">Username</label>
                    <input type="text" name="username" required autofocus placeholder="Enter your username"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-4 py-3 text-[14px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10">
                </div>

                <div class="mb-4">
                    <label class="block font-bold text-[13px] text-[#1A1F4B] mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Enter your password"
                        class="w-full border-2 border-[#E8ECF8] rounded-xl px-4 py-3 text-[14px] outline-none transition-all focus:border-[#4F6AF5] focus:ring-4 focus:ring-[#4F6AF5]/10">
                </div>

                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember_me" 
                            class="w-4 h-4 text-[#4F6AF5] border-[#E8ECF8] rounded focus:ring-[#4F6AF5]">
                        <label for="remember_me" class="text-[13px] text-[#7B8DB7] cursor-pointer">
                            Remember me
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[#4F6AF5] text-[13px] font-bold no-underline hover:underline transition-all">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" 
                    class="w-full bg-gradient-to-br from-[#4F6AF5] to-[#3751D7] text-white rounded-xl py-[13px] px-6 font-bold text-[15px] shadow-[0_4px_16px_rgba(79,106,245,0.3)] transition-all duration-200 hover:shadow-[0_6px_20px_rgba(79,106,245,0.4)] hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fa fa-sign-in-alt"></i> Sign In
                </button>
            </form>
        </div>
    </div>

</body>
</html>