<?php

namespace App\Services;

use App\Enums\EvaluationStatus;
use App\Enums\PortfolioCategory;
use App\Enums\PortfolioRating;
use App\Enums\StatusSeleksi;
use App\Enums\UserRole;
use App\Models\MagangAktif;
use App\Models\Mahasiswa;
use App\Models\PortfolioEvaluation;
use App\Models\PortfolioEvaluationScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PortfolioEvaluationService
{
    /**
     * Get all magangs that an evaluator can evaluate.
     */
    public function getEvaluableMagangs(User $evaluator): array
    {
        $query = MagangAktif::with([
            'pendaftaran.mahasiswa',
            'pendaftaran.industri',
            'portfolioEvaluation',
        ]);

        if ($evaluator->role === UserRole::INDUSTRY) {
            $industri = $evaluator->industri;
            if (! $industri) {
                return [];
            }
            $query->whereHas('pendaftaran', function ($q) use ($industri) {
                $q->where('industri_id', $industri->id)
                  ->where('status_seleksi', StatusSeleksi::DITERIMA);
            });
        } elseif ($evaluator->role === UserRole::SUPERVISOR_1) {
            $dosen = $evaluator->dosen;
            if (! $dosen) {
                return [];
            }
            $query->where('supervisor_kampus_id', $dosen->id);
        } else {
            return [];
        }

        return $query->get()
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
                'evaluation' => $m->portfolioEvaluation ? [
                    'id' => $m->portfolioEvaluation->id,
                    'status' => $m->portfolioEvaluation->status->value,
                    'status_label' => $m->portfolioEvaluation->status->label(),
                    'overall_score' => $m->portfolioEvaluation->overall_score,
                    'qualification_result' => $m->portfolioEvaluation->qualification_result,
                    'updated_at' => $m->portfolioEvaluation->updated_at->format('d M Y H:i'),
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
            'portfolioEvaluation.scores',
        ]);

        $evaluation = $magang->portfolioEvaluation;

        // Build scores map
        $scoresMap = [];
        if ($evaluation) {
            foreach ($evaluation->scores as $score) {
                $key = $score->sub_category
                    ? "{$score->category->value}.{$score->sub_category}"
                    : $score->category->value;
                $scoresMap[$key] = $score->selected_rating->value;
            }
        }

        // Determine evaluator type
        $evaluatorType = $evaluator->role === UserRole::INDUSTRY
            ? 'industry_supervisor'
            : 'university_mentor';

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
                'qualification_result' => $evaluation->qualification_result,
                'comments' => $evaluation->comments,
                'can_edit' => $evaluation->canBeEdited(),
            ] : null,
            'scores' => $scoresMap,
            'evaluator_type' => $evaluatorType,
            'rubric_config' => PortfolioCategory::rubricConfig(),
            'contents_options' => PortfolioRating::contentsOptions(),
            'secondary_options' => PortfolioRating::secondaryOptions(),
        ];
    }

    /**
     * Save or update evaluation with scores.
     */
    public function saveEvaluation(MagangAktif $magang, User $evaluator, array $data): PortfolioEvaluation
    {
        return DB::transaction(function () use ($magang, $evaluator, $data) {
            $evaluatorType = $evaluator->role === UserRole::INDUSTRY
                ? 'industry_supervisor'
                : 'university_mentor';

            $evaluation = PortfolioEvaluation::updateOrCreate(
                ['magang_aktif_id' => $magang->id],
                [
                    'evaluator_id' => $evaluator->id,
                    'evaluator_type' => $evaluatorType,
                    'company_name' => $data['company_name'],
                    'department' => $data['department'] ?? null,
                    'position' => $data['position'] ?? null,
                    'evaluation_date' => $data['evaluation_date'] ?? now()->toDateString(),
                    'comments' => $data['comments'] ?? null,
                ]
            );

            if ($evaluation->isFinalized()) {
                throw new \Exception('Evaluasi yang sudah difinalisasi tidak dapat diubah.');
            }

            // Upsert scores
            $this->upsertScores($evaluation, $data['scores'] ?? []);

            // Recalculate
            $evaluation->refresh();
            $evaluation->update([
                'overall_score' => $evaluation->calculateOverallScore(),
                'qualification_result' => $evaluation->determineQualification(),
            ]);

            activity('portfolio-evaluation')
                ->performedOn($evaluation)
                ->causedBy($evaluator)
                ->withProperties(['status' => $evaluation->status->value])
                ->log('Portfolio evaluation saved as draft');

            return $evaluation->fresh(['scores']);
        });
    }

    /**
     * Submit evaluation (draft → submitted).
     */
    public function submitEvaluation(PortfolioEvaluation $evaluation): PortfolioEvaluation
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
            'qualification_result' => $evaluation->determineQualification(),
        ]);

        activity('portfolio-evaluation')
            ->performedOn($evaluation)
            ->log('Portfolio evaluation submitted');

        return $evaluation->fresh();
    }

    /**
     * Finalize evaluation (submitted → finalized).
     */
    public function finalizeEvaluation(PortfolioEvaluation $evaluation): PortfolioEvaluation
    {
        if (! $evaluation->status->canTransitionTo(EvaluationStatus::FINALIZED)) {
            throw new \Exception("Tidak dapat mengubah status dari {$evaluation->status->label()} ke Finalized.");
        }

        if (! $evaluation->isComplete()) {
            throw new \Exception('Semua kriteria penilaian harus diisi sebelum finalisasi.');
        }

        $evaluation->update([
            'status' => EvaluationStatus::FINALIZED,
            'overall_score' => $evaluation->calculateOverallScore(),
            'qualification_result' => $evaluation->determineQualification(),
            'finalized_at' => now(),
        ]);

        activity('portfolio-evaluation')
            ->performedOn($evaluation)
            ->withProperties([
                'overall_score' => $evaluation->overall_score,
                'qualification_result' => $evaluation->qualification_result,
            ])
            ->log('Portfolio evaluation finalized');

        return $evaluation->fresh(['scores']);
    }

    /**
     * Get evaluation data for student view.
     */
    public function getStudentEvaluation(Mahasiswa $mahasiswa): ?array
    {
        $magang = $mahasiswa->active_magang;
        $magang?->load(['portfolioEvaluation.scores', 'portfolioEvaluation.evaluator']);
        $evaluation = $magang?->portfolioEvaluation;

        if (! $evaluation) {
            return null;
        }

        // Build structured scores
        $structuredScores = [];
        foreach (PortfolioCategory::cases() as $category) {
            $categoryScores = $evaluation->scores->where('category', $category);
            $items = [];

            if ($category->hasSubCategories()) {
                foreach ($category->subCategories() as $subKey => $subLabel) {
                    $score = $categoryScores->firstWhere('sub_category', $subKey);
                    $items[] = [
                        'sub_category' => $subKey,
                        'sub_category_label' => $subLabel,
                        'rating' => $score?->selected_rating->value,
                        'rating_label' => $score?->selected_rating->label(),
                        'score' => $score?->numeric_score ?? 0,
                    ];
                }
            } else {
                $score = $categoryScores->first();
                $items[] = [
                    'sub_category' => null,
                    'sub_category_label' => null,
                    'rating' => $score?->selected_rating->value,
                    'rating_label' => $score?->selected_rating->label(),
                    'score' => $score?->numeric_score ?? 0,
                ];
            }

            $structuredScores[] = [
                'category' => $category->value,
                'category_label' => $category->label(),
                'weight' => $category->weight(),
                'items' => $items,
            ];
        }

        return [
            'evaluation' => [
                'id' => $evaluation->id,
                'status' => $evaluation->status->value,
                'status_label' => $evaluation->status->label(),
                'evaluator_type' => $evaluation->evaluator_type,
                'evaluator_name' => $evaluation->evaluator?->dosen?->nama_dosen
                    ?? $evaluation->evaluator?->industri?->kontak_person
                    ?? $evaluation->evaluator?->username ?? '-',
                'company_name' => $evaluation->company_name,
                'department' => $evaluation->department,
                'position' => $evaluation->position,
                'evaluation_date' => $evaluation->evaluation_date?->format('d M Y'),
                'overall_score' => $evaluation->overall_score,
                'qualification_result' => $evaluation->qualification_result,
                'comments' => $evaluation->comments,
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
    private function upsertScores(PortfolioEvaluation $evaluation, array $scores): void
    {
        foreach (PortfolioCategory::cases() as $category) {
            if ($category->hasSubCategories()) {
                foreach ($category->subCategories() as $subKey => $subLabel) {
                    $ratingKey = "{$category->value}.{$subKey}";
                    if (! isset($scores[$ratingKey])) {
                        continue;
                    }

                    $rating = PortfolioRating::from($scores[$ratingKey]);
                    $numericScore = PortfolioEvaluationScore::computeScore($category, $rating);

                    PortfolioEvaluationScore::updateOrCreate(
                        [
                            'portfolio_evaluation_id' => $evaluation->id,
                            'category' => $category->value,
                            'sub_category' => $subKey,
                        ],
                        [
                            'selected_rating' => $rating->value,
                            'numeric_score' => $numericScore,
                        ]
                    );
                }
            } else {
                $ratingKey = $category->value;
                if (! isset($scores[$ratingKey])) {
                    continue;
                }

                $rating = PortfolioRating::from($scores[$ratingKey]);
                $numericScore = PortfolioEvaluationScore::computeScore($category, $rating);

                PortfolioEvaluationScore::updateOrCreate(
                    [
                        'portfolio_evaluation_id' => $evaluation->id,
                        'category' => $category->value,
                        'sub_category' => null,
                    ],
                    [
                        'selected_rating' => $rating->value,
                        'numeric_score' => $numericScore,
                    ]
                );
            }
        }
    }
}
