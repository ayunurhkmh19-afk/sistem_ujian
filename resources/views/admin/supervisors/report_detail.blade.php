<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Berita Acara & Absensi') }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back button -->
        <a href="{{ route('sessions.supervisors.index', $schedule->exam_session_id) }}" class="inline-flex items-center gap-2 text-emerald-800 hover:text-emerald-950 font-bold text-xs bg-white/40 border border-white/60 hover:bg-white hover:shadow px-4 py-2.5 rounded-xl backdrop-blur-sm transition-all">
            &larr; Kembali ke Matriks Pengawas
        </a>

        <!-- Main Info Card -->
        <div class="bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <div class="flex justify-between items-start border-b border-emerald-900/10 pb-6 mb-6">
                <div>
                    <h3 class="text-2xl font-extrabold text-emerald-900 leading-tight mb-2">Detail Laporan Ujian</h3>
                    <p class="text-xs font-semibold text-emerald-800/60 uppercase tracking-widest">{{ $schedule->session->title }}</p>
                </div>
                <span class="px-4 py-2 rounded-full border text-xs font-black uppercase tracking-wider {{ $report && $report->status === 'Submitted' ? 'bg-emerald-100 border-emerald-200 text-emerald-800' : 'bg-amber-100 border-amber-200 text-amber-800' }}">
                    {{ $report ? $report->status : 'Belum Ada Laporan' }}
                </span>
            </div>

            <!-- Metadata Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Mata Pelajaran</span>
                    <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $schedule->subject->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Ruangan</span>
                    <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $room->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Hari & Sesi</span>
                    <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $schedule->exam_date->format('d M Y') }} ({{ $schedule->timeSession->name }})</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Dilaporkan Oleh</span>
                    <span class="text-sm font-bold text-gray-800 block mt-0.5">{{ $report && $report->user ? $report->user->name : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Berita Acara & Attendance Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Berita Acara (1 col) -->
            <div class="bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2rem] shadow-2xl p-6 relative overflow-hidden h-fit">
                <h4 class="text-sm font-extrabold text-emerald-900 uppercase tracking-wider pb-3 border-b border-emerald-900/10 mb-4">Berita Acara Ujian</h4>

                @if($report)
                    <div class="space-y-4 text-xs font-semibold text-gray-700">
                        <div class="flex justify-between p-2 bg-emerald-50/50 rounded-lg border border-emerald-100/30">
                            <span>Siswa Hadir:</span>
                            <span class="font-extrabold text-emerald-700 text-sm">{{ $report->total_present }}</span>
                        </div>
                        <div class="flex justify-between p-2 bg-red-50/50 rounded-lg border border-red-100/30">
                            <span>Siswa Absen:</span>
                            <span class="font-extrabold text-red-700 text-sm">{{ $report->total_absent }}</span>
                        </div>
                        <div class="pt-3 border-t border-emerald-900/5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Catatan Kejadian:</span>
                            <p class="p-3 bg-white border border-gray-100 rounded-xl text-gray-600 font-medium italic min-h-[80px]">
                                {{ $report->incident_notes ?? 'Tidak ada kejadian khusus.' }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 text-gray-400 italic text-sm">
                        Laporan Berita Acara belum dibuat oleh pengawas ruangan.
                    </div>
                @endif
            </div>

            <!-- Right: Absensi Siswa (2 cols) -->
            <div class="md:col-span-2 bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2rem] shadow-2xl p-6 relative overflow-hidden">
                <h4 class="text-sm font-extrabold text-emerald-900 uppercase tracking-wider pb-3 border-b border-emerald-900/10 mb-4 flex justify-between items-center">
                    <span>Daftar Kehadiran Siswa</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                        {{ $attendances->count() }} Siswa Terdaftar
                    </span>
                </h4>

                @if($attendances->count() > 0)
                    <div class="overflow-x-auto max-h-[400px] overflow-y-auto pr-1">
                        <table class="w-full text-left text-xs font-semibold">
                            <thead class="bg-emerald-50/50 border border-emerald-100 text-emerald-800 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-2 rounded-l-lg">NIS</th>
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2 rounded-r-lg text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-900/5">
                                @foreach($attendances as $attendance)
                                    <tr>
                                        <td class="px-4 py-3 text-emerald-900 font-bold font-mono">{{ $attendance->student->nis }}</td>
                                        <td class="px-4 py-3 text-gray-800">{{ $attendance->student->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                                                {{ $attendance->status === 'Hadir' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}
                                                {{ $attendance->status === 'Sakit' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                                                {{ $attendance->status === 'Izin' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                                                {{ $attendance->status === 'Alpa' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                            ">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-20 text-gray-400 italic text-sm">
                        Belum ada data kehadiran siswa yang direkam.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
