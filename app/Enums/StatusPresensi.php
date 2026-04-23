<?php

namespace App\Enums;

enum StatusPresensi: string
{
    case HADIR = 'hadir';
    case IZIN = 'izin';
    case SAKIT = 'sakit';

    public function label(): string
    {
        return match ($this) {
            self::HADIR => 'Hadir',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
        };
    }
}
