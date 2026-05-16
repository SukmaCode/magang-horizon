<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipEvaluationComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_evaluation_id',
        'comments',
        'feedback',
    ];

    // ──────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────

    public function internshipEvaluation(): BelongsTo
    {
        return $this->belongsTo(InternshipEvaluation::class);
    }
}
