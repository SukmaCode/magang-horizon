<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    case UPLOADED = 'uploaded';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::UPLOADED => 'Diupload Industri',
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * Get badge color class for frontend display.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::UPLOADED => 'info',
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
