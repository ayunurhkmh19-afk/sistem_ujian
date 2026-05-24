<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Level;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('level');
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $subjects = $query->orderBy('name')->paginate(10);
        $levels = Level::all();

        return view('admin.subjects.index', compact('subjects', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
        ]);

        Subject::create($validated);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
        ]);

        $subject->update($validated);

        return back()->with('success', 'Mata pelajaran diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->examSessions()->count() > 0) {
            return back()->with('error', 'Mata pelajaran ini sedang digunakan dalam sesi ujian, tidak dapat dihapus.');
        }

        $subject->delete();
        return back()->with('success', 'Mata pelajaran dihapus.');
    }
}
