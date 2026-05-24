<x-app-layout>
    <x-slot name="header">
        Wizard: Pre-Flight Check & Generate
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 5: Pre-Flight Check & Eksekusi</span>
                <span class="opacity-100 font-extrabold text-emerald-600">Progres: 100%</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 100%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Pre-Flight Check Penjadwalan</h3>
            <p class="text-emerald-800/60 text-sm mb-8">Validasi kesiapan parameter penjadwalan sebelum menjalankan Algoritma Genetika V2.</p>

            <!-- Alerts -->
            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-950 text-sm backdrop-blur-md">
                    <p class="font-bold">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- PARAMETERS SUMMARY GRID -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white/70 border border-white/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mata Pelajaran</span>
                    <span class="text-emerald-700 font-black text-xl mt-1 block">{{ $selectedSubjects->count() }} Terpilih</span>
                    <span class="text-[10px] text-gray-500 block mt-1">Siap disebar merata</span>
                </div>

                <div class="bg-white/70 border border-white/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Kapasitas Sesi Ruang</span>
                    <span class="text-emerald-700 font-black text-xl mt-1 block">{{ $totalCapacity }} Kursi</span>
                    <span class="text-[10px] text-gray-500 block mt-1">Dari {{ $selectedRooms->count() }} ruangan aktif</span>
                </div>

                <div class="bg-white/70 border border-white/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Sesi Waktu Aktif</span>
                    <span class="text-emerald-700 font-black text-xl mt-1 block">{{ $timeSessionsCount }} Sesi / Hari</span>
                    <span class="text-[10px] text-gray-500 block mt-1">Sesi: {{ implode(', ', $selectedTimes->pluck('name')->toArray()) }}</span>
                </div>
            </div>

            <!-- CAPACITY FORMULA EXPLANATION -->
            <div class="bg-emerald-50/50 border border-emerald-100 p-6 rounded-[1.5rem] shadow-inner mb-8">
                <h4 class="text-emerald-900 font-extrabold text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Kalkulasi Kapasitas Efektif
                </h4>
                <div class="text-xs text-emerald-800 space-y-2">
                    <p>Formula penentuan daya tampung maksimal penjadwalan ujian per tingkatan kelas:</p>
                    <div class="p-3 bg-white/80 rounded-xl font-mono text-emerald-950 font-bold border border-emerald-100/50 flex justify-between items-center text-sm">
                        <span>Kapasitas_Efektif = Total_Kapasitas_Ruang × Jumlah_Sesi_Waktu</span>
                        <span class="text-emerald-700 font-black text-base">{{ $totalCapacity }} × {{ $timeSessionsCount }} = {{ $effectiveCapacity }} Kursi</span>
                    </div>
                </div>
            </div>

            <!-- PRE-FLIGHT VALIDATION CHECKLIST -->
            <div class="space-y-4 mb-8">
                <h4 class="text-emerald-900 font-extrabold text-sm uppercase tracking-wider ml-1">Checklist Validasi Tingkatan</h4>
                
                <div class="space-y-2">
                    @foreach($levelStats as $stat)
                    <div class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-300 {{ $stat['passed'] ? 'bg-emerald-50/30 border-emerald-100 text-emerald-950 shadow-sm' : 'bg-red-50 border-red-100 text-red-950 shadow-md' }}">
                        <div class="flex items-center gap-3">
                            @if($stat['passed'])
                                <div class="p-1 rounded-full bg-emerald-100 text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @else
                                <div class="p-1 rounded-full bg-red-100 text-red-600 animate-pulse">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                            @endif
                            <div>
                                <span class="font-extrabold block text-sm">{{ $stat['name'] }}</span>
                                <span class="text-xs opacity-75">Jumlah Siswa Terdaftar: <strong>{{ $stat['count'] }} Siswa</strong></span>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($stat['passed'])
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider">Lolos Validasi</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-black uppercase tracking-wider">Kapasitas Kurang</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- RESULT MESSAGE & ACTION TRIGGER -->
            <div class="pt-6 border-t border-emerald-900/10 flex flex-col md:flex-row justify-between items-center gap-4">
                @if($preFlightPassed)
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-full text-emerald-600 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-emerald-900 text-sm">Validasi Berhasil!</p>
                            <p class="text-xs text-emerald-700/60 font-semibold">Semua tingkatan kelas lolos pre-flight check. Algoritma Genetika V2 siap dijalankan.</p>
                        </div>
                    </div>

                    <form action="{{ route('wizard.execute', $session->id) }}" method="POST" class="w-full md:w-auto">
                        @csrf
                        <button type="submit" class="w-full md:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 active:scale-95">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mulai Penjadwalan Genetika V2
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 rounded-full text-red-600 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-red-900 text-sm">Validasi Gagal!</p>
                            <p class="text-xs text-red-700/60 font-semibold">Terdapat tingkatan kelas yang melebihi kapasitas efektif. Tambahkan ruangan/sesi waktu, atau kurangi data siswa.</p>
                        </div>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <a href="{{ route('wizard.step3', $session->id) }}" class="w-full md:w-auto px-6 py-4 rounded-2xl bg-white border border-gray-200 text-center text-gray-700 font-bold hover:bg-gray-50 hover:shadow transition-all">
                            &larr; Sesuaikan Ruang & Waktu
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
