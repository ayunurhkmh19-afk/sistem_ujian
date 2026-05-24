# **DOKUMENTASI FINAL: SKEMA & WIZARD PENJADWALAN AG (REVISI LOGIKA)**

**Sistem Informasi Penjadwalan Ujian SMAN 3 Bontang**

Dokumen ini merupakan panduan teknis final untuk arsitektur *Database*, *User Interface* (Wizard Panitia), dan logika Algoritma Genetika (AG) yang telah disempurnakan untuk menangani ujian multi-tingkatan (Kelas 10, 11, 12).

## **1\. SKEMA DATABASE FINAL (ENTITY RELATIONSHIP)**

Struktur tabel telah disesuaikan agar mampu menangani sesi per jam, distribusi ruangan proporsional, dan rentang tanggal ujian.

### **A. Tabel Master Data**

1. **levels (Tingkatan)**: id, name (Misal: Kelas 10, Kelas 11).  
2. **student\_classes (Kelas)**: id, level\_id (FK), name (Misal: 10 IPA 1).  
3. **subjects (Mata Pelajaran)**: id, level\_id (FK), name.  
4. **time\_sessions (Sesi Waktu)**: id, name (Misal: Sesi 1), start\_time, end\_time.  
5. **rooms**: id, name, capacity. (Dapat digunakan berulang kali di sesi waktu yang berbeda).

### **B. Tabel Operasional Transaksi**

1. **exam\_sessions (Sesi Ujian Global)**  
   * id, title, start\_date, end\_date, allocation\_status, is\_active.  
2. **exam\_schedules (Jadwal Spesifik \- Digenerate oleh AG)**  
   * id, exam\_session\_id (FK)  
   * subject\_id (FK)  
   * time\_session\_id (FK)  
   * exam\_date (Kapan mapel ini diujikan)  
3. **exam\_allocations (Alokasi Siswa \- Digenerate oleh AG)**  
   * id, exam\_schedule\_id (FK) \-\> *Perbaikan: Mengikat ke jadwal spesifik\!*  
   * room\_id (FK)  
   * student\_id (FK)  
   * desk\_number  
4. **room\_supervisors (Penugasan Pengawas)**  
   * id, user\_id (FK ke Pengawas)  
   * exam\_schedule\_id (FK) \-\> *Perbaikan: Pengawas ditugaskan per jadwal/mapel/jam.*  
   * room\_id (FK)

## **2\. ALUR WIZARD PEMBUATAN UJIAN (PANITIA)**

Antarmuka *wizard* diubah menjadi 5 langkah, dilengkapi dengan validasi *pre-flight* sebelum AG diizinkan berjalan.

* **Step 1: Inisiasi Sesi Ujian**  
  Input: Nama Ujian, **Tanggal Mulai (start\_date)**, dan **Tanggal Selesai (end\_date)**.  
* **Step 2: Pemilihan Mata Pelajaran (Subject)**  
  Sistem menampilkan daftar mapel berdasar tingkatan. Panitia men-ceklis mapel yang diujikan.  
* **Step 3: Setup Ruangan & Sesi Waktu**  
  Panitia men-ceklis Ruangan mana saja yang dipakai DAN Sesi Waktu mana saja yang aktif dalam sehari (Misal: Sesi 1, 2, 3).  
* **Step 4: Import & Rekapitulasi Siswa**  
  Upload daftar siswa. Sistem menampilkan *Summary*:*Total Kelas 10: 120 Siswa | Kelas 11: 110 Siswa | Kelas 12: 115 Siswa*  
* **Step 5: Validasi & Eksekusi AG**  
  * **Pre-Flight Check (Otomatis oleh Sistem):**  
    Jika (Total Kapasitas Ruang x Jumlah Sesi Waktu) \< Total Siswa Angkatan Terbesar  
    \-\> **Tolak eksekusi\!** Tampilkan peringatan: *"Kapasitas ruangan/sesi tidak cukup untuk menampung seluruh peserta."*  
  * Jika lolos, Panitia menekan tombol "Auto-Generate Jadwal (AG)".

