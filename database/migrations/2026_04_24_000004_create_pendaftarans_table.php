<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas');
            $table->foreignId('industri_id')->constrained('industris');
            $table->string('status_seleksi')->default('pending');
            $table->text('keterangan_industri')->nullable();
            $table->timestamps();

            // Prevent duplicate active applications
            $table->index(['mahasiswa_id', 'industri_id', 'status_seleksi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
