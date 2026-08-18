<div x-cloak>
    <!-- A. MOBILE HEADER (Hanya muncul di HP) -->
    <div class="md:hidden flex items-center justify-between px-6 py-4 bg-emerald-900/90 backdrop-blur-xl text-white border-b border-white/10 shadow-lg fixed w-full z-50 top-0 transition-all duration-300">
        <div class="font-bold tracking-wider flex items-center gap-3">
            <img src="{{ asset('images/logo-sekolah.png') }}" class="h-10 w-auto object-contain drop-shadow-md" alt="Logo">
            <span class="text-sm font-bold tracking-wide">SMAN 3 BONTANG</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-white focus:outline-none hover:bg-white/10 p-2 rounded-xl transition">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </div>

    <!-- B. SIDEBAR DESKTOP -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col bg-emerald-900/85 backdrop-blur-2xl border-r border-white/10 text-white shadow-2xl transition-all duration-300 ease-in-out pt-20 md:pt-0 overflow-hidden group/sidebar"
           :class="{
               'translate-x-0': sidebarOpen,
               '-translate-x-full': !sidebarOpen,
               'md:translate-x-0': true,
               'w-64': !sidebarCollapsed,
               'w-24': sidebarCollapsed
           }">
        
        <!-- HEADER & TOGGLE -->
        <div class="hidden md:flex items-center h-24 border-b border-white/10 bg-black/10 backdrop-blur-sm relative transition-all duration-300"
             :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between px-5'">
            
            <!-- Logo Full (Expanded) -->
            <div class="flex items-center gap-3 font-bold tracking-widest text-white whitespace-nowrap overflow-hidden transition-all duration-300"
                 x-show="!sidebarCollapsed"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-x-2"
                 x-transition:enter-end="opacity-100 translate-x-0">
                
                <img src="{{ asset('images/logo-sekolah.png') }}" class="h-10 w-auto object-contain drop-shadow-lg hover:scale-110 transition-transform duration-300" alt="Logo">
                
                <div class="flex flex-col leading-tight">
                    <span class="text-[10px] text-emerald-300 font-medium tracking-wider">E-UJIAN</span>
                    <span class="font-bold text-sm font-figtree tracking-wide">SMAN 3 BONTANG</span>
                </div>
            </div>

            <!-- Logo Mini (Collapsed) -->
            <div x-show="sidebarCollapsed" class="absolute inset-0 flex items-center justify-center" x-transition.opacity>
                <img src="{{ asset('images/logo-sekolah.png') }}" class="h-10 w-auto object-contain drop-shadow-lg" alt="Logo" title="SMA Negeri 3 Bontang">
            </div>

            <!-- Toggle Button -->
            <button @click="toggleCollapse()" 
                    class="hidden md:flex items-center justify-center w-6 h-6 rounded-full bg-lime-500 text-emerald-900 border-2 border-emerald-900 shadow-lg absolute -right-3 top-1/2 transform -translate-y-1/2 hover:bg-white hover:scale-110 transition-all z-50">
                <svg class="w-3 h-3 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
        </div>

        <!-- MENU ITEMS -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 space-y-2 scrollbar-hide">
            
            <!-- Dashboard -->
            @php
                $dashboardUrl = Auth::user()->role === 'panitia' ? route('dashboard') : route('pengawas.dashboard');
                $dashboardActive = Auth::user()->role === 'panitia' ? request()->routeIs('dashboard') : request()->routeIs('pengawas.dashboard');
            @endphp
            <x-nav-link-glass :href="$dashboardUrl" :active="$dashboardActive" icon="home">
                <span class="whitespace-nowrap transition-opacity duration-200" 
                      :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Dashboard</span>
            </x-nav-link-glass>

            @if(Auth::user()->role === 'panitia')
                
                <!-- Divider 1: Manajemen & Transaksional Ujian -->
                <div class="mt-6 mb-2 text-[10px] font-extrabold text-emerald-300/60 uppercase tracking-widest transition-all duration-300 whitespace-nowrap overflow-hidden flex items-center"
                     :class="sidebarCollapsed ? 'justify-center px-0' : 'px-6'">
                    <span x-show="!sidebarCollapsed">Manajemen Ujian</span>
                    <span x-show="sidebarCollapsed" class="h-0.5 w-4 bg-emerald-300/30 rounded-full"></span>
                </div>

                <!-- Wizard: Buat Ujian Baru -->
                <x-nav-link-glass :href="route('wizard.step1')" :active="request()->routeIs('wizard.*')" icon="sparkles" isSpecial="true">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Buat Ujian Baru</span>
                </x-nav-link-glass>

                <!-- Riwayat Ujian -->
                <x-nav-link-glass :href="route('sessions.index')" :active="request()->routeIs('sessions.index') || request()->routeIs('sessions.edit')" icon="archive">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Riwayat Ujian</span>
                </x-nav-link-glass>

                <!-- Jadwal & Pengawas -->
                <x-nav-link-glass :href="route('schedules.selection')" :active="request()->routeIs('schedules.selection') || request()->routeIs('sessions.schedules.*')" icon="calendar">
                    <span class="whitespace-nowrap transition-opacity duration-200 flex items-center justify-between w-full" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">
                        <span>Jadwal & Pengawas</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-white/20 text-white font-bold shadow-sm border border-white/10" x-show="!sidebarCollapsed">
                            {{ \App\Models\ExamSession::count() }}
                        </span>
                    </span>
                </x-nav-link-glass>

                <!-- Divider 2: Data Master / Bank Data Akademik -->
                <div class="mt-6 mb-2 text-[10px] font-extrabold text-emerald-300/60 uppercase tracking-widest transition-all duration-300 whitespace-nowrap overflow-hidden flex items-center"
                     :class="sidebarCollapsed ? 'justify-center px-0' : 'px-6'">
                    <span x-show="!sidebarCollapsed">Data Akademik</span>
                    <span x-show="sidebarCollapsed" class="h-0.5 w-4 bg-emerald-300/30 rounded-full"></span>
                </div>

                <!-- Tingkatan Kelas (Levels) -->
                <x-nav-link-glass :href="route('levels.index')" :active="request()->routeIs('levels.*')" icon="users">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Tingkatan Kelas</span>
                </x-nav-link-glass>

                <!-- Kelas (Classes) -->
                <x-nav-link-glass :href="route('student_classes.index')" :active="request()->routeIs('student_classes.*')" icon="users">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Data Kelas</span>
                </x-nav-link-glass>

                <!-- Mata Pelajaran (Subjects) -->
                <x-nav-link-glass :href="route('subjects.index')" :active="request()->routeIs('subjects.*')" icon="sparkles">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Mata Pelajaran</span>
                </x-nav-link-glass>

                <!-- Sesi Waktu (Time Sessions) -->
                <x-nav-link-glass :href="route('time_sessions.index')" :active="request()->routeIs('time_sessions.*')" icon="calendar">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Sesi Waktu</span>
                </x-nav-link-glass>

                <!-- Master Ruangan -->
                <x-nav-link-glass :href="route('master_rooms.index')" :active="request()->routeIs('master_rooms.*')" icon="archive">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Master Ruangan</span>
                </x-nav-link-glass>

                <!-- Data Siswa -->
                <x-nav-link-glass :href="route('students.index')" :active="request()->routeIs('students.*')" icon="users">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Data Siswa</span>
                </x-nav-link-glass>

                <!-- Manajemen User (Panitia & Pengawas) -->
                <x-nav-link-glass :href="route('users.index')" :active="request()->routeIs('users.*')" icon="shield">
                    <span class="whitespace-nowrap transition-opacity duration-200" 
                          :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Manajemen User</span>
                </x-nav-link-glass>

            @endif

            <!-- Divider -->
            <div class="mt-6 mb-2 text-[10px] font-extrabold text-emerald-300/60 uppercase tracking-widest transition-all duration-300 whitespace-nowrap overflow-hidden flex items-center"
                 :class="sidebarCollapsed ? 'justify-center px-0' : 'px-6'">
                <span x-show="!sidebarCollapsed">Pengaturan</span>
                <span x-show="sidebarCollapsed" class="h-0.5 w-4 bg-emerald-300/30 rounded-full"></span>
            </div>

            <!-- Profil -->
            <x-nav-link-glass :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="users">
                <span class="whitespace-nowrap transition-opacity duration-200" 
                      :class="sidebarCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'ml-3 opacity-100'">Profil Saya</span>
            </x-nav-link-glass>

        </nav>

        <!-- USER PROFILE BOTTOM -->
        <div class="border-t border-white/10 bg-black/20 backdrop-blur-md transition-all duration-300"
             :class="sidebarCollapsed ? 'p-3' : 'p-4'">
            
            <div class="flex items-center gap-3 transition-all duration-300"
                 :class="sidebarCollapsed ? 'flex-col justify-center' : 'flex-row'">
                
                <a href="{{ route('profile.edit') }}" class="flex-shrink-0 relative group cursor-pointer">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-lime-400 to-emerald-600 flex items-center justify-center text-white font-bold border-2 border-white/30 shadow-lg group-hover:scale-105 transition-transform overflow-hidden">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-emerald-900 rounded-full animate-pulse"></div>
                </a>

                <div class="flex-1 overflow-hidden whitespace-nowrap" 
                     :class="sidebarCollapsed ? 'w-0 opacity-0 h-0' : 'w-auto opacity-100'">
                    <p class="text-sm font-bold text-white truncate" title="{{ Auth::user()->name }}">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[10px] text-emerald-300 uppercase tracking-wide font-semibold">
                        {{ Auth::user()->role }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="rounded-xl bg-white/5 hover:bg-red-500 hover:text-white text-emerald-200 transition-all border border-white/10 hover:border-red-400 shadow-sm flex items-center justify-center group/logout"
                            :class="sidebarCollapsed ? 'w-10 h-10 mt-2' : 'p-2'"
                            title="Keluar Aplikasi">
                        <svg class="w-5 h-5 opacity-70 group-hover/logout:opacity-100 transition-transform group-hover/logout:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>