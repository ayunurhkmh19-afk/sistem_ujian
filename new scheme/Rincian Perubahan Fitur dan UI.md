# **RINCIAN PERUBAHAN FITUR & TAMPILAN INTI (SKEMA V2)**

**Sistem Informasi Penjadwalan Ujian SMAN 3 Bontang**

Pembaruan arsitektur ke Skema V2 mengubah cara sistem memandang data (dari berbasis "Sesi Ujian Global" menjadi berbasis "Jadwal per Jam/Mata Pelajaran"). Berikut adalah rincian fitur apa saja yang berubah dan file/direktori mana saja yang menjadi *core* (inti) dari perubahan tersebut.

## **1\. FITUR MANAJEMEN DATA MASTER (BARU)**

Karena sistem sekarang mengenal "Tingkatan" (Kelas 10, 11, 12\) dan "Sesi Waktu" (Sesi 1: 09.00 \- 11.00), Panitia harus memiliki fitur CRUD (Create, Read, Update, Delete) untuk mengatur data master ini sebelum membuat jadwal ujian.

### **Direktori & File Inti:**

* **Controller:** \* app/Http/Controllers/Panitia/MasterDataController.php (File baru untuk mengatur semua master data).  
* **Views (UI):**  
  * resources/views/panitia/master/levels/index.blade.php \-\> Tampilan daftar tingkatan kelas.  
  * resources/views/panitia/master/subjects/index.blade.php \-\> Tampilan daftar mata pelajaran per tingkat.  
  * resources/views/panitia/master/time\_sessions/index.blade.php \-\> Tampilan pengaturan jam sesi ujian (Sesi 1, Sesi 2, dst).  
  * resources/views/panitia/master/rooms/index.blade.php \-\> Tampilan manajemen kapasitas ruangan (Modifikasi file lama).

### **Penjelasan Perubahan:**

Tampilan ini akan berupa tabel-tabel DataTables biasa. Inti perubahannya adalah Panitia kini mengikat *Mata Pelajaran* ke *Tingkatan* (Misal: Matematika \-\> Kelas 10). Ini menjadi syarat mutlak agar Algoritma Genetika (AG) tahu mata pelajaran apa yang harus diberikan ke kelas mana.

## **2\. FITUR WIZARD PEMBUATAN UJIAN (PERUBAHAN TOTAL)**

Sebelumnya, panitia mungkin hanya mengisi form sederhana untuk membuat "Sesi Ujian". Sekarang, pembuatan ujian diubah menjadi sistem *Wizard* (Langkah Bertahap \- 5 Step) karena banyaknya variabel yang harus disiapkan sebelum menekan tombol Algoritma Genetika.

### **Direktori & File Inti:**

* **Controller:** \* app/Http/Controllers/Panitia/ExamSessionWizardController.php (Disarankan membuat controller terpisah khusus untuk menangani *multi-step form*).  
* **Views (UI):**  
  * resources/views/panitia/sessions/wizard/step\_1\_info.blade.php \-\> Input rentang tanggal (Start Date \- End Date).  
  * resources/views/panitia/sessions/wizard/step\_2\_subjects.blade.php \-\> UI Checklist Mapel berdasarkan tingkatan.  
  * resources/views/panitia/sessions/wizard/step\_3\_rooms\_times.blade.php \-\> UI Checklist Ruangan dan Sesi Waktu yang aktif.  
  * resources/views/panitia/sessions/wizard/step\_4\_students.blade.php \-\> UI Upload File Excel siswa dan *Summary Card* per tingkatan.  
  * resources/views/panitia/sessions/wizard/step\_5\_generate.blade.php \-\> UI *Pre-flight check* (Validasi kecukupan ruang) dan tombol Eksekusi AG.

### **Penjelasan Perubahan:**

Tampilan inti di sini adalah **Step 4 dan Step 5**. Pada Step 4, UI harus memunculkan jumlah siswa per tingkatan secara visual (Card/Grafik). Pada Step 5, UI harus bisa memberikan pesan *Error Box* warna merah jika (Total Ruang x Total Sesi Waktu) \< Siswa. Jika aman, tombol hijau "Generate Jadwal" baru bisa ditekan.

## **3\. FITUR MONITORING ALGORITMA GENETIKA (BARU & MODIFIKASI)**

Fitur ini adalah tempat di mana Panitia melihat status pemrosesan Algoritma Genetika secara *real-time* dengan desain *Glassmorphism*.

