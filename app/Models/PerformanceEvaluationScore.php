<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceEvaluationScore extends Model
{
    use HasFactory;

    /**
     * The 10 evaluation component definitions.
     */
    public const COMPONENTS = [
        'pengetahuan_kemahiran'      => 'Pengetahuan dan Kemahiran',
        'produktivitas'              => 'Produktivitas',
        'kualitas_kerja'             => 'Kualitas Kerja',
        'komunikasi_presentasi'      => 'Komunikasi dan Keterampilan Presentasi',
        'kehadiran_ketepatan_waktu'  => 'Kehadiran dan Ketepatan Waktu',
        'inisiatif_kreativitas'      => 'Inisiatif dan Kreativitas',
        'kemampuan_dibimbing'        => 'Kemampuan untuk Dibimbing dan Respons terhadap Pengawasan',
        'kemampuan_beradaptasi'      => 'Kemampuan Beradaptasi',
        'keterampilan_interpersonal' => 'Keterampilan Interpersonal',
        'penampilan'                 => 'Penampilan',
    ];

    protected $fillable = [
        'performance_id',
        'komponen',
        'nilai',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
        ];
    }

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function performanceEvaluation(): BelongsTo
    {
        return $this->belongsTo(PerformanceEvaluation::class, 'performance_id');
    }

    // ──────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────

    /**
     * Get the human-readable label for this score's component.
     */
    public function getLabelAttribute(): string
    {
        return self::COMPONENTS[$this->komponen] ?? $this->komponen;
    }

    /**
     * Check if a given key is a valid component.
     */
    public static function isValidComponent(string $key): bool
    {
        return array_key_exists($key, self::COMPONENTS);
    }
}
