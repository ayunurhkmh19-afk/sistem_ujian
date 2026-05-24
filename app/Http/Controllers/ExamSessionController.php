<?php

namespace App\Http\Controllers;

use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    // List all sessions (History)
    public function index()
    {
        $sessions = ExamSession::withCount(['rooms', 'allocations'])
                    ->latest()
                    ->paginate(10);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function edit(ExamSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    // Update detail session
    // Note: Ensure the type hint matches the route parameter (implicit binding)
    // If route is /sessions/{session}, use ExamSession $session
    public function update(Request $request, ExamSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $session->update($validated);

        return redirect()->route('sessions.index')->with('success', 'Informasi sesi diperbarui.');
    }

    // Delete session
    public function destroy(ExamSession $session)
    {
        $session->delete();
        return redirect()->route('sessions.index')->with('success', 'Sesi ujian dan seluruh datanya berhasil dihapus.');
    }
    
    // Toggle Status
    public function toggleStatus(ExamSession $examSession)
    {
        // Note: If route param is {examSession}, keep variable name matching or type hint works too
        $examSession->update(['is_active' => !$examSession->is_active]);
        return back()->with('success', 'Status sesi diubah.');
    }

    /**
     * Trigger Alokasi Otomatis (Algoritma Genetika)
     */
    public function generateAlokasi($id)
    {
        $session = ExamSession::findOrFail($id);
        
        // Update status ke Processing
        $session->update(['allocation_status' => 'Processing']);
        
        // Dispatch Background Job
        \App\Jobs\GenerateAllocationJob::dispatch($id);
        
        return back()->with('success', 'Algoritma Genetika mulai menyusun jadwal di latar belakang.');
    }

    /**
     * Cek Status AG (Untuk Polling AJAX)
     */
    public function cekStatusAG($id)
    {
        $session = ExamSession::findOrFail($id);
        return response()->json([
            'status' => $session->allocation_status,
            'status_label' => $session->allocation_status // Bisa dikembangkan untuk label yang lebih user-friendly
        ]);
    }
}