### **Direktori & File Inti:**

* **Job (Backend):**  
  * app/Jobs/GenerateAllocationJob.php \-\> Pekerja di balik layar yang menjalankan proses evolusi penjadwalan.  
* **Controller:**  
  * app/Http/Controllers/Panitia/ExamSessionController.php \-\> (Fungsi cekStatusAG dan generateAlokasi).  
* **Views (UI):**  
  * resources/views/panitia/sessions/index.blade.php \-\> Halaman daftar riwayat ujian.

### **Penjelasan Perubahan:**

Halaman index.blade.php (Riwayat Sesi Ujian) menjadi *core UI*. Di kolom aksi, terdapat *AJAX Polling Script*. Saat AG berjalan, UI akan menampilkan animasi *loading spinner* (Menyusun Ruang...). UI ini akan terus menembak API ke server setiap 3 detik. Begitu AG selesai, halaman otomatis *refresh* dan menampilkan tombol "Lihat Hasil Jadwal".

## **4\. FITUR DAFTAR JADWAL UJIAN HASIL AG (PERUBAHAN BESAR)**

Sebelumnya, hasil alokasi hanya berupa daftar siswa per ruangan. Sekarang, hasilnya adalah **Matriks Jadwal (Time Table)** layaknya jadwal kuliah/sekolah.

### **Direktori & File Inti:**

* **Views (UI):**  
  * resources/views/panitia/sessions/schedules\_result.blade.php (File baru/Modifikasi file lama).

### **Penjelasan Perubahan:**

Tampilan ini akan merender hasil dari AG (tabel exam\_schedules). UI-nya diformat berdasarkan *Hari (Date)* dan *Sesi Waktu (Time Session)*.

*Contoh Tampilan Visual:* \* **Senin, 12 Okt 2026**

\* \[09:00 \- 11:00\] MTK Kelas 10 (Ruang 1, Ruang 2\) | Pengawas: \[Belum Ditugaskan\]

\* \[13:00 \- 15:00\] MTK Kelas 10 (Ruang 1, Ruang 2\) | Pengawas: \[Belum Ditugaskan\]

UI ini juga menjadi tempat Panitia menekan tombol "Assign Pengawas" untuk masing-masing baris jadwal tersebut.

## **5\. FITUR DASHBOARD & ABSENSI PENGAWAS (PERUBAHAN LOGIKA RELASI)**

Ini adalah imbas dari pembaruan relasi di mana room\_supervisors tidak lagi diikat ke "Sesi Ujian Global" (exam\_session\_id), melainkan diikat ke "Jadwal Per Jam/Mapel" (exam\_schedule\_id).

### **Direktori & File Inti:**

* **Controller:**  
  * app/Http/Controllers/Pengawas/DashboardController.php \-\> Harus query melalui exam\_schedule\_id.  
  * app/Http/Controllers/Pengawas/SessionController.php \-\> Menangani UI Absensi.  
* **Views (UI):**  
  * resources/views/pengawas/dashboard.blade.php  
  * resources/views/pengawas/sesi\_detail.blade.php

### **Penjelasan Perubahan:**

* **Dashboard:** Kartu jadwal milik pengawas sekarang menjadi jauh lebih spesifik. UI akan menampilkan: *"Anda mengawasi Matematika Kelas 10, di Ruang 1, Sesi Waktu: 09:00 \- 11:00"*. Indikator status (Akan/Sedang/Sudah Berlangsung) kini membandingkan waktu saat ini dengan jam Sesi Waktu tersebut secara akurat.  
* **Form Absensi:** UI Form Absensi tetap sama secara visual, namun di *backend*, saat form disubmit, ia mengunci absensi untuk mata pelajaran dan rentang waktu (Sesi) tersebut secara spesifik, bukan mengunci satu hari penuh.

### **RINGKASAN PRIORITAS UNTUK DEVELOPER:**

Jika tim Developer mulai mengerjakan pembaruan ini, kerjakan dengan urutan direktori berikut:

1. Selesaikan folder app/Models/... dan *Database Migrations* terlebih dahulu (Skema V2).  
2. Buat UI Master Data di resources/views/panitia/master/....  
3. Bangun UI Wizard 5 Langkah di resources/views/panitia/sessions/wizard/....  
4. Rangkai logika app/Services/GeneticAlgorithmService.php dan app/Jobs/....  
5. Terakhir, perbarui UI Pengawas di resources/views/pengawas/... menyesuaikan relasi jadwal baru.