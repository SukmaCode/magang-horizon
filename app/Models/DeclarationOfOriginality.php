<?php

namespace App\Models;

use App\Enums\DeclarationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeclarationOfOriginality extends Model
{
    use HasFactory;

    protected $table = 'declaration_of_originalities';

    protected $fillable = [
        'magang_aktif_id',
        'file_path',
        'original_filename',
        'status',
        'reviewer_id',
        'reviewed_at',
        'rejection_note',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeclarationStatus::class,
            'reviewed_at' => 'datetime',
            'uploaded_at' => 'datetime',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === DeclarationStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === DeclarationStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === DeclarationStatus::REJECTED;
    }

    /**
     * Check if the declaration file can be replaced.
     * Allowed when status is pending or rejected.
     */
    public function canBeUpdated(): bool
    {
        return in_array($this->status, [
            DeclarationStatus::PENDING,
            DeclarationStatus::REJECTED,
        ]);
    }
}