## **3\. LOGIKA INTI ALGORITMA GENETIKA (CORE AG LOGIC)**

Aturan constraints AG diperbarui untuk mencegah *logical error* dan ruang hangus.

### **A. Strategi Proporsi Ruangan (Bukan 1/3 Kursi)**

AG tidak memecah kapasitas kursi menjadi 1/3, melainkan **mengalokasikan ruangan utuh** secara proporsional.

* *Contoh:* Ada 6 Ruangan. Jika populasi Kelas 10, 11, 12 hampir sama rata, AG akan menetapkan Ruang 1 & 2 untuk Kelas 10, Ruang 3 & 4 untuk Kelas 11, dan Ruang 5 & 6 untuk Kelas 12\.  
* *Hard Constraint:* Dilarang mencampur siswa beda tingkatan (beda level\_id) dalam satu ruangan pada Sesi Waktu yang sama.

### **B. Strategi Distribusi Waktu & Tanggal**

* **Berdasarkan Rentang Hari:** AG menghitung durasi hari ujian (end\_date \- start\_date). Mapel yang dipilih di Step 2 disebar secara merata. (Contoh: 15 Mapel dibagi 5 Hari \= 3 Mapel/hari untuk tiap tingkatan).  
* **Pemanggilan Sesi Waktu:** AG menempatkan Mapel A untuk Kelas 10 di *Hari ke-1*. AG mencoba mengisi Ruang 1 & 2 di **Sesi 1**. Jika Ruang 1 & 2 penuh dan masih ada sisa siswa Kelas 10, AG memindahkan sisa siswa ke **Sesi 2** pada hari yang sama dan mapel yang sama.

### **C. Sistem Penalti (Fitness Evaluation)**

1. Kelebihan kapasitas ruangan \-\> Penalti \+100  
2. Siswa beda tingkatan dalam 1 ruang di waktu yang sama \-\> Penalti \+100  
3. Ada siswa di *subject* tersebut yang belum dapat ruangan \-\> Penalti \+100  
4. Meja ganda (Duplicate desk number) \-\> Penalti \+100

## **4\. PANDUAN MIGRASI LARAVEL (CODE SNIPPETS)**

Bagi *developer*, jalankan *migration* ini untuk mengeksekusi arsitektur database baru:

// 1\. Modifikasi exam\_sessions  
Schema::table('exam\_sessions', function (Blueprint $table) {  
    $table-\>date('start\_date')-\>after('title');  
    $table-\>date('end\_date')-\>after('start\_date');  
});

// 2\. Modifikasi exam\_schedules (Re-create)  
Schema::dropIfExists('exam\_schedules');  
Schema::create('exam\_schedules', function (Blueprint $table) {  
    $table-\>id();  
    $table-\>foreignId('exam\_session\_id')-\>constrained()-\>cascadeOnDelete();  
    $table-\>foreignId('subject\_id')-\>constrained(); // Mapel spesifik  
    $table-\>foreignId('time\_session\_id')-\>constrained(); // Sesi jam berapa  
    $table-\>date('exam\_date');  
    $table-\>timestamps();  
});

// 3\. Modifikasi exam\_allocations (Penting\!)  
Schema::table('exam\_allocations', function (Blueprint $table) {  
    $table-\>dropForeign(\['exam\_session\_id'\]);  
    $table-\>dropColumn('exam\_session\_id');  
      
    // Ikat ke Schedule  
    $table-\>foreignId('exam\_schedule\_id')-\>after('id')-\>constrained()-\>cascadeOnDelete();  
});

// 4\. Modifikasi room\_supervisors  
Schema::table('room\_supervisors', function (Blueprint $table) {  
    $table-\>dropForeign(\['exam\_session\_id'\]);  
    $table-\>dropColumn('exam\_session\_id');  
      
    $table-\>foreignId('exam\_schedule\_id')-\>after('user\_id')-\>constrained()-\>cascadeOnDelete();  
});  
