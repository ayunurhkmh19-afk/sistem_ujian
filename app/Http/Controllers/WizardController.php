<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use App\Models\Room;
use App\Models\Student;
use App\Models\ExamAllocation;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class WizardController extends Controller
{
    /**
     * ==========================================
     * STEP 1: INISIASI SESI & UPLOAD DATA
     * ==========================================
     */
    public function step1()
    {
        return view('wizard.step1');
    }

    public function storeStep1(Request $request)
    {
        // Validasi input (File siswa nullable/opsional)
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'file_siswa' => 'nullable|mimes:xlsx,xls|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 1. Buat Sesi Ujian Baru
            $session = ExamSession::create([
                'title' => $request->title,
                'start_date' => $request->start_date,
                'is_active' => true
            ]);

            // 2. Import Data Siswa (Jika ada file)
            if ($request->hasFile('file_siswa')) {
                Excel::import(new StudentsImport, $request->file('file_siswa'));
                $message = 'Sesi berhasil dibuat dan data siswa baru telah diimpor!';
            } else {
                $message = 'Sesi berhasil dibuat menggunakan data siswa yang sudah ada di sistem.';
            }

            DB::commit();

            // Lanjut ke Step 2
            return redirect()->route('wizard.step2', ['session' => $session->id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * ==========================================
     * STEP 2: PENGATURAN RUANGAN (CRUD SYNC)
     * ==========================================
     */
    public function step2(ExamSession $session)
    {
        // 1. Ambil ruangan yang sudah tersimpan di sesi INI (untuk diedit)
        $rooms = $session->rooms;

        // 2. Ambil Master Ruangan (Bank Data)
        $masterRooms = \App\Models\MasterRoom::orderBy('name')->get();

        return view('wizard.step2', compact('session', 'rooms', 'masterRooms'));
    }

    public function storeStep2(Request $request, ExamSession $session)
    {
        // Validasi array input
        $request->validate([
            'rooms' => 'required|array',
            'rooms.*.id' => 'nullable|integer',     // ID null = Ruangan Baru
            'rooms.*.master_room_id' => 'nullable|integer|exists:master_rooms,id',
            'rooms.*.name' => 'required|string',
            'rooms.*.capacity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($session, $request) {
            // A. LOGIKA HAPUS
            // Ambil semua ID yang dikirim dari form (ID yang tidak null)
            $submittedIds = collect($request->rooms)->pluck('id')->filter()->toArray();
            
            // Hapus ruangan di DB yang ID-nya TIDAK ada di list yang dikirim (artinya user menghapusnya di form)
            $session->rooms()->whereNotIn('id', $submittedIds)->delete();

            // B. LOGIKA UPDATE / CREATE
            foreach ($request->rooms as $roomData) {
                $session->rooms()->updateOrCreate(
                    ['id' => $roomData['id'] ?? null], // Kunci: Jika ID ada, update. Jika null, create.
                    [
                        'master_room_id' => $roomData['master_room_id'] ?? null,
                        'name' => $roomData['name'],
                        'capacity' => $roomData['capacity']
                    ]
                );
            }
        });

        return redirect()->route('wizard.step3', ['session' => $session->id])
            ->with('success', 'Data ruangan berhasil disimpan dan disinkronisasi.');
    }

    /**
     * ==========================================
     * STEP 3: DISTRIBUSI SISWA (CORE LOGIC)
     * ==========================================
     */
    public function step3(ExamSession $session)
    {
        // 1. Data Ruangan & Keterisiannya
        $rooms = Room::where('exam_session_id', $session->id)
            ->withCount('allocations') // Hitung jumlah siswa di tiap ruangan
            ->get();

        // 2. [UPDATE] Ambil Kelas + Jumlah Siswa yg Belum Dapat Ruang
        // Query ini hanya mengambil kelas yang masih punya siswa "nganggur" di sesi ini
        $classesData = Student::select('class', DB::raw('count(*) as remaining'))
            ->whereDoesntHave('allocations', function ($query) use ($session) {
                // Filter: Siswa yang TIDAK punya alokasi di sesi ini
                $query->where('exam_session_id', $session->id);
            })
            ->groupBy('class')
            ->orderBy('class')
            ->get();

        // 3. Statistik Global
        $totalStudents = Student::count();
        // Hitung berapa siswa yang SUDAH masuk ruangan di sesi ini
        $allocatedStudents = ExamAllocation::where('exam_session_id', $session->id)->count();
        // Sisa siswa yang belum dapat ruangan
        $unallocatedCount = $totalStudents - $allocatedStudents;

        return view('wizard.step3', compact('session', 'rooms', 'classesData', 'unallocatedCount', 'allocatedStudents', 'totalStudents'));
    }

    public function storeStep3(Request $request, ExamSession $session)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'source_class' => 'required|string',
            'limit' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // 1. Cek Kapasitas Ruangan
        $currentOccupancy = $room->allocations()->count();
        $availableSeats = $room->capacity - $currentOccupancy;

        if ($request->limit > $availableSeats) {
            return back()->with('error', "Gagal! Kapasitas ruangan tidak mencukupi. Sisa kursi: $availableSeats, Permintaan: {$request->limit}");
        }

        // 2. Ambil Siswa yang Valid (Belum punya ruangan di sesi ini)
        $studentsToAssign = Student::where('class', $request->source_class)
            ->whereDoesntHave('allocations', function ($query) use ($session) {
                $query->where('exam_session_id', $session->id);
            })
            ->orderBy('name', 'asc') // Urutkan nama agar rapi
            ->take($request->limit)  // Batasi sesuai jumlah yang diminta
            ->get();

        if ($studentsToAssign->isEmpty()) {
            return back()->with('warning', "Tidak ada siswa tersisa di kelas {$request->source_class} yang belum mendapatkan ruangan.");
        }

        // 3. Eksekusi Masukkan ke Ruangan
        DB::transaction(function () use ($session, $room, $studentsToAssign) {
            // Cari nomor meja terakhir di ruangan ini (untuk melanjutkan urutan)
            $lastDeskNumber = ExamAllocation::where('room_id', $room->id)->max('desk_number') ?? 0;

            foreach ($studentsToAssign as $index => $student) {
                ExamAllocation::create([
                    'exam_session_id' => $session->id,
                    'room_id' => $room->id,
                    'student_id' => $student->id,
                    'desk_number' => $lastDeskNumber + $index + 1 // Auto-increment nomor meja
                ]);
            }
        });

        return back()->with('success', count($studentsToAssign) . " siswa dari kelas {$request->source_class} berhasil dimasukkan ke {$room->name}.");
    }

    /**
     * Fitur Reset: Mengosongkan Satu Ruangan
     */
    public function resetRoom(ExamSession $session, Room $room)
    {
        // Hapus semua alokasi di ruangan ini untuk sesi ini
        // Siswa otomatis kembali ke status "Belum Dapat Ruang"
        $deletedCount = ExamAllocation::where('exam_session_id', $session->id)
            ->where('room_id', $room->id)
            ->delete();

        return back()->with('success', "Ruangan {$room->name} berhasil dikosongkan. {$deletedCount} siswa dikembalikan ke daftar tunggu.");
    }
}