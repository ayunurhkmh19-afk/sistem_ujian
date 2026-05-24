<x-app-layout>
    <x-slot name="header">
        Wizard: Pilih Mata Pelajaran
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 2: Pilih Mata Pelajaran</span>
                <span class="opacity-40">Progres: 40%</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 40%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Pilih Mata Pelajaran</h3>
                    <p class="text-emerald-800/60 text-sm">Pilih mata pelajaran yang akan diujikan pada sesi <span class="font-bold text-emerald-800">{{ $session->title }}</span>.</p>
                </div>
                <a href="{{ route('subjects.index') }}" target="_blank" class="px-4 py-2 rounded-xl bg-white/60 border border-white/80 hover:bg-white text-emerald-800 font-bold text-xs shadow-sm transition-all flex items-center gap-1.5">
                    Kelola Mapel Master &rarr;
                </a>
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

            <form action="{{ route('wizard.storeStep2', $session->id) }}" method="POST" class="space-y-8" x-data="{ selectedSubjects: @js($selectedSubjectIds) }">
                @csrf

                <!-- Grid Groups per Level -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($levels as $level)
                    <div class="bg-white/60 border border-white/85 p-5 rounded-[1.5rem] shadow-sm relative overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center pb-3 border-b border-emerald-900/10 mb-4">
                            <h4 class="font-extrabold text-emerald-900 text-base flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-emerald-600 rounded-full"></span>
                                {{ $level->name }}
                            </h4>
                            <!-- Check all button -->
                            <button type="button" 
                                    @click="
                                        let ids = {{ json_encode($level->subjects->pluck('id')->toArray()) }};
                                        let allSelected = ids.every(id => selectedSubjects.includes(id));
                                        if (allSelected) {
                                            selectedSubjects = selectedSubjects.filter(id => !ids.includes(id));
                                        } else {
                                            selectedSubjects = [...new Set([...selectedSubjects, ...ids])];
                                        }
                                    "
                                    class="text-[10px] font-bold text-emerald-700 hover:text-emerald-950 transition-colors uppercase tracking-wider">
                                Toggle Semua
                            </button>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto max-h-[300px] pr-2 scrollbar-thin scrollbar-thumb-emerald-100">
                            @if($level->subjects->count() > 0)
                                @foreach($level->subjects as $subject)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50/50 cursor-pointer transition-colors border border-transparent hover:border-emerald-100/50">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                           x-model="selectedSubjects"
                                           :value="{{ $subject->id }}"
                                           class="rounded-lg border-emerald-300 text-emerald-600 focus:ring-emerald-500 w-5 h-5 transition">
                                    <span class="text-sm font-semibold text-emerald-900 leading-tight">{{ $subject->name }}</span>
                                </label>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center py-10 text-center opacity-60">
                                    <span class="text-xs text-gray-500 italic">Belum ada mata pelajaran</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="pt-6 flex justify-between items-center border-t border-emerald-900/10">
                    <span class="text-xs font-bold text-emerald-800 bg-emerald-100/50 px-3.5 py-2 rounded-xl">
                        Total Terpilih: <span class="text-emerald-700 font-black" x-text="selectedSubjects.length"></span> Mapel
                    </span>
                    <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        Simpan & Lanjut
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
