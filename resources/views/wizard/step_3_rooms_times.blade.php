<x-app-layout>
    <x-slot name="header">
        Wizard: Setup Ruangan & Sesi Waktu
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 3: Ruangan & Waktu</span>
                <span class="opacity-40">Progres: 60%</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 60%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Pilih Ruangan & Sesi Waktu</h3>
                    <p class="text-emerald-800/60 text-sm">Aktifkan ruangan default dan tentukan sesi waktu per hari untuk sesi ujian.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('master_rooms.index') }}" target="_blank" class="px-4 py-2 rounded-xl bg-white/60 border border-white/80 hover:bg-white text-emerald-800 font-bold text-xs shadow-sm transition-all">
                        Kelola Ruangan Master &rarr;
                    </a>
                    <a href="{{ route('time_sessions.index') }}" target="_blank" class="px-4 py-2 rounded-xl bg-white/60 border border-white/80 hover:bg-white text-emerald-800 font-bold text-xs shadow-sm transition-all">
                        Kelola Sesi Master &rarr;
                    </a>
                </div>
            </div>

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

            <form action="{{ route('wizard.storeStep3', $session->id) }}" method="POST" class="space-y-8" x-data="{ selectedRooms: @js($selectedRoomIds), selectedTimes: @js($selectedTimeSessionIds) }">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Column 1: Ruangan Checklist -->
                    <div class="bg-white/60 border border-white/85 p-6 rounded-[1.5rem] shadow-sm flex flex-col">
                        <div class="flex justify-between items-center pb-3 border-b border-emerald-900/10 mb-4">
                            <h4 class="font-extrabold text-emerald-900 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Pilih Ruangan Ujian
                            </h4>
                            <button type="button" 
                                    @click="
                                        let ids = {{ json_encode($rooms->pluck('id')->toArray()) }};
                                        let allSelected = ids.every(id => selectedRooms.includes(id));
                                        if (allSelected) {
                                            selectedRooms = [];
                                        } else {
                                            selectedRooms = ids;
                                        }
                                    "
                                    class="text-[10px] font-bold text-emerald-700 hover:text-emerald-950 transition-colors uppercase tracking-wider">
                                Toggle Semua
                            </button>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto max-h-[300px] pr-2 scrollbar-thin scrollbar-thumb-emerald-100">
                            @if($rooms->count() > 0)
                                @foreach($rooms as $room)
                                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-emerald-50/50 cursor-pointer transition-colors border border-transparent hover:border-emerald-100/50">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="rooms[]" value="{{ $room->id }}"
                                               x-model="selectedRooms"
                                               :value="{{ $room->id }}"
                                               class="rounded-lg border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-5 h-5 transition">
                                        <span class="text-sm font-bold text-emerald-900 leading-tight">{{ $room->name }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-black">
                                        {{ $room->capacity }} Kursi
                                    </span>
                                </label>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center py-10 text-center opacity-60">
                                    <span class="text-xs text-gray-500 italic">Belum ada ruangan master</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Column 2: Sesi Waktu Checklist -->
                    <div class="bg-white/60 border border-white/85 p-6 rounded-[1.5rem] shadow-sm flex flex-col">
                        <div class="flex justify-between items-center pb-3 border-b border-emerald-900/10 mb-4">
                            <h4 class="font-extrabold text-emerald-900 text-base flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pilih Sesi Waktu
                            </h4>
                            <button type="button" 
                                    @click="
                                        let ids = {{ json_encode($timeSessions->pluck('id')->toArray()) }};
                                        let allSelected = ids.every(id => selectedTimes.includes(id));
                                        if (allSelected) {
                                            selectedTimes = [];
                                        } else {
                                            selectedTimes = ids;
                                        }
                                    "
                                    class="text-[10px] font-bold text-emerald-700 hover:text-emerald-950 transition-colors uppercase tracking-wider">
                                Toggle Semua
                            </button>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto max-h-[300px] pr-2 scrollbar-thin scrollbar-thumb-emerald-100">
                            @if($timeSessions->count() > 0)
                                @foreach($timeSessions as $ts)
                                <label class="flex items-center justify-between p-3 rounded-xl hover:bg-emerald-50/50 cursor-pointer transition-colors border border-transparent hover:border-emerald-100/50">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="time_sessions[]" value="{{ $ts->id }}"
                                               x-model="selectedTimes"
                                               :value="{{ $ts->id }}"
                                               class="rounded-lg border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-5 h-5 transition">
                                        <span class="text-sm font-bold text-emerald-900 leading-tight">{{ $ts->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 font-extrabold text-[10px]">
                                            {{ substr($ts->start_time, 0, 5) }}
                                        </span>
                                        <span class="text-gray-400 font-bold text-[8px]">S/D</span>
                                        <span class="px-2 py-0.5 rounded-lg bg-teal-50 border border-teal-100 text-teal-700 font-extrabold text-[10px]">
                                            {{ substr($ts->end_time, 0, 5) }}
                                        </span>
                                    </div>
                                </label>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center py-10 text-center opacity-60">
                                    <span class="text-xs text-gray-500 italic">Belum ada sesi waktu master</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 flex justify-between items-center border-t border-emerald-900/10">
                    <div class="flex gap-4">
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-100/50 px-3.5 py-2 rounded-xl">
                            Ruangan Terpilih: <span class="text-emerald-700 font-black" x-text="selectedRooms.length"></span>
                        </span>
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-100/50 px-3.5 py-2 rounded-xl">
                            Sesi Terpilih: <span class="text-emerald-700 font-black" x-text="selectedTimes.length"></span>
                        </span>
                    </div>
                    <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        Simpan & Lanjut
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
