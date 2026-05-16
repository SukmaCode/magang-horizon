<?php

namespace App\Services;

use App\Enums\PortfolioCategory;
use App\Models\MagangAktif;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PortfolioEvaluationPdfService
{
    /**
     * Get all data needed for the portfolio evaluation PDF.
     */
    public function getPdfData(int $magangAktifId): array
    {
        $magangAktif = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'portfolioEvaluation.scores',
            'portfolioEvaluation.evaluator.signatures',
            'portfolioEvaluation.evaluator.industri',
            'portfolioEvaluation.evaluator.dosen',
        ])->findOrFail($magangAktifId);

        $mahasiswa = $magangAktif->pendaftaran->mahasiswa;
        $industri = $magangAktif->pendaftaran->industri;
        $evaluation = $magangAktif->portfolioEvaluation;
        $evaluatorUser = $evaluation->evaluator;

        // Build structured scores for PDF
        $categories = [];
        foreach (PortfolioCategory::cases() as $category) {
            $categoryScores = $evaluation->scores->where('category', $category);
            $items = [];

            if ($category->hasSubCategories()) {
                foreach ($category->subCategories() as $subKey => $subLabel) {
                    $score = $categoryScores->firstWhere('sub_category', $subKey);
                    $items[] = [
                        'label' => $subLabel,
                        'rating' => $score?->selected_rating->label() ?? '-',
                        'score' => $score?->numeric_score ?? 0,
                    ];
                }
            } else {
                $score = $categoryScores->first();
                $items[] = [
                    'label' => null,
                    'rating' => $score?->selected_rating->label() ?? '-',
                    'score' => $score?->numeric_score ?? 0,
                ];
            }

            $categories[] = [
                'label' => $category->label(),
                'weight' => $category->weight() . '%',
                'items' => $items,
            ];
        }

        // Get evaluator signature
        $signature = $evaluatorUser?->signatures()?->latest('signed_at')?->first();

        // Evaluator display name
        $evaluatorName = $evaluatorUser?->dosen?->nama_dosen
            ?? $evaluatorUser?->industri?->kontak_person
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
                'qualification_result' => $evaluation->qualification_result === 'qualified' ? 'Qualified' : 'Not Qualified',
                'comments' => $evaluation->comments ?? '-',
                'evaluator_type' => $evaluation->evaluator_type === 'industry_supervisor'
                    ? 'Supervisor Industri' : 'Dosen Pembimbing',
            ],
            'categories' => $categories,
            'evaluator_name' => $evaluatorName,
            'signatureBase64' => $this->getImageBase64($signature?->signature_image_path),
            'tanggal_mulai' => $magangAktif->tanggal_mulai?->format('d F Y') ?? '-',
            'tanggal_selesai' => $magangAktif->tanggal_selesai?->format('d F Y') ?? '-',
        ];
    }

    /**
     * Generate the portfolio evaluation PDF.
     */
    public function generatePdf(int $magangAktifId)
    {
        $data = $this->getPdfData($magangAktifId);

        return Pdf::loadView('pdf.portfolio-evaluation', $data)
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

        return 'data:image/' . $type . ';base64,' . base64_encode($image);
    }
}
