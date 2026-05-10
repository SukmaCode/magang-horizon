<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'nomor_hp',
        'profile_photo_path',
        'bio',
        'skills',
        'linkedin_url',
        'cv_file_path',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nim', 'nama_lengkap', 'prodi', 'cv_file_path', 'linkedin_url', 'bio', 'skills'])
            ->logOnlyDirty();
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    /**
     * Get the URL for the profile photo.
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo_path) {
            return asset('storage/'.$this->profile_photo_path);
        }

        return null;
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Check if profile is complete enough for internship application.
     * Requires LinkedIn and CV.
     */
    public function isProfileComplete(): bool
    {
        return $this->hasLinkedIn()
            && $this->cv_file_path !== null;
    }

    /**
     * Check if LinkedIn URL is filled.
     */
    public function hasLinkedIn(): bool
    {
        return $this->linkedin_url !== null && $this->linkedin_url !== '';
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

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
