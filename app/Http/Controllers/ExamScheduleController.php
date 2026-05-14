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
            // ... (Kode AJAX lama tetap sama) ...
            $schedules = $session->schedules()
                ->whereDate('exam_date', '>=', $request->start)
                ->whereDate('exam_date', '<=', $request->end)
                ->get()
                ->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->subject_name,
                        'start' => $schedule->exam_date->format('Y-m-d') . 'T' . $schedule->start_time->format('H:i:s'),
                        'end' => $schedule->exam_date->format('Y-m-d') . 'T' . $schedule->end_time->format('H:i:s'),
                        // ... props lainnya ...
                        'extendedProps' => [
                            'exam_date' => $schedule->exam_date->format('Y-m-d'),
                            'start_time' => $schedule->start_time->format('H:i'),
                            'end_time' => $schedule->end_time->format('H:i'),
                        ]
                    ];
                });
            return response()->json($schedules);
        }

        $schedulesList = $session->schedules()->orderBy('exam_date')->orderBy('start_time')->get();

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
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        ExamSchedule::create([
            'exam_session_id' => $session->id,
            'subject_name' => $request->subject_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Jadwal berhasil disimpan']);
    }

    public function update(Request $request, ExamSession $session, ExamSchedule $schedule)
    {
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $schedule->update([
            'subject_name' => $request->subject_name,
            'exam_date' => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Jadwal diperbarui']);
    }

    public function destroy(ExamSession $session, ExamSchedule $schedule)
    {
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Jadwal dihapus']);
    }
}
