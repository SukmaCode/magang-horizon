<?php

namespace App\Enums;

enum StatusSeleksi: string
{
    case PENDING = 'pending';
    case DITERIMA = 'diterima';
    case MENUNGGU_MAHASISWA = 'menunggu_mahasiswa';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Seleksi',
            self::DITERIMA => 'Diterima',
            self::MENUNGGU_MAHASISWA => 'Menunggu Mahasiswa',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Check if transition to target status is allowed.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING => in_array($target, [self::DITERIMA, self::DITOLAK]),
            self::DITOLAK => false, // Reset is done by creating new pendaftaran
            self::MENUNGGU_MAHASISWA => in_array($target, [self::DITERIMA, self::DITOLAK]),
            self::DITERIMA => false,
        };
    }
}
