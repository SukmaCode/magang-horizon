<template>
    <AuthenticatedLayout>
        <Head title="Form Internship Evaluation" />
        <div class="mb-4">
            <Link href="/internship/dosen-pembimbing/internship-evaluation" class="inline-flex font-jakartaSemiBold items-center gap-2 text-sm text-text-secondary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </Link>
        </div>
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Internship Evaluation</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Guidance for Supervisor / University Mentor</p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <!-- Rating Selection -->
                <div class="space-y-3">
                    <div v-for="(rOpt, rIdx) in getRatingOptions(cat)" :key="rIdx" class="group">
                        <label class="flex items-start gap-3 p-3 border rounded-md cursor-pointer transition-all" :class="[form.scores[cat.key]?.rating === rOpt.value ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300', isFinalized ? 'cursor-not-allowed opacity-60' : '']">
                            <input type="radio" :value="rOpt.value" :checked="form.scores[cat.key]?.rating === rOpt.value" :disabled="isFinalized" @change="selectRating(cat, rOpt)" class="mt-0.5 text-primary focus:ring-primary" />
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-jakartaSemiBold text-text-primary">{{ rOpt.label }}</span>
                                    <span v-if="cat.is_range" class="text-xs text-text-secondary">({{ rOpt.min }} - {{ rOpt.max }})</span>
                                    <span v-else class="text-xs text-text-secondary">({{ rOpt.score }})</span>
                                </div>
                                <p v-if="cat.descriptions[rOpt.value]" class="text-xs text-text-secondary mt-1 leading-relaxed">{{ cat.descriptions[rOpt.value] }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Range Score Input (for Performance Rating & Portfolio) -->
                <div v-if="cat.is_range && form.scores[cat.key]?.rating" class="mt-4 pt-4 border-t border-gray-100">
                    <label class="text-xs font-jakartaSemiBold text-text-secondary mb-2 block">
                        Exact Score ({{ getActiveRange(cat).min }} - {{ getActiveRange(cat).max }})
                    </label>
                    <input v-model.number="form.scores[cat.key].score" type="number" :min="getActiveRange(cat).min" :max="getActiveRange(cat).max" :step="cat.key === 'portfolio' ? 0.1 : 1" :disabled="isFinalized" class="w-full md:w-48 px-3 py-2.5 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary disabled:bg-gray-50" />
                    <p v-if="scoreRangeError(cat)" class="text-xs text-danger mt-1">Score harus antara {{ getActiveRange(cat).min }} dan {{ getActiveRange(cat).max }}.</p>
                </div>

                <p v-if="form.errors[`scores.${cat.key}.rating`]" class="text-xs text-danger mt-2">{{ form.errors[`scores.${cat.key}.rating`] }}</p>

                <!-- Category Score Preview -->
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-jakartaSemiBold text-text-secondary">Category Score</span>
                    <span class="text-lg font-jakartaSemiBold" :class="getCategoryScore(cat) > 0 ? 'text-primary' : 'text-gray-300'">
                        {{ getCategoryScore(cat).toFixed(1) }} / {{ cat.weight }}
                    </span>
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
                    <span class="text-xs font-jakartaSemiBold text-text-secondary">Result:</span>
                    <span class="text-xs font-jakartaSemiBold px-2 py-0.5 rounded-full" :class="passStatus === 'pass' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                        {{ passStatus === 'pass' ? 'PASS' : 'FAIL' }}
                    </span>
                </div>
            </CardContainer>

            <!-- Comments -->
            <CardContainer class="mb-6">
                <div class="border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Comments / Feedback</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Comments</label>
                        <textarea v-model="form.comments" rows="3" :disabled="isFinalized" class="w-full px-4 py-3 text-sm font-jakarta border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none disabled:bg-gray-50" placeholder="Masukkan komentar evaluasi (opsional)..."></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-jakartaSemiBold text-text-secondary mb-1 block">Additional Feedback</label>
                        <textarea v-model="form.feedback" rows="3" :disabled="isFinalized" class="w-full px-4 py-3 text-sm font-jakarta border border-gray-200 rounded-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none disabled:bg-gray-50" placeholder="Masukkan feedback tambahan (opsional)..."></textarea>
                    </div>
                </div>
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

        <!-- Flash Toast -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">{{ flashMsg }}</div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
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
    rubric_config: Array,
    performance_options: Array,
    fixed_options: Array,
    portfolio_options: Array,
});

