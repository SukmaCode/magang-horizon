<?php

namespace App\Services;

use App\Models\PembimbingAssignment;
use App\Models\SuratKeputusanPembimbing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SuratKeputusanService
{
    /**
     * Upload and save SK Pembimbing.
     */
    public function uploadSK(int $assignmentId, string $nomorSk, string $tanggalSk, UploadedFile $file, int $kaprodiId): SuratKeputusanPembimbing
    {
        return DB::transaction(function () use ($assignmentId, $nomorSk, $tanggalSk, $file, $kaprodiId) {
            $assignment = PembimbingAssignment::findOrFail($assignmentId);
            
            $path = $file->store('sk-pembimbing', 'public');

            $sk = SuratKeputusanPembimbing::updateOrCreate(
                ['assignment_id' => $assignmentId],
                [
                    'nomor_sk' => $nomorSk,
                    'tanggal_sk' => $tanggalSk,
                    'file_sk' => $path,
                    'uploaded_by' => $kaprodiId,
                ]
            );

            // Sync with old magang_aktif column for backward compatibility during transition
            if ($assignment->magangAktif) {
                $assignment->magangAktif->update([
                    'sk_pembimbing_path' => $path
                ]);
            }

            return $sk;
        });
    }
}
