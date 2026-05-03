<?php

namespace App\Services;

use App\Enums\StatusApproval;
use App\Enums\StatusTahapan;
use App\Models\LaporanAkhir;
use App\Models\MagangAktif;
use App\Models\Penilaian;

class GradingService
{
    /**
     * Submit industry grade.
     */
    public function gradeByIndustry(MagangAktif $magang, float $nilai): Penilaian
    {
        $this->validateGrade($nilai);

        $penilaian = Penilaian::updateOrCreate(
            ['magang_id' => $magang->id],
            ['nilai_industri' => $nilai]
        );

        activity('grading')
            ->performedOn($penilaian)
            ->withProperties(['nilai_industri' => $nilai])
            ->log('Industry grade submitted');

        return $penilaian;
    }

    /**
     * Submit campus grade.
     */
    public function gradeByCampus(MagangAktif $magang, float $nilai): Penilaian
    {
        $this->validateGrade($nilai);

        $penilaian = Penilaian::updateOrCreate(
            ['magang_id' => $magang->id],
            ['nilai_kampus' => $nilai]
        );

        activity('grading')
            ->performedOn($penilaian)
            ->withProperties(['nilai_kampus' => $nilai])
            ->log('Campus grade submitted');

        return $penilaian;
    }

    /**
     * Verify grading by admin.
     */
    public function verify(Penilaian $penilaian): Penilaian
    {
        if (! $penilaian->isComplete()) {
            throw new \Exception('Both industry and campus grades must be submitted before verification.');
        }

        $penilaian->update(['status_verifikasi_admin' => true]);

        // ✅ Business rule: setelah verifikasi admin, status magang otomatis jadi LULUS
        // Dipindahkan dari DosenProdiController ke sini agar encapsulated di Service
        $penilaian->magangAktif->update(['status_tahapan' => StatusTahapan::LULUS]);

        activity('grading')
            ->performedOn($penilaian)
            ->log('Grading verified by admin — internship marked as LULUS');

        return $penilaian->fresh();
    }

    /**
     * Review final report (by campus supervisor).
     */
    public function reviewLaporan(LaporanAkhir $laporan, StatusApproval $status, ?string $catatan = null): LaporanAkhir
    {
        if (! $laporan->status_approval_kampus->canTransitionTo($status)) {
            throw new \Exception("Cannot transition report from {$laporan->status_approval_kampus->value} to {$status->value}");
        }

        $laporan->update([
            'status_approval_kampus' => $status,
            'catatan_revisi' => $catatan,
        ]);

        activity('laporan')
            ->performedOn($laporan)
            ->withProperties(['status' => $status->value, 'catatan' => $catatan])
            ->log("Report review: {$status->label()}");

        return $laporan;
    }

    /**
     * Upload final report.
     */
    public function uploadLaporan(MagangAktif $magang, string $filePath): LaporanAkhir
    {
        if (! $magang->status_tahapan->allowsReportUpload()) {
            throw new \Exception('Reports can only be uploaded during pelaksanaan or penutupan phase.');
        }

        $laporan = LaporanAkhir::updateOrCreate(
            ['magang_id' => $magang->id],
            [
                'file_laporan' => $filePath,
                'status_approval_kampus' => StatusApproval::PENDING,
            ]
        );

        activity('laporan')
            ->performedOn($laporan)
            ->log('Final report uploaded');

        return $laporan;
    }

    private function validateGrade(float $nilai): void
    {
        if ($nilai < 0 || $nilai > 100) {
            throw new \InvalidArgumentException('Grade must be between 0 and 100.');
        }
    }
}
