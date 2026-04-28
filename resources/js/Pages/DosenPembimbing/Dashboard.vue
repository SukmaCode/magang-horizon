<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Dosen Pembimbing" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Dashboard Dosen Pembimbing</h1>
            <p class="text-sm text-text-secondary mt-1">Pantau perkembangan mahasiswa bimbingan Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <CardContainer padding="p-6">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Mahasiswa Bimbingan</h3>
                <p class="text-2xl font-bold text-text-primary font-jakarta">{{ activeStudents }} Mahasiswa</p>
                <p class="text-xs text-text-secondary mt-2">Sedang melaksanakan magang</p>
            </CardContainer>

            <CardContainer padding="p-6" :class="pendingLaporan > 0 ? 'border-l-4 border-l-accent' : ''">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Laporan Perlu Review</h3>
                <p class="text-2xl font-bold text-text-primary font-jakarta">{{ pendingLaporan }}</p>
                <p class="text-xs text-text-secondary mt-2">Menunggu persetujuan Anda</p>
            </CardContainer>

            <CardContainer padding="p-6" :class="studentsToGrade > 0 ? 'border-l-4 border-l-danger' : ''">
                <h3 class="text-sm font-semibold text-text-secondary uppercase tracking-wider mb-2">Belum Dinilai</h3>
                <p class="text-2xl font-bold text-text-primary font-jakarta">{{ studentsToGrade }} Mahasiswa</p>
                <p class="text-xs text-text-secondary mt-2">Perlu input nilai akademis</p>
            </CardContainer>
        </div>

        <CardContainer padding="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-text-primary font-jakarta">Laporan Akhir Terbaru</h2>
                <Link href="/dosen-pembimbing/review-laporan" class="text-sm text-primary font-semibold hover:text-primary-hover">Lihat Semua</Link>
            </div>

            <div v-if="recentLaporan.length > 0" class="space-y-3">
                <div v-for="laporan in recentLaporan" :key="laporan.id" class="flex items-center justify-between gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            {{ laporan.mahasiswa.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-text-primary">{{ laporan.mahasiswa }}</p>
                            <p class="text-xs text-text-secondary">{{ laporan.nim }} · {{ laporan.updated_at }}</p>
                        </div>
                    </div>
                    <div>
                        <span :class="['text-xs px-2.5 py-1 rounded-full font-medium', statusBadge(laporan.status)]">
                            {{ laporan.status_label }}
                        </span>
                    </div>
                </div>
            </div>
            <div v-else class="p-8 text-center text-text-secondary text-sm bg-gray-50 rounded-xl border border-dashed">
                Belum ada laporan akhir yang diupload mahasiswa bimbingan.
            </div>
        </CardContainer>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    activeStudents: Number,
    pendingLaporan: Number,
    studentsToGrade: Number,
    recentLaporan: Array,
});

function statusBadge(status) {
    return {
        pending: 'bg-amber-50 text-amber-700',
        disetujui: 'bg-success/10 text-success',
        revisi: 'bg-danger/10 text-danger',
    }[status] || 'bg-gray-100 text-gray-600';
}
</script>
