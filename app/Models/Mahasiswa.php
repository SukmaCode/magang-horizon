<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Mahasiswa extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'nim',
        'nama_lengkap',
        'prodi',
        'cv_file_path',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nim', 'nama_lengkap', 'prodi', 'cv_file_path'])
            ->logOnlyDirty();
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    /**
     * Get the active (accepted) pendaftaran.
     */
    public function activePendaftaran(): HasMany
    {
        return $this->pendaftarans()->where('status_seleksi', 'diterima');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
