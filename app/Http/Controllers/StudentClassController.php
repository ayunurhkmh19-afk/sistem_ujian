<?php

namespace App\Http\Controllers;

use App\Models\StudentClass;
use App\Models\Level;
use Illuminate\Http\Request;

class StudentClassController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentClass::with('level');
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $studentClasses = $query->orderBy('name')->paginate(10);
        $levels = Level::all();

        return view('admin.student_classes.index', compact('studentClasses', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255|unique:student_classes,name',
        ]);

        StudentClass::create($validated);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, StudentClass $studentClass)
    {
        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:255|unique:student_classes,name,' . $studentClass->id,
        ]);

        $studentClass->update($validated);

        return back()->with('success', 'Kelas diperbarui.');
    }

    public function destroy(StudentClass $studentClass)
    {
        if ($studentClass->students()->count() > 0) {
            return back()->with('error', 'Kelas ini sedang memiliki siswa terdaftar, tidak dapat dihapus.');
        }

        $studentClass->delete();
        return back()->with('success', 'Kelas dihapus.');
    }
}
