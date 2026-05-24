# **DEKOMPOSISI TAHAP LANJUTAN: MANAJEMEN KEPENGAWASAN & MONITORING (ROLE PANITIA)**

**Sistem Informasi Penjadwalan Ujian SMAN 3 Bontang**

Dokumen ini merincikan bagaimana Role Panitia menugaskan Pengawas ke dalam jadwal yang telah dibuat oleh Algoritma Genetika (AG), serta bagaimana Panitia memantau hasil Absensi dan Berita Acara yang disubmit oleh Pengawas.

## **1\. PENYESUAIAN SKEMA DATABASE (KONSEKUENSI V2)**

Karena jadwal kini dipecah per jam/mapel (exam\_schedules), tabel laporan dari Pengawas juga harus merujuk ke tabel ini.

* **Modifikasi Tabel student\_attendances & exam\_reports:**  
  Hapus kolom exam\_session\_id lalu ganti dengan exam\_schedule\_id.  
  *(Perintah: php artisan make:migration update\_reports\_to\_schedule\_id)*

## **2\. ALUR KERJA MANAJEMEN PENUGASAN (ASSIGNMENT)**

Setelah Panitia mengklik tombol "Lihat Hasil Jadwal" pasca-eksekusi Algoritma Genetika, Panitia akan diarahkan ke halaman **Matriks Jadwal & Penugasan**.

### **2.1 Konsep Antarmuka (UI Matrix)**

Sistem merender tabel bersarang (nested table) untuk memudahkan panitia:

* **Level 1 (Tanggal & Sesi):** Menampilkan Hari dan Jam (Misal: Senin, 09.00 \- 11.00).  
* **Level 2 (Mata Pelajaran):** Menampilkan Mapel yang diujikan (Misal: MTK Kelas 10).  
* **Level 3 (Ruangan & Pengawas):** Menampilkan Ruang 1, Ruang 2, dst. Di sebelah nama ruangan, terdapat *Dropdown Select* berisi daftar nama akun yang memiliki role 'Pengawas'.

### **2.2 Logika *Backend* Penugasan (SupervisorController@assign)**

* **Controller:** app/Http/Controllers/Panitia/SupervisorController.php  
* **Logika Simpan:**  
  Menggunakan metode updateOrCreate pada model RoomSupervisor agar jika Panitia salah pilih pengawas, ia cukup mengganti di *dropdown* dan menyimpannya lagi tanpa duplikasi data.

## **3\. ALUR KERJA MONITORING (ABSENSI & BERITA ACARA)**

Saat ujian sedang atau sudah berlangsung, Panitia memiliki akses untuk memantau kondisi setiap ruangan secara *real-time*.

### **3.1 Status Indikator di Dashboard Panitia**

Pada halaman Matriks Jadwal, tambahkan kolom aksi khusus monitoring. Tombol ini memiliki badge status:

* ⚪ **Belum Mulai** (Ujian belum jadwalnya).  
* 🟡 **Menunggu Laporan** (Ujian selesai, tapi pengawas belum submit Berita Acara).  
* 🟢 **Laporan Diterima** (Pengawas sudah submit).

### **3.2 Halaman Detail Laporan (View Only)**

Ketika Panitia mengklik tombol "Lihat Laporan", Panitia akan dibawa ke halaman detail yang menampilkan:

1. **Informasi Jadwal:** Mapel, Waktu, Ruang, Nama Pengawas.  
2. **Rekap Kehadiran (Read-Only):** Tabel daftar siswa di ruangan tersebut beserta statusnya (Hadir/Sakit/Izin/Alpa). *Radio button disabled*.  
3. **Berita Acara (Read-Only):** Menampilkan catatan kejadian/insiden yang ditulis pengawas.

## **4\. IMPLEMENTASI KODE (CODE SNIPPETS)**

### **A. Route Definition (routes/web.php \- Group Panitia)**

// Manajemen Penugasan Pengawas  
Route::get('/sessions/{session\_id}/supervisors', \[SupervisorController::class, 'index'\])-\>name('panitia.supervisors.index');  
Route::post('/sessions/supervisors/assign', \[SupervisorController::class, 'assign'\])-\>name('panitia.supervisors.assign');

// Monitoring Absensi & Berita Acara  
Route::get('/schedules/{schedule\_id}/room/{room\_id}/report', \[SupervisorController::class, 'showReport'\])-\>name('panitia.report.show');

### **B. Controller Logic (SupervisorController.php)**

namespace App\\Http\\Controllers\\Panitia;

use App\\Http\\Controllers\\Controller;  
use Illuminate\\Http\\Request;  
use App\\Models\\ExamSession;  
use App\\Models\\ExamSchedule;  
use App\\Models\\RoomSupervisor;  
use App\\Models\\ExamReport;  
use App\\Models\\StudentAttendance;  
use App\\Models\\User;

