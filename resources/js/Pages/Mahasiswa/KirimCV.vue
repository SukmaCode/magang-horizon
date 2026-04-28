<template>
    <AuthenticatedLayout>
        <Head title="Apply Magang" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Apply Magang</h1>
            <p class="text-sm text-text-secondary mt-1">Pilih industri tujuan dan kirim CV Anda untuk melamar magang.</p>
        </div>

        <!-- Already Accepted Alert -->
        <div v-if="hasAccepted" class="mb-6 p-4 bg-success/5 border border-success/20 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-success">Anda sudah diterima!</p>
                <p class="text-xs text-text-secondary mt-1">Anda sudah diterima di salah satu industri. Tidak dapat mengajukan lamaran baru.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left: Application Form -->
            <div class="lg:col-span-3">
                <div class="bg-card rounded-xl border border-gray-100 overflow-hidden">
                    <!-- Form Header -->
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-base font-bold text-text-primary font-jakarta">Formulir Lamaran</h2>
                        <p class="text-xs text-text-secondary mt-1">
                            Kuota: <span class="font-semibold" :class="pendingCount >= maxApplications ? 'text-danger' : 'text-primary'">{{ pendingCount }} / {{ maxApplications }}</span> lamaran aktif
                        </p>
                    </div>

                    <form @submit.prevent="submitApplication" class="p-6 space-y-6">
                        <!-- Industry Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-2">
                                Pilih Industri Tujuan <span class="text-danger">*</span>
                            </label>

                            <!-- Search -->
                            <div class="relative mb-3">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Cari nama perusahaan..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                />
                            </div>

                            <!-- Industry List -->
                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                                <label
                                    v-for="ind in filteredIndustris"
                                    :key="ind.id"
                                    :class="[
                                        'flex items-start gap-3 p-3.5 cursor-pointer transition-colors duration-150',
                                        form.industri_id === ind.id
                                            ? 'bg-primary/5 border-l-3 border-l-primary'
                                            : 'hover:bg-gray-50',
                                        isIndustriDisabled(ind.id) ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        :value="ind.id"
                                        v-model="form.industri_id"
                                        :disabled="isIndustriDisabled(ind.id)"
                                        class="mt-0.5 text-primary focus:ring-primary/20"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-text-primary">{{ ind.nama_perusahaan }}</p>
                                        <p v-if="ind.alamat" class="text-xs text-text-secondary mt-0.5 truncate">{{ ind.alamat }}</p>
                                        <span v-if="getIndustriStatus(ind.id)" :class="['text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block', statusBadge(getIndustriStatus(ind.id))]">
                                            {{ getIndustriStatusLabel(ind.id) }}
                                        </span>
                                    </div>
                                </label>

                                <div v-if="filteredIndustris.length === 0" class="p-6 text-center">
                                    <p class="text-sm text-text-secondary">Tidak ada industri ditemukan.</p>
                                </div>
                            </div>
                            <p v-if="form.errors.industri_id" class="text-xs text-danger mt-1.5">{{ form.errors.industri_id }}</p>
                        </div>

                        <!-- CV Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-2">
                                Upload CV <span class="text-danger">*</span>
                            </label>

                            <!-- Already has CV indicator -->
                            <div v-if="cvUploaded && !selectedFile" class="mb-3 p-3 bg-success/5 border border-success/20 rounded-xl flex items-center gap-3">
                                <svg class="w-5 h-5 text-success shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-success">CV sudah terupload sebelumnya</p>
                                    <p class="text-xs text-text-secondary mt-0.5">Anda bisa upload ulang jika ingin mengganti.</p>
                                </div>
                            </div>

                            <!-- Upload Area -->
                            <div
                                class="border-2 border-dashed rounded-xl p-6 text-center transition-colors duration-200 cursor-pointer"
                                :class="[
                                    dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300',
                                    selectedFile ? 'bg-primary/5 border-primary/30' : ''
                                ]"
                                @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false"
                                @drop.prevent="handleDrop"
                                @click="$refs.cvInput.click()"
                            >
                                <input ref="cvInput" type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />

                                <div v-if="selectedFile" class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm font-semibold text-text-primary">{{ selectedFile.name }}</p>
                                    <p class="text-xs text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                                    <button type="button" @click.stop="removeFile" class="mt-2 text-xs text-danger hover:text-danger/80 font-medium">
                                        Hapus file
                                    </button>
                                </div>
                                <div v-else class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm font-medium text-text-primary">Upload CV (PDF)</p>
                                    <p class="text-xs text-text-secondary mt-1">Drag & drop atau klik untuk memilih · Maks 10MB</p>
                                </div>
                            </div>
                            <p v-if="form.errors.cv_file" class="text-xs text-danger mt-1.5">{{ form.errors.cv_file }}</p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="!canSubmit || form.processing"
                            class="w-full px-5 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <template v-if="form.processing">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Mengirim Lamaran...
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim Lamaran
                            </template>
                        </button>

                        <!-- Disabled reason -->
                        <p v-if="hasAccepted" class="text-xs text-center text-text-secondary">Anda sudah diterima di industri lain.</p>
                        <p v-else-if="pendingCount >= maxApplications" class="text-xs text-center text-danger">Kuota lamaran aktif penuh ({{ maxApplications }}/{{ maxApplications }}).</p>
                    </form>
                </div>
            </div>

            <!-- Right: Application History -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-base font-bold text-text-primary font-jakarta">Riwayat Lamaran</h2>
                    </div>

                    <div v-if="pendaftarans.length > 0" class="divide-y divide-gray-50">
                        <div
                            v-for="p in pendaftarans"
                            :key="p.id"
                            class="px-5 py-4 hover:bg-gray-50/50 transition-colors"
                        >
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h3 class="text-sm font-semibold text-text-primary leading-tight">{{ p.industri.nama_perusahaan }}</h3>
                                <span :class="['text-xs px-2.5 py-0.5 rounded-full font-medium shrink-0', statusBadge(p.status)]">
                                    {{ p.status_label }}
                                </span>
                            </div>
                            <p v-if="p.industri.alamat" class="text-xs text-text-secondary mb-1.5 truncate">{{ p.industri.alamat }}</p>
                            <p class="text-xs text-text-secondary">{{ p.created_at }}</p>

                            <!-- Rejection reason -->
                            <div v-if="p.status === 'ditolak' && p.keterangan" class="mt-2.5 p-2.5 bg-danger/5 rounded-lg border border-danger/10">
                                <p class="text-xs text-danger font-medium mb-0.5">Alasan Penolakan:</p>
                                <p class="text-xs text-text-primary">{{ p.keterangan }}</p>
                            </div>

                            <!-- Accepted info -->
                            <div v-if="p.status === 'diterima' && p.keterangan" class="mt-2.5 p-2.5 bg-success/5 rounded-lg border border-success/10">
                                <p class="text-xs text-success font-medium mb-0.5">Catatan Industri:</p>
                                <p class="text-xs text-text-primary">{{ p.keterangan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-sm font-bold text-text-primary mb-1">Belum Ada Lamaran</h3>
                        <p class="text-xs text-text-secondary">Kirim lamaran pertama Anda ke industri tujuan.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    industris: { type: Array, default: () => [] },
    pendaftarans: { type: Array, default: () => [] },
    hasAccepted: { type: Boolean, default: false },
    pendingCount: { type: Number, default: 0 },
    maxApplications: { type: Number, default: 3 },
    cvUploaded: { type: Boolean, default: false },
});

