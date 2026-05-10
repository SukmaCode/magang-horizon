<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('nomor_hp', 20)->nullable()->after('prodi');
            $table->string('profile_photo_path')->nullable()->after('nomor_hp');
            $table->text('bio')->nullable()->after('profile_photo_path');
            $table->text('skills')->nullable()->after('bio');
            $table->string('linkedin_url')->nullable()->after('skills');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_hp',
                'profile_photo_path',
                'bio',
                'skills',
                'linkedin_url',
            ]);
        });
    }
};
