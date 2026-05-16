<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InternshipEvaluation;
use App\Models\MagangAktif;
use App\Services\InternshipEvaluationPdfService;
use App\Services\InternshipEvaluationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternshipEvaluationController extends Controller
{
    public function __construct(
        private readonly InternshipEvaluationService $evaluationService,
        private readonly InternshipEvaluationPdfService $pdfService,
    ) {}

    // ──────────────────────────────────────
    // Dosen Pembimbing: List
    // ──────────────────────────────────────

    public function dosenIndex(Request $request)
    {
        $magangs = $this->evaluationService->getEvaluableMagangs($request->user());

        return Inertia::render('DosenPembimbing/InternshipEvaluation', [
            'magangs' => $magangs,
        ]);
    }

    // ──────────────────────────────────────
    // Create/Edit Form
    // ──────────────────────────────────────

    public function create(Request $request, MagangAktif $magangAktif)
    {
        if ($request->user()->cannot('create', [InternshipEvaluation::class, $magangAktif])) {
            abort(403, 'Anda tidak memiliki akses untuk mengevaluasi mahasiswa ini.');
        }

        $data = $this->evaluationService->getFormData($magangAktif, $request->user());

        return Inertia::render('DosenPembimbing/FormInternshipEvaluation', $data);
    }

    // ──────────────────────────────────────
    // Store/Update
    // ──────────────────────────────────────

    public function store(Request $request, MagangAktif $magangAktif)
    {
        if ($request->user()->cannot('create', [InternshipEvaluation::class, $magangAktif])) {
            abort(403, 'Anda tidak memiliki akses untuk mengevaluasi mahasiswa ini.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'evaluation_date' => 'nullable|date',
            'comments' => 'nullable|string|max:5000',
            'feedback' => 'nullable|string|max:5000',
            'scores' => 'required|array',
            'scores.*.rating' => 'required|string|in:exceptional,exceeds,meets,nears,below',
            'scores.*.score' => 'nullable|numeric|min:0|max:100',
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'scores.*.rating.required' => 'Semua kriteria penilaian wajib diisi.',
            'scores.*.rating.in' => 'Rating tidak valid.',
        ]);

        try {
            $this->evaluationService->saveEvaluation(
                $magangAktif,
                $request->user(),
                $validated
            );

            return redirect('/dosen-pembimbing/internship-evaluation')
                ->with('success', 'Internship evaluation berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Submit (draft → submitted)
    // ──────────────────────────────────────

    public function submit(Request $request, InternshipEvaluation $evaluation)
    {
        if ($request->user()->cannot('update', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        try {
            $this->evaluationService->submitEvaluation($evaluation);

            return back()->with('success', 'Internship evaluation berhasil disubmit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Finalize (submitted → finalized)
    // ──────────────────────────────────────

    public function finalize(Request $request, InternshipEvaluation $evaluation)
    {
        if ($request->user()->cannot('update', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        try {
            $this->evaluationService->finalizeEvaluation($evaluation);

            return back()->with('success', 'Internship evaluation berhasil difinalisasi. Mahasiswa sekarang dapat melihat dan mendownload hasil evaluasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Mahasiswa: View Results
    // ──────────────────────────────────────

    public function studentShow(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $data = $mahasiswa
            ? $this->evaluationService->getStudentEvaluation($mahasiswa)
            : null;

        return Inertia::render('Mahasiswa/HasilInternshipEvaluation', [
            'evaluationData' => $data,
        ]);
    }

    // ──────────────────────────────────────
    // Download PDF
    // ──────────────────────────────────────

    public function downloadPdf(Request $request, MagangAktif $magangAktif)
    {
        $evaluation = $magangAktif->internshipEvaluation;

        if (! $evaluation) {
            return back()->with('error', 'Internship evaluation belum tersedia.');
        }

        if ($request->user()->cannot('download', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses untuk mendownload PDF ini.');
        }

        try {
            $pdf = $this->pdfService->generatePdf($magangAktif->id);
            $filename = 'Internship-Evaluation-' . str_replace(' ', '-', $magangAktif->pendaftaran->mahasiswa->nama_lengkap) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
