<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAllocationJob implements ShouldQueue
{
    use Queueable;

    protected $examSessionId;
    public $timeout = 120; // Maksimal 2 menit eksekusi

    /**
     * Create a new job instance.
     */
    public function __construct($examSessionId)
    {
        $this->examSessionId = $examSessionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $session = \App\Models\ExamSession::find($this->examSessionId);
        
        if (!$session) return;

        try {
            // Bersihkan data lama menggunakan transaksi DB agar aman
            \Illuminate\Support\Facades\DB::transaction(function () {
                \App\Models\ExamAllocation::where('exam_session_id', $this->examSessionId)->delete();
            });

            // Eksekusi Service Algoritma Genetika
            $agService = new \App\Services\GeneticAlgorithmService($this->examSessionId);
            $result = $agService->runEvolution();

            if (empty($result['chromosome'])) {
                throw new \Exception("Gagal men-generate alokasi: Tidak ada hasil dari algoritma.");
            }

            $insertData = [];
            $seenStudents = [];
            foreach ($result['chromosome'] as $gen) {
                if (isset($seenStudents[$gen['student_id']])) continue;
                $seenStudents[$gen['student_id']] = true;

                $insertData[] = [
                    'exam_session_id' => $this->examSessionId,
                    'student_id'      => $gen['student_id'],
                    'room_id'         => $gen['room_id'],
                    'desk_number'     => $gen['desk_number'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            // Gunakan chunk insert (per 500 data) agar memori aman
            foreach (array_chunk($insertData, 500) as $chunk) {
                \App\Models\ExamAllocation::insert($chunk);
            }

            // Catat Kesuksesan di Log
            \App\Models\GaAllocationLog::create([
                'exam_session_id' => $this->examSessionId,
                'total_generations' => $result['generations'],
                'best_fitness_score' => $result['fitness'],
                'execution_time_seconds' => microtime(true) - $startTime
            ]);

            $session->update(['allocation_status' => 'Completed']);

        } catch (\Exception $e) {
            // Catat Kegagalan
            \App\Models\GaAllocationLog::create([
                'exam_session_id' => $this->examSessionId,
                'total_generations' => 0,
                'best_fitness_score' => 0,
                'execution_time_seconds' => microtime(true) - $startTime,
                'error_message' => $e->getMessage()
            ]);
            $session->update(['allocation_status' => 'Failed']);
        }
    }
}
