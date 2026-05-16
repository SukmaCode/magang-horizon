<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use App\Enums\InternshipEvalCategory;
use App\Enums\InternshipEvalRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class InternshipEvaluation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'magang_aktif_id',
        'evaluator_id',
        'company_name',
        'department',
        'position',
        'evaluation_date',
        'overall_score',
        'pass_status',
        'status',
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
            ->logOnly(['status', 'overall_score', 'pass_status', 'evaluation_date'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Internship Evaluation #{$this->id} was {$eventName}");
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
        return $this->hasMany(InternshipEvaluationScore::class);
    }

    public function comment(): HasOne
    {
        return $this->hasOne(InternshipEvaluationComment::class);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Calculate the overall score by summing all category numeric scores.
     */
    public function calculateOverallScore(): float
    {
        return round($this->scores->sum('numeric_score'), 2);
    }

    /**
     * Determine pass/fail status.
     *
     * PASS conditions (both must be true):
     *   1. Overall score >= 50
     *   2. No category has a rating below NEARS EXPECTATIONS
     */
    public function determinePassStatus(): string
    {
        $overallScore = $this->calculateOverallScore();

        if ($overallScore < 50) {
            return 'fail';
        }

        // Check that no rating is below NEARS
        foreach ($this->scores as $score) {
            if ($score->selected_rating === InternshipEvalRating::BELOW) {
                return 'fail';
            }
        }

        return 'pass';
    }

    /**
     * Check if all 4 category scores have been filled.
     */
    public function isComplete(): bool
    {
        return $this->scores()->count() === count(InternshipEvalCategory::cases());
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
}
