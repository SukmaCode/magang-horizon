<template>
    <AuthenticatedLayout>
        <Head title="Monitoring Logbook" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Monitoring Logbook</h1>
            <p class="text-sm text-text-secondary mt-1">Pantau kegiatan harian mahasiswa bimbingan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Mahasiswa List -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-card rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-jakartaSemiBold text-text-primary">Mahasiswa Bimbingan</h2>
                    </div>
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-50">
                        <div v-if="magangs.length === 0" class="p-4 text-center">
                            <p class="text-xs text-text-secondary">Tidak ada mahasiswa.</p>
                        </div>
                        <template v-else>
                            <Link
                                v-for="m in magangs"
                                :key="m.id"
                                :href="`/dosen-pembimbing/monitoring-logbook?magang_id=${m.id}`"
                                preserve-scroll
                                :class="['block p-4 transition-colors', selectedMagangId === m.id ? 'bg-primary/5 border-l-4 border-primary' : 'hover:bg-gray-50 border-l-4 border-transparent']"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-jakartaSemiBold text-text-primary truncate">{{ m.nama_lengkap }}</p>
                                        <p class="text-xs text-text-secondary">{{ m.nim }}</p>
                                    </div>
                                    <span class="text-xs font-jakartaSemiBold text-text-secondary shrink-0">{{ m.total_logbook }} Hari</span>
                                </div>
                            </Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main: Logbook List -->
            <div class="lg:col-span-3">
                <CardContainer v-if="selectedMagangId" class="overflow-hidden">
                    <div class="border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Riwayat Logbook</h2>
                    </div>

                    <div v-if="logbooks.data && logbooks.data.length > 0" class="divide-y divide-gray-50">
                        <div v-for="log in logbooks.data" :key="log.id" class="py-6">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-jakartaSemiBold text-text-primary">{{ log.tanggal }}</span>
                                <span :class="['text-xs px-2 py-0.5 rounded-full font-jakartaSemiBold', presensiColor(log.status_presensi)]">
                                    {{ log.status_presensi_label }}
                                </span>
                                <span v-if="log.is_approved" class="text-xs px-2 py-0.5 rounded-full font-jakartaSemiBold bg-success/10 text-success">
                                    Disetujui Industri
                                </span>
                                <span v-else class="text-xs px-2 py-0.5 rounded-full font-jakartaSemiBold bg-amber-50 text-amber-600">
                                    Menunggu Industri
                                </span>
                            </div>
                            <p class="text-sm text-text-primary leading-relaxed whitespace-pre-wrap">{{ log.kegiatan }}</p>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Belum Ada Kegiatan</h3>
                        <p class="text-sm text-text-secondary">Mahasiswa belum mengisi logbook.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="logbooks.links && logbooks.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex justify-center gap-1">
                        <template v-for="link in logbooks.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200" :class="[link.active ? 'bg-primary text-white font-jakartaSemiBold' : 'text-text-secondary hover:bg-gray-100']" v-html="link.label" preserve-scroll />
                            <span v-else class="px-3.5 py-2 text-sm text-gray-300" v-html="link.label" />
                        </template>
                    </div>
                </CardContainer>
                
                <div v-else class="h-full flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl border border-dashed border-gray-200 min-h-[300px]">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                    <p class="text-sm text-text-secondary">Pilih mahasiswa di samping untuk melihat logbook.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    magangs: Array,
    logbooks: Object,
    selectedMagangId: Number,
});

function presensiColor(status) {
    return {
        hadir: 'bg-success/10 text-success',
        izin: 'bg-blue-50 text-blue-600',
        sakit: 'bg-amber-50 text-amber-600',
    }[status] || 'bg-gray-100 text-gray-600';
}
</script>
