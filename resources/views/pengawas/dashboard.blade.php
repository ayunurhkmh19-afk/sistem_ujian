<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard Pengawas') }}
    </x-slot>

    <!-- Welcome Card -->
    <div class="mb-8 relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-emerald-900 to-teal-800 text-white shadow-2xl p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-lime-400/20 blur-3xl"></div>

        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                Halo, <span class="text-lime-400">{{ Auth::user()->name }}</span>! 👋
            </h2>
            <p class="text-emerald-100 text-lg max-w-xl">
                Selamat datang di panel pengawas. Silakan pantau jadwal ujian dan lakukan absensi kehadiran siswa tepat waktu.
            </p>
        </div>
        
        <div class="relative z-10 shrink-0">
            <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-lime-400 flex items-center justify-center text-emerald-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-emerald-300 font-bold uppercase tracking-widest">Sesi Aktif</p>
                    <p class="text-xl font-black">{{ $countHariIni }} Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Menu -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Card: Jadwal Hari Ini -->
        <div class="group bg-white/40 backdrop-blur-lg border border-white/50 p-6 rounded-[2rem] shadow-lg hover:bg-white/60 transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-teal-100 text-teal-600 group-hover:bg-teal-500 group-hover:text-white transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/50 border border-white/50 text-xs font-bold text-teal-800">Hari Ini</span>
            </div>
            <h3 class="text-3xl font-black text-emerald-900">{{ $countHariIni }} Sesi</h3>
            <p class="text-emerald-800/70 font-medium italic">Jadwal pengawasan Anda hari ini</p>
        </div>

        <!-- Card: Riwayat Selesai -->
        <div class="group bg-white/40 backdrop-blur-lg border border-white/50 p-6 rounded-[2rem] shadow-lg hover:bg-white/60 transition-all hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 rounded-2xl bg-blue-100 text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="px-3 py-1 rounded-full bg-white/50 border border-white/50 text-xs font-bold text-blue-800">Selesai</span>
            </div>
            <h3 class="text-3xl font-black text-emerald-900">{{ $countRiwayat }} Sesi</h3>
            <p class="text-emerald-800/70 font-medium italic">Riwayat pengawasan yang telah selesai</p>
        </div>

        <!-- Card: Panduan SOP -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white p-6 rounded-[2rem] shadow-xl relative overflow-hidden group cursor-pointer hover:shadow-emerald-500/30 transition-all">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/20 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/20 rounded-xl border border-white/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest opacity-90">Bantuan</span>
                </div>
                <h3 class="text-lg font-bold leading-tight mb-2">Panduan Pengawas</h3>
                <p class="text-xs opacity-80">Klik untuk membaca tata tertib dan SOP pelaksanaan ujian di SMAN 3 Bontang.</p>
            </div>
        </div>
    </div>

    <!-- Jadwal Table -->
    <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl overflow-hidden">
        <div class="p-6 border-b border-white/20 flex justify-between items-center">
            <h3 class="text-lg font-bold text-emerald-900">Manajemen Sesi Ujian Saya</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-emerald-900/5 text-emerald-900/70 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4">Kegiatan / Tanggal</th>
                        <th class="px-6 py-4 text-center">Ruangan</th>
                        <th class="px-6 py-4 text-center">Status Sesi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/20">
                    @forelse($jadwalDiolah as $item)
                    <tr class="hover:bg-white/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-emerald-900">{{ $item->schedule->session->title }}</div>
                            <div class="text-sm font-black text-emerald-600 mb-1">
                                {{ $item->schedule->subject->name ?? '-' }} ({{ $item->schedule->subject->level->name }})
                            </div>
                            <div class="text-xs text-emerald-700/70 font-medium">
                                {{ $item->schedule ? $item->schedule->exam_date->translatedFormat('d F Y') : '-' }} <br>
                                {{ $item->schedule ? substr($item->schedule->timeSession->start_time, 0, 5) . ' - ' . substr($item->schedule->timeSession->end_time, 0, 5) : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-4 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-sm border border-emerald-200">
                                {{ $item->room->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->status_sesi == 'Sudah Berlangsung')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-blue-100 text-blue-800 border-blue-200">
                                    Sudah Berlangsung
                                </span>
                            @elseif($item->status_sesi == 'Sedang Berlangsung')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border bg-red-100 text-red-800 border-red-200">
                                    <span class="flex h-2 w-2 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                    Sedang Berlangsung
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-gray-100 text-gray-800 border-gray-200">
                                    Akan Berlangsung
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('pengawas.sesi.detail', ['schedule_id' => $item->exam_schedule_id, 'room_id' => $item->room_id]) }}" 
                               class="group inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-lg transition-all hover:scale-105 active:scale-95">
                               <span>Lihat Detail</span>
                               <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Belum ada penugasan pengawasan untuk Anda.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