const showFinalizeConfirm = ref(false);
const isFinalized = computed(() => props.evaluation?.status === 'finalized');

// Init scores from props
const initScores = {};
for (const cat of props.rubric_config) {
    const savedScore = props.scores[cat.key];
    if (savedScore) {
        initScores[cat.key] = { rating: savedScore.rating, score: savedScore.score };
    } else {
        initScores[cat.key] = { rating: '', score: null };
    }
}

const form = useForm({
    company_name: props.evaluation?.company_name || props.magang.industri.nama_perusahaan,
    department: props.evaluation?.department || '',
    position: props.evaluation?.position || '',
    evaluation_date: props.evaluation?.evaluation_date || new Date().toISOString().split('T')[0],
    comments: props.evaluation?.comments || '',
    feedback: props.evaluation?.feedback || '',
    scores: reactive(initScores),
});

// Get rating options based on category type
function getRatingOptions(cat) {
    if (cat.key === 'performance_rating') return props.performance_options;
    if (cat.key === 'portfolio') return props.portfolio_options;
    return props.fixed_options;
}

// Select a rating and auto-set score
function selectRating(cat, rOpt) {
    form.scores[cat.key] = {
        rating: rOpt.value,
        score: cat.is_range ? (rOpt.default || rOpt.min) : rOpt.score,
    };
}

// Get active range for range-based categories
function getActiveRange(cat) {
    const opts = getRatingOptions(cat);
    const selected = form.scores[cat.key]?.rating;
    const opt = opts.find(o => o.value === selected);
    return opt || { min: 0, max: 0 };
}

// Validate range score
function scoreRangeError(cat) {
    if (!cat.is_range || !form.scores[cat.key]?.rating) return false;
    const range = getActiveRange(cat);
    const score = form.scores[cat.key]?.score;
    if (score === null || score === undefined) return false;
    return score < range.min || score > range.max;
}

// Get the displayed score for a category
function getCategoryScore(cat) {
    const data = form.scores[cat.key];
    if (!data?.rating) return 0;
    if (cat.is_range) return data.score ?? 0;
    const opts = getRatingOptions(cat);
    const opt = opts.find(o => o.value === data.rating);
    return opt?.score ?? 0;
}

// Computed overall score
const overallScore = computed(() => {
    let total = 0;
    for (const cat of props.rubric_config) {
        total += getCategoryScore(cat);
    }
    return total;
});

// Computed pass/fail
const passStatus = computed(() => {
    if (overallScore.value < 50) return 'fail';
    // Check if any rating is "below"
    for (const cat of props.rubric_config) {
        const data = form.scores[cat.key];
        if (data?.rating === 'below') return 'fail';
    }
    return 'pass';
});

function submitForm() {
    form.post(`/dosen-pembimbing/internship-evaluation/${props.magang.id}`, { preserveScroll: true });
}

function doSubmit() {
    if (!props.evaluation?.id) return;
    router.post(`/dosen-pembimbing/internship-evaluation/${props.evaluation.id}/submit`, {}, { preserveScroll: true });
}

function doFinalize() {
    if (!props.evaluation?.id) return;
    showFinalizeConfirm.value = false;
    router.post(`/dosen-pembimbing/internship-evaluation/${props.evaluation.id}/finalize`, {}, { preserveScroll: true });
}

function badgeClass(s) { return { 'text-gray-700': s === 'draft', 'text-amber-700': s === 'submitted', 'text-emerald-700': s === 'finalized' }; }
function dotClass(s) { return { 'bg-gray-400': s === 'draft', 'bg-amber-500': s === 'submitted', 'bg-emerald-500': s === 'finalized' }; }
</script>
