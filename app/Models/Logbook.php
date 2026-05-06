<?php

namespace App\Models;

use App\Enums\StatusPresensi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'magang_id',
        'tanggal_waktu',
        'kegiatan',
        'status_presensi',
        'is_approved_industri',
        'komentar_industri',
        'is_checked_kampus',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_waktu' => 'datetime',
            'status_presensi' => StatusPresensi::class,
            'is_approved_industri' => 'boolean',
            'is_checked_kampus' => 'boolean',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('is_approved_industri', true);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('is_approved_industri', false);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('tanggal_waktu', $date);
    }
}
