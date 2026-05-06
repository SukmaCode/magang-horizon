<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\LogbookController;
use App\Http\Controllers\Api\MagangController;
use App\Http\Controllers\Api\PendaftaranController;
use App\Http\Controllers\Api\PenilaianController;
use App\Http\Controllers\Api\SertifikatController;
use App\Http\Controllers\Api\SignatureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ──────────────────────────────────────
// Public Routes
// ──────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ──────────────────────────────────────
// Authenticated Routes (Sanctum)
// ──────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // ──────────────────────────────────
    // Student Routes
    // ──────────────────────────────────
    Route::middleware('role:student')->prefix('student')->group(function () {
        Route::get('/pendaftaran', [PendaftaranController::class, 'index']);
        Route::post('/pendaftaran', [PendaftaranController::class, 'store']);
        Route::get('/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show']);

        Route::get('/logbook', [LogbookController::class, 'index']);
        Route::post('/logbook', [LogbookController::class, 'store']);

        Route::post('/laporan', [LaporanController::class, 'store']);

        Route::post('/signature', [SignatureController::class, 'store']);
        Route::get('/signature/latest', [SignatureController::class, 'latest']);
    });

    // ──────────────────────────────────
    // Industry Routes
    // ──────────────────────────────────
    Route::middleware('role:industry')->prefix('industry')->group(function () {
        Route::get('/seleksi', [PendaftaranController::class, 'index']);
        Route::put('/seleksi/{pendaftaran}', [PendaftaranController::class, 'updateSeleksi']);

        Route::get('/logbook/pending', [LogbookController::class, 'pending']);
        Route::put('/logbook/{logbook}/approve', [LogbookController::class, 'approve']);

        Route::post('/penilaian', [PenilaianController::class, 'gradeIndustry']);

        Route::post('/magang/{magangAktif}/agreement', [MagangController::class, 'uploadAgreement']);
    });

    // ──────────────────────────────────
    // Supervisor 1 (Dosen Pembimbing)
    // ──────────────────────────────────
    Route::middleware('role:supervisor_1')->prefix('supervisor1')->group(function () {
        Route::get('/magang', [MagangController::class, 'index']);
        Route::get('/magang/{magangAktif}', [MagangController::class, 'show']);

        Route::get('/logbook', [LogbookController::class, 'index']);
        Route::put('/logbook/{logbook}/check', [LogbookController::class, 'check']);

        Route::get('/laporan/{laporanAkhir}', [LaporanController::class, 'show']);
        Route::put('/laporan/{laporanAkhir}/review', [LaporanController::class, 'review']);
    });

    // ──────────────────────────────────
    // Supervisor 2 (Dosen Prodi)
    // ──────────────────────────────────
    Route::middleware('role:supervisor_2')->prefix('supervisor2')->group(function () {
        Route::post('/penilaian', [PenilaianController::class, 'gradeCampus']);
        Route::get('/penilaian/{penilaian}', [PenilaianController::class, 'show']);
    });

    // ──────────────────────────────────
    // Admin Routes
    // ──────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/activity-log', [AdminController::class, 'activityLog']);

        Route::put('/magang/{magangAktif}/assign-supervisor', [MagangController::class, 'assignSupervisor']);
        Route::put('/magang/{magangAktif}/transition', [MagangController::class, 'transition']);

        Route::post('/sertifikat/{magangAktif}/generate', [SertifikatController::class, 'generate']);
        Route::put('/penilaian/{penilaian}/verify', [PenilaianController::class, 'verify']);
    });

    // ──────────────────────────────────
    // Shared Routes (any authenticated user)
    // ──────────────────────────────────
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::post('/documents', [DocumentController::class, 'upload']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    Route::get('/sertifikat/{sertifikat}', [SertifikatController::class, 'show']);
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download']);

    // Notifications
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications()->paginate(15);
    });
    Route::post('/notifications/{id}/read', function (Request $request, string $id) {
        $request->user()->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    });
    Route::post('/notifications/read-all', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    });
});
