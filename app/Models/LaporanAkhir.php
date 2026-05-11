<?php

namespace App\Models;

use App\Enums\StatusApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LaporanAkhir extends Model
{
    use HasFactory;

    protected $fillable = [
        'magang_id',
        'file_laporan',
        'status_approval_kampus',
        'catatan_revisi',
        'approval_letter_file',
    ];

    protected function casts(): array
    {
        return [
            'status_approval_kampus' => StatusApproval::class,
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
