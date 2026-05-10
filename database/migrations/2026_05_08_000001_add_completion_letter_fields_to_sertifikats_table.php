<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->string('posisi_magang', 255)->nullable()->after('tanggal_terbit');
            $table->string('departemen', 255)->nullable()->after('posisi_magang');
            $table->text('deskripsi_tugas')->nullable()->after('departemen');
            $table->text('komentar_penutup')->nullable()->after('deskripsi_tugas');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->dropColumn([
                'posisi_magang',
                'departemen',
                'deskripsi_tugas',
                'komentar_penutup',
            ]);
        });
    }
};
