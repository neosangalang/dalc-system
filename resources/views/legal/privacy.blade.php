<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Dream Achievers Learning Center PH</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 min-h-screen">

    <!-- Simple Top Bar -->
    <header class="no-print border-b border-gray-200 bg-white sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-sm text-gray-900">Dream Achievers Learning Center</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="text-xs font-semibold px-3 py-1.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    <i class="fa fa-print mr-1"></i> Print
                </button>
                @if(Auth::check())
                    <a href="{{ route(strtolower(Auth::user()->role) . '.dashboard') }}" class="text-xs font-semibold px-3 py-1.5 text-white bg-gray-900 hover:bg-black rounded-lg transition">
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-1.5 text-white bg-gray-900 hover:bg-black rounded-lg transition">
                        Login
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Plain White Document -->
    <main class="max-w-4xl mx-auto px-6 sm:px-12 py-10 bg-white">
        @include('legal.privacy-content')
    </main>

</body>
</html>
