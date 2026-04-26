<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogbookRequest;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Models\Sertifikat;
use App\Services\DailyLogService;
use App\Services\GradingService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MahasiswaController extends Controller
{
    public function __construct(
        private readonly DailyLogService $dailyLogService,
        private readonly GradingService $gradingService,
    ) {}

    // ──────────────────────────────────────
    // Dashboard
    // ──────────────────────────────────────

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        // Get active internship (if exists)
        $magang = null;
        $logbookStats = [
            'total' => 0,
            'approved' => 0,
            'pending' => 0,
            'target' => 60,
        ];
        $pendaftaranCount = 0;
        $recentLogbooks = [];
        $statusMagang = 'Belum Dimulai';
        $statusDescription = 'Menunggu penempatan industri';

        if ($mahasiswa) {
            // Count pendaftarans
            $pendaftaranCount = $mahasiswa->pendaftarans()->count();

            // Find active magang through accepted pendaftaran
            $activePendaftaran = $mahasiswa->pendaftarans()
                ->where('status_seleksi', 'diterima')
                ->with('magangAktif')
                ->first();

            if ($activePendaftaran && $activePendaftaran->magangAktif) {
                $magang = $activePendaftaran->magangAktif;
                $statusMagang = $magang->status_tahapan->label();
                $statusDescription = match ($magang->status_tahapan) {
                    StatusTahapan::PERSIAPAN => 'Lengkapi dokumen & agreement',
                    StatusTahapan::PELAKSANAAN => 'Magang sedang berjalan',
                    StatusTahapan::PENUTUPAN => 'Masa penutupan & laporan akhir',
                    StatusTahapan::LULUS => 'Selamat, Anda telah lulus!',
                };

                // Logbook statistics
                $logbookStats['total'] = $magang->logbooks()->count();
                $logbookStats['approved'] = $magang->logbooks()->approved()->count();
                $logbookStats['pending'] = $magang->logbooks()->pendingApproval()->count();

                // Recent logbooks
                $recentLogbooks = $magang->logbooks()
                    ->orderBy('tanggal', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn (Logbook $l) => [
                        'id' => $l->id,
                        'tanggal' => $l->tanggal->format('d M Y'),
                        'kegiatan' => $l->kegiatan,
                        'status_presensi' => $l->status_presensi->label(),
                        'is_approved' => $l->is_approved_industri,
                    ]);
            }
        }

        return Inertia::render('Mahasiswa/Dashboard', [
            'statusMagang' => $statusMagang,
            'statusDescription' => $statusDescription,
            'logbookStats' => $logbookStats,
            'pendaftaranCount' => $pendaftaranCount,
            'recentLogbooks' => $recentLogbooks,
            'hasMagang' => $magang !== null,
        ]);
    }

    // ──────────────────────────────────────
    // Logbook
    // ──────────────────────────────────────

    public function logbook(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        $magang = $this->getActiveMagang($mahasiswa);

        $logbooks = [];
        $canSubmit = false;
        $magangId = null;
        $statusTahapan = null;

        if ($magang) {
            $magangId = $magang->id;
            $statusTahapan = $magang->status_tahapan->value;
            $canSubmit = $magang->status_tahapan->allowsDailyLogs();

            $logbooks = $magang->logbooks()
                ->orderBy('tanggal', 'desc')
                ->paginate(15)
                ->through(fn (Logbook $l) => [
                    'id' => $l->id,
                    'tanggal' => $l->tanggal->format('Y-m-d'),
                    'tanggal_display' => $l->tanggal->format('d M Y'),
                    'kegiatan' => $l->kegiatan,
                    'status_presensi' => $l->status_presensi->value,
                    'status_presensi_label' => $l->status_presensi->label(),
                    'is_approved' => $l->is_approved_industri,
                    'komentar_industri' => $l->komentar_industri,
                    'is_checked_kampus' => $l->is_checked_kampus,
                ]);
        }

        return Inertia::render('Mahasiswa/Logbook', [
            'logbooks' => $logbooks,
            'canSubmit' => $canSubmit,
            'magangId' => $magangId,
            'statusTahapan' => $statusTahapan,
        ]);
    }

    public function storeLogbook(Request $request)
    {
        $request->validate([
            'kegiatan' => 'required|string|max:2000',
            'status_presensi' => 'nullable|in:hadir,izin,sakit',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (!$magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        try {
            $this->dailyLogService->submit($magang, $request->only([
                'kegiatan', 'status_presensi', 'latitude', 'longitude',
            ]));

            return back()->with('success', 'Logbook berhasil disubmit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Laporan Akhir
    // ──────────────────────────────────────

    public function laporanAkhir(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        $laporan = null;
        $canUpload = false;
        $magangId = null;

        if ($magang) {
            $magangId = $magang->id;
            $canUpload = $magang->status_tahapan->allowsReportUpload();
            $laporan = $magang->laporanAkhir;

            if ($laporan) {
                $laporan = [
                    'id' => $laporan->id,
                    'file_laporan' => $laporan->file_laporan,
                    'status' => $laporan->status_approval_kampus->value,
                    'status_label' => $laporan->status_approval_kampus->label(),
                    'catatan_revisi' => $laporan->catatan_revisi,
                    'updated_at' => $laporan->updated_at->format('d M Y H:i'),
                ];
            }
        }

        return Inertia::render('Mahasiswa/LaporanAkhir', [
            'laporan' => $laporan,
            'canUpload' => $canUpload,
            'magangId' => $magangId,
        ]);
    }

    public function storeLaporan(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (!$magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        try {
            $path = $request->file('file')->store('documents/laporan', 'private');
            $this->gradingService->uploadLaporan($magang, $path);

            return back()->with('success', 'Laporan akhir berhasil diupload.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Sertifikat / Kelulusan
    // ──────────────────────────────────────

    public function sertifikat(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        $sertifikat = null;
        $penilaian = null;
        $isLulus = false;

        if ($magang) {
            $isLulus = $magang->status_tahapan === StatusTahapan::LULUS;

            // Load penilaian for grade info
            if ($magang->penilaian) {
                $penilaian = [
                    'nilai_industri' => $magang->penilaian->nilai_industri,
                    'nilai_kampus' => $magang->penilaian->nilai_kampus,
                    'nilai_akhir' => $magang->penilaian->nilai_akhir,
                    'is_verified' => $magang->penilaian->isVerified(),
                ];
            }

            // Load sertifikat if exists
            if ($magang->sertifikat) {
                $sertifikat = [
                    'id' => $magang->sertifikat->id,
                    'nomor_sertifikat' => $magang->sertifikat->nomor_sertifikat,
                    'tanggal_terbit' => $magang->sertifikat->tanggal_terbit?->format('d M Y'),
                    'has_file' => $magang->sertifikat->file_sertifikat_path !== null,
                ];
            }
        }

        return Inertia::render('Mahasiswa/Sertifikat', [
            'sertifikat' => $sertifikat,
            'penilaian' => $penilaian,
            'isLulus' => $isLulus,
            'hasMagang' => $magang !== null,
        ]);
    }

    public function downloadSertifikat(Request $request)
    {
        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (!$magang || $magang->status_tahapan !== StatusTahapan::LULUS) {
            return back()->with('error', 'Anda belum berhak mendownload sertifikat.');
        }

        $sertifikat = $magang->sertifikat;

        if (!$sertifikat || !$sertifikat->file_sertifikat_path) {
            return back()->with('error', 'Sertifikat belum tersedia.');
        }

        $path = storage_path('app/private/' . $sertifikat->file_sertifikat_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File sertifikat tidak ditemukan.');
        }

        return response()->download($path, "Sertifikat-{$sertifikat->nomor_sertifikat}.pdf");
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    private function getActiveMagang($mahasiswa): ?MagangAktif
    {
        if (!$mahasiswa) {
            return null;
        }

        $pendaftaran = $mahasiswa->pendaftarans()
            ->where('status_seleksi', 'diterima')
            ->with('magangAktif')
            ->first();

        return $pendaftaran?->magangAktif;
    }
}
