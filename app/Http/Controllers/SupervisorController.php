<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\ExamSchedule;
use App\Models\Room;
use App\Models\RoomSupervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    /**
     * Tampilkan matriks jadwal kepengawasan untuk sesi ujian tertentu
     */
    public function index($sessionId)
    {
        $session = ExamSession::findOrFail($sessionId);

        // Ambil semua jadwal untuk sesi ini, urutkan berdasarkan tanggal dan sesi waktu
        $schedules = ExamSchedule::where('exam_session_id', $sessionId)
            ->with(['subject', 'timeSession', 'allocations.room', 'roomSupervisors.user'])
            ->get();

        // Ambil semua pengguna dengan role pengawas untuk dropdown
        $supervisors = User::where('role', 'pengawas')->orderBy('name')->get();

        return view('admin.supervisors.index', compact('session', 'schedules', 'supervisors'));
    }

    /**
     * Assign pengawas ke ruangan pada jadwal tertentu (Multi-select)
     */
    public function assign(Request $request)
    {
        $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'room_id' => 'required|exists:rooms,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $scheduleId = $request->exam_schedule_id;
        $roomId = $request->room_id;
        $userIds = $request->user_ids ?? [];

        DB::transaction(function () use ($scheduleId, $roomId, $userIds) {
            // Hapus pengawas lama untuk ruangan & jadwal ini
            RoomSupervisor::where('exam_schedule_id', $scheduleId)
                ->where('room_id', $roomId)
                ->delete();

            // Insert pengawas baru
            foreach ($userIds as $userId) {
                RoomSupervisor::create([
                    'exam_schedule_id' => $scheduleId,
                    'room_id' => $roomId,
                    'user_id' => $userId,
                ]);
            }
        });

        return back()->with('success', 'Penugasan pengawas berhasil diperbarui.');
    }

    /**
     * Tampilkan Berita Acara read-only untuk panitia
     */
    public function showReport($scheduleId, $roomId)
    {
        $schedule = ExamSchedule::with(['subject', 'timeSession', 'session'])->findOrFail($scheduleId);
        $room = Room::findOrFail($roomId);
        
        $report = \App\Models\ExamReport::where('exam_schedule_id', $scheduleId)
            ->where('room_id', $roomId)
            ->with('user')
            ->first();

        $attendances = \App\Models\StudentAttendance::where('exam_schedule_id', $scheduleId)
            ->where('room_id', $roomId)
            ->with('student')
            ->get();

        return view('admin.supervisors.report_detail', compact('schedule', 'room', 'report', 'attendances'));
    }
}
