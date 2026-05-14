<div class="flex flex-col items-center gap-2" id="ag-container-{{ $session->id }}">
    @if($session->allocation_status === 'Processing')
        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-400/20 text-yellow-700 border border-yellow-400/50 text-[10px] font-bold animate-pulse">
            <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        </div>
    @elseif($session->allocation_status === 'Completed')
        <div class="flex flex-col items-center">
            <span class="px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-700 border border-emerald-400/50 text-[10px] font-bold">
                ✓ Optimal
            </span>
            <form action="{{ route('sessions.generate.alokasi', $session->id) }}" method="POST" class="mt-1">
                @csrf
                <button type="submit" 
                        class="text-[9px] text-emerald-600 hover:text-emerald-800 underline font-semibold transition-all"
                        onclick="return confirm('Proses ini akan menimpa alokasi lama. Lanjutkan?')">
                    Re-Generate
                </button>
            </form>
        </div>
    @elseif($session->allocation_status === 'Failed')
        <div class="flex flex-col items-center">
            <span class="px-3 py-1 rounded-full bg-red-400/20 text-red-700 border border-red-400/50 text-[10px] font-bold">
                ⚠ Gagal
            </span>
            <form action="{{ route('sessions.generate.alokasi', $session->id) }}" method="POST" class="mt-1">
                @csrf
                <button type="submit" 
                        class="text-[9px] text-red-600 hover:text-red-800 underline font-semibold transition-all"
                        onclick="return confirm('Coba lagi proses alokasi?')">
                    Coba Lagi
                </button>
            </form>
        </div>
    @else
        {{-- Pending / Null --}}
        <form action="{{ route('sessions.generate.alokasi', $session->id) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="px-4 py-1.5 rounded-full bg-indigo-600/10 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-600/30 text-[10px] font-bold transition-all shadow-sm flex items-center gap-1 group"
                    onclick="return confirm('Mulai proses alokasi otomatis dengan Algoritma Genetika?')">
                <svg class="w-3 h-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Auto-Generate
            </button>
        </form>
    @endif

    @if($session->allocation_status === 'Processing')
    <script>
        (function() {
            let sessionId = {{ $session->id }};
            let interval = setInterval(function() {
                fetch("{{ route('sessions.status.ag', $session->id) }}")
                    .then(response => response.json())
                    .then(data => {
                        if(data.status === 'Completed' || data.status === 'Failed') {
                            clearInterval(interval);
                            window.location.reload(); 
                        }
                    })
                    .catch(err => console.error("Polling error:", err));
            }, 3000);
        })();
    </script>
    @endif
</div>
