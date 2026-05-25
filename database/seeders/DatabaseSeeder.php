<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Dosen;
use App\Models\Industri;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed roles & permissions first
        $this->call(RolePermissionSeeder::class);
        $this->call(SettingSeeder::class);

        // 2. Create Admin user
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@magang-horizon.test',
            'password' => '12345678',
            'role' => UserRole::ADMIN->value,
        ]);
        $admin->assignRole('admin');

        // 3. Create sample Student
        $studentUser = User::create([
            'username' => 'mahasiswa01',
            'email' => 'mahasiswa01@magang-horizon.test',
            'password' => '12345678',
            'role' => UserRole::STUDENT->value,
        ]);
        $studentUser->assignRole('student');
        Mahasiswa::create([
            'user_id' => $studentUser->id,
            'nim' => '2024001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'prodi' => 'Teknik Informatika',
        ]);

        // 4. Create sample Industry
        $industryUser = User::create([
            'username' => 'ptmajumapan',
            'email' => 'industri@magang-horizon.test',
            'password' => '12345678',
            'role' => UserRole::INDUSTRY->value,
        ]);
        $industryUser->assignRole('industry');
        Industri::create([
            'user_id' => $industryUser->id,
            'nama_perusahaan' => 'PT Maju Mapan',
            'alamat' => 'Jl. Sudirman No. 123, Jakarta',
            'kontak_person' => 'Budi Santoso',
        ]);

        // 5. Create sample Dosen Pembimbing
        $dosenUser1 = User::create([
            'username' => 'dosenpembimbing01',
            'email' => 'dosen1@magang-horizon.test',
            'password' => '12345678',
            'role' => UserRole::SUPERVISOR_1->value,
        ]);
        $dosenUser1->assignRole('supervisor_1');
        Dosen::create([
            'user_id' => $dosenUser1->id,
            'nip' => '198501012020011001',
            'nama_dosen' => 'Dr. Siti Aminah',
        ]);

        // 6. Create sample Dosen Prodi
        $dosenUser2 = User::create([
            'username' => 'dosenprodi01',
            'email' => 'dosen2@magang-horizon.test',
            'password' => '12345678',
            'role' => UserRole::SUPERVISOR_2->value,
        ]);
        $dosenUser2->assignRole('supervisor_2');
        Dosen::create([
            'user_id' => $dosenUser2->id,
            'nip' => '198801012020011002',
            'nama_dosen' => 'Prof. Andi Rahman',
        ]);
    }
}
