<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Data Siswa') }}
    </x-slot>

    <div class="bg-white/40 backdrop-blur-lg border border-white/50 rounded-[2rem] shadow-xl overflow-hidden" x-data="{ openModal: false }">
        
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-white/20">
            
            <form action="{{ route('students.index') }}" method="GET" class="w-full md:w-1/2 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-emerald-600 group-focus-within:text-lime-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full pl-11 pr-4 py-3 rounded-full bg-white/60 border-transparent focus:border-lime-400 focus:ring-4 focus:ring-lime-400/20 text-emerald-900 placeholder-emerald-900/50 shadow-sm transition-all" 
                       placeholder="Cari Nama, NIS, atau Kelas...">
            </form>

            <button @click="openModal = true" class="px-6 py-3 rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold shadow-lg hover:shadow-emerald-500/30 hover:scale-105 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Siswa
            </button>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-emerald-100/80 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-sm" x-data="{ show: true }" x-show="show">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto hover:text-emerald-950">&times;</button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-900/5 text-emerald-900/70 text-xs uppercase tracking-wider font-bold">
                        <th class="px-6 py-4 rounded-tl-lg">NIS</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                        <th class="px-6 py-4 rounded-tr-lg"></th> </tr>
                </thead>
                
                @forelse($students as $student)
                <tbody x-data="{ expanded: false }" class="border-b border-emerald-900/5 hover:bg-white/30 transition-colors group">
                    
                    <tr class="cursor-pointer" @click="expanded = !expanded">
                        <td class="px-6 py-4 font-medium text-emerald-900">
                            <span class="px-3 py-1 rounded-lg bg-white/50 border border-emerald-100 text-xs shadow-sm">
                                {{ $student->nis }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors">
                            {{ $student->name }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                {{ $student->studentClass?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2" @click.stop>
                                <a href="{{ route('students.edit', $student->id) }}" class="p-2 rounded-full text-yellow-600 hover:bg-yellow-50 transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Hapus data siswa ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-full text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-emerald-400">
                            <svg class="w-5 h-5 transform transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </td>
                    </tr>

                    <tr x-show="expanded" 
                        x-collapse 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="bg-emerald-50/50">
                        <td colspan="5" class="px-6 py-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-bold text-emerald-800 mb-3 uppercase tracking-wider">Informasi Detail</h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><strong>Terdaftar Sejak:</strong> {{ $student->created_at->format('d M Y') }}</p>
                                        <p><strong>Update Terakhir:</strong> {{ $student->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-bold text-emerald-800 mb-3 uppercase tracking-wider">Status Ujian Aktif</h4>
                                    @if($student->allocations->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($student->allocations as $alloc)
                                                <div class="bg-white px-3 py-2 rounded-lg border border-emerald-100 shadow-sm">
                                                    <p class="text-xs font-bold text-emerald-700">{{ $alloc->schedule?->session?->title ?? '-' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        Ruang: {{ $alloc->room?->name ?? '-' }} | 
                                                        Meja: {{ str_pad($alloc->desk_number, 2, '0', STR_PAD_LEFT) }} |
                                                        Mapel: {{ $alloc->schedule?->subject?->name ?? '-' }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic bg-white/50 px-3 py-1 rounded-full">Belum ada alokasi ujian aktif</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>

                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                            Belum ada data siswa. Silakan tambahkan data baru.
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>

        <div class="px-6 py-4 border-t border-white/20">
            {{ $students->links() }}
        </div>

        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 transition-opacity bg-emerald-900/40 backdrop-blur-sm" @click="openModal = false"></div>

                <div class="inline-block w-full max-w-lg my-8 overflow-hidden text-left align-middle transition-all transform bg-white/90 backdrop-blur-xl shadow-2xl rounded-3xl border border-white/50">
                    
                    <div class="px-6 py-6">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-xl font-bold text-emerald-900">Tambah Siswa Baru</h3>
                            <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form action="{{ route('students.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div x-data="{ nisValue: '{{ old('nis') }}' }">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Siswa (NIS)</label>
                                    <input type="text" 
                                           name="nis" 
                                           x-model="nisValue"
                                           maxlength="10"
                                           x-on:input="nisValue = nisValue.replace(/[^0-9]/g, '')"
                                           class="w-full rounded-xl border-gray-300 bg-white/50 focus:border-emerald-500 focus:ring-emerald-500" 
                                           placeholder="1234567890"
                                           required>
                                    <p x-show="nisValue.length > 0 && nisValue.length < 10" class="text-red-500 text-xs mt-1 animate-pulse">
                                        ⚠️ NIS harus 10 digit (kurang <span x-text="10 - nisValue.length"></span> digit)
                                    </p>
                                    <p x-show="nisValue.length === 10" class="text-emerald-600 text-xs mt-1">
                                        ✓ Panjang NIS valid
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" class="w-full rounded-xl border-gray-300 bg-white/50 focus:border-emerald-500 focus:ring-emerald-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                    <select name="student_class_id" class="w-full rounded-xl border-gray-300 bg-white/50 focus:border-emerald-500 focus:ring-emerald-500 focus:border-emerald-500 focus:ring-emerald-500" required>
                                        <option value="">Pilih Kelas...</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('student_class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} ({{ $class->level?->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-3">
                                <button type="button" @click="openModal = false" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold shadow-lg hover:shadow-emerald-500/30 hover:scale-[1.02] transition-all">
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>