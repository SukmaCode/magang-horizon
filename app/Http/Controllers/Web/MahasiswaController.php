<?php

namespace App\Http\Controllers\Web;

use App\Enums\DocumentType;
use App\Enums\StatusTahapan;
use App\Http\Controllers\Controller;
use App\Models\Industri;
use App\Models\Logbook;
use App\Models\MagangAktif;
use App\Services\ApplicationService;
use App\Services\DailyLogService;
use App\Services\DocumentService;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MahasiswaController extends Controller
{
    public function __construct(
        private readonly ApplicationService $applicationService,
        private readonly DocumentService $documentService,
        private readonly DailyLogService $dailyLogService,
        private readonly GradingService $gradingService,
    ) {}

    // ──────────────────────────────────────
    // Apply Magang (Kirim CV)
    // ──────────────────────────────────────

    public function kirimCV(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        // Get list of available industries
        $industris = Industri::select('id', 'nama_perusahaan', 'alamat', 'kontak_person')
            ->orderBy('nama_perusahaan')
            ->get();

        // Get application history
        $pendaftarans = [];
        $hasAccepted = false;
        $pendingCount = 0;
        $cvUploaded = false;

        if ($mahasiswa) {
            $pendaftarans = $mahasiswa->pendaftarans()
                ->with('industri:id,nama_perusahaan,alamat')
                ->latest()
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'industri' => [
                        'id' => $p->industri->id,
                        'nama_perusahaan' => $p->industri->nama_perusahaan,
                        'alamat' => $p->industri->alamat,
                    ],
                    'status' => $p->status_seleksi->value,
                    'status_label' => $p->status_seleksi->label(),
                    'keterangan' => $p->keterangan_industri,
                    'created_at' => $p->created_at->format('d M Y H:i'),
                ]);

            $hasAccepted = $mahasiswa->pendaftarans()
                ->where('status_seleksi', 'diterima')
                ->exists();

            $pendingCount = $mahasiswa->pendaftarans()
                ->where('status_seleksi', 'pending')
                ->count();

            $cvUploaded = $mahasiswa->cv_file_path !== null;
        }

        return Inertia::render('Mahasiswa/KirimCV', [
            'industris' => $industris,
            'pendaftarans' => $pendaftarans,
            'hasAccepted' => $hasAccepted,
            'pendingCount' => $pendingCount,
            'maxApplications' => 3,
            'cvUploaded' => $cvUploaded,
        ]);
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industris,id',
            'cv_file' => 'required_without:cv_exists|file|mimes:pdf|max:10240',
        ], [
            'industri_id.required' => 'Pilih industri tujuan magang.',
            'cv_file.required_without' => 'Upload file CV Anda (format PDF).',
            'cv_file.mimes' => 'File CV harus berformat PDF.',
            'cv_file.max' => 'Ukuran file CV maksimal 10MB.',
        ]);

        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        try {
            return DB::transaction(function () use ($request, $mahasiswa) {
                // Upload CV if provided
                if ($request->hasFile('cv_file')) {
                    $cvFile = $request->file('cv_file');
                    $cvPath = $cvFile->store('documents/cv/' . $mahasiswa->id, 'private');

                    // Update mahasiswa cv_file_path
                    $mahasiswa->update(['cv_file_path' => $cvPath]);

                    // Also store in documents table for tracking
                    $this->documentService->upload(
                        $cvFile,
                        DocumentType::CV,
                        $mahasiswa,
                        $mahasiswa->user_id
                    );
                }

                // Check if CV already exists (either just uploaded or already in DB)
                if (!$mahasiswa->cv_file_path) {
                    throw new \Exception('Anda wajib mengunggah CV terlebih dahulu melalui menu Manajemen CV.');
                }

                // Create the application via service (handles logic gates)
                $this->applicationService->apply(
                    $mahasiswa->id,
                    $request->input('industri_id')
                );

                return back()->with('success', 'Lamaran berhasil dikirim! Tunggu hasil seleksi dari industri.');
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

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
        ]);

        $user = $request->user();
        $magang = $this->getActiveMagang($user->mahasiswa);

        if (!$magang) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        try {
            $this->dailyLogService->submit($magang, $request->only([
                'kegiatan', 'status_presensi'
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
