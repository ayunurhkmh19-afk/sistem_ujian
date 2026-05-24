<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\Room;
use App\Models\Subject;
use App\Models\TimeSession;
use App\Models\Student;
use App\Models\Level;
use App\Imports\StudentsImport;
use App\Jobs\GenerateAllocationJob;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExamSessionWizardController extends Controller
{
    /**
     * STEP 1: INFO UJIAN
     */
    public function step1()
    {
        return view('wizard.step_1_info');
    }

    public function storeStep1(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $session = ExamSession::create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
            'allocation_status' => 'Pending',
        ]);

        return redirect()->route('wizard.step2', ['session' => $session->id])
            ->with('success', 'Info Sesi Ujian berhasil disimpan. Silakan pilih mata pelajaran.');
    }

    /**
     * STEP 2: CHECKLIST MATA PELAJARAN PER TINGKATAN
     */
    public function step2(ExamSession $session)
    {
        $levels = Level::with('subjects')->orderBy('name')->get();
        $selectedSubjectIds = $session->subjects->pluck('id')->toArray();

        return view('wizard.step_2_subjects', compact('session', 'levels', 'selectedSubjectIds'));
    }

    public function storeStep2(Request $request, ExamSession $session)
    {
        $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $session->subjects()->sync($request->subjects);

        return redirect()->route('wizard.step3', ['session' => $session->id])
            ->with('success', 'Mata pelajaran berhasil dikunci. Silakan pilih ruangan dan sesi waktu.');
    }

    /**
     * STEP 3: SETUP RUANGAN & SESI WAKTU
     */
    public function step3(ExamSession $session)
    {
        $rooms = Room::orderBy('name')->get();
        $timeSessions = TimeSession::orderBy('start_time')->get();

        $selectedRoomIds = $session->rooms->pluck('id')->toArray();
        $selectedTimeSessionIds = $session->timeSessions->pluck('id')->toArray();

        return view('wizard.step_3_rooms_times', compact('session', 'rooms', 'timeSessions', 'selectedRoomIds', 'selectedTimeSessionIds'));
    }

    public function storeStep3(Request $request, ExamSession $session)
    {
        $request->validate([
            'rooms' => 'required|array|min:1',
            'rooms.*' => 'exists:rooms,id',
            'time_sessions' => 'required|array|min:1',
            'time_sessions.*' => 'exists:time_sessions,id',
        ]);

        $session->rooms()->sync($request->rooms);
        $session->timeSessions()->sync($request->time_sessions);

        return redirect()->route('wizard.step4', ['session' => $session->id])
            ->with('success', 'Ruangan dan sesi waktu berhasil disimpan. Silakan import data siswa.');
    }

    /**
     * STEP 4: IMPORT DATA SISWA
     */
    public function step4(ExamSession $session)
    {
        // Summary data siswa per level
        $levelsSummary = Level::withCount(['studentClasses as students_count' => function ($query) {
            $query->join('students', 'student_classes.id', '=', 'students.student_class_id')
                  ->select(DB::raw('count(students.id)'));
        }])->get();

        return view('wizard.step_4_students', compact('session', 'levelsSummary'));
    }

    public function storeStep4(Request $request, ExamSession $session)
    {
        $request->validate([
            'file_siswa' => 'nullable|mimes:xlsx,xls|max:2048',
        ]);

        if ($request->hasFile('file_siswa')) {
            try {
                // Clear existing students only if fresh import is desired.
                // In our design decision: fresh import is chosen so we can optionally truncate students table.
                // But to be safe, we just let the StudentsImport do updateOrCreate so no students are deleted
                // unless explicitly required. But wait, design decision #1: fresh import via Wizard.
                // Let's do a transaction and let Maatwebsite Excel parse it.
                Excel::import(new StudentsImport, $request->file('file_siswa'));
                $message = 'Data siswa baru berhasil diimpor!';
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Gagal mengimpor file Excel: ' . $e->getMessage()]);
            }
        } else {
            $message = 'Melanjutkan dengan data siswa yang sudah ada di database.';
        }

        return redirect()->route('wizard.step5', ['session' => $session->id])
            ->with('success', $message);
    }

    /**
     * STEP 5: PRE-FLIGHT CHECK & EXECUTE AG
     */
    public function step5(ExamSession $session)
    {
        $selectedRooms = $session->rooms;
        $selectedTimes = $session->timeSessions;
        $selectedSubjects = $session->subjects;

        $totalCapacity = $selectedRooms->sum('capacity');
        $timeSessionsCount = $selectedTimes->count();
        $effectiveCapacity = $totalCapacity * $timeSessionsCount;

        // Ambil list level yang aktif (memiliki mata pelajaran yang dipilih)
        $activeLevelIds = $selectedSubjects->pluck('level_id')->unique()->toArray();
        $activeLevels = Level::whereIn('id', $activeLevelIds)->get();

        $preFlightPassed = true;
        $failedLevels = [];
        $levelStats = [];

        foreach ($activeLevels as $level) {
            // Hitung siswa di level ini
            $studentsCount = Student::whereHas('studentClass', function ($q) use ($level) {
                $q->where('level_id', $level->id);
            })->count();

            $passed = $studentsCount <= $effectiveCapacity;
            if (!$passed) {
                $preFlightPassed = false;
                $failedLevels[] = [
                    'name' => $level->name,
                    'count' => $studentsCount,
                    'capacity' => $effectiveCapacity
                ];
            }

            $levelStats[] = [
                'name' => $level->name,
                'count' => $studentsCount,
                'passed' => $passed
            ];
        }

        return view('wizard.step_5_generate', compact(
            'session',
            'selectedRooms',
            'selectedTimes',
            'selectedSubjects',
            'totalCapacity',
            'timeSessionsCount',
            'effectiveCapacity',
            'levelStats',
            'preFlightPassed',
            'failedLevels'
        ));
    }

    public function executeAG(Request $request, ExamSession $session)
    {
        // 1. Double check pre-flight check
        $selectedRooms = $session->rooms;
        $selectedTimes = $session->timeSessions;
        $selectedSubjects = $session->subjects;

        $totalCapacity = $selectedRooms->sum('capacity');
        $timeSessionsCount = $selectedTimes->count();
        $effectiveCapacity = $totalCapacity * $timeSessionsCount;

        $activeLevelIds = $selectedSubjects->pluck('level_id')->unique()->toArray();
        $activeLevels = Level::whereIn('id', $activeLevelIds)->get();

        foreach ($activeLevels as $level) {
            $studentsCount = Student::whereHas('studentClass', function ($q) use ($level) {
                $q->where('level_id', $level->id);
            })->count();

            if ($studentsCount > $effectiveCapacity) {
                return back()->with('error', "Gagal memulai penjadwalan. Kapasitas ruangan tidak mencukupi untuk {$level->name}.");
            }
        }

        // 2. Set allocation status to Processing
        $session->update(['allocation_status' => 'Processing']);

        // 3. Dispatch background job
        GenerateAllocationJob::dispatch($session->id);

        return redirect()->route('sessions.index')
            ->with('success', 'Algoritma Genetika V2 berhasil dijalankan di background. Silakan pantau status pengerjaan.');
    }
}
