<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────
        // Create Permissions
        // ──────────────────────────────────────
        $permissions = [
            // Pendaftaran
            'pendaftaran.create',
            'pendaftaran.view',
            'pendaftaran.review',

            // Magang
            'magang.view',
            'magang.assign-supervisor',
            'magang.transition',

            // Logbook
            'logbook.create',
            'logbook.view',
            'logbook.approve',
            'logbook.check',

            // Laporan
            'laporan.upload',
            'laporan.view',
            'laporan.review',

            // Penilaian
            'penilaian.grade-industry',
            'penilaian.grade-campus',
            'penilaian.verify',
            'penilaian.view',

            // Sertifikat
            'sertifikat.generate',
            'sertifikat.view',

            // Documents
            'document.upload',
            'document.download',
            'document.delete',

            // Signature
            'signature.create',
            'signature.view',

            // Users (admin)
            'user.manage',
            'user.view',

            // Activity log
            'activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ──────────────────────────────────────
        // Create Roles & Assign Permissions
        // ──────────────────────────────────────

        // Admin — full access
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Student
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student->givePermissionTo([
            'pendaftaran.create',
            'pendaftaran.view',
            'logbook.create',
            'logbook.view',
            'laporan.upload',
            'laporan.view',
            'penilaian.view',
            'sertifikat.view',
            'document.upload',
            'document.download',
            'signature.create',
            'signature.view',
        ]);

        // Industry
        $industry = Role::firstOrCreate(['name' => 'industry', 'guard_name' => 'web']);
        $industry->givePermissionTo([
            'pendaftaran.view',
            'pendaftaran.review',
            'logbook.view',
            'logbook.approve',
            'penilaian.grade-industry',
            'penilaian.view',
            'document.upload',
            'document.download',
            'magang.view',
        ]);

        // Supervisor 1 (Dosen Pembimbing)
        $supervisor1 = Role::firstOrCreate(['name' => 'supervisor_1', 'guard_name' => 'web']);
        $supervisor1->givePermissionTo([
            'magang.view',
            'logbook.view',
            'logbook.check',
            'laporan.view',
            'laporan.review',
            'penilaian.view',
            'document.download',
        ]);

        // Supervisor 2 (Dosen Prodi)
        $supervisor2 = Role::firstOrCreate(['name' => 'supervisor_2', 'guard_name' => 'web']);
        $supervisor2->givePermissionTo([
            'magang.view',
            'penilaian.grade-campus',
            'penilaian.view',
            'laporan.view',
            'document.download',
        ]);
    }
}
