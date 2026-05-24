<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\TimeSession;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Level (Tingkatan Kelas)
        $l10 = Level::create(['name' => 'Kelas 10']);
        $l11 = Level::create(['name' => 'Kelas 11']);
        $l12 = Level::create(['name' => 'Kelas 12']);

        // 2. Buat Student Classes (Kelas)
        $classes = [
            $l10->id => ['10 IPA 1', '10 IPA 2', '10 IPS 1'],
            $l11->id => ['11 IPA 1', '11 IPA 2', '11 IPS 1'],
            $l12->id => ['12 IPA 1', '12 IPA 2', '12 IPS 1'],
        ];

        $classModels = [];
        foreach ($classes as $levelId => $names) {
            foreach ($names as $name) {
                $classModels[] = StudentClass::create([
                    'level_id' => $levelId,
                    'name' => $name,
                ]);
            }
        }

        // 3. Buat Subjects (Mata Pelajaran)
        $subjects = [
            $l10->id => ['Matematika 10', 'Fisika 10', 'Kimia 10', 'Biologi 10', 'Bahasa Indonesia 10'],
            $l11->id => ['Matematika 11', 'Fisika 11', 'Kimia 11', 'Biologi 11', 'Bahasa Indonesia 11'],
            $l12->id => ['Matematika 12', 'Fisika 12', 'Kimia 12', 'Biologi 12', 'Bahasa Indonesia 12'],
        ];

        foreach ($subjects as $levelId => $names) {
            foreach ($names as $name) {
                Subject::create([
                    'level_id' => $levelId,
                    'name' => $name,
                ]);
            }
        }

        // 4. Buat Sesi Waktu (Time Sessions)
        $timeSessions = [
            ['name' => 'Sesi 1', 'start_time' => '07:30:00', 'end_time' => '09:30:00'],
            ['name' => 'Sesi 2', 'start_time' => '10:00:00', 'end_time' => '12:00:00'],
            ['name' => 'Sesi 3', 'start_time' => '13:00:00', 'end_time' => '15:00:00'],
        ];

        foreach ($timeSessions as $ts) {
            TimeSession::create($ts);
        }

        // 5. Buat Ruangan Global (Rooms)
        $rooms = [
            ['name' => 'Lab Komputer 1', 'capacity' => 20],
            ['name' => 'Lab Komputer 2', 'capacity' => 20],
            ['name' => 'Ruang Teori 01', 'capacity' => 15],
            ['name' => 'Ruang Teori 02', 'capacity' => 15],
            ['name' => 'Aula Utama', 'capacity' => 60],
        ];

        foreach ($rooms as $r) {
            Room::create($r);
        }

        // 6. Buat Siswa Dummy (untuk testing awal jika tidak mau import, tapi sistem tetap dukung wizard import)
        // Kita buat masing-masing kelas memiliki 10-15 siswa agar tidak terlalu banyak tapi cukup untuk test run
        foreach ($classModels as $classIndex => $classModel) {
            for ($i = 1; $i <= 12; $i++) {
                $nisCode = ($classIndex + 1) * 100 + $i;
                Student::create([
                    'nis' => '2026' . str_pad($nisCode, 3, '0', STR_PAD_LEFT),
                    'name' => "Siswa {$classModel->name} Urut {$i}",
                    'student_class_id' => $classModel->id,
                ]);
            }
        }
    }
}