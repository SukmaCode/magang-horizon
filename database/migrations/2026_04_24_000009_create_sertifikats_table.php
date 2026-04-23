<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->unique()->constrained('magang_aktifs');
            $table->string('nomor_sertifikat', 100)->unique()->nullable();
            $table->string('file_sertifikat_path')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
