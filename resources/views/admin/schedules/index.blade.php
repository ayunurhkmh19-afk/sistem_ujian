@php
    // Persiapkan data di PHP agar bersih di JavaScript
    $eventsData = $schedulesList->map(function($s) {
        // Group allocations by room to show room details
        $roomsData = $s->allocations->groupBy('room_id')->map(function($allocs, $roomId) use ($s) {
            $room = $allocs->first()?->room;
            $roomName = $room?->name ?? 'Ruangan Tidak Diketahui';
            
            // Get supervisors for this room and schedule
            $supervisors = $s->roomSupervisors->where('room_id', $roomId)->map(function($rs) {
                return $rs->user?->name;
            })->filter()->unique()->values()->all();
            
            // Get unique classes in this room
            $classes = $allocs->map(function($a) {
                return $a->student?->studentClass?->name;
            })->filter()->unique()->values()->all();
            
            return [
                'room_id' => $roomId,
                'room_name' => $roomName,
                'capacity' => $room?->capacity ?? 0,
                'allocated_count' => $allocs->count(),
                'supervisors' => $supervisors,
                'classes' => $classes,
            ];
        })->values()->all();

        return [
            'id' => $s->id,
            'subject_name' => $s->subject?->name ?? '-',
            'subject_code' => $s->subject?->code ?? '-',
            'exam_date' => $s->exam_date->format('Y-m-d'),
            'exam_date_formatted' => $s->exam_date->translatedFormat('l, d F Y'),
            'time_session_name' => $s->timeSession?->name ?? '-',
            'start_time' => $s->timeSession ? substr($s->timeSession->start_time, 0, 5) : '00:00',
            'end_time' => $s->timeSession ? substr($s->timeSession->end_time, 0, 5) : '00:00',
            'rooms' => $roomsData,
        ];
    })->values()->all();

    // Selalu mengacu pada tanggal, bulan, dan tahun terkini (Hari Ini)
    $initialDate = now();
    $initialYear = $initialDate->year;
    $initialMonth = $initialDate->month - 1; // JS Month is 0-indexed
@endphp

