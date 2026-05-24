<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $query = Level::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $levels = $query->orderBy('name')->paginate(10);

        return view('admin.levels.index', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:levels,name',
        ]);

        Level::create($validated);

        return back()->with('success', 'Tingkatan kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:levels,name,' . $level->id,
        ]);

        $level->update($validated);

        return back()->with('success', 'Tingkatan kelas diperbarui.');
    }

    public function destroy(Level $level)
    {
        if ($level->studentClasses()->count() > 0 || $level->subjects()->count() > 0) {
            return back()->with('error', 'Tingkatan ini sedang digunakan oleh kelas atau mata pelajaran, tidak dapat dihapus.');
        }

        $level->delete();
        return back()->with('success', 'Tingkatan kelas dihapus.');
    }
}
