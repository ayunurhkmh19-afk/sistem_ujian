<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%')
                  ->orWhere('class', 'like', '%' . $request->search . '%');
        }

        $students = $query->orderBy('class')->orderBy('name')->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|digits:10|unique:students,nis',
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:50',
        ]);

        Student::create($validated);

        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    // --- TAMBAHKAN METHOD INI ---
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }
    // ----------------------------

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|digits:10|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:50',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Siswa dihapus.');
    }
}