<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PerformanceEvaluation;

foreach (PerformanceEvaluation::all() as $eval) {
    echo "Eval ID: {$eval->id}, Updated At: {$eval->updated_at}\n";
}
