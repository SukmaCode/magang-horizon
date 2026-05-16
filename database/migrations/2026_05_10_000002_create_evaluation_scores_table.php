<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_evaluation_id')->constrained('internship_evaluations')->cascadeOnDelete();
            $table->string('category');
            $table->string('selected_rating');
            $table->decimal('numeric_score', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['internship_evaluation_id', 'category'], 'internship_eval_scores_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
