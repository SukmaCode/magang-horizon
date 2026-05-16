<?php

namespace App\Enums;

enum InternshipEvalRating: string
{
    case EXCEPTIONAL = 'exceptional';
    case EXCEEDS = 'exceeds';
    case MEETS = 'meets';
    case NEARS = 'nears';
    case BELOW = 'below';

    public function label(): string
    {
        return match ($this) {
            self::EXCEPTIONAL => 'Exceptional',
            self::EXCEEDS => 'Exceeds Expectations',
            self::MEETS => 'Meets Expectations',
            self::NEARS => 'Nears Expectations',
            self::BELOW => 'Below Expectations',
        };
    }

    /**
     * Options for Performance Rating (Weight 40, Range).
     */
    public static function performanceOptions(): array
    {
        return [
            ['value' => self::EXCEPTIONAL->value, 'label' => 'Exceptional', 'min' => 36.1, 'max' => 40.0, 'default' => 38.0],
            ['value' => self::EXCEEDS->value, 'label' => 'Exceeds Expectations', 'min' => 32.0, 'max' => 36.0, 'default' => 34.0],
            ['value' => self::MEETS->value, 'label' => 'Meets Expectations', 'min' => 28.0, 'max' => 31.9, 'default' => 30.0],
            ['value' => self::NEARS->value, 'label' => 'Nears Expectations', 'min' => 24.0, 'max' => 27.9, 'default' => 26.0],
            ['value' => self::BELOW->value, 'label' => 'Below Expectations', 'min' => 0.0, 'max' => 23.9, 'default' => 20.0],
        ];
    }

    /**
     * Options for Portfolio (Weight 20, Range).
     */
    public static function portfolioOptions(): array
    {
        return [
            ['value' => self::EXCEPTIONAL->value, 'label' => 'Exceptional', 'min' => 16.1, 'max' => 20.0, 'default' => 18.0],
            ['value' => self::EXCEEDS->value, 'label' => 'Exceeds Expectations', 'min' => 8.0, 'max' => 16.0, 'default' => 12.0],
            ['value' => self::MEETS->value, 'label' => 'Meets Expectations', 'min' => 4.0, 'max' => 7.9, 'default' => 6.0],
            ['value' => self::NEARS->value, 'label' => 'Nears Expectations', 'min' => 1.1, 'max' => 3.9, 'default' => 2.5],
            ['value' => self::BELOW->value, 'label' => 'Below Expectations', 'min' => 0.0, 'max' => 1.0, 'default' => 0.5],
        ];
    }

    /**
     * Options for Fixed Score categories (Relevance & Extent, Weight 20 each).
     */
    public static function fixedOptions(): array
    {
        return [
            ['value' => self::EXCEPTIONAL->value, 'label' => 'Exceptional', 'score' => 20],
            ['value' => self::EXCEEDS->value, 'label' => 'Exceeds Expectations', 'score' => 15],
            ['value' => self::MEETS->value, 'label' => 'Meets Expectations', 'score' => 10],
            ['value' => self::NEARS->value, 'label' => 'Nears Expectations', 'score' => 5],
            ['value' => self::BELOW->value, 'label' => 'Below Expectations', 'score' => 1],
        ];
    }

    /**
     * Helper to get score based on category and current rating.
     */
    public function scoreForCategory(InternshipEvalCategory $category): float
    {
        if ($category === InternshipEvalCategory::PERFORMANCE_RATING) {
            $opts = self::performanceOptions();
            $opt = collect($opts)->firstWhere('value', $this->value);
            return $opt['default'] ?? 0;
        }

        if ($category === InternshipEvalCategory::PORTFOLIO) {
            $opts = self::portfolioOptions();
            $opt = collect($opts)->firstWhere('value', $this->value);
            return $opt['default'] ?? 0;
        }

        $opts = self::fixedOptions();
        $opt = collect($opts)->firstWhere('value', $this->value);
        return $opt['score'] ?? 0;
    }

    /**
     * Check if a manual score is within the allowed range for this rating + category.
     */
    public function isScoreValid(InternshipEvalCategory $category, float $score): bool
    {
        if (! $category->isRange()) {
            return false;
        }

        $opts = ($category === InternshipEvalCategory::PERFORMANCE_RATING)
            ? self::performanceOptions()
            : self::portfolioOptions();

        $opt = collect($opts)->firstWhere('value', $this->value);
        if (! $opt) {
            return false;
        }

        return $score >= $opt['min'] && $score <= $opt['max'];
    }
}
