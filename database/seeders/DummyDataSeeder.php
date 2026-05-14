<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\ExamSession;
use App\Models\Room;
use App\Models\ExamSchedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. BUAT DATA SISWA (50 ORANG) ---
        $classes = ['XII IPA 1', 'XII IPS 1'];
        
        foreach ($classes as $classIndex => $className) {
            for ($i = 1; $i <= 25; $i++) {
                // Format NIS: 2024 + Kode Kelas + No Urut (Contoh: 2024101)
                $nisCode = ($classIndex + 1) * 100 + $i; 
                
                Student::create([
                    'nis' => '2025' . $nisCode,
                    'name' => "Siswa {$className} Urut {$i}",
                    'class' => $className,
                ]);
            }
        }

        // --- 2. BUAT JADWAL & SESI UJIAN (1 Sesi = 1 Mapel) ---
        $subjects = [
            [
                'title' => 'UAS - Bahasa Indonesia',
                'subject' => 'Bahasa Indonesia',
                'date' => Carbon::now()->addDays(7),
                'start' => '08:00', 'end' => '10:00'
            ],
            [
                'title' => 'UAS - Matematika Wajib',
                'subject' => 'Matematika Wajib',
                'date' => Carbon::now()->addDays(7),
                'start' => '10:30', 'end' => '12:30'
            ],
            [
                'title' => 'UAS - Bahasa Inggris',
                'subject' => 'Bahasa Inggris',
                'date' => Carbon::now()->addDays(8),
                'start' => '08:00', 'end' => '10:00'
            ],
        ];

        foreach ($subjects as $index => $sub) {
            $session = ExamSession::create([
                'title' => $sub['title'],
                'start_date' => $sub['date'],
                'is_active' => true,
            ]);

            // --- 3. BUAT RUANGAN (Terhubung ke Sesi) ---
            $rooms = [
                ['name' => 'Lab Komputer 1', 'capacity' => 20],
                ['name' => 'Lab Komputer 2', 'capacity' => 20],
                ['name' => 'Ruang Teori 01', 'capacity' => 15],
            ];

            foreach ($rooms as $r) {
                Room::create([
                    'exam_session_id' => $session->id,
                    'name' => $r['name'],
                    'capacity' => $r['capacity'],
                ]);
            }

            // --- 4. BUAT JADWAL MATA PELAJARAN ---
            ExamSchedule::create([
                'exam_session_id' => $session->id,
                'subject_name' => $sub['subject'],
                'exam_date' => $sub['date'],
                'start_time' => $sub['start'],
                'end_time' => $sub['end'],
            ]);
        }
    }
}