<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporan_akhirs', function (Blueprint $table) {
            $table->string('approval_letter_file')->nullable()->after('file_laporan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_akhirs', function (Blueprint $table) {
            $table->dropColumn('approval_letter_file');
        });
    }
};