const searchQuery = ref('');
const selectedFile = ref(null);
const dragOver = ref(false);

const form = useForm({
    industri_id: null,
    cv_file: null,
    cv_exists: false,
});

// Set cv_exists based on prop
if (props.cvUploaded) {
    form.cv_exists = true;
}

// Filter industries by search
const filteredIndustris = computed(() => {
    if (!searchQuery.value) return props.industris;
    const q = searchQuery.value.toLowerCase();
    return props.industris.filter(i =>
        i.nama_perusahaan.toLowerCase().includes(q) ||
        (i.alamat && i.alamat.toLowerCase().includes(q))
    );
});

// Check if industry already has pending/accepted application
function getIndustriStatus(industriId) {
    const p = props.pendaftarans.find(a => a.industri.id === industriId);
    return p?.status || null;
}

function getIndustriStatusLabel(industriId) {
    const p = props.pendaftarans.find(a => a.industri.id === industriId);
    return p?.status_label || '';
}

function isIndustriDisabled(industriId) {
    const status = getIndustriStatus(industriId);
    return status === 'pending' || status === 'diterima' || props.hasAccepted;
}

const canSubmit = computed(() => {
    if (props.hasAccepted) return false;
    if (props.pendingCount >= props.maxApplications) return false;
    if (!form.industri_id) return false;
    if (!selectedFile.value && !props.cvUploaded) return false;
    return true;
});

function statusBadge(status) {
    const map = {
        pending: 'bg-amber-50 text-amber-700',
        diterima: 'bg-success/10 text-success',
        ditolak: 'bg-danger/10 text-danger',
    };
    return map[status] || 'bg-gray-100 text-gray-600';
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        form.cv_file = file;
    }
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        form.cv_file = file;
    }
}

function removeFile() {
    selectedFile.value = null;
    form.cv_file = null;
    if (document.querySelector('input[type=file]')) {
        document.querySelector('input[type=file]').value = '';
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function submitApplication() {
    form.post('/mahasiswa/kirim-cv', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            selectedFile.value = null;
            form.reset('industri_id', 'cv_file');
            searchQuery.value = '';
        },
    });
}
</script>
