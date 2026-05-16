<?php

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Enums\InternshipEvalCategory;
use App\Enums\InternshipEvalRating;
use App\Enums\UserRole;
use App\Models\InternshipEvaluation;
use App\Models\InternshipEvaluationComment;
use App\Models\InternshipEvaluationScore;
use App\Models\MagangAktif;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InternshipEvaluationService
{
    public function __construct(
        private readonly GradingService $gradingService,
    ) {}
    /**
     * Get all magangs that a dosen pembimbing can evaluate.
     */
    public function getEvaluableMagangs(User $evaluator): array
    {
        $dosen = $evaluator->dosen;

        if (! $dosen) {
            return [];
        }

        return MagangAktif::where('supervisor_kampus_id', $dosen->id)
            ->with([
                'pendaftaran.mahasiswa',
                'pendaftaran.industri',
                'internshipEvaluation',
            ])
            ->get()
            ->map(fn (MagangAktif $m) => [
                'id' => $m->id,
                'mahasiswa' => [
                    'nama_lengkap' => $m->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $m->pendaftaran->mahasiswa->nim,
                    'prodi' => $m->pendaftaran->mahasiswa->prodi,
                ],
                'industri' => [
                    'nama_perusahaan' => $m->pendaftaran->industri->nama_perusahaan,
                ],
                'status_tahapan' => $m->status_tahapan->value,
                'status_tahapan_label' => $m->status_tahapan->label(),
                'evaluation' => $m->internshipEvaluation ? [
                    'id' => $m->internshipEvaluation->id,
                    'status' => $m->internshipEvaluation->status->value,
                    'status_label' => $m->internshipEvaluation->status->label(),
                    'overall_score' => $m->internshipEvaluation->overall_score,
                    'pass_status' => $m->internshipEvaluation->pass_status,
                    'updated_at' => $m->internshipEvaluation->updated_at?->format('d M Y H:i') ?? '-',
                ] : null,
            ])
            ->values()
            ->toArray();
    }

    /**
     * Get form data for create/edit.
     */
    public function getFormData(MagangAktif $magang, User $evaluator): array
    {
        $magang->load([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'internshipEvaluation.scores',
            'internshipEvaluation.comment',
        ]);

        $evaluation = $magang->internshipEvaluation;

        // Build scores map: category => { rating, score }
        $scoresMap = [];
        if ($evaluation) {
            foreach ($evaluation->scores as $score) {
                $scoresMap[$score->category->value] = [
                    'rating' => $score->selected_rating->value,
                    'score' => (float) $score->numeric_score,
                ];
            }
        }

        return [
            'magang' => [
                'id' => $magang->id,
                'mahasiswa' => [
                    'nama_lengkap' => $magang->pendaftaran->mahasiswa->nama_lengkap,
                    'nim' => $magang->pendaftaran->mahasiswa->nim,
                    'prodi' => $magang->pendaftaran->mahasiswa->prodi,
                ],
                'industri' => [
                    'nama_perusahaan' => $magang->pendaftaran->industri->nama_perusahaan,
                ],
                'tanggal_mulai' => $magang->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $magang->tanggal_selesai?->format('d M Y'),
            ],
            'evaluation' => $evaluation ? [
                'id' => $evaluation->id,
                'status' => $evaluation->status->value,
                'status_label' => $evaluation->status->label(),
                'company_name' => $evaluation->company_name,
                'department' => $evaluation->department,
                'position' => $evaluation->position,
                'evaluation_date' => $evaluation->evaluation_date?->format('Y-m-d'),
                'overall_score' => $evaluation->overall_score,
                'pass_status' => $evaluation->pass_status,
                'comments' => $evaluation->comment?->comments,
                'feedback' => $evaluation->comment?->feedback,
                'can_edit' => $evaluation->canBeEdited(),
            ] : null,
            'scores' => $scoresMap,
            'rubric_config' => InternshipEvalCategory::rubricConfig(),
            'performance_options' => InternshipEvalRating::performanceOptions(),
            'fixed_options' => InternshipEvalRating::fixedOptions(),
            'portfolio_options' => InternshipEvalRating::portfolioOptions(),
        ];
    }

    /**
     * Save or update evaluation with scores.
     */
    public function saveEvaluation(MagangAktif $magang, User $evaluator, array $data): InternshipEvaluation
    {
        return DB::transaction(function () use ($magang, $evaluator, $data) {
            $evaluation = InternshipEvaluation::updateOrCreate(
                ['magang_aktif_id' => $magang->id],
                [
                    'evaluator_id' => $evaluator->id,
                    'company_name' => $data['company_name'],
                    'department' => $data['department'] ?? null,
                    'position' => $data['position'] ?? null,
                    'evaluation_date' => $data['evaluation_date'] ?? now()->toDateString(),
                ]
            );

            if ($evaluation->isFinalized()) {
                throw new \Exception('Evaluasi yang sudah difinalisasi tidak dapat diubah.');
            }

            // Upsert scores
            $this->upsertScores($evaluation, $data['scores'] ?? []);

            // Upsert comments
            InternshipEvaluationComment::updateOrCreate(
                ['internship_evaluation_id' => $evaluation->id],
                [
                    'comments' => $data['comments'] ?? null,
                    'feedback' => $data['feedback'] ?? null,
                ]
            );

            // Recalculate
            $evaluation->refresh();
            $evaluation->update([
                'overall_score' => $evaluation->calculateOverallScore(),
                'pass_status' => $evaluation->determinePassStatus(),
            ]);

            activity('internship-evaluation')
                ->performedOn($evaluation)
                ->causedBy($evaluator)
                ->withProperties(['status' => $evaluation->status->value])
                ->log('Internship evaluation saved as draft');

            return $evaluation->fresh(['scores', 'comment']);
        });
    }

    /**
     * Submit evaluation (draft → submitted).
     */
    public function submitEvaluation(InternshipEvaluation $evaluation): InternshipEvaluation
    {
        if (! $evaluation->status->canTransitionTo(EvaluationStatus::SUBMITTED)) {
            throw new \Exception("Tidak dapat mengubah status dari {$evaluation->status->label()} ke Submitted.");
        }

        if (! $evaluation->isComplete()) {
            throw new \Exception('Semua kriteria penilaian harus diisi sebelum submit.');
        }

        $evaluation->update([
            'status' => EvaluationStatus::SUBMITTED,
            'overall_score' => $evaluation->calculateOverallScore(),
            'pass_status' => $evaluation->determinePassStatus(),
        ]);

        activity('internship-evaluation')
            ->performedOn($evaluation)
            ->log('Internship evaluation submitted');

        return $evaluation->fresh();
    }

    /**
     * Finalize evaluation (submitted → finalized).
     */
    public function finalizeEvaluation(InternshipEvaluation $evaluation): InternshipEvaluation
    {
        if (! $evaluation->status->canTransitionTo(EvaluationStatus::FINALIZED)) {
            throw new \Exception("Tidak dapat mengubah status dari {$evaluation->status->label()} ke Finalized.");
        }

        if (! $evaluation->isComplete()) {
            throw new \Exception('Semua kriteria penilaian harus diisi sebelum finalisasi.');
        }

        return DB::transaction(function () use ($evaluation) {
            $overallScore = $evaluation->calculateOverallScore();

            $evaluation->update([
                'status' => EvaluationStatus::FINALIZED,
                'overall_score' => $overallScore,
                'pass_status' => $evaluation->determinePassStatus(),
                'finalized_at' => now(),
            ]);

            // ✅ Sync to legacy penilaians table for graduation requirement
            $this->gradingService->gradeByCampus($evaluation->magangAktif, $evaluation->id);

            activity('internship-evaluation')
                ->performedOn($evaluation)
                ->withProperties([
                    'overall_score' => $evaluation->overall_score,
                    'pass_status' => $evaluation->pass_status,
                ])
                ->log('Internship evaluation finalized — score synced to penilaians');

            return $evaluation->fresh(['scores', 'comment']);
        });
    }

    /**
     * Get evaluation data for student view.
     */
    public function getStudentEvaluation(Mahasiswa $mahasiswa): ?array
    {
        $magang = $mahasiswa->active_magang;
        $magang?->load(['internshipEvaluation.scores', 'internshipEvaluation.comment', 'internshipEvaluation.evaluator']);
        $evaluation = $magang?->internshipEvaluation;

        if (! $evaluation) {
            return null;
        }

        // Build structured scores
        $structuredScores = [];
        foreach (InternshipEvalCategory::cases() as $category) {
            $score = $evaluation->scores->firstWhere('category', $category);
            $structuredScores[] = [
                'category' => $category->value,
                'category_label' => $category->label(),
                'weight' => $category->weight(),
                'is_range' => $category->isRange(),
                'rating' => $score?->selected_rating->value,
                'rating_label' => $score?->selected_rating->label(),
                'score' => $score ? (float) $score->numeric_score : 0,
            ];
        }

        // Evaluator name
        $evaluatorUser = $evaluation->evaluator;
        $evaluatorName = $evaluatorUser?->dosen?->nama_dosen
            ?? $evaluatorUser?->username ?? '-';

        return [
            'evaluation' => [
                'id' => $evaluation->id,
                'status' => $evaluation->status->value,
                'status_label' => $evaluation->status->label(),
                'evaluator_name' => $evaluatorName,
                'company_name' => $evaluation->company_name,
                'department' => $evaluation->department,
                'position' => $evaluation->position,
                'evaluation_date' => $evaluation->evaluation_date?->format('d M Y'),
                'overall_score' => $evaluation->overall_score,
                'pass_status' => $evaluation->pass_status,
                'comments' => $evaluation->comment?->comments,
                'feedback' => $evaluation->comment?->feedback,
                'finalized_at' => $evaluation->finalized_at?->format('d M Y H:i'),
                'can_download' => $evaluation->status->canDownload(),
            ],
            'scores' => $structuredScores,
            'magang' => [
                'id' => $magang->id,
                'tanggal_mulai' => $magang->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $magang->tanggal_selesai?->format('d M Y'),
            ],
        ];
    }

    /**
     * Upsert scores from form data.
     */
    private function upsertScores(InternshipEvaluation $evaluation, array $scores): void
    {
        foreach (InternshipEvalCategory::cases() as $category) {
            $key = $category->value;
            if (! isset($scores[$key])) {
                continue;
            }

            $scoreData = $scores[$key];
            $rating = InternshipEvalRating::from($scoreData['rating']);
            $explicitScore = isset($scoreData['score']) ? (float) $scoreData['score'] : null;

            $numericScore = InternshipEvaluationScore::computeScore($category, $rating, $explicitScore);

            InternshipEvaluationScore::updateOrCreate(
                [
                    'internship_evaluation_id' => $evaluation->id,
                    'category' => $category->value,
                ],
                [
                    'selected_rating' => $rating->value,
                    'numeric_score' => $numericScore,
                ]
            );
        }
    }
}
