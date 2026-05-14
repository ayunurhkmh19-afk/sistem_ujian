# **DEKOMPOSISI DETAIL TAHAP IMPLEMENTASI: ALGORITMA GENETIKA (AG)**

**Modul: Optimasi Alokasi Ruang & Jadwal Ujian (Sistem SMAN 3 Bontang)**

Dokumen ini merincikan langkah-langkah teknis secara komprehensif untuk mengimplementasikan Algoritma Genetika sebagai *Background Job* dalam sistem, lengkap dengan logika komputasi genetikanya.

## **TAHAP 1: Persiapan Database & Model (Fase Skema AG)**

Fase ini bertujuan menyiapkan kolom status untuk *tracking* antrean algoritma dan tabel *log* untuk mencatat riwayat pemrosesan.

### **1.1 Update Tabel exam\_sessions**

Tambahkan indikator status pemrosesan jadwal.

* **Perintah Terminal:** php artisan make:migration add\_allocation\_status\_to\_exam\_sessions\_table  
* **Kode Migrasi (up method):**  
  Schema::table('exam\_sessions', function (Blueprint $table) {  
      $table-\>enum('allocation\_status', \['Pending', 'Processing', 'Completed', 'Failed'\])  
            \-\>default('Pending')  
            \-\>after('is\_active');  
  });

### **1.2 Pembuatan Tabel ga\_allocation\_logs**

Tabel ini merekam performa Algoritma Genetika setiap kali dieksekusi.

* **Perintah Terminal:** php artisan make:migration create\_ga\_allocation\_logs\_table  
* **Kode Migrasi (up method):**  
  Schema::create('ga\_allocation\_logs', function (Blueprint $table) {  
      $table-\>id();  
      $table-\>foreignId('exam\_session\_id')-\>constrained('exam\_sessions')-\>onDelete('cascade');  
      $table-\>integer('total\_generations');  
      $table-\>float('best\_fitness\_score', 8, 4);  
      $table-\>float('execution\_time\_seconds', 8, 2);  
      $table-\>text('error\_message')-\>nullable();  
      $table-\>timestamps();  
  });

### **1.3 Setup Eloquent Models Terkait**

Definisikan relasi secara eksplisit agar mudah dipanggil melalui Controller atau Job.

* **Model ExamSession.php:**  
  public function gaLogs() {  
      return $this-\>hasMany(GaAllocationLog::class, 'exam\_session\_id');  
  }

* **Model GaAllocationLog.php:**  
  protected $fillable \= \['exam\_session\_id', 'total\_generations', 'best\_fitness\_score', 'execution\_time\_seconds', 'error\_message'\];

## **TAHAP 2: Pembuatan Class Service Algoritma Genetika (Core Logic)**

Logika inti dari AG. Kita akan mengubah *skeleton* menjadi fungsi PHP nyata yang menghitung *penalty* (denda) berdasarkan kapasitas ruang dan duplikasi meja.

### **2.1 File app/Services/GeneticAlgorithmService.php**

namespace App\\Services;

use App\\Models\\Room;  
use App\\Models\\Student;

class GeneticAlgorithmService  
{  
    protected $students;  
    protected $rooms;  
    protected $populationSize \= 50;  
    protected $maxGenerations \= 500;  
    protected $mutationRate \= 0.10; // 10% kemungkinan mutasi

    public function \_\_construct($examSessionId)  
    {  
        // Ambil data ruangan dan kapasitasnya (Misal hanya ruang yang di-assign ke sesi ini)  
        $this-\>rooms \= Room::all()-\>toArray();   
          
        // Ambil daftar siswa yang ikut ujian  
        $this-\>students \= Student::all()-\>toArray();   
    }

