# **DEEP DIVE: LOGIKA INTI ALGORITMA GENETIKA (SKEMA V2)**

**Sistem Informasi Penjadwalan Ujian SMAN 3 Bontang**

Dokumen ini merincikan "otak" di balik Algoritma Genetika, yaitu bagaimana data direpresentasikan ke dalam kromosom, dan bagaimana sistem mendeteksi cacat jadwal melalui *Fitness Function* secara akurat pada skema V2 (multi-tingkatan & multi-sesi waktu).

## **1\. REPRESENTASI KROMOSOM (STRUKTUR GEN)**

Dalam AG tradisional, gen hanya berisi \[student\_id, room\_id\]. Namun, di skema V2, ujian memiliki **Hari** dan **Sesi Per Jam**. Oleh karena itu, kita harus memetakan **setiap mata pelajaran yang diambil siswa** ke dalam ruang, waktu, dan meja.

### **A. Struktur Gen (Satu Alokasi)**

Satu Gen merepresentasikan: *"Siswa X, ujian Mapel Y, dilaksanakan pada Hari ke-H, Sesi ke-S, di Ruang R, Meja M"*.

{  
  "student\_id": 101,  
  "level\_id": 1,         // (Disisipkan untuk mempermudah pengecekan isolasi tingkat)  
  "subject\_id": 5,       // Matematika  
  "day\_index": 1,        // Hari ke-1 dari rentang start\_date \- end\_date  
  "time\_session\_id": 2,  // Sesi ke-2 (13:00 \- 15:00)  
  "room\_id": 3,          // Ruang C  
  "desk\_number": 12  
}

### **B. Struktur Kromosom (Satu Alternatif Jadwal)**

Kromosom adalah *array* raksasa yang berisi ribuan Gen di atas. Jika ada 300 siswa dan masing-masing ujian 10 mapel, maka **satu kromosom berisi 3.000 Gen**. Populasi awal akan memiliki misalnya 50 Kromosom (50 alternatif jadwal utuh).

## **2\. MEKANISME *FITNESS EVALUATION* (OTAK PENALTI)**

Ini adalah fungsi paling krusial. Sistem harus melakukan *looping* ke 3.000 Gen dalam satu kromosom, lalu mengelompokkannya (grouping) untuk mencari bentrokan.

Semakin banyak aturan yang dilanggar, semakin besar penaltinya. Fitness \= 1 / (1 \+ Total Penalti).

### **A. Kategori Penalti & Bobot (Constraints)**

#### **1\. HARD CONSTRAINTS (Penalti \+100 per pelanggaran)**

Jadwal dianggap cacat total dan tidak bisa digunakan jika melanggar aturan ini:

* **Overcapacity:** Jumlah siswa di satu \[day\]\[session\]\[room\] melebih kapasitas room\_id.  
* **Level Mixing:** Di dalam satu \[day\]\[session\]\[room\], terdapat lebih dari 1 jenis level\_id. (Siswa kelas 10 dan 11 tercampur di ruangan dan waktu yang sama).  
* **Student Collision (Bentrok Waktu):** Satu student\_id memiliki lebih dari satu jadwal pada \[day\]\[session\] yang sama (Siswa harus membelah diri menjadi dua).  
* **Desk Collision:** Ada dua siswa yang duduk di desk\_number yang sama pada \[day\]\[session\]\[room\] yang sama.

#### **2\. SOFT CONSTRAINTS (Penalti \+10 per pelanggaran)**

Jadwal bisa digunakan, tapi kurang ideal dan efisien di lapangan:

* **Subject Scattering:** Jika mata pelajaran yang sama (Misal: MTK Kelas 10\) terpecah menjadi terlalu banyak Sesi. Idealnya, jika kapasitas cukup, MTK Kelas 10 diselesaikan di Sesi 1 semua. Jika terpaksa, baru pecah ke Sesi 2\.  
* **Room Underutilization:** Jika sebuah ruangan hanya diisi oleh 2 atau 3 anak padahal kapasitasnya 30 (Sistem harus mencoba memadatkan anak ke ruangan seminimal mungkin).

