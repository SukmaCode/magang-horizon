<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magang_aktifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftarans');

            // Agreement & permit documents
            $table->string('file_agreement_industri')->nullable();
            $table->string('file_agreement_mahasiswa')->nullable();
            $table->string('sk_pembimbing_path')->nullable();

            // Supervisor assignment
            $table->foreignId('supervisor_kampus_id')->nullable()->constrained('dosens');
            $table->foreignId('supervisor_industri_id')->nullable()->constrained('users');

            // Progress & period
            $table->string('status_tahapan')->default('persiapan');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magang_aktifs');
    }
};
