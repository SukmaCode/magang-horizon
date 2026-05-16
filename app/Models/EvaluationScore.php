<?php

// ⚠️ DEPRECATED: This file has been renamed to PerformanceEvaluationScore.php
// The EvaluationScore class no longer exists as a standalone model.
// Use App\Models\PerformanceEvaluationScore for industry evaluations.
// Use App\Models\InternshipEvaluationScore for dosen pembimbing evaluations.

namespace App\Models;

class EvaluationScore extends PerformanceEvaluationScore
{
    // Backward-compatibility alias
}
