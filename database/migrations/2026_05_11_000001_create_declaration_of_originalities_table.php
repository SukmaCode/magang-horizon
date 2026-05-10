<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaration_of_originalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_aktif_id')->unique()->constrained('magang_aktifs')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaration_of_originalities');
    }
};
