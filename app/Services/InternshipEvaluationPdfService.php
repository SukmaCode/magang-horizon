<?php

namespace App\Services;

use App\Enums\InternshipEvalCategory;
use App\Models\MagangAktif;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InternshipEvaluationPdfService
{
    /**
     * Get all data needed for the internship evaluation PDF.
     */
    public function getPdfData(int $magangAktifId): array
    {
        $magangAktif = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'internshipEvaluation.scores',
            'internshipEvaluation.comment',
            'internshipEvaluation.evaluator.signatures',
            'internshipEvaluation.evaluator.dosen',
        ])->findOrFail($magangAktifId);

        $mahasiswa = $magangAktif->pendaftaran->mahasiswa;
        $industri = $magangAktif->pendaftaran->industri;
        $evaluation = $magangAktif->internshipEvaluation;
        $evaluatorUser = $evaluation->evaluator;

        // Build structured scores for PDF
        $categories = [];
        foreach (InternshipEvalCategory::cases() as $category) {
            $score = $evaluation->scores->firstWhere('category', $category);
            $categories[] = [
                'label' => $category->label(),
                'weight' => $category->weight() . '%',
                'is_range' => $category->isRange(),
                'descriptions' => $category->ratingDescriptions(),
                'rating' => $score?->selected_rating->value ?? null,
                'rating_label' => $score?->selected_rating->label() ?? '-',
                'score' => $score ? (float) $score->numeric_score : 0,
            ];
        }

        // Get evaluator signature
        $signature = $evaluatorUser?->signatures()?->latest('signed_at')?->first();

        // Evaluator display name
        $evaluatorName = $evaluatorUser?->dosen?->nama_dosen
            ?? $evaluatorUser?->username ?? '-';

        return [
            'mahasiswa' => [
                'nama_lengkap' => $mahasiswa->nama_lengkap,
                'nim' => $mahasiswa->nim,
                'prodi' => $mahasiswa->prodi ?? '-',
            ],
            'industri' => [
                'nama_perusahaan' => $industri->nama_perusahaan,
                'alamat' => $industri->alamat ?? '-',
            ],
            'evaluation' => [
                'company_name' => $evaluation->company_name,
                'department' => $evaluation->department ?? '-',
                'position' => $evaluation->position ?? '-',
                'evaluation_date' => $evaluation->evaluation_date?->format('d F Y') ?? now()->format('d F Y'),
                'overall_score' => $evaluation->overall_score,
                'pass_status' => $evaluation->pass_status === 'pass' ? 'PASS' : 'FAIL',
                'comments' => $evaluation->comment?->comments ?? '-',
                'feedback' => $evaluation->comment?->feedback ?? '-',
            ],
            'categories' => $categories,
            'evaluator_name' => $evaluatorName,
            'signatureBase64' => $this->getImageBase64($signature?->signature_image_path),
            'tanggal_mulai' => $magangAktif->tanggal_mulai?->format('d F Y') ?? '-',
            'tanggal_selesai' => $magangAktif->tanggal_selesai?->format('d F Y') ?? '-',
        ];
    }

    /**
     * Generate the internship evaluation PDF.
     */
    public function generatePdf(int $magangAktifId)
    {
        $data = $this->getPdfData($magangAktifId);

        return Pdf::loadView('pdf.internship-evaluation', $data)
            ->setPaper('a4', 'landscape');
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

        return 'data:image/' . $type . ';base64,' . base64_encode($image);
    }
}
