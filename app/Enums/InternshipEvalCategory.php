<?php

namespace App\Enums;

enum InternshipEvalCategory: string
{
    case PERFORMANCE_RATING = 'performance_rating';
    case RELEVANCE = 'relevance';
    case EXTENT = 'extent';
    case PORTFOLIO = 'portfolio';

    public function label(): string
    {
        return match ($this) {
            self::PERFORMANCE_RATING => 'Performance Rating',
            self::RELEVANCE => 'Relevance to Program',
            self::EXTENT => 'Extent of Professional Development',
            self::PORTFOLIO => 'Professional Portfolio',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::PERFORMANCE_RATING => 40,
            self::RELEVANCE => 20,
            self::EXTENT => 20,
            self::PORTFOLIO => 20,
        };
    }

    public function isRange(): bool
    {
        return match ($this) {
            self::PERFORMANCE_RATING, self::PORTFOLIO => true,
            default => false,
        };
    }

    /**
     * Get descriptions for each rating within this category.
     */
    public function ratingDescriptions(): array
    {
        return match ($this) {
            self::PERFORMANCE_RATING => [
                'exceptional' => 'Always demonstrates exceptional technical skills, professionalism, and proactive attitude.',
                'exceeds' => 'Frequently demonstrates high level of skills and professionalism beyond expectations.',
                'meets' => 'Consistently meets all requirements and expectations of the internship.',
                'nears' => 'Generally meets expectations but occasionally requires guidance or improvement.',
                'below' => 'Fails to meet basic expectations and requirements.',
            ],
            self::RELEVANCE => [
                'exceptional' => 'Internship activities are perfectly aligned with academic program objectives.',
                'exceeds' => 'Activities are highly relevant with minor exceptions.',
                'meets' => 'Activities are sufficiently relevant to the program.',
                'nears' => 'Some activities are relevant, but many fall outside program scope.',
                'below' => 'Activities have little to no relevance to the academic program.',
            ],
            self::EXTENT => [
                'exceptional' => 'Significant professional growth and skill acquisition observed.',
                'exceeds' => 'Noticeable professional growth across multiple areas.',
                'meets' => 'Moderate professional growth consistent with internship duration.',
                'nears' => 'Limited professional growth observed.',
                'below' => 'No significant professional growth observed.',
            ],
            self::PORTFOLIO => [
                'exceptional' => 'Portfolio contains high-quality documentation of diverse and complex projects.',
                'exceeds' => 'Portfolio is well-organized and showcases quality work.',
                'meets' => 'Portfolio adequately documents the required internship activities.',
                'nears' => 'Portfolio is incomplete or lacks sufficient detail.',
                'below' => 'Portfolio is missing or of very poor quality.',
            ],
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
                'is_range' => $cat->isRange(),
                'descriptions' => $cat->ratingDescriptions(),
            ];
        }

        return $config;
    }
}
