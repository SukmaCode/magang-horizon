<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PerformanceEvaluation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'magang_id',
        'supervisor_id',
        'status',
        'nilai_akhir',
        'catatan_supervisor',
        'tanggal_evaluasi',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
            'nilai_akhir' => 'decimal:2',
            'tanggal_evaluasi' => 'date',
            'finalized_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'nilai_akhir', 'catatan_supervisor', 'tanggal_evaluasi'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => "Performance Evaluation #{$this->id} was {$eventName}");
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function magangAktif(): BelongsTo
    {
        return $this->belongsTo(MagangAktif::class, 'magang_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(PerformanceEvaluationScore::class, 'performance_id');
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Calculate the average score from all component scores.
     */
    public function calculateNilaiAkhir(): float
    {
        $scores = $this->scores;

        if ($scores->isEmpty()) {
            return 0;
        }

        return round($scores->avg('nilai'), 2);
    }

    /**
     * Check if all 10 components have been scored.
     */
    public function isComplete(): bool
    {
        return $this->scores()->count() === count(PerformanceEvaluationScore::COMPONENTS);
    }

    public function isFinalized(): bool
    {
        return $this->status === EvaluationStatus::FINALIZED;
    }

    public function isDraft(): bool
    {
        return $this->status === EvaluationStatus::DRAFT;
    }

    public function canBeEdited(): bool
    {
        return $this->status->canEdit();
    }
}
