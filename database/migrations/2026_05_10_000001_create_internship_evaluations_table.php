<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_aktif_id')->unique()->constrained('magang_aktifs')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('evaluation_date');
            $table->decimal('overall_score', 5, 2)->default(0.00);
            $table->string('pass_status')->default('fail');
            $table->string('status')->default('draft'); // draft, submitted, finalized
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->index('evaluator_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_evaluations');
    }
};
