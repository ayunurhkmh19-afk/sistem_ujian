<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen User (Panitia & Pengawas)') }}
    </x-slot>

    <!-- State Management with Alpine.js -->
    <div x-data="{
        openCreateModal: false,
        openEditModal: false,
        openDeleteModal: false,
        editId: null,
        editName: '',
        editEmail: '',
        editRole: 'pengawas',
        deleteId: null,
        deleteName: '',
        deleteRole: '',
        showCreatePassword: false,
        showEditPassword: false,
        
        openEdit(user) {
            this.editId = user.id;
            this.editName = user.name;
            this.editEmail = user.email;
            this.editRole = user.role;
            this.openEditModal = true;
        },
        
        openDelete(user) {
            this.deleteId = user.id;
            this.deleteName = user.name;
            this.deleteRole = user.role;
            this.openDeleteModal = true;
        }
    }">

        <!-- STATS OVERVIEW CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
            <!-- Total Pengguna -->
            <div class="bg-white/60 backdrop-blur-xl border border-white/50 p-6 rounded-[2rem] shadow-lg relative overflow-hidden group hover:bg-white/80 transition-all duration-300">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-3 rounded-2xl bg-emerald-100 text-emerald-700 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100/60 text-emerald-800 text-[11px] font-extrabold uppercase tracking-wider">Total</span>
                </div>
                <h3 class="text-3xl font-black text-emerald-950">{{ $totalUsers }}</h3>
                <p class="text-emerald-800/70 text-xs font-semibold mt-1">Total Pengguna Terdaftar</p>
            </div>

            <!-- Total Panitia -->
            <div class="bg-white/60 backdrop-blur-xl border border-white/50 p-6 rounded-[2rem] shadow-lg relative overflow-hidden group hover:bg-white/80 transition-all duration-300">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-3 rounded-2xl bg-teal-100 text-teal-700 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-teal-100/60 text-teal-800 text-[11px] font-extrabold uppercase tracking-wider">Admin</span>
                </div>
                <h3 class="text-3xl font-black text-teal-950">{{ $totalPanitia }}</h3>
                <p class="text-teal-800/70 text-xs font-semibold mt-1">Akun Panitia Ujian</p>
            </div>

            <!-- Total Pengawas -->
            <div class="bg-white/60 backdrop-blur-xl border border-white/50 p-6 rounded-[2rem] shadow-lg relative overflow-hidden group hover:bg-white/80 transition-all duration-300">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-3 rounded-2xl bg-sky-100 text-sky-700 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-sky-100/60 text-sky-800 text-[11px] font-extrabold uppercase tracking-wider">Pengawas</span>
                </div>
                <h3 class="text-3xl font-black text-sky-950">{{ $totalPengawas }}</h3>
                <p class="text-sky-800/70 text-xs font-semibold mt-1">Akun Pengawas Ruangan</p>
            </div>

            <!-- Total Penugasan -->
            <div class="bg-white/60 backdrop-blur-xl border border-white/50 p-6 rounded-[2rem] shadow-lg relative overflow-hidden group hover:bg-white/80 transition-all duration-300">
                <div class="flex justify-between items-start mb-3">
                    <div class="p-3 rounded-2xl bg-amber-100 text-amber-700 shadow-inner group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-100/60 text-amber-800 text-[11px] font-extrabold uppercase tracking-wider">Tugas</span>
                </div>
                <h3 class="text-3xl font-black text-amber-950">{{ $totalAssignments }}</h3>
                <p class="text-amber-800/70 text-xs font-semibold mt-1">Total Plot Penugasan</p>
            </div>
        </div>

        <!-- MAIN CONTAINER -->
        <div class="bg-white/60 backdrop-blur-2xl border border-white/40 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
            
            <!-- Decorative Glow -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- TOOLBAR & FILTERS -->
            <div class="p-6 md:p-8 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-6 relative z-10 border-b border-white/30">
                
                <!-- Left: Search & Filter Tabs -->
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4 flex-1">
                    
                    <!-- Search Input -->
                    <form action="{{ route('users.index') }}" method="GET" class="w-full md:w-80 relative group">
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
                        @endif
                        <div class="relative bg-emerald-50/60 hover:bg-emerald-50 focus-within:bg-white rounded-full transition-all duration-300 border border-emerald-100/80 focus-within:border-emerald-300 focus-within:shadow-lg focus-within:shadow-emerald-500/10">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-emerald-700/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="w-full pl-11 pr-10 py-3 rounded-full bg-transparent border-none text-emerald-950 placeholder-emerald-800/40 focus:ring-0 text-sm font-medium" 
                                   placeholder="Cari nama atau email...">
                            @if(request('search'))
                                <a href="{{ route('users.index', ['role' => request('role')]) }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    <!-- Role Filter Tabs -->
                    <div class="inline-flex p-1 rounded-2xl bg-emerald-900/10 backdrop-blur-md border border-white/30 text-xs font-bold text-emerald-900 shadow-inner">
                        <a href="{{ route('users.index', array_filter(['search' => request('search')])) }}" 
                           class="px-4 py-2 rounded-xl transition-all {{ !request('role') ? 'bg-white text-emerald-900 shadow-md scale-[1.02]' : 'hover:bg-white/40 text-emerald-800' }}">
                            Semua ({{ $totalUsers }})
                        </a>
                        <a href="{{ route('users.index', array_filter(['role' => 'panitia', 'search' => request('search')])) }}" 
                           class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request('role') === 'panitia' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 scale-[1.02]' : 'hover:bg-white/40 text-emerald-800' }}">
                            <span class="w-2 h-2 rounded-full {{ request('role') === 'panitia' ? 'bg-lime-300' : 'bg-emerald-500' }}"></span>
                            Panitia ({{ $totalPanitia }})
                        </a>
                        <a href="{{ route('users.index', array_filter(['role' => 'pengawas', 'search' => request('search')])) }}" 
                           class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request('role') === 'pengawas' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/30 scale-[1.02]' : 'hover:bg-white/40 text-emerald-800' }}">
                            <span class="w-2 h-2 rounded-full {{ request('role') === 'pengawas' ? 'bg-white' : 'bg-sky-500' }}"></span>
                            Pengawas ({{ $totalPengawas }})
                        </a>
                    </div>
                </div>

                <!-- Right: Add Button -->
                <div class="flex items-center gap-3">
                    <button @click="openCreateModal = true" class="w-full md:w-auto px-6 py-3.5 rounded-2xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-600/25 hover:bg-emerald-700 hover:shadow-emerald-600/40 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Tambah User Baru</span>
                    </button>
                </div>
            </div>

            <!-- ALERTS / FLASH MESSAGES -->
            <div class="px-6 md:px-8 pt-6">
                @if(session('success'))
                    <div class="p-4 mb-4 rounded-2xl bg-emerald-50/90 border border-emerald-200 text-emerald-900 flex items-start gap-4 shadow-sm backdrop-blur-md animate-fade-in-down" x-data="{ show: true }" x-show="show">
                        <div class="p-2 rounded-full bg-emerald-100 text-emerald-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="py-1">
                            <p class="font-bold text-sm">Berhasil</p>
                            <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="ml-auto p-1.5 rounded-full hover:bg-emerald-200/50 text-emerald-700 transition">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 mb-4 rounded-2xl bg-red-50/90 border border-red-200 text-red-900 flex items-start gap-4 shadow-sm backdrop-blur-md animate-fade-in-down" x-data="{ show: true }" x-show="show">
                        <div class="p-2 rounded-full bg-red-100 text-red-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="py-1">
                            <p class="font-bold text-sm">Peringatan</p>
                            <p class="text-xs text-red-800 mt-0.5">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="ml-auto p-1.5 rounded-full hover:bg-red-200/50 text-red-700 transition">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 mb-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 flex items-start gap-4 shadow-sm backdrop-blur-md animate-fade-in-down" x-data="{ show: true }" x-show="show">
                        <div class="p-2 rounded-full bg-amber-100 text-amber-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="py-1 flex-1">
                            <p class="font-bold text-sm">Periksa Kembali Isian Formulir</p>
                            <ul class="text-xs text-amber-800 mt-1 list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button @click="show = false" class="ml-auto p-1.5 rounded-full hover:bg-amber-200/50 text-amber-700 transition">&times;</button>
                    </div>
                @endif
            </div>

            <!-- USERS DATA LIST -->
            <div class="p-6 md:p-8 relative z-10">
                @if($users->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($users as $user)
                            @php
                                $isCurrentUser = Auth::id() === $user->id;
                                $isPanitia = $user->role === 'panitia';
                            @endphp
                            
                            <div class="group bg-white/75 backdrop-blur-xl border border-white/80 p-6 rounded-[2rem] shadow-sm hover:shadow-[0_12px_36px_rgba(0,0,0,0.06)] hover:bg-white hover:-translate-y-1 transition-all duration-300 relative flex flex-col justify-between overflow-hidden">
                                
                                <!-- Top Accent Glow -->
                                <div class="absolute top-0 right-0 w-32 h-32 rounded-full blur-2xl pointer-events-none {{ $isPanitia ? 'bg-emerald-400/10' : 'bg-sky-400/10' }}"></div>

                                <div>
                                    <!-- User Header (Avatar + Role Badge + Actions) -->
                                    <div class="flex items-start justify-between gap-4 mb-4">
                                        
                                        <!-- Avatar & Info -->
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-black text-white shadow-md shadow-emerald-900/10 {{ $isPanitia ? 'bg-gradient-to-br from-emerald-500 to-teal-700' : 'bg-gradient-to-br from-sky-500 to-indigo-700' }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="text-base font-bold text-gray-900 group-hover:text-emerald-900 transition-colors leading-tight line-clamp-1" title="{{ $user->name }}">
                                                        {{ $user->name }}
                                                    </h3>
                                                    @if($isCurrentUser)
                                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200" title="Akun Anda Saat Ini">
                                                            Anda
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 font-medium truncate max-w-[180px]" title="{{ $user->email }}">
                                                    {{ $user->email }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Quick Action Buttons -->
                                        <div class="flex items-center gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <!-- Edit Button -->
                                            <button @click="openEdit({
                                                id: {{ $user->id }},
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ addslashes($user->email) }}',
                                                role: '{{ $user->role }}'
                                            })" class="p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 hover:text-amber-700 transition shadow-sm" title="Edit Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>

                                            <!-- Delete Button -->
                                            @if(!$isCurrentUser)
                                                <button @click="openDelete({
                                                    id: {{ $user->id }},
                                                    name: '{{ addslashes($user->name) }}',
                                                    role: '{{ $user->role }}'
                                                })" class="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition shadow-sm" title="Hapus Pengguna">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            @else
                                                <span class="p-2 text-gray-300 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m11-3.5v-1a5.5 5.5 0 00-11 0v1m-2 0a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2v-6a2 2 0 00-2-2h-2z" /></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- User Meta & Badges -->
                                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                                        <!-- Role Badge -->
                                        <div>
                                            @if($isPanitia)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                    Panitia Ujian
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-200 shadow-sm">
                                                    <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                    Pengawas Ruang
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Contextual Details -->
                                        <div class="text-right">
                                            @if(!$isPanitia)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-[11px] font-bold">
                                                    {{ $user->pengawasan_count ?? 0 }} Sesi Tugas
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 font-semibold">
                                                    Akses Penuh
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Joined Date Footer -->
                                <div class="mt-4 pt-3 border-t border-dashed border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                                    <span>Terdaftar:</span>
                                    <span class="font-medium text-gray-600">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PAGINATION -->
                    <div class="mt-8">
                        {{ $users->links() }}
                    </div>

                @else
                    <div class="flex flex-col items-center justify-center py-20 text-center opacity-70">
                        <div class="p-6 rounded-full bg-emerald-50 mb-4 shadow-inner">
                            <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Tidak ada pengguna ditemukan</h3>
                        <p class="text-gray-500 mt-1 max-w-sm text-sm">
                            @if(request('search') || request('role'))
                                Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.
                            @else
                                Belum ada data pengguna selain akun administrator utama.
                            @endif
                        </p>
                        @if(request('search') || request('role'))
                            <a href="{{ route('users.index') }}" class="mt-4 px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                Reset Pencarian
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH USER (CREATE MODAL) -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="openCreateModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
                
                <!-- Backdrop -->
                <div x-show="openCreateModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-emerald-950/40 backdrop-blur-md transition-opacity" 
                     @click="openCreateModal = false"></div>

                <!-- Panel -->
                <div x-show="openCreateModal"
                     x-transition:enter="ease-[cubic-bezier(0.2,0,0,1)] duration-500"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-lg transform overflow-hidden rounded-[2.5rem] bg-white border border-white/60 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2)]">
                    
                    <!-- Header -->
                    <div class="h-32 bg-gradient-to-br from-emerald-600 to-teal-700 relative overflow-hidden flex items-end p-8">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-32"></div>
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-lime-300">Pengguna Baru</span>
                            <h3 class="text-2xl font-bold text-white relative z-10 tracking-tight">Tambah Pengguna</h3>
                        </div>
                        <button @click="openCreateModal = false" class="absolute top-6 right-6 p-2 rounded-full bg-white/20 text-white hover:bg-white/30 transition backdrop-blur-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <div class="p-8 max-h-[75vh] overflow-y-auto">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf

                            <div class="space-y-5">
                                <!-- Nama Lengkap -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Nama Lengkap & Gelar</label>
                                    <input type="text" name="name" 
                                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                           placeholder="Cth: Dra. Hj. Siti Aminah, M.Pd" required>
                                </div>

                                <!-- Email -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Alamat Email (Untuk Login)</label>
                                    <input type="email" name="email" 
                                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                           placeholder="Cth: sitiaminah@sekolah.sch.id" required>
                                </div>

                                <!-- Role Selection Cards -->
                                <div>
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Peran / Hak Akses (Role)</label>
                                    <div class="grid grid-cols-2 gap-3" x-data="{ selectedRole: 'pengawas' }">
                                        <!-- Opsi Pengawas -->
                                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all flex flex-col justify-between"
                                               :class="selectedRole === 'pengawas' ? 'border-sky-500 bg-sky-50/50 shadow-sm' : 'border-gray-100 bg-gray-50/50 hover:bg-gray-100/50'">
                                            <input type="radio" name="role" value="pengawas" class="sr-only" x-model="selectedRole">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="p-2 rounded-xl" :class="selectedRole === 'pengawas' ? 'bg-sky-500 text-white' : 'bg-gray-200 text-gray-600'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                </div>
                                                <span class="w-2.5 h-2.5 rounded-full" :class="selectedRole === 'pengawas' ? 'bg-sky-500 ring-4 ring-sky-200' : 'bg-gray-300'"></span>
                                            </div>
                                            <p class="font-bold text-sm text-gray-900">Pengawas</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-tight">Absensi siswa & berita acara ujian</p>
                                        </label>

                                        <!-- Opsi Panitia -->
                                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all flex flex-col justify-between"
                                               :class="selectedRole === 'panitia' ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-gray-100 bg-gray-50/50 hover:bg-gray-100/50'">
                                            <input type="radio" name="role" value="panitia" class="sr-only" x-model="selectedRole">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="p-2 rounded-xl" :class="selectedRole === 'panitia' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                </div>
                                                <span class="w-2.5 h-2.5 rounded-full" :class="selectedRole === 'panitia' ? 'bg-emerald-600 ring-4 ring-emerald-200' : 'bg-gray-300'"></span>
                                            </div>
                                            <p class="font-bold text-sm text-gray-900">Panitia (Admin)</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-tight">Kelola jadwal, sesi, siswa & kartu</p>
                                        </label>
                                    </div>
                                </div>

                                <!-- Password & Konfirmasi -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="group">
                                        <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Password</label>
                                        <div class="relative">
                                            <input :type="showCreatePassword ? 'text' : 'password'" name="password" 
                                                   class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                                   placeholder="Min. 6 karakter" required minlength="6">
                                            <button type="button" @click="showCreatePassword = !showCreatePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!showCreatePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <svg x-show="showCreatePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Ulangi Password</label>
                                        <input :type="showCreatePassword ? 'text' : 'password'" name="password_confirmation" 
                                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                               placeholder="Ulangi password" required minlength="6">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-4">
                                <button type="button" @click="openCreateModal = false" class="flex-1 py-4 rounded-2xl text-emerald-800 font-bold hover:bg-emerald-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-emerald-600 text-white font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 hover:shadow-emerald-600/40 hover:scale-[1.02] active:scale-95 transition-all">
                                    Simpan Pengguna
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- MODAL EDIT USER (EDIT MODAL) -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="openEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
                
                <!-- Backdrop -->
                <div x-show="openEditModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-emerald-950/40 backdrop-blur-md transition-opacity" 
                     @click="openEditModal = false"></div>

                <!-- Panel -->
                <div x-show="openEditModal"
                     x-transition:enter="ease-[cubic-bezier(0.2,0,0,1)] duration-500"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-lg transform overflow-hidden rounded-[2.5rem] bg-white border border-white/60 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2)]">
                    
                    <!-- Header -->
                    <div class="h-32 bg-gradient-to-br from-amber-500 to-teal-700 relative overflow-hidden flex items-end p-8">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-32"></div>
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-200">Perbarui Data</span>
                            <h3 class="text-2xl font-bold text-white relative z-10 tracking-tight">Edit Pengguna</h3>
                        </div>
                        <button @click="openEditModal = false" class="absolute top-6 right-6 p-2 rounded-full bg-white/20 text-white hover:bg-white/30 transition backdrop-blur-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <div class="p-8 max-h-[75vh] overflow-y-auto">
                        <form :action="'{{ url('users') }}/' + editId" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="space-y-5">
                                <!-- Nama Lengkap -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Nama Lengkap & Gelar</label>
                                    <input type="text" name="name" x-model="editName"
                                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                           placeholder="Nama Lengkap" required>
                                </div>

                                <!-- Email -->
                                <div class="group">
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Alamat Email</label>
                                    <input type="email" name="email" x-model="editEmail"
                                           class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                           placeholder="Alamat Email" required>
                                </div>

                                <!-- Role Selection Cards -->
                                <div>
                                    <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Peran / Hak Akses (Role)</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <!-- Opsi Pengawas -->
                                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all flex flex-col justify-between"
                                               :class="editRole === 'pengawas' ? 'border-sky-500 bg-sky-50/50 shadow-sm' : 'border-gray-100 bg-gray-50/50 hover:bg-gray-100/50'">
                                            <input type="radio" name="role" value="pengawas" class="sr-only" x-model="editRole">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="p-2 rounded-xl" :class="editRole === 'pengawas' ? 'bg-sky-500 text-white' : 'bg-gray-200 text-gray-600'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                </div>
                                                <span class="w-2.5 h-2.5 rounded-full" :class="editRole === 'pengawas' ? 'bg-sky-500 ring-4 ring-sky-200' : 'bg-gray-300'"></span>
                                            </div>
                                            <p class="font-bold text-sm text-gray-900">Pengawas</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-tight">Absensi & berita acara ujian</p>
                                        </label>

                                        <!-- Opsi Panitia -->
                                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition-all flex flex-col justify-between"
                                               :class="editRole === 'panitia' ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-gray-100 bg-gray-50/50 hover:bg-gray-100/50'">
                                            <input type="radio" name="role" value="panitia" class="sr-only" x-model="editRole">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="p-2 rounded-xl" :class="editRole === 'panitia' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-600'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                </div>
                                                <span class="w-2.5 h-2.5 rounded-full" :class="editRole === 'panitia' ? 'bg-emerald-600 ring-4 ring-emerald-200' : 'bg-gray-300'"></span>
                                            </div>
                                            <p class="font-bold text-sm text-gray-900">Panitia (Admin)</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-tight">Kelola semua data ujian</p>
                                        </label>
                                    </div>
                                </div>

                                <!-- Reset Password Info Box -->
                                <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-xs">
                                    <div class="flex items-center gap-2 font-bold mb-1">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Ganti Password (Opsional)</span>
                                    </div>
                                    <p class="text-amber-800/80">Biarkan kolom password di bawah kosong jika Anda tidak ingin mengubah password akun ini.</p>
                                </div>

                                <!-- Password Baru & Konfirmasi -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="group">
                                        <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Password Baru</label>
                                        <div class="relative">
                                            <input :type="showEditPassword ? 'text' : 'password'" name="password" 
                                                   class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                                   placeholder="Kosongkan jika tetap" minlength="6">
                                            <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                <svg x-show="!showEditPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                <svg x-show="showEditPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 ml-1">Ulangi Password Baru</label>
                                        <input :type="showEditPassword ? 'text' : 'password'" name="password_confirmation" 
                                               class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all shadow-inner" 
                                               placeholder="Ulangi password baru" minlength="6">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex gap-4">
                                <button type="button" @click="openEditModal = false" class="flex-1 py-4 rounded-2xl text-emerald-800 font-bold hover:bg-emerald-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-[2] py-4 rounded-2xl bg-amber-600 text-white font-bold shadow-lg shadow-amber-500/30 hover:bg-amber-700 hover:shadow-amber-600/40 hover:scale-[1.02] active:scale-95 transition-all">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- MODAL KONFIRMASI HAPUS (DELETE MODAL) -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="openDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;">
                
                <!-- Backdrop -->
                <div x-show="openDeleteModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-red-950/40 backdrop-blur-md transition-opacity" 
                     @click="openDeleteModal = false"></div>

                <!-- Panel -->
                <div x-show="openDeleteModal"
                     x-transition:enter="ease-[cubic-bezier(0.2,0,0,1)] duration-500"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                     class="relative w-full max-w-md transform overflow-hidden rounded-[2.5rem] bg-white border border-white/60 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.2)]">
                    
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 mx-auto flex items-center justify-center mb-5 shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </div>
                        
                        <h3 class="text-xl font-black text-gray-900 mb-2">Hapus Akun Pengguna?</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Apakah Anda yakin ingin menghapus akun <span class="font-bold text-gray-900" x-text="deleteName"></span>? Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <form :action="'{{ url('users') }}/' + deleteId" method="POST">
                            @csrf
                            @method('DELETE')

                            <div class="flex gap-3">
                                <button type="button" @click="openDeleteModal = false" class="flex-1 py-3.5 rounded-2xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="flex-1 py-3.5 rounded-2xl bg-red-600 text-white font-bold shadow-lg shadow-red-500/30 hover:bg-red-700 hover:shadow-red-600/40 hover:scale-[1.02] active:scale-95 transition-all">
                                    Ya, Hapus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
