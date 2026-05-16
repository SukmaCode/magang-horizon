<template>
    <AuthenticatedLayout>
        <Head title="Internship Evaluation" />
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Internship Evaluation</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Evaluasi internship mahasiswa bimbingan Anda.</p>
        </div>

        <!-- Empty State -->
        <div v-if="magangs.length === 0" class="bg-card rounded-md border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Mahasiswa</p>
            <p class="text-xs font-jakarta text-text-secondary">Tidak ada mahasiswa bimbingan yang tersedia untuk dievaluasi.</p>
        </div>

        <!-- Mahasiswa List -->
        <div v-else class="space-y-4">
            <CardContainer v-for="m in magangs" :key="m.id">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-sm font-jakartaSemiBold text-text-primary">{{ m.mahasiswa.nama_lengkap }}</h3>
                            <span v-if="m.evaluation" :class="badgeClass(m.evaluation.status)" class="text-[10px] font-jakartaSemiBold inline-flex items-center gap-1 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full" :class="dotClass(m.evaluation.status)"></span>
                                {{ m.evaluation.status_label }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs text-text-secondary">
                            <div>
                                <span class="font-jakarta">NIM: </span>
                                <span class="font-jakartaSemiBold">{{ m.mahasiswa.nim }}</span>
                            </div>
                            <div>
                                <span class="font-jakarta">Prodi: </span>
                                <span class="font-jakartaSemiBold">{{ m.mahasiswa.prodi || '-' }}</span>
                            </div>
                            <div>
                                <span class="font-jakarta">Perusahaan: </span>
                                <span class="font-jakartaSemiBold">{{ m.industri.nama_perusahaan }}</span>
                            </div>
                            <div v-if="m.evaluation">
                                <span class="font-jakarta">Score:</span>
                                <span :class="parseFloat(m.evaluation.overall_score) >= 50 ? 'text-success' : 'text-danger'" class="font-jakartaSemiBold ml-1">
                                    {{ parseFloat(m.evaluation.overall_score).toFixed(2) }}
                                </span>
                                <span class="mx-1">·</span>
                                <span :class="m.evaluation.pass_status === 'pass' ? 'text-success' : 'text-danger'" class="font-jakartaSemiBold uppercase">
                                    {{ m.evaluation.pass_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0">
                        <Link :href="`/dosen-pembimbing/internship-evaluation/${m.id}/create`" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-jakartaSemiBold rounded-md transition-colors" :class="m.evaluation ? 'border border-gray-200 text-text-primary hover:bg-gray-50' : 'bg-primary text-white hover:bg-primary-hover'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            {{ m.evaluation ? 'Lihat / Edit' : 'Evaluasi' }}
                        </Link>
                    </div>
                </div>
            </CardContainer>
        </div>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">{{ flashMsg }}</div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    magangs: { type: Array, default: () => [] },
});

function badgeClass(s) {
    return {
        'bg-gray-100 text-gray-600': s === 'draft',
        'bg-amber-50 text-amber-700': s === 'submitted',
        'bg-emerald-50 text-emerald-700': s === 'finalized',
    };
}
function dotClass(s) {
    return {
        'bg-gray-400': s === 'draft',
        'bg-amber-500': s === 'submitted',
        'bg-emerald-500': s === 'finalized',
    };
}
</script>
