<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Student;
use App\Models\ExamSession;
use App\Models\ExamSchedule;
use App\Models\RoomSupervisor;
use App\Models\ExamAllocation;
use App\Models\Subject;
use App\Models\TimeSession;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PengawasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Exam Session V2
        $session = ExamSession::create([
            'title' => 'Ujian Akhir Semester Genap 2026',
            'start_date' => Carbon::now()->addDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'is_active' => true,
            'allocation_status' => 'Completed',
        ]);

        // 2. Attach all subjects, rooms, time sessions to this exam session
        $subjects = Subject::all();
        $rooms = Room::all();
        $timeSessions = TimeSession::all();

        $session->subjects()->attach($subjects->pluck('id'));
        $session->rooms()->attach($rooms->pluck('id'));
        $session->timeSessions()->attach($timeSessions->pluck('id'));

        // 3. Buat beberapa Exam Schedules (misal Mapel Bahasa Indonesia dan Matematika untuk Kelas 10, 11, 12)
        $p1 = User::where('role', 'pengawas')->first();
        $p2 = User::where('role', 'pengawas')->skip(1)->first();

        // Ambil beberapa mapel dari database
        $subject1 = Subject::where('name', 'like', '%Bahasa Indonesia%')->first();
        $subject2 = Subject::where('name', 'like', '%Matematika%')->first();
        $time1 = TimeSession::where('name', 'Sesi 1')->first();
        $time2 = TimeSession::where('name', 'Sesi 2')->first();

        if ($subject1 && $time1) {
            $sched1 = ExamSchedule::create([
                'exam_session_id' => $session->id,
                'subject_id' => $subject1->id,
                'time_session_id' => $time1->id,
                'exam_date' => Carbon::now()->addDays(2),
            ]);

            // Assign Room Supervisors (multi-select / multi-pengawas)
            $r1 = Room::where('name', 'Lab Komputer 1')->first();
            $r2 = Room::where('name', 'Lab Komputer 2')->first();

            if ($r1 && $p1) {
                RoomSupervisor::create([
                    'user_id' => $p1->id,
                    'exam_schedule_id' => $sched1->id,
                    'room_id' => $r1->id,
                ]);

                // Alokasikan beberapa siswa kelas 10 ke ruangan 1
                $students10 = Student::whereHas('studentClass.level', function($q) {
                    $q->where('name', 'Kelas 10');
                })->limit(10)->get();

                foreach ($students10 as $index => $s) {
                    ExamAllocation::create([
                        'exam_schedule_id' => $sched1->id,
                        'room_id' => $r1->id,
                        'student_id' => $s->id,
                        'desk_number' => $index + 1,
                    ]);
                }
            }

            if ($r2 && $p2) {
                RoomSupervisor::create([
                    'user_id' => $p2->id,
                    'exam_schedule_id' => $sched1->id,
                    'room_id' => $r2->id,
                ]);

                // Alokasikan beberapa siswa kelas 10 ke ruangan 2
                $students10_part2 = Student::whereHas('studentClass.level', function($q) {
                    $q->where('name', 'Kelas 10');
                })->skip(10)->limit(10)->get();

                foreach ($students10_part2 as $index => $s) {
                    ExamAllocation::create([
                        'exam_schedule_id' => $sched1->id,
                        'room_id' => $r2->id,
                        'student_id' => $s->id,
                        'desk_number' => $index + 1,
                    ]);
                }
            }
        }
    }
}
