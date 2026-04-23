<?php

namespace App\Models;

use App\Enums\StatusSeleksi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pendaftaran extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'mahasiswa_id',
        'industri_id',
        'status_seleksi',
        'keterangan_industri',
    ];

    protected function casts(): array
    {
        return [
            'status_seleksi' => StatusSeleksi::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status_seleksi', 'keterangan_industri'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Pendaftaran #{$this->id} was {$eventName}");
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function industri(): BelongsTo
    {
        return $this->belongsTo(Industri::class);
    }

    public function magangAktif(): HasOne
    {
        return $this->hasOne(MagangAktif::class);
    }

    // ──────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status_seleksi', StatusSeleksi::PENDING);
    }

    public function scopeDiterima($query)
    {
        return $query->where('status_seleksi', StatusSeleksi::DITERIMA);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status_seleksi', StatusSeleksi::DITOLAK);
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Check if student can re-apply (has been rejected).
     */
    public function canReApply(): bool
    {
        return $this->status_seleksi === StatusSeleksi::DITOLAK;
    }
}
