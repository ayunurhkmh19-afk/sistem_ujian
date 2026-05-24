# Skill Agent: Validator Perhitungan Algoritma Genetika V2

**Tujuan:** Dokumen ini menjadi panduan bagi agen/developer untuk **memvalidasi setiap perhitungan** dalam Algoritma Genetika V2 secara teliti. Setiap kali menulis atau memodifikasi kode AG, gunakan checklist ini untuk memastikan **tidak ada logika yang terlewat atau salah hitung**.

---

## 1. Validasi Pre-Flight Check (Step 5 Wizard)

### Formula Utama

```
Kapasitas_Efektif = Total_Kapasitas_Semua_Ruangan × Jumlah_Sesi_Waktu_Aktif

UNTUK SETIAP level_id:
  JIKA Jumlah_Siswa_Level > Kapasitas_Efektif
    → TOLAK eksekusi AG
    → Tampilkan: "Kapasitas tidak cukup untuk [Nama Level]"
```

### Checklist Validasi

- [ ] **Kapasitas dihitung dari ruangan yang DIPILIH di Step 3**, bukan seluruh tabel `rooms`
- [ ] **Sesi waktu dihitung dari yang DIPILIH di Step 3**, bukan seluruh tabel `time_sessions`
- [ ] **Siswa dihitung PER LEVEL**, bukan total semua siswa
- [ ] **Ruangan TIDAK dibagi per level di pre-flight** — yang dihitung adalah total kapasitas × sesi (karena AG yang akan mendistribusikan nanti)
- [ ] **Edge case: Level tanpa siswa** — Jika mapel dipilih tapi tidak ada siswa di level tersebut, beri warning (bukan error)

### Test Case Contoh

```
Ruangan dipilih: [R1(30), R2(30), R3(25)] → Total = 85
Sesi waktu aktif: [Sesi1, Sesi2, Sesi3] → Jumlah = 3
Kapasitas_Efektif = 85 × 3 = 255

Siswa Kelas 10: 120 → 120 ≤ 255 ✅ LOLOS
Siswa Kelas 11: 110 → 110 ≤ 255 ✅ LOLOS
Siswa Kelas 12: 260 → 260 > 255 ❌ TOLAK
```

> [!CAUTION]
> **Jebakan umum:** Menghitung `Total_Siswa_Semua_Level` bukannya `per level`. Yang benar adalah pengecekan per level karena setiap level punya jadwal mapel sendiri-sendiri.

---

## 2. Validasi Inisialisasi Populasi

### Aturan Generate Gen

Setiap kromosom harus memiliki **1 gen untuk setiap kombinasi [student_id, subject_id]** di mana `subject.level_id == student.studentClass.level_id`.

### Formula Jumlah Gen

```
Total_Gen_Per_Kromosom = Σ (Jumlah_Siswa_Level_X × Jumlah_Mapel_Level_X)
                       untuk setiap Level X yang aktif

Contoh:
  Level 10: 120 siswa × 5 mapel = 600 gen
  Level 11: 110 siswa × 6 mapel = 660 gen
  Level 12: 115 siswa × 5 mapel = 575 gen
  ─────────────────────────────────────
  Total: 1835 gen per kromosom
```

### Checklist Validasi

- [ ] **Setiap siswa punya gen untuk SETIAP mapel di level-nya** — Tidak boleh ada siswa yang hanya punya gen untuk sebagian mapel
- [ ] **Gen awal harus memiliki `day_index` valid** — Dalam range `[1, total_hari_ujian]` di mana `total_hari_ujian = end_date - start_date + 1` (hitung inklusif)
- [ ] **Gen awal harus memiliki `time_session_id` valid** — Hanya dari sesi yang dipilih di Step 3
- [ ] **Gen awal harus memiliki `room_id` valid** — Hanya dari ruangan yang dipilih di Step 3
- [ ] **`desk_number` di range [1, room.capacity]**
- [ ] **`level_id` di gen harus COCOK dengan level siswa** — Disisipkan dari `student → studentClass → level`
- [ ] **Jumlah gen per kromosom = hitungan formula di atas** — Verifikasi `count($chromosome['genes'])`

### Distribusi Mapel ke Hari

```
Mapel_Per_Hari_Per_Level = ceil(Jumlah_Mapel_Level / Total_Hari)

Contoh: 15 mapel, 5 hari → 3 mapel/hari/level
AG menyebarkan secara merata, BUKAN menaruh semua di hari 1.
```

