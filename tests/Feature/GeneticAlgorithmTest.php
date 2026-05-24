<?php

use App\Models\ExamSession;
use App\Models\Room;
use App\Models\Level;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\TimeSession;
use App\Models\Student;
use App\Services\GeneticAlgorithmService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('genetic algorithm fitness function calculates penalties correctly', function () {
    // 1. SETUP DATA MASTER
    $level = Level::create(['name' => 'Kelas 10 TEST']);
    
    $class = StudentClass::create([
        'level_id' => $level->id,
        'name' => '10 TEST 1'
    ]);

    // Tambahkan 4 siswa
    $students = [];
    for ($i = 1; $i <= 4; $i++) {
        $students[] = Student::create([
            'nis' => '100000000' . $i,
            'name' => 'Siswa Test ' . $i,
            'student_class_id' => $class->id,
        ]);
    }

    // Tambahkan 2 mata pelajaran
    $subject1 = Subject::create(['level_id' => $level->id, 'name' => 'TEST MTK']);
    $subject2 = Subject::create(['level_id' => $level->id, 'name' => 'TEST FIS']);

    // Tambahkan 1 ruangan global dengan kapasitas 2
    $room = Room::create([
        'name' => 'Lab Komputer TEST',
        'capacity' => 2
    ]);

    // Tambahkan 2 sesi waktu
    $timeSession1 = TimeSession::create([
        'name' => 'Sesi 1 Uji Coba',
        'start_time' => '07:30:00',
        'end_time' => '09:30:00'
    ]);

    $timeSession2 = TimeSession::create([
        'name' => 'Sesi 2 Uji Coba',
        'start_time' => '10:00:00',
        'end_time' => '12:00:00'
    ]);

    // 2. SETUP SESI UJIAN & PARAMETER PIVOT
    $session = ExamSession::create([
        'title' => 'Ujian Akhir TEST',
        'start_date' => '2026-06-01',
        'end_date' => '2026-06-01', // 1 hari saja
        'is_active' => true,
        'allocation_status' => 'Pending'
    ]);

    $session->rooms()->sync([$room->id]);
    $session->subjects()->sync([$subject1->id, $subject2->id]);
    $session->timeSessions()->sync([$timeSession1->id, $timeSession2->id]);

    // 3. INISIALISASI SERVICE AG
    $agService = new GeneticAlgorithmService($session->id);

    // 4. SCENARIO A: KROMOSOM SEMPURNA (FITNESS = 1.0)
    // 2 siswa ujian MTK di Sesi 1, 2 siswa ujian FIS di Sesi 2.
    // Tidak ada overcapacity (2 siswa per room-session), tidak ada desk collision, tidak ada student collision, tidak ada level mixing.
    $perfectChromosome = [
        'genes' => [
            // Gen Siswa 1 (MTK) - Sesi 1, Meja 1
            [
                'student_id' => $students[0]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            // Gen Siswa 2 (MTK) - Sesi 1, Meja 2
            [
                'student_id' => $students[1]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 2
            ],
            // Gen Siswa 3 (FIS) - Sesi 2, Meja 1
            [
                'student_id' => $students[2]->id,
                'level_id' => $level->id,
                'subject_id' => $subject2->id,
                'day_index' => 1,
                'time_session_id' => $timeSession2->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            // Gen Siswa 4 (FIS) - Sesi 2, Meja 2
            [
                'student_id' => $students[3]->id,
                'level_id' => $level->id,
                'subject_id' => $subject2->id,
                'day_index' => 1,
                'time_session_id' => $timeSession2->id,
                'room_id' => $room->id,
                'desk_number' => 2
            ],
        ],
        'fitness' => 0
    ];

    $evalPerfect = $agService->evaluateFitness([$perfectChromosome]);
    expect($evalPerfect[0]['fitness'])->toEqual(1.0); // Penalti = 0, Fitness = 1.0

    // 5. SCENARIO B: OVERCAPACITY & DESK COLLISION
    // Kita tempatkan ke-4 siswa dalam ruangan yang sama, meja yang sama (Meja 1), pada subjek yang sama.
    // Overcapacity: 4 siswa di ruang berkapasitas 2 -> overflow = 2 -> penalti = 100 * 2 = 200.
    // Desk Collision: 4 siswa di meja 1 -> duplikat meja = 3 -> penalti = 100 * 3 = 300.
    // Total Penalti Ekspektasi = 200 + 300 = 500.
    // Fitness Ekspektasi = 1 / (1 + 500) = 0.001996
    $badChromosome = [
        'genes' => [
            [
                'student_id' => $students[0]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            [
                'student_id' => $students[1]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            [
                'student_id' => $students[2]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            [
                'student_id' => $students[3]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
        ],
        'fitness' => 0
    ];

    $evalBad = $agService->evaluateFitness([$badChromosome]);
    $expectedFitness = 1 / (1 + 500);
    expect(abs($evalBad[0]['fitness'] - $expectedFitness))->toBeLessThan(0.00001);

    // 6. SCENARIO C: STUDENT TIME COLLISION
    // Siswa 1 di-assign 2 gen mapel berbeda di hari & sesi waktu yang sama.
    // Penalti Student Collision = 100.
    // Lainnya normal: Ruang kapasitas 2 hanya berisi 2 siswa di meja berbeda (no overcapacity, no desk collision).
    // Total Penalti Ekspektasi = 100.
    // Fitness Ekspektasi = 1 / (1 + 100) = 0.0099
    $collisionChromosome = [
        'genes' => [
            // Siswa 1 ujian MTK di Sesi 1, Meja 1
            [
                'student_id' => $students[0]->id,
                'level_id' => $level->id,
                'subject_id' => $subject1->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 1
            ],
            // Siswa 1 JUGA ujian FIS di Sesi 1, Meja 2 (BENTROK WAKTU!)
            [
                'student_id' => $students[0]->id,
                'level_id' => $level->id,
                'subject_id' => $subject2->id,
                'day_index' => 1,
                'time_session_id' => $timeSession1->id,
                'room_id' => $room->id,
                'desk_number' => 2
            ],
        ],
        'fitness' => 0
    ];

    $evalCollision = $agService->evaluateFitness([$collisionChromosome]);
    $expectedCollisionFitness = 1 / (1 + 100);
    expect(abs($evalCollision[0]['fitness'] - $expectedCollisionFitness))->toBeLessThan(0.00001);
});
