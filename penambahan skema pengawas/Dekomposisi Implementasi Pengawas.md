# **DEKOMPOSISI TAHAP IMPLEMENTASI (DETAIL): SETUP AWAL & DASHBOARD PENGAWAS**

Dokumen ini merincikan langkah-langkah teknis secara mendalam (disertai contoh *snippet* kode) untuk mengimplementasikan fondasi fitur Pengawas Ujian pada Sistem Informasi Ujian SMAN 3 Bontang. Fokus pada dokumen ini adalah setup *database*, pengambilan data di *Controller*, dan penyusunan UI *Dashboard* beserta *Quick Menu*\-nya.

## **TAHAP 1: Persiapan Database & Model (Fase Fondasi)**

### **1.1 Update Tabel users (Migration)**

Kita perlu mengubah kolom role yang sudah ada agar mengenali role 'Pengawas'.

* **Perintah Terminal:** php artisan make:migration update\_role\_column\_in\_users\_table  
* **Kode Migrasi (up method):**  
  Schema::table('users', function (Blueprint $table) {  
      // Mengubah definisi enum untuk menyertakan 'Pengawas'  
      $table-\>enum('role', \['Panitia', 'Siswa', 'Pengawas'\])-\>default('Siswa')-\>change();  
  });

### **1.2 Pembuatan Tabel Pivot room\_supervisors**

Tabel ini memetakan "Siapa mengawasi ujian apa, dan di ruangan mana".

* **Perintah Terminal:** php artisan make:migration create\_room\_supervisors\_table  
* **Kode Migrasi (up method):**  
  Schema::create('room\_supervisors', function (Blueprint $table) {  
      $table-\>id();  
      $table-\>foreignId('user\_id')-\>constrained('users')-\>onDelete('cascade'); // ID Pengawas  
      $table-\>foreignId('exam\_session\_id')-\>constrained('exam\_sessions')-\>onDelete('cascade');  
      $table-\>foreignId('room\_id')-\>constrained('rooms')-\>onDelete('cascade');  
      $table-\>timestamps();  
  });

### **1.3 Pembaruan Eloquent Models**

Mendefinisikan relasi agar Controller mudah melakukan *Eager Loading* data jadwal.

* **Di dalam app/Models/User.php:**  
  public function pengawasan() {  
      return $this-\>hasMany(RoomSupervisor::class, 'user\_id');  
  }

* **Buat Model Baru (app/Models/RoomSupervisor.php):**  
  class RoomSupervisor extends Model {  
      protected $fillable \= \['user\_id', 'exam\_session\_id', 'room\_id'\];

      public function examSession() { return $this-\>belongsTo(ExamSession::class); }  
      public function room() { return $this-\>belongsTo(Room::class); }  
  }

## **TAHAP 2: Logika Backend & Status Sesi (Fase Controller)**

Kita membutuhkan Controller untuk mengolah data jadwal dari tabel room\_supervisors dan menghitung status secara *real-time* sebelum dikirim ke Dashboard.

### **2.1 Pembuatan PengawasDashboardController**

