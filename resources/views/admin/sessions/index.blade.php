<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Sesi Ujian') }}
    </x-slot>

    <div class="bg-white/40 backdrop-blur-lg border border-white/50 rounded-[2rem] shadow-xl overflow-hidden">
        
        <div class="p-6 flex flex-col md:flex-row justify-between items-center gap-4 border-b border-white/20">
            <div>
                <h3 class="text-lg font-bold text-emerald-900">Daftar Ujian</h3>
                <p class="text-sm text-emerald-800/60">Kelola jadwal UAS, UTS, atau ujian lainnya.</p>
            </div>
            <a href="{{ route('wizard.step1') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-lime-500 to-emerald-600 text-white font-bold shadow-lg hover:shadow-emerald-500/40 hover:scale-105 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Buat Sesi Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 p-4 rounded-2xl bg-emerald-100/80 border border-emerald-200 text-emerald-800 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-emerald-900/5 text-emerald-900/70 text-xs uppercase tracking-wider font-bold border-b border-white/10">
                        <th class="px-6 py-4">Nama Kegiatan</th>
                        <th class="px-6 py-4">Pelaksanaan</th>
                        <th class="px-6 py-4">Statistik</th>
                        <th class="px-6 py-4 text-center">AG Status</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/20">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-white/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-emerald-900">{{ $session->title }}</div>
                            <div class="text-xs text-emerald-600/70">ID: #{{ $session->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($session->start_date)->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <span class="px-2 py-1 rounded-md bg-white/50 border border-white text-xs font-semibold text-emerald-700 shadow-sm">
                                    {{ $session->rooms_count }} R
                                </span>
                                <span class="px-2 py-1 rounded-md bg-white/50 border border-white text-xs font-semibold text-teal-700 shadow-sm">
                                    {{ $session->allocations_count }} P
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @include('admin.sessions._ag_panel', ['session' => $session])
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('sessions.toggle', $session->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold border transition-all {{ $session->is_active ? 'bg-lime-400/20 text-lime-700 border-lime-400 hover:bg-lime-400/40' : 'bg-gray-200/50 text-gray-500 border-gray-300 hover:bg-gray-300/50' }}">
                                    {{ $session->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('wizard.step3', $session->id) }}" class="p-2 rounded-xl bg-white/40 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-white/50 transition-all shadow-sm" title="Atur Distribusi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </a>
                                <a href="{{ route('sessions.schedules.index', $session->id) }}" class="p-2 rounded-xl bg-white/40 text-blue-600 hover:bg-blue-500 hover:text-white border border-white/50 transition-all shadow-sm" title="Jadwal Mapel">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </a>
                                <a href="{{ route('sessions.edit', $session->id) }}" class="p-2 rounded-xl bg-white/40 text-yellow-600 hover:bg-yellow-500 hover:text-white border border-white/50 transition-all shadow-sm" title="Edit Info">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini beserta semua data kartu ujian?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-white/40 text-red-600 hover:bg-red-500 hover:text-white border border-white/50 transition-all shadow-sm" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Belum ada sesi ujian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-white/20">
            {{ $sessions->links() }}
        </div>
    </div>
</x-app-layout>