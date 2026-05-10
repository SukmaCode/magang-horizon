<template>
    <AuthenticatedLayout>
        <Head title="Hasil Evaluasi Magang" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Hasil Evaluasi Magang</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Lihat hasil penilaian evaluasi dari Supervisor Industri.</p>
        </div>

        <!-- Has Evaluation Data -->
        <template v-if="evaluationData">
            <!-- Status Banner -->
            <div v-if="evaluationData.evaluation.can_download" class="mb-6 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-md">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="flex-1">
                    <p class="text-sm text-emerald-700 font-jakartaSemiBold">Evaluasi telah difinalisasi pada {{ evaluationData.evaluation.finalized_at }}.</p>
                </div>
                <a :href="`/evaluation-report/${evaluationData.magang.id}/download`" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-jakartaSemiBold text-white bg-success rounded-md hover:bg-emerald-600 transition-colors shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Download PDF
                </a>
            </div>

            <div v-else class="mb-6 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-md">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm text-amber-700 font-jakartaSemiBold">Evaluasi masih dalam status <strong>{{ evaluationData.evaluation.status_label }}</strong>. PDF dapat diunduh setelah evaluasi difinalisasi oleh Supervisor Industri.</p>
            </div>

            <!-- Internship Period Info -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Informasi Magang</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs font-jakarta text-text-secondary mb-1">Tanggal Evaluasi</p>
                            <p class="text-sm font-jakartaSemiBold text-text-primary">{{ evaluationData.evaluation.tanggal_evaluasi || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-jakarta text-text-secondary mb-1">Periode Magang</p>
                            <p class="text-sm font-jakartaSemiBold text-text-primary">{{ evaluationData.magang.tanggal_mulai || '-' }} — {{ evaluationData.magang.tanggal_selesai || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-jakarta text-text-secondary mb-1">Status</p>
                            <span :class="statusBadgeClass(evaluationData.evaluation.status)" class="text-xs rounded-full font-jakartaSemiBold inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :class="statusDotClass(evaluationData.evaluation.status)"></span>
                                {{ evaluationData.evaluation.status_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </CardContainer>

            <!-- Scores Table -->
            <CardContainer class="mb-6">
                <div class="pb-6 border-b border-gray-100">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Tabel Penilaian</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="text-left text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider w-12">No</th>
                                <th class="px-4 text-left text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Komponen Penilaian</th>
                                <th class="text-center text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider w-32">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(score, index) in evaluationData.scores" :key="score.komponen" class="hover:bg-gray-50/50 transition-colors">
                                <td class="text-sm font-jakartaSemiBold text-text-secondary">{{ index + 1 }}</td>
                                <td class="px-4 py-4 text-sm font-jakartaSemiBold text-text-primary">{{ score.label }}</td>
                                <td class="px-4 py-4 text-right">
                                    <span v-if="score.nilai !== null" class="text-lg font-jakartaSemiBold" :class="getScoreColor(score.nilai)">{{ Number(score.nilai).toFixed(2) }}</span>
                                    <span v-else class="text-sm font-jakarta text-gray-400 italic">-</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary/5 border-t-2 border-primary/20">
                                <td colspan="2" class="px-6 text-sm font-jakartaSemiBold text-text-primary">Nilai Akhir</td>
                                <td class="px-6 py-4 text-right flex items-center justify-between">
                                    <span class="text-lg font-jakartaSemiBold text-primary" :class="getScoreColor(evaluationData.evaluation.nilai_akhir)">{{ Number(evaluationData.evaluation.nilai_akhir).toFixed(2) }}</span>
                                    <span class="text-sm font-jakartaSemiBold text-text-secondary">/100</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </CardContainer>

            <!-- Supervisor Notes -->
            <CardContainer v-if="evaluationData.evaluation.catatan_supervisor" class="mb-6">
                <div class="border-b border-gray-100">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Catatan Supervisor</h2>
                </div>
                <div class="pt-6">
                    <p class="text-sm font-jakarta text-text-primary leading-relaxed whitespace-pre-line">{{ evaluationData.evaluation.catatan_supervisor }}</p>
                </div>
            </CardContainer>
        </template>

        <!-- No Evaluation Yet -->
        <template v-else>
            <CardContainer>
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Evaluasi Belum Tersedia</h3>
                    <p class="text-sm font-jakarta text-text-secondary max-w-md mx-auto">Supervisor Industri belum memberikan evaluasi untuk magang Anda. Silakan tunggu proses penilaian selesai.</p>
                </div>
            </CardContainer>
        </template>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    evaluationData: { type: Object, default: null },
});

function statusBadgeClass(status) {
    return {
        'text-gray-700': status === 'draft',
        'text-amber-700': status === 'submitted',
        'text-emerald-700': status === 'finalized',
    };
}

function statusDotClass(status) {
    return {
        'bg-gray-400': status === 'draft',
        'bg-amber-500': status === 'submitted',
        'bg-emerald-500': status === 'finalized',
    };
}

function getScoreColor(nilai) {
    if (nilai >= 80) return 'text-success';
    if (nilai >= 60) return 'text-primary';
    if (nilai >= 40) return 'text-amber-600';
    return 'text-danger';
}
</script>
