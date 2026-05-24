<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\RoomSupervisor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // 1. Ambil jadwal milik pengawas login (V2)
        $semuaJadwal = RoomSupervisor::with(['schedule.subject.level', 'schedule.timeSession', 'room'])
            ->where('user_id', Auth::id())
            ->get();

        // 2. Mapping data untuk menyuntikkan properti 'status_sesi'
        $jadwalDiolah = $semuaJadwal->map(function ($jadwal) use ($now) {
            $schedule = $jadwal->schedule; 

            if (!$schedule) {
                $jadwal->status_sesi = 'Belum Dijadwalkan';
                return $jadwal;
            }

            // Gabungkan tanggal dan waktu dari timeSession
            $startStr = $schedule->exam_date->format('Y-m-d') . ' ' . $schedule->timeSession->start_time;
            $endStr = $schedule->exam_date->format('Y-m-d') . ' ' . $schedule->timeSession->end_time;
            
            $startDateTime = Carbon::parse($startStr);
            $endDateTime = Carbon::parse($endStr);

            // Kondisional Status Sesi
            if ($now->lt($startDateTime)) {
                $jadwal->status_sesi = 'Akan Berlangsung';
            } elseif ($now->between($startDateTime, $endDateTime)) {
                $jadwal->status_sesi = 'Sedang Berlangsung';
            } else {
                $jadwal->status_sesi = 'Sudah Berlangsung';
            }

            return $jadwal;
        });

        // 3. Kalkulasi data untuk Quick Menu
        $countHariIni = $jadwalDiolah->filter(function($j) use ($now) {
            $schedule = $j->schedule;
            return $schedule ? $schedule->exam_date->isToday() : false;
        })->count();

        $countRiwayat = $jadwalDiolah->where('status_sesi', 'Sudah Berlangsung')->count();

        return view('pengawas.dashboard', compact('jadwalDiolah', 'countHariIni', 'countRiwayat'));
    }
}
