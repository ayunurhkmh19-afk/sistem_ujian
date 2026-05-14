<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamSession;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'panitia') {
            // Ambil daftar sesi ujian untuk dashboard admin
            $sessions = ExamSession::latest()->get();
            return view('dashboard.panitia', compact('sessions'));
        } 
        
        if ($user->role === 'pengawas') {
            return redirect()->route('pengawas.dashboard');
        }

        return abort(403);
    }
}