<template>
  <Head title="Mahasiswa Dashboard" />
  <div class="min-h-screen bg-bg flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary">Mahasiswa Dashboard</h1>
      <p class="text-text-secondary mt-2">Selamat datang, Mahasiswa!</p>
      <Link href="/logout" method="post" as="button" class="mt-6 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
        Logout
      </Link>
    </div>
  </div>
</template>
<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
