<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Student;
use App\Models\ExamSession;
use App\Models\RoomSupervisor;
use App\Models\ExamAllocation;
use Illuminate\Database\Seeder;

class PengawasSeeder extends Seeder
{
    public function run(): void
    {
        $session = ExamSession::first();
        if (!$session) return;

        $rooms = Room::where('exam_session_id', $session->id)->get();
        $pengawas = User::where('role', 'pengawas')->get();
        $students = Student::all();

        if ($rooms->isEmpty() || $pengawas->isEmpty()) return;

        // 1. Assign Pengawas to Rooms
        foreach ($rooms as $index => $room) {
            $p = $pengawas[$index % $pengawas->count()];
            RoomSupervisor::updateOrCreate(
                [
                    'user_id' => $p->id,
                    'exam_session_id' => $session->id,
                    'room_id' => $room->id,
                ]
            );

            // 2. Assign some students to this room for testing detail page
            // We'll take 10 students per room
            $roomStudents = $students->slice($index * 10, 10);
            foreach ($roomStudents as $sIndex => $s) {
                ExamAllocation::updateOrCreate(
                    [
                        'exam_session_id' => $session->id,
                        'student_id' => $s->id,
                    ],
                    [
                        'room_id' => $room->id,
                        'desk_number' => $sIndex + 1
                    ]
                );
            }
        }
    }
}
