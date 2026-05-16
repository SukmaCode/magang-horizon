<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Services\PerformanceEvaluationPdfService;
use Illuminate\Http\Request;

class PerformanceEvaluationReportController extends Controller
{
    public function __construct(
        private readonly PerformanceEvaluationPdfService $pdfService,
    ) {}

    /**
     * Download the performance evaluation PDF.
     * Only accessible for finalized evaluations by the owning student.
     */
    public function download(Request $request, MagangAktif $magangAktif)
    {
        $user = $request->user();

        // Authorization: mahasiswa can only download their own evaluation
        $mahasiswa = $user->mahasiswa;
        if ($mahasiswa) {
            $pendaftaran = $magangAktif->pendaftaran;
            if (! $pendaftaran || $pendaftaran->mahasiswa_id !== $mahasiswa->id) {
                abort(403, 'Anda tidak memiliki akses ke evaluasi ini.');
            }
        }

        // Check evaluation exists and is finalized
        $evaluation = $magangAktif->performanceEvaluation;

        if (! $evaluation) {
            return back()->with('error', 'Evaluasi belum tersedia.');
        }

        if (! $evaluation->status->canDownload()) {
            return back()->with('error', 'Evaluasi belum difinalisasi. PDF hanya dapat diunduh setelah evaluasi selesai.');
        }

        try {
            $pdf = $this->pdfService->generatePdf($magangAktif->id);

            $filename = 'Performance-Evaluation-' . str_replace(' ', '-', $magangAktif->pendaftaran->mahasiswa->nama_lengkap) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
