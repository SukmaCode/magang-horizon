<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magang_aktifs');
            $table->dateTime('tanggal_waktu');
            $table->text('kegiatan');
            $table->string('status_presensi')->default('hadir');

            // Approval by industry supervisor
            $table->boolean('is_approved_industri')->default(false);
            $table->text('komentar_industri')->nullable();

            // Check by campus supervisor
            $table->boolean('is_checked_kampus')->default(false);

            $table->timestamps();

            // Index for efficient querying
            $table->index(['magang_id', 'tanggal_waktu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
