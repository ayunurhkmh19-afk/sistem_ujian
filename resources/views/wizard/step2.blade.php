<x-app-layout>
    <x-slot name="header">
        Wizard: Setup Ruangan
    </x-slot>

    <!-- CUSTOM STYLE UNTUK SCROLL & DROPDOWN -->
    <style>
        /* Scrollbar Halus */
        .custom-scroll::-webkit-scrollbar { width: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.3); border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.6); }
        
        /* Animasi Dropdown */
        .dropdown-enter { animation: slideDown 0.2s ease-out forwards; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>

    <div class="max-w-4xl mx-auto">
        
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 2: Ruangan</span>
                <span class="opacity-50">Sesi: {{ $session->title }}</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 66%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 mb-8 relative overflow-visible">
            
            <!-- Header & Tombol Template -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 relative z-20">
                <h3 class="text-xl font-extrabold text-emerald-900 flex items-center gap-2">
                    <div class="p-2 bg-white/50 rounded-xl shadow-sm"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                    Input Ruangan Ujian
                </h3>

                <!-- DROPDOWN MASTER ROOM (PREMIUM STYLE) -->
                @if($masterRooms->isNotEmpty())
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" type="button" class="px-4 py-2.5 rounded-xl bg-white/80 text-emerald-800 text-xs font-bold shadow-sm hover:bg-white hover:shadow-md transition-all flex items-center gap-2 border border-white/60 group">
                        <div class="bg-emerald-100 p-1 rounded-md group-hover:bg-emerald-200 transition"><svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg></div>
                        Pilih dari Master Ruangan
                        <svg class="w-3 h-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         class="absolute right-0 mt-3 w-72 bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] border border-white/60 py-2 z-50 dropdown-enter ring-1 ring-black/5" 
                         style="display: none;">
                        
                        <div class="px-4 py-2.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 flex justify-between items-center">
                            <span>Bank Ruangan</span>
                            <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[9px]">{{ $masterRooms->count() }} Tersedia</span>
                        </div>
                        
                        <div class="max-h-60 overflow-y-auto custom-scroll p-1 space-y-1">
                            @foreach($masterRooms as $tpl)
                                <button type="button" 
                                        @click="$dispatch('add-template', {id: {{ $tpl->id }}, name: '{{ $tpl->name }}', capacity: {{ $tpl->capacity }}}); open = false"
                                        class="w-full text-left px-3 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 transition flex justify-between items-center group">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-300 group-hover:bg-emerald-500 transition"></div>
                                        <span class="font-medium truncate max-w-[150px]">{{ $tpl->name }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold bg-gray-100 group-hover:bg-emerald-200/50 group-hover:text-emerald-700 px-2 py-0.5 rounded-md transition text-gray-500 border border-gray-200 group-hover:border-emerald-200">
                                        {{ $tpl->capacity }} Kursi
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        <div class="px-3 py-2 border-t border-gray-100 text-center">
                            <a href="{{ route('master_rooms.index') }}" target="_blank" class="text-[10px] text-emerald-600 font-bold hover:underline">Kelola Master Ruangan &rarr;</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- ALPINE DATA & FORM -->
            <div x-data="{ 
                rooms: {{ $rooms->isNotEmpty() ? Js::from($rooms) : "[{id: null, master_room_id: null, name: '', capacity: 20}]" }}, 
                
                addRoom() { this.rooms.push({id: null, master_room_id: null, name: '', capacity: 20}); },
                
                addFromTemplate(data) {
                    let lastIdx = this.rooms.length - 1;
                    // Jika baris terakhir kosong, isi baris tersebut. Jika tidak, tambah baru.
                    if (this.rooms[lastIdx].name === '') {
                        this.rooms[lastIdx].master_room_id = data.id;
                        this.rooms[lastIdx].name = data.name;
                        this.rooms[lastIdx].capacity = data.capacity;
                    } else {
                        // Cek duplikasi jika perlu, tapi biarkan dulu
                        this.rooms.push({id: null, master_room_id: data.id, name: data.name, capacity: data.capacity});
                    }
                },

                removeRoom(index) { 
                    if(this.rooms.length > 1) {
                        if(this.rooms[index].id && !confirm('Hapus ruangan ini?')) return;
                        this.rooms.splice(index, 1); 
                    }
                }
            }" @add-template.window="addFromTemplate($event.detail)">
                
                <form action="{{ route('wizard.step2', $session->id) }}" method="POST">
                    @csrf
                    
                    <!-- Repeater Item -->
                    <template x-for="(room, index) in rooms" :key="index">
                        <div class="relative group mb-4 transition-all duration-300 hover:-translate-y-1">
                            <!-- Background Card -->
                            <div class="absolute inset-0 bg-white/40 rounded-2xl border border-white/50 shadow-sm group-hover:shadow-md group-hover:bg-white/60 transition-all"></div>
                            
                            <div class="relative flex flex-col sm:flex-row gap-4 p-4 items-end">
                                <input type="hidden" :name="'rooms['+index+'][id]'" x-model="room.id">
                                <input type="hidden" :name="'rooms['+index+'][master_room_id]'" x-model="room.master_room_id">

                                <!-- Input Nama Ruangan -->
                                <div class="flex-1 w-full group/input">
                                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 transition-colors group-hover/input:text-emerald-600">Nama Ruangan</label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-600/50 transition-colors group-focus-within/input:text-emerald-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                                        <input type="text" :name="'rooms['+index+'][name]'" x-model="room.name" 
                                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-transparent bg-white/50 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 text-sm font-bold text-emerald-900 placeholder-emerald-900/30 shadow-inner transition-all" 
                                               placeholder="Cth: Lab Komputer 1" required>
                                    </div>
                                </div>

                                <!-- Input Kapasitas -->
                                <div class="w-full sm:w-36 group/input">
                                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 transition-colors group-hover/input:text-emerald-600">Kapasitas</label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-600/50 transition-colors group-focus-within/input:text-emerald-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                                        <input type="number" :name="'rooms['+index+'][capacity]'" x-model="room.capacity" 
                                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border-transparent bg-white/50 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 text-sm font-bold text-emerald-900 text-center shadow-inner transition-all" 
                                               min="1" required>
                                    </div>
                                </div>

                                <!-- Tombol Hapus -->
                                <div class="pb-0.5">
                                    <button type="button" @click="removeRoom(index)" class="p-3 rounded-xl bg-white/50 text-red-400 hover:bg-red-50 hover:text-red-600 border border-transparent hover:border-red-100 shadow-sm hover:shadow transition-all group/btn" title="Hapus Baris">
                                        <svg class="w-5 h-5 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Tombol Tambah Manual -->
                    <button type="button" @click="addRoom()" class="mt-6 mb-8 w-full py-3 rounded-2xl border-2 border-dashed border-emerald-300/50 text-emerald-700 font-bold hover:bg-emerald-50/50 hover:border-emerald-400 hover:text-emerald-900 transition-all flex items-center justify-center gap-2 group">
                        <div class="bg-emerald-100 p-1 rounded-full group-hover:bg-emerald-200 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                        Tambah Baris Manual
                    </button>

                    <!-- Footer Actions -->
                    <div class="border-t border-emerald-900/10 pt-6 flex justify-between items-center">
                        <div class="hidden sm:flex items-center gap-2 text-xs text-emerald-800/60 bg-white/40 px-3 py-1.5 rounded-full border border-white/40 shadow-sm">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pastikan kapasitas sesuai kursi fisik.
                        </div>
                        
                        <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                            Simpan & Lanjut
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>