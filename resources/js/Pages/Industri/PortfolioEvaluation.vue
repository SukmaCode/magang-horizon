<template>
    <AuthenticatedLayout>
        <Head title="Portfolio Evaluation" />
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Portfolio Evaluation</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Evaluasi portfolio mahasiswa magang berdasarkan rubric scoring sheet.</p>
        </div>
        <div v-if="!filteredMagangs || filteredMagangs.length === 0" class="bg-card rounded-md border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Mahasiswa</p>
            <p class="text-xs font-jakarta text-text-secondary">Tidak ada mahasiswa magang yang dapat dievaluasi.</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div v-for="m in filteredMagangs" :key="m.id" class="bg-card rounded-md border border-gray-100 hover:border-primary/30 hover:shadow-sm transition-all duration-200 p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="min-w-0">
                        <p class="text-sm font-jakartaSemiBold text-text-primary truncate">{{ m.mahasiswa.nama_lengkap }}</p>
                        <p class="text-xs font-jakarta text-text-secondary mt-0.5">{{ m.mahasiswa.nim }}</p>
                        <p class="text-[10px] font-jakarta text-text-secondary mt-1">{{ m.industri.nama_perusahaan }}</p>
                    </div>
                    <span v-if="m.evaluation" :class="badgeClass(m.evaluation.status)" class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-jakartaSemiBold shrink-0">
                        <span class="w-1 h-1 rounded-full" :class="dotClass(m.evaluation.status)"></span>
                        {{ m.evaluation.status_label }}
                    </span>
                </div>
                <div v-if="m.evaluation" class="space-y-2 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider">Score</span>
                        <span class="text-sm font-jakartaSemiBold" :class="parseFloat(m.evaluation.overall_score) >= 50 ? 'text-success' : 'text-danger'">{{ parseFloat(m.evaluation.overall_score).toFixed(2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider">Result</span>
                        <span class="text-[10px] font-jakartaSemiBold" :class="m.evaluation.qualification_result === 'qualified' ? 'text-success' : 'text-danger'">{{ m.evaluation.qualification_result === 'qualified' ? 'Qualified' : 'Not Qualified' }}</span>
                    </div>
                </div>
                <Link :href="`${routePrefix}/portfolio-evaluation/${m.id}/create`" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-jakartaSemiBold rounded-md transition-colors duration-200 cursor-pointer" :class="m.evaluation ? 'text-primary bg-primary/10 hover:bg-primary/20' : 'text-white bg-primary hover:bg-primary-hover'">
                    {{ m.evaluation ? 'Edit Evaluasi' : 'Mulai Evaluasi' }}
                </Link>
            </div>
        </div>
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">{{ flashMsg }}</div>
        </Transition>
    </AuthenticatedLayout>
</template>
<script setup>
import { computed } from 'vue';
import { usePage, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');
const props = defineProps({
    magangs: { type: Array, default: () => [] },
    statusTahapan: { type: String, default: '' },
});

const filteredMagangs = computed(() => {
    return props.magangs.filter(m => m.status_tahapan === props.statusTahapan);
});
const routePrefix = '/industri';
function badgeClass(s) { return { 'bg-gray-100 text-gray-600': s==='draft', 'bg-warning/10 text-warning': s==='submitted', 'bg-success/10 text-success': s==='finalized' }; }
function dotClass(s) { return { 'bg-gray-400': s==='draft', 'bg-warning': s==='submitted', 'bg-success': s==='finalized' }; }
</script>
