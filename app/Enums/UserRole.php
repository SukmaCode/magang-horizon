<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case STUDENT = 'mahasiswa';
    case SUPERVISOR_1 = 'dosen_pembimbing';
    case SUPERVISOR_2 = 'dosen_prodi';
    case INDUSTRY = 'supervisor_industri';

    /**
     * Get the Spatie role name mapping.
     */
    public function roleName(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::STUDENT => 'student',
            self::SUPERVISOR_1 => 'supervisor_1',
            self::SUPERVISOR_2 => 'supervisor_2',
            self::INDUSTRY => 'industry',
        };
    }

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::STUDENT => 'Mahasiswa',
            self::SUPERVISOR_1 => 'Dosen Pembimbing',
            self::SUPERVISOR_2 => 'Dosen Prodi',
            self::INDUSTRY => 'Supervisor Industri',
        };
    }

    /**
     * Get the default redirect path after login.
     */
    public function dashboardPath(): string
    {
        return match ($this) {
            self::ADMIN => route('admin.dashboard'),
            self::STUDENT => route('mahasiswa.dashboard'),
            self::SUPERVISOR_1 => route('dosen-pembimbing.dashboard'),
            self::SUPERVISOR_2 => route('dosen-prodi.dashboard'),
            self::INDUSTRY => route('industri.dashboard'),
        };
    }
}
