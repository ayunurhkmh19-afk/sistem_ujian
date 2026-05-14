# **DEKOMPOSISI TAHAP 4-6: MODUL ABSENSI, BERITA ACARA & PENGUJIAN PENGAWAS**

**Sistem Informasi Penjadwalan & Pelaksanaan Ujian (Studi Kasus: SMAN 3 Bontang)**

Dokumen ini merupakan kelanjutan dari implementasi fitur Pengawas. Fokus pada dokumen ini adalah bagaimana sistem menangani interaksi input data (absensi), rekapitulasi akhir (berita acara), penguncian data (*locking*), serta skenario pengujian kualitas (UAT).

## **TAHAP 4: Implementasi Modul Absensi (Fase Interaksi Data)**

Pada tahap ini, kita membuat antarmuka dan logika *backend* agar pengawas dapat menandai kehadiran siswa saat status ujian sudah selesai ("Sudah Berlangsung").

### **4.1 Menampilkan Detail Sesi dan Form Absensi (SessionController@show)**

Controller ini merender halaman detail sesi sekaligus mengirimkan daftar peserta dan data absensi mereka (jika sudah pernah diisi).

* **Perintah Terminal:** php artisan make:controller Pengawas/SessionController  
* **Logika Kode:**  
  namespace App\\Http\\Controllers\\Pengawas;

  use App\\Http\\Controllers\\Controller;  
  use App\\Models\\ExamSession;  
  use App\\Models\\ExamAllocation;  
  use App\\Models\\StudentAttendance;  
  use App\\Models\\ExamReport;  
  use Carbon\\Carbon;

  class SessionController extends Controller  
  {  
      public function show($session\_id, $room\_id)  
      {  
          // 1\. Ambil data sesi  
          $sesi \= ExamSession::with('schedules')-\>findOrFail($session\_id);

          // 2\. Verifikasi status waktu ujian (seperti di Dashboard)  
          $schedule \= $sesi-\>schedules-\>first();  
          $endDateTime \= Carbon::parse($schedule-\>exam\_date . ' ' . $schedule-\>end\_time);

          // Jika ujian belum selesai, tolak akses ke form  
          if (Carbon::now()-\>lt($endDateTime)) {  
              return back()-\>with('error', 'Modul Absensi belum bisa diakses. Ujian belum selesai.');  
          }

          // 3\. Ambil daftar siswa di ruangan tersebut (Tabel lama: exam\_allocations)  
          $alokasiSiswa \= ExamAllocation::with('student')  
              \-\>where('exam\_session\_id', $session\_id)  
              \-\>where('room\_id', $room\_id)  
              \-\>get();

          // 4\. Cek apakah absensi sudah di-lock oleh Berita Acara  
          $laporan \= ExamReport::where('exam\_session\_id', $session\_id)  
              \-\>where('room\_id', $room\_id)  
              \-\>first();  
          $isLocked \= ($laporan && $laporan-\>status \=== 'Submitted');

          // 5\. Ambil data kehadiran yang sudah ada (jika pengawas menyicil absen)  
          $kehadiran \= StudentAttendance::where('exam\_session\_id', $session\_id)  
              \-\>where('room\_id', $room\_id)  
              \-\>get()-\>keyBy('student\_id');

          return view('pengawas.sesi\_detail', compact('sesi', 'alokasiSiswa', 'kehadiran', 'isLocked', 'room\_id'));  
      }  
  }

### **4.2 Tampilan Tabel Absensi Interaktif (resources/views/pengawas/sesi\_detail.blade.php)**

Pastikan setiap *radio button* akan *disabled* (tidak bisa diklik) jika variabel $isLocked bernilai *true*.

