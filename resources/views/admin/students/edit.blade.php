<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Data Siswa') }}
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-400/20 to-transparent rounded-bl-full -mr-4 -mt-4 pointer-events-none"></div>

            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-emerald-900">Form Edit Siswa</h3>
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                    {{ $student->nis }}
                </span>
            </div>

            <form action="{{ route('students.update', $student->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div x-data="{ nisValue: '{{ old('nis', $student->nis) }}' }">
                    <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">NIS</label>
                    <input type="text" 
                           name="nis"
                           x-model="nisValue" 
                           maxlength="10"
                           x-on:input="nisValue = nisValue.replace(/[^0-9]/g, '')"
                           class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-2.5 px-4 transition-all" 
                           required>
                    <p x-show="nisValue.length > 0 && nisValue.length < 10" class="text-red-500 text-xs ml-1 mt-1 animate-pulse">
                         ⚠️ NIS harus 10 digit (kurang <span x-text="10 - nisValue.length"></span> digit)
                    </p>
                    @error('nis') <span class="text-red-500 text-xs ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" 
                           class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-2.5 px-4 transition-all" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-800 mb-1 ml-1">Kelas</label>
                    <input type="text" name="class" value="{{ old('class', $student->class) }}" 
                           class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 text-emerald-900 shadow-inner py-2.5 px-4 transition-all" required>
                </div>

                <div class="pt-4 flex gap-3">
                    <a href="{{ route('students.index') }}" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold text-center hover:bg-white hover:shadow-md transition-all">
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