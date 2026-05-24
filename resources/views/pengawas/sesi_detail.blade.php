<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengawas.dashboard') }}" class="p-2 bg-white/60 rounded-xl hover:bg-white transition-colors">
                <svg class="w-5 h-5 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            {{ __('Detail Sesi Ujian') }}
        </div>
    </x-slot>

    <!-- Info Sesi Card -->
    <div class="mb-8 p-8 rounded-[2rem] bg-white/40 backdrop-blur-xl border border-white/50 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
        
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-1">
                <p class="text-xs font-bold text-emerald-800/60 uppercase tracking-widest">Sesi Kegiatan</p>
                <p class="text-xl font-black text-emerald-900 leading-tight">{{ $schedule->session->title }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-emerald-800/60 uppercase tracking-widest">Mata Pelajaran & Ruang</p>
                <p class="text-xl font-black text-emerald-900 leading-tight">
                    {{ $schedule->subject->name }} ({{ $room->name }})
                </p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-emerald-800/60 uppercase tracking-widest">Tanggal & Waktu</p>
                <p class="text-xl font-black text-emerald-900">
                    {{ $schedule->exam_date->translatedFormat('d F Y') }} <br>
                    <span class="text-emerald-700/70 text-sm">
                        {{ substr($schedule->timeSession->start_time, 0, 5) }} - {{ substr($schedule->timeSession->end_time, 0, 5) }} ({{ $schedule->timeSession->name }})
                    </span>
                </p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-emerald-800/60 uppercase tracking-widest">Status Penguncian</p>
                @if($isLocked)
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-red-100 text-red-800 font-bold border border-red-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                        TERKUNCI
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-lime-100 text-lime-800 font-bold border border-lime-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2V7a5 5 0 00-5-5zM7 7a3 3 0 016 0v2H7V7z"></path></svg>
                        TERBUKA
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if($isLocked)
        <div class="mb-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center gap-4 text-red-800 animate-pulse">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <p class="font-bold text-sm">Data telah dikunci karena Berita Acara sudah disubmit. Perubahan absensi tidak dapat dilakukan lagi.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tabel Absensi (Left 2/3) -->
        <div class="lg:col-span-2">
            <form action="{{ route('pengawas.absensi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-white/20 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-emerald-900">Daftar Kehadiran Siswa</h3>
                        @if(!$isLocked)
                            <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all hover:scale-105 active:scale-95">
                                Simpan Absensi Sementara
                            </button>
                        @endif
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-emerald-900/5 text-emerald-900/70 text-xs uppercase tracking-wider font-bold">
                                <tr>
                                    <th class="px-6 py-4">Nama Siswa</th>
                                    <th class="px-6 py-4 text-center">Meja</th>
                                    <th class="px-6 py-4 text-center">Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/20">
                                @foreach($alokasiSiswa as $alokasi)
                                    @php 
                                        $status = isset($kehadiran[$alokasi->student_id]) ? $kehadiran[$alokasi->student_id]->status : 'Alpa'; 
                                    @endphp
                                    <tr class="hover:bg-white/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-emerald-900">{{ $alokasi->student->name }}</div>
                                            <div class="text-[10px] text-emerald-700/60 uppercase tracking-widest">{{ $alokasi->student->nis }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-emerald-900">
                                            <div class="inline-block px-3 py-1 bg-white/60 rounded-lg shadow-inner border border-white/40">
                                                {{ str_pad($alokasi->desk_number, 2, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                @foreach(['Hadir', 'Sakit', 'Izin', 'Alpa'] as $opt)
                                                    <label class="relative group cursor-pointer">
                                                        <input type="radio" 
                                                               name="attendances[{{ $alokasi->student_id }}]" 
                                                               value="{{ $opt }}" 
                                                               {{ $status == $opt ? 'checked' : '' }}
                                                               {{ $isLocked ? 'disabled' : '' }}
                                                               class="peer sr-only">
                                                        <div class="px-3 py-1.5 text-[10px] font-black rounded-lg border border-emerald-900/10 bg-white/40 text-emerald-900/40 peer-checked:bg-emerald-900 peer-checked:text-white peer-checked:border-emerald-900 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed transition-all uppercase tracking-widest group-hover:scale-105">
                                                            {{ $opt }}
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>

        <!-- Berita Acara (Right 1/3) -->
        <div class="lg:col-span-1">
            <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-xl p-8 sticky top-8">
                <h3 class="text-xl font-black text-emerald-900 mb-6 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    Berita Acara
                </h3>

                <form action="{{ route('pengawas.report.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-emerald-800/60 uppercase tracking-widest mb-2">Catatan Kejadian Selama Ujian</label>
                        <textarea name="incident_notes" 
                                  rows="6" 
                                  {{ $isLocked ? 'disabled' : '' }}
                                  class="w-full bg-white/60 border-white/40 rounded-2xl text-emerald-900 text-sm focus:ring-emerald-500 focus:border-emerald-500 placeholder-emerald-800/30"
                                  placeholder="Contoh: Ujian berjalan lancar, atau Siswa A terlambat 10 menit...">{{ $laporan->incident_notes ?? '' }}</textarea>
                    </div>

                    @if(!$isLocked)
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin mensubmit Berita Acara? Tindakan ini akan MENGUNCI data absensi dan tidak dapat diubah lagi.')"
                                class="w-full py-4 bg-emerald-900 text-white font-black rounded-2xl shadow-xl hover:bg-black transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z"></path></svg>
                            SUBMIT & KUNCI
                        </button>
                    @else
                        <div class="p-6 rounded-2xl bg-emerald-900 text-white text-center shadow-inner">
                            <p class="text-xs font-bold uppercase tracking-widest mb-1 opacity-70">Status Laporan</p>
                            <p class="text-xl font-black">SUDAH DISUBMIT</p>
                        </div>
                    @endif
                </form>

                <!-- Rekap Data (Server Side Calculated when locked) -->
                @if($laporan)
                    <div class="mt-8 pt-8 border-t border-emerald-900/10 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-emerald-800/60 font-bold uppercase tracking-wider">Hadir</span>
                            <span class="font-black text-emerald-900">{{ $laporan->total_present }} Siswa</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-emerald-800/60 font-bold uppercase tracking-wider">Tidak Hadir</span>
                            <span class="font-black text-red-600">{{ $laporan->total_absent }} Siswa</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
