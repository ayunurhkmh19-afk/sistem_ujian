<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Student;

class GeneticAlgorithmService
{
    protected $students;
    protected $rooms;
    protected $populationSize = 100;
    protected $maxGenerations = 1000;
    protected $mutationRate = 0.10; // 10% kemungkinan mutasi

    public function __construct($examSessionId)
    {
        // Ambil data ruangan yang di-assign ke sesi ini
        $this->rooms = Room::where('exam_session_id', $examSessionId)->get()->toArray();
        
        // Ambil daftar seluruh siswa untuk dialokasikan
        $this->students = Student::all()->toArray();
    }

    public function runEvolution()
    {
        if (empty($this->rooms) || empty($this->students)) {
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

            if ($bestFitness >= 1.0) break; // Sempurna, hentikan evolusi

            $newPopulation = [];
            // Elitism: Pertahankan 2 kromosom terbaik ke generasi berikutnya
            $newPopulation[] = $population[0];
            $newPopulation[] = $population[1];

            // Siklus Crossover & Mutasi untuk sisa populasi
            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->tournamentSelection($population);
                $parent2 = $this->tournamentSelection($population);

                $child = $this->crossover($parent1, $parent2);
                $child = $this->mutate($child);
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

    // 1. INISIALISASI POPULASI
    private function initializePopulation()
    {
        $population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $chromosome = [];
            foreach ($this->students as $student) {
                // Alokasi acak ruang dan meja untuk awal
                $randomRoom = $this->rooms[array_rand($this->rooms)];
                $chromosome[] = [
                    'student_id' => $student['id'],
                    'room_id' => $randomRoom['id'],
                    'desk_number' => rand(1, $randomRoom['capacity'])
                ];
            }
            $population[] = ['genes' => $chromosome, 'fitness' => 0];
        }
        return $population;
    }

    // 2. EVALUASI FITNESS BERDASARKAN PENALTI
    private function evaluateFitness($population)
    {
        foreach ($population as &$indv) {
            $penalty = 0;
            $roomCounts = [];
            $deskTracker = [];

            foreach ($indv['genes'] as $gene) {
                $rId = $gene['room_id'];
                $desk = $gene['desk_number'];

                // Hitung jumlah siswa di satu ruangan
                if (!isset($roomCounts[$rId])) $roomCounts[$rId] = 0;
                $roomCounts[$rId]++;

                // Lacak duplikasi meja di ruangan yang sama
                $key = $rId . '_' . $desk;
                if (isset($deskTracker[$key])) {
                    $penalty += 100; // HARD CONSTRAINT: Meja sama
                } else {
                    $deskTracker[$key] = true;
                }
            }

            // Cek kelebihan kapasitas
            foreach ($this->rooms as $room) {
                $rId = $room['id'];
                if (isset($roomCounts[$rId]) && $roomCounts[$rId] > $room['capacity']) {
                    $penalty += 100 * ($roomCounts[$rId] - $room['capacity']); // HARD CONSTRAINT
                }
            }

            // Hitung skor fitness (Makin kecil penalti, fitness mendekati 1)
            $indv['fitness'] = 1 / (1 + $penalty);
        }
        return $population;
    }

    // 3. TOURNAMENT SELECTION
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

    // 4. SINGLE POINT CROSSOVER
    private function crossover($parent1, $parent2)
    {
        $genesLength = count($parent1['genes']);
        $crossoverPoint = rand(1, $genesLength - 1);

        $childGenes = array_merge(
            array_slice($parent1['genes'], 0, $crossoverPoint),
            array_slice($parent2['genes'], $crossoverPoint)
        );

        return ['genes' => $childGenes, 'fitness' => 0];
    }

    // 5. MUTASI
    private function mutate($chromosome)
    {
        foreach ($chromosome['genes'] as &$gene) {
            if (rand(1, 100) <= ($this->mutationRate * 100)) {
                // Ubah acak ruangan atau meja
                $randomRoom = $this->rooms[array_rand($this->rooms)];
                $gene['room_id'] = $randomRoom['id'];
                $gene['desk_number'] = rand(1, $randomRoom['capacity']);
            }
        }
        return $chromosome;
    }

    // 6. REPAIR
    private function repair($chromosome)
    {
        $usedSlots = [];
        $conflicts = [];

        // 1. Identifikasi slot yang digunakan dan konflik
        foreach ($chromosome['genes'] as $index => $gene) {
            $key = $gene['room_id'] . '_' . $gene['desk_number'];
            if (isset($usedSlots[$key])) {
                $conflicts[] = $index; // Index dari gen yang konflik
            } else {
                $usedSlots[$key] = true;
            }
        }

        // Jika tidak ada konflik, selesai
        if (empty($conflicts)) {
            return $chromosome;
        }

        // 2. Kumpulkan semua slot yang tersedia (kapasitas ruangan)
        $availableSlots = [];
        foreach ($this->rooms as $room) {
            for ($i = 1; $i <= $room['capacity']; $i++) {
                $key = $room['id'] . '_' . $i;
                if (!isset($usedSlots[$key])) {
                    $availableSlots[] = ['room_id' => $room['id'], 'desk_number' => $i];
                }
            }
        }

        // Acak slot yang tersedia
        shuffle($availableSlots);

        // 3. Perbaiki gen yang konflik dengan slot yang tersedia
        foreach ($conflicts as $index) {
            if (!empty($availableSlots)) {
                $slot = array_pop($availableSlots);
                $chromosome['genes'][$index]['room_id'] = $slot['room_id'];
                $chromosome['genes'][$index]['desk_number'] = $slot['desk_number'];
            }
        }

        return $chromosome;
    }
}