* **Perintah Terminal:** php artisan make:controller Pengawas/DashboardController  
* **Logika Kode (index method):**  
  namespace App\\Http\\Controllers\\Pengawas;  
  use App\\Http\\Controllers\\Controller;  
  use App\\Models\\RoomSupervisor;  
  use Carbon\\Carbon;  
  use Illuminate\\Support\\Facades\\Auth;

  class DashboardController extends Controller  
  {  
      public function index()  
      {  
          $pengawasId \= Auth::id();  
          $now \= Carbon::now();

          // 1\. Ambil semua jadwal penugasan pengawas ini  
          $semuaJadwal \= RoomSupervisor::with(\['examSession.schedules', 'room'\])  
              \-\>where('user\_id', $pengawasId)  
              \-\>get();

          // 2\. Olah data untuk menyuntikkan "Status Sesi"  
          $jadwalDiolah \= $semuaJadwal-\>map(function ($jadwal) use ($now) {  
              $schedule \= $jadwal-\>examSession-\>schedules-\>first(); // Ambil jadwal mapel pertama/terkait

              if (\!$schedule) {  
                  $jadwal-\>status\_sesi \= 'Belum Dijadwalkan';  
                  return $jadwal;  
              }

              $startDateTime \= Carbon::parse($schedule-\>exam\_date . ' ' . $schedule-\>start\_time);  
              $endDateTime \= Carbon::parse($schedule-\>exam\_date . ' ' . $schedule-\>end\_time);

              if ($now-\>lt($startDateTime)) {  
                  $jadwal-\>status\_sesi \= 'Akan Berlangsung';  
              } elseif ($now-\>between($startDateTime, $endDateTime)) {  
                  $jadwal-\>status\_sesi \= 'Sedang Berlangsung';  
              } else {  
                  $jadwal-\>status\_sesi \= 'Sudah Berlangsung';  
              }

              return $jadwal;  
          });

          // 3\. Siapkan Data untuk Quick Menu Card  
          $countHariIni \= $jadwalDiolah-\>filter(function($j) use ($now) {  
              return Carbon::parse($j-\>examSession-\>schedules-\>first()-\>exam\_date ?? '')-\>isToday();  
          })-\>count();

          $countRiwayat \= $jadwalDiolah-\>where('status\_sesi', 'Sudah Berlangsung')-\>count();

          return view('pengawas.dashboard', compact('jadwalDiolah', 'countHariIni', 'countRiwayat'));  
      }  
  }

## **TAHAP 3: Desain Antarmuka Dashboard (Fase UI \- Blade Template)**

Membuat tampilan resources/views/pengawas/dashboard.blade.php. Desain ini dibagi menjadi dua area utama: **Quick Menu** dan **Tabel Jadwal**.

### **3.1 Struktur HTML / Tailwind untuk "Quick Menu"**

Quick Menu dirancang dalam bentuk kotak-kotak (Cards) besar di bagian atas agar informatif dan interaktif.

\<\!-- Bagian Quick Menu \--\>  
\<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8"\>  
      
    \<\!-- Card 1: Jadwal Hari Ini \--\>  
    \<div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500"\>  
        \<div class="flex items-center justify-between"\>  
            \<div\>  
                \<p class="text-gray-500 text-sm font-medium"\>Jadwal Ujian Hari Ini\</p\>  
                \<p class="text-3xl font-bold text-gray-800"\>{{ $countHariIni }} Sesi\</p\>  
            \</div\>  
            \<div class="bg-green-100 p-3 rounded-full"\>  
                \<\!-- Icon Calendar (Heroicons) \--\>  
                \<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"\>\<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"\>\</path\>\</svg\>  
            \</div\>  
        \</div\>  
        \<div class="mt-4"\>  
            \<a href="\#tabel-jadwal" class="text-green-600 text-sm font-semibold hover:underline"\>Lihat Jadwal ↓\</a\>  
        \</div\>  
    \</div\>

    \<\!-- Card 2: Riwayat Pengawasan \--\>  
    \<div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500"\>  
        \<div class="flex items-center justify-between"\>  
            \<div\>  
                \<p class="text-gray-500 text-sm font-medium"\>Riwayat Selesai\</p\>  
                \<p class="text-3xl font-bold text-gray-800"\>{{ $countRiwayat }} Sesi\</p\>  
            \</div\>  
            \<div class="bg-blue-100 p-3 rounded-full"\>  
                \<\!-- Icon Check-Circle \--\>  
                \<svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"\>\<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"\>\</path\>\</svg\>  
            \</div\>  
        \</div\>  
        \<div class="mt-4"\>  
            \<a href="\#" class="text-blue-600 text-sm font-semibold hover:underline"\>Lihat Riwayat & Laporan →\</a\>  
        \</div\>  
    \</div\>

    \<\!-- Card 3: Panduan SOP \--\>  
    \<div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg shadow p-6 text-white cursor-pointer hover:shadow-lg transition" onclick="bukaModalPanduan()"\>  
        \<div class="flex items-center gap-4"\>  
            \<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"\>\<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"\>\</path\>\</svg\>  
            \<div\>  
                \<h3 class="text-lg font-bold"\>Panduan Pengawas\</h3\>  
                \<p class="text-sm opacity-90"\>Klik untuk membaca SOP Pelaksanaan Ujian\</p\>  
            \</div\>  
        \</div\>  
    \</div\>

