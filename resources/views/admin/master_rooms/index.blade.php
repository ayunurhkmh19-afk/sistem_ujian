<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Master Ruangan') }}
    </x-slot>

    <!-- MD3 + Glassmorphism Container -->
    <div class="bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2.5rem] shadow-2xl shadow-emerald-900/10 overflow-hidden relative" x-data="{ openModal: false }">
        
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Toolbar / App Bar -->
        <div class="p-8 flex flex-col md:flex-row justify-between items-center gap-6 relative z-10">
            
            <!-- Search Bar (Filled Tonal) -->
            <form action="{{ route('master_rooms.index') }}" method="GET" class="w-full md:w-1/3 relative group">
                <div class="relative bg-emerald-50/50 hover:bg-emerald-50 focus-within:bg-white rounded-full transition-all duration-300 border border-transparent focus-within:border-emerald-200 focus-within:shadow-lg focus-within:shadow-emerald-500/10">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-emerald-700/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full pl-12 pr-6 py-4 rounded-full bg-transparent border-none text-emerald-900 placeholder-emerald-900/40 focus:ring-0 text-base font-medium" 
                           placeholder="Cari ruangan...">
                </div>
            </form>

            <!-- Action Buttons (FAB & Tonal) -->
            <div class="flex items-center gap-4">
                
                <!-- Sync Button (Outlined/Tonal) -->
                <form action="{{ route('master_rooms.sync') }}" method="POST" onsubmit="return confirm('Pindai dan import ruangan dari riwayat sesi?');">
                    @csrf
                    <button type="submit" class="group px-6 py-4 rounded-xl bg-white/40 border border-white/60 text-emerald-800 font-bold hover:bg-white hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2.5 backdrop-blur-sm" title="Sync from History">
                        <div class="p-1.5 rounded-lg bg-emerald-100/50 group-hover:bg-emerald-100 text-emerald-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <span class="hidden md:inline">Sync History</span>
                    </button>
                </form>

                <!-- Add Button (Filled Primary) -->
                <button @click="openModal = true" class="px-8 py-4 rounded-xl bg-emerald-600 text-white font-bold shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 hover:shadow-emerald-600/30 hover:scale-[1.02] transition-all flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Buat Baru</span>
                </button>
            </div>
        </div>

        <!-- Alerts -->
        @foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $key => $color)
            @if(session($key))
                <div class="mx-8 mb-6 p-4 rounded-2xl bg-{{ $color }}-50/80 border border-{{ $color }}-100 text-{{ $color }}-900 flex items-start gap-4 shadow-sm backdrop-blur-md" x-data="{ show: true }" x-show="show">
                    <div class="p-2 rounded-full bg-{{ $color }}-100 text-{{ $color }}-600 shrink-0">
                        @if($key === 'error')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($key === 'info')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

        <!-- Data List / Table -->
        <div class="px-4 pb-8 relative z-10">
            @if($rooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($rooms as $room)
                    <div class="group bg-white/70 backdrop-blur-xl border border-white/60 p-5 rounded-[1.5rem] shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:bg-white hover:scale-[1.01] transition-all duration-300 relative overflow-hidden">
                        
                        <!-- Card Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-emerald-100/50 rounded-2xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 shadow-inner">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-2 group-hover:translate-x-0">
                                <a href="{{ route('master_rooms.edit', $room->id) }}" class="p-2 rounded-full bg-amber-50 text-amber-600 hover:bg-amber-100 transition shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('master_rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus master ruangan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition shadow-sm" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 leading-tight mb-1">{{ $room->name }}</h3>
                            <p class="text-sm font-medium text-emerald-600/80 mb-4">{{ $room->capacity }} Kursi</p>
                            
                            <!-- Usage Stats (Pill Tags) -->
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pemakaian Terakhir</p>
                                <div class="flex flex-wrap gap-1.5 min-h-[1.5rem]">
                                    @if($room->rooms->count() > 0)
                                        @foreach($room->rooms->unique('exam_session_id')->take(3) as $usage)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-white border border-gray-100 text-[10px] font-semibold text-gray-600 shadow-sm">
                                                {{ \Illuminate\Support\Str::limit($usage->session->title ?? 'Sesi Terhapus', 15) }}
                                            </span>
                                        @endforeach
                                        @if($room->rooms->unique('exam_session_id')->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-[10px] text-gray-500">
                                                +{{ $room->rooms->unique('exam_session_id')->count() - 3 }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[11px] text-gray-400 italic">Belum ada riwayat</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 px-4">
                    {{ $rooms->links() }}
                </div>

            @else
                <div class="flex flex-col items-center justify-center py-20 text-center opacity-60">
                    <div class="p-6 rounded-full bg-gray-50 mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-600">Belum ada Master Ruangan</h3>
                    <p class="text-gray-500 mt-1 max-w-sm">Tambahkan ruangan baru secara manual atau sync dari riwayat ujian sebelumnya.</p>
                </div>
            @endif
        </div>

        <!-- MODAL TAMBAH (MD3 GLASS DIALOG) -->
        <template x-teleport="body">
            <div x-show="openModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
                
                <!-- Backdrop -->
                <div x-show="openModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-emerald-950/30 backdrop-blur-sm transition-opacity" 
                     @click="openModal = false"></div>

                <!-- Panel -->
                <div x-show="openModal"
                     x-transition:enter="ease-[cubic-bezier(0.2,0,0,1)] duration-500"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-md transform overflow-hidden rounded-[2.5rem] bg-white border border-white/50 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] ring-1 ring-black/5">
                    
                    <!-- Decorative Header -->
                    <div class="h-32 bg-gradient-to-br from-emerald-500 to-teal-600 relative overflow-hidden flex items-end p-8">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-32"></div>
                        <h3 class="text-2xl font-bold text-white relative z-10 tracking-tight">Ruangan Baru</h3>
                        <button @click="openModal = false" class="absolute top-6 right-6 p-2 rounded-full bg-white/20 text-white hover:bg-white/30 transition backdrop-blur-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-8">
                        <form action="{{ route('master_rooms.store') }}" method="POST">
                            @csrf
                            <div class="space-y-6">
                                <!-- Input -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2 ml-1">Nama Ruangan</label>
                                    <input type="text" name="name" 
                                           class="w-full bg-gray-50/80 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                           placeholder="Cth: Lab Bahasa" required autofocus>
                                </div>
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2 ml-1">Kapasitas Default</label>
                                    <div class="relative">
                                        <input type="number" name="capacity" 
                                               class="w-full bg-gray-50/80 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                               placeholder="20" required min="1" value="20">
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-bold pointer-events-none">KURSI</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-10 flex gap-4">
                                <button type="button" @click="openModal = false" class="flex-1 py-4 rounded-2xl text-emerald-700 font-bold hover:bg-emerald-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 hover:shadow-emerald-600/40 hover:scale-[1.02] transition-all">
                                    Simpan Ruangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
