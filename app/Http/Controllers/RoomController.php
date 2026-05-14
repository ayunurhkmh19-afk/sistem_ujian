<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Menambah Ruangan Manual ke Sesi Tertentu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        Room::create($validated);

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    // Edit Ruangan (Misal kapasitas berubah)
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);
        
        // Cek apakah kapasitas baru lebih kecil dari jumlah siswa yang sudah ada
        $currentAllocated = $room->allocations()->count();
        if ($request->capacity < $currentAllocated) {
             return back()->with('error', "Tidak bisa mengecilkan kapasitas. Sudah ada $currentAllocated siswa di ruangan ini.");
        }

        $room->update($validated);

        return back()->with('success', 'Data ruangan diperbarui.');
    }

    // Hapus Ruangan
    public function destroy(Room $room)
    {
        $room->delete();
        return back()->with('success', 'Ruangan dihapus.');
    }
}