<x-app-layout>
    <x-slot name="header">
        Wizard: Distribusi Siswa
    </x-slot>

    <!-- PROGRESS BAR -->
    <div class="mb-8 max-w-7xl mx-auto">
        <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
            <span>Langkah 3: Atur Tempat Duduk</span>
            <span class="opacity-50">Finalisasi</span>
        </div>
        <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
            <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)]" style="width: 100%"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
        
        <!-- KOLOM KIRI: PANEL KONTROL & STATISTIK -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- 1. Statistik Skema Siswa (Visual Card) -->
            <div class="bg-gradient-to-br from-emerald-800 to-teal-900 rounded-[2rem] p-6 shadow-2xl text-white relative overflow-hidden group hover:shadow-emerald-500/20 transition-all duration-500">
                <!-- Dekorasi -->
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-lime-400/20 rounded-full blur-2xl group-hover:bg-lime-400/30 transition-all duration-500"></div>
                <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-emerald-400/10 rounded-full blur-xl group-hover:bg-emerald-400/20 transition-all duration-500"></div>
                
                <h4 class="text-xs font-bold uppercase tracking-widest opacity-70 mb-4 border-b border-white/10 pb-2 flex justify-between items-center">
                    Status Distribusi
                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </h4>
                
                <div class="grid grid-cols-2 gap-4 text-center relative z-10">
                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <p class="text-3xl font-black text-lime-400">{{ $allocatedStudents }}</p>
                        <p class="text-[10px] uppercase tracking-wide opacity-80 mt-1">Sudah Dapat Ruang</p>
                    </div>
                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                        <p class="text-3xl font-black text-white">{{ $unallocatedCount }}</p>
                        <p class="text-[10px] uppercase tracking-wide opacity-80 mt-1">Belum Dapat Ruang</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-white/10 text-center relative z-10">
                    <p class="text-xs opacity-60 font-medium">Total Siswa Terdaftar: <span class="text-white">{{ $totalStudents }}</span></p>
                </div>
            </div>

            <!-- 2. Form Distribusi -->
            <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl p-6 sticky top-6 overflow-hidden">
                 <!-- Dekorasi Background -->
                 <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-400/10 to-transparent rounded-bl-full -mr-4 -mt-4 pointer-events-none"></div>

                <h3 class="text-lg font-bold text-emerald-900 mb-6 border-b border-emerald-900/10 pb-4 flex items-center gap-2">
                    <div class="p-1.5 bg-emerald-100 rounded-lg text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg></div>
                    Isi Ruangan
                </h3>
                
                <!-- Flash Message -->
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-100/80 text-emerald-800 text-xs border border-emerald-200 shadow-sm font-bold flex items-center gap-2 animate-pulse">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-4 p-3 rounded-xl bg-yellow-100/80 text-yellow-800 text-xs border border-yellow-200 shadow-sm font-bold">
                        {{ session('warning') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-3 rounded-xl bg-red-100/80 text-red-800 text-xs border border-red-200 shadow-sm font-bold">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('wizard.step3', $session->id) }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <!-- Pilih Ruangan -->
                    <div class="group">
                        <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Target Ruangan</label>
                        <div class="relative">
                            <select name="room_id" class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 shadow-inner py-2.5 pl-4 pr-10 text-sm appearance-none cursor-pointer font-semibold text-emerald-900 transition-all hover:bg-white/80" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    @php $sisa = $room->capacity - $room->allocations_count; @endphp
                                    <option value="{{ $room->id }}" {{ $sisa <= 0 ? 'disabled' : '' }} class="py-1">
                                        {{ $room->name }} (Sisa Kursi: {{ $sisa }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Kelas (UPDATED) -->
                    <div class="group">
                        <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Ambil Dari Kelas</label>
                        <div class="relative">
                            <select name="source_class" class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 shadow-inner py-2.5 pl-4 pr-10 text-sm appearance-none cursor-pointer font-semibold text-emerald-900 transition-all hover:bg-white/80" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classesData as $item)
                                    <option value="{{ $item->class }}">
                                        {{ $item->class }} (Sisa: {{ $item->remaining }} Siswa)
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-emerald-600/70 mt-1.5 ml-1 font-medium italic bg-emerald-50/50 px-2 py-1 rounded-lg inline-block border border-emerald-100/50">*Hanya kelas dengan siswa belum terdaftar yang muncul.</p>
                    </div>

                    <!-- Jumlah -->
                    <div class="group">
                        <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Jumlah Siswa</label>
                        <div class="relative">
                            <input type="number" name="limit" 
                                   class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 shadow-inner py-2.5 pl-4 text-sm font-bold text-emerald-900 placeholder-emerald-900/30 transition-all hover:bg-white/80" 
                                   placeholder="Cth: 20" min="1" required>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-600 pointer-events-none">SISWA</div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:shadow-emerald-500/40 hover:scale-[1.02] active:scale-95 transition-all mt-4 flex justify-center items-center gap-2 group">
                        <span class="bg-white/20 p-1 rounded-full group-hover:bg-white/30 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></span>
                        Proses Masukkan
                    </button>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: MONITORING STATUS -->
        <div class="lg:col-span-2">
            <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl p-6 min-h-[600px] relative overflow-hidden">
                
                <!-- Header Kanan -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4 border-b border-emerald-900/10 pb-6 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-emerald-900">Status Ruangan</h3>
                        <p class="text-xs text-emerald-800/60 mt-1">Pantau keterisian ruangan secara real-time.</p>
                    </div>
                    <a href="{{ route('print.all', $session->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-white hover:bg-emerald-50 text-emerald-800 text-xs font-bold shadow-sm hover:shadow border border-emerald-100 flex items-center gap-2 transition-all group">
                        <svg class="w-4 h-4 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Semua Kartu
                    </a>
                </div>

                <!-- Grid Ruangan -->
                <div class="grid grid-cols-1 gap-5 relative z-10">
                    @foreach($rooms as $room)
                    <div class="bg-white/60 p-5 rounded-[1.5rem] border border-white/60 shadow-sm hover:shadow-lg hover:bg-white/80 transition-all relative overflow-hidden group">
                        
                        @php
                            $percent = $room->capacity > 0 ? ($room->allocations_count / $room->capacity) * 100 : 0;
                            // Warna Progress Bar Dinamis
                            $barColor = $percent >= 100 ? 'bg-gradient-to-r from-red-500 to-pink-500' : ($percent > 80 ? 'bg-gradient-to-r from-yellow-400 to-orange-500' : 'bg-gradient-to-r from-lime-400 to-emerald-500');
                            $textColor = $percent >= 100 ? 'text-red-600 bg-red-100 border-red-200' : 'text-emerald-700 bg-emerald-100/50 border-emerald-200';
                        @endphp
                        
                        <div class="flex justify-between items-start relative z-10 mb-3">
                            <div>
                                <h4 class="font-bold text-lg text-emerald-900 flex items-center gap-2">
                                    {{ $room->name }}
                                    @if($percent >= 100)
                                        <span class="text-[10px] font-extrabold text-red-600 bg-red-100 px-2 py-0.5 rounded-full border border-red-200 animate-pulse">PENUH</span>
                                    @endif
                                </h4>
                                <div class="flex items-center gap-2 text-xs font-bold mt-1.5">
                                    <span class="{{ $textColor }} px-2.5 py-1 rounded-lg border transition-colors">
                                        Terisi: {{ $room->allocations_count }} / {{ $room->capacity }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2 opacity-100 sm:opacity-60 sm:group-hover:opacity-100 transition-all transform sm:translate-x-4 sm:group-hover:translate-x-0">
                                <!-- Tombol Cetak -->
                                <a href="{{ route('print.room', [$session->id, $room->id]) }}" target="_blank" class="p-2.5 rounded-xl bg-white text-emerald-600 hover:bg-emerald-500 hover:text-white shadow-sm hover:shadow border border-emerald-100 transition-all group/btn" title="Cetak Ruang Ini">
                                    <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                
                                <!-- Tombol Panggil Kembali (Reset) -->
                                <form action="{{ route('wizard.resetRoom', [$session->id, $room->id]) }}" method="POST" onsubmit="return confirm('PERHATIAN: Anda akan mengosongkan ruangan ini.\n\nSemua siswa di ruangan ini akan dikembalikan ke status BELUM DAPAT RUANG dan bisa didistribusikan ulang.\n\nLanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white shadow-sm hover:shadow border border-red-100 transition-all flex items-center gap-2 group/reset" title="Panggil Kembali Siswa (Reset)">
                                        <svg class="w-4 h-4 group-hover/reset:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <span class="text-xs font-bold hidden xl:inline">Reset</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Bar Visual Latar -->
                        <div class="w-full bg-emerald-900/5 rounded-full h-3 mt-3 overflow-hidden border border-white/50 relative">
                            <div class="h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(0,0,0,0.1)] {{ $barColor }}" style="width: {{ $percent }}%">
                                <div class="absolute top-0 left-0 bottom-0 right-0 bg-[linear-gradient(45deg,rgba(255,255,255,0.2)_25%,transparent_25%,transparent_50%,rgba(255,255,255,0.2)_50%,rgba(255,255,255,0.2)_75%,transparent_75%,transparent)] bg-[length:1rem_1rem] opacity-30"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Tombol Selesai -->
                <div class="mt-12 text-center relative z-10">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl bg-emerald-800 text-white font-bold hover:bg-emerald-900 hover:scale-105 transition-all shadow-lg group">
                        <span>Selesai & Kembali ke Dashboard</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>