<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magang_aktifs', function (Blueprint $table) {
            $table->string('status_agreement')->nullable()->after('file_agreement_mahasiswa');
            $table->text('alasan_tolak_agreement')->nullable()->after('status_agreement');
        });
    }

    public function down(): void
    {
        Schema::table('magang_aktifs', function (Blueprint $table) {
            $table->dropColumn(['status_agreement', 'alasan_tolak_agreement']);
        });
    }
};
