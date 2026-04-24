<template>
  <Head title="Dosen Pembimbing Dashboard" />
  <div class="min-h-screen bg-bg flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary">Dosen Pembimbing Dashboard</h1>
      <p class="text-text-secondary mt-2">Selamat datang, Dosen!</p>
      <Link href="/logout" method="post" as="button" class="mt-6 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
        Logout
      </Link>
    </div>
  </div>
</template>
<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
