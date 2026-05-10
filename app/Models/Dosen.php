<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'nama_dosen',
    ];

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Internships supervised as campus supervisor.
     */
    public function magangAktifs(): HasMany
    {
        return $this->hasMany(MagangAktif::class, 'supervisor_kampus_id');
    }

    public function pembimbingAssignments(): HasMany
    {
        return $this->hasMany(PembimbingAssignment::class, 'dosen_id');
    }
}
