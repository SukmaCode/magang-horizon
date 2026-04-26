<template>
    <AuthenticatedLayout>
        <Head title="Laporan Akhir" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Laporan Akhir</h1>
            <p class="text-sm text-text-secondary mt-1">Upload dan pantau status laporan akhir magang Anda.</p>
        </div>

        <!-- No Magang Warning -->
        <div v-if="!magangId" class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-text-primary">Belum memiliki magang aktif</p>
                <p class="text-xs text-text-secondary mt-1">Silakan ajukan lamaran magang terlebih dahulu.</p>
            </div>
        </div>

        <template v-else>
            <!-- Gate Warning -->
            <div v-if="!canUpload" class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Upload tidak tersedia</p>
                    <p class="text-xs text-amber-600 mt-1">Laporan akhir hanya dapat diupload saat tahap <strong>Pelaksanaan</strong> atau <strong>Penutupan</strong>.</p>
                </div>
            </div>

            <!-- Existing Report Status -->
            <div v-if="laporan" class="bg-card rounded-xl border border-gray-100 p-6 mb-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h2 class="text-base font-bold text-text-primary font-jakarta">Status Laporan</h2>
                    <span
                        :class="[
                            'text-xs px-3 py-1 rounded-full font-semibold',
                            statusColor(laporan.status)
                        ]"
                    >
                        {{ laporan.status_label }}
                    </span>
                </div>

                <!-- Status Timeline -->
                <div class="flex items-center gap-2 mb-6">
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold', laporan.status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-success/10 text-success']">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 rounded">
                        <div :class="['h-full rounded transition-all duration-500', laporan.status !== 'pending' ? 'bg-success w-full' : 'bg-amber-400 w-1/3']"></div>
                    </div>
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold', laporan.status === 'disetujui' ? 'bg-success/10 text-success' : laporan.status === 'revisi' ? 'bg-danger/10 text-danger' : 'bg-gray-100 text-gray-400']">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-200 rounded">
                        <div :class="['h-full rounded transition-all duration-500', laporan.status === 'disetujui' ? 'bg-success w-full' : 'w-0']"></div>
                    </div>
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold', laporan.status === 'disetujui' ? 'bg-success/10 text-success' : 'bg-gray-100 text-gray-400']">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                </div>

                <div class="text-xs text-text-secondary">Terakhir diperbarui: {{ laporan.updated_at }}</div>

                <!-- Revision Notes -->
                <div v-if="laporan.catatan_revisi" class="mt-4 p-4 bg-danger/5 border border-danger/20 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <p class="text-sm font-semibold text-danger">Catatan Revisi dari Dosen</p>
                    </div>
                    <p class="text-sm text-text-primary leading-relaxed">{{ laporan.catatan_revisi }}</p>
                </div>
            </div>

            <!-- Upload Form -->
            <div v-if="canUpload" class="bg-card rounded-xl border border-gray-100 p-6">
                <h2 class="text-base font-bold text-text-primary font-jakarta mb-4">
                    {{ laporan ? 'Upload Ulang Laporan' : 'Upload Laporan Akhir' }}
                </h2>

                <form @submit.prevent="submitLaporan">
                    <!-- Drag & Drop Area -->
                    <div
                        class="border-2 border-dashed rounded-xl p-8 text-center transition-colors duration-200 cursor-pointer"
                        :class="[
                            dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300',
                            selectedFile ? 'bg-success/5 border-success/30' : ''
                        ]"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleDrop"
                        @click="$refs.fileInput.click()"
                    >
                        <input ref="fileInput" type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />

                        <div v-if="selectedFile" class="flex flex-col items-center">
                            <svg class="w-10 h-10 text-success mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-semibold text-text-primary">{{ selectedFile.name }}</p>
                            <p class="text-xs text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                        </div>
                        <div v-else class="flex flex-col items-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="text-sm font-semibold text-text-primary">Drag & drop file PDF di sini</p>
                            <p class="text-xs text-text-secondary mt-1">atau klik untuk memilih file (max 20MB)</p>
                        </div>
                    </div>

                    <p v-if="uploadForm.errors.file" class="text-xs text-danger mt-2">{{ uploadForm.errors.file }}</p>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="!selectedFile || uploadForm.processing"
                        class="mt-4 w-full px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="uploadForm.processing" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Mengupload...
                        </span>
                        <span v-else>Upload Laporan</span>
                    </button>
                </form>
            </div>
        </template>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.success" class="fixed bottom-6 right-6 bg-success text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.success }}
            </div>
        </Transition>
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.error" class="fixed bottom-6 right-6 bg-danger text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.error }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});

const props = defineProps({
    laporan: { type: Object, default: null },
    canUpload: { type: Boolean, default: false },
    magangId: { type: Number, default: null },
});

const selectedFile = ref(null);
const dragOver = ref(false);

const uploadForm = useForm({
    file: null,
});

function statusColor(status) {
    const map = {
        pending: 'bg-amber-50 text-amber-700',
        revisi: 'bg-danger/10 text-danger',
        disetujui: 'bg-success/10 text-success',
    };
    return map[status] || 'bg-gray-100 text-gray-600';
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        uploadForm.file = file;
    }
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        uploadForm.file = file;
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function submitLaporan() {
    uploadForm.post('/mahasiswa/laporan-akhir', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            selectedFile.value = null;
            uploadForm.reset();
        },
    });
}
</script>
