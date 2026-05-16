<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('performance_evaluation_scores');
echo "Columns in performance_evaluation_scores:\n";
print_r($columns);

$columns2 = Schema::getColumnListing('performance_evaluations');
echo "\nColumns in performance_evaluations:\n";
print_r($columns2);
