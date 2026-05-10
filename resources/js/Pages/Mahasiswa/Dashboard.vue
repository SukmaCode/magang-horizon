<template>
    <AuthenticatedLayout>
        <Head title="Mahasiswa Dashboard" />

        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">
                Selamat Datang, {{ user.nama_lengkap || user.username }}
            </h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">
                Pantau progres magang dan kegiatan harian Anda di sini.
            </p>
        </div>

        <h2 class="mb-4 text-md font-jakartaSemiBold text-text-primary">Informasi Magang</h2>
        <!-- Status Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Status Magang -->
            <CardContainer padding="p-4" class="border-l-4 border-l-primary">
                <h3 class="text-sm font-jakartaBold text-primary uppercase tracking-wider mb-2">
                    Status Magang
                </h3>
                <p class="text-xl font-jakartaSemiBold text-text-primary">
                    {{ statusMagang }}
                </p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">
                    {{ statusDescription }}
                </p>
            </CardContainer>

            <!-- Total Kehadiran -->
            <CardContainer padding="p-4" class="border-l-4 border-l-primary">
                <h3 class="text-sm font-jakartaBold text-primary uppercase tracking-wider mb-2">
                    Total Kehadiran
                </h3>
                <div class="flex items-end gap-2">
                    <p class="text-xl font-jakartaSemiBold text-text-primary">
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
                    <p class="text-xs font-jakarta text-text-secondary mt-1">
                        Dari target {{ logbookStats.target }} hari
                    </p>
                </div>
            </CardContainer>
            

            <!-- Pengajuan CV -->
            <CardContainer padding="p-4" class="border-l-4 border-l-primary">
                <h3 class="text-sm font-jakartaBold text-primary uppercase tracking-wider mb-2">
                    Pengajuan CV
                </h3>
                <p class="text-xl font-jakartaSemiBold text-text-primary">
                    {{ pendaftaranCount }} / 3
                </p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">
                    Kuota lamaran aktif
                </p>
            </CardContainer>
        </div>

        <!-- Quick Actions -->
         <h2 class="mb-4 text-md font-jakartaSemiBold text-text-primary">Aksi Cepat</h2>
        <div v-if="hasMagang" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <CardContainer
                v-for="(item, index) in quickAction"
                :key="index"
                padding="p-0"
            >
                <!-- Use native <a> for download links, Inertia <Link> for SPA navigation -->
                <a
                    v-if="item.isDownload"
                    :href="item.href"
                    target="_blank"
                    class="flex flex-col items-center gap-4 p-6"
                >
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <span v-html="item.svg"></span>
                    </div>
                    <span class="text-xs font-jakartaSemiBold text-center text-text-primary">{{ item.label }}</span>
                </a>
                <Link
                    v-else
                    :href="item.href"
                    class="flex flex-col items-center gap-4 p-6"
                >
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                        <span v-html="item.svg"></span>
                    </div>
                    <span class="text-xs font-jakartaSemiBold text-center text-text-primary">{{ item.label }}</span>
                </Link>
            </CardContainer>
        </div>

        <h2 class="mb-4 text-md font-jakartaSemiBold text-text-primary">Lainnya</h2>
        <!-- Recent Activity -->
        <CardContainer padding="p-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-jakartaBold text-text-primary">
                    Aktivitas Terakhir
                </h2>
                <Link
                    v-if="recentLogbooks.length > 0"
                    href="/mahasiswa/logbook"
                    class="text-sm text-primary font-jakartaSemiBold hover:text-primary-hover transition-colors"
                >
                    Lihat Semua
                </Link>
            </div>

            <!-- Has Activities -->
            <div v-if="recentLogbooks.length > 0" class="space-y-3">
                <div
                    v-for="log in recentLogbooks"
                    :key="log.id"
                    class="flex items-center gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-100 hover:border-gray-300 transition-colors"
                >
                    <div class="shrink-0 mt-0.5">
                        <div
                            :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center text-xs font-jakartaSemiBold',
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
                        <div class="flex items-center mb-1">
                            <span class="text-xs font-jakartaSemiBold text-text-secondary">{{ log.tanggal }}</span>
                            <span
                                :class="[
                                    'text-sm font-jakartaSemiBold',
                                    log.status_presensi === 'Hadir'
                                        ? ' text-success'
                                        : log.status_presensi === 'Izin'
                                            ? ' text-blue-600'
                                            : ' text-amber-600'
                                ]"
                            >
                                {{ log.status_presensi }}
                            </span>
                        </div>
                        <p class="text-sm font-jakarta text-text-primary line-clamp-2">{{ log.kegiatan }}</p>
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
                <h3 class="text-sm font-jakartaSemiBold text-text-primary">
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
            <div v-if="flash.success" class="fixed bottom-6 right-6 bg-success text-white px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50">
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
            <div v-if="flash.error" class="fixed bottom-6 right-6 bg-danger text-white px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50">
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
    magang: { type: Object, default: () => ({}) }
});

const quickAction = computed(() => {
    if (!props.hasMagang) {
        return [];
    }
    
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();

    const actions = [
        {
            icon: 'book-open',
            label: 'Agreement',
            href: '/mahasiswa/agreement',
            svg : `<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>`
        },
        {
            icon: 'clipboard-list',
            label: 'Laporan Akhir',
            href: `/mahasiswa/laporan-akhir?month=${currentMonth}&year=${currentYear}`,
            svg : `<div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>`
        },
        {
            icon: 'certificate',
            label: 'Sertifikat',
            href: '/mahasiswa/sertifikat',
            svg : `<div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center group-hover:bg-success/20 transition-colors">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>`
        },
        {
            icon: 'document-text',
            label: 'Kirim CV',
            href: '/mahasiswa/kirim-cv',
            svg : `<div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>`
        },
    ];

    // Only show download button when completion letter data has been filled by industri
    if (props.magang?.has_completion_letter) {
        actions.push({
            icon: 'download',
            label: 'Internship Completion Letter',
            href: `/completion-letter/${props.magang.id}/download`,
            isDownload: true,
            svg : `<div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>`
        });
    }

    return actions;
});
</script>
