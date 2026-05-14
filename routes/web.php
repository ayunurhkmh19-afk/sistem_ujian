<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WizardController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamSessionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ExamScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes - E-UJIAN SMAN 3 BONTANG
|--------------------------------------------------------------------------
*/

// Redirect Halaman Depan ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard Utama (Redirect berdasarkan Role)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Group Profile (Breeze Default)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| AREA KHUSUS PANITIA (ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:panitia'])->group(function () {

    // --- 1. WIZARD (ALUR PEMBUATAN KARTU) ---
    // Step 1: Upload & Buat Sesi
    Route::get('/wizard/start', [WizardController::class, 'step1'])->name('wizard.step1');
    Route::post('/wizard/start', [WizardController::class, 'storeStep1']);

    // Step 2: Setup Ruangan
    Route::get('/wizard/{session}/rooms', [WizardController::class, 'step2'])->name('wizard.step2');
    Route::post('/wizard/{session}/rooms', [WizardController::class, 'storeStep2']);

    // Step 3: Distribusi Siswa
    Route::get('/wizard/{session}/distribute', [WizardController::class, 'step3'])->name('wizard.step3');
    Route::post('/wizard/{session}/distribute', [WizardController::class, 'storeStep3']);
    
    // Reset Ruangan (Hapus isinya saja)
    Route::delete('/wizard/{session}/room/{room}/reset', [WizardController::class, 'resetRoom'])->name('wizard.resetRoom');


    // --- 2. CETAK KARTU (PDF) ---
    Route::get('/print/{session}/all', [PrintController::class, 'printAll'])->name('print.all');
    Route::get('/print/{session}/room/{roomId}', [PrintController::class, 'printByRoom'])->name('print.room');


    // --- 3. MANAJEMEN DATA (CRUD) ---
    
    // Siswa
    Route::resource('students', StudentController::class)->except(['show']);

    // Sesi Ujian
    Route::resource('sessions', ExamSessionController::class)->except(['create', 'store', 'show']);
    Route::patch('/sessions/{examSession}/toggle', [ExamSessionController::class, 'toggleStatus'])->name('sessions.toggle');
    Route::post('/sessions/{session}/generate-alokasi', [ExamSessionController::class, 'generateAlokasi'])->name('sessions.generate.alokasi');
    Route::get('/sessions/{session}/status-ag', [ExamSessionController::class, 'cekStatusAG'])->name('sessions.status.ag');

    // Ruangan (Manual Edit)
    Route::resource('rooms', RoomController::class)->only(['store', 'update', 'destroy']);
    
    // Master Ruangan (Bank Data)
    Route::post('/master_rooms/sync', [\App\Http\Controllers\MasterRoomController::class, 'syncFromHistory'])->name('master_rooms.sync');
    Route::resource('master_rooms', \App\Http\Controllers\MasterRoomController::class);


    // --- 4. MANAJEMEN JADWAL (FULLCALENDAR) ---
    
    // Halaman Pemilihan Sesi (SEBELUM KALENDER)
    Route::get('/schedules/select', [ExamScheduleController::class, 'selection'])->name('schedules.selection');

    // Halaman Kalender & API AJAX
    Route::get('/sessions/{session}/schedules', [ExamScheduleController::class, 'index'])->name('sessions.schedules.index');
    Route::post('/sessions/{session}/schedules', [ExamScheduleController::class, 'store'])->name('sessions.schedules.store');
    Route::put('/sessions/{session}/schedules/{schedule}', [ExamScheduleController::class, 'update'])->name('sessions.schedules.update');
    Route::delete('/sessions/{session}/schedules/{schedule}', [ExamScheduleController::class, 'destroy'])->name('sessions.schedules.destroy');

});

/*
|--------------------------------------------------------------------------
| AREA KHUSUS PENGAWAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Pengawas\DashboardController::class, 'index'])->name('dashboard');
    
    // Detail Sesi (Absensi + Berita Acara)
    Route::get('/sesi/{session_id}/ruang/{room_id}', [\App\Http\Controllers\Pengawas\SessionController::class, 'show'])->name('sesi.detail');
    Route::post('/absensi', [\App\Http\Controllers\Pengawas\SessionController::class, 'storeAttendance'])->name('absensi.store');
    Route::post('/report', [\App\Http\Controllers\Pengawas\SessionController::class, 'storeReport'])->name('report.store');
});

require __DIR__.'/auth.php';