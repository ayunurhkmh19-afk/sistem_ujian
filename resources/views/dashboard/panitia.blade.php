<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard Panitia') }}
    </x-slot>

    <div class="mb-8 relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-emerald-900 to-teal-800 text-white shadow-2xl p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
        
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-lime-400/20 blur-3xl"></div>

        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                Halo, <span class="text-lime-400">{{ Auth::user()->name }}</span>! 👋
            </h2>
            <p class="text-emerald-100 text-lg max-w-xl">
                Selamat datang di panel administrasi ujian. Siap untuk membuat jadwal dan kartu ujian baru hari ini?
            </p>
        </div>
        
        <div class="relative z-10 shrink-0">
            <a href="{{ route('wizard.step1') }}" class="group flex items-center gap-3 px-6 py-4 bg-white text-emerald-900 font-bold rounded-2xl shadow-lg hover:shadow-emerald-500/50 hover:scale-105 transition-all duration-300">
                <div class="p-2 bg-emerald-100 rounded-xl group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </div>
                <span>Buat Ujian Baru</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <a href="{{ route('students.index') }}" class="group bg-white/40 backdrop-blur-lg border border-white/50 p-6 rounded-[2rem] shadow-lg hover:bg-white/60 transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-teal-100 text-teal-600 group-hover:bg-teal-500 group-hover:text-white transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/50 border border-white/50 text-xs font-bold text-teal-800">Siswa</span>
            </div>
            <h3 class="text-3xl font-black text-emerald-900">{{ \App\Models\Student::count() }}</h3>
            <p class="text-emerald-800/70 font-medium text-xs mt-1">Total Siswa Terdaftar</p>
        </a>

        <a href="{{ route('sessions.index') }}" class="group bg-white/40 backdrop-blur-lg border border-white/50 p-6 rounded-[2rem] shadow-lg hover:bg-white/60 transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-lime-100 text-lime-600 group-hover:bg-lime-500 group-hover:text-white transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/50 border border-white/50 text-xs font-bold text-lime-800">Arsip</span>
            </div>
            <h3 class="text-3xl font-black text-emerald-900">{{ $sessions->count() }}</h3>
            <p class="text-emerald-800/70 font-medium text-xs mt-1">Total Sesi Ujian</p>
        </a>

        <a href="{{ route('users.index') }}" class="group bg-white/40 backdrop-blur-lg border border-white/50 p-6 rounded-[2rem] shadow-lg hover:bg-white/60 transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-sky-100 text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/50 border border-white/50 text-xs font-bold text-sky-800">Akun</span>
            </div>
            <h3 class="text-3xl font-black text-emerald-900">{{ \App\Models\User::count() }}</h3>
            <p class="text-emerald-800/70 font-medium text-xs mt-1">Pengguna & Pengawas</p>
        </a>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-6 rounded-[2rem] shadow-xl relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-2 opacity-90">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-lime-400"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest">Status Sistem</span>
                </div>
                <h3 class="text-base font-bold leading-tight mb-2">Sistem Siap</h3>
                <p class="text-xs opacity-80">Pastikan data siswa & akun pengawas sudah terbaru.</p>
            </div>
        </div>
    </div>

    <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl overflow-hidden">
        <div class="p-6 border-b border-white/20 flex justify-between items-center">
            <h3 class="text-lg font-bold text-emerald-900">Riwayat Ujian Terakhir</h3>
            <a href="{{ route('sessions.index') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-500 transition-colors">Lihat Semua &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-emerald-900/5 text-emerald-900/70 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Nama Kegiatan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/20">
                    @forelse($sessions->take(5) as $session)
                    <tr class="hover:bg-white/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-emerald-900">
                            {{ $session->title }}
                        </td>
                        <td class="px-6 py-4 text-sm text-emerald-800">
                            {{ \Carbon\Carbon::parse($session->start_date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $session->is_active ? 'bg-lime-400/20 text-lime-800 border-lime-400/50' : 'bg-gray-200/50 text-gray-500 border-gray-300' }}">
                                {{ $session->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('wizard.step3', $session->id) }}" class="text-sm font-bold text-blue-600 hover:underline bg-white/50 px-3 py-1 rounded-lg">
                                Kelola
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                            Belum ada sesi ujian. Mulai dengan membuat baru!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>