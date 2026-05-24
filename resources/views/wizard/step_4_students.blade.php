<x-app-layout>
    <x-slot name="header">
        Wizard: Data Siswa & Import
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- PROGRESS BAR -->
        <div class="mb-8">
            <div class="flex justify-between text-xs font-bold text-emerald-800 uppercase tracking-widest mb-2">
                <span>Langkah 4: Import Siswa</span>
                <span class="opacity-40">Progres: 80%</span>
            </div>
            <div class="w-full bg-black/10 rounded-full h-3 backdrop-blur-sm p-0.5">
                <div class="bg-gradient-to-r from-lime-400 to-emerald-500 h-2 rounded-full shadow-[0_0_10px_rgba(16,185,129,0.6)] transition-all duration-500" style="width: 80%"></div>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="bg-white/40 backdrop-blur-xl border border-white/50 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
            <!-- Dekorasi -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-b from-white/20 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>

            <h3 class="text-2xl font-extrabold text-emerald-900 mb-1">Import Data Siswa</h3>
            <p class="text-emerald-800/60 text-sm mb-8">Unggah file Excel berisi data siswa baru, atau lanjutkan menggunakan data siswa yang sudah ada di database.</p>

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

            <form action="{{ route('wizard.storeStep4', $session->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Current Database Summary Card -->
                <div class="bg-emerald-50/50 border border-emerald-100 p-6 rounded-[1.5rem] shadow-inner">
                    <h4 class="text-emerald-900 font-extrabold text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        Ringkasan Siswa Terdaftar saat ini
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php $totalDbStudents = 0; @endphp
                        @foreach($levelsSummary as $summary)
                            @php $totalDbStudents += $summary->students_count; @endphp
                            <div class="bg-white/80 p-4 rounded-xl border border-emerald-100/30 flex flex-col justify-center">
                                <span class="text-xs text-gray-400 font-bold uppercase">{{ $summary->name }}</span>
                                <span class="text-emerald-700 font-black text-xl mt-1">{{ $summary->students_count }} Siswa</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-100/50 flex justify-between text-xs text-emerald-800 font-bold">
                        <span>Total Keseluruhan di Database:</span>
                        <span class="text-emerald-900 font-black">{{ $totalDbStudents }} Siswa</span>
                    </div>
                </div>

                <!-- Input File -->
                <div class="group">
                    <label class="block text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider mb-1.5 ml-1 group-hover:text-emerald-600 transition-colors">
                        Upload Data Siswa Baru (Excel .xlsx) <span class="text-[9px] font-normal bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded ml-2">Opsional</span>
                    </label>
                    <div class="p-8 rounded-2xl border-2 border-dashed border-emerald-300/50 bg-white/40 hover:bg-white/60 hover:border-emerald-400 transition-all text-center relative cursor-pointer overflow-hidden group-hover:shadow-lg group-hover:shadow-emerald-500/10">
                        <input type="file" name="file_siswa" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="p-3 bg-emerald-100/50 rounded-full mb-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-emerald-900">Klik atau seret file Excel ke sini</p>
                            <p class="text-xs text-emerald-700/60 mt-1">Format: .xlsx, .xls (Max 2MB)</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-emerald-600/60 mt-2 ml-1 italic">*Kolom minimal dalam Excel: <strong>nis</strong>, <strong>nama</strong>, dan <strong>kelas</strong>. Format nama kelas default adalah nomor kelas di depan, e.g. "10 IPA 1" atau "XII IPS 2".</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 flex justify-between items-center border-t border-emerald-900/10">
                    <p class="text-xs text-emerald-800/40 font-semibold">*Kosongkan jika ingin langsung lanjut menggunakan data di atas.</p>
                    <button type="submit" class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-[0_10px_20px_-5px_rgba(5,150,105,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(5,150,105,0.5)] hover:scale-[1.02] hover:-translate-y-0.5 transition-all flex items-center gap-2 active:scale-95">
                        Simpan & Lanjut
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
