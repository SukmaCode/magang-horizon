<template>
  <Head title="Login" />

  <div class="min-h-screen bg-bg flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Decorative blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
      <!-- Logo / Brand -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-primary rounded-2xl shadow-lg shadow-primary/25 mb-4">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Magang Horizon</h1>
        <p class="mt-1.5 text-sm text-text-secondary">Masuk ke akun Anda</p>
      </div>

      <!-- Login Card -->
      <CardContainer padding="p-8">
        <!-- Flash Error -->
        <div
          v-if="form.errors.email || loginError"
          class="mb-6 flex items-start gap-3 p-4 bg-danger/5 border border-danger/20 rounded-xl"
        >
          <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <p class="text-sm text-danger">{{ form.errors.email || loginError }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <InputField
            id="email"
            v-model="form.email"
            label="Email"
            type="email"
            placeholder="nama@krw.horizon.ac.id"
            :error="form.errors.email"
            required
          />

          <InputField
            id="password"
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="••••••••"
            :error="form.errors.password"
            required
          />

          <!-- Remember me -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer group">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30 transition-colors"
              />
              <span class="text-sm text-text-secondary group-hover:text-text-primary transition-colors">
                Ingat saya
              </span>
            </label>
          </div>

          <ButtonPrimary
            type="submit"
            :loading="form.processing"
            :disabled="form.processing"
            full-width
          >
            Masuk
          </ButtonPrimary>
        </form>

        <!-- Divider -->
        <div class="relative mt-6 mb-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-100"></div>
          </div>
          <div class="relative flex justify-center text-xs">
            <span class="bg-card px-3 text-text-secondary">Belum punya akun?</span>
          </div>
        </div>

        <!-- Register link -->
        <Link
          href="/register"
          class="w-full inline-flex items-center justify-center gap-2 font-semibold text-sm rounded-xl px-6 py-3 bg-white text-text-primary border border-gray-200 shadow-sm hover:bg-surface hover:border-gray-300 transition-all duration-200 active:scale-[0.98]"
        >
          Daftar Sekarang
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </Link>
      </CardContainer>

      <!-- Footer -->
      <p class="text-center text-xs text-text-secondary/60 mt-6">
        &copy; {{ new Date().getFullYear() }} Magang Horizon. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputField from '@/Components/InputField.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import CardContainer from '@/Components/CardContainer.vue';
import { ref } from 'vue';

const loginError = ref('');

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const handleLogin = () => {
  loginError.value = '';
  form.post('/login', {
    onError: () => {
      form.reset('password');
    },
  });
};
</script>