\</div\>

### **3.2 Struktur HTML / Tailwind untuk "Tabel Manajemen Sesi Saya"**

Di bawah *Quick Menu*, kita tampilkan perulangan data (list jadwal) menggunakan DataTables atau tabel HTML standar dengan pewarnaan otomatis pada status.

\<\!-- Bagian Tabel Jadwal \--\>  
\<div id="tabel-jadwal" class="bg-white rounded-lg shadow overflow-hidden"\>  
    \<div class="px-6 py-4 border-b border-gray-200"\>  
        \<h3 class="text-lg font-bold text-gray-800"\>Manajemen Sesi Ujian Saya\</h3\>  
    \</div\>  
      
    \<table class="min-w-full divide-y divide-gray-200"\>  
        \<thead class="bg-gray-50"\>  
            \<tr\>  
                \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Sesi Ujian / Tanggal\</th\>  
                \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Ruangan\</th\>  
                \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Status\</th\>  
                \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Aksi\</th\>  
            \</tr\>  
        \</thead\>  
        \<tbody class="bg-white divide-y divide-gray-200"\>  
            @forelse($jadwalDiolah as $item)  
            \<tr\>  
                \<\!-- Kolom Nama Sesi & Waktu \--\>  
                \<td class="px-6 py-4"\>  
                    \<div class="font-bold text-gray-900"\>{{ $item-\>examSession-\>title }}\</div\>  
                    \<div class="text-sm text-gray-500"\>  
                        {{ $item-\>examSession-\>schedules-\>first()-\>exam\_date ?? '-' }}   
                        ({{ $item-\>examSession-\>schedules-\>first()-\>start\_time ?? '' }} \- {{ $item-\>examSession-\>schedules-\>first()-\>end\_time ?? '' }})  
                    \</div\>  
                \</td\>  
                  
                \<\!-- Kolom Ruangan \--\>  
                \<td class="px-6 py-4 text-sm text-gray-900"\>  
                    {{ $item-\>room-\>name }}  
                \</td\>  
                  
                \<\!-- Kolom Status (Dengan logika warna badge) \--\>  
                \<td class="px-6 py-4"\>  
                    @if($item-\>status\_sesi \== 'Sudah Berlangsung')  
                        \<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"\>Sudah Berlangsung\</span\>  
                    @elseif($item-\>status\_sesi \== 'Sedang Berlangsung')  
                        \<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 animate-pulse"\>Sedang Berlangsung\</span\>  
                    @else  
                        \<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"\>Akan Berlangsung\</span\>  
                    @endif  
                \</td\>  
                  
                \<\!-- Kolom Aksi \--\>  
                \<td class="px-6 py-4 text-sm font-medium"\>  
                    \<a href="{{ route('pengawas.sesi.detail', $item-\>exam\_session\_id) }}" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded"\>Lihat Detail\</a\>  
                \</td\>  
            \</tr\>  
            @empty  
            \<tr\>  
                \<td colspan="4" class="px-6 py-4 text-center text-gray-500"\>Belum ada jadwal penugasan pengawasan untuk Anda.\</td\>  
            \</tr\>  
            @endforelse  
        \</tbody\>  
    \</table\>  
\</div\>

Melalui dekomposisi mendalam ini, *programmer* tidak perlu lagi meraba-raba logika untuk *Quick Menu* (berapa jumlah *query* yang digunakan) atau bagaimana *Badge Status* diubah warnanya. Semua langsung siap diimplementasikan dalam struktur *Model-View-Controller* Laravel.