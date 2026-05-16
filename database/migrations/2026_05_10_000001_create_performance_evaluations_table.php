<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->unique()->constrained('magang_aktifs')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users');
            $table->string('status')->default('draft'); // draft, submitted, finalized
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan_supervisor')->nullable();
            $table->date('tanggal_evaluasi')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->index('supervisor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