    public function runEvolution()  
    {  
        $population \= $this-\>initializePopulation();  
        $generation \= 0;  
        $bestFitness \= 0;  
        $bestChromosome \= \[\];

        while ($generation \< $this-\>maxGenerations && $bestFitness \< 1.0) {  
            $population \= $this-\>evaluateFitness($population);  
              
            // Urutkan dari fitness tertinggi (Desc)  
            usort($population, fn($a, $b) \=\> $b\['fitness'\] \<=\> $a\['fitness'\]);  
              
            $bestChromosome \= $population\[0\];  
            $bestFitness \= $bestChromosome\['fitness'\];

            if ($bestFitness \>= 1.0) break; // Sempurna, hentikan evolusi

            $newPopulation \= \[\];  
            // Elitism: Pertahankan 2 kromosom terbaik ke generasi berikutnya  
            $newPopulation\[\] \= $population\[0\];  
            $newPopulation\[\] \= $population\[1\];

            // Siklus Crossover & Mutasi untuk sisa populasi  
            while (count($newPopulation) \< $this-\>populationSize) {  
                $parent1 \= $this-\>tournamentSelection($population);  
                $parent2 \= $this-\>tournamentSelection($population);

                $child \= $this-\>crossover($parent1, $parent2);  
                $child \= $this-\>mutate($child);

                $newPopulation\[\] \= $child;  
            }  
            $population \= $newPopulation;  
            $generation++;  
        }

        return \[  
            'chromosome' \=\> $bestChromosome\['genes'\],  
            'fitness' \=\> $bestFitness,  
            'generations' \=\> $generation  
        \];  
    }

    // 1\. INISIALISASI POPULASI  
    private function initializePopulation()  
    {  
        $population \= \[\];  
        for ($i \= 0; $i \< $this-\>populationSize; $i++) {  
            $chromosome \= \[\];  
            foreach ($this-\>students as $student) {  
                // Alokasi acak ruang dan meja untuk awal  
                $randomRoom \= $this-\>rooms\[array\_rand($this-\>rooms)\];  
                $chromosome\[\] \= \[  
                    'student\_id' \=\> $student\['id'\],  
                    'room\_id' \=\> $randomRoom\['id'\],  
                    'desk\_number' \=\> rand(1, $randomRoom\['capacity'\])  
                \];  
            }  
            $population\[\] \= \['genes' \=\> $chromosome, 'fitness' \=\> 0\];  
        }  
        return $population;  
    }

    // 2\. EVALUASI FITNESS BERDASARKAN PENALTI  
    private function evaluateFitness($population)  
    {  
        foreach ($population as &$indv) {  
            $penalty \= 0;  
            $roomCounts \= \[\];  
            $deskTracker \= \[\];

            foreach ($indv\['genes'\] as $gene) {  
                $rId \= $gene\['room\_id'\];  
                $desk \= $gene\['desk\_number'\];

                // Hitung jumlah siswa di satu ruangan  
                if (\!isset($roomCounts\[$rId\])) $roomCounts\[$rId\] \= 0;  
                $roomCounts\[$rId\]++;

                // Lacak duplikasi meja di ruangan yang sama  
                $key \= $rId . '\_' . $desk;  
                if (isset($deskTracker\[$key\])) {  
                    $penalty \+= 100; // HARD CONSTRAINT: Meja sama  
                } else {  
                    $deskTracker\[$key\] \= true;  
                }  
            }

            // Cek kelebihan kapasitas  
            foreach ($this-\>rooms as $room) {  
                $rId \= $room\['id'\];  
                if (isset($roomCounts\[$rId\]) && $roomCounts\[$rId\] \> $room\['capacity'\]) {  
                    $penalty \+= 100 \* ($roomCounts\[$rId\] \- $room\['capacity'\]); // HARD CONSTRAINT  
                }  
            }

            // Hitung skor fitness (Makin kecil penalti, fitness mendekati 1\)  
            $indv\['fitness'\] \= 1 / (1 \+ $penalty);  
        }  
        return $population;  
    }

    // 3\. TOURNAMENT SELECTION  
    private function tournamentSelection($population)  
    {  
        $tournamentSize \= 5;  
        $best \= null;  
        for ($i \= 0; $i \< $tournamentSize; $i++) {  
            $randomIndv \= $population\[array\_rand($population)\];  
            if ($best \=== null || $randomIndv\['fitness'\] \> $best\['fitness'\]) {  
                $best \= $randomIndv;  
            }  
        }  
        return $best;  
    }

