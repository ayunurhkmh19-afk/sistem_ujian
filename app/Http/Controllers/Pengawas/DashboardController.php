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

        // 1. Ambil jadwal milik pengawas login
        // Eager load schedules karena kita butuh info waktu ujian.
        // Karena di SMAN 3 Bontang 1 Sesi = 1 Mapel, maka schedules->first() 
        // akan selalu mengarah ke mata pelajaran yang tepat untuk sesi tersebut.
        $semuaJadwal = RoomSupervisor::with(['examSession.schedules', 'room'])
            ->where('user_id', Auth::id())
            ->get();

        // 2. Mapping data untuk menyuntikkan properti 'status_sesi'
        $jadwalDiolah = $semuaJadwal->map(function ($jadwal) use ($now) {
            $schedule = $jadwal->examSession->schedules->first(); 

            if (!$schedule) {
                $jadwal->status_sesi = 'Belum Dijadwalkan';
                return $jadwal;
            }

            // Gabungkan tanggal dan waktu
            $startDateTime = Carbon::parse($schedule->exam_date->format('Y-m-d') . ' ' . $schedule->start_time->format('H:i:s'));
            $endDateTime = Carbon::parse($schedule->exam_date->format('Y-m-d') . ' ' . $schedule->end_time->format('H:i:s'));

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
            $schedule = $j->examSession->schedules->first();
            return $schedule ? $schedule->exam_date->isToday() : false;
        })->count();

        $countRiwayat = $jadwalDiolah->where('status_sesi', 'Sudah Berlangsung')->count();

        return view('pengawas.dashboard', compact('jadwalDiolah', 'countHariIni', 'countRiwayat'));
    }
}
