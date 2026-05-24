<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\ExamAllocation;
use App\Models\StudentAttendance;
use App\Models\ExamReport;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function show($schedule_id, $room_id)
    {
        $schedule = ExamSchedule::with(['subject.level', 'timeSession', 'session'])->findOrFail($schedule_id);
        $room = Room::findOrFail($room_id);

        // Verifikasi waktu: Akses dibuka jika Sedang Berlangsung atau Sudah Berlangsung
        $startDateTime = Carbon::parse($schedule->exam_date->format('Y-m-d') . ' ' . $schedule->timeSession->start_time);
        
        // Tolak jika ujian BELUM MULAI (Akan Berlangsung)
        if (Carbon::now()->lt($startDateTime)) {
            return back()->with('error', 'Modul Absensi belum bisa diakses. Ujian belum dimulai.');
        }

        // Ambil daftar siswa di ruangan tersebut untuk jadwal ujian ini
        $alokasiSiswa = ExamAllocation::with('student')
            ->where('exam_schedule_id', $schedule_id)
            ->where('room_id', $room_id)
            ->get();

        // Cek status lock (Berita Acara)
        $laporan = ExamReport::where('exam_schedule_id', $schedule_id)
            ->where('room_id', $room_id)
            ->first();
        $isLocked = ($laporan && $laporan->status === 'Submitted');

        // Ambil data kehadiran existing
        $kehadiran = StudentAttendance::where('exam_schedule_id', $schedule_id)
            ->where('room_id', $room_id)
            ->get()->keyBy('student_id');

        return view('pengawas.sesi_detail', compact('schedule', 'room', 'alokasiSiswa', 'kehadiran', 'isLocked', 'room_id', 'laporan'));
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'room_id' => 'required|exists:rooms,id',
            'attendances' => 'required|array',
        ]);

        $scheduleId = $request->exam_schedule_id;
        $roomId = $request->room_id;
        $userId = Auth::id();

        foreach ($request->attendances as $studentId => $status) {
            StudentAttendance::updateOrCreate(
                [
                    'exam_schedule_id' => $scheduleId,
                    'room_id' => $roomId,
                    'student_id' => $studentId
                ],
                [
                    'status' => $status,
                    'recorded_by' => $userId
                ]
            );
        }

        return back()->with('success', 'Data absensi berhasil disimpan!');
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'room_id' => 'required|exists:rooms,id',
            'incident_notes' => 'nullable|string',
        ]);

        $scheduleId = $request->exam_schedule_id;
        $roomId = $request->room_id;

        // Kalkulasi otomatis total hadir/tidak hadir
        $totalHadir = StudentAttendance::where('exam_schedule_id', $scheduleId)
            ->where('room_id', $roomId)
            ->where('status', 'Hadir')
            ->count();

        $totalTidakHadir = StudentAttendance::where('exam_schedule_id', $scheduleId)
            ->where('room_id', $roomId)
            ->where('status', '!=', 'Hadir')
            ->count();

        ExamReport::updateOrCreate(
            [
                'exam_schedule_id' => $scheduleId,
                'room_id' => $roomId,
            ],
            [
                'user_id' => Auth::id(),
                'total_present' => $totalHadir,
                'total_absent' => $totalTidakHadir,
                'incident_notes' => $request->incident_notes,
                'status' => 'Submitted'
            ]
        );

        return back()->with('success', 'Berita Acara berhasil disubmit! Seluruh data absensi telah dikunci.');
    }
}
