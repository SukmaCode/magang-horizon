<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Penilaian;

echo "Penilaian Count: " . Penilaian::count() . "\n";
foreach (Penilaian::all() as $p) {
    echo "Penilaian ID: {$p->id}, Magang ID: {$p->magang_id}, Nilai Industri: {$p->nilai_industri}\n";
}
