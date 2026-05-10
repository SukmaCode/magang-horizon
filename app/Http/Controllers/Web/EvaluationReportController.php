<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Services\EvaluationPdfService;
use Illuminate\Http\Request;

class EvaluationReportController extends Controller
{
    public function __construct(
        private readonly EvaluationPdfService $pdfService,
    ) {}

    /**
     * Download the evaluation PDF.
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
        $evaluation = $magangAktif->internshipEvaluation;

        if (! $evaluation) {
            return back()->with('error', 'Evaluasi belum tersedia.');
        }

        if (! $evaluation->status->canDownload()) {
            return back()->with('error', 'Evaluasi belum difinalisasi. PDF hanya dapat diunduh setelah evaluasi selesai.');
        }

        try {
            $pdf = $this->pdfService->generatePdf($magangAktif->id);

            $filename = 'Lembar-Evaluasi-' . str_replace(' ', '-', $magangAktif->pendaftaran->mahasiswa->nama_lengkap) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
