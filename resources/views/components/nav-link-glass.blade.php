@props(['active', 'icon' => 'home', 'isSpecial' => false])

@php
// Base classes untuk container link
// Kita tambahkan 'w-full' agar link memenuhi lebar sidebar
$baseClasses = 'group flex items-center relative overflow-hidden transition-all duration-300 mb-1 w-full ';

// Styling Aktif vs Tidak Aktif
if ($active) {
    $baseClasses .= $isSpecial 
        ? 'bg-gradient-to-r from-lime-500/30 to-emerald-500/30 border border-lime-400/40 text-white shadow-[0_0_20px_rgba(132,204,22,0.3)] rounded-2xl ' 
        : 'bg-white/20 shadow-[0_4px_30px_rgba(0,0,0,0.1)] border border-white/20 text-white rounded-2xl ';
} else {
    $baseClasses .= 'text-emerald-100/80 hover:bg-white/10 hover:text-white hover:shadow-lg border border-transparent rounded-xl ';
}

// Daftar Ikon SVG
$icons = [
    'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
    'archive' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />',
    'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
];
@endphp

<a {{ $attributes->merge(['class' => $baseClasses]) }}
   :class="sidebarCollapsed ? 'justify-center px-0 py-3' : 'px-4 py-3'"
   :title="sidebarCollapsed ? '{{ strip_tags($slot) }}' : ''">

    <!-- Efek Kilau saat Hover -->
    <div class="absolute inset-0 bg-white/5 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000 ease-in-out"></div>

    <!-- IKON SVG -->
    <!-- Perbaikan: Margin kanan (mr-3) hanya ada jika sidebar TIDAK collapsed -->
    <!-- mx-auto digunakan saat collapsed untuk memaksa ikon ke tengah -->
    <svg class="h-5 w-5 flex-shrink-0 transition-all duration-300 {{ $active ? 'text-lime-300' : 'text-emerald-300 group-hover:text-lime-200' }}"
         :class="sidebarCollapsed ? 'mx-auto' : 'mr-3'" 
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $icons[$icon] ?? $icons['home'] !!}
    </svg>

    <!-- Teks Menu -->
    <!-- Perbaikan: Dibungkus span agar bisa dikontrol opacity dan width-nya dengan mulus -->
    <span class="whitespace-nowrap transition-all duration-300 origin-left"
          :class="sidebarCollapsed ? 'w-0 opacity-0 scale-0 hidden' : 'w-auto opacity-100 scale-100 block'">
        {{ $slot }}
    </span>
    
    <!-- Indikator Aktif (Titik Hijau) -->
    @if($active)
        <span class="absolute right-3 w-1.5 h-1.5 rounded-full bg-lime-400 shadow-[0_0_10px_#a3e635]" 
              x-show="!sidebarCollapsed"></span>
    @endif
</a>