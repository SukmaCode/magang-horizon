<?php

namespace App\Services;

use App\Models\EvaluationScore;
use App\Models\MagangAktif;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EvaluationPdfService
{
    /**
     * Get all data needed for the evaluation PDF.
     */
    public function getPdfData(int $magangAktifId): array
    {
        $magangAktif = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'internshipEvaluation.scores',
            'supervisorIndustri.signatures',
            'supervisorIndustri.industri',
        ])->findOrFail($magangAktifId);

        $mahasiswa = $magangAktif->pendaftaran->mahasiswa;
        $industri = $magangAktif->pendaftaran->industri;
        $evaluation = $magangAktif->internshipEvaluation;
        $supervisorUser = $magangAktif->supervisorIndustri;

        // Build scores in correct order
        $scores = [];
        $index = 1;
        foreach (EvaluationScore::COMPONENTS as $key => $label) {
            $score = $evaluation->scores->firstWhere('komponen', $key);
            $scores[] = [
                'no' => $index++,
                'komponen' => $key,
                'label' => $label,
                'nilai' => $score?->nilai ?? 0,
            ];
        }

        // Get supervisor signature
        $supervisorSignature = $supervisorUser
            ? $supervisorUser->signatures()->latest('signed_at')->first()
            : null;

        return [
            'mahasiswa' => [
                'nama_lengkap' => $mahasiswa->nama_lengkap,
                'nim' => $mahasiswa->nim,
                'prodi' => $mahasiswa->prodi ?? '-',
            ],
            'industri' => [
                'nama_perusahaan' => $industri->nama_perusahaan,
                'alamat' => $industri->alamat ?? '-',
                'kontak_person' => $industri->kontak_person ?? '-',
            ],
            'scores' => $scores,
            'nilai_akhir' => $evaluation->nilai_akhir,
            'catatan_supervisor' => $evaluation->catatan_supervisor ?? '-',
            'tanggal_evaluasi' => $evaluation->tanggal_evaluasi?->format('d F Y') ?? now()->format('d F Y'),
            'tanggal_mulai' => $magangAktif->tanggal_mulai?->format('d F Y') ?? '-',
            'tanggal_selesai' => $magangAktif->tanggal_selesai?->format('d F Y') ?? '-',
            'supervisor_name' => $supervisorUser?->username ?? $industri->kontak_person ?? '-',
            'supervisorSignatureBase64' => $this->getImageBase64($supervisorSignature?->signature_image_path),
        ];
    }

    /**
     * Generate the evaluation PDF using dompdf.
     */
    public function generatePdf(int $magangAktifId)
    {
        $data = $this->getPdfData($magangAktifId);

        return Pdf::loadView('pdf.evaluation', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Convert storage image path to base64 for dompdf compatibility.
     */
    private function getImageBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('private')->exists($path)) {
            return null;
        }

        $image = Storage::disk('private')->get($path);
        $type = pathinfo(Storage::disk('private')->path($path), PATHINFO_EXTENSION);

        return 'data:image/'.$type.';base64,'.base64_encode($image);
    }
}
