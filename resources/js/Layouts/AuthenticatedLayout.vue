<template>
    <div class="min-h-screen bg-surface">
        <!-- Sidebar Component (fixed, never scrolls) -->
        <AppSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

        <!-- Main Content (offset by sidebar width on desktop) -->
        <div class="md:ml-64 flex flex-col h-screen">
            <!-- Header (sticky at top) -->
            <header class="bg-surface shadow-sm z-10 shrink-0">
                <div
                    class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
                >
                    <div class="flex items-center gap-4">
                        <!-- Hamburger Bar -->
                        <button
                            @click="isSidebarOpen = true"
                            class="md:hidden p-2 -ml-2 text-text-secondary hover:bg-gray-50 rounded-lg"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-4">
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                stroke="#8B0000"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                />
                            </svg>
                        </div>
                        <div class="hidden sm:flex flex-col items-end">
                            <span
                                class="text-sm font-jakartaSemiBold text-text-primary"
                            >
                                {{ user.nama_lengkap || user.nama_perusahaan || user.nama_dosen}}
                                <span v-if="user.prodi || user.kontak_person"
                                    >|</span
                                >
                                {{ user.prodi || user.kontak_person }}</span
                            >
                            <span
                                class="text-xs font-jakartaSemiBold text-primary capitalize"
                                >{{ userRoleLabel }}</span
                            >
                        </div>
                        <div v-if="user.profile_photo_url"
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary/20 shadow-sm"
                        >
                            <a :href="`/mahasiswa/profil`">
                                <img :src="user.profile_photo_url" alt="Foto Profil" class="w-full h-full object-cover" />
                            </a>
                        </div>
                        <div v-else
                            class="w-10 h-10 rounded-full bg-primary/10 border-2 border-primary/20 flex items-center justify-center text-primary font-jakartaSemiBold"
                        >
                            <a :href="`/mahasiswa/profil`">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content (scrollable independently) -->
            <main class="flex-1 overflow-y-auto bg-surface p-4 lg:p-6">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import AppSidebar from "@/Components/AppSidebar.vue";

const isSidebarOpen = ref(false);
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const role = computed(() => user.value?.role);

const userRoleLabel = computed(() => {
    const r = role.value;
    if (!r) return "Guest";
    return r.split("_").join(" ");
});
</script>