    // 4\. SINGLE POINT CROSSOVER  
    private function crossover($parent1, $parent2)  
    {  
        $genesLength \= count($parent1\['genes'\]);  
        $crossoverPoint \= rand(1, $genesLength \- 1);

        $childGenes \= array\_merge(  
            array\_slice($parent1\['genes'\], 0, $crossoverPoint),  
            array\_slice($parent2\['genes'\], $crossoverPoint)  
        );

        return \['genes' \=\> $childGenes, 'fitness' \=\> 0\];  
    }

    // 5\. MUTASI  
    private function mutate($chromosome)  
    {  
        foreach ($chromosome\['genes'\] as &$gene) {  
            if (rand(1, 100\) \<= ($this-\>mutationRate \* 100)) {  
                // Ubah acak ruangan atau meja  
                $randomRoom \= $this-\>rooms\[array\_rand($this-\>rooms)\];  
                $gene\['room\_id'\] \= $randomRoom\['id'\];  
                $gene\['desk\_number'\] \= rand(1, $randomRoom\['capacity'\]);  
            }  
        }  
        return $chromosome;  
    }  
}

## **TAHAP 3: Implementasi Background Job (Fase Eksekusi)**

Algoritma dipanggil di belakang layar. Kita akan memasukkan proses *Database Chunking* jika pesertanya mencapai ratusan agar RAM server tidak meledak saat eksekusi.

### **3.1 Pembuatan Job GenerateAllocationJob.php**

namespace App\\Jobs;

use App\\Models\\ExamSession;  
use App\\Models\\ExamAllocation;  
use App\\Models\\GaAllocationLog;  
use App\\Services\\GeneticAlgorithmService;  
use Illuminate\\Bus\\Queueable;  
use Illuminate\\Contracts\\Queue\\ShouldQueue;  
use Illuminate\\Foundation\\Bus\\Dispatchable;  
use Illuminate\\Queue\\InteractsWithQueue;  
use Illuminate\\Queue\\SerializesModels;  
use Illuminate\\Support\\Facades\\DB;

class GenerateAllocationJob implements ShouldQueue  
{  
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout \= 120; // Maksimal 2 menit eksekusi  
    protected $examSessionId;

    public function \_\_construct($examSessionId)  
    {  
        $this-\>examSessionId \= $examSessionId;  
    }

    public function handle()  
    {  
        $startTime \= microtime(true);  
        $session \= ExamSession::find($this-\>examSessionId);  
          
        if(\!$session) return;

        try {  
            // Bersihkan data lama menggunakan transaksi DB agar aman  
            DB::transaction(function () {  
                ExamAllocation::where('exam\_session\_id', $this-\>examSessionId)-\>delete();  
            });

            // Eksekusi Service Algoritma Genetika  
            $agService \= new GeneticAlgorithmService($this-\>examSessionId);  
            $result \= $agService-\>runEvolution();

            $insertData \= \[\];  
            foreach ($result\['chromosome'\] as $gen) {  
                $insertData\[\] \= \[  
                    'exam\_session\_id' \=\> $this-\>examSessionId,  
                    'student\_id'      \=\> $gen\['student\_id'\],  
                    'room\_id'         \=\> $gen\['room\_id'\],  
                    'desk\_number'     \=\> $gen\['desk\_number'\],  
                    'created\_at'      \=\> now(),  
                    'updated\_at'      \=\> now(),  
                \];  
            }

            // Gunakan chunk insert (per 500 data) agar memori aman  
            foreach (array\_chunk($insertData, 500\) as $chunk) {  
                ExamAllocation::insert($chunk);  
            }

            // Catat Kesuksesan di Log  
            GaAllocationLog::create(\[  
                'exam\_session\_id' \=\> $this-\>examSessionId,  
                'total\_generations' \=\> $result\['generations'\],  
                'best\_fitness\_score' \=\> $result\['fitness'\],  
                'execution\_time\_seconds' \=\> microtime(true) \- $startTime  
            \]);

            $session-\>update(\['allocation\_status' \=\> 'Completed'\]);

        } catch (\\Exception $e) {  
            // Catat Kegagalan  
            GaAllocationLog::create(\[  
                'exam\_session\_id' \=\> $this-\>examSessionId,  
                'total\_generations' \=\> 0,  
                'best\_fitness\_score' \=\> 0,  
                'execution\_time\_seconds' \=\> microtime(true) \- $startTime,  
                'error\_message' \=\> $e-\>getMessage()  
            \]);  
            $session-\>update(\['allocation\_status' \=\> 'Failed'\]);  
        }  
    }  
}

