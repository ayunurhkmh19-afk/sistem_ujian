<?php

namespace App\Http\Controllers;

use App\Models\MasterRoom;
use Illuminate\Http\Request;

class MasterRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MasterRoom::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Load with rooms (history usage) and session info
        $rooms = $query->with('rooms.session')->orderBy('name')->paginate(10);

        return view('admin.master_rooms.index', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_rooms,name',
            'capacity' => 'required|integer|min:1',
        ]);

        MasterRoom::create($validated);

        return back()->with('success', 'Ruangan master berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterRoom $masterRoom)
    {
        // We will likely use a modal, but if we need a separate page:
        return view('admin.master_rooms.edit', compact('masterRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterRoom $masterRoom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:master_rooms,name,' . $masterRoom->id,
            'capacity' => 'required|integer|min:1',
        ]);

        $masterRoom->update($validated);

        return redirect()->route('master_rooms.index')->with('success', 'Data ruangan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterRoom $masterRoom)
    {
        // Check if used
        if ($masterRoom->rooms()->count() > 0) {
            return back()->with('error', 'Ruangan ini sedang digunakan dalam sesi ujian, tidak dapat dihapus.');
        }

        $masterRoom->delete();
        return back()->with('success', 'Ruangan master dihapus.');
    }

    /**
     * Import/Sync ruangan dari riwayat sesi ujian sebelumnya.
     */
    public function syncFromHistory()
    {
        // 1. Ambil semua nama ruangan unik dari tabel rooms yang BELUM punya master_room_id
        //    atau yang namanya belum ada di master_rooms
        $existingMasterNames = MasterRoom::pluck('name')->toArray();

        $candidates = \App\Models\Room::select('name', 'capacity')
            ->whereNotIn('name', $existingMasterNames)
            ->distinct() // Ambil unik berdasarkan nama
            ->get()
            ->unique('name'); // Double check unique collection

        $count = 0;
        foreach ($candidates as $candidate) {
            MasterRoom::create([
                'name' => $candidate->name,
                'capacity' => $candidate->capacity
            ]);
            $count++;
        }

        // 2. (Opsional) Update referensi rooms yang ada agar menunjuk ke master yang baru dibuat
        //    Logic: Cari room yang master_room_id null, cari master_room by name, update id.
        $allMasters = MasterRoom::all();
        foreach ($allMasters as $master) {
            \App\Models\Room::where('name', $master->name)
                ->whereNull('master_room_id')
                ->update(['master_room_id' => $master->id]);
        }

        if ($count > 0) {
            return back()->with('success', "Berhasil mengimpor $count ruangan dari riwayat sesi.");
        }

        return back()->with('info', 'Semua ruangan di riwayat sudah terdaftar di Master Ruangan.');
    }
}
