<template>
    <AuthenticatedLayout>
        <Head title="Hasil Portfolio Evaluation" />
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Hasil Portfolio Evaluation</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Hasil evaluasi portfolio magang Anda oleh supervisor.</p>
        </div>

        <!-- No Evaluation -->
        <div v-if="!evaluationData" class="bg-card rounded-md border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Evaluasi</p>
            <p class="text-xs font-jakarta text-text-secondary">Supervisor belum melakukan portfolio evaluation.</p>
        </div>

        <template v-else>
            <!-- Evaluation Info -->
            <CardContainer class="mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-base font-jakartaBold text-text-primary">Portfolio Evaluation</h2>
                        <p class="text-xs font-jakarta text-text-secondary mt-0.5">{{ ev.company_name }} · {{ ev.evaluation_date }}</p>
                    </div>
                    <span :class="badgeClass(ev.status)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-jakartaSemiBold self-start">
                        <span class="w-1.5 h-1.5 rounded-full" :class="dotClass(ev.status)"></span>
                        {{ ev.status_label }}
                    </span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div><p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Evaluator</p><p class="text-xs font-jakartaSemiBold text-text-primary">{{ ev.evaluator_name }}</p></div>
                    <div><p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Type</p><p class="text-xs font-jakartaSemiBold text-text-primary">{{ ev.evaluator_type === 'industry_supervisor' ? 'Supervisor Industri' : 'Dosen Pembimbing' }}</p></div>
                    <div v-if="ev.department"><p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Department</p><p class="text-xs font-jakartaSemiBold text-text-primary">{{ ev.department }}</p></div>
                    <div v-if="ev.finalized_at"><p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Finalized</p><p class="text-xs font-jakartaSemiBold text-text-primary">{{ ev.finalized_at }}</p></div>
                </div>
            </CardContainer>

            <!-- Scores Table -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaBold text-text-secondary uppercase tracking-wider">Rubric Scores</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-jakartaSemiBold text-text-secondary uppercase">Category</th>
                                <th class="text-left py-3 px-4 text-xs font-jakartaSemiBold text-text-secondary uppercase">Sub-Criteria</th>
                                <th class="text-left py-3 px-4 text-xs font-jakartaSemiBold text-text-secondary uppercase">Rating</th>
                                <th class="text-center py-3 px-4 text-xs font-jakartaSemiBold text-text-secondary uppercase">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="cat in evaluationData.scores" :key="cat.category">
                                <tr v-for="(item, idx) in cat.items" :key="`${cat.category}-${idx}`" class="border-b border-gray-50">
                                    <td v-if="idx === 0" :rowspan="cat.items.length" class="py-3 px-4 font-jakartaSemiBold text-text-primary align-middle">
                                        {{ cat.category_label }}<br>
                                        <span class="text-[10px] font-jakarta text-text-secondary">({{ cat.weight }}%)</span>
                                    </td>
                                    <td class="py-3 px-4 text-text-secondary text-xs">{{ item.sub_category_label || '—' }}</td>
                                    <td class="py-3 px-4 text-xs font-jakartaSemiBold text-text-primary">{{ item.rating_label || '-' }}</td>
                                    <td class="py-3 px-4 text-center font-jakartaSemiBold" :class="item.score > 0 ? 'text-primary' : 'text-gray-300'">{{ item.score }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </CardContainer>

            <!-- Overall Score & Qualification -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <CardContainer>
                    <div class="text-center py-4">
                        <p class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Overall Score</p>
                        <p class="text-4xl font-jakartaSemiBold" :class="parseFloat(ev.overall_score) >= 50 ? 'text-primary' : 'text-danger'">{{ parseFloat(ev.overall_score).toFixed(2) }}</p>
                        <p class="text-xs text-text-secondary mt-1">/ 100</p>
                    </div>
                </CardContainer>
                <CardContainer>
                    <div class="text-center py-4">
                        <p class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Qualification</p>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" :class="ev.qualification_result === 'qualified' ? 'bg-success/10' : 'bg-danger/10'">
                            <svg v-if="ev.qualification_result === 'qualified'" class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <svg v-else class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-lg font-jakartaSemiBold" :class="ev.qualification_result === 'qualified' ? 'text-success' : 'text-danger'">
                                {{ ev.qualification_result === 'qualified' ? 'Qualified' : 'Not Qualified' }}
                            </span>
                        </div>
                    </div>
                </CardContainer>
            </div>

            <!-- Comments -->
            <CardContainer v-if="ev.comments" class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Comments</h2>
                </div>
                <p class="text-sm font-jakarta text-text-primary whitespace-pre-line">{{ ev.comments }}</p>
            </CardContainer>

            <!-- Download PDF -->
            <div v-if="ev.can_download" class="flex justify-end">
                <a :href="`/mahasiswa/portfolio-evaluation/${evaluationData.magang.id}/pdf`" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download PDF Evaluasi
                </a>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const props = defineProps({
    evaluationData: { type: Object, default: null },
});

const ev = computed(() => props.evaluationData?.evaluation || {});

function badgeClass(s) { return { 'bg-gray-100 text-gray-600': s==='draft', 'bg-warning/10 text-warning': s==='submitted', 'bg-success/10 text-success': s==='finalized' }; }
function dotClass(s) { return { 'bg-gray-400': s==='draft', 'bg-warning': s==='submitted', 'bg-success': s==='finalized' }; }
</script>
