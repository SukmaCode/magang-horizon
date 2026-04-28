<template>
    <AuthenticatedLayout>
        <Head title="Agreement Magang" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Agreement Industri</h1>
            <p class="text-sm text-text-secondary mt-1">Upload dokumen persetujuan magang untuk mahasiswa yang diterima.</p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-text-primary font-jakarta">Peserta Tahap Persiapan</h2>
            </div>

            <div v-if="magangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in magangs" :key="magang.id" class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm text-text-secondary">{{ magang.mahasiswa.nim }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-medium bg-primary/10 text-primary capitalize">
                                    Tahap: {{ magang.status }}
                                </span>
                                <span v-if="magang.has_agreement" class="text-xs px-2.5 py-0.5 rounded-full font-medium bg-success/10 text-success inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Agreement Terupload
                                </span>
                                <span v-else class="text-xs px-2.5 py-0.5 rounded-full font-medium bg-amber-50 text-amber-600">
                                    Menunggu Agreement
                                </span>
                            </div>
                        </div>

                        <div>
                            <button @click="openUploadModal(magang)" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl transition-colors duration-200" :class="magang.has_agreement ? 'bg-gray-100 text-text-primary hover:bg-gray-200' : 'bg-primary text-white hover:bg-primary-hover shadow-sm'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ magang.has_agreement ? 'Upload Ulang Agreement' : 'Upload Agreement' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-base font-bold text-text-primary mb-1">Tidak Ada Peserta Tahap Persiapan</h3>
                <p class="text-sm text-text-secondary">Terima lamaran mahasiswa pada Seleksi CV terlebih dahulu.</p>
            </div>
        </CardContainer>

        <!-- Upload Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showUploadModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-text-primary font-jakarta">Upload Agreement</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitUpload" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">Upload dokumen Agreement Magang yang telah ditandatangani Industri untuk <strong>{{ selectedMagang?.mahasiswa.nama_lengkap }}</strong>.</p>
                        
                        <div class="mb-6">
                            <div class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer" :class="[dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300', selectedFile ? 'bg-primary/5 border-primary/30' : '']" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop" @click="$refs.fileInput.click()">
                                <input ref="fileInput" type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />
                                <div v-if="selectedFile" class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <p class="text-sm font-semibold text-text-primary">{{ selectedFile.name }}</p>
                                    <p class="text-xs text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                                </div>
                                <div v-else class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                    <p class="text-sm font-semibold text-text-primary">Pilih File PDF</p>
                                    <p class="text-xs text-text-secondary mt-1">Atau drag and drop kesini (Maks 10MB)</p>
                                </div>
                            </div>
                            <p v-if="uploadForm.errors.file" class="text-xs text-danger mt-1">{{ uploadForm.errors.file }}</p>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="!selectedFile || uploadForm.processing" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover flex justify-center items-center gap-2 disabled:opacity-50">
                                <span v-if="uploadForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    magangs: { type: Array, default: () => [] }
});

const showUploadModal = ref(false);
const selectedMagang = ref(null);
const selectedFile = ref(null);
const dragOver = ref(false);

const uploadForm = useForm({ file: null });

function openUploadModal(magang) {
    selectedMagang.value = magang;
    selectedFile.value = null;
    uploadForm.reset();
    showUploadModal.value = true;
}

function closeModal() {
    showUploadModal.value = false;
    selectedMagang.value = null;
    selectedFile.value = null;
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

function submitUpload() {
    if(!selectedMagang.value || !selectedFile.value) return;
    uploadForm.post(`/industri/agreement/${selectedMagang.value.id}`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: closeModal
    });
}
</script>
