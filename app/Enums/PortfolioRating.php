<?php

namespace App\Enums;

enum PortfolioRating: string
{
    case EXCEPTIONAL = 'exceptional';
    case EXCEEDS = 'exceeds';
    case MEETS = 'meets';
    case NEARS = 'nears';
    case BELOW = 'below';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::EXCEPTIONAL => 'Exceptional',
            self::EXCEEDS => 'Exceeds Expectations',
            self::MEETS => 'Meets Expectations',
            self::NEARS => 'Nears Expectations',
            self::BELOW => 'Below Expectations',
            self::NONE => 'No Portfolio',
        };
    }

    /**
     * Get the numeric score for Portfolio Contents (60% weight).
     */
    public function contentsScore(): int
    {
        return match ($this) {
            self::EXCEPTIONAL => 60,
            self::EXCEEDS => 45,
            self::MEETS => 30,
            self::NEARS => 15,
            self::BELOW => 1,
            self::NONE => 0,
        };
    }

    /**
     * Get the numeric score for Format & Organization or Academic Integrity (20% weight).
     */
    public function secondaryScore(): int
    {
        return match ($this) {
            self::EXCEPTIONAL => 20,
            self::EXCEEDS => 15,
            self::MEETS => 10,
            self::NEARS => 5,
            self::BELOW => 1,
            self::NONE => 0,
        };
    }

    /**
     * Get all ratings as options for frontend.
     */
    public static function allOptions(): array
    {
        return array_map(fn (self $r) => [
            'value' => $r->value,
            'label' => $r->label(),
        ], self::cases());
    }

    /**
     * Get contents score options for frontend.
     */
    public static function contentsOptions(): array
    {
        return array_map(fn (self $r) => [
            'value' => $r->value,
            'label' => $r->label(),
            'score' => $r->contentsScore(),
        ], self::cases());
    }

    /**
     * Get secondary score options for frontend.
     */
    public static function secondaryOptions(): array
    {
        return array_map(fn (self $r) => [
            'value' => $r->value,
            'label' => $r->label(),
            'score' => $r->secondaryScore(),
        ], self::cases());
    }
}
