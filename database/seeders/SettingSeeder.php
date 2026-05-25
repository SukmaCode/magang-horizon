<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name', 'value' => 'Sistem Informasi Magang Horizon', 'type' => 'text', 'group' => 'general', 'label' => 'Nama Aplikasi'],
            ['key' => 'app_logo', 'value' => null, 'type' => 'image', 'group' => 'general', 'label' => 'Logo Aplikasi'],
            ['key' => 'contact_email', 'value' => 'admin@horizon.ac.id', 'type' => 'text', 'group' => 'general', 'label' => 'Email Kontak Bantuan'],
            ['key' => 'contact_phone', 'value' => '081234567890', 'type' => 'text', 'group' => 'general', 'label' => 'Nomor Telepon Bantuan'],
            
            // Internship Rules
            ['key' => 'max_bimbingan_per_dosen', 'value' => '10', 'type' => 'number', 'group' => 'internship', 'label' => 'Batas Maksimal Mahasiswa Bimbingan per Dosen'],
            ['key' => 'min_attendance_percentage', 'value' => '80', 'type' => 'number', 'group' => 'internship', 'label' => 'Syarat Minimal Kehadiran Logbook (%)'],
            ['key' => 'passing_grade', 'value' => '70', 'type' => 'number', 'group' => 'internship', 'label' => 'Standar Nilai Kelulusan Minimal (Passing Grade)'],
            ['key' => 'allow_backdate_logbook', 'value' => '0', 'type' => 'boolean', 'group' => 'internship', 'label' => 'Izinkan Mahasiswa Mengisi Logbook Tanggal Berlalu (Backdate)'],
            
            // System
            ['key' => 'global_announcement', 'value' => '', 'type' => 'text', 'group' => 'system', 'label' => 'Pengumuman Global (Banner)'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'system', 'label' => 'Mode Maintenance (Aplikasi Sedang Diperbaiki)'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
