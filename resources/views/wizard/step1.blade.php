<x-app-layout>
    <x-slot name="header">
        Wizard: Buat Ujian Baru
    </x-slot>

    <!-- STYLE CUSTOM -->
    <style>
        .popover-calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-top: 8px; }
        .popover-day { width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; border-radius: 9999px; font-size: 0.875rem; cursor: pointer; transition: all 0.2s; color: #374151; }
        .popover-day:hover { background-color: #d1fae5; color: #065f46; }
        .popover-day.is-selected { background-color: #10b981; color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4); }
        .popover-day.is-today { border: 1px solid #10b981; font-weight: bold; }
    </style>

    <div class="max-w-4xl mx-auto">
        
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 1: Data Utama</span>
                <span class="opacity-50">Langkah 3: Selesai</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 33%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-visible" x-data="datePickerApp()">
            
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Buat Sesi Ujian Baru</h3>
            <p class="text-emerald-800/60 text-sm mb-8">Silakan isi detail sesi ujian dan upload data siswa.</p>

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

            <form action="{{ route('wizard.step1') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Input Judul -->
                <div class="group">
                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Nama Kegiatan Ujian <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600/50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></div>
                        <input type="text" name="title" 
                               class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 font-bold placeholder-emerald-900/30 shadow-inner transition-all" 
                               placeholder="Contoh: UAS Semester Ganjil 2025" required>
                    </div>
                </div>

                <!-- Input Tanggal (FIXED POPUP) -->
                <div class="group relative">
                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">Tanggal Mulai <span class="text-red-500">*</span></label>
                    
                    <!-- Trigger Input -->
                    <div class="relative cursor-pointer" @click="showPicker = !showPicker" @click.away="showPicker = false">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600/50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                        
                        <!-- Input Tampilan (Readonly) -->
                        <input type="text" x-model="formattedDate" class="w-full pl-12 pr-4 py-3.5 rounded-2xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 font-bold placeholder-emerald-900/30 shadow-inner transition-all cursor-pointer" readonly placeholder="Pilih Tanggal">
                        
                        <!-- Input Hidden (Value ke Backend) -->
                        <input type="hidden" name="start_date" x-model="selectedDate"> 

                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-emerald-400"><svg class="w-4 h-4 transform transition-transform" :class="showPicker ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
                    </div>

                    <!-- Popover Kalender (FIXED CLICK PROPAGATION) -->
                    <div x-show="showPicker" 
                         @click.stop
                         class="absolute top-full left-0 mt-2 w-full md:w-80 bg-white border border-gray-100 shadow-[0_10px_40px_rgba(0,0,0,0.15)] rounded-2xl z-50 p-4 transform origin-top"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         style="display: none;">
                        
                        <!-- Header Navigasi -->
                        <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                            <button type="button" @click.stop="prevMonth()" class="p-1.5 hover:bg-emerald-50 rounded-lg text-emerald-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                            <span class="text-sm font-bold text-emerald-900" x-text="monthNames[month] + ' ' + year"></span>
                            <button type="button" @click.stop="nextMonth()" class="p-1.5 hover:bg-emerald-50 rounded-lg text-emerald-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                        </div>
                        
                        <!-- Grid Hari -->
                        <div class="grid grid-cols-7 text-center mb-2">
                            <template x-for="day in ['Mn','Sn','Sl','Rb','Km','Jm','Sb']">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider" x-text="day"></span>
                            </template>
                        </div>
                        
                        <!-- Grid Tanggal -->
                        <div class="grid grid-cols-7 gap-1">
                            <template x-for="blank in blanks"><div></div></template>
                            <template x-for="day in days" :key="day">
                                <div @click.stop="selectDate(day)" 
                                     class="text-sm w-8 h-8 flex items-center justify-center rounded-full cursor-pointer hover:bg-emerald-100 transition font-medium mx-auto"
                                     :class="isSelected(day) ? '!bg-emerald-500 !text-white shadow-md' : 'text-gray-600'"
                                     x-text="day">
                                </div>
                            </template>
                        </div>
                        
                        <!-- Footer Hari Ini -->
                        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                            <button type="button" @click.stop="gotoToday()" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:underline">Hari Ini</button>
                        </div>
                    </div>
                </div>

                <!-- Input File -->
                <div class="group">
                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">
                        Upload Data Siswa (Excel .xlsx) <span class="text-[9px] font-normal bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded ml-2">Opsional</span>
                    </label>
                    <div class="p-8 rounded-2xl border-2 border-dashed border-emerald-300/50 bg-white/40 hover:bg-white/60 hover:border-emerald-400 transition-all text-center relative cursor-pointer overflow-hidden group-hover:shadow-lg group-hover:shadow-emerald-500/10">
                        <input type="file" name="file_siswa" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="p-3 bg-emerald-100/50 rounded-full mb-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-emerald-900">Klik atau seret file ke sini</p>
                            <p class="text-xs text-emerald-700/60 mt-1">Format: .xlsx, .xls (Max 2MB)</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-emerald-600/60 mt-2 ml-1 italic">*Jika dikosongkan, sesi akan menggunakan data siswa yang sudah ada di sistem.</p>
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

    <!-- LOGIKA SCRIPT FIXED -->
    <script>
        function datePickerApp() {
            return {
                showPicker: false,
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                days: [],
                blanks: [],
                selectedDate: '', // YYYY-MM-DD
                formattedDate: '', // Tampilan
                monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'],

                init() {
                    this.calculateDays();
                },

                calculateDays() {
                    // Mendapatkan hari pertama dalam bulan (0=Minggu, 1=Senin, dst)
                    let firstDayOfMonth = new Date(this.year, this.month, 1).getDay();
                    // Jumlah hari dalam bulan
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    
                    this.blanks = Array.from({ length: firstDayOfMonth });
                    this.days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
                },

                prevMonth() {
                    if (this.month === 0) { this.month = 11; this.year--; } 
                    else { this.month--; }
                    this.calculateDays();
                },

                nextMonth() {
                    if (this.month === 11) { this.month = 0; this.year++; } 
                    else { this.month++; }
                    this.calculateDays();
                },

                selectDate(day) {
                    let mm = String(this.month + 1).padStart(2, '0');
                    let dd = String(day).padStart(2, '0');
                    let yyyy = this.year;
                    
                    this.selectedDate = `${yyyy}-${mm}-${dd}`;
                    this.formattedDate = `${day} ${this.monthNames[this.month]} ${yyyy}`;
                    this.showPicker = false;
                },
                
                gotoToday() {
                    let today = new Date();
                    this.month = today.getMonth();
                    this.year = today.getFullYear();
                    this.selectDate(today.getDate());
                    this.calculateDays();
                },

                isSelected(day) {
                    if (!this.selectedDate) return false;
                    let mm = String(this.month + 1).padStart(2, '0');
                    let dd = String(day).padStart(2, '0');
                    let currentStr = `${this.year}-${mm}-${dd}`;
                    return this.selectedDate === currentStr;
                }
            }
        }
    </script>
</x-app-layout>