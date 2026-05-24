<x-app-layout>
    <x-slot name="header">
        {{ __('Matriks Penugasan Pengawas') }}
    </x-slot>

    <!-- MD3 + Glassmorphism Container -->
    <div class="bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Toolbar / Header -->
        <div class="p-8 border-b border-emerald-900/10 relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white/30">
            <div>
                <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Jadwal Kepengawasan</h3>
                <p class="text-emerald-800/60 text-sm">Sesi Ujian: <span class="font-bold text-emerald-800">{{ $session->title }}</span> ({{ Carbon\Carbon::parse($session->start_date)->format('d M Y') }} s/d {{ Carbon\Carbon::parse($session->end_date)->format('d M Y') }})</p>
            </div>
            <span class="px-4 py-2 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-black uppercase tracking-wider">
                Total Jadwal: {{ $schedules->count() }} Slot
            </span>
        </div>

        <!-- Alerts -->
        @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $key => $color)
            @if(session($key))
                <div class="mx-8 mt-6 p-4 rounded-2xl bg-{{ $color }}-50/80 border border-{{ $color }}-100 text-{{ $color }}-900 flex items-start gap-4 shadow-sm backdrop-blur-md" x-data="{ show: true }" x-show="show">
                    <div class="p-2 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 shrink-0">
                        @if($key === 'error')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="py-1">
                        <p class="font-bold">{{ ucfirst($key) }}</p>
                        <p class="text-sm opacity-90">{{ session($key) }}</p>
                    </div>
                    <button @click="show = false" class="ml-auto p-2 rounded-full hover:bg-{{ $color }}-200/50 text-{{ $color }}-700 transition">&times;</button>
                </div>
            @endif
        @endforeach

        <!-- Matrix Content -->
        <div class="p-8 relative z-10">
            @if($schedules->count() > 0)
                <div class="space-y-8">
                    @php
                        // Group schedules by date and then by time session name
                        $groupedSchedules = $schedules->groupBy(function($item) {
                            return $item->exam_date->format('Y-m-d');
                        });
                    @endphp

                    @foreach($groupedSchedules as $date => $dateSchedules)
                        <div class="bg-white/40 border border-white/80 rounded-[2rem] p-6 shadow-sm">
                            <!-- Date Header -->
                            <div class="flex items-center gap-3 pb-4 border-b border-emerald-900/10 mb-6">
                                <div class="p-3 bg-emerald-600 rounded-2xl text-white shadow-lg shadow-emerald-600/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-emerald-950">{{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</h4>
                                    <p class="text-xs text-emerald-700/60 font-semibold">Terdapat {{ $dateSchedules->count() }} jadwal ujian</p>
                                </div>
                            </div>

                            <!-- List Schedules for this date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($dateSchedules as $schedule)
                                    <div class="bg-white/80 p-5 rounded-[1.5rem] border border-emerald-100/30 shadow-inner flex flex-col justify-between">
                                        <!-- Schedule Top Info -->
                                        <div class="mb-4">
                                            <div class="flex justify-between items-start gap-2 mb-2">
                                                <span class="px-2.5 py-0.5 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-wider">
                                                    {{ $schedule->timeSession->name }} ({{ substr($schedule->timeSession->start_time, 0, 5) }} - {{ substr($schedule->timeSession->end_time, 0, 5) }})
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase tracking-wider">
                                                    {{ $schedule->subject->level->name }}
                                                </span>
                                            </div>
                                            <h5 class="text-base font-bold text-gray-800 leading-tight">{{ $schedule->subject->name }}</h5>
                                        </div>

                                        <!-- Rooms & Multi-select Supervisors -->
                                        <div class="space-y-4 pt-4 border-t border-emerald-900/5">
                                            <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Daftar Ruangan & Pengawas</span>
                                            
                                            @php
                                                // Extract active rooms from allocations
                                                $activeRoomIds = $schedule->allocations->pluck('room_id')->unique();
                                                $activeRooms = App\Models\Room::whereIn('id', $activeRoomIds)->get();
                                            @endphp

                                            @if($activeRooms->count() > 0)
                                                @foreach($activeRooms as $room)
                                                    <div class="p-3.5 bg-gray-50/80 border border-gray-100/50 rounded-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-3 hover:bg-white hover:shadow transition duration-200">
                                                        <div class="flex-shrink-0">
                                                            <span class="font-bold text-gray-800 text-sm block">{{ $room->name }}</span>
                                                            <span class="text-[10px] text-gray-400 font-semibold block">Terisi: {{ $schedule->allocations->where('room_id', $room->id)->count() }} / {{ $room->capacity }} Siswa</span>
                                                        </div>

                                                        <!-- Assignment Form (Multi-select style) -->
                                                        <form action="{{ route('supervisors.assign') }}" method="POST" class="w-full md:w-2/3 flex items-center gap-2" x-data="{ openDropdown: false, selected: @js($schedule->roomSupervisors->where('room_id', $room->id)->pluck('user_id')->toArray()) }">
                                                            @csrf
                                                            <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">
                                                            <input type="hidden" name="room_id" value="{{ $room->id }}">

                                                            <!-- Dropdown Multi-select simulator -->
                                                            <div class="relative w-full">
                                                                <button type="button" @click="openDropdown = !openDropdown" class="w-full bg-white border border-gray-200 px-4 py-2.5 rounded-xl text-left text-xs font-semibold text-gray-700 flex justify-between items-center hover:bg-gray-50 hover:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-inner">
                                                                    <span class="truncate" x-text="selected.length === 0 ? 'Pilih Pengawas...' : selected.length + ' Pengawas Terpilih'"></span>
                                                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                                </button>

                                                                <!-- Dropdown popover -->
                                                                <div x-show="openDropdown" @click.away="openDropdown = false" class="absolute left-0 right-0 mt-1 max-h-[160px] overflow-y-auto bg-white border border-gray-100 shadow-xl rounded-xl z-50 p-2 space-y-1 scrollbar-hide" style="display: none;">
                                                                    @foreach($supervisors as $supervisor)
                                                                        <label class="flex items-center gap-2 p-2 hover:bg-emerald-50/50 rounded-lg cursor-pointer transition">
                                                                            <input type="checkbox" name="user_ids[]" value="{{ $supervisor->id }}"
                                                                                   x-model="selected"
                                                                                   :value="{{ $supervisor->id }}"
                                                                                   class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-gray-300">
                                                                            <span class="text-xs font-bold text-gray-700 leading-none">{{ $supervisor->name }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            <button type="submit" class="p-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg shadow-emerald-600/10 hover:scale-105 active:scale-95 transition-all" title="Simpan Kepengawasan">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            </button>

                                                            <!-- Indikator Berita Acara (Submitted/Draft/None) -->
                                                            @php
                                                                $report = \App\Models\ExamReport::where('exam_schedule_id', $schedule->id)->where('room_id', $room->id)->first();
                                                            @endphp
                                                            @if($report)
                                                                <a href="{{ route('schedules.report.show', ['schedule' => $schedule->id, 'room' => $room->id]) }}" 
                                                                   class="p-2.5 rounded-xl border flex items-center justify-center transition-all duration-200 {{ $report->status === 'Submitted' ? 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-100' : 'bg-amber-50 border-amber-200 text-amber-600 hover:bg-amber-100' }}" 
                                                                   title="Lihat Berita Acara ({{ $report->status }})">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                </a>
                                                            @else
                                                                <div class="p-2.5 rounded-xl border bg-gray-50 border-gray-100 text-gray-400 flex items-center justify-center cursor-not-allowed" title="Berita Acara Belum Ada">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                </div>
                                                            @endif
                                                        </form>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-xs text-gray-400 italic block">Tidak ada ruangan aktif/alokasi</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 text-center opacity-60">
                    <div class="p-6 rounded-full bg-gray-50 mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-600">Belum ada Jadwal Ujian</h3>
                    <p class="text-gray-500 mt-1 max-w-sm">Jalankan penjadwalan via Wizard terlebih dahulu untuk menyusun jadwal dan alokasi ruangan siswa.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