<x-app-layout>
    <x-slot name="header">
        Jadwal: {{ $session->title }}
    </x-slot>

    <style>
        /* Grid Kalender */
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 1.5rem; overflow: hidden; }
        .calendar-day { background: rgba(255, 255, 255, 0.25); min-height: 120px; backdrop-filter: blur(5px); transition: all 0.2s ease; display: flex; flex-direction: column; }
        .calendar-day:hover { background: rgba(255, 255, 255, 0.5); }
        .calendar-day.is-today { background: rgba(209, 250, 229, 0.8); }
        .cal-event { font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; margin-bottom: 2px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-left: 2px solid #a7f3d0; transition: transform 0.1s; }
        .cal-event:hover { transform: scale(1.05); z-index: 10; }
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 10px; }
        .timeline-item::before { content: ''; position: absolute; left: 19px; top: 36px; bottom: -20px; width: 2px; background: rgba(16, 185, 129, 0.3); z-index: 0; }
        .timeline-item:last-child::before { display: none; }
    </style>

    <!-- Main App Container -->
    <div x-data="calendarApp({{ $session->id }}, {{ Js::from($eventsData) }}, {{ $initialMonth }}, {{ $initialYear }})" 
         class="pb-10 relative">

        <!-- TOAST NOTIFICATION -->
        <div class="fixed top-24 right-5 z-[100] space-y-2 pointer-events-none">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="toast.show" x-transition.duration.300ms class="flex items-center w-full max-w-xs p-4 bg-white rounded-xl shadow-2xl border border-emerald-100 pointer-events-auto">
                    <div class="text-emerald-500 bg-emerald-100 rounded-lg p-2 mr-3"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg></div>
                    <div class="text-sm font-bold text-emerald-900" x-text="toast.message"></div>
                </div>
            </template>
        </div>

        <!-- 1. HEADER FILTER (SIMPEL & REAKTIF) -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] p-4 shadow-lg z-20 relative">
            
            <div class="flex gap-2">
                <a href="{{ route('sessions.index') }}" class="px-4 py-2 rounded-xl bg-white/60 hover:bg-white text-emerald-900 font-bold text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Kembali
                </a>
                <button @click="goToToday()" class="px-4 py-2 rounded-xl bg-white/60 hover:bg-white text-emerald-900 font-bold text-sm shadow-sm transition-all border border-white/40">
                    Hari Ini
                </button>
            </div>

            <!-- Filter Tengah (Direct x-model) -->
            <div class="flex items-center bg-white/50 backdrop-blur-md rounded-2xl p-1.5 shadow-inner border border-white/30">
                <button @click="changeMonth(-1)" class="p-2 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                
                <div class="flex items-center px-2 gap-2">
                    <!-- Dropdown Bulan (Blade Loop - Sesuai Standar HTML) -->
                    <select x-model.number="month" class="bg-transparent border-none text-emerald-900 font-extrabold text-base focus:ring-0 cursor-pointer py-1 pr-8 text-center appearance-none">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $index => $m)
                            <option value="{{ $index }}">{{ $m }}</option>
                        @endforeach
                    </select>

                    <span class="text-emerald-300">|</span>

                    <!-- Dropdown Tahun (Blade Loop - Sesuai Standar HTML) -->
                    <select x-model.number="year" class="bg-transparent border-none text-emerald-900 font-extrabold text-base focus:ring-0 cursor-pointer py-1 pr-8 text-center appearance-none">
                        @php
                            $currentYear = now()->year;
                        @endphp
                        @for($y = $currentYear - 5; $y <= $currentYear + 5; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <button @click="changeMonth(1)" class="p-2 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>

            <!-- Sisi Kanan Header: Info Status / Wizard Link -->
            <div class="flex gap-2">
                <a href="{{ route('sessions.edit', $session->id) }}" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.178 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.3c-.773-.57-.375-1.81.587-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z"></path></svg>
                    <span>Pengaturan Sesi Ujian</span>
                </a>
            </div>
        </div>

        <!-- 2. KONTEN UTAMA -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- LIST (KIRI) -->
            <div class="lg:col-span-4 bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2.5rem] p-6 shadow-xl h-[700px] flex flex-col overflow-hidden">
                <div class="mb-4 pb-4 border-b border-emerald-900/10 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-emerald-900 flex items-center gap-2"><span class="w-2 h-6 bg-lime-500 rounded-full"></span> Agenda</h3>
                    <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-2 py-1 rounded-lg" x-text="events.length + ' Item'"></span>
                </div>
                <div class="flex-1 overflow-y-auto custom-scroll space-y-6 pr-2">
                    <template x-for="(group, dateKey) in groupedEvents" :key="dateKey">
                        <div class="pl-4 relative">
                            <div class="sticky top-0 bg-white/80 backdrop-blur-md z-10 py-2 mb-2 -ml-4 px-4 border-b border-emerald-100 rounded-b-xl shadow-sm">
                                <span class="text-sm font-extrabold text-emerald-800 uppercase tracking-wider" x-text="formatDateLong(dateKey)"></span>
                            </div>
                            <div class="space-y-3 border-l-2 border-emerald-200 pl-4 ml-1">
                                <template x-for="event in group" :key="event.id">
                                    <div @click="showDetail(event)" class="bg-white/60 border border-white/60 rounded-2xl p-3 shadow-sm hover:bg-white cursor-pointer transition-all group relative">
                                        <div class="flex justify-between items-center">
                                            <h5 class="font-bold text-emerald-900 text-sm" x-text="event.subject_name"></h5>
                                            <span class="text-[9px] font-black text-emerald-800/40" x-text="event.subject_code"></span>
                                        </div>
                                        <div class="mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-100/50 px-2 py-0.5 rounded inline-block">
                                            <span x-text="event.start_time + ' - ' + event.end_time"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div x-show="events.length === 0" class="text-center py-10 opacity-50 text-sm font-bold text-emerald-900">Belum ada jadwal. Silakan jalankan Algoritma Genetika di menu Wizard.</div>
                </div>
            </div>

            <!-- KALENDER (KANAN) -->
            <div class="lg:col-span-8 bg-white/30 backdrop-blur-lg border border-white/40 rounded-[2.5rem] p-6 md:p-8 shadow-2xl h-[700px] flex flex-col">
                <div class="grid grid-cols-7 mb-2 text-center">
                    <template x-for="day in ['MIN','SEN','SEL','RAB','KAM','JUM','SAB']"><div class="text-[10px] font-black text-emerald-800 opacity-60 py-2" x-text="day"></div></template>
                </div>
                <div class="calendar-grid flex-1">
                    <template x-for="blank in blanks"><div class="calendar-day bg-transparent !border-none !backdrop-filter-none"></div></template>
                    <template x-for="dayObj in daysInMonth" :key="dayObj.date">
                        <div class="calendar-day p-2 relative group" 
                             :class="{'is-today': dayObj.isToday}">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-bold w-7 h-7 flex items-center justify-center rounded-full" 
                                      :class="dayObj.isToday ? 'bg-emerald-600 text-white shadow-md' : 'text-emerald-800'" 
                                      x-text="dayObj.day"></span>
                            </div>
                            <div class="flex-1 overflow-y-auto custom-scroll space-y-1">
                                <template x-for="evt in dayObj.events">
                                    <div class="cal-event" @click.stop="showDetail(evt)" :title="evt.subject_name">
                                        <span x-text="evt.start_time"></span> <span x-text="evt.subject_name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 3. MODAL POP-UP DETAIL JADWAL (GLASSMORPHIC & PREMIUM) -->
        <div x-show="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center px-4" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="fixed inset-0 bg-emerald-950/80 backdrop-blur-md transition-opacity" @click="closeModal()"></div>
            
            <div class="bg-white/90 backdrop-blur-2xl w-full max-w-lg rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden transform transition-all border border-white/50 max-h-[90vh] flex flex-col">
                <div class="bg-gradient-to-r from-emerald-800 to-teal-800 p-6 flex justify-between items-center text-white">
                    <div class="flex items-center gap-2">
                        <span class="p-2 bg-white/20 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></span>
                        <h3 class="text-xl font-black">Detail Jadwal Ujian</h3>
                    </div>
                    <button @click="closeModal()" class="hover:bg-white/20 p-2 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="p-6 space-y-6 overflow-y-auto custom-scroll flex-1">
                    <!-- Detail Mapel & Waktu -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100">
                            <span class="text-[10px] font-black text-emerald-800 uppercase tracking-widest block mb-1">Mata Pelajaran</span>
                            <div class="font-extrabold text-emerald-900 text-base" x-text="form.subject_name"></div>
                            <div class="text-xs font-bold text-emerald-700/60" x-text="'Kode: ' + (form.subject_code || '-')"></div>
                        </div>
                        <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100">
                            <span class="text-[10px] font-black text-emerald-800 uppercase tracking-widest block mb-1">Sesi Waktu</span>
                            <div class="font-extrabold text-emerald-900 text-base" x-text="form.time_session_name"></div>
                            <div class="text-xs font-bold text-emerald-700/60" x-text="form.start_time + ' - ' + form.end_time"></div>
                        </div>
                    </div>

                    <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100">
                        <span class="text-[10px] font-black text-emerald-800 uppercase tracking-widest block mb-1">Tanggal Ujian</span>
                        <div class="font-black text-emerald-900 text-base" x-text="form.exam_date_formatted"></div>
                    </div>

                    <!-- Detail Ruangan & Pengawas -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-black text-emerald-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-teal-500 rounded-full"></span> Ruangan & Pengawas
                        </h4>
                        
                        <div class="space-y-3">
                            <template x-for="room in form.rooms" :key="room.room_id">
                                <div class="bg-white/60 border border-emerald-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition duration-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-black text-emerald-900 text-sm" x-text="room.room_name"></div>
                                        <span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-lg" x-text="room.allocated_count + ' / ' + room.capacity + ' Kursi'"></span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3 pt-3 border-t border-emerald-100/50">
                                        <div>
                                            <span class="text-[9px] font-bold text-emerald-700/60 uppercase block mb-1">Pengawas</span>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="supervisor in room.supervisors" :key="supervisor">
                                                    <span class="text-xs font-bold text-emerald-900 bg-white border border-emerald-100 px-2 py-1 rounded-lg shadow-sm" x-text="supervisor"></span>
                                                </template>
                                                <span x-show="!room.supervisors || room.supervisors.length === 0" class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">Belum ada pengawas</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-emerald-700/60 uppercase block mb-1">Kelas Peserta</span>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="cls in room.classes" :key="cls">
                                                    <span class="text-xs font-extrabold text-teal-900 bg-teal-50 px-2 py-0.5 rounded-lg" x-text="cls"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="!form.rooms || form.rooms.length === 0" class="text-center py-6 text-sm font-bold text-emerald-900/40 bg-emerald-50/20 rounded-2xl border border-dashed border-emerald-200">
                                Belum ada alokasi ruangan. Silakan jalankan Algoritma Genetika di menu Wizard.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-emerald-50/50 border-t border-emerald-100 flex justify-end">
                    <button @click="closeModal()" class="px-6 py-2.5 bg-emerald-800 text-white font-extrabold rounded-xl hover:bg-emerald-900 transition shadow-md">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- JAVASCRIPT LOGIC SEDERHANA -->
    <script>
        function calendarApp(sessionId, initialEvents, initMonth, initYear) {
            return {
                // State
                month: initMonth, // 0-11
                year: initYear,
                events: initialEvents,
                
                // Data
                monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],
                daysInMonth: [], blanks: [], yearsList: [], groupedEvents: {},

                // Modal
                isModalOpen: false, selectedId: null,
                form: { subject_name: '', subject_code: '', exam_date: '', exam_date_formatted: '', time_session_name: '', start_time: '08:00', end_time: '10:00', rooms: [] },

                init() {
                    // Generate Tahun (Relative)
                    const currentYear = new Date().getFullYear();
                    for(let i = currentYear - 5; i <= currentYear + 5; i++) this.yearsList.push(i);
                    
                    // Reactive: Hitung ulang jika month/year berubah
                    this.$watch('month', () => this.calculateDays());
                    this.$watch('year', () => this.calculateDays());

                    this.groupEvents();
                    this.calculateDays();
                },

                calculateDays() {
                    let firstDay = new Date(this.year, this.month, 1).getDay();
                    let daysCount = new Date(this.year, this.month + 1, 0).getDate();
                    
                    this.blanks = Array.from({ length: firstDay });
                    this.daysInMonth = Array.from({ length: daysCount }, (_, i) => {
                        let day = i + 1;
                        let dateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        return {
                            day: day,
                            date: dateStr,
                            isToday: this.isDateToday(dateStr),
                            events: this.events.filter(e => e.exam_date === dateStr).sort((a,b) => a.start_time.localeCompare(b.start_time))
                        };
                    });
                },

                groupEvents() {
                    this.events.sort((a, b) => {
                        if (a.exam_date !== b.exam_date) return a.exam_date.localeCompare(b.exam_date);
                        return a.start_time.localeCompare(b.start_time);
                    });
                    this.groupedEvents = this.events.reduce((groups, event) => {
                        if (!groups[event.exam_date]) groups[event.exam_date] = [];
                        groups[event.exam_date].push(event);
                        return groups;
                    }, {});
                },

                // Navigasi
                changeMonth(step) {
                    let newMonth = this.month + step;
                    if (newMonth > 11) { this.month = 0; this.year++; }
                    else if (newMonth < 0) { this.month = 11; this.year--; }
                    else { this.month = newMonth; }
                },
                goToToday() {
                    let t = new Date(); this.month = t.getMonth(); this.year = t.getFullYear();
                },

                // Helpers
                isDateToday(dStr) { return dStr === new Date().toISOString().split('T')[0]; },
                formatDateLong(dStr) { return new Date(dStr).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' }); },

                // Actions
                closeModal() { this.isModalOpen = false; },
                showDetail(evt) {
                    this.selectedId = evt.id;
                    this.form = { ...evt };
                    this.isModalOpen = true;
                }
            }
        }
    </script>
</x-app-layout>