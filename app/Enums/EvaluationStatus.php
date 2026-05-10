<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case FINALIZED = 'finalized';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::FINALIZED => 'Finalized',
        };
    }

    /**
     * Badge color class for frontend.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'amber',
            self::FINALIZED => 'green',
        };
    }

    /**
     * Allowed state transitions.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::DRAFT => $target === self::SUBMITTED,
            self::SUBMITTED => in_array($target, [self::FINALIZED, self::DRAFT]),
            self::FINALIZED => false,
        };
    }

    /**
     * Check if evaluation can still be edited.
     */
    public function canEdit(): bool
    {
        return $this !== self::FINALIZED;
    }

    /**
     * Check if evaluation PDF can be downloaded.
     */
    public function canDownload(): bool
    {
        return $this === self::FINALIZED;
    }
}
