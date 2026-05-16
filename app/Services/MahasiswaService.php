<?php

namespace App\Services;

use App\Enums\StatusAgreement;
use App\Enums\StatusTahapan;
use App\Models\MagangAktif;
use App\Models\Mahasiswa;

class MahasiswaService
{
    // ──────────────────────────────────────
    // Kirim CV Page Data
    // ──────────────────────────────────────

    /**
     * Ambil semua data yang dibutuhkan halaman KirimCV.
     * Dipindahkan dari MahasiswaController::kirimCV() agar mudah di-test.
     */
    public function getKirimCvData(Mahasiswa $mahasiswa): array
    {
        $pendaftarans = $mahasiswa->pendaftarans()
            ->with(['industri:id,nama_perusahaan,alamat', 'magangAktif'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $agreementRejected = false;
                $alasanTolakAgreement = null;

                if (in_array($p->status_seleksi->value, ['diterima', 'menunggu_mahasiswa']) && $p->magangAktif) {
                    $agreementRejected = $p->magangAktif->isAgreementRejected();
                    $alasanTolakAgreement = $p->magangAktif->alasan_tolak_agreement;
                }

                return [
                    'id' => $p->id,
                    'industri' => [
                        'id' => $p->industri->id,
                        'nama_perusahaan' => $p->industri->nama_perusahaan,
                        'alamat' => $p->industri->alamat,
                    ],
                    'status' => $p->status_seleksi->value,
                    'status_label' => $p->status_seleksi->label(),
                    'keterangan' => $p->keterangan_industri,
                    'created_at' => $p->created_at?->format('d M Y H:i') ?? '-',
                    'agreement_rejected' => $agreementRejected,
                    'alasan_tolak_agreement' => $alasanTolakAgreement,
                ];
            });

        // Cek apakah mahasiswa punya pendaftaran yang agreementnya sudah ditandatangani
        $hasAccepted = $mahasiswa->pendaftarans()
            ->whereIn('status_seleksi', ['diterima', 'menunggu_mahasiswa'])
            ->whereHas('magangAktif', function ($q) {
                $q->where('status_agreement', StatusAgreement::ACCEPTED);
            })
            ->exists();

        return [
            'pendaftarans' => $pendaftarans,
            'hasAccepted' => $hasAccepted,
            'pendingCount' => $mahasiswa->pendaftarans()->where('status_seleksi', 'pending')->count(),
            'cvUploaded' => $mahasiswa->cv_file_path !== null,
        ];
    }

    // ──────────────────────────────────────
    // Agreement Page Data
    // ──────────────────────────────────────

    /**
     * Ambil daftar agreement untuk semua pendaftaran yang diterima.
     * Menggantikan foreach loop di MahasiswaController::agreement().
     */
    public function getAgreements(Mahasiswa $mahasiswa): array
    {
        return $mahasiswa->pendaftarans()
            ->whereIn('status_seleksi', ['diterima', 'menunggu_mahasiswa'])
            ->with(['magangAktif.industri'])
            ->get()
            ->map(fn ($p) => $p->magangAktif ? [
                'id' => $p->magangAktif->id,
                'industri' => $p->magangAktif->industri?->nama_perusahaan,
                'has_agreement' => $p->magangAktif->file_agreement_industri !== null,
                'status_agreement' => $p->magangAktif->status_agreement?->value,
                'alasan_tolak' => $p->magangAktif->alasan_tolak_agreement,
                'download_url' => $p->magangAktif->file_agreement_industri
                    ? route('mahasiswa.agreement.download', $p->magangAktif->id)
                    : null,
            ] : null)
            ->filter()    // buang null (yang tidak punya magangAktif)
            ->values()
            ->toArray();
    }

    // ──────────────────────────────────────
    // Dashboard Page Data
    // ──────────────────────────────────────

    /**
     * Ambil deskripsi status magang berdasarkan tahapan.
     * Business rule ini tidak boleh ada di controller (match statement).
     */
    public function getStatusDescription(StatusTahapan $tahapan): string
    {
        return match ($tahapan) {
            StatusTahapan::PERSIAPAN => 'Lengkapi dokumen & agreement',
            StatusTahapan::PELAKSANAAN => 'Magang sedang berjalan',
            StatusTahapan::PENUTUPAN => 'Masa penutupan & laporan akhir',
            StatusTahapan::LULUS => 'Selamat, Anda telah lulus!',
        };
    }

    // ──────────────────────────────────────
    // Dashboard Data
    // ──────────────────────────────────────

    public function getDashboardData(?Mahasiswa $mahasiswa): array
    {
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
            $pendaftaranCount = $mahasiswa->pendaftarans()->count();
            $magang = $mahasiswa->active_magang;

            if ($magang) {
                $statusMagang = $magang->status_tahapan->label();
                $statusDescription = $this->getStatusDescription($magang->status_tahapan);

                $logbookStats['total'] = $magang->logbooks()->count();
                $logbookStats['approved'] = $magang->logbooks()->approved()->count();
                $logbookStats['pending'] = $magang->logbooks()->pendingApproval()->count();

                $recentLogbooks = $magang->logbooks()
                    ->orderBy('tanggal_waktu', 'desc')
                    ->take(5)
                    ->get()
                    ->map(fn (\App\Models\Logbook $l) => [
                        'id' => $l->id,
                        'tanggal_waktu' => $l->tanggal_waktu?->format('d M Y') ?? '-',
                        'kegiatan' => $l->kegiatan,
                        'status_presensi' => $l->status_presensi->label(),
                        'is_approved' => $l->is_approved_industri,
                    ]);
            }
        }

        return compact(
            'statusMagang',
            'statusDescription',
            'logbookStats',
            'pendaftaranCount',
            'recentLogbooks',
            'magang'
        );
    }

    // ──────────────────────────────────────
    // Sertifikat Page Data
    // ──────────────────────────────────────

    public function getSertifikatData(?MagangAktif $magang): array
    {
        $sertifikat = null;
        $penilaian = null;
        $isLulus = false;

        if ($magang) {
            $isLulus = $magang->status_tahapan === StatusTahapan::LULUS;

            // Load evaluation relationships through penilaian FK
            $magang->load(['penilaian.performanceEvaluation', 'penilaian.internshipEvaluation']);

            if ($magang->penilaian) {
                $penilaian = [
                    'nilai_industri' => $magang->penilaian->performanceEvaluation?->nilai_akhir,
                    'nilai_kampus' => $magang->penilaian->internshipEvaluation?->overall_score,
                    'nilai_akhir' => $magang->penilaian->nilai_akhir,
                    'is_verified' => $magang->penilaian->isVerified(),
                ];
            }

            if ($magang->sertifikat) {
                $sertifikat = [
                    'id' => $magang->sertifikat->id,
                    'nomor_sertifikat' => $magang->sertifikat->nomor_sertifikat,
                    'tanggal_terbit' => $magang->sertifikat->tanggal_terbit?->format('d M Y'),
                    'has_file' => $magang->sertifikat->file_sertifikat_path !== null,
                ];
            }
        }

        return compact('sertifikat', 'penilaian', 'isLulus');
    }
}
