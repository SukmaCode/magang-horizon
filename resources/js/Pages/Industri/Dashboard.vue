<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Industri" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">
                Dashboard Industri
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Kelola permohonan magang dan pantau aktivitas peserta magang Anda.
            </p>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <CardContainer padding="p-6" class="border-l-4 border-l-accent">
                <h3 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Review CV Menunggu</h3>
                <p class="text-2xl font-jakartaSemiBold text-text-primary">{{ pendingCount }}</p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">Permohonan mahasiswa baru</p>
                <Link v-if="pendingCount > 0" :href="url('/industri/seleksi-cv')" class="inline-flex items-center gap-1 text-xs text-primary font-jakartaSemiBold mt-3 hover:text-primary-hover transition-colors">
                    Review Sekarang
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </Link>
            </CardContainer>

            <CardContainer padding="p-6">
                <h3 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Peserta Aktif</h3>
                <p class="text-2xl font-jakartaSemiBold text-text-primary">{{ activeStudents }} Mahasiswa</p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">Sedang melaksanakan magang</p>
            </CardContainer>

            <CardContainer padding="p-6">
                <h3 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Persetujuan Absensi</h3>
                <p class="text-2xl font-jakartaSemiBold" :class="pendingLogbooks > 0 ? 'text-accent' : 'text-success'">{{ pendingLogbooks }}</p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">Menunggu konfirmasi Anda</p>
                <Link v-if="pendingLogbooks > 0" :href="url('/industri/persetujuan-logbook')" class="inline-flex items-center gap-1 text-xs text-primary font-jakartaSemiBold mt-3 hover:text-primary-hover transition-colors">
                    Review Logbook
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </Link>
            </CardContainer>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <Link :href="url('/industri/seleksi-cv')" class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-jakartaSemiBold text-text-primary">Seleksi CV</span>
            </Link>

            <Link :href="url('/industri/agreement')" class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <span class="text-xs font-jakartaSemiBold text-text-primary">Agreement</span>
            </Link>

            <Link :href="url('/industri/persetujuan-logbook')" class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group">
                <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center group-hover:bg-success/20 transition-colors">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-jakartaSemiBold text-text-primary">Logbook</span>
            </Link>

            <Link :href="url('/industri/input-nilai')" class="flex flex-col items-center gap-2 p-4 bg-card rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-md transition-all duration-200 group">
                <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <span class="text-xs font-jakartaSemiBold text-text-primary">Input Nilai</span>
            </Link>
        </div>

        <!-- Recent Applications & Signature -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <CardContainer padding="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-jakartaSemiBold text-text-primary">Lamaran Terbaru</h2>
                        <Link :href="url('/industri/seleksi-cv')" class="text-sm text-primary font-jakartaSemiBold hover:text-primary-hover transition-colors">Lihat Semua</Link>
                    </div>

                    <div v-if="recentApplications.length > 0" class="space-y-3">
                        <div v-for="app in recentApplications" :key="app.id" class="flex items-center justify-between gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary text-sm font-jakartaSemiBold shrink-0">
                                    {{ app.mahasiswa.nama_lengkap.charAt(0) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-jakartaSemiBold text-text-primary truncate">{{ app.mahasiswa.nama_lengkap }}</p>
                                    <p class="text-xs font-jakarta text-text-secondary">{{ app.mahasiswa.nim }} · {{ app.mahasiswa.prodi }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-jakarta text-text-secondary">{{ app.created_at }}</span>
                                <span :class="['text-xs px-2.5 py-0.5 rounded-full font-jakartaSemiBold', statusBadge(app.status)]">{{ app.status_label }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="text-sm font-jakartaSemiBold text-text-primary">Belum Ada Aktivitas</h3>
                        <p class="text-xs text-text-secondary mt-1">Belum ada peserta magang atau permohonan masuk.</p>
                    </div>
                </CardContainer>
            </div>
            
            <div class="lg:col-span-1">
                <SignatureUpload :has-signature="hasSignature" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
import SignatureUpload from '@/Components/SignatureUpload.vue';
import { url } from '@/utils/prefix';

defineProps({
    pendingCount: { type: Number, default: 0 },
    activeStudents: { type: Number, default: 0 },
    pendingLogbooks: { type: Number, default: 0 },
    recentApplications: { type: Array, default: () => [] },
    hasSignature: { type: Boolean, default: false },
});

function statusBadge(status) {
    return {
        pending: 'bg-amber-50 text-amber-700',
        diterima: 'bg-success/10 text-success',
        ditolak: 'bg-danger/10 text-danger',
    }[status] || 'bg-gray-100 text-gray-600';
}
</script>
