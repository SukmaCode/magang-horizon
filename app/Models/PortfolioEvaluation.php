<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use App\Enums\PortfolioCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PortfolioEvaluation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'magang_aktif_id',
        'evaluator_id',
        'evaluator_type',
        'company_name',
        'department',
        'position',
        'evaluation_date',
        'overall_score',
        'qualification_result',
        'status',
        'comments',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
            'overall_score' => 'decimal:2',
            'evaluation_date' => 'date',
            'finalized_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'overall_score', 'qualification_result', 'comments'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Portfolio Evaluation #{$this->id} was {$eventName}");
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(PortfolioEvaluationScore::class);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Calculate the overall score from all category scores.
     * Portfolio Contents sub-categories are averaged then weighted.
     */
    public function calculateOverallScore(): float
    {
        $total = 0;

        foreach (PortfolioCategory::cases() as $category) {
            $categoryScores = $this->scores->where('category', $category->value);

            if ($category->hasSubCategories()) {
                // Average the sub-category scores
                $avg = $categoryScores->avg('numeric_score') ?? 0;
                $total += $avg;
            } else {
                $score = $categoryScores->first();
                $total += $score?->numeric_score ?? 0;
            }
        }

        return round($total, 2);
    }

    /**
     * Determine qualification result.
     * Must obtain at least Nears Expectations in all three criteria.
     */
    public function determineQualification(): string
    {
        foreach (PortfolioCategory::cases() as $category) {
            $minScore = $category->nearExpectationsMinScore();
            $categoryScores = $this->scores->where('category', $category->value);

            if ($category->hasSubCategories()) {
                // For portfolio contents: each sub-category must meet minimum
                foreach ($categoryScores as $score) {
                    if ($score->numeric_score < $minScore) {
                        return 'not_qualified';
                    }
                }
                // Also check if all sub-categories exist
                if ($categoryScores->count() < count($category->subCategories())) {
                    return 'not_qualified';
                }
            } else {
                $score = $categoryScores->first();
                if (! $score || $score->numeric_score < $minScore) {
                    return 'not_qualified';
                }
            }
        }

        return 'qualified';
    }

    /**
     * Check if all required scores have been filled.
     * Portfolio Contents: 4 sub-categories + Format: 1 + Academic: 1 = 6 total scores.
     */
    public function isComplete(): bool
    {
        return $this->scores()->count() === 6;
    }

    public function isFinalized(): bool
    {
        return $this->status === EvaluationStatus::FINALIZED;
    }

    public function isDraft(): bool
    {
        return $this->status === EvaluationStatus::DRAFT;
    }

    public function canBeEdited(): bool
    {
        return $this->status->canEdit();
    }

    public function isEvaluatedByIndustry(): bool
    {
        return $this->evaluator_type === 'industry_supervisor';
    }

    public function isEvaluatedByDosen(): bool
    {
        return $this->evaluator_type === 'university_mentor';
    }
}