> [!WARNING]
> **Jebakan umum:** Lupa bahwa distribusi mapel ke hari harus MERATA. Jika inisialisasi acak, populasi awal mungkin menaruh 10 mapel di hari 1 dan 0 di hari lainnya → AG butuh banyak generasi untuk memperbaiki.
> 
> **Solusi:** Inisialisasi heuristik — distribusikan mapel secara round-robin ke hari-hari yang tersedia.

---

## 3. Validasi Fitness Evaluation (PALING KRITIS)

### A. Struktur Data Tracker

Fitness function harus membangun 3 tracker:

```php
$roomSessionTracker  = []; // [day][session][room] => {count, levels[], desks[]}
$studentTimeTracker  = []; // [student_id][day][session] => jumlah_mapel
$subjectTimeTracker  = []; // [subject_id] => [day_session_keys[]]
```

### B. Hard Constraint #1: Overcapacity (+100 per overflow)

```
UNTUK SETIAP [day][session][room]:
  JIKA count_siswa > room.capacity:
    penalty += 100 × (count_siswa - room.capacity)
```

**Checklist:**
- [ ] Kapasitas diambil dari data `rooms` yang benar (ID cocok)
- [ ] Overflow dihitung per orang kelebihan, BUKAN flat +100
- [ ] Pengecekan dilakukan SETELAH semua gen dimap, bukan saat iterasi per gen

### C. Hard Constraint #2: Level Mixing (+100 per ruangan)

```
UNTUK SETIAP [day][session][room]:
  JIKA count(unique_level_ids) > 1:
    penalty += 100
```

**Checklist:**
- [ ] Level mixing dicek per `[day][session][room]`, BUKAN hanya per `[room]`
- [ ] `level_id` dicatat sebagai set/unique (bukan counter) di tracker
- [ ] Penalti dihitung SEKALI per ruangan yang melanggar (bukan per siswa)

### D. Hard Constraint #3: Student Collision — Bentrok Waktu (+100)

```
UNTUK SETIAP [student_id][day][session]:
  JIKA jumlah_mapel > 1:
    penalty += 100 × (jumlah_mapel - 1)
```

**Checklist:**
- [ ] Tracker menghitung jumlah MAPEL per siswa per [day][session], bukan kehadiran
- [ ] Penalti dihitung proporsional: jika siswa punya 3 mapel di jam yang sama, penalti = 100 × 2
- [ ] Cek dilakukan saat iterasi gen (real-time tracking)

> [!CAUTION]
> **Jebakan umum:** Menggunakan `isset()` saja tanpa increment counter. Harus pakai counter (`++`) agar bisa deteksi lebih dari 2 bentrokan.

### E. Hard Constraint #4: Desk Collision (+100)

```
UNTUK SETIAP [day][session][room]:
  JIKA desk_number sudah dipakai siswa lain:
    penalty += 100
```

**Checklist:**
- [ ] Desk collision dicek per `[day][session][room]`, BUKAN hanya per `[room]`
- [ ] Tracking desk: `$roomSessionTracker[day][ses][room]['desks'][desk] = true`
- [ ] Jika sudah `isset`, langsung tambah penalti

### F. Soft Constraint #1: Subject Scattering (+10)

```
UNTUK SETIAP subject_id:
  session_count = count(unique [day_session] keys)
  JIKA session_count > 2:
    penalty += 10 × (session_count - 2)
```

**Checklist:**
- [ ] Tracking menggunakan `[day . '_' . session]` sebagai key unik
- [ ] Threshold adalah 2 (bukan 1) — artinya mapel boleh terpecah ke maksimal 2 sesi
- [ ] Penalti proporsional: semakin banyak sesi terpecah, semakin besar penalti

### G. Soft Constraint #2: Room Underutilization (+5)

```
UNTUK SETIAP [day][session][room] yang TERISI:
  JIKA count_siswa < (room.capacity × 0.3):
    penalty += 5
```

**Checklist:**
- [ ] Hanya dicek untuk ruangan yang **TERISI** (`count > 0`), bukan ruangan kosong
- [ ] Threshold 30% dari kapasitas
- [ ] Penalti flat +5 (bukan proporsional)

### H. Formula Fitness Score

```
fitness = 1 / (1 + total_penalty)

Jika penalty = 0 → fitness = 1.0 (sempurna)
Jika penalty = 100 → fitness = 0.0099
Jika penalty = 1000 → fitness = 0.000999
```

**Checklist:**
- [ ] Pembagi adalah `1 + penalty`, BUKAN `penalty` (mencegah division by zero)
- [ ] Fitness di-assign ke `$chromosome['fitness']`
- [ ] Populasi dikembalikan setelah evaluasi

