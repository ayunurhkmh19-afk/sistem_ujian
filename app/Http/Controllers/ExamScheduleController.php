<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
    public function index(Request $request, ExamSession $session)
    {
        // 1. Jika Request AJAX (Untuk FullCalendar)
        if ($request->ajax()) {
            $schedules = $session->schedules()
                ->with(['subject', 'timeSession'])
                ->whereDate('exam_date', '>=', $request->start)
                ->whereDate('exam_date', '<=', $request->end)
                ->get()
                ->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->subject?->name ?? '-',
                        'start' => $schedule->exam_date->format('Y-m-d') . 'T' . ($schedule->timeSession?->start_time ?? '00:00:00'),
                        'end' => $schedule->exam_date->format('Y-m-d') . 'T' . ($schedule->timeSession?->end_time ?? '00:00:00'),
                        'extendedProps' => [
                            'exam_date' => $schedule->exam_date->format('Y-m-d'),
                            'start_time' => $schedule->timeSession ? substr($schedule->timeSession->start_time, 0, 5) : '00:00',
                            'end_time' => $schedule->timeSession ? substr($schedule->timeSession->end_time, 0, 5) : '00:00',
                            'subject_name' => $schedule->subject?->name ?? '-',
                            'time_session_name' => $schedule->timeSession?->name ?? '-',
                        ]
                    ];
                });
            return response()->json($schedules);
        }

        $schedulesList = $session->schedules()
            ->with([
                'subject', 
                'timeSession', 
                'allocations.room', 
                'allocations.student.studentClass',
                'roomSupervisors.user'
            ])
            ->get()
            ->sortBy(function($schedule) {
                return $schedule->exam_date->format('Y-m-d') . '_' . ($schedule->timeSession->start_time ?? '00:00:00');
            })
            ->values();

        // Kirim variabel $schedulesList ke view
        return view('admin.schedules.index', compact('session', 'schedulesList'));
    }
    public function selection()
    {
        // Ambil semua sesi, urutkan dari yang terbaru
        $sessions = ExamSession::withCount('schedules')->latest()->get();
        
        return view('admin.schedules.selection', compact('sessions'));
    }

    public function store(Request $request, ExamSession $session)
    {
        return response()->json([
            'status' => 'error', 
            'message' => 'Penambahan jadwal secara manual dinonaktifkan di Skema V2. Semua jadwal harus dibuat melalui Wizard Algoritma Genetika.'
        ], 403);
    }

    public function update(Request $request, ExamSession $session, ExamSchedule $schedule)
    {
        return response()->json([
            'status' => 'error', 
            'message' => 'Pembaruan jadwal secara manual dinonaktifkan di Skema V2. Semua jadwal harus dikelola melalui Wizard Algoritma Genetika.'
        ], 403);
    }

    public function destroy(ExamSession $session, ExamSchedule $schedule)
    {
        return response()->json([
            'status' => 'error', 
            'message' => 'Penghapusan jadwal secara manual dinonaktifkan di Skema V2. Semua jadwal harus dikelola melalui Wizard Algoritma Genetika.'
        ], 403);
    }
}
