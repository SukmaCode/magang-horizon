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
        'status_verifikasi_dosen_prodi',
    ];

    protected function casts(): array
    {
        return [
            'status_verifikasi_dosen_prodi' => 'boolean',
        ];
    }

    // ──────────────────────────────────────
    // Accessors
    // ──────────────────────────────────────

    /**
     * Computed average grade from both evaluations.
     * Resolves the actual scores via the FK relationships.
     */
    public function getNilaiAkhirAttribute(): ?float
    {
        $nilaiIndustri = $this->performanceEvaluation?->nilai_akhir;
        $nilaiKampus = $this->internshipEvaluation?->overall_score;

        if ($nilaiIndustri === null || $nilaiKampus === null) {
            return null;
        }

        return round(($nilaiIndustri + $nilaiKampus) / 2, 2);
    }

    /**
     * Resolve nilai_industri score from the related PerformanceEvaluation.
     */
    public function getNilaiIndustriScoreAttribute(): ?float
    {
        return $this->performanceEvaluation?->nilai_akhir;
    }

    /**
     * Resolve nilai_kampus score from the related InternshipEvaluation.
     */
    public function getNilaiKampusScoreAttribute(): ?float
    {
        return $this->internshipEvaluation?->overall_score;
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }

    /**
     * nilai_industri FK → performance_evaluations.id
     */
    public function performanceEvaluation(): BelongsTo
    {
        return $this->belongsTo(PerformanceEvaluation::class, 'performance_evaluation_id');
    }

    /**
     * nilai_kampus FK → internship_evaluations.id
     */
    public function internshipEvaluation(): BelongsTo
    {
        return $this->belongsTo(InternshipEvaluation::class, 'internship_evaluation_id');
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    public function isComplete(): bool
    {
        return $this->performanceEvaluation !== null && $this->internshipEvaluation !== null;
    }

    public function isVerified(): bool
    {
        return $this->status_verifikasi_dosen_prodi === true;
    }
}
