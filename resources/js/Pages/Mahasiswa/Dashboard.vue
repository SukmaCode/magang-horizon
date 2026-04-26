<template>
    <AuthenticatedLayout>
        <Head title="Mahasiswa Dashboard" />

        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">
                Selamat Datang, {{ user.nama_lengkap || user.username }}
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Pantau progres magang dan kegiatan harian Anda di sini.
            </p>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Status Magang -->
            <CardContainer padding="p-6" class="border-l-4 border-l-primary">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">
                    Status Magang
                </h3>
                <p class="text-xl font-bold text-text-primary font-jakarta">
                    {{ statusMagang }}
                </p>
                <p class="text-xs text-text-secondary mt-2">
                    {{ statusDescription }}
                </p>
            </CardContainer>

            <!-- Total Kehadiran -->
            <CardContainer padding="p-6">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">
                    Total Kehadiran
                </h3>
                <div class="flex items-end gap-2">
                    <p class="text-xl font-bold text-text-primary font-jakarta">
                        {{ logbookStats.total }} Hari
                    </p>
                    <span v-if="logbookStats.pending > 0" class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full mb-0.5">
                        {{ logbookStats.pending }} pending
                    </span>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div
                            class="bg-primary h-2 rounded-full transition-all duration-500"
                            :style="{ width: Math.min((logbookStats.total / logbookStats.target) * 100, 100) + '%' }"
                        ></div>
                    </div>
                    <p class="text-xs text-text-secondary mt-1">
                        Dari target {{ logbookStats.target }} hari
                    </p>
                </div>
            </CardContainer>

            <!-- Pengajuan CV -->
            <CardContainer padding="p-6">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">
                    Pengajuan CV
                </h3>
                <p class="text-xl font-bold text-primary font-jakarta">
                    {{ pendaftaranCount }} / 3
                </p>
                <p class="text-xs text-text-secondary mt-2">
                    Kuota lamaran aktif
                </p>
            </CardContainer>
        </div>

        <!-- Quick Actions -->
        <div v-if="hasMagang" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <Link
                href="/mahasiswa/logbook"
                class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group"
            >
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-text-primary">Logbook</span>
            </Link>

            <Link
                href="/mahasiswa/laporan-akhir"
                class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group"
            >
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-text-primary">Laporan Akhir</span>
            </Link>

            <Link
                href="/mahasiswa/sertifikat"
                class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group"
            >
                <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center group-hover:bg-success/20 transition-colors">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-text-primary">Sertifikat</span>
            </Link>

            <Link
                href="/mahasiswa/kirim-cv"
                class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group"
            >
                <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-text-primary">Kirim CV</span>
            </Link>
        </div>

        <!-- Recent Activity -->
        <CardContainer padding="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-text-primary font-jakarta">
                    Aktivitas Terakhir
                </h2>
                <Link
                    v-if="recentLogbooks.length > 0"
                    href="/mahasiswa/logbook"
                    class="text-sm text-primary font-semibold hover:text-primary-hover transition-colors"
                >
                    Lihat Semua
                </Link>
            </div>

            <!-- Has Activities -->
            <div v-if="recentLogbooks.length > 0" class="space-y-3">
                <div
                    v-for="log in recentLogbooks"
                    :key="log.id"
                    class="flex items-start gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-100 hover:border-gray-200 transition-colors"
                >
                    <div class="shrink-0 mt-0.5">
                        <div
                            :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold',
                                log.is_approved
                                    ? 'bg-success/10 text-success'
                                    : 'bg-amber-50 text-amber-600'
                            ]"
                        >
                            <svg v-if="log.is_approved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold text-text-secondary">{{ log.tanggal }}</span>
                            <span
                                :class="[
                                    'text-xs px-2 py-0.5 rounded-full font-medium',
                                    log.status_presensi === 'Hadir'
                                        ? 'bg-success/10 text-success'
                                        : log.status_presensi === 'Izin'
                                            ? 'bg-blue-50 text-blue-600'
                                            : 'bg-amber-50 text-amber-600'
                                ]"
                            >
                                {{ log.status_presensi }}
                            </span>
                        </div>
                        <p class="text-sm text-text-primary line-clamp-2">{{ log.kegiatan }}</p>
                    </div>
                </div>
            </div>

            <!-- No Activities -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200"
            >
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-sm font-bold text-text-primary">
                    Belum Ada Aktivitas
                </h3>
                <p class="text-xs text-text-secondary mt-1">
                    Anda belum memulai kegiatan magang apapun.
                </p>
            </div>
        </CardContainer>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.success" class="fixed bottom-6 right-6 bg-success text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.success }}
            </div>
        </Transition>
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.error" class="fixed bottom-6 right-6 bg-danger text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.error }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const flash = computed(() => page.props.flash || {});

// Props from controller
const props = defineProps({
    statusMagang: { type: String, default: 'Belum Dimulai' },
    statusDescription: { type: String, default: 'Menunggu penempatan industri' },
    logbookStats: {
        type: Object,
        default: () => ({ total: 0, approved: 0, pending: 0, target: 60 }),
    },
    pendaftaranCount: { type: Number, default: 0 },
    recentLogbooks: { type: Array, default: () => [] },
    hasMagang: { type: Boolean, default: false },
});
</script>
