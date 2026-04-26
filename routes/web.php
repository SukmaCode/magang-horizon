<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\MahasiswaController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ──────────────────────────────────
    // Mahasiswa Routes
    // ──────────────────────────────────
    Route::prefix('mahasiswa')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
        Route::get('/kirim-cv', fn () => Inertia::render('Mahasiswa/KirimCV'))->name('mahasiswa.kirim-cv');

        // Logbook
        Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('mahasiswa.logbook');
        Route::post('/logbook', [MahasiswaController::class, 'storeLogbook'])->name('mahasiswa.logbook.store');

        // Laporan Akhir
        Route::get('/laporan-akhir', [MahasiswaController::class, 'laporanAkhir'])->name('mahasiswa.laporan-akhir');
        Route::post('/laporan-akhir', [MahasiswaController::class, 'storeLaporan'])->name('mahasiswa.laporan-akhir.store');

        // Sertifikat / Kelulusan
        Route::get('/sertifikat', [MahasiswaController::class, 'sertifikat'])->name('mahasiswa.sertifikat');
        Route::get('/sertifikat/download', [MahasiswaController::class, 'downloadSertifikat'])->name('mahasiswa.sertifikat.download');
    });

    // ──────────────────────────────────
    // Other Role Dashboards (placeholders)
    // ──────────────────────────────────
    Route::get('/admin/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
    Route::get('/industri/dashboard', fn () => Inertia::render('Industri/Dashboard'))->name('industri.dashboard');
    Route::get('/dosen-pembimbing/dashboard', fn () => Inertia::render('DosenPembimbing/Dashboard'))->name('dosen-pembimbing.dashboard');
    Route::get('/dosen-prodi/dashboard', fn () => Inertia::render('DosenProdi/Dashboard'))->name('dosen-prodi.dashboard');
});

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->role->dashboardPath());
    }
    return redirect('/login');
});
