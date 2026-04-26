<template>
  <div class="min-h-screen bg-surface">
    <!-- Sidebar Component (fixed, never scrolls) -->
    <AppSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

    <!-- Main Content (offset by sidebar width on desktop) -->
    <div class="md:ml-64 flex flex-col h-screen">
      <!-- Header (sticky at top) -->
      <header class="bg-card shadow-sm z-10 shrink-0">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <!-- Hamburger Bar -->
            <button @click="isSidebarOpen = true" class="md:hidden p-2 -ml-2 text-text-secondary hover:bg-gray-50 rounded-lg">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <h2 class="text-xl font-bold text-text-primary font-jakarta">Dashboard</h2>
          </div>

          <div class="flex items-center gap-4">
            <div class="hidden sm:flex flex-col items-end">
              <span class="text-sm font-semibold text-text-primary font-jakarta">{{ user.nama_lengkap || user.nama_perusahaan }} | {{ user.prodi || user.kontak_person }}</span>
              <span class="text-xs font-medium text-primary bg-primary/10 px-2 py-0.5 rounded-full capitalize">{{ userRoleLabel }}</span>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary/10 border-2 border-primary/20 flex items-center justify-center text-primary font-bold">
              {{ (user.nama_lengkap || 'U').charAt(0).toUpperCase() }}
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content (scrollable independently) -->
      <main class="flex-1 overflow-y-auto bg-surface p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppSidebar from '@/Components/AppSidebar.vue';

const isSidebarOpen = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const role = computed(() => user.value?.role);

const userRoleLabel = computed(() => {
  const r = role.value;
  if (!r) return 'Guest';
  return r.split('_').join(' ');
});
</script>
