<?php

namespace App\Services;

use App\Models\PembimbingAssignment;
use App\Models\MagangAktif;
use App\Enums\StatusTahapan;
use Illuminate\Support\Facades\DB;

class PembimbingAssignmentService
{
    /**
     * Assign a dosen pembimbing to a specific active internship (magang aktif).
     */
    public function assignPembimbing(int $magangAktifId, int $dosenId, int $adminId): PembimbingAssignment
    {
        return DB::transaction(function () use ($magangAktifId, $dosenId, $adminId) {
            $magangAktif = MagangAktif::with('pendaftaran.industri')->findOrFail($magangAktifId);
            
            // Sync with old column and update status to pelaksanaan
            $magangAktif->update([
                'supervisor_kampus_id' => $dosenId,
                'supervisor_industri_id' => $magangAktif->pendaftaran->industri->user_id,
                'status_tahapan' => StatusTahapan::PELAKSANAAN,
                'tanggal_mulai' => now(),
            ]);

            $assignment = PembimbingAssignment::updateOrCreate(
                ['magang_aktif_id' => $magangAktifId],
                [
                    'dosen_id' => $dosenId,
                    'assigned_by' => $adminId,
                ]
            );

            return $assignment;
        });
    }
}
