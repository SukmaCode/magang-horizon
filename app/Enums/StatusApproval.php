<?php

namespace App\Enums;

enum StatusApproval: string
{
    case PENDING = 'pending';
    case REVISI = 'revisi';
    case DISETUJUI = 'disetujui';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Review',
            self::REVISI => 'Perlu Revisi',
            self::DISETUJUI => 'Disetujui',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING => in_array($target, [self::REVISI, self::DISETUJUI]),
            self::REVISI => in_array($target, [self::PENDING, self::DISETUJUI]),
            self::DISETUJUI => false,
        };
    }
}
