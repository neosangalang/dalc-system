<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Legal & Compliance') - Dream Achievers Learning Center PH</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .legal-card { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }

        .legal-content h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1E2657;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .legal-content h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #2E3D8F;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .legal-content p, .legal-content li {
            font-size: 0.925rem;
            line-height: 1.7;
            color: #374151;
        }

        .legal-content ul, .legal-content ol {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .legal-content li {
            margin-bottom: 0.35rem;
        }

        .legal-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body class="font-['Nunito'] bg-[#F4F6FB] text-[#1E2657] antialiased min-h-screen flex flex-col selection:bg-[#4F6AF5] selection:text-white">

    <!-- TOP HEADER -->
    <header class="no-print sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-[#1E2657]/10 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ Auth::check() ? route(strtolower(Auth::user()->role) . '.dashboard') : route('login') }}" class="flex items-center gap-3 group text-decoration-none">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1E2657] to-[#2E3D8F] flex items-center justify-center text-white font-black text-sm shadow-sm group-hover:scale-105 transition-transform">
                    DA
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-extrabold text-[#1E2657] leading-tight m-0">Dream Achievers Learning Center</h1>
                    <p class="text-[11px] font-semibold text-[#6B7280] m-0">Institutional Governance & Legal Portal</p>
                </div>
            </a>

            <div class="flex items-center gap-2.5">
                <button onclick="window.print()" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#1E2657] bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors border border-black/5">
                    <i class="fa fa-print"></i> Print / Save PDF
                </button>
                @if(Auth::check())
                    <a href="{{ route(strtolower(Auth::user()->role) . '.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-extrabold text-white bg-[#4F6AF5] hover:bg-[#3b54d1] rounded-lg shadow-sm transition-all">
                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-extrabold text-white bg-[#4F6AF5] hover:bg-[#3b54d1] rounded-lg shadow-sm transition-all">
                        <i class="fa fa-sign-in-alt"></i> Portal Login
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <div class="no-print bg-gradient-to-br from-[#1E2657] via-[#2E3D8F] to-[#4F6AF5] text-white py-10 px-4 sm:px-6 shadow-md relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="max-w-6xl mx-auto relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="legal-badge bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                    <i class="fa fa-shield-halved"></i> RA 10173 Compliant
                </span>
                <span class="legal-badge bg-amber-500/20 text-amber-300 border border-amber-400/30">
                    <i class="fa fa-child-reaching"></i> Special Education Standards
                </span>
            </div>
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight m-0">@yield('page_title', 'Terms & Institutional Policies')</h2>
            <p class="text-xs sm:text-sm text-slate-200 mt-2 max-w-3xl leading-relaxed m-0">
                Dream Achievers Learning Center (DALC) upholds stringent safeguards for confidential Special Education (SPED) student records, clinical diagnostic evaluations, and parent-teacher collaboration.
            </p>
        </div>
    </div>

    <!-- TAB NAVIGATION BAR -->
    <nav class="no-print sticky top-16 z-40 bg-white border-b border-[#1E2657]/10 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex space-x-1 sm:space-x-3 overflow-x-auto py-2.5 custom-scrollbar text-xs sm:text-sm font-extrabold">
                <a href="{{ route('legal.terms') }}" 
                   class="px-3.5 py-2 rounded-xl transition-all whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('legal.terms') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#4B5563] hover:text-[#1E2657] hover:bg-slate-100' }}">
                    <i class="fa fa-file-contract"></i> Terms & Conditions
                </a>
                <a href="{{ route('legal.privacy') }}" 
                   class="px-3.5 py-2 rounded-xl transition-all whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('legal.privacy') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#4B5563] hover:text-[#1E2657] hover:bg-slate-100' }}">
                    <i class="fa fa-user-shield"></i> Privacy Policy (RA 10173)
                </a>
                <a href="{{ route('legal.parental-consent') }}" 
                   class="px-3.5 py-2 rounded-xl transition-all whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('legal.parental-consent') ? 'bg-[#1E2657] text-white shadow-sm' : 'text-[#4B5563] hover:text-[#1E2657] hover:bg-slate-100' }}">
                    <i class="fa fa-handshake"></i> Parental Consent & Media
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Table of Contents (Desktop Sticky Sidebar) -->
            <aside class="no-print hidden lg:block lg:col-span-4 sticky top-36">
                <div class="bg-white rounded-2xl p-5 border border-black/10 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-black/5 pb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-[#6B7280]">Table of Contents</span>
                        <span class="text-[11px] font-bold text-[#10B981] bg-emerald-50 px-2 py-0.5 rounded-md">
                            Effective 2026
                        </span>
                    </div>
                    @yield('toc')
                    <div class="pt-3 border-t border-black/5 text-[11px] text-[#6B7280] space-y-1">
                        <div><strong class="text-[#1E2657]">Jurisdiction:</strong> Republic of the Philippines</div>
                        <div><strong class="text-[#1E2657]">Data Protection Officer:</strong> dpo@dalc.edu.ph</div>
                        <div><strong class="text-[#1E2657]">Center Hotline:</strong> 0936-952-3782</div>
                    </div>
                </div>
            </aside>

            <!-- Main Legal Document Card -->
            <article class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-10 border border-black/10 shadow-sm legal-card legal-content">
                <div class="border-b border-black/10 pb-4 mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <div>
                        <span class="text-xs font-extrabold text-[#4F6AF5] uppercase tracking-wider">Dream Achievers Learning Center</span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#1E2657] m-0 border-none p-0">@yield('doc_heading')</h2>
                    </div>
                    <div class="text-[12px] font-bold text-[#6B7280]">
                        Last Revised: <span class="text-[#1E2657]">{{ $lastUpdated ?? 'October 28, 2026' }}</span>
                    </div>
                </div>

                @yield('document_body')

                <!-- Document Signature / Authority Stamp -->
                <div class="mt-12 pt-6 border-t border-black/10 bg-[#F8FAFC] rounded-xl p-5 border">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#10B981] text-white flex items-center justify-center font-bold text-lg shrink-0">
                            ✓
                        </div>
                        <div>
                            <div class="text-[11px] font-extrabold text-[#C4922E] uppercase tracking-wider">Official Institutional Policy</div>
                            <div class="text-sm font-extrabold text-[#1E2657]">Board of Trustees & Administration &bull; Dream Achievers Learning Center</div>
                            <div class="text-xs text-[#6B7280]">In compliance with RA 10173 (DPA 2012), DepEd DO 40 s. 2012, and RA 11650.</div>
                        </div>
                    </div>
                </div>
            </article>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="no-print mt-auto bg-[#1E2657] text-white py-10 px-4 sm:px-6 text-xs border-t border-white/10">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="w-7 h-7 rounded-lg bg-[#C4922E] flex items-center justify-center text-white font-black text-xs">DA</div>
                    <span class="font-extrabold text-sm tracking-tight">Dream Achievers Learning Center</span>
                </div>
                <p class="text-slate-300 leading-relaxed m-0">
                    A specialized educational and developmental institution committed to fostering holistic growth for learners with diverse developmental needs.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-2.5">Legal & Compliance Quick Links</h4>
                <ul class="space-y-1.5 list-none p-0 m-0">
                    <li><a href="{{ route('legal.terms') }}" class="text-slate-300 hover:text-white transition-colors">Terms & Conditions</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="text-slate-300 hover:text-white transition-colors">Privacy Policy (RA 10173)</a></li>
                    <li><a href="{{ route('legal.parental-consent') }}" class="text-slate-300 hover:text-white transition-colors">Parental Consent & Media Release Agreement</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white text-xs uppercase tracking-wider mb-2.5">Contact & Compliance</h4>
                <p class="text-slate-300 leading-relaxed m-0">
                    <strong>Operating Hours:</strong> Mon - Fri, 8:00 AM – 5:00 PM<br>
                    <strong>Hotline:</strong> 0936-952-3782<br>
                    <strong>Compliance Officer:</strong> dpo@dalc.edu.ph<br>
                    <strong>Location:</strong> Philippines
                </p>
            </div>
        </div>
        <div class="max-w-6xl mx-auto mt-8 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center text-slate-400 gap-2">
            <span>&copy; {{ date('Y') }} Dream Achievers Learning Center PH. All rights reserved.</span>
            <span>Protected by 3-Gate MFA &bull; Encrypted Storage</span>
        </div>
    </footer>

    @include('components.toast-notification')
</body>
</html>
