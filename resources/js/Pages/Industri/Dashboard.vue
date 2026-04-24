<template>
  <Head title="Industri Dashboard" />
  <div class="min-h-screen bg-bg flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary">Industri Dashboard</h1>
      <p class="text-text-secondary mt-2">Selamat datang, Industri!</p>
      <Link href="/logout" method="post" as="button" class="mt-6 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
        Logout
      </Link>
    </div>
  </div>
</template>
<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
