<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\ExamAllocation;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan facade ini diimport
use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Cetak Semua Kartu dalam Satu Sesi
     */
    public function printAll(ExamSession $session)
    {
        // Ambil data alokasi yang terikat ke jadwal sesi ini, urutkan berdasarkan ruangan lalu nomor meja
        $allocations = ExamAllocation::with(['student.studentClass.level', 'room', 'schedule.subject', 'schedule.timeSession'])
            ->whereHas('schedule', function ($q) use ($session) {
                $q->where('exam_session_id', $session->id);
            })
            ->orderBy('room_id')
            ->orderBy('desk_number')
            ->get();

        if ($allocations->isEmpty()) {
            return back()->with('error', 'Belum ada data kartu ujian yang dibuat.');
        }

        $pdf = Pdf::loadView('exports.cards_pdf', compact('session', 'allocations'));
        
        // Atur ukuran kertas (misal A4)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("Kartu_Ujian_{$session->title}.pdf");
    }

    /**
     * Cetak Kartu Per Ruangan (Opsional)
     */
    public function printByRoom(ExamSession $session, $roomId)
    {
        $allocations = ExamAllocation::with(['student.studentClass.level', 'room', 'schedule.subject', 'schedule.timeSession'])
            ->whereHas('schedule', function ($q) use ($session) {
                $q->where('exam_session_id', $session->id);
            })
            ->where('room_id', $roomId)
            ->orderBy('desk_number')
            ->get();

        if ($allocations->isEmpty()) {
            return back()->with('error', 'Ruangan ini masih kosong.');
        }

        $pdf = Pdf::loadView('exports.cards_pdf', compact('session', 'allocations'));
        return $pdf->stream("Kartu_Ujian_Ruang_{$roomId}.pdf");
    }
}