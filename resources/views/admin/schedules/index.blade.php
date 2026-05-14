@php
    // Persiapkan data di PHP agar bersih di JavaScript
    $eventsData = $schedulesList->map(function($s) {
        return [
            'id' => $s->id,
            'subject_name' => $s->subject_name,
            'exam_date' => $s->exam_date->format('Y-m-d'),
            'start_time' => $s->start_time->format('H:i'),
            'end_time' => $s->end_time->format('H:i')
        ];
    })->values()->all();

    // Tentukan tanggal awal (Bisa dari sesi atau hari ini)
    $initialDate = $session->start_date ? \Carbon\Carbon::parse($session->start_date) : now();
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
                    <!-- Dropdown Bulan -->
                    <select x-model.number="month" class="bg-transparent border-none text-emerald-900 font-extrabold text-base focus:ring-0 cursor-pointer py-1 pr-8 text-center appearance-none">
                        <template x-for="(m, index) in monthNames" :key="index">
                            <option :value="index" x-text="m"></option>
                        </template>
                    </select>

                    <span class="text-emerald-300">|</span>

                    <!-- Dropdown Tahun -->
                    <select x-model.number="year" class="bg-transparent border-none text-emerald-900 font-extrabold text-base focus:ring-0 cursor-pointer py-1 pr-8 text-center appearance-none">
                        <template x-for="y in yearsList" :key="y">
                            <option :value="y" x-text="y"></option>
                        </template>
                    </select>
                </div>

                <button @click="changeMonth(1)" class="p-2 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>

            <!-- Tombol Tambah -->
            <button @click="openModal()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                <span>+ Buat Jadwal</span>
            </button>
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
                                    <div @click="editEvent(event)" class="bg-white/60 border border-white/60 rounded-2xl p-3 shadow-sm hover:bg-white cursor-pointer transition-all group relative">
                                        <div class="flex justify-between">
                                            <h5 class="font-bold text-emerald-900 text-sm" x-text="event.subject_name"></h5>
                                            <button @click.stop="deleteEvent(event.id)" class="text-red-300 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                        </div>
                                        <div class="mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-100/50 px-2 py-0.5 rounded inline-block">
                                            <span x-text="event.start_time + ' - ' + event.end_time"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div x-show="events.length === 0" class="text-center py-10 opacity-50 text-sm font-bold text-emerald-900">Belum ada jadwal.</div>
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
                        <div class="calendar-day p-2 cursor-pointer relative group" 
                             :class="{'is-today': dayObj.isToday}" 
                             @click="selectDate(dayObj.day)">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-bold w-7 h-7 flex items-center justify-center rounded-full" 
                                      :class="dayObj.isToday ? 'bg-emerald-600 text-white shadow-md' : 'text-emerald-800 group-hover:bg-white/60'" 
                                      x-text="dayObj.day"></span>
                                <button class="opacity-0 group-hover:opacity-100 transition text-emerald-500 hover:bg-emerald-100 rounded-full p-0.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                            </div>
                            <div class="flex-1 overflow-y-auto custom-scroll space-y-1">
                                <template x-for="evt in dayObj.events">
                                    <div class="cal-event" @click.stop="editEvent(evt)" :title="evt.subject_name">
                                        <span x-text="evt.start_time"></span> <span x-text="evt.subject_name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 3. MODAL POP-UP (SIMPEL & CLEAN) -->
        <div x-show="isModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center px-4" style="display: none;">
            <div class="fixed inset-0 bg-emerald-950/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
            
            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl relative z-10 overflow-hidden transform transition-all border border-white/50">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 flex justify-between items-center text-white">
                    <h3 class="text-xl font-bold" x-text="isEditMode ? 'Edit Jadwal' : 'Jadwal Baru'"></h3>
                    <button @click="closeModal()" class="hover:bg-white/20 p-1 rounded-full transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <form @submit.prevent="saveData" class="p-6 space-y-5">
                    <div>
                        <label class="text-xs font-bold text-emerald-800 uppercase tracking-wider ml-1">Mata Pelajaran</label>
                        <input type="text" x-model="form.subject_name" class="w-full mt-1 rounded-xl border-gray-200 bg-emerald-50/30 focus:ring-emerald-500 focus:border-emerald-500 font-bold" required placeholder="Contoh: Biologi">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-emerald-800 uppercase tracking-wider ml-1">Tanggal</label>
                        <input type="date" x-model="form.exam_date" class="w-full mt-1 rounded-xl border-gray-200 bg-emerald-50/30 focus:ring-emerald-500 focus:border-emerald-500 font-medium" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-emerald-800 uppercase tracking-wider ml-1">Mulai</label>
                            <input type="time" x-model="form.start_time" class="w-full mt-1 rounded-xl border-gray-200 bg-emerald-50/30 focus:ring-emerald-500 focus:border-emerald-500 text-center" required>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-emerald-800 uppercase tracking-wider ml-1">Selesai</label>
                            <input type="time" x-model="form.end_time" class="w-full mt-1 rounded-xl border-gray-200 bg-emerald-50/30 focus:ring-emerald-500 focus:border-emerald-500 text-center" required>
                        </div>
                    </div>
                    <button type="submit" :disabled="isLoading" class="w-full py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg transition disabled:opacity-50">
                        <span x-show="!isLoading">Simpan</span><span x-show="isLoading">Memproses...</span>
                    </button>
                </form>
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
                isModalOpen: false, isEditMode: false, isLoading: false, selectedId: null,
                form: { subject_name: '', exam_date: '', start_time: '08:00', end_time: '10:00' },
                toasts: [],

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
                openModal() { this.isEditMode=false; this.resetForm(); this.isModalOpen=true; },
                closeModal() { this.isModalOpen=false; },
                
                selectDate(day) {
                    let m = String(this.month + 1).padStart(2, '0');
                    let d = String(day).padStart(2, '0');
                    this.resetForm();
                    this.form.exam_date = `${this.year}-${m}-${d}`;
                    this.isModalOpen = true;
                },
                editEvent(evt) {
                    this.selectedId = evt.id;
                    this.form = { ...evt };
                    this.isEditMode = true;
                    this.isModalOpen = true;
                },
                saveData() {
                    this.isLoading = true;
                    let url = this.isEditMode ? `/sessions/${sessionId}/schedules/${this.selectedId}` : `/sessions/${sessionId}/schedules`;
                    let method = this.isEditMode ? 'PUT' : 'POST';
                    fetch(url, {
                        method: method,
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(this.form)
                    }).then(res => res.json()).then(() => { location.reload(); }).catch(() => { this.isLoading = false; alert('Gagal menyimpan'); });
                },
                deleteEvent(id) {
                    if(!confirm('Hapus?')) return;
                    fetch(`/sessions/${sessionId}/schedules/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => location.reload());
                },
                resetForm() { this.form = { subject_name: '', exam_date: '', start_time: '08:00', end_time: '10:00' }; }
            }
        }
    </script>
</x-app-layout>