---

## 4. Validasi Uniform Crossover

### Logika

```
UNTUK SETIAP index gen [0..N-1]:
  JIKA random(0,1) >= 0.5:
    child_genes[i] = parent1_genes[i]
  LAINNYA:
    child_genes[i] = parent2_genes[i]
```

### Checklist Validasi

- [ ] Kedua parent harus punya **jumlah gen yang sama** (assert equal length)
- [ ] Setiap gen diambil utuh (seluruh properti: student_id, level_id, subject_id, dst)
- [ ] Gen anak untuk `student_id X, subject_id Y` harus tetap berisi student_id X dan subject_id Y — **hanya day, session, room, desk yang berubah**
- [ ] Child fitness di-reset ke 0

> [!CAUTION]
> **Jebakan FATAL:** Jika parent 1 dan parent 2 memiliki urutan gen yang BERBEDA (misal gen index 5 di parent 1 adalah [student 10, mapel MTK] tapi di parent 2 adalah [student 20, mapel FIS]), maka crossover akan **MENGHILANGKAN** alokasi siswa.
>
> **Solusi Wajib:** Gen harus DIURUTKAN dengan key yang konsisten `[student_id, subject_id]` di kedua parent SEBELUM crossover, ATAU gunakan mapping by key (bukan by index).

---

## 5. Validasi Targeted Mutation

### Logika

```
UNTUK SETIAP gen (dengan probability = mutation_rate):
  1. Cek jenis pelanggaran gen ini:
  
  JIKA student_collision (siswa bentrok waktu):
    → Ubah day_index ATAU time_session_id (pindah ke waktu lain)
    → JANGAN ubah room_id/desk_number
    
  JIKA overcapacity ATAU desk_collision:
    → Ubah room_id DAN desk_number (pindah ke ruang lain)
    → JANGAN ubah day_index/time_session_id
    
  JIKA tidak ada pelanggaran spesifik:
    → Mutasi acak salah satu: day_index, time_session_id, room_id, desk_number
```

### Checklist Validasi

- [ ] Mutation rate diterapkan per gen (bukan per kromosom)
- [ ] Mutasi TIDAK MENGUBAH `student_id`, `level_id`, `subject_id` — hanya dimensi alokasi (waktu/ruang)
- [ ] Nilai baru `day_index` harus dalam range valid `[1, total_hari]`
- [ ] Nilai baru `time_session_id` harus dari daftar sesi yang dipilih
- [ ] Nilai baru `room_id` harus dari daftar ruangan yang dipilih
- [ ] Nilai baru `desk_number` harus dalam `[1, room_baru.capacity]`
- [ ] Pengecekan pelanggaran di targeted mutation boleh bersifat **heuristik** (tidak perlu evaluasi fitness penuh)

---

## 6. Validasi Translasi Gen ke Database

### Step 1: Extract Unique Schedules

```
unique_schedules = DISTINCT [subject_id, day_index, time_session_id]
                   dari semua gen di kromosom terbaik

UNTUK SETIAP unique_schedule:
  actual_date = start_date + (day_index - 1) hari
  INSERT INTO exam_schedules:
    exam_session_id, subject_id, time_session_id, exam_date=actual_date
```

### Step 2: Insert Allocations

```
UNTUK SETIAP gen di kromosom terbaik:
  schedule_id = CARI exam_schedules WHERE
    subject_id == gen.subject_id AND
    time_session_id == gen.time_session_id AND
    exam_date == start_date + (gen.day_index - 1)
    
  INSERT INTO exam_allocations:
    exam_schedule_id=schedule_id, room_id, student_id, desk_number
```

### Checklist Validasi

- [ ] **Konversi `day_index` ke tanggal**: `Carbon::parse(start_date)->addDays(day_index - 1)` — Perhatikan `day_index` dimulai dari 1 (bukan 0)
- [ ] **Jumlah exam_schedules** yang di-insert harus = jumlah kombinasi unik `[subject_id, day_index, time_session_id]`
- [ ] **Jumlah exam_allocations** harus = jumlah gen di kromosom terbaik
- [ ] **Tidak ada gen ganda** — Jika ada duplikat `[student_id, subject_id]`, ambil yang pertama atau flag error
- [ ] **Schedule lookup** harus cocok (jangan sampai gen merujuk ke schedule yang tidak ada)
- [ ] **Gunakan DB::transaction** — Jika gagal di tengah jalan, rollback seluruhnya
- [ ] **Bersihkan data lama** — Hapus `exam_schedules` dan `exam_allocations` untuk session ini sebelum insert

