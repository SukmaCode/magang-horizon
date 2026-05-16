<?php

namespace App\Http\Controllers\Web;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MagangAktif;
use App\Models\PortfolioEvaluation;
use App\Services\PortfolioEvaluationPdfService;
use App\Services\PortfolioEvaluationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortfolioEvaluationController extends Controller
{
    public function __construct(
        private readonly PortfolioEvaluationService $evaluationService,
        private readonly PortfolioEvaluationPdfService $pdfService,
    ) {}

    // ──────────────────────────────────────
    // Industri: List
    // ──────────────────────────────────────

    public function industryIndex(Request $request)
    {
        $magangs = $this->evaluationService->getEvaluableMagangs($request->user());
        $statusTahapan = \App\Enums\StatusTahapan::PELAKSANAAN->value;

        return Inertia::render('Industri/PortfolioEvaluation', [
            'magangs' => $magangs,
            'statusTahapan' => $statusTahapan,
        ]);
    }

    // ──────────────────────────────────────
    // Dosen Pembimbing: List
    // ──────────────────────────────────────

    public function dosenIndex(Request $request)
    {
        $magangs = $this->evaluationService->getEvaluableMagangs($request->user());
        $statusTahapan = \App\Enums\StatusTahapan::PELAKSANAAN->value;

        return Inertia::render('DosenPembimbing/PortfolioEvaluation', [
            'magangs' => $magangs,
            'statusTahapan' => $statusTahapan,
        ]);
    }

    // ──────────────────────────────────────
    // Create/Edit Form (shared)
    // ──────────────────────────────────────

    public function create(Request $request, MagangAktif $magangAktif)
    {
        if ($request->user()->cannot('create', [PortfolioEvaluation::class, $magangAktif])) {
            abort(403, 'Anda tidak memiliki akses untuk mengevaluasi mahasiswa ini.');
        }

        $data = $this->evaluationService->getFormData($magangAktif, $request->user());

        $page = match ($request->user()->role) {
            UserRole::INDUSTRY => 'Industri/FormPortfolioEvaluation',
            UserRole::SUPERVISOR_1 => 'DosenPembimbing/FormPortfolioEvaluation',
            default => abort(403),
        };

        return Inertia::render($page, $data);
    }

    // ──────────────────────────────────────
    // Store/Update
    // ──────────────────────────────────────

    public function store(Request $request, MagangAktif $magangAktif)
    {
        if ($request->user()->cannot('create', [PortfolioEvaluation::class, $magangAktif])) {
            abort(403, 'Anda tidak memiliki akses untuk mengevaluasi mahasiswa ini.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'evaluation_date' => 'nullable|date',
            'comments' => 'nullable|string|max:5000',
            'scores' => 'required|array',
            'scores.*' => 'required|string|in:exceptional,exceeds,meets,nears,below,none',
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'scores.*.required' => 'Semua kriteria penilaian wajib diisi.',
            'scores.*.in' => 'Rating tidak valid.',
        ]);

        try {
            $this->evaluationService->saveEvaluation(
                $magangAktif,
                $request->user(),
                $validated
            );

            $backRoute = match ($request->user()->role) {
                UserRole::INDUSTRY => '/industri/portfolio-evaluation',
                UserRole::SUPERVISOR_1 => '/dosen-pembimbing/portfolio-evaluation',
                default => '/',
            };

            return redirect($backRoute)->with('success', 'Portfolio evaluation berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Submit (draft → submitted)
    // ──────────────────────────────────────

    public function submit(Request $request, PortfolioEvaluation $evaluation)
    {
        if ($request->user()->cannot('update', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        try {
            $this->evaluationService->submitEvaluation($evaluation);

            return back()->with('success', 'Portfolio evaluation berhasil disubmit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Finalize (submitted → finalized)
    // ──────────────────────────────────────

    public function finalize(Request $request, PortfolioEvaluation $evaluation)
    {
        if ($request->user()->cannot('update', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        try {
            $this->evaluationService->finalizeEvaluation($evaluation);

            return back()->with('success', 'Portfolio evaluation berhasil difinalisasi. Mahasiswa sekarang dapat melihat dan mendownload hasil evaluasi.');
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

        return Inertia::render('Mahasiswa/HasilPortfolioEvaluation', [
            'evaluationData' => $data,
        ]);
    }

    // ──────────────────────────────────────
    // Download PDF
    // ──────────────────────────────────────

    public function downloadPdf(Request $request, MagangAktif $magangAktif)
    {
        $evaluation = $magangAktif->portfolioEvaluation;

        if (! $evaluation) {
            return back()->with('error', 'Portfolio evaluation belum tersedia.');
        }

        if ($request->user()->cannot('download', $evaluation)) {
            abort(403, 'Anda tidak memiliki akses untuk mendownload PDF ini.');
        }

        try {
            $pdf = $this->pdfService->generatePdf($magangAktif->id);
            $filename = 'Portfolio-Evaluation-' . str_replace(' ', '-', $magangAktif->pendaftaran->mahasiswa->nama_lengkap) . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
