<template>
    <AuthenticatedLayout>
        <Head title="Mahasiswa Dashboard" />

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-text-primary font-jakarta">
                Selamat Datang, {{ user.nama_lengkap || user.username }}
            </h1>
            <p class="text-text-secondary mt-1">
                Pantau progres magang dan kegiatan harian Anda di sini.
            </p>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <CardContainer padding="p-6" class="border-l-4 border-l-primary">
                <h3
                    class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Status Magang
                </h3>
                <p class="text-2xl font-bold text-text-primary font-jakarta">
                    Belum Dimulai
                </p>
                <p class="text-sm text-text-secondary mt-2">
                    Menunggu penempatan industri
                </p>
            </CardContainer>

            <CardContainer padding="p-6">
                <h3
                    class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Total Kehadiran
                </h3>
                <p class="text-2xl font-bold text-text-primary font-jakarta">
                    0 Hari
                </p>
                <p class="text-sm text-text-secondary mt-2">
                    Dari target 60 hari
                </p>
            </CardContainer>

            <CardContainer padding="p-6">
                <h3
                    class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Pengajuan CV
                </h3>
                <p class="text-2xl font-bold text-primary font-jakarta">
                    0 / 3
                </p>
                <p class="text-sm text-text-secondary mt-2">
                    Kuota lamaran aktif
                </p>
            </CardContainer>
        </div>

        <!-- Recent Activity Placeholder -->
        <CardContainer padding="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-text-primary font-jakarta">
                    Aktivitas Terakhir
                </h2>
                <button
                    class="text-sm text-primary font-semibold hover:text-primary-hover"
                >
                    Lihat Semua
                </button>
            </div>

            <div
                class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200"
            >
                <svg
                    class="w-12 h-12 text-gray-300 mb-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <h3 class="text-sm font-bold text-text-primary">
                    Belum Ada Aktivitas
                </h3>
                <p class="text-xs text-text-secondary mt-1">
                    Anda belum memulai kegiatan magang apapun.
                </p>
            </div>
        </CardContainer>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
</script>
