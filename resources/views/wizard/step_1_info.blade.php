<x-app-layout>
    <x-slot name="header">
        Wizard: Buat Ujian Baru
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- PROGRESS BAR (5 Steps) -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 1: Informasi Sesi</span>
                <span class="opacity-40">Progres: 20%</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 20%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Informasi Sesi Ujian</h3>
            <p class="text-emerald-800/60 text-sm mb-8">Langkah awal untuk memulai penjadwalan ujian. Masukkan detail informasi sesi ujian.</p>

            <!-- Error Handling -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-100/80 border border-red-200 text-red-800 text-sm backdrop-blur-sm shadow-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('wizard.storeStep1') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Input Judul -->
                <div class="group">
                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Nama Sesi Ujian <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <input type="text" name="title" 
                               class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 font-bold placeholder-emerald-900/30 shadow-inner transition-all" 
                               placeholder="Contoh: Ujian Akhir Semester Genap 2026" required value="{{ old('title') }}">
                    </div>
                </div>

                <!-- Input Tanggal (Start & End) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="date" name="start_date" 
                                   class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 font-bold placeholder-emerald-900/30 shadow-inner transition-all" 
                                   required value="{{ old('start_date') }}">
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="date" name="end_date" 
                                   class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 font-bold placeholder-emerald-900/30 shadow-inner transition-all" 
                                   required value="{{ old('end_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 flex justify-between items-center border-t border-emerald-900/10">
                    <p class="text-xs text-emerald-800/50 font-medium">*) Wajib diisi</p>
                    <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        Simpan & Lanjut
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
