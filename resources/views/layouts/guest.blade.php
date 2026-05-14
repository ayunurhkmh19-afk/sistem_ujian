<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMA Negeri 3 Bontang') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* 1. Ubah Label (Email, Password) jadi Putih */
        .glass-form label {
            color: #ffffff !important;
            font-weight: 600 !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        
        /* 2. Ubah Teks Tambahan (Remember me, Forgot Password) jadi Putih/Terang */
        .glass-form .text-gray-600, 
        .glass-form .text-sm.text-gray-600 {
            color: #d1fae5 !important; /* Hijau muda terang */
        }
        
        /* 3. Efek Hover pada Link Forgot Password */
        .glass-form a.text-gray-600:hover {
            color: #ffffff !important;
            text-shadow: 0 0 5px rgba(255,255,255,0.5);
        }

        /* 4. Ubah Tombol Login jadi Gradasi Hijau (bukan Hitam) */
        .glass-form button[type="submit"] {
            background-image: linear-gradient(to right, #10b981, #059669) !important; /* emerald-500 -> emerald-600 */
            background-color: transparent !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            color: white !important;
            font-weight: bold !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .glass-form button[type="submit"]:hover {
            background-image: linear-gradient(to right, #34d399, #10b981) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        /* 5. (Opsional) Mempercantik Input Field agar semi-transparan */
        .glass-form input[type="text"],
        .glass-form input[type="email"],
        .glass-form input[type="password"] {
            background-color: rgba(255, 255, 255, 0.9) !important; /* Putih 90% */
            border: 1px solid rgba(255,255,255,0.5) !important;
            color: #064e3b !important; /* Teks input hijau tua */
        }
        .glass-form input:focus {
            background-color: #ffffff !important;
            border-color: #34d399 !important; /* Ring hijau saat diklik */
            box-shadow: 0 0 0 2px rgba(52, 211, 153, 0.5) !important;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
        
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/logo-sekolah.png') }}" class="w-full h-full object-cover blur-sm scale-105 opacity-60" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/80 via-teal-900/80 to-green-900/80 mix-blend-multiply"></div>
        </div>

        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-lime-400 rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-emerald-400 rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

        <div class="relative z-10 w-full sm:max-w-md flex flex-col items-center">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl mb-4 animate-fade-in-down">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMA 3" class="w-24 h-auto object-contain drop-shadow-lg">
                </div>
                <h1 class="text-3xl font-bold text-white drop-shadow-md tracking-wide font-figtree uppercase">
                    E-UJIAN
                </h1>
                <h2 class="text-sm font-medium text-emerald-100 tracking-widest mt-1 uppercase">
                    SMA NEGERI 3 BONTANG
                </h2>
            </div>

            <div class="w-full px-8 py-10 bg-white/10 backdrop-blur-xl border border-white/20 shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] rounded-[2rem] overflow-hidden relative">
                
                <div class="absolute inset-0 border border-white/10 rounded-[2rem] pointer-events-none"></div>
                
                <div class="glass-form relative z-10">
                    {{ $slot }}
                </div>

            </div>

            <div class="mt-8 text-emerald-100/60 text-xs text-center font-light">
                &copy; {{ date('Y') }} SMA Negeri 3 Bontang.<br>All rights reserved.
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        
        .animate-fade-in-down { animation: fadeInDown 0.8s ease-out; }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>