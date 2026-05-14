<x-app-layout>
    <x-slot name="header">
        Pilih Sesi Ujian
    </x-slot>

    <div class="max-w-6xl mx-auto">
        
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-extrabold text-emerald-900 drop-shadow-sm">Kelola Jadwal Mata Pelajaran</h2>
            <p class="text-emerald-800/70 mt-2">Pilih sesi ujian di bawah ini untuk membuka kalender dan mengatur jadwal.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            @foreach($sessions as $session)
            <a href="{{ route('sessions.schedules.index', $session->id) }}" class="group relative block">
                
                <div class="h-full bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-lg p-6 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-emerald-500/20 group-hover:bg-white/60 overflow-hidden">
                    
                    <div class="absolute inset-0 bg-gradient-to-br from-lime-400/0 via-emerald-500/0 to-teal-500/0 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>

                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="p-3 bg-emerald-100 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        
                        @if($session->is_active)
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-lime-400 text-emerald-900 shadow-sm animate-pulse">
                                AKTIF
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-500">
                                ARSIP
                            </span>
                        @endif
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-xl font-bold text-emerald-900 mb-1 group-hover:text-emerald-700 transition-colors">
                            {{ $session->title }}
                        </h3>
                        <p class="text-sm text-emerald-800/60 mb-6">
                            {{ \Carbon\Carbon::parse($session->start_date)->translatedFormat('d F Y') }}
                        </p>

                        <div class="flex items-center gap-3 text-xs font-bold text-emerald-700 bg-white/50 p-3 rounded-xl border border-white/40">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                {{ $session->schedules_count }} Mapel
                            </div>
                            <div class="h-3 w-px bg-emerald-300"></div>
                            <div class="flex items-center gap-1">
                                <span>Buka Kalender &rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach

            <a href="{{ route('wizard.step1') }}" class="group relative block h-full min-h-[200px] border-2 border-dashed border-emerald-300/50 rounded-[2rem] hover:border-emerald-500 hover:bg-emerald-50/30 transition-all flex flex-col items-center justify-center text-center p-6">
                <div class="p-4 bg-white/40 rounded-full mb-3 group-hover:scale-110 transition-transform shadow-sm">
                    <svg class="w-8 h-8 text-emerald-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="font-bold text-emerald-700 group-hover:text-emerald-900">Buat Sesi Baru</span>
            </a>

        </div>
    </div>
</x-app-layout>