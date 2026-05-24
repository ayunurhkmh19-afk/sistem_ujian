<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()->with([
            'studentClass.level',
            'allocations.room',
            'allocations.schedule.session',
            'allocations.schedule.subject'
        ]);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%')
                  ->orWhereHas('studentClass', function ($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $students = $query->orderBy('student_class_id')->orderBy('name')->paginate(20);
        $classes = StudentClass::with('level')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|digits:10|unique:students,nis',
            'name' => 'required|string|max:255',
            'student_class_id' => 'required|exists:student_classes,id',
        ]);

        Student::create($validated);

        return back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $classes = StudentClass::with('level')->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|digits:10|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'student_class_id' => 'required|exists:student_classes,id',
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