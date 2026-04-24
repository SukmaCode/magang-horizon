<template>
  <Head title="Dosen Prodi Dashboard" />
  <div class="min-h-screen bg-bg flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary">Dosen Prodi Dashboard</h1>
      <p class="text-text-secondary mt-2">Selamat datang, Koordinator Prodi!</p>
      <Link href="/logout" method="post" as="button" class="mt-6 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
        Logout
      </Link>
    </div>
  </div>
</template>
<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
