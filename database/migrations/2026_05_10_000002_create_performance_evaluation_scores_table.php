<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_id')->constrained('performance_evaluations')->cascadeOnDelete();
            $table->string('komponen');
            $table->decimal('nilai', 5, 2);
            $table->timestamps();

            $table->unique(['performance_id', 'komponen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_evaluation_scores');
    }
};
