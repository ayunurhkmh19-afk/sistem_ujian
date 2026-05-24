<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\Room;
use App\Models\Subject;
use App\Models\TimeSession;
use App\Models\Student;
use App\Models\Level;
use App\Models\ExamSchedule;
use App\Models\ExamAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneticAlgorithmService
{
    protected $session;
    protected $rooms;
    protected $timeSessions;
    protected $subjects;
    protected $students;
    protected $levels;
    protected $totalDays;

    protected $populationSize = 25;   // Diturunkan sesuai keputusan V2
    protected $maxGenerations = 100;  // Diturunkan sesuai keputusan V2
    protected $mutationRate = 0.10;   // 10%

    public function __construct($examSessionId)
    {
        $this->session = ExamSession::findOrFail($examSessionId);
        
        // Ambil data terpilih dari pivot
        $this->rooms = $this->session->rooms()->get()->toArray();
        $this->timeSessions = $this->session->timeSessions()->get()->toArray();
        $this->subjects = $this->session->subjects()->with('level')->get()->toArray();
        
        // Ambil levels yang aktif
        $activeLevelIds = collect($this->subjects)->pluck('level_id')->unique()->toArray();
        $this->levels = Level::whereIn('id', $activeLevelIds)->get()->toArray();

        // Ambil siswa yang terdaftar di level aktif
        $this->students = Student::whereHas('studentClass', function ($q) use ($activeLevelIds) {
            $q->whereIn('level_id', $activeLevelIds);
        })->with('studentClass')->get()->toArray();

        // Hitung total hari ujian
        $start = Carbon::parse($this->session->start_date);
        $end = Carbon::parse($this->session->end_date);
        $this->totalDays = $start->diffInDays($end) + 1;
    }

    public function runEvolution()
    {
        if (empty($this->rooms) || empty($this->timeSessions) || empty($this->subjects) || empty($this->students)) {
            return [
                'chromosome' => [],
                'fitness' => 0,
                'generations' => 0
            ];
        }

        $population = $this->initializePopulation();
        $generation = 0;
        $bestFitness = 0;
        $bestChromosome = [];

        while ($generation < $this->maxGenerations && $bestFitness < 1.0) {
            $population = $this->evaluateFitness($population);
            
            // Urutkan dari fitness tertinggi (Desc)
            usort($population, fn($a, $b) => $b['fitness'] <=> $a['fitness']);
            
            $bestChromosome = $population[0];
            $bestFitness = $bestChromosome['fitness'];

            // Log status perkembangan evolusi
            Log::info("GA Generation {$generation} - Best Fitness: {$bestFitness}");

            if ($bestFitness >= 1.0) break; // Solusi sempurna ditemukan!

            $newPopulation = [];
            // Elitism: Pertahankan 2 kromosom terbaik tanpa perubahan
            $newPopulation[] = $population[0];
            $newPopulation[] = $population[1];

            // Siklus Crossover & Mutasi untuk sisa populasi
            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->tournamentSelection($population);
                $parent2 = $this->tournamentSelection($population);

                $child = $this->uniformCrossover($parent1, $parent2);
                $child = $this->targetedMutate($child);
                $child = $this->repair($child);

                $newPopulation[] = $child;
            }
            $population = $newPopulation;
            $generation++;
        }

        return [
            'chromosome' => $bestChromosome['genes'],
            'fitness' => $bestFitness,
            'generations' => $generation
        ];
    }

    /**
     * 1. INISIALISASI POPULASI DENGAN HEURISTIK ROUND-ROBIN
     */
    private function initializePopulation()
    {
        $population = [];
        
        // Dapatkan slot waktu yang tersedia: array of [day_index, time_session_id]
        $timeSlots = [];
        for ($day = 1; $day <= $this->totalDays; $day++) {
            foreach ($this->timeSessions as $ts) {
                $timeSlots[] = [
                    'day_index' => $day,
                    'time_session_id' => $ts['id']
                ];
            }
        }

        // Distribusikan mata pelajaran per level secara heuristik round-robin ke slot-slot waktu
        $levelSubjectSlots = []; // [level_id][subject_id] => [day_index, time_session_id]
        foreach ($this->levels as $level) {
            $levelId = $level['id'];
            $levelSubjects = collect($this->subjects)->where('level_id', $levelId)->values()->all();
            
            // Shuffle slot waktu agar inisiasi populasi bervariasi per level
            $shuffledSlots = $timeSlots;
            shuffle($shuffledSlots);

            foreach ($levelSubjects as $index => $subject) {
                // Gunakan slot sirkuler jika jumlah mapel > total slots (meski pre-flight melarangnya)
                $slot = $shuffledSlots[$index % count($shuffledSlots)];
                $levelSubjectSlots[$levelId][$subject['id']] = $slot;
            }
        }

        // Generate N kromosom
        for ($i = 0; $i < $this->populationSize; $i++) {
            $genes = [];

            // Urutan loop ini konsisten di seluruh inisialisasi populasi
            foreach ($this->levels as $level) {
                $levelId = $level['id'];
                $levelStudents = collect($this->students)->where('student_class.level_id', $levelId)->all();
                $levelSubjects = collect($this->subjects)->where('level_id', $levelId)->all();

                foreach ($levelStudents as $student) {
                    foreach ($levelSubjects as $subject) {
                        // Ambil slot heuristik yang sudah ditentukan agar tidak bentrok
                        $slot = $levelSubjectSlots[$levelId][$subject['id']] ?? $timeSlots[0];
                        
                        // Pilih ruangan acak dan nomor meja acak
                        $randomRoom = $this->rooms[array_rand($this->rooms)];
                        $desk = rand(1, $randomRoom['capacity']);

                        $genes[] = [
                            'student_id' => $student['id'],
                            'level_id' => $levelId,
                            'subject_id' => $subject['id'],
                            'day_index' => $slot['day_index'],
                            'time_session_id' => $slot['time_session_id'],
                            'room_id' => $randomRoom['id'],
                            'desk_number' => $desk
                        ];
                    }
                }
            }

            $population[] = [
                'genes' => $genes,
                'fitness' => 0
            ];
        }

        return $population;
    }

    /**
     * 2. EVALUASI FITNESS DENGAN 4 HARD CONSTRAINTS & 2 SOFT CONSTRAINTS
     */
    public function evaluateFitness($population)
    {
        // Cache data ruangan untuk pencarian kapasitas super cepat
        $roomCapacities = collect($this->rooms)->pluck('capacity', 'id')->toArray();

        foreach ($population as &$indv) {
            $penalty = 0;

            // TRACKERS
            $roomSessionTracker = [];  // [day][session][room] => {studentsCount, levelIds[], deskNumbers[]}
            $studentTimeTracker = [];  // [student][day][session] => subjectCount
            $subjectTimeTracker = [];  // [subject] => array of 'day_session' keys

            foreach ($indv['genes'] as $gene) {
                $studentId = $gene['student_id'];
                $levelId = $gene['level_id'];
                $subjectId = $gene['subject_id'];
                $day = $gene['day_index'];
                $session = $gene['time_session_id'];
                $room = $gene['room_id'];
                $desk = $gene['desk_number'];

                // 1. TRACK ROOM SESSION
                if (!isset($roomSessionTracker[$day][$session][$room])) {
                    $roomSessionTracker[$day][$session][$room] = [
                        'count' => 0,
                        'levels' => [],
                        'desks' => []
                    ];
                }
                $roomSessionTracker[$day][$session][$room]['count']++;
                $roomSessionTracker[$day][$session][$room]['levels'][$levelId] = true;
                
                // Cek Hard Constraint #4: Desk Collision (+100)
                if (isset($roomSessionTracker[$day][$session][$room]['desks'][$desk])) {
                    $penalty += 100;
                } else {
                    $roomSessionTracker[$day][$session][$room]['desks'][$desk] = true;
                }

                // 2. TRACK STUDENT TIME
                if (!isset($studentTimeTracker[$studentId][$day][$session])) {
                    $studentTimeTracker[$studentId][$day][$session] = 0;
                }
                $studentTimeTracker[$studentId][$day][$session]++;
                
                // Cek Hard Constraint #3: Student Collision (+100)
                if ($studentTimeTracker[$studentId][$day][$session] > 1) {
                    $penalty += 100;
                }

                // 3. TRACK SUBJECT TIME
                $timeKey = $day . '_' . $session;
                $subjectTimeTracker[$subjectId][$timeKey] = true;
            }

            // 4. EVALUASI HARD CONSTRAINT #1 (Overcapacity) & #2 (Level Mixing) & SOFT CONSTRAINT #2 (Underutilization)
            foreach ($roomSessionTracker as $day => $sessions) {
                foreach ($sessions as $session => $roomsData) {
                    foreach ($roomsData as $roomId => $data) {
                        $count = $data['count'];
                        $capacity = $roomCapacities[$roomId] ?? 30;
                        
                        // Hard Constraint #1: Overcapacity
                        if ($count > $capacity) {
                            $penalty += 100 * ($count - $capacity);
                        }

                        // Hard Constraint #2: Level Mixing
                        if (count($data['levels']) > 1) {
                            $penalty += 100;
                        }

                        // Soft Constraint #2: Room Underutilization (+5)
                        // Ruangan terisi tapi di bawah 30% kapasitas
                        if ($count > 0 && $count < ($capacity * 0.3)) {
                            $penalty += 5;
                        }
                    }
                }
            }

            // 5. EVALUASI SOFT CONSTRAINT #1 (Subject Scattering)
            foreach ($subjectTimeTracker as $subjectId => $slots) {
                $sessionCount = count($slots);
                if ($sessionCount > 2) {
                    $penalty += 10 * ($sessionCount - 2);
                }
            }

            // Skor Fitness (1 / (1 + penalti))
            $indv['fitness'] = 1 / (1 + $penalty);
        }

        return $population;
    }

    /**
     * 3. TOURNAMENT SELECTION
     */
    private function tournamentSelection($population)
    {
        $tournamentSize = 5;
        $best = null;
        for ($i = 0; $i < $tournamentSize; $i++) {
            $randomIndv = $population[array_rand($population)];
            if ($best === null || $randomIndv['fitness'] > $best['fitness']) {
                $best = $randomIndv;
            }
        }
        return $best;
    }

    /**
     * 4. UNIFORM CROSSOVER (50/50 PER GENE)
     */
    private function uniformCrossover($parent1, $parent2)
    {
        $genesLength = count($parent1['genes']);
        $childGenes = [];

        for ($i = 0; $i < $genesLength; $i++) {
            if (rand(0, 1) >= 0.5) {
                $childGenes[] = $parent1['genes'][$i];
            } else {
                $childGenes[] = $parent2['genes'][$i];
            }
        }

        return [
            'genes' => $childGenes,
            'fitness' => 0
        ];
    }

    /**
     * 5. TARGETED MUTATION (MUTASI CERDAS BERDASARKAN PELANGGARAN)
     */
    private function targetedMutate($chromosome)
    {
        // Tracker cepat untuk deteksi pelanggaran gen
        $studentTimeCount = [];
        $roomSessionCount = [];

        foreach ($chromosome['genes'] as $gene) {
            $studentId = $gene['student_id'];
            $day = $gene['day_index'];
            $session = $gene['time_session_id'];
            $room = $gene['room_id'];

            $studentKey = $studentId . '_' . $day . '_' . $session;
            $studentTimeCount[$studentKey] = ($studentTimeCount[$studentKey] ?? 0) + 1;

            $roomKey = $day . '_' . $session . '_' . $room;
            $roomSessionCount[$roomKey] = ($roomSessionCount[$roomKey] ?? 0) + 1;
        }

        // Ambil data kapasitas ruangan untuk rujukan mutasi
        $roomCapacities = collect($this->rooms)->pluck('capacity', 'id')->toArray();

        foreach ($chromosome['genes'] as &$gene) {
            if (rand(1, 100) <= ($this->mutationRate * 100)) {
                $studentId = $gene['student_id'];
                $day = $gene['day_index'];
                $session = $gene['time_session_id'];
                $room = $gene['room_id'];

                $studentKey = $studentId . '_' . $day . '_' . $session;
                $roomKey = $day . '_' . $session . '_' . $room;

                $hasStudentCollision = ($studentTimeCount[$studentKey] ?? 0) > 1;
                $hasRoomOverflow = ($roomSessionCount[$roomKey] ?? 0) > ($roomCapacities[$room] ?? 30);

                if ($hasStudentCollision) {
                    // TARGETED MUTATION: Ubah waktu (day atau session) agar bentrok hilang
                    $gene['day_index'] = rand(1, $this->totalDays);
                    $gene['time_session_id'] = $this->timeSessions[array_rand($this->timeSessions)]['id'];
                } elseif ($hasRoomOverflow) {
                    // TARGETED MUTATION: Ubah ruangan & meja agar tidak overcapacity
                    $randomRoom = $this->rooms[array_rand($this->rooms)];
                    $gene['room_id'] = $randomRoom['id'];
                    $gene['desk_number'] = rand(1, $randomRoom['capacity']);
                } else {
                    // Mutasi acak salah satu dimensi
                    $choice = rand(1, 4);
                    if ($choice === 1) {
                        $gene['day_index'] = rand(1, $this->totalDays);
                    } elseif ($choice === 2) {
                        $gene['time_session_id'] = $this->timeSessions[array_rand($this->timeSessions)]['id'];
                    } else {
                        $randomRoom = $this->rooms[array_rand($this->rooms)];
                        $gene['room_id'] = $randomRoom['id'];
                        $gene['desk_number'] = rand(1, $randomRoom['capacity']);
                    }
                }
            }
        }

        return $chromosome;
    }

    /**
     * 6. REPAIR FUNCTION (PERBAIKI MEJA BENTROK SECARA INSTAN)
     */
    private function repair($chromosome)
    {
        $usedSlots = [];
        $conflicts = [];

        // Lacak duplikasi meja di setiap slot
        foreach ($chromosome['genes'] as $index => $gene) {
            $key = $gene['day_index'] . '_' . $gene['time_session_id'] . '_' . $gene['room_id'] . '_' . $gene['desk_number'];
            if (isset($usedSlots[$key])) {
                $conflicts[] = $index;
            } else {
                $usedSlots[$key] = true;
            }
        }

        if (empty($conflicts)) {
            return $chromosome;
        }

        // Cari meja kosong di ruangan & slot yang bersangkutan
        foreach ($conflicts as $index) {
            $gene = $chromosome['genes'][$index];
            $day = $gene['day_index'];
            $session = $gene['time_session_id'];
            $room = $gene['room_id'];

            // Lacak kapasitas ruangan aktif secara aman
            $roomData = collect($this->rooms)->where('id', $room)->first();
            $roomCapacity = $roomData['capacity'] ?? 30;

            $repaired = false;
            for ($d = 1; $d <= $roomCapacity; $d++) {
                $testKey = $day . '_' . $session . '_' . $room . '_' . $d;
                if (!isset($usedSlots[$testKey])) {
                    $chromosome['genes'][$index]['desk_number'] = $d;
                    $usedSlots[$testKey] = true;
                    $repaired = true;
                    break;
                }
            }

            // Jika ruangan itu benar-benar penuh meja kosongnya, carikan ruangan aktif lain di slot itu
            if (!$repaired) {
                $shuffledRooms = $this->rooms;
                shuffle($shuffledRooms);
                foreach ($shuffledRooms as $altRoom) {
                    for ($d = 1; $d <= $altRoom['capacity']; $d++) {
                        $testKey = $day . '_' . $session . '_' . $altRoom['id'] . '_' . $d;
                        if (!isset($usedSlots[$testKey])) {
                            $chromosome['genes'][$index]['room_id'] = $altRoom['id'];
                            $chromosome['genes'][$index]['desk_number'] = $d;
                            $usedSlots[$testKey] = true;
                            $repaired = true;
                            break 2;
                        }
                    }
                }
            }
        }

        return $chromosome;
    }

    /**
     * 7. TRANSLASI KROMOSOM TERBAIK KE DATABASE JADWAL & ALOKASI
     */
    public function translateToDatabase($bestGenes)
    {
        DB::beginTransaction();

        try {
            // Hapus jadwal & alokasi lama sesi ini (Fresh Overwrite)
            $oldScheduleIds = ExamSchedule::where('exam_session_id', $this->session->id)->pluck('id')->toArray();
            ExamAllocation::whereIn('exam_schedule_id', $oldScheduleIds)->delete();
            ExamSchedule::where('exam_session_id', $this->session->id)->delete();

            // 1. Ekstrak jadwal unik: kombinasi [subject_id, day_index, time_session_id]
            $uniqueScheduleKeys = [];
            foreach ($bestGenes as $gene) {
                $key = $gene['subject_id'] . '_' . $gene['day_index'] . '_' . $gene['time_session_id'];
                $uniqueScheduleKeys[$key] = [
                    'subject_id' => $gene['subject_id'],
                    'day_index' => $gene['day_index'],
                    'time_session_id' => $gene['time_session_id']
                ];
            }

            // Simpan Jadwal Baru
            $scheduleModels = [];
            $startDate = Carbon::parse($this->session->start_date);

            foreach ($uniqueScheduleKeys as $key => $data) {
                // Hitung tanggal nyata: start_date + (day_index - 1)
                $examDate = (clone $startDate)->addDays($data['day_index'] - 1);

                $sched = ExamSchedule::create([
                    'exam_session_id' => $this->session->id,
                    'subject_id' => $data['subject_id'],
                    'time_session_id' => $data['time_session_id'],
                    'exam_date' => $examDate->format('Y-m-d'),
                ]);

                $scheduleModels[$key] = $sched->id;
            }

            // 2. Simpan Alokasi Baru
            foreach ($bestGenes as $gene) {
                $key = $gene['subject_id'] . '_' . $gene['day_index'] . '_' . $gene['time_session_id'];
                $scheduleId = $scheduleModels[$key] ?? null;

                if ($scheduleId) {
                    ExamAllocation::create([
                        'exam_schedule_id' => $scheduleId,
                        'room_id' => $gene['room_id'],
                        'student_id' => $gene['student_id'],
                        'desk_number' => $gene['desk_number'],
                    ]);
                }
            }

            DB::commit();
            Log::info("GA Evolution successfully translated to Database for ExamSession #{$this->session->id}");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to translate GA results to Database: " . $e->getMessage());
            throw $e;
        }
    }
}
