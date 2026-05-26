<template>
    <AuthenticatedLayout>
        <Head title="Form Evaluasi" />

        <!-- Back Button -->
        <div class="mb-4">
            <Link :href="url('/industri/evaluasi')" class="inline-flex font-jakartaSemiBold items-center gap-2 text-sm text-text-secondary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali ke Daftar
            </Link>
        </div>

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Form Evaluasi Mahasiswa</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Berikan penilaian untuk setiap komponen evaluasi (skala 0–100).</p>
        </div>

        <!-- Student Identity -->
        <CardContainer class="mb-6">
            <div class="border-b border-gray-100">
                <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Identitas Mahasiswa</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Nama Lengkap</p>
                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">NIM</p>
                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nim }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Program Studi</p>
                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.prodi }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Perusahaan</p>
                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.industri.nama_perusahaan }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Periode Magang</p>
                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.tanggal_mulai || '-' }} — {{ magang.tanggal_selesai || '-' }}</p>
                    </div>
                    <div v-if="evaluation">
                        <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Status Evaluasi</p>
                        <span :class="statusBadgeClass(evaluation.status)" class="text-xs font-jakartaSemiBold inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass(evaluation.status)"></span>
                            {{ evaluation.status_label }}
                        </span>
                    </div>
                </div>
            </div>
        </CardContainer>

        <!-- Evaluation Form -->
        <form @submit.prevent="submitForm">
            <!-- Scores Section -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100">
                    <h2 class="text-sm font-jakartaBold text-text-secondary uppercase tracking-wider">Komponen Penilaian</h2>
                    <p class="text-xs font-jakarta text-text-secondary mt-1">Masukkan nilai untuk setiap aspek evaluasi (0–100).</p>
                </div>
                <div class="p-6 space-y-5">
                    <div v-for="(label, key, idx) in components" :key="key" class="group">
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <div class="flex items-center gap-3 md:w-2/3">
                                <span class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-jakartaSemiBold flex items-center justify-center shrink-0">{{ idx + 1 }}</span>
                                <label :for="`score-${key}`" class="text-sm font-jakartaSemiBold text-text-primary">{{ label }}</label>
                            </div>
                            <div class="md:w-1/3 flex items-center gap-3">
                                <input
                                    :id="`score-${key}`"
                                    v-model.number="form.scores[key]"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    :disabled="isFinalized"
                                    class="w-full text-center text-lg font-jakartaSemiBold px-3 py-2.5 border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all disabled:bg-gray-50 disabled:text-gray-400"
                                    :class="{'border-danger': form.errors[`scores.${key}`]}"
                                    placeholder="0"
                                />
                                <span class="text-xs text-text-secondary shrink-0 w-8">/100</span>
                            </div>
                        </div>
                        <p v-if="form.errors[`scores.${key}`]" class="text-xs text-danger mt-1 md:ml-10">{{ form.errors[`scores.${key}`] }}</p>
                    </div>

                    <!-- Average Display -->
                    <div class="mt-6 pt-5 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-jakartaSemiBold text-text-primary">Nilai Rata-rata</span>
                            <div class="flex items-center gap-1">
                                <span class="text-lg font-jakartaSemiBold" :class="averageScore > 0 ? 'text-primary' : 'text-gray-300'">{{ averageScore.toFixed(2) }}</span>
                                <span class="text-sm font-jakartaSemiBold text-text-secondary">/ 100</span>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContainer>

            <!-- Supervisor Notes -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Catatan Supervisor</h2>
                </div>
                <div class="p-6">
                    <textarea
                        v-model="form.catatan_supervisor"
                        rows="4"
                        :disabled="isFinalized"
                        class="w-full px-4 py-3 text-sm font-jakarta border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none disabled:bg-gray-50 disabled:text-gray-400"
                        placeholder="Masukkan catatan evaluasi untuk mahasiswa (opsional)..."
                    ></textarea>
                </div>
            </CardContainer>

            <!-- Action Buttons -->
            <div v-if="!isFinalized" class="flex flex-col sm:flex-row gap-3 justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center cursor-pointer justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md border border-gray-200 bg-white text-text-primary hover:bg-gray-50 transition-colors"
                >
                    <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-gray-300 border-t-gray-600 rounded-full"></span>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                    Simpan Draft
                </button>

                <button
                    v-if="evaluation && evaluation.status === 'draft'"
                    type="button"
                    @click="submitEvaluation"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Submit Evaluasi
                </button>

                <button
                    v-if="evaluation && evaluation.status === 'submitted'"
                    type="button"
                    @click="showFinalizeConfirm = true"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md bg-success text-white hover:bg-emerald-600 transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Finalisasi Evaluasi
                </button>
            </div>

            <!-- Finalized Notice -->
            <div v-else class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-md">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm text-emerald-700 font-jakartaSemiBold">Evaluasi ini telah difinalisasi dan tidak dapat diubah. Mahasiswa sekarang dapat melihat dan mendownload hasil evaluasi.</p>
            </div>
        </form>

        <!-- Finalize Confirmation Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showFinalizeConfirm" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="showFinalizeConfirm = false">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-sm">
                    <div class="p-6 text-center">
                        <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                        </div>
                        <h3 class="text-lg font-jakartaSemiBold text-text-primary mb-2">Finalisasi Evaluasi?</h3>
                        <p class="text-sm font-jakarta text-text-secondary mb-6">Setelah difinalisasi, evaluasi <strong>tidak dapat diubah</strong>. Mahasiswa akan dapat melihat dan mendownload hasil evaluasi.</p>
                        <div class="flex gap-3">
                            <button @click="showFinalizeConfirm = false" class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold border border-gray-200 rounded-md hover:bg-gray-50">Batal</button>
                            <button @click="finalizeEvaluation" :disabled="form.processing" class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold text-white bg-success rounded-md hover:bg-emerald-600 flex justify-center items-center gap-2">
                                <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Ya, Finalisasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm, Link, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
import { url } from '@/utils/prefix';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    magang: { type: Object, required: true },
    evaluation: { type: Object, default: null },
    scores: { type: Object, default: () => ({}) },
    components: { type: Object, required: true },
});

const showFinalizeConfirm = ref(false);

const isFinalized = computed(() => props.evaluation?.status === 'finalized');

// Initialize form with existing scores or empty
const initialScores = {};
for (const key of Object.keys(props.components)) {
    initialScores[key] = props.scores[key] !== undefined ? Number(props.scores[key]) : '';
}

const form = useForm({
    scores: initialScores,
    catatan_supervisor: props.evaluation?.catatan_supervisor || '',
    tanggal_evaluasi: props.evaluation?.tanggal_evaluasi || new Date().toISOString().split('T')[0],
});

// Auto-calculate average
const averageScore = computed(() => {
    const values = Object.values(form.scores).filter(v => v !== '' && v !== null && !isNaN(v));
    if (values.length === 0) return 0;
    const sum = values.reduce((a, b) => a + Number(b), 0);
    return sum / Object.keys(props.components).length;
});

function submitForm() {
    form.post(url(`/industri/evaluasi/${props.magang.id}`), {
        preserveScroll: true,
    });
}

function submitEvaluation() {
    if (!props.evaluation?.id) return;
    router.post(url(`/industri/evaluasi/${props.evaluation.id}/submit`), {}, {
        preserveScroll: true,
    });
}

function finalizeEvaluation() {
    if (!props.evaluation?.id) return;
    showFinalizeConfirm.value = false;
    router.post(url(`/industri/evaluasi/${props.evaluation.id}/finalize`), {}, {
        preserveScroll: true,
    });
}

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
</script>
