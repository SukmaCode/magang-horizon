<template>
  <div class="min-h-screen bg-surface flex relative">
    <!-- Mobile Sidebar Backdrop -->
    <Transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-show="isSidebarOpen" class="fixed inset-0 bg-gray-900/80 z-40 md:hidden" @click="isSidebarOpen = false"></div>
    </Transition>

    <!-- Sidebar -->
    <aside 
      :class="[
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
        'fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-gray-100 flex flex-col shadow-2xl transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto md:shadow-sm'
      ]"
    >
      <div class="sticky top-0 p-6 flex items-center justify-between gap-3 border-b border-gray-50">
        <div class="flex items-center gap-3">
          <img src="../../assets/images/logo-horizon.png" alt="logo" class="w-10 h-10">
          <span class="font-jakarta font-bold text-lg text-text-primary">Magang<span class="text-primary">Horizon</span></span>
        </div>
        <!-- <button @click="isSidebarOpen = false" class="md:hidden absolute right-3 top-3 p-2 text-gray-400 hover:bg-gray-100 rounded-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button> -->
      </div>

      <nav class="flex-1 p-4 space-y-1">
        <template v-for="item in navigation" :key="item.name">
          <Link
            @click="isSidebarOpen = false"
            :href="item.href"
            :class="[
              item.current
                ? 'bg-primary/5 text-primary font-semibold'
                : 'text-text-secondary hover:bg-gray-50 hover:text-text-primary',
              'group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200'
            ]"
          >
            <component
              :is="item.icon"
              :class="[
                item.current ? 'text-primary' : 'text-gray-400 group-hover:text-gray-500',
                'flex-shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors duration-200'
              ]"
              aria-hidden="true"
            />
            <span class="truncate">{{ item.name }}</span>
          </Link>
        </template>
      </nav>

      <div class="p-4 border-t border-gray-100">
        <Link
          @click="isSidebarOpen = false"
          href="/logout"
          method="post"
          as="button"
          class="w-full flex items-center gap-2 px-3 py-2.5 text-sm font-medium text-danger bg-danger/5 hover:bg-danger/10 rounded-xl transition-colors duration-200"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Keluar
        </Link>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Header -->
      <header class="bg-card shadow-sm z-10">
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

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto bg-surface p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, h, ref } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

const isSidebarOpen = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const role = computed(() => user.value?.role);

// ─── Icons ─────────────────────────────────────────────────────────────
const HomeIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [ h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' }) ]) };
const DocumentIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [ h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' }) ]) };
const CheckIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [ h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }) ]) };
const ClipboardIcon = { render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [ h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' }) ]) };

const navigation = computed(() => {
  const currentPath = window.location.pathname;
  
  // 1. Menu dasar yang selalu ada di semua Role
  const menu = [
    { name: 'Dashboard', href: `/${role.value}/dashboard`, icon: HomeIcon, current: currentPath.includes('dashboard') },
  ];

  // 2. Tambahan menu KHUSUS MAHASISWA
  if (role.value === 'mahasiswa') {
    menu.push({ 
      name: 'Kirim CV', 
      href: '/mahasiswa/kirim-cv', 
      icon: DocumentIcon, 
      current: currentPath.includes('kirim-cv') 
    });
    menu.push({ 
      name: 'Logbook', 
      href: '/mahasiswa/logbook', 
      icon: ClipboardIcon, 
      current: currentPath.includes('logbook') 
    });
  }

  // 3. Tambahan menu KHUSUS DOSEN PEMBIMBING
  if (role.value === 'dosen_pembimbing') {
    menu.push({ 
      name: 'Review Laporan', 
      href: '/dosen-pembimbing/review-laporan', 
      icon: CheckIcon, 
      current: currentPath.includes('review-laporan') 
    });
  }

  // 4. Tambahan menu KHUSUS INDUSTRI
  if (role.value === 'supervisor_industri') {
    menu.push({ 
      name: 'Review CV', 
      href: '/industri/seleksi-cv', 
      icon: DocumentIcon, 
      current: currentPath.includes('seleksi-cv') 
    });
    menu.push({ 
      name: 'Persetujuan Logbook', 
      href: '/industri/persetujuan-logbook', 
      icon: CheckIcon, 
      current: currentPath.includes('persetujuan-logbook') 
    });
  }

  return menu;
});

const userRoleLabel = computed(() => {
  const r = role.value;
  if (!r) return 'Guest';
  return r.split('_').join(' ');
});
</script>
