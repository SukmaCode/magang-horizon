<?php

namespace App\Models;

use App\Enums\ClearanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipClearance extends Model
{
    use HasFactory;

    protected $table = 'internship_clearances';

    protected $fillable = [
        'magang_aktif_id',
        'file_path',
        'original_filename',
        'status',
        'submitted_at',
        'reviewer_id',
        'reviewed_at',
        'rejection_note',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ClearanceStatus::class,
            'submitted_at' => 'datetime',
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

    public function isUploaded(): bool
    {
        return $this->status === ClearanceStatus::UPLOADED;
    }

    public function isPending(): bool
    {
        return $this->status === ClearanceStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === ClearanceStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === ClearanceStatus::REJECTED;
    }

    /**
     * Check if the file can be replaced by industri.
     * Allowed when status is uploaded or rejected.
     */
    public function canBeUpdatedByIndustri(): bool
    {
        return in_array($this->status, [
            ClearanceStatus::UPLOADED,
            ClearanceStatus::REJECTED,
        ]);
    }

    /**
     * Check if mahasiswa can submit this clearance to dosen.
     * Allowed when status is uploaded (first time) or rejected (re-submit).
     */
    public function canBeSubmitted(): bool
    {
        return in_array($this->status, [
            ClearanceStatus::UPLOADED,
            ClearanceStatus::REJECTED,
        ]);
    }
}
