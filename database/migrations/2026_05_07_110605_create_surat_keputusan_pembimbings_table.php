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
        Schema::create('surat_keputusan_pembimbings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('pembimbing_assignments')->cascadeOnDelete();
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->string('file_sk');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keputusan_pembimbings');
    }
};
