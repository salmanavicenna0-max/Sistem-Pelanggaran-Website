<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/ubah-password', [PasswordChangeController::class, 'edit'])->name('password.change.edit');
    Route::put('/ubah-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

Route::middleware(['auth', 'active', 'password.changed'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Guru ---
    Route::get('/teacher.dashboard', [DashboardController::class, 'index'])->middleware('role:guru')->name('teacher.dashboard');

    // --- Kesiswaan / BK ---
    Route::get('/student-affairs.dashboard', [DashboardController::class, 'index'])->middleware('role:kesiswaan_bk')->name('student-affairs.dashboard');

    // --- Wali Kelas ---
    Route::get('/homeroom.dashboard', [DashboardController::class, 'index'])->middleware('homeroom.teacher')->name('homeroom.dashboard');

    // --- Siswa ---
    Route::get('/student.dashboard', [DashboardController::class, 'index'])->middleware('role:siswa')->name('student.dashboard');

    // --- Laporan ---
    Route::get('/lapor', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/lapor', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->middleware('role:kesiswaan_bk')->name('reports.index');
    Route::put('/reports/{report}/verify', [ReportController::class, 'verify'])->middleware('role:kesiswaan_bk')->name('reports.verify');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    // --- Poin ---
    Route::get('/poin', [PointController::class, 'index'])->name('points.siswa');
    Route::get('/poin/{student_id}/riwayat', [PointController::class, 'riwayat'])->middleware('role:kesiswaan_bk')->name('points.riwayat');

    // --- Master Data ---
    Route::get('/master/aturan', [MasterDataController::class, 'rules'])->name('master.rules');
    Route::get('/master/siswa', [MasterDataController::class, 'students'])->name('master.students');
    Route::get('/master/export', [MasterDataController::class, 'export'])->name('master.export');
});

// --- Case & Action Routes (added for completeness) ---
Route::middleware(['auth', 'active', 'password.changed'])->group(function (): void {
    Route::get('/kasus', [CaseController::class, 'index'])->name('cases.index');
    Route::get('/kasus/create', [CaseController::class, 'create'])->name('cases.create');
    Route::post('/kasus', [CaseController::class, 'store'])->name('cases.store');
    Route::get('/kasus/{case}', [CaseController::class, 'show'])->name('cases.show');
    Route::post('/kasus/{case}/action', [CaseController::class, 'storeAction'])->name('cases.action');
});