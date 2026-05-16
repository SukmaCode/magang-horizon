<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->unique()->constrained('magang_aktifs');
            $table->foreignId('nilai_industri')->nullable()->constrained('performance_evaluations')->nullOnDelete();
            $table->foreignId('nilai_kampus')->nullable()->constrained('internship_evaluations')->nullOnDelete();
            $table->boolean('status_verifikasi_dosen_prodi')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
