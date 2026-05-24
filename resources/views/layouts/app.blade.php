<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Dashboard E-Ujian') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    
    <body class="font-sans antialiased bg-gradient-to-br from-green-50 via-teal-50 to-emerald-100 min-h-screen relative selection:bg-lime-400 selection:text-emerald-900">
        
        <div class="fixed inset-0 -z-20 flex items-center justify-center pointer-events-none overflow-hidden">
            <img src="{{ asset('images/logo-sekolah.png') }}" 
                 class="w-[80%] md:w-[40%] h-auto object-contain opacity-10 blur-[1px]" 
                 alt="Background Watermark">
        </div>

        <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-emerald-300/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-teal-300/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-96 h-96 bg-lime-300/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <div x-data="{ 
                sidebarOpen: false, 
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                toggleCollapse() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                }
             }">
            
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
                 :class="sidebarCollapsed ? 'md:ml-24' : 'md:ml-64'">
                
                <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-emerald-950/60 backdrop-blur-sm z-40 md:hidden" style="display: none;"></div>

                <main class="p-4 md:p-8 pt-20 md:pt-8">
                    
                    @if (isset($header))
                        <div class="mb-8 p-6 rounded-[2rem] bg-white/40 backdrop-blur-xl border border-white/50 shadow-[0_8px_32px_rgba(31,38,135,0.05)] flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
                            <h2 class="text-2xl font-extrabold text-emerald-900 drop-shadow-sm tracking-tight">
                                {{ $header }}
                            </h2>
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/60 rounded-full border border-white/50 text-emerald-700 text-sm font-semibold shadow-inner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </div>
                        </div>
                    @endif

                    <div class="animate-fade-in-up">
                        {{ $slot }}
                    </div>
                
                </main>
            </div>
        </div>

        <style>
            /* CUSTOM SCROLLBAR TEMA GLASSMORPHISM EMERALD/LIME */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: rgba(6, 78, 59, 0.05);
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb {
                background: rgba(163, 230, 53, 0.25);
                border-radius: 9999px;
                border: 1.5px solid rgba(16, 185, 129, 0.1);
            }
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(163, 230, 53, 0.65);
                box-shadow: 0 0 10px rgba(163, 230, 53, 0.4);
            }
            
            /* Firefox Support */
            * {
                scrollbar-width: thin;
                scrollbar-color: rgba(163, 230, 53, 0.25) rgba(6, 78, 59, 0.05);
            }

            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob { animation: blob 10s infinite; }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-delay-4000 { animation-delay: 4s; }
            .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in-down { animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
            @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    </body>
</html>