<template>
    <AuthenticatedLayout>
        <Head title="Form Portfolio Evaluation" />
        <div class="mb-4">
            <Link :href="`${routePrefix}/portfolio-evaluation`" class="inline-flex font-jakartaSemiBold items-center gap-2 text-sm text-text-secondary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </Link>
        </div>
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Form Portfolio Evaluation</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Isi rubric evaluasi portfolio mahasiswa.</p>
        </div>

        <!-- Student Info -->
        <CardContainer class="mb-6">
            <div class="border-b border-gray-100 pb-3 mb-4">
                <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Informasi Mahasiswa</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Nama</p><p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</p></div>
                <div><p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">NIM</p><p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nim }}</p></div>
                <div><p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Perusahaan</p><p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.industri.nama_perusahaan }}</p></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                <div><p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Periode</p><p class="text-sm font-jakartaSemiBold text-text-primary">{{ magang.tanggal_mulai || '-' }} — {{ magang.tanggal_selesai || '-' }}</p></div>
                <div v-if="evaluation">
                    <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Status</p>
                    <span :class="badgeClass(evaluation.status)" class="text-xs font-jakartaSemiBold inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full" :class="dotClass(evaluation.status)"></span>
                        {{ evaluation.status_label }}
                    </span>
                </div>
            </div>
        </CardContainer>

        <form @submit.prevent="submitForm">
            <!-- Header Info -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaBold text-text-secondary uppercase tracking-wider">Evaluation Header</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Company / Agency *</label>
                        <input v-model="form.company_name" type="text" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" />
                        <p v-if="form.errors.company_name" class="text-xs text-danger mt-1">{{ form.errors.company_name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Department</label>
                        <input v-model="form.department" type="text" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" />
                    </div>
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Position</label>
                        <input v-model="form.position" type="text" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" />
                    </div>
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Date</label>
                        <input v-model="form.evaluation_date" type="date" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" />
                    </div>
                </div>
            </CardContainer>

            <!-- Rubric Sections -->
            <CardContainer v-for="cat in rubric_config" :key="cat.key" class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaBold text-text-secondary uppercase tracking-wider">{{ cat.label }} ({{ cat.weight }}%)</h2>
                </div>
                <div class="space-y-4">
                    <!-- With sub-categories -->
                    <template v-if="cat.has_sub_categories">
                        <div v-for="(subLabel, subKey) in cat.sub_categories" :key="subKey" class="group">
                            <div class="flex flex-col md:flex-row md:items-center gap-3">
                                <div class="md:w-1/3">
                                    <label class="text-sm font-jakartaSemiBold text-text-primary">{{ subLabel }}</label>
                                </div>
                                <div class="md:w-2/3">
                                    <select v-model="form.scores[`${cat.key}.${subKey}`]" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" :class="{'border-danger': form.errors[`scores.${cat.key}.${subKey}`]}">
                                        <option value="">-- Select Rating --</option>
                                        <option v-for="opt in contents_options" :key="opt.value" :value="opt.value">{{ opt.label }} ({{ opt.score }})</option>
                                    </select>
                                </div>
                            </div>
                            <p v-if="form.errors[`scores.${cat.key}.${subKey}`]" class="text-xs text-danger mt-1 md:ml-[33.33%]">{{ form.errors[`scores.${cat.key}.${subKey}`] }}</p>
                        </div>
                    </template>
                    <!-- Without sub-categories -->
                    <template v-else>
                        <div class="group">
                            <div class="flex flex-col md:flex-row md:items-center gap-3">
                                <div class="md:w-1/3">
                                    <label class="text-sm font-jakartaSemiBold text-text-primary">Rating</label>
                                </div>
                                <div class="md:w-2/3">
                                    <select v-model="form.scores[cat.key]" :disabled="isFinalized" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" :class="{'border-danger': form.errors[`scores.${cat.key}`]}">
                                        <option value="">-- Select Rating --</option>
                                        <option v-for="opt in secondary_options" :key="opt.value" :value="opt.value">{{ opt.label }} ({{ opt.score }})</option>
                                    </select>
                                </div>
                            </div>
                            <p v-if="form.errors[`scores.${cat.key}`]" class="text-xs text-danger mt-1 md:ml-[33.33%]">{{ form.errors[`scores.${cat.key}`] }}</p>
                        </div>
                    </template>
                </div>

                <!-- Category Score Preview -->
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-jakartaSemiBold text-text-secondary">Category Score</span>
                    <span class="text-lg font-jakartaSemiBold" :class="getCategoryScore(cat) > 0 ? 'text-primary' : 'text-gray-300'">{{ getCategoryScore(cat) }} / {{ cat.weight }}</span>
                </div>
            </CardContainer>

            <!-- Overall Score -->
            <CardContainer class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-jakartaBold text-text-primary">Overall Score</h2>
                        <p class="text-xs font-jakarta text-text-secondary mt-0.5">Sum of all category scores (max 100)</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-jakartaSemiBold" :class="overallScore > 0 ? 'text-primary' : 'text-gray-300'">{{ overallScore.toFixed(2) }}</span>
                        <span class="text-sm text-text-secondary ml-1">/ 100</span>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs font-jakartaSemiBold text-text-secondary">Qualification:</span>
                    <span class="text-xs font-jakartaSemiBold px-2 py-0.5 rounded-full" :class="qualificationResult === 'qualified' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                        {{ qualificationResult === 'qualified' ? 'Qualified' : 'Not Qualified' }}
                    </span>
                </div>
            </CardContainer>

            <!-- Comments -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Comments / Feedback</h2>
                </div>
                <textarea v-model="form.comments" rows="4" :disabled="isFinalized" class="w-full px-4 py-3 text-sm font-jakarta border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none disabled:bg-gray-50" placeholder="Masukkan komentar evaluasi (opsional)..."></textarea>
            </CardContainer>

            <!-- Actions -->
            <div v-if="!isFinalized" class="flex flex-col sm:flex-row gap-3 justify-end">
                <button type="submit" :disabled="form.processing" class="inline-flex items-center cursor-pointer justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md border border-gray-200 bg-white text-text-primary hover:bg-gray-50 transition-colors">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Draft' }}
                </button>
                <button v-if="evaluation && evaluation.status === 'draft'" type="button" @click="doSubmit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md bg-amber-500 text-white hover:bg-amber-600 transition-colors cursor-pointer">
                    Submit Evaluasi
                </button>
                <button v-if="evaluation && evaluation.status === 'submitted'" type="button" @click="showFinalizeConfirm = true" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-jakartaSemiBold rounded-md bg-success text-white hover:bg-emerald-600 transition-colors shadow-sm cursor-pointer">
                    Finalisasi
                </button>
            </div>
            <div v-else class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-md">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm text-emerald-700 font-jakartaSemiBold">Evaluasi telah difinalisasi dan tidak dapat diubah.</p>
            </div>
        </form>

        <!-- Finalize Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showFinalizeConfirm" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="showFinalizeConfirm = false">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                    </div>
                    <h3 class="text-lg font-jakartaSemiBold text-text-primary mb-2">Finalisasi Evaluasi?</h3>
                    <p class="text-sm font-jakarta text-text-secondary mb-6">Setelah difinalisasi, evaluasi <strong>tidak dapat diubah</strong>.</p>
                    <div class="flex gap-3">
                        <button @click="showFinalizeConfirm = false" class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">Batal</button>
                        <button @click="doFinalize" :disabled="form.processing" class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold text-white bg-success rounded-md hover:bg-emerald-600 cursor-pointer">Ya, Finalisasi</button>
                    </div>
                </div>
            </div>
        </Transition>

        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">{{ flashMsg }}</div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm, Link, router, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    magang: Object,
    evaluation: { type: Object, default: null },
    scores: { type: Object, default: () => ({}) },
    evaluator_type: String,
    rubric_config: Array,
    contents_options: Array,
    secondary_options: Array,
});