class SupervisorController extends Controller  
{  
    // 1\. Menampilkan Matriks Jadwal untuk Penugasan Pengawas  
    public function index($session\_id)  
    {  
        $session \= ExamSession::findOrFail($session\_id);  
          
        // Ambil jadwal beserta alokasi ruangannya (Group by Date & Time Session)  
        $schedules \= ExamSchedule::with(\['subject', 'timeSession', 'allocations.room'\])  
                        \-\>where('exam\_session\_id', $session\_id)  
                        \-\>orderBy('exam\_date')  
                        \-\>get();

        // Ambil daftar user yang memiliki role 'Pengawas'  
        $pengawas \= User::where('role', 'Pengawas')-\>get();

        // Ambil data penugasan yang sudah ada agar dropdown terisi otomatis jika sudah di-assign  
        $assigned \= RoomSupervisor::whereHas('examSchedule', function($q) use ($session\_id) {  
            $q-\>where('exam\_session\_id', $session\_id);  
        })-\>get()-\>groupBy(function($item) {  
            return $item-\>exam\_schedule\_id . '\_' . $item-\>room\_id;  
        });

        return view('panitia.supervisors.index', compact('session', 'schedules', 'pengawas', 'assigned'));  
    }

    // 2\. Logika Menyimpan Penugasan (Bisa single assign atau bulk assign)  
    public function assign(Request $request)  
    {  
        $request-\>validate(\[  
            'exam\_schedule\_id' \=\> 'required',  
            'room\_id' \=\> 'required',  
            'user\_id' \=\> 'required', // ID Pengawas  
        \]);

        RoomSupervisor::updateOrCreate(  
            \[  
                'exam\_schedule\_id' \=\> $request-\>exam\_schedule\_id,  
                'room\_id' \=\> $request-\>room\_id,  
            \],  
            \[  
                'user\_id' \=\> $request-\>user\_id  
            \]  
        );

        return back()-\>with('success', 'Pengawas berhasil ditugaskan ke ruangan tersebut.');  
    }

    // 3\. Menampilkan Laporan (Absensi & Berita Acara) untuk dicek Panitia  
    public function showReport($schedule\_id, $room\_id)  
    {  
        $schedule \= ExamSchedule::with(\['subject', 'timeSession'\])-\>findOrFail($schedule\_id);  
          
        // Cek Berita Acara  
        $report \= ExamReport::with('user')  
                    \-\>where('exam\_schedule\_id', $schedule\_id)  
                    \-\>where('room\_id', $room\_id)  
                    \-\>first();

        // Ambil Data Absensi  
        $attendances \= StudentAttendance::with('student')  
                    \-\>where('exam\_schedule\_id', $schedule\_id)  
                    \-\>where('room\_id', $room\_id)  
                    \-\>get();

        return view('panitia.supervisors.report\_detail', compact('schedule', 'report', 'attendances', 'room\_id'));  
    }  
}

### **C. UI Penugasan Pengawas (Blade Snippet)**

Ini adalah contoh potongan kode untuk menampilkan *dropdown* pilihan pengawas di sebelah data ruangan pada matriks jadwal.

