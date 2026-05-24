<?php

namespace App\Jobs;

use App\Models\ExamSession;
use App\Models\GaAllocationLog;
use App\Services\GeneticAlgorithmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateAllocationJob implements ShouldQueue
{
    use Queueable;

    protected $examSessionId;
    public $timeout = 300; // Meningkat ke 5 menit sesuai keputusan V2

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
        $session = ExamSession::find($this->examSessionId);
        
        if (!$session) return;

        try {
            // Jalankan evolusi Algoritma Genetika V2
            $agService = new GeneticAlgorithmService($this->examSessionId);
            $result = $agService->runEvolution();

            if (empty($result['chromosome'])) {
                throw new \Exception("Gagal melakukan evolusi: Tidak ada kromosom terbaik yang dihasilkan.");
            }

            // Translasi hasil evolusi ke tabel exam_schedules dan exam_allocations
            $agService->translateToDatabase($result['chromosome']);

            // Catat log kesuksesan
            GaAllocationLog::create([
                'exam_session_id' => $this->examSessionId,
                'total_generations' => $result['generations'],
                'best_fitness_score' => $result['fitness'],
                'execution_time_seconds' => microtime(true) - $startTime
            ]);

            // Update status sesi menjadi Selesai
            $session->update(['allocation_status' => 'Completed']);

        } catch (\Exception $e) {
            Log::error("GenerateAllocationJob failed: " . $e->getMessage());

            // Catat log kegagalan
            GaAllocationLog::create([
                'exam_session_id' => $this->examSessionId,
                'total_generations' => 0,
                'best_fitness_score' => 0,
                'execution_time_seconds' => microtime(true) - $startTime,
                'error_message' => $e->getMessage()
            ]);

            // Update status sesi menjadi Gagal
            $session->update(['allocation_status' => 'Failed']);
        }
    }
}