> [!WARNING]
> **Jebakan:** `day_index = 1` berarti hari PERTAMA (`start_date + 0`), BUKAN `start_date + 1`. Pastikan formula `addDays(day_index - 1)`.

---

## 7. Checklist Invariant Global (HARUS SELALU BENAR)

Setiap kali kode AG dimodifikasi, verifikasi invariant berikut:

| # | Invariant | Cara Cek |
|---|-----------|----------|
| 1 | Setiap siswa punya tepat N gen (N = jumlah mapel di level-nya) | `count(genes where student_id == X) == expected` |
| 2 | Tidak ada gen dengan `student_id` yang tidak ada di database | `in_array(gen.student_id, valid_student_ids)` |
| 3 | Tidak ada gen dengan `subject_id` yang tidak sesuai level siswa | `subject.level_id == student.level_id` |
| 4 | `room_id` dan `desk_number` selalu valid | `room exists && desk <= capacity` |
| 5 | `day_index` selalu dalam `[1, total_hari]` | `1 <= day_index <= (end_date - start_date + 1)` |
| 6 | `time_session_id` selalu dari daftar aktif | `in_array(time_session_id, active_sessions)` |
| 7 | Fitness terbaik monoton naik (non-decreasing) | Log fitness per generasi |
| 8 | Populasi selalu berisi `populationSize` kromosom | `count(population) == populationSize` |

---

## 8. Template Unit Test PHP

```php
class GeneticAlgorithmFitnessTest extends TestCase
{
    /** @test */
    public function overcapacity_gives_100_penalty_per_overflow()
    {
        // Room capacity = 2, tapi 4 siswa di [day=1][session=1][room=1]
        // Expected penalty: 100 × (4-2) = 200
    }

    /** @test */
    public function level_mixing_gives_100_penalty()
    {
        // 2 siswa level 10 + 1 siswa level 11 di [day=1][session=1][room=1]
        // Expected penalty: 100 (satu ruangan bercampur)
    }

    /** @test */
    public function student_collision_gives_100_penalty()
    {
        // Student A punya 2 gen di [day=1][session=1] (2 mapel berbeda)
        // Expected penalty: 100 × 1
    }

    /** @test */
    public function desk_collision_gives_100_penalty()
    {
        // 2 siswa di desk_number=5 di [day=1][session=1][room=1]
        // Expected penalty: 100
    }

    /** @test */
    public function subject_scattering_gives_10_penalty_per_extra_session()
    {
        // Mapel MTK terpecah ke 4 sesi → penalty = 10 × (4-2) = 20
    }

    /** @test */
    public function perfect_chromosome_has_fitness_1()
    {
        // Kromosom tanpa pelanggaran apapun → fitness = 1.0
    }

    /** @test */
    public function translation_produces_correct_schedule_count()
    {
        // 3 mapel × 5 hari = maks 15 jadwal unik (tergantung distribusi AG)
    }

    /** @test */
    public function day_index_to_date_conversion_is_correct()
    {
        // start_date = 2026-10-12, day_index = 3
        // Expected: 2026-10-14 (bukan 2026-10-15)
    }
}
```

---

## 9. Panduan Debugging AG

Jika AG tidak konvergen (fitness tetap rendah setelah ratusan generasi):

1. **Log fitness per generasi** — Plot grafik. Jika flat, mutation rate terlalu rendah atau constraint terlalu ketat
2. **Cek distribusi penalti** — Constraint mana yang paling sering dilanggar? Fokuskan targeted mutation ke situ
3. **Periksa populasi awal** — Apakah inisialisasi heuristik sudah merata? Atau terlalu acak?
4. **Naikkan populasi** — Dari 50 ke 100 jika search space terlalu besar
5. **Cek elitism** — Pastikan 2 terbaik SELALU dipertahankan tanpa mutasi
6. **Tambahkan repair function** — Setelah crossover/mutasi, perbaiki constraint paling mudah (desk collision → cari desk kosong)

---

## 10. Ringkasan Bobot Penalti

| Constraint | Tipe | Bobot | Proporsional? |
|------------|------|-------|---------------|
| Overcapacity | Hard | +100 | Per orang kelebihan |
| Level Mixing | Hard | +100 | Per ruangan |
| Student Collision | Hard | +100 | Per bentrokan ekstra |
| Desk Collision | Hard | +100 | Per meja duplikat |
| Subject Scattering | Soft | +10 | Per sesi ekstra (>2) |
| Room Underutilization | Soft | +5 | Flat per ruangan |
