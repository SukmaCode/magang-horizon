<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_aktif_id')->unique()->constrained('magang_aktifs')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->string('evaluator_type'); // 'industry_supervisor' or 'university_mentor'
            $table->string('company_name');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('evaluation_date');
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('qualification_result')->default('not_qualified'); // 'qualified' or 'not_qualified'
            $table->string('status')->default('draft'); // draft, submitted, finalized
            $table->text('comments')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('portfolio_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_evaluation_id')->constrained('portfolio_evaluations')->cascadeOnDelete();
            $table->string('category'); // portfolio_contents, format_organization, academic_integrity
            $table->string('sub_category')->nullable(); // experience, projects, certifications, activities (only for portfolio_contents)
            $table->string('selected_rating'); // exceptional, exceeds, meets, nears, below, none
            $table->integer('numeric_score')->default(0);
            $table->timestamps();

            // Each category+sub_category combo is unique per evaluation
            $table->unique(['portfolio_evaluation_id', 'category', 'sub_category'], 'portfolio_score_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_evaluation_scores');
        Schema::dropIfExists('portfolio_evaluations');
    }
};
