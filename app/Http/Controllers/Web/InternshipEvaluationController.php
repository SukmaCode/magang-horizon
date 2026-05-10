<?php

namespace App\Http\Controllers\Web;

use App\Enums\StatusSeleksi;
use App\Http\Controllers\Controller;
use App\Models\EvaluationScore;
use App\Models\InternshipEvaluation;
use App\Models\MagangAktif;
use App\Services\InternshipEvaluationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InternshipEvaluationController extends Controller
{
    public function __construct(
        private readonly InternshipEvaluationService $evaluationService,
    ) {}

    // ──────────────────────────────────────
    // Supervisor Industri: List Evaluations
    // ──────────────────────────────────────

    public function index(Request $request)
    {
        $user = $request->user();
        $magangs = $this->evaluationService->getEvaluableMagangs($user);

        return Inertia::render('Industri/EvaluasiMahasiswa', [
            'magangs' => $magangs,
            'components' => EvaluationScore::COMPONENTS,
        ]);
    }

    // ──────────────────────────────────────
    // Supervisor Industri: Create/Edit Form
    // ──────────────────────────────────────

    public function create(Request $request, MagangAktif $magangAktif)
    {
        $this->authorizeSupervisor($request, $magangAktif);

        $data = $this->evaluationService->getEvaluationFormData($magangAktif);

        return Inertia::render('Industri/FormEvaluasi', array_merge($data, [
            'components' => EvaluationScore::COMPONENTS,
        ]));
    }

    // ──────────────────────────────────────
    // Supervisor Industri: Store/Update
    // ──────────────────────────────────────

    public function store(Request $request, MagangAktif $magangAktif)
    {
        $this->authorizeSupervisor($request, $magangAktif);

        $componentKeys = array_keys(EvaluationScore::COMPONENTS);

        $rules = [
            'catatan_supervisor' => 'nullable|string|max:5000',
            'tanggal_evaluasi' => 'nullable|date',
        ];

        // Dynamic validation for each component score
        foreach ($componentKeys as $key) {
            $rules["scores.{$key}"] = 'required|numeric|min:0|max:100';
        }

        $validated = $request->validate($rules, [
            'scores.*.required' => 'Semua komponen penilaian wajib diisi.',
            'scores.*.min' => 'Nilai minimal 0.',
            'scores.*.max' => 'Nilai maksimal 100.',
        ]);

        try {
            $this->evaluationService->saveEvaluation(
                $magangAktif,
                $request->user(),
                $validated
            );

            return redirect()
                ->route('industri.evaluasi')
                ->with('success', 'Evaluasi berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Supervisor Industri: Submit
    // ──────────────────────────────────────

    public function submit(Request $request, InternshipEvaluation $evaluation)
    {
        $this->authorizeEvaluation($request, $evaluation);

        try {
            $this->evaluationService->submitEvaluation($evaluation);

            return back()->with('success', 'Evaluasi berhasil disubmit.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Supervisor Industri: Finalize
    // ──────────────────────────────────────

    public function finalize(Request $request, InternshipEvaluation $evaluation)
    {
        $this->authorizeEvaluation($request, $evaluation);

        try {
            $this->evaluationService->finalizeEvaluation($evaluation);

            return back()->with('success', 'Evaluasi berhasil difinalisasi. Mahasiswa sekarang dapat melihat dan mendownload hasil evaluasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ──────────────────────────────────────
    // Mahasiswa: View Evaluation
    // ──────────────────────────────────────

    public function show(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $data = $mahasiswa
            ? $this->evaluationService->getStudentEvaluation($mahasiswa)
            : null;

        return Inertia::render('Mahasiswa/HasilEvaluasi', [
            'evaluationData' => $data,
        ]);
    }

    // ──────────────────────────────────────
    // Authorization Helpers
    // ──────────────────────────────────────

    /**
     * Ensure the current supervisor owns the magang's industri.
     */
    private function authorizeSupervisor(Request $request, MagangAktif $magangAktif): void
    {
        $industri = $request->user()->industri;

        if (! $industri) {
            abort(403, 'Unauthorized — no industri profile.');
        }

        $pendaftaran = $magangAktif->pendaftaran;

        if (! $pendaftaran || $pendaftaran->industri_id !== $industri->id) {
            abort(403, 'Unauthorized — magang bukan milik industri Anda.');
        }
    }

    /**
     * Ensure the current supervisor owns the evaluation.
     */
    private function authorizeEvaluation(Request $request, InternshipEvaluation $evaluation): void
    {
        if ($evaluation->supervisor_id !== $request->user()->id) {
            abort(403, 'Unauthorized — evaluasi bukan milik Anda.');
        }
    }
}
