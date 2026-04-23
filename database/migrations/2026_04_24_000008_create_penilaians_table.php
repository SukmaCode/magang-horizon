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
            $table->decimal('nilai_industri', 5, 2)->nullable();
            $table->decimal('nilai_kampus', 5, 2)->nullable();
            $table->boolean('status_verifikasi_admin')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