\<\!-- Perulangan Jadwal dan Ruangan \--\>  
@foreach($schedules as $schedule)  
    @php   
        // Mengambil ruangan unik dari alokasi siswa (yang digenerate AG)  
        $uniqueRooms \= $schedule-\>allocations-\>pluck('room')-\>unique('id');  
    @endphp

    \<div class="mb-6 p-4 bg-white rounded-lg shadow border-l-4 border-emerald-500"\>  
        \<h4 class="font-bold text-lg"\>{{ $schedule-\>exam\_date }} | {{ $schedule-\>timeSession-\>name }}\</h4\>  
        \<p class="text-sm text-gray-600 mb-4"\>Mata Pelajaran: {{ $schedule-\>subject-\>name }}\</p\>  
          
        \<table class="w-full text-sm text-left"\>  
            \<thead class="bg-gray-50 text-gray-700 uppercase"\>  
                \<tr\>  
                    \<th class="px-4 py-2"\>Ruangan\</th\>  
                    \<th class="px-4 py-2"\>Pilih Pengawas\</th\>  
                    \<th class="px-4 py-2"\>Status Laporan\</th\>  
                \</tr\>  
            \</thead\>  
            \<tbody\>  
                @foreach($uniqueRooms as $room)  
                    @php   
                        $key \= $schedule-\>id . '\_' . $room-\>id;  
                        $assignedPengawasId \= isset($assigned\[$key\]) ? $assigned\[$key\]-\>first()-\>user\_id : null;  
                    @endphp  
                    \<tr class="border-b"\>  
                        \<td class="px-4 py-2 font-medium"\>{{ $room-\>name }}\</td\>  
                        \<td class="px-4 py-2"\>  
                            \<\!-- Form Assign Pengawas \--\>  
                            \<form action="{{ route('panitia.supervisors.assign') }}" method="POST" class="flex gap-2"\>  
                                @csrf  
                                \<input type="hidden" name="exam\_schedule\_id" value="{{ $schedule-\>id }}"\>  
                                \<input type="hidden" name="room\_id" value="{{ $room-\>id }}"\>  
                                  
                                \<select name="user\_id" class="border rounded p-1" onchange="this.form.submit()"\>  
                                    \<option value=""\>-- Pilih Pengawas \--\</option\>  
                                    @foreach($pengawas as $p)  
                                        \<option value="{{ $p-\>id }}" {{ $assignedPengawasId \== $p-\>id ? 'selected' : '' }}\>  
                                            {{ $p-\>name }}  
                                        \</option\>  
                                    @endforeach  
                                \</select\>  
                            \</form\>  
                        \</td\>  
                        \<td class="px-4 py-2"\>  
                            \<a href="{{ route('panitia.report.show', \['schedule\_id' \=\> $schedule-\>id, 'room\_id' \=\> $room-\>id\]) }}"   
                               class="text-blue-600 hover:underline"\>  
                                Lihat Absensi & Laporan ➡️  
                            \</a\>  
                        \</td\>  
                    \</tr\>  
                @endforeach  
            \</tbody\>  
        \</table\>  
    \</div\>  
@endforeach

### **D. UI Monitoring Laporan (Blade Snippet)**

Halaman ini (report\_detail.blade.php) murni bersifat *Read-Only* agar Panitia tidak tanpa sengaja merusak data yang disubmit pengawas.

\<div class="bg-white p-6 rounded-lg shadow-md mb-6"\>  
    \<h2 class="text-xl font-bold mb-4"\>Berita Acara Ujian\</h2\>  
      
    @if($report && $report-\>status \=== 'Submitted')  
        \<div class="grid grid-cols-2 gap-4 mb-4 text-sm"\>  
            \<div\>\<strong\>Pengawas:\</strong\> {{ $report-\>user-\>name }}\</div\>  
            \<div\>\<strong\>Total Hadir:\</strong\> \<span class="text-green-600 font-bold"\>{{ $report-\>total\_present }}\</span\>\</div\>  
            \<div\>\<strong\>Status Laporan:\</strong\> \<span class="bg-green-100 text-green-800 px-2 rounded"\>Diterima\</span\>\</div\>  
            \<div\>\<strong\>Total Tidak Hadir:\</strong\> \<span class="text-red-600 font-bold"\>{{ $report-\>total\_absent }}\</span\>\</div\>  
        \</div\>  
        \<div class="bg-gray-50 p-4 rounded border"\>  
            \<p class="font-semibold mb-2"\>Catatan Kejadian (Insiden):\</p\>  
            \<p class="text-gray-700 italic"\>{{ $report-\>incident\_notes ?? 'Tidak ada catatan kejadian khusus. Ujian berjalan lancar.' }}\</p\>  
        \</div\>  
    @else  
        \<div class="p-4 bg-yellow-50 text-yellow-800 border border-yellow-200 rounded"\>  
            Laporan Berita Acara belum disubmit oleh pengawas ruangan bersangkutan.  
        \</div\>  
    @endif  
\</div\>

\<div class="bg-white p-6 rounded-lg shadow-md"\>  
    \<h2 class="text-xl font-bold mb-4"\>Detail Kehadiran Siswa (Absensi)\</h2\>  
    \<table class="w-full text-left border-collapse"\>  
        \<thead\>  
            \<tr class="bg-gray-100"\>  
                \<th class="border p-2"\>Nama Siswa\</th\>  
                \<th class="border p-2"\>Status Kehadiran\</th\>  
            \</tr\>  
        \</thead\>  
        \<tbody\>  
            @forelse($attendances as $absen)  
                \<tr\>  
                    \<td class="border p-2"\>{{ $absen-\>student-\>name }}\</td\>  
                    \<td class="border p-2 font-bold   
                        {{ $absen-\>status \== 'Hadir' ? 'text-green-600' : 'text-red-600' }}"\>  
                        {{ $absen-\>status }}  
                    \</td\>  
                \</tr\>  
            @empty  
                \<tr\>  
                    \<td colspan="2" class="border p-4 text-center text-gray-500"\>Belum ada data absensi yang dicatat oleh pengawas.\</td\>  
                \</tr\>  
            @endforelse  
        \</tbody\>  
    \</table\>  
\</div\>  