## **TAHAP 4: Integrasi Controller & UI (Fase UX & Polling)**

Panitia tidak perlu menekan *refresh* (F5) terus menerus. Kita akan menggunakan **AJAX Polling sederhana** di Blade agar status berubah otomatis dari *Processing* ke *Completed*.

### **4.1 Route dan Controller Panitia**

Tambahkan endpoint API kecil untuk mengecek status oleh Javascript.

// routes/web.php  
Route::post('/panitia/sesi/{id}/generate-alokasi', \[PanitiaSessionController::class, 'generateAlokasi'\])-\>name('panitia.generate.alokasi');  
Route::get('/panitia/sesi/{id}/status-ag', \[PanitiaSessionController::class, 'cekStatusAG'\])-\>name('panitia.status.ag');

// PanitiaSessionController.php  
public function generateAlokasi($id) {  
    $sesi \= ExamSession::findOrFail($id);  
    $sesi-\>update(\['allocation\_status' \=\> 'Processing'\]);  
    \\App\\Jobs\\GenerateAllocationJob::dispatch($id);  
    return back();  
}

public function cekStatusAG($id) {  
    $sesi \= ExamSession::findOrFail($id);  
    return response()-\>json(\['status' \=\> $sesi-\>allocation\_status\]);  
}

### **4.2 Tampilan Tombol dengan Polling Otomatis (Blade View)**

\<div class="bg-white rounded-lg shadow p-6 mb-6 flex justify-between items-center" id="ag-container"\>  
    \<div\>  
        \<h3 class="text-lg font-bold"\>Alokasi Ruang Otomatis (Algoritma Genetika)\</h3\>  
        \<p class="text-sm text-gray-500"\>Membagi peserta ke ruangan tanpa bentrok.\</p\>  
    \</div\>  
      
    \<div id="ag-action-box"\>  
        @if($sesi-\>allocation\_status \=== 'Processing' || $sesi-\>allocation\_status \=== 'Pending')  
            \<button disabled class="bg-yellow-500 text-white px-4 py-2 rounded opacity-75 cursor-not-allowed"\>  
                \<svg class="animate-spin h-5 w-5 mr-3 inline-block" viewBox="0 0 24 24"\>\<\!-- icon loading \--\>\</svg\>  
                Sedang Menyusun Jadwal...  
            \</button\>  
        @else  
            \<form action="{{ route('panitia.generate.alokasi', $sesi-\>id) }}" method="POST"\>  
                @csrf  
                \<button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-bold transition" onclick="return confirm('Proses ini akan menimpa alokasi lama. Lanjutkan?')"\>  
                    Auto-Generate Ruangan  
                \</button\>  
            \</form\>  
              
            @if($sesi-\>allocation\_status \=== 'Completed')  
                \<p class="text-sm text-green-600 font-bold mt-2 text-right"\>✓ Selesai (Optimal)\</p\>  
            @elseif($sesi-\>allocation\_status \=== 'Failed')  
                \<p class="text-sm text-red-600 font-bold mt-2 text-right"\>⚠ Gagal Memproses\</p\>  
            @endif  
        @endif  
    \</div\>  
\</div\>

\<\!-- Script Polling Status (Hanya berjalan jika status sedang Processing) \--\>  
@if($sesi-\>allocation\_status \=== 'Processing' || $sesi-\>allocation\_status \=== 'Pending')  
\<script\>  
    document.addEventListener("DOMContentLoaded", function() {  
        let interval \= setInterval(function() {  
            fetch("{{ route('panitia.status.ag', $sesi-\>id) }}")  
                .then(response \=\> response.json())  
                .then(data \=\> {  
                    if(data.status \=== 'Completed' || data.status \=== 'Failed') {  
                        clearInterval(interval);  
                        window.location.reload(); // Refresh halaman saat background job selesai  
                    }  
                });  
        }, 3000); // Polling setiap 3 detik  
    });  
\</script\>  
@endif  
