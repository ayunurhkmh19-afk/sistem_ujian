<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class MasterRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $rooms = $query->orderBy('name')->paginate(10);

        return view('admin.master_rooms.index', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'capacity' => 'required|integer|min:1',
        ]);

        Room::create($validated);

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $masterRoom)
    {
        return view('admin.master_rooms.edit', compact('masterRoom'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $masterRoom)
    {
        // Parameter name matches standard resource route parameter (master_room -> Room model in route binding)
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $masterRoom->id,
            'capacity' => 'required|integer|min:1',
        ]);

        $masterRoom->update($validated);

        return redirect()->route('master_rooms.index')->with('success', 'Data ruangan diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $masterRoom)
    {
        // Check if used in active exam allocations or exam sessions
        if ($masterRoom->allocations()->count() > 0 || $masterRoom->examSessions()->count() > 0) {
            return back()->with('error', 'Ruangan ini sedang digunakan dalam sesi atau alokasi ujian, tidak dapat dihapus.');
        }

        $masterRoom->delete();
        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * Obsoleted sync function for V2.
     */
    public function syncFromHistory()
    {
        return back()->with('info', 'Sinkronisasi riwayat dinonaktifkan di Skema V2 karena ruangan sudah global.');
    }
}
