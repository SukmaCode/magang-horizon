<?php

namespace App\Enums;

enum StatusTahapan: string
{
    case PERSIAPAN = 'persiapan';
    case PELAKSANAAN = 'pelaksanaan';
    case PENUTUPAN = 'penutupan';
    case LULUS = 'lulus';

    public function label(): string
    {
        return match ($this) {
            self::PERSIAPAN => 'Persiapan',
            self::PELAKSANAAN => 'Pelaksanaan',
            self::PENUTUPAN => 'Penutupan',
            self::LULUS => 'Lulus',
        };
    }

    /**
     * Define allowed state transitions.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PERSIAPAN => $target === self::PELAKSANAAN,
            self::PELAKSANAAN => $target === self::PENUTUPAN,
            self::PENUTUPAN => $target === self::LULUS,
            self::LULUS => false,
        };
    }

    /**
     * Check if daily logs are allowed in this stage.
     */
    public function allowsDailyLogs(): bool
    {
        return $this === self::PELAKSANAAN;
    }

    /**
     * Check if report upload is allowed in this stage.
     */
    public function allowsReportUpload(): bool
    {
        return in_array($this, [self::PELAKSANAAN, self::PENUTUPAN]);
    }
}
