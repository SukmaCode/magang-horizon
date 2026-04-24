<template>
  <Head title="Admin Dashboard" />
  <div class="min-h-screen bg-bg flex items-center justify-center">
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-4">
        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-text-primary">Admin Dashboard</h1>
      <p class="text-text-secondary mt-2">Selamat datang, Admin!</p>
      <form method="POST" action="/logout" class="mt-6">
        <input type="hidden" name="_token" :value="csrfToken" />
        <button type="submit" class="text-sm text-primary hover:text-primary-hover font-medium transition-colors">
          Logout
        </button>
      </form>
    </div>
  </div>
</template>
<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const csrfToken = computed(() => usePage().props?.csrf_token || document.querySelector('meta[name="csrf-token"]')?.content || '');
</script>
