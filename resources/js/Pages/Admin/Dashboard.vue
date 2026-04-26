<template>
  <AuthenticatedLayout>
    <Head title="Admin Dashboard" />

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-text-primary font-jakarta">Dashboard Administrator</h1>
      <p class="text-text-secondary mt-1">Kelola seluruh sistem magang dan pantau statistik utama.</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <CardContainer padding="p-6" class="border-l-4 border-l-primary">
        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Total Mahasiswa</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-xs text-text-secondary mt-2">Terdaftar di sistem</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Industri Mitra</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-xs text-text-secondary mt-2">Perusahaan aktif</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Menunggu SK</h3>
        <p class="text-2xl font-bold text-accent font-jakarta">0</p>
        <p class="text-xs text-text-secondary mt-2">Penugasan dosen pending</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Penerbitan Sertifikat</h3>
        <p class="text-2xl font-bold text-success font-jakarta">0</p>
        <p class="text-xs text-text-secondary mt-2">Menunggu verifikasi akhir</p>
      </CardContainer>
    </div>

    <!-- Recent Activity Placeholder -->
    <CardContainer padding="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-text-primary font-jakarta">Log Sistem Terakhir</h2>
        <button class="text-sm text-primary font-semibold hover:text-primary-hover">Lihat Semua</button>
      </div>
      
      <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
        <h3 class="text-sm font-bold text-text-primary">Tidak Ada Aktivitas Sistem</h3>
        <p class="text-xs text-text-secondary mt-1">Sistem belum mencatat log aktivitas pengguna.</p>
      </div>
    </CardContainer>

  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
</script>
