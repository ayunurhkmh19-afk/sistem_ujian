<x-app-layout>
    <x-slot name="header">Ruangan: {{ $session->title }}</x-slot>

    <div class="bg-white/40 backdrop-blur-lg border border-white/50 rounded-[2rem] shadow-xl p-6 mb-6">
        <h3 class="text-lg font-bold text-emerald-900 mb-4">Tambah Ruangan Cepat</h3>
        <form action="{{ route('rooms.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <input type="hidden" name="exam_session_id" value="{{ $session->id }}">
            
            <div class="flex-1 w-full">
                <label class="text-xs font-bold text-emerald-800 ml-1">Nama Ruangan</label>
                <input type="text" name="name" class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-emerald-500 shadow-sm mt-1" placeholder="Lab Komputer 1" required>
            </div>
            
            <div class="w-full md:w-32">
                <label class="text-xs font-bold text-emerald-800 ml-1">Kapasitas</label>
                <input type="number" name="capacity" class="w-full rounded-xl border-transparent bg-white/60 focus:bg-white focus:ring-emerald-500 shadow-sm mt-1" placeholder="20" required>
            </div>

            <button type="submit" class="w-full md:w-auto px-6 py-2.5 rounded-xl bg-emerald-700 text-white font-bold shadow-lg hover:bg-emerald-800 transition-all">
                Simpan
            </button>
        </form>
    </div>

    <div class="bg-white/40 backdrop-blur-lg border border-white/50 rounded-[2rem] shadow-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-emerald-900/5 text-emerald-900/70">
                <tr>
                    <th class="px-6 py-4 font-bold text-xs uppercase">Nama Ruang</th>
                    <th class="px-6 py-4 font-bold text-xs uppercase">Kapasitas</th>
                    <th class="px-6 py-4 font-bold text-xs uppercase">Terisi</th>
                    <th class="px-6 py-4 font-bold text-xs uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/20">
                @foreach($session->rooms as $room)
                <tr class="hover:bg-white/30 transition-colors">
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                        @csrf @method('PUT')
                        <td class="px-6 py-4">
                            <input type="text" name="name" value="{{ $room->name }}" class="bg-transparent border-none p-0 font-semibold text-emerald-900 focus:ring-0 w-full">
                        </td>
                        <td class="px-6 py-4">
                            <input type="number" name="capacity" value="{{ $room->capacity }}" class="bg-white/40 border-none rounded px-2 py-1 w-20 text-sm text-center focus:bg-white focus:ring-emerald-500">
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded bg-teal-100 text-teal-800 text-xs font-bold">
                                {{ $room->allocations_count }} Siswa
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <button type="submit" class="text-xs font-bold text-blue-600 hover:underline uppercase tracking-wide">Update</button>
                            <button type="submit" form="del-{{ $room->id }}" class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wide">Hapus</button>
                        </td>
                    </form>
                    <form id="del-{{ $room->id }}" action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>