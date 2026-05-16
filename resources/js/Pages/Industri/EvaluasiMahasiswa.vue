<template>
    <AuthenticatedLayout>
        <Head title="Evaluasi Mahasiswa" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Evaluasi Mahasiswa Magang</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Berikan penilaian evaluasi terhadap mahasiswa yang telah menyelesaikan magang.</p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="border-b border-gray-100">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Daftar Mahasiswa</h2>
            </div>

            <div v-if="filteredMagangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in filteredMagangs" :key="magang.id" class="p-6 hover:bg-gray-50/50 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <!-- Student Info -->
                        <div class="flex-1">
                            <h3 class="text-base font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm font-jakarta text-text-secondary">{{ magang.mahasiswa.nim }} · {{ magang.mahasiswa.prodi }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold bg-primary/10 text-primary capitalize flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Tahap: {{ magang.status_tahapan_label }}
                                </span>
                                <span class="text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold bg-gray-100 text-text-secondary">
                                    {{ magang.approved_logbook }} / {{ magang.total_logbook }} Logbook
                                </span>
                            </div>
                        </div>

                        <!-- Evaluation Status & Actions -->
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <!-- Has Evaluation -->
                            <div v-if="magang.evaluation" class="text-right">
                                <p class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-1">Evaluasi</p>
                                <span :class="statusBadgeClass(magang.evaluation.status)" class="text-xs px-3 py-1.5 rounded-full font-jakartaSemiBold inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(magang.evaluation.status)"></span>
                                    {{ magang.evaluation.status_label }}
                                </span>
                                <div v-if="magang.evaluation.nilai_akhir" class="mt-2">
                                    <span class="text-2xl font-jakartaSemiBold text-success">{{ Number(magang.evaluation.nilai_akhir).toFixed(2) }}</span>
                                    <span class="text-sm font-jakartaSemiBold text-success">/ 100</span>
                                </div>
                            </div>
                            <div v-else class="text-right">
                                <span class="text-sm font-jakartaSemiBold text-amber-600">Belum Dievaluasi</span>
                            </div>

                            <Link :href="`/industri/evaluasi/${magang.id}`"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-jakartaSemiBold rounded-md transition-colors duration-200"
                                :class="magang.evaluation ? 'bg-gray-100 text-text-primary hover:bg-gray-200' : 'bg-primary text-white hover:bg-primary-hover shadow-sm'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ magang.evaluation ? 'Lihat / Edit' : 'Beri Evaluasi' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Tidak Ada Peserta</h3>
                <p class="text-sm font-jakarta text-text-secondary">Anda belum memiliki mahasiswa magang yang aktif.</p>
            </div>
        </CardContainer>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    magangs: { type: Array, default: () => [] },
    components: { type: Object, default: () => ({}) },
    statusTahapan: { type: String, default: '' },
});

const filteredMagangs = computed(() => {
    return props.magangs.filter(m => m.status_tahapan === props.statusTahapan);
});

function statusBadgeClass(status) {
    return {
        'bg-gray-100 text-gray-700': status === 'draft',
        'bg-amber-50 text-amber-700': status === 'submitted',
        'bg-emerald-50 text-emerald-700': status === 'finalized',
    };
}

function statusDotClass(status) {
    return {
        'bg-gray-400': status === 'draft',
        'bg-amber-500': status === 'submitted',
        'bg-emerald-500': status === 'finalized',
    };
}
</script>
