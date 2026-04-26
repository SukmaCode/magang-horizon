<template>
  <AuthenticatedLayout>
    <Head title="Koordinator Prodi Dashboard" />

    <div class="mb-8">
      <h1 class="text-2xl font-bold text-text-primary font-jakarta">Dashboard Koordinator Prodi</h1>
      <p class="text-text-secondary mt-1">Pantau seluruh aktivitas magang mahasiswa di program studi Anda.</p>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <CardContainer padding="p-6" class="border-l-4 border-l-primary">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Total Mahasiswa Magang</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Sedang aktif magang</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Persetujuan Lulus</h3>
        <p class="text-2xl font-bold text-text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Menunggu verifikasi Anda</p>
      </CardContainer>

      <CardContainer padding="p-6">
        <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Dosen Pembimbing</h3>
        <p class="text-2xl font-bold text-primary font-jakarta">0</p>
        <p class="text-sm text-text-secondary mt-2">Dosen aktif membimbing</p>
      </CardContainer>
    </div>

    <!-- Recent Activity Placeholder -->
    <CardContainer padding="p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-text-primary font-jakarta">Laporan Prodi Terkini</h2>
        <button class="text-sm text-primary font-semibold hover:text-primary-hover">Lihat Semua</button>
      </div>
      
      <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-sm font-bold text-text-primary">Belum Ada Laporan</h3>
        <p class="text-xs text-text-secondary mt-1">Belum ada mahasiswa yang mencapai tahap kelulusan.</p>
      </div>
    </CardContainer>

  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
</script>
