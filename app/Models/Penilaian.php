<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'magang_id',
        'nilai_industri',
        'nilai_kampus',
        'status_verifikasi_admin',
    ];

    protected function casts(): array
    {
        return [
            'nilai_industri' => 'decimal:2',
            'nilai_kampus' => 'decimal:2',
            'status_verifikasi_admin' => 'boolean',
        ];
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    /**
     * Computed average grade (replaces MySQL GENERATED column).
     */
    public function getNilaiAkhirAttribute(): ?float
    {
        if ($this->nilai_industri === null || $this->nilai_kampus === null) {
            return null;
        }

        return round(($this->nilai_industri + $this->nilai_kampus) / 2, 2);
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    public function isComplete(): bool
    {
        return $this->nilai_industri !== null && $this->nilai_kampus !== null;
    }

    public function isVerified(): bool
    {
        return $this->status_verifikasi_admin === true;
    }
}
