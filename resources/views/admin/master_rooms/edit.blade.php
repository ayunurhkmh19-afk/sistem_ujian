<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Master Ruangan') }}
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-400/20 to-transparent rounded-bl-full -mr-4 -mt-4 pointer-events-none"></div>

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-emerald-900">Form Edit Ruangan</h3>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                    {{ $masterRoom->name }}
                </span>
            </div>

            <form action="{{ route('master_rooms.update', $masterRoom->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Nama Ruangan</label>
                    <input type="text" name="name" value="{{ old('name', $masterRoom->name) }}" 
                           class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-2.5 px-4 transition-all" required>
                    @error('name') <span class="text-red-500 text-xs ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Kapasitas Default</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $masterRoom->capacity) }}" 
                           class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-2.5 px-4 transition-all" required min="1">
                    @error('capacity') <span class="text-red-500 text-xs ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex gap-3">
                    <a href="{{ route('master_rooms.index') }}" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold text-center hover:bg-white hover:shadow-md transition-all">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-lg hover:shadow-emerald-500/40 hover:scale-[1.02] transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
