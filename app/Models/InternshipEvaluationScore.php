<?php

namespace App\Models;

use App\Enums\InternshipEvalCategory;
use App\Enums\InternshipEvalRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipEvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_evaluation_id',
        'category',
        'selected_rating',
        'numeric_score',
    ];

    protected function casts(): array
    {
        return [
            'category' => InternshipEvalCategory::class,
            'selected_rating' => InternshipEvalRating::class,
            'numeric_score' => 'decimal:2',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function internshipEvaluation(): BelongsTo
    {
        return $this->belongsTo(InternshipEvaluation::class);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return $this->category->label();
    }

    public function getRatingLabelAttribute(): string
    {
        return $this->selected_rating->label();
    }

    /**
     * Compute the numeric score for a given category + rating + optional explicit value.
     * For range categories, the explicit score (within valid range) is used.
     * For fixed categories, the rating's fixed value is returned.
     */
    public static function computeScore(
        InternshipEvalCategory $category,
        InternshipEvalRating $rating,
        ?float $explicitScore = null,
    ): float {
        if ($category->isRange() && $explicitScore !== null) {
            // Validate it falls in the correct range
            if ($rating->isScoreValid($category, $explicitScore)) {
                return round($explicitScore, 2);
            }

            // Fallback to default if invalid
            return $rating->scoreForCategory($category);
        }

        return $rating->scoreForCategory($category);
    }
}
