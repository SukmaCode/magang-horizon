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
                    'created_at' => $p->created_at->format('d M Y H:i'),
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
}
