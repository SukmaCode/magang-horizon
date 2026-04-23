<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // documentable_type + documentable_id
            $table->string('type'); // DocumentType enum value
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('file_size'); // bytes
            $table->string('mime_type', 100);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
