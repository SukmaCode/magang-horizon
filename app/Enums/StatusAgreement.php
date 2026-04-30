<?php

namespace App\Enums;

enum StatusAgreement: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Persetujuan',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
        };
    }
}
