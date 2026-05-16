<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PerformanceEvaluation;
use App\Models\PerformanceEvaluationScore;

echo "PerformanceEvaluation Count: " . PerformanceEvaluation::count() . "\n";
echo "PerformanceEvaluationScore Count: " . PerformanceEvaluationScore::count() . "\n";

foreach (PerformanceEvaluation::all() as $eval) {
    echo "Eval ID: {$eval->id}, Magang ID: {$eval->magang_id}, Status: {$eval->status->value}\n";
    echo "  Scores Count: " . $eval->scores()->count() . "\n";
}
