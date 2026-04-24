<?php

use App\Http\Controllers\Web\AuthController;
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

    // Dashboard placeholders (redirect by role)
    Route::get('/admin/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');
    Route::get('/mahasiswa/dashboard', fn () => Inertia::render('Mahasiswa/Dashboard'))->name('mahasiswa.dashboard');
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
