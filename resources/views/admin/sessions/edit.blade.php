<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Sesi Ujian') }}
    </x-slot>

    <div class="max-w-4xl mx-auto">
        
        <div class="mb-6">
            <a href="{{ route('sessions.index') }}" class="inline-flex items-center text-sm font-bold text-emerald-800 hover:text-emerald-600 transition-colors bg-white/40 px-4 py-2 rounded-full backdrop-blur-sm border border-white/30 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
                    
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-400/20 to-transparent rounded-bl-full -mr-4 -mt-4 pointer-events-none"></div>

                    <h3 class="text-xl font-bold text-emerald-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Informasi Dasar
                    </h3>

                    <form action="{{ route('sessions.update', $session->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Nama Kegiatan Ujian</label>
                            <input type="text" name="title" value="{{ old('title', $session->title) }}" 
                                   class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-3 px-4 transition-all" 
                                   placeholder="Contoh: UAS Ganjil 2025" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Tanggal Pelaksanaan</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $session->start_date) }}" 
                                   class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-3 px-4 transition-all" required>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:shadow-emerald-500/40 hover:scale-[1.02] transition-all flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                
                <h4 class="text-sm font-extrabold text-emerald-800 uppercase tracking-widest ml-2">Kelola Data Terkait</h4>

                <a href="{{ route('wizard.step2', $session->id) }}" class="group block bg-white/40 backdrop-blur-lg border border-white/50 rounded-[1.5rem] p-5 shadow-lg hover:bg-white/60 hover:-translate-y-1 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-xl bg-lime-100 text-lime-600 group-hover:bg-lime-500 group-hover:text-white transition-colors shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-emerald-900">Ruangan Ujian</h5>
                            <p class="text-xs text-emerald-800/70 mt-1">Tambah/Hapus ruangan & kapasitas.</p>
                            <div class="mt-3 text-xs font-bold text-lime-700 bg-lime-400/20 px-2 py-1 rounded-lg inline-block border border-lime-400/30">
                                {{ $session->rooms()->count() }} Ruangan
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('sessions.schedules.index', $session->id) }}" class="group block bg-white/40 backdrop-blur-lg border border-white/50 rounded-[1.5rem] p-5 shadow-lg hover:bg-white/60 hover:-translate-y-1 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-emerald-900">Jadwal Mapel</h5>
                            <p class="text-xs text-emerald-800/70 mt-1">Atur mata pelajaran & jam ujian.</p>
                            <div class="mt-3 text-xs font-bold text-blue-700 bg-blue-400/20 px-2 py-1 rounded-lg inline-block border border-blue-400/30">
                                {{ $session->schedules()->count() }} Mapel
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('wizard.step3', $session->id) }}" class="group block bg-white/40 backdrop-blur-lg border border-white/50 rounded-[1.5rem] p-5 shadow-lg hover:bg-white/60 hover:-translate-y-1 transition-all">
                    <div class="flex items-start gap-4">
                        <div class="p-3 rounded-xl bg-teal-100 text-teal-600 group-hover:bg-teal-500 group-hover:text-white transition-colors shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h5 class="font-bold text-emerald-900">Distribusi Siswa</h5>
                            <p class="text-xs text-emerald-800/70 mt-1">Cek penempatan kursi & cetak kartu.</p>
                            <div class="mt-3 text-xs font-bold text-teal-700 bg-teal-400/20 px-2 py-1 rounded-lg inline-block border border-teal-400/30">
                                {{ $session->allocations()->count() }} Terdaftar
                            </div>
                        </div>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>