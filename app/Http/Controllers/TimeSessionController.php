<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use Illuminate\Http\Request;

class TimeSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeSession::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $timeSessions = $query->orderBy('start_time')->paginate(10);

        return view('admin.time_sessions.index', compact('timeSessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        TimeSession::create($validated);

        return back()->with('success', 'Sesi waktu berhasil ditambahkan.');
    }

    public function update(Request $request, TimeSession $timeSession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $timeSession->update($validated);

        return back()->with('success', 'Sesi waktu diperbarui.');
    }

    public function destroy(TimeSession $timeSession)
    {
        if ($timeSession->examSessions()->count() > 0) {
            return back()->with('error', 'Sesi waktu ini sedang digunakan dalam sesi ujian, tidak dapat dihapus.');
        }

        $timeSession->delete();
        return back()->with('success', 'Sesi waktu dihapus.');
    }
}