const showFinalizeConfirm = ref(false);
const isFinalized = computed(() => props.evaluation?.status === 'finalized');

const routePrefix = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === 'supervisor_industri') return '/industri';
    if (role === 'dosen_pembimbing') return '/dosen-pembimbing';
    return '';
});

// Init scores from props
const initScores = {};
for (const cat of props.rubric_config) {
    if (cat.has_sub_categories) {
        for (const subKey of Object.keys(cat.sub_categories)) {
            const k = `${cat.key}.${subKey}`;
            initScores[k] = props.scores[k] || '';
        }
    } else {
        initScores[cat.key] = props.scores[cat.key] || '';
    }
}

const form = useForm({
    company_name: props.evaluation?.company_name || props.magang.industri.nama_perusahaan,
    department: props.evaluation?.department || '',
    position: props.evaluation?.position || '',
    evaluation_date: props.evaluation?.evaluation_date || new Date().toISOString().split('T')[0],
    comments: props.evaluation?.comments || '',
    scores: initScores,
});

// Score helpers
function getNumericScore(catKey, rating) {
    if (!rating) return 0;
    const isContents = catKey === 'portfolio_contents';
    const opts = isContents ? props.contents_options : props.secondary_options;
    const opt = opts.find(o => o.value === rating);
    return opt ? opt.score : 0;
}

function getCategoryScore(cat) {
    if (cat.has_sub_categories) {
        const subKeys = Object.keys(cat.sub_categories);
        let total = 0, count = 0;
        for (const sk of subKeys) {
            const r = form.scores[`${cat.key}.${sk}`];
            if (r) { total += getNumericScore(cat.key, r); count++; }
        }
        return count > 0 ? Math.round((total / subKeys.length) * 100) / 100 : 0;
    }
    return getNumericScore(cat.key, form.scores[cat.key]);
}

const overallScore = computed(() => {
    let total = 0;
    for (const cat of props.rubric_config) {
        total += getCategoryScore(cat);
    }
    return total;
});

const qualificationResult = computed(() => {
    for (const cat of props.rubric_config) {
        if (cat.has_sub_categories) {
            for (const sk of Object.keys(cat.sub_categories)) {
                const r = form.scores[`${cat.key}.${sk}`];
                const s = getNumericScore(cat.key, r);
                if (s < cat.min_score) return 'not_qualified';
            }
        } else {
            const s = getNumericScore(cat.key, form.scores[cat.key]);
            if (s < cat.min_score) return 'not_qualified';
        }
    }
    return 'qualified';
});

function submitForm() {
    form.post(`${routePrefix.value}/portfolio-evaluation/${props.magang.id}`, { preserveScroll: true });
}
function doSubmit() {
    if (!props.evaluation?.id) return;
    router.post(`${routePrefix.value}/portfolio-evaluation/${props.evaluation.id}/submit`, {}, { preserveScroll: true });
}
function doFinalize() {
    if (!props.evaluation?.id) return;
    showFinalizeConfirm.value = false;
    router.post(`${routePrefix.value}/portfolio-evaluation/${props.evaluation.id}/finalize`, {}, { preserveScroll: true });
}

function badgeClass(s) { return { 'text-gray-700': s==='draft', 'text-amber-700': s==='submitted', 'text-emerald-700': s==='finalized' }; }
function dotClass(s) { return { 'bg-gray-400': s==='draft', 'bg-amber-500': s==='submitted', 'bg-emerald-500': s==='finalized' }; }
</script>
