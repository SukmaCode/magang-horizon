<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_evaluation_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_evaluation_id')->unique()->constrained('internship_evaluations')->cascadeOnDelete();
            $table->text('comments')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_evaluation_comments');
    }
};
