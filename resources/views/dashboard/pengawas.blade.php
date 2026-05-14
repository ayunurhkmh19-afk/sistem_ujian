<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengawas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Selamat Datang, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-500">Anda login sebagai Pengawas Ujian.</p>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-800 mb-2">Informasi:</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                            <li>Tugas Anda adalah mengawasi jalannya ujian di ruangan yang telah ditentukan.</li>
                            <li>Jadwal pengawasan akan diinformasikan oleh Panitia (Admin).</li>
                            <li>Jika terjadi kendala teknis pada siswa, harap lapor ke Panitia.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>