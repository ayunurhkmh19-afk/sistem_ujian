# **DEKOMPOSISI TAHAP 1-3: SETUP AWAL & DASHBOARD PENGAWAS UJIAN**

**Sistem Informasi Penjadwalan & Pelaksanaan Ujian (Studi Kasus: SMAN 3 Bontang)**

Dokumen ini merupakan panduan implementasi teknis mendetail bagi pengembang untuk membangun fondasi awal (Database, Middleware, Routing) dan antarmuka utama (Dashboard) untuk Role Pengawas Ujian.

## **TAHAP 1: Persiapan Database & Relasi Model (Fase Fondasi)**

Fase ini bertujuan untuk menyiapkan struktur penyimpanan agar sistem dapat mengenali role pengawas dan menyimpan jadwal penugasannya.

### **1.1 Update Tabel users**

Kita harus memperbarui tipe data ENUM pada kolom role di tabel users agar menerima nilai 'Pengawas'.

* **Perintah Terminal:** php artisan make:migration update\_role\_column\_in\_users\_table  
* **Kode Migrasi (up method):**  
  public function up()  
  {  
      Schema::table('users', function (Blueprint $table) {  
          // Memperbarui enum untuk memasukkan 'Pengawas'  
          // Catatan: Jika menggunakan doctrine/dbal, gunakan cara statement SQL mentah  
          DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Panitia', 'Pengawas', 'Siswa') NOT NULL DEFAULT 'Siswa'");  
      });  
  }

### **1.2 Membuat Tabel Pivot room\_supervisors**

Tabel ini memetakan Pengawas ke Sesi Ujian dan Ruangan secara spesifik.

* **Perintah Terminal:** php artisan make:migration create\_room\_supervisors\_table  
* **Kode Migrasi (up method):**  
  public function up()  
  {  
      Schema::create('room\_supervisors', function (Blueprint $table) {  
          $table-\>id();  
          // Relasi ke tabel pengguna (hanya yang ber-role Pengawas)  
          $table-\>foreignId('user\_id')-\>constrained('users')-\>onDelete('cascade');   
          // Relasi ke tabel sesi ujian  
          $table-\>foreignId('exam\_session\_id')-\>constrained('exam\_sessions')-\>onDelete('cascade');  
          // Relasi ke ruangan yang diawasi  
          $table-\>foreignId('room\_id')-\>constrained('rooms')-\>onDelete('cascade');  
          $table-\>timestamps();  
      });  
  }

### **1.3 Setup Eloquent Models & Relationships**

Tambahkan relasi pada Model agar pemanggilan data di Controller bisa menggunakan teknik *Eager Loading* untuk mencegah masalah *N+1 Query*.

* **Model User.php:**  
  // Relasi: Pengawas memiliki banyak jadwal pengawasan  
  public function pengawasan()  
  {  
      return $this-\>hasMany(RoomSupervisor::class, 'user\_id');  
  }

* **Model RoomSupervisor.php (Model Baru):**  
  *Perintah: php artisan make:model RoomSupervisor*  
  class RoomSupervisor extends Model  
  {  
      protected $fillable \= \['user\_id', 'exam\_session\_id', 'room\_id'\];

      public function user() {   
          return $this-\>belongsTo(User::class);   
      }  
      public function examSession() {   
          return $this-\>belongsTo(ExamSession::class);   
      }  
      public function room() {   
          return $this-\>belongsTo(Room::class);   
      }  
  }

## **TAHAP 2: Keamanan, Middleware & Routing (Fase Aksesibilitas)**

Fase ini akan membatasi akses sehingga URL pengawas tidak bisa dibongkar oleh Panitia maupun Siswa.

### **2.1 Pembuatan Middleware RolePengawas**

* **Perintah Terminal:** php artisan make:middleware RolePengawas  
* **Logika Kode (app/Http/Middleware/RolePengawas.php):**  
  namespace App\\Http\\Middleware;

  use Closure;  
  use Illuminate\\Http\\Request;  
  use Illuminate\\Support\\Facades\\Auth;

  class RolePengawas  
  {  
      public function handle(Request $request, Closure $next)  
      {  
          // Pastikan user sudah login dan memiliki role 'Pengawas'  
          if (Auth::check() && Auth::user()-\>role \=== 'Pengawas') {  
              return $next($request);  
          }

          // Lemparkan error 403 Forbidden jika mencoba mengakses  
          abort(403, 'Akses Ditolak. Halaman ini khusus untuk Pengawas Ujian.');  
      }  
  }

* **Registrasi Middleware:** Daftarkan alias middleware ini di app/Http/Kernel.php pada array $routeMiddleware dengan nama 'role.pengawas'.

### **2.2 Definisi Routing (routes/web.php)**

Buat grup *route* khusus pengawas yang dilindungi middleware.

use App\\Http\\Controllers\\Pengawas\\DashboardController;  
use App\\Http\\Controllers\\Pengawas\\SessionController;

// Group Route Pengawas  
Route::middleware(\['auth', 'role.pengawas'\])-\>prefix('pengawas')-\>name('pengawas.')-\>group(function () {  
      
    // Halaman Dashboard Utama  
    Route::get('/dashboard', \[DashboardController::class, 'index'\])-\>name('dashboard');  
      
    // Halaman Detail Sesi (Menampilkan List Siswa & Info Sesi)  
    Route::get('/sesi/{session\_id}/ruang/{room\_id}', \[SessionController::class, 'show'\])-\>name('sesi.detail');

});

## **TAHAP 3: Implementasi Dashboard & Status Sesi (Fase Pemantauan)**

Fase ini berfokus pada logika *Backend* dan *Frontend* untuk menampilkan halaman awal (Dashboard) saat Pengawas berhasil login.

### **3.1 Controller Dashboard (Pengawas/DashboardController.php)**

Di sini kita akan mengambil jadwal dan mengkalkulasi status setiap sesi secara *real-time* menggunakan *library* Carbon.

* **Perintah Terminal:** php artisan make:controller Pengawas/DashboardController  
* **Logika Kode:**  
  namespace App\\Http\\Controllers\\Pengawas;

  use App\\Http\\Controllers\\Controller;  
  use App\\Models\\RoomSupervisor;  
  use Carbon\\Carbon;  
  use Illuminate\\Support\\Facades\\Auth;

  class DashboardController extends Controller  
  {  
      public function index()  
      {  
          $now \= Carbon::now();

          // 1\. Ambil jadwal milik pengawas login, eager load relasi agar query efisien  
          $semuaJadwal \= RoomSupervisor::with(\['examSession.schedules', 'room'\])  
              \-\>where('user\_id', Auth::id())  
              \-\>get();

          // 2\. Mapping data untuk menyuntikkan properti 'status\_sesi'  
          $jadwalDiolah \= $semuaJadwal-\>map(function ($jadwal) use ($now) {  
              $schedule \= $jadwal-\>examSession-\>schedules-\>first(); // Ambil jadwal mapel terkait

              if (\!$schedule) {  
                  $jadwal-\>status\_sesi \= 'Belum Dijadwalkan';  
                  return $jadwal;  
              }

              // Gabungkan tanggal dan waktu dari database menjadi objek Carbon  
              $startDateTime \= Carbon::parse($schedule-\>exam\_date . ' ' . $schedule-\>start\_time);  
              $endDateTime \= Carbon::parse($schedule-\>exam\_date . ' ' . $schedule-\>end\_time);

              // Kondisional Status Sesi  
              if ($now-\>lt($startDateTime)) {  
                  $jadwal-\>status\_sesi \= 'Akan Berlangsung';  
              } elseif ($now-\>between($startDateTime, $endDateTime)) {  
                  $jadwal-\>status\_sesi \= 'Sedang Berlangsung';  
              } else {  
                  $jadwal-\>status\_sesi \= 'Sudah Berlangsung';  
              }

              return $jadwal;  
          });

          // 3\. Kalkulasi data untuk Quick Menu  
          $countHariIni \= $jadwalDiolah-\>filter(function($j) use ($now) {  
              $jadwalDate \= $j-\>examSession-\>schedules-\>first()-\>exam\_date ?? null;  
              return $jadwalDate ? Carbon::parse($jadwalDate)-\>isToday() : false;  
          })-\>count();

          $countRiwayat \= $jadwalDiolah-\>where('status\_sesi', 'Sudah Berlangsung')-\>count();

          // 4\. Return ke view Blade  
          return view('pengawas.dashboard', compact('jadwalDiolah', 'countHariIni', 'countRiwayat'));  
      }  
  }

### **3.2 View Dashboard (resources/views/pengawas/dashboard.blade.php)**

Tampilan ini akan merender *Quick Menu* dan *Tabel Manajemen Sesi*. (Contoh menggunakan class Tailwind CSS yang umum digunakan di ekosistem Laravel).

@extends('layouts.app')

@section('content')  
\<div class="container mx-auto px-4 py-6"\>  
    \<h1 class="text-2xl font-bold text-gray-800 mb-6"\>Dashboard Pengawas\</h1\>

    \<\!-- 1\. QUICK MENU (Akses Cepat) \--\>  
    \<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8"\>  
          
        \<\!-- Card: Jadwal Hari Ini \--\>  
        \<div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500"\>  
            \<div class="flex justify-between items-center"\>  
                \<div\>  
                    \<p class="text-sm text-gray-500 font-medium"\>Jadwal Ujian Hari Ini\</p\>  
                    \<p class="text-3xl font-bold text-gray-800"\>{{ $countHariIni }} Sesi\</p\>  
                \</div\>  
            \</div\>  
        \</div\>

        \<\!-- Card: Riwayat Selesai \--\>  
        \<div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500"\>  
            \<div class="flex justify-between items-center"\>  
                \<div\>  
                    \<p class="text-sm text-gray-500 font-medium"\>Riwayat Pengawasan Selesai\</p\>  
                    \<p class="text-3xl font-bold text-gray-800"\>{{ $countRiwayat }} Sesi\</p\>  
                \</div\>  
            \</div\>  
        \</div\>

        \<\!-- Card: Panduan SOP \--\>  
        \<div class="bg-emerald-600 rounded-lg shadow p-6 text-white hover:bg-emerald-700 transition cursor-pointer"\>  
            \<div class="flex items-center gap-3"\>  
                \<div\>  
                    \<h3 class="text-lg font-bold"\>Panduan Pengawas\</h3\>  
                    \<p class="text-sm opacity-90"\>Baca tata tertib pelaksanaan ujian\</p\>  
                \</div\>  
            \</div\>  
        \</div\>  
    \</div\>

    \<\!-- 2\. TABEL JADWAL & MANAJEMEN SESI SAYA \--\>  
    \<div class="bg-white rounded-lg shadow overflow-hidden"\>  
        \<div class="px-6 py-4 border-b"\>  
            \<h3 class="text-lg font-bold text-gray-800"\>Manajemen Sesi Ujian Saya\</h3\>  
        \</div\>  
        \<table class="min-w-full"\>  
            \<thead class="bg-gray-50"\>  
                \<tr\>  
                    \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Kegiatan / Tanggal\</th\>  
                    \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Ruangan\</th\>  
                    \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Status\</th\>  
                    \<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"\>Aksi\</th\>  
                \</tr\>  
            \</thead\>  
            \<tbody class="divide-y divide-gray-200"\>  
                @forelse($jadwalDiolah as $item)  
                \<tr\>  
                    \<td class="px-6 py-4"\>  
                        \<div class="font-bold text-gray-900"\>{{ $item-\>examSession-\>title }}\</div\>  
                        \<div class="text-sm text-gray-500"\>  
                            {{ $item-\>examSession-\>schedules-\>first()-\>exam\_date ?? '-' }} \<br\>  
                            {{ $item-\>examSession-\>schedules-\>first()-\>start\_time ?? '' }} \- {{ $item-\>examSession-\>schedules-\>first()-\>end\_time ?? '' }}  
                        \</div\>  
                    \</td\>  
                    \<td class="px-6 py-4 text-sm text-gray-900"\>  
                        {{ $item-\>room-\>name }}  
                    \</td\>  
                    \<td class="px-6 py-4"\>  
                        \<\!-- Indikator Status \--\>  
                        @if($item-\>status\_sesi \== 'Sudah Berlangsung')  
                            \<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold"\>Sudah Berlangsung\</span\>  
                        @elseif($item-\>status\_sesi \== 'Sedang Berlangsung')  
                            \<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold animate-pulse"\>Sedang Berlangsung\</span\>  
                        @else  
                            \<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold"\>Akan Berlangsung\</span\>  
                        @endif  
                    \</td\>  
                    \<td class="px-6 py-4"\>  
                        \<a href="{{ route('pengawas.sesi.detail', \['session\_id' \=\> $item-\>exam\_session\_id, 'room\_id' \=\> $item-\>room\_id\]) }}"   
                           class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-sm font-medium transition"\>  
                           Lihat Detail  
                        \</a\>  
                    \</td\>  
                \</tr\>  
                @empty  
                \<tr\>  
                    \<td colspan="4" class="px-6 py-8 text-center text-gray-500"\>Belum ada penugasan pengawasan ujian untuk Anda.\</td\>  
                \</tr\>  
                @endforelse  
            \</tbody\>  
        \</table\>  
    \</div\>  
\</div\>  
@endsection  