## **3\. IMPLEMENTASI KODE (FUNGSI EVALUASI)**

Berikut adalah algoritma *grouping* di dalam PHP untuk mendeteksi pelanggaran secara efisien (O(N) Complexity).

private function evaluateFitness(&$population, $roomsCapacity)  
{  
    foreach ($population as &$chromosome) {  
        $penalty \= 0;  
          
        // Tracker Arrays untuk grouping  
        $roomSessionTracker \= \[\]; // Format: \[day\]\[session\]\[room\] \=\> array of students & levels  
        $studentTimeTracker \= \[\]; // Format: \[student\_id\]\[day\]\[session\] \=\> jumlah mapel  
        $subjectTimeTracker \= \[\]; // Format: \[subject\_id\] \=\> array of unique \[day\_session\]

        // 1\. MAPPING DATA (Mengelompokkan 3000 gen)  
        foreach ($chromosome\['genes'\] as $gene) {  
            $day \= $gene\['day\_index'\];  
            $ses \= $gene\['time\_session\_id'\];  
            $room \= $gene\['room\_id'\];  
            $stu \= $gene\['student\_id'\];  
            $lvl \= $gene\['level\_id'\];  
            $desk \= $gene\['desk\_number'\];  
            $sub \= $gene\['subject\_id'\];

            // Inisialisasi struktur array jika belum ada  
            if(\!isset($roomSessionTracker\[$day\]\[$ses\]\[$room\])) {  
                $roomSessionTracker\[$day\]\[$ses\]\[$room\] \= \[  
                    'count' \=\> 0,   
                    'levels' \=\> \[\],   
                    'desks' \=\> \[\]  
                \];  
            }

            // A. Pencatatan untuk Evaluasi Ruang & Meja  
            $roomSessionTracker\[$day\]\[$ses\]\[$room\]\['count'\]++;  
            $roomSessionTracker\[$day\]\[$ses\]\[$room\]\['levels'\]\[$lvl\] \= true; // Catat level\_id  
              
            // Cek duplikasi meja  
            if(isset($roomSessionTracker\[$day\]\[$ses\]\[$room\]\['desks'\]\[$desk\])) {  
                $penalty \+= 100; // HARD: Meja bentrok  
            }  
            $roomSessionTracker\[$day\]\[$ses\]\[$room\]\['desks'\]\[$desk\] \= true;

            // B. Pencatatan Waktu Siswa (Bentrok Pribadi)  
            if(\!isset($studentTimeTracker\[$stu\]\[$day\]\[$ses\])) {  
                $studentTimeTracker\[$stu\]\[$day\]\[$ses\] \= 0;  
            }  
            $studentTimeTracker\[$stu\]\[$day\]\[$ses\]++;  
            if($studentTimeTracker\[$stu\]\[$day\]\[$ses\] \> 1\) {  
                $penalty \+= 100; // HARD: 1 Siswa ujian 2 mapel di jam & hari yang sama  
            }

            // C. Pencatatan Penyebaran Mata Pelajaran  
            $timeKey \= $day . '\_' . $ses;  
            $subjectTimeTracker\[$sub\]\[$timeKey\] \= true;  
        }

        // 2\. EVALUASI HARD CONSTRAINTS RUANGAN (Overcapacity & Level Mixing)  
        foreach ($roomSessionTracker as $day \=\> $sessions) {  
            foreach ($sessions as $ses \=\> $rooms) {  
                foreach ($rooms as $roomId \=\> $data) {  
                      
                    // Cek Overcapacity  
                    $maxCapacity \= $roomsCapacity\[$roomId\];  
                    if ($data\['count'\] \> $maxCapacity) {  
                        $overflow \= $data\['count'\] \- $maxCapacity;  
                        $penalty \+= (100 \* $overflow);   
                    }

                    // Cek Level Mixing (Isolasi Angkatan)  
                    // Jika count dari array keys 'levels' lebih dari 1, berarti ada campuran tingkat  
                    if (count($data\['levels'\]) \> 1\) {  
                        $penalty \+= 100; // HARD: Ruangan bercampur Kelas 10, 11, dst.  
                    }

                    // Cek Underutilization (Soft Constraint)  
                    if ($data\['count'\] \> 0 && $data\['count'\] \< ($maxCapacity \* 0.3)) {  
                        // Jika ruangan terisi di bawah 30% dari kapasitasnya  
                        $penalty \+= 5;   
                    }  
                }  
            }  
        }

        // 3\. EVALUASI SOFT CONSTRAINTS PENYEBARAN MAPEL  
        foreach ($subjectTimeTracker as $subId \=\> $timeKeys) {  
            $sessionCount \= count($timeKeys);  
            // Jika satu mapel terpecah ke lebih dari 2 sesi/hari, beri penalti ringan  
            // Ini memaksa AG untuk mengumpulkan siswa yang ujian mapel sama ke sesi yang sama  
            if ($sessionCount \> 2\) {  
                $penalty \+= (10 \* ($sessionCount \- 2));   
            }  
        }

        // 4\. HITUNG SKOR AKHIR  
        $chromosome\['fitness'\] \= 1 / (1 \+ $penalty);  
    }  
      
    return $population;  
}

