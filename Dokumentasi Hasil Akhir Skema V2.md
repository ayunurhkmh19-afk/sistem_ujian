# **DOKUMENTASI OUTPUT & HASIL AKHIR PENJADWALAN AG (SKEMA V2)**

**Sistem Informasi Penjadwalan Ujian SMAN 3 Bontang**

Dokumen ini mendeskripsikan hasil akhir (*Deliverables*) setelah Algoritma Genetika (AG) dan *Skill Agent* berhasil dieksekusi dengan status Completed (Fitness \= 1.0). Hasil akhir ini mencakup representasi data di dalam *Database*, visualisasi pada antarmuka (UI), hingga wujud cetak dokumen.

## **1\. OUTPUT DATABASE (REPRESENTASI DATA)**

Setelah *Background Job* selesai, sistem akan menghasilkan ratusan atau ribuan baris data yang terstruktur rapi dan bebas dari bentrok (berkat keahlian *Heuristic Repair* dari Agen Pintar).

### **A. Tabel exam\_schedules (Jadwal Ujian Terbentuk)**

Tabel ini berisi jadwal yang di- *generate* secara dinamis oleh AG. Jika kapasitas ruang untuk satu kelas tidak cukup di Sesi 1, AG otomatis memecahnya ke Sesi 2\.

| id | exam\_session\_id | subject\_id | time\_session\_id | exam\_date | Keterangan (Visualisasi Logika) |
| :---- | :---- | :---- | :---- | :---- | :---- |
| 1 | 1 (UAS Ganjil) | 12 (MTK Kls 10\) | 1 (08.00-10.00) | 2026-12-01 | Sebagian Siswa Kls 10 Ujian MTK di Sesi 1 |
| 2 | 1 (UAS Ganjil) | 12 (MTK Kls 10\) | 2 (10.30-12.30) | 2026-12-01 | Sisa Siswa Kls 10 Ujian MTK di Sesi 2 |
| 3 | 1 (UAS Ganjil) | 25 (Fisika Kls 11\) | 1 (08.00-10.00) | 2026-12-01 | Siswa Kls 11 Ujian Fisika di Sesi 1 |
| 4 | 1 (UAS Ganjil) | 40 (Kimia Kls 12\) | 1 (08.00-10.00) | 2026-12-01 | Siswa Kls 12 Ujian Kimia di Sesi 1 |

### **B. Tabel exam\_allocations (Alokasi Ruangan & Meja)**

Tabel ini memetakan siswa secara spesifik ke jadwal di atas. Dijamin 100% tidak ada isolasi tingkat yang dilanggar dan tidak ada meja ganda.

| id | exam\_schedule\_id | room\_id | student\_id | desk\_number | Analisis Keberhasilan AG |
| :---- | :---- | :---- | :---- | :---- | :---- |
| 1 | 1 (MTK Sesi 1\) | 1 (Ruang A) | 1001 (Andi \- Kls 10\) | 1 | Sesuai Tingkat & Mapel |
| 2 | 1 (MTK Sesi 1\) | 1 (Ruang A) | 1002 (Budi \- Kls 10\) | 2 | Meja tidak ganda |
| 3 | 3 (Fisika Sesi 1\) | 5 (Ruang E) | 2001 (Citra \- Kls 11\) | 1 | Ruang E khusus Kelas 11 (Terisolasi) |
| 4 | 2 (MTK Sesi 2\) | 1 (Ruang A) | 1050 (Dodi \- Kls 10\) | 1 | Dodi masuk Sesi 2 karena Sesi 1 penuh |

## **2\. OUTPUT ANTARMUKA PANITIA (UI/UX)**

Di sisi Panitia, hasil keluaran AG tidak ditampilkan dalam bentuk data mentah, melainkan sebagai **Matriks Jadwal & Kepengawasan** yang elegan (*Glassmorphism Design*).

### **Tampilan Halaman: "Manajemen Jadwal & Pengawas"**

Sistem akan mengelompokkan exam\_schedules berdasarkan Tanggal dan Sesi Waktu.

**📅 Selasa, 1 Desember 2026**

* **Sesi 1 (08:00 \- 10:00)**  
  * **Matematika (Kelas 10\)**  
    * Ruang A (30 Siswa) ➔ \[Dropdown Pilih Pengawas: Pak Ardi\] ➔ *Status: Laporan Diterima*  
    * Ruang B (30 Siswa) ➔ \[Dropdown Pilih Pengawas: Bu Ayu\] ➔ *Status: Menunggu Laporan*  
  * **Fisika (Kelas 11\)**  
    * Ruang C (30 Siswa) ➔ \[Dropdown Pilih Pengawas: \_\_\_\_\_\_\_\_\] ➔ *Status: Belum Mulai*  
* **Sesi 2 (10:30 \- 12:30)**  
  * **Matematika (Kelas 10\) \- (Lanjutan)**  
    * Ruang A (25 Siswa) ➔ \[Dropdown Pilih Pengawas: \_\_\_\_\_\_\_\_\] ➔ *Status: Belum Mulai*

*Fitur Unggulan Output Ini:* Panitia langsung tahu bahwa Kelas 10 dipecah menjadi Sesi 1 dan Sesi 2 karena keterbatasan ruang, dan Panitia bisa menugaskan pengawas yang berbeda untuk Ruang A di Sesi 1 dan Sesi 2\.

## **3\. OUTPUT ANTARMUKA PENGAWAS (UI/UX)**

Di sisi Pengawas, output yang dihasilkan sangat spesifik dan personal (berkat relasi exam\_schedule\_id ke room\_supervisors).

### **Tampilan Halaman: "Dashboard Pengawas"**

Saat Bu Ayu (Pengawas) login pada tanggal 1 Desember 2026, ia akan melihat *Card* jadwal miliknya:

**Jadwal Pengawasan Anda Hari Ini:**

🟢 **Sedang Berlangsung (08:00 \- 10:00)**

Mata Pelajaran: **Matematika (Kelas 10\)**

Ruangan: **Ruang B**

\[Tombol: Buka Form Absensi & Berita Acara\]

*Fitur Unggulan Output Ini:* Bu Ayu hanya melihat daftar 30 anak Kelas 10 yang memang dialokasikan ke Ruang B pada Sesi 1\. Data tidak akan tertukar dengan Sesi 2 atau ruangan lain.

## **4\. OUTPUT DOKUMEN CETAK (KARTU UJIAN SISWA)**

Ini adalah *end-product* yang paling dirasakan oleh pengguna (Siswa). Karena jadwal tiap siswa kini bisa berbeda-beda (ada yang masuk Sesi 1, ada yang Sesi 2), format Kartu Ujian Siswa harus bersifat dinamis mengikuti tabel exam\_allocations.

### **Format Cetak (PDF Generator)**

Kartu Ujian dicetak per siswa, menampilkan tabel jadwal ujian pribadi mereka.

**KARTU PESERTA UJIAN SMAN 3 BONTANG**

**Nama :** Andi (ID: 1001\)

**Kelas:** 10 IPA 1

| Hari, Tanggal | Waktu (Sesi) | Mata Pelajaran | Ruang | No. Meja |
| :---- | :---- | :---- | :---- | :---- |
| Selasa, 01 Des 2026 | 08:00 \- 10:00 (Sesi 1\) | Matematika | Ruang A | 01 |
| Rabu, 02 Des 2026 | 10:30 \- 12:30 (Sesi 2\) | Bahasa Inggris | Ruang B | 15 |
| Kamis, 03 Des 2026 | 08:00 \- 10:00 (Sesi 1\) | Biologi | Ruang A | 08 |

*Fitur Unggulan Output Ini:* Siswa mendapat kepastian mutlak mengenai jadwal mereka. Siswa tidak perlu lagi melihat papan pengumuman yang rumit karena semuanya sudah dikustomisasi oleh algoritma.

## **5\. METRIK KEBERHASILAN (KPI) SISTEM V2**

Untuk memastikan output ini sesuai dengan rencana awal perancangan, berikut adalah metrik yang membuktikan bahwa AG dan *Skill Agent* bekerja dengan sempurna:

1. **Zero Collision Rate:** Terbukti dari tabel exam\_allocations di mana kombinasi \[exam\_schedule\_id, room\_id, desk\_number\] bersifat UNIQUE.  
2. **Strict Level Isolation:** Tidak ada satupun room\_id pada satu exam\_schedule\_id yang berisi student\_id dari dua level\_id (Tingkatan) yang berbeda. (Kelas 10 dan 11 tidak pernah seruangan).  
3. **Proportional Optimization:** Jika total ruangan ada 9, maka Kelas 10 mendapat 3 ruang, Kelas 11 mendapat 3 ruang, Kelas 12 mendapat 3 ruang pada Sesi 1, secara otomatis dan proporsional.