<?php

namespace App\Models;

use App\Enums\PortfolioCategory;
use App\Enums\PortfolioRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioEvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_evaluation_id',
        'category',
        'sub_category',
        'selected_rating',
        'numeric_score',
    ];

    protected function casts(): array
    {
        return [
            'category' => PortfolioCategory::class,
            'selected_rating' => PortfolioRating::class,
            'numeric_score' => 'integer',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function portfolioEvaluation(): BelongsTo
    {
        return $this->belongsTo(PortfolioEvaluation::class);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Get the human-readable label for this score's category.
     */
    public function getCategoryLabelAttribute(): string
    {
        return $this->category->label();
    }

    /**
     * Get the human-readable label for this score's sub-category.
     */
    public function getSubCategoryLabelAttribute(): ?string
    {
        if (! $this->sub_category) {
            return null;
        }

        $subs = $this->category->subCategories();

        return $subs[$this->sub_category] ?? $this->sub_category;
    }

    /**
     * Get the human-readable rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->selected_rating->label();
    }

    /**
     * Calculate the numeric score based on rating and category.
     */
    public static function computeScore(PortfolioCategory $category, PortfolioRating $rating): int
    {
        if ($category === PortfolioCategory::PORTFOLIO_CONTENTS) {
            return $rating->contentsScore();
        }

        return $rating->secondaryScore();
    }
}
