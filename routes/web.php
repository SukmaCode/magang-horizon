<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CompletionLetterController;
use App\Http\Controllers\Web\CvController;
use App\Http\Controllers\Web\DosenPembimbingController;
use App\Http\Controllers\Web\DosenProdiController;
use App\Http\Controllers\Web\EvaluationReportController;
use App\Http\Controllers\Web\IndustriController;
use App\Http\Controllers\Web\InternshipEvaluationController;
use App\Http\Controllers\Web\LogbookReportController;
use App\Http\Controllers\Web\MahasiswaController;
use App\Http\Controllers\Web\SignatureController;
use App\Http\Controllers\Web\StudentProfileController;
use App\Http\Controllers\PembimbingAssignmentController;
use App\Http\Controllers\SuratKeputusanController;
use Illuminate\Support\Facades\Route;

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
    // Shared Routes
    // ──────────────────────────────────
    Route::get('/logbook/report/{magangAktif}', [LogbookReportController::class, 'download'])
        ->name('logbook.report.download');

    Route::get('/completion-letter/{magangAktif}/download', [CompletionLetterController::class, 'download'])
        ->name('completion-letter.download');

    Route::get('/evaluation-report/{magangAktif}/download', [EvaluationReportController::class, 'download'])
        ->name('evaluation-report.download');

    Route::post('/signature', [SignatureController::class, 'store'])
        ->name('signature.store');

    // ──────────────────────────────────
    // Mahasiswa Routes
    // ──────────────────────────────────
    Route::prefix('mahasiswa')->middleware('checkRole:mahasiswa')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');

        // Profil Mahasiswa
        Route::get('/profil', [StudentProfileController::class, 'show'])->name('mahasiswa.profil');
        Route::put('/profil', [StudentProfileController::class, 'update'])->name('mahasiswa.profil.update');
        Route::post('/profil/photo', [StudentProfileController::class, 'uploadPhoto'])->name('mahasiswa.profil.upload-photo');
        Route::delete('/profil/photo', [StudentProfileController::class, 'deletePhoto'])->name('mahasiswa.profil.delete-photo');

        Route::get('/kirim-cv', [MahasiswaController::class, 'kirimCV'])->name('mahasiswa.kirim-cv');
        Route::post('/kirim-cv', [MahasiswaController::class, 'storeApplication'])->name('mahasiswa.kirim-cv.store');
        Route::get('/agreement', [MahasiswaController::class, 'agreement'])->name('mahasiswa.agreement');
        Route::get('/agreement/{magangAktif}/download', [MahasiswaController::class, 'downloadAgreement'])->name('mahasiswa.agreement.download');
        Route::post('/agreement/{magangAktif}/accept', [MahasiswaController::class, 'acceptAgreement'])->name('mahasiswa.agreement.accept');
        Route::post('/agreement/{magangAktif}/reject', [MahasiswaController::class, 'rejectAgreement'])->name('mahasiswa.agreement.reject');

        // Manajemen CV
        Route::get('/manajemen-cv', [CvController::class, 'index'])->name('mahasiswa.cv.index');
        Route::post('/manajemen-cv/upload', [CvController::class, 'upload'])->name('mahasiswa.cv.upload');
        Route::delete('/manajemen-cv/delete', [CvController::class, 'destroy'])->name('mahasiswa.cv.destroy');
        Route::get('/manajemen-cv/preview', [CvController::class, 'previewCv'])->name('mahasiswa.cv.preview');

        // Manajemen CV
        Route::get('/manajemen-cv', [CvController::class, 'index'])->name('mahasiswa.cv.index');
        Route::post('/manajemen-cv/upload', [CvController::class, 'upload'])->name('mahasiswa.cv.upload');
        Route::delete('/manajemen-cv/delete', [CvController::class, 'destroy'])->name('mahasiswa.cv.destroy');
        Route::get('/manajemen-cv/preview', [CvController::class, 'previewCv'])->name('mahasiswa.cv.preview');

        // Logbook
        Route::get('/logbook', [MahasiswaController::class, 'logbook'])->name('mahasiswa.logbook');
        Route::post('/logbook', [MahasiswaController::class, 'storeLogbook'])->name('mahasiswa.logbook.store');

        // Laporan Akhir
        Route::get('/laporan-akhir', [MahasiswaController::class, 'laporanAkhir'])->name('mahasiswa.laporan-akhir');
        Route::post('/laporan-akhir', [MahasiswaController::class, 'storeLaporan'])->name('mahasiswa.laporan-akhir.store');

        // Evaluasi Magang
        Route::get('/evaluasi', [InternshipEvaluationController::class, 'show'])->name('mahasiswa.evaluasi');

        // Sertifikat / Kelulusan
        Route::get('/sertifikat', [MahasiswaController::class, 'sertifikat'])->name('mahasiswa.sertifikat');
        Route::get('/sertifikat/download', [MahasiswaController::class, 'downloadSertifikat'])->name('mahasiswa.sertifikat.download');
    });

    // ──────────────────────────────────
    // Supervisor Industri Routes
    // ──────────────────────────────────
    Route::prefix('industri')->middleware('checkRole:supervisor_industri')->group(function () {
        Route::get('/dashboard', [IndustriController::class, 'dashboard'])->name('industri.dashboard');

        // Seleksi CV
        Route::get('/seleksi-cv', [IndustriController::class, 'seleksiCV'])->name('industri.seleksi-cv');
        Route::post('/seleksi-cv/{pendaftaran}/accept', [IndustriController::class, 'acceptApplication'])->name('industri.seleksi-cv.accept');
        Route::post('/seleksi-cv/{pendaftaran}/reject', [IndustriController::class, 'rejectApplication'])->name('industri.seleksi-cv.reject');
        Route::get('/seleksi-cv/{pendaftaran}/cv', [CvController::class, 'viewApplicantCv'])->name('industri.seleksi-cv.download-cv');
        Route::get('/seleksi-cv/{pendaftaran}/profil', [IndustriController::class, 'applicantProfile'])->name('industri.seleksi-cv.profil');

        // Agreement
        Route::get('/agreement', [IndustriController::class, 'agreement'])->name('industri.agreement');
        Route::post('/agreement/{magangAktif}', [IndustriController::class, 'uploadAgreement'])->name('industri.agreement.upload');

        // Persetujuan Logbook
        Route::get('/persetujuan-logbook', [IndustriController::class, 'persetujuanLogbook'])->name('industri.persetujuan-logbook');
        Route::post('/persetujuan-logbook/{logbook}/approve', [IndustriController::class, 'approveLogbook'])->name('industri.logbook.approve');

        // Evaluasi Mahasiswa
        Route::get('/evaluasi', [InternshipEvaluationController::class, 'index'])->name('industri.evaluasi');
        Route::get('/evaluasi/{magangAktif}', [InternshipEvaluationController::class, 'create'])->name('industri.evaluasi.create');
        Route::post('/evaluasi/{magangAktif}', [InternshipEvaluationController::class, 'store'])->name('industri.evaluasi.store');
        Route::post('/evaluasi/{evaluation}/submit', [InternshipEvaluationController::class, 'submit'])->name('industri.evaluasi.submit');
        Route::post('/evaluasi/{evaluation}/finalize', [InternshipEvaluationController::class, 'finalize'])->name('industri.evaluasi.finalize');

        // Input Nilai (Legacy — redirect to evaluasi)
        Route::get('/input-nilai', [IndustriController::class, 'inputNilai'])->name('industri.input-nilai');
        Route::post('/input-nilai/{magangAktif}', [IndustriController::class, 'storeNilai'])->name('industri.input-nilai.store');

        // Internship Completion Letter
        Route::get('/completion-letter', [IndustriController::class, 'completionLetter'])->name('industri.completion-letter');
        Route::post('/completion-letter/{magangAktif}', [IndustriController::class, 'storeCompletionLetter'])->name('industri.completion-letter.store');
    });

    // ──────────────────────────────────
    // Admin Routes
    // ──────────────────────────────────
    Route::prefix('admin')->middleware('checkRole:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/periode', [AdminController::class, 'kelolaPeriode'])->name('admin.periode');
        Route::post('/periode', [AdminController::class, 'storePeriode'])->name('admin.periode.store');
        Route::patch('/periode/{periode}', [AdminController::class, 'updatePeriode'])->name('admin.periode.update');

        Route::get('/assign-pembimbing', [AdminController::class, 'assignPembimbing'])->name('admin.assign-pembimbing');
        Route::post('/assign-pembimbing', [PembimbingAssignmentController::class, 'store'])->name('admin.assign-pembimbing.store');

        Route::get('/verifikasi-kelulusan', [AdminController::class, 'verifikasiKelulusan'])->name('admin.verifikasi');
        Route::post('/verifikasi-kelulusan/{magangAktif}/terbitkan', [AdminController::class, 'terbitkanSertifikat'])->name('admin.sertifikat.terbitkan');

        Route::get('/manajemen-user', [AdminController::class, 'manajemenUser'])->name('admin.users');
    });

    // ──────────────────────────────────
    // Dosen Pembimbing Routes
    // ──────────────────────────────────
    Route::prefix('dosen-pembimbing')->middleware('checkRole:dosen_pembimbing')->group(function () {
        Route::get('/dashboard', [DosenPembimbingController::class, 'dashboard'])->name('dosen-pembimbing.dashboard');

        Route::get('/mahasiswa-bimbingan', [PembimbingAssignmentController::class, 'indexBimbingan'])->name('dosen-pembimbing.bimbingan.index');
        Route::get('/surat-keputusan/{sk}/download', [SuratKeputusanController::class, 'download'])->name('dosen-pembimbing.sk.download');

        Route::get('/monitoring-logbook', [DosenPembimbingController::class, 'monitoringLogbook'])->name('dosen-pembimbing.logbook');

        Route::get('/review-laporan', [DosenPembimbingController::class, 'reviewLaporan'])->name('dosen-pembimbing.laporan');
        Route::post('/review-laporan/{laporan}/review', [DosenPembimbingController::class, 'submitReviewLaporan'])->name('dosen-pembimbing.laporan.review');
        Route::get('/review-laporan/{laporan}/download', [DosenPembimbingController::class, 'downloadLaporan'])->name('dosen-pembimbing.laporan.download');

        Route::get('/input-nilai', [DosenPembimbingController::class, 'inputNilai'])->name('dosen-pembimbing.input-nilai');
        Route::post('/input-nilai/{magangAktif}', [DosenPembimbingController::class, 'storeNilai'])->name('dosen-pembimbing.input-nilai.store');
    });

    // ──────────────────────────────────
    // Dosen Prodi Routes
    // ──────────────────────────────────
    Route::prefix('dosen-prodi')->middleware('checkRole:dosen_prodi')->group(function () {
        Route::get('/dashboard', [DosenProdiController::class, 'dashboard'])->name('dosen-prodi.dashboard');

        Route::get('/surat-keputusan', [SuratKeputusanController::class, 'indexUpload'])->name('dosen-prodi.sk.index');
        Route::post('/surat-keputusan', [SuratKeputusanController::class, 'store'])->name('dosen-prodi.sk.store');
        Route::get('/surat-keputusan/{sk}/download', [SuratKeputusanController::class, 'download'])->name('dosen-prodi.sk.download');

        Route::get('/verifikasi-kelulusan', [DosenProdiController::class, 'verifikasiKelulusan'])->name('dosen-prodi.verifikasi');
        Route::post('/verifikasi-kelulusan/{penilaian}/verify', [DosenProdiController::class, 'submitVerifikasi'])->name('dosen-prodi.verifikasi.submit');
    });
});

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->role->dashboardPath());
    }

    return redirect('/login');
});
