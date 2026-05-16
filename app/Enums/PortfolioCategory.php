<?php

namespace App\Enums;

enum PortfolioCategory: string
{
    case PORTFOLIO_CONTENTS = 'portfolio_contents';
    case FORMAT_ORGANIZATION = 'format_organization';
    case ACADEMIC_INTEGRITY = 'academic_integrity';

    public function label(): string
    {
        return match ($this) {
            self::PORTFOLIO_CONTENTS => 'Portfolio Contents',
            self::FORMAT_ORGANIZATION => 'Format and Organization',
            self::ACADEMIC_INTEGRITY => 'Academic Integrity',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::PORTFOLIO_CONTENTS => 60,
            self::FORMAT_ORGANIZATION => 20,
            self::ACADEMIC_INTEGRITY => 20,
        };
    }

    /**
     * Sub-categories for Portfolio Contents.
     */
    public function subCategories(): array
    {
        return match ($this) {
            self::PORTFOLIO_CONTENTS => [
                'experience' => 'Experience',
                'projects' => 'Projects',
                'certifications' => 'Certifications',
                'activities' => 'Activities',
            ],
            default => [],
        };
    }

    /**
     * Whether this category has sub-categories.
     */
    public function hasSubCategories(): bool
    {
        return $this === self::PORTFOLIO_CONTENTS;
    }

    /**
     * Minimum score to meet "Nears Expectations" threshold.
     */
    public function nearExpectationsMinScore(): int
    {
        return match ($this) {
            self::PORTFOLIO_CONTENTS => 15,
            self::FORMAT_ORGANIZATION => 5,
            self::ACADEMIC_INTEGRITY => 5,
        };
    }

    /**
     * Get all categories as structured config for frontend.
     */
    public static function rubricConfig(): array
    {
        $config = [];
        foreach (self::cases() as $cat) {
            $config[] = [
                'key' => $cat->value,
                'label' => $cat->label(),
                'weight' => $cat->weight(),
                'has_sub_categories' => $cat->hasSubCategories(),
                'sub_categories' => $cat->subCategories(),
                'min_score' => $cat->nearExpectationsMinScore(),
            ];
        }

        return $config;
    }
}
