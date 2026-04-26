<template>
  <AuthenticatedLayout>
    <Head title="Industri Dashboard" />

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-text-primary font-jakarta">Dashboard Industri</h1>
      <p class="text-text-secondary mt-1">Kelola permohonan magang dan pantau aktivitas peserta magang Anda.</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <CardContainer padding="p-6" class="border-l-4 border-l-accent">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Review CV Menunggu</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Permohonan mahasiswa baru</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Peserta Aktif</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0 Mahasiswa</p>
        <p class="text-sm text-text-secondary mt-2">Sedang melaksanakan magang</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Persetujuan Absensi</h3>
        <p class="text-2xl font-bold text-accent font-jakarta">0 / Hari Ini</p>
        <p class="text-sm text-text-secondary mt-2">Menunggu konfirmasi Anda</p>
      </CardContainer>
    </div>

    <!-- Recent Activity Placeholder -->
    <CardContainer padding="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-text-primary font-jakarta">Aktivitas Terakhir</h2>
        <button class="text-sm text-primary font-semibold hover:text-primary-hover">Lihat Semua</button>
      </div>
      
      <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-sm font-bold text-text-primary">Belum Ada Aktivitas</h3>
        <p class="text-xs text-text-secondary mt-1">Belum ada peserta magang atau permohonan masuk.</p>
      </div>
    </CardContainer>

  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
</script>