## **4\. EVOLUSI: CROSSOVER & MUTASI UNTUK MULTI-DIMENSI**

Karena gen kita memiliki banyak properti (ruang, sesi, tanggal, meja), mutasi harus dirancang secara cerdas agar cepat menemukan solusi.

### **A. Teknik Crossover (Uniform Crossover)**

Jangan gunakan *Single-point crossover* untuk jadwal yang sangat padat. Gunakan *Uniform Crossover* di mana setiap gen (alokasi seorang siswa untuk satu mapel) memiliki peluang 50/50 untuk diambil dari Induk A atau Induk B.

### **B. Teknik Mutasi Target (Targeted Mutation)**

Mutasi buta (mengacak semua angka) akan merusak jadwal yang sudah hampir sempurna. Lakukan mutasi bersyarat:

1. Pilih gen secara acak (berdasarkan *mutation rate*, misal 5%).  
2. Apa yang dimutasi?  
   * Jika gen tersebut mengalami bentrok waktu (siswa ujian 2 mapel bersamaan), **ubah day\_index atau time\_session\_id**\-nya.  
   * Jika gen tersebut berada di ruangan yang *overcapacity* atau mejanya ganda, **ubah room\_id atau desk\_number**\-nya, tapi biarkan waktunya tetap.

## **5\. TRANSLASI GEN KE DATABASE (FINALISASI)**

Ketika algoritma selesai (fitness \== 1.0 atau penalti terendah), sistem harus mengubah susunan gen menjadi format *Database Schema V2* (exam\_schedules dan exam\_allocations).

**Logika Penyimpanan:**

1. **Buat Skenario exam\_schedules unik:**  
   Sistem men- *scan* kromosom terbaik. Ambil setiap kombinasi unik dari \[subject\_id, day\_index, time\_session\_id\]. Insert kombinasi tersebut ke tabel exam\_schedules (Sistem mengonversi day\_index 1 menjadi Tanggal aktual dari start\_date sesi ujian).  
2. **Insert exam\_allocations:**  
   Setelah ID exam\_schedules tercipta, sistem melalukan iterasi ulang ke kromosom. Untuk setiap gen, cari ID Schedule-nya, lalu masukkan \[schedule\_id, room\_id, student\_id, desk\_number\] secara massal (Bulk Insert) ke tabel exam\_allocations.

Dengan pemisahan logika yang rapi di atas, Algoritma Genetika dijamin dapat memecahkan masalah jadwal sekompleks apa pun di SMAN 3 Bontang secara akurat.