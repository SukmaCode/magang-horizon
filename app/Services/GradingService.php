<?php

namespace App\Services;

use App\Enums\StatusApproval;
use App\Enums\StatusTahapan;
use App\Models\LaporanAkhir;
use App\Models\MagangAktif;
use App\Models\Penilaian;
use Illuminate\Support\Facades\Storage;

class GradingService
{
    /**
     * Link industry evaluation to penilaian record.
     */
    public function gradeByIndustry(MagangAktif $magang, int $evaluationId): Penilaian
    {
        $penilaian = Penilaian::updateOrCreate(
            ['magang_id' => $magang->id],
            ['nilai_industri' => $evaluationId]
        );

        activity('grading')
            ->performedOn($penilaian)
            ->withProperties(['performance_evaluation_id' => $evaluationId])
            ->log('Industry evaluation linked');

        return $penilaian;
    }

    /**
     * Link campus evaluation to penilaian record.
     */
    public function gradeByCampus(MagangAktif $magang, int $evaluationId): Penilaian
    {
        $penilaian = Penilaian::updateOrCreate(
            ['magang_id' => $magang->id],
            ['nilai_kampus' => $evaluationId]
        );

        activity('grading')
            ->performedOn($penilaian)
            ->withProperties(['internship_evaluation_id' => $evaluationId])
            ->log('Campus evaluation linked');

        return $penilaian;
    }

    /**
     * Verify grading by admin.
     */
    public function verify(Penilaian $penilaian): Penilaian
    {
        if (!$penilaian->isComplete()) {
            throw new \Exception('Both industry and campus grades must be submitted before verification.');
        }

        $penilaian->update(['status_verifikasi_dosen_prodi' => true]);

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
    public function reviewLaporan(LaporanAkhir $laporan, StatusApproval $status, ?string $catatan = null, ?\App\Models\User $user = null): LaporanAkhir
    {
        if (! $laporan->status_approval_kampus->canTransitionTo($status)) {
            throw new \Exception("Cannot transition report from {$laporan->status_approval_kampus->value} to {$status->value}");
        }

        if ($status === StatusApproval::DISETUJUI && $user && $user->dosen) {
            app(\App\Services\PdfService::class)->generateApprovalLetter($laporan, $user, $user->dosen);
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

        // Hapus file lama jika ada untuk menghemat ruang
        $existingLaporan = LaporanAkhir::where('magang_id', $magang->id)->first();
        if ($existingLaporan && $existingLaporan->file_laporan) {
            Storage::disk('private')->delete($existingLaporan->file_laporan);
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


}
