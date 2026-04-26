<template>
  <AuthenticatedLayout>
    <Head title="Dosen Pembimbing Dashboard" />

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-text-primary font-jakarta">Dashboard Dosen Pembimbing</h1>
      <p class="text-text-secondary mt-1">Pantau progres magang mahasiswa bimbingan Anda.</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <CardContainer padding="p-6" class="border-l-4 border-l-success">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Mahasiswa Bimbingan</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Total mahasiswa aktif</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Pengajuan CV Baru</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Menunggu persetujuan Anda</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Laporan Magang</h3>
        <p class="text-2xl font-bold text-success font-jakarta">0 / 0</p>
        <p class="text-sm text-text-secondary mt-2">Menunggu direview</p>
      </CardContainer>
    </div>

    <!-- Recent Activity Placeholder -->
    <CardContainer padding="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-text-primary font-jakarta">Aktivitas Terakhir Mahasiswa</h2>
        <button class="text-sm text-primary font-semibold hover:text-primary-hover">Lihat Semua</button>
      </div>
      
      <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h3 class="text-sm font-bold text-text-primary">Belum Ada Aktivitas</h3>
        <p class="text-xs text-text-secondary mt-1">Belum ada mahasiswa yang ditugaskan kepada Anda.</p>
      </div>
    </CardContainer>

  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
</script>