\<form action="{{ route('pengawas.absensi.store') }}" method="POST"\>  
    @csrf  
    \<input type="hidden" name="exam\_session\_id" value="{{ $sesi-\>id }}"\>  
    \<input type="hidden" name="room\_id" value="{{ $room\_id }}"\>

    \<table class="min-w-full bg-white border border-gray-200"\>  
        \<thead\>  
            \<tr\>  
                \<th class="border-b px-4 py-2 text-left"\>Nama Siswa\</th\>  
                \<th class="border-b px-4 py-2 text-center"\>Meja\</th\>  
                \<th class="border-b px-4 py-2 text-center"\>Status Kehadiran\</th\>  
            \</tr\>  
        \</thead\>  
        \<tbody\>  
            @foreach($alokasiSiswa as $alokasi)  
                @php   
                    $status \= isset($kehadiran\[$alokasi-\>student\_id\]) ? $kehadiran\[$alokasi-\>student\_id\]-\>status : 'Alpa';   
                @endphp  
            \<tr\>  
                \<td class="border-b px-4 py-2"\>{{ $alokasi-\>student-\>name }}\</td\>  
                \<td class="border-b px-4 py-2 text-center"\>{{ $alokasi-\>desk\_number }}\</td\>  
                \<td class="border-b px-4 py-2 text-center"\>  
                    \<\!-- Gunakan logika pengecekan nilai $status untuk 'checked' \--\>  
                    \<label class="mr-3"\>\<input type="radio" name="attendances\[{{ $alokasi-\>student\_id }}\]" value="Hadir" {{ $status \== 'Hadir' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}\> Hadir\</label\>  
                    \<label class="mr-3"\>\<input type="radio" name="attendances\[{{ $alokasi-\>student\_id }}\]" value="Sakit" {{ $status \== 'Sakit' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}\> Sakit\</label\>  
                    \<label class="mr-3"\>\<input type="radio" name="attendances\[{{ $alokasi-\>student\_id }}\]" value="Izin" {{ $status \== 'Izin' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}\> Izin\</label\>  
                    \<label\>\<input type="radio" name="attendances\[{{ $alokasi-\>student\_id }}\]" value="Alpa" {{ $status \== 'Alpa' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}\> Alpa\</label\>  
                \</td\>  
            \</tr\>  
            @endforeach  
        \</tbody\>  
    \</table\>

    @if(\!$isLocked)  
        \<button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded"\>Simpan Absensi Sementara\</button\>  
    @else  
        \<div class="mt-4 p-3 bg-red-100 text-red-700 rounded border border-red-300"\>  
            \<p\>\<strong\>Terkunci:\</strong\> Berita acara telah disubmit. Data absensi tidak dapat diubah lagi.\</p\>  
        \</div\>  
    @endif  
\</form\>

### **4.3 Logika Simpan Absensi (*Upsert Data*)**

Gunakan updateOrCreate agar data tidak duplikat jika pengawas menyimpan berulang kali.

public function storeAttendance(Request $request)  
{  
    // Validasi basic  
    $request-\>validate(\[  
        'exam\_session\_id' \=\> 'required',  
        'room\_id' \=\> 'required',  
        'attendances' \=\> 'required|array',  
    \]);

    $sessionId \= $request-\>exam\_session\_id;  
    $roomId \= $request-\>room\_id;  
    $userId \= Auth::id();

    // Looping data input dan Upsert ke DB  
    foreach ($request-\>attendances as $studentId \=\> $status) {  
        StudentAttendance::updateOrCreate(  
            \[  
                'exam\_session\_id' \=\> $sessionId,  
                'room\_id' \=\> $roomId,  
                'student\_id' \=\> $studentId  
            \],  
            \[  
                'status' \=\> $status,  
                'recorded\_by' \=\> $userId  
            \]  
        );  
    }

    return back()-\>with('success', 'Data absensi berhasil disimpan\!');  
}

## **TAHAP 5: Implementasi Modul Berita Acara & Locking**

Pada halaman yang sama (di bagian bawah tabel absensi), tampilkan Form Berita Acara.

### **5.1 Logika Form dan Tampilan (*View*)**

\<hr class="my-8"\>  
\<h3 class="text-xl font-bold mb-4"\>Berita Acara Pelaksanaan\</h3\>

\<form action="{{ route('pengawas.report.store') }}" method="POST"\>  
    @csrf  
    \<input type="hidden" name="exam\_session\_id" value="{{ $sesi-\>id }}"\>  
    \<input type="hidden" name="room\_id" value="{{ $room\_id }}"\>

    \<div class="mb-4"\>  
        \<label class="block text-gray-700 font-bold mb-2"\>Catatan Kejadian Selama Ujian:\</label\>  
        \<textarea name="incident\_notes" class="w-full border rounded p-2" rows="4" placeholder="Misal: Ujian berjalan lancar, atau Siswa A terlambat..." {{ $isLocked ? 'disabled' : '' }}\>{{ $laporan-\>incident\_notes ?? '' }}\</textarea\>  
    \</div\>

    @if(\!$isLocked)  
        \<button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700" onclick="return confirm('Yakin ingin submit? Setelah disubmit, data absen tidak bisa diubah lagi.')"\>  
            Submit & Kunci Berita Acara  
        \</button\>  
    @endif  
\</form\>

### **5.2 Logika *Backend* Penguncian (ReportController@store)**

*Kalkulasi total\_present harus dilakukan secara Server-side untuk mencegah data absensi dimanipulasi melalui Inspect Element (Frontend).*

public function storeReport(Request $request)  
{  
    $sessionId \= $request-\>exam\_session\_id;  
    $roomId \= $request-\>room\_id;

    // 1\. Kalkulasi otomatis dari Database Tabel Absensi  
    $totalHadir \= StudentAttendance::where('exam\_session\_id', $sessionId)  
                    \-\>where('room\_id', $roomId)  
                    \-\>where('status', 'Hadir')  
                    \-\>count();

    $totalTidakHadir \= StudentAttendance::where('exam\_session\_id', $sessionId)  
                    \-\>where('room\_id', $roomId)  
                    \-\>where('status', '\!=', 'Hadir')  
                    \-\>count();

    // 2\. Simpan ke Tabel exam\_reports dan Set Status jadi 'Submitted' (Penguncian)  
    ExamReport::updateOrCreate(  
        \[  
            'exam\_session\_id' \=\> $sessionId,  
            'room\_id' \=\> $roomId,  
        \],  
        \[  
            'user\_id' \=\> Auth::id(),  
            'total\_present' \=\> $totalHadir,  
            'total\_absent' \=\> $totalTidakHadir,  
            'incident\_notes' \=\> $request-\>incident\_notes,  
            'status' \=\> 'Submitted' // Kunci utama untuk mendisable form absensi\!  
        \]  
    );

    return back()-\>with('success', 'Berita Acara berhasil disubmit\! Seluruh data absensi telah dikunci.');  
}

## **TAHAP 6: Pengujian dan Validasi Kualitas (Testing / UAT)**

Tim pengembang (QA) harus menguji modul ini dengan metode Black Box testing menggunakan skenario (Test Cases) berikut:

### **6.1 Skenario Kontrol Akses (Security)**

| Langkah Pengujian | Hasil yang Diharapkan | Status |
| :---- | :---- | :---- |
| Login sebagai **Siswa**, paksa buka URL /pengawas/dashboard | Muncul halaman Error 403 (Akses Ditolak). | ✅ |
| Login sebagai **Panitia**, paksa buka URL /pengawas/dashboard | Muncul halaman Error 403 (Akses Ditolak). | ✅ |

### **6.2 Skenario Validasi Waktu Sesi (Time-Based Access)**

| Langkah Pengujian | Hasil yang Diharapkan | Status |
| :---- | :---- | :---- |
| Tekan tombol "Lihat Detail" pada sesi yang berstatus **Akan Berlangsung** | Halaman tertolak / dikembalikan dengan pesan error *"Ujian belum selesai"*. | ✅ |
| Tekan tombol "Lihat Detail" pada sesi berstatus **Sudah Berlangsung** | Berhasil masuk ke halaman Sesi Detail dan form absensi terbuka. | ✅ |

### **6.3 Skenario Integritas Absensi & *Locking***

| Langkah Pengujian | Hasil yang Diharapkan | Status |
| :---- | :---- | :---- |
| Isi 2 Hadir, 1 Sakit, 1 Alpa. Klik *Simpan Sementara*. | Data tersimpan, halaman *reload*, radio button masih di posisi yang sama. | ✅ |
| Klik *Submit & Kunci Berita Acara*. | Tampil *popup* konfirmasi. Jika *OK*, *reload*. | ✅ |
| Setelah halaman di-*reload* (Status Terkunci) | Semua *radio button* absensi **disabled**, textarea *disabled*, tombol submit hilang. Terdapat kotak merah pesan *Terkunci*. | ✅ |
| Cek di Database tabel exam\_reports | Field total\_present harus bernilai 2, dan total\_absent bernilai 2\. | ✅ |

