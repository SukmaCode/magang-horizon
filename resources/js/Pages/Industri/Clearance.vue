<template>
    <AuthenticatedLayout>
        <Head title="Clearance Issued By Company" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Clearance Issued By Company</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Upload dokumen clearance untuk mahasiswa magang Anda.</p>
        </div>

        <!-- Student List -->
        <CardContainer class="overflow-hidden">
            <div class="border-b border-gray-100">
                <h2 class="text-base font-jakartaBold text-text-primary">Daftar Mahasiswa Magang</h2>
                <p class="text-sm font-jakarta text-text-secondary mt-0.5">{{ filteredMagangs.length }} mahasiswa</p>
            </div>

            <div v-if="filteredMagangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in filteredMagangs" :key="magang.id" class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <!-- Student Info -->
                        <div class="flex-1">
                            <h3 class="text-base font-jakartaBold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm font-jakarta text-text-secondary">{{ magang.mahasiswa.nim }} · {{ magang.mahasiswa.prodi }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold bg-primary/10 text-primary capitalize flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Tahap: {{ magang.status_tahapan_label }}
                                </span>
                                <span v-if="magang.clearance" :class="getBadgeClass(magang.clearance.status)" class="text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getDotClass(magang.clearance.status)"></span>
                                    {{ magang.clearance.status_label }}
                                </span>
                            </div>

                            <!-- Rejection Note -->
                            <div v-if="magang.clearance?.status === 'rejected' && magang.clearance?.rejection_note" class="mt-3 bg-danger/5 border border-danger/15 rounded-md p-3">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-danger shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-jakartaSemiBold text-danger">Alasan Penolakan</p>
                                        <p class="text-xs font-jakarta text-text-secondary mt-0.5">{{ magang.clearance.rejection_note }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- File Info -->
                            <div v-if="magang.clearance" class="mt-3 flex items-center gap-2 text-xs font-jakarta text-text-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="truncate max-w-[200px]">{{ magang.clearance.original_filename }}</span>
                                <span class="text-text-secondary/50">·</span>
                                <span>{{ magang.clearance.uploaded_at }}</span>
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <template v-if="!magang.clearance">
                                <button
                                    @click="openUploadModal(magang)"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors duration-200 shadow-sm cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Clearance
                                </button>
                            </template>
                            <template v-else-if="magang.clearance.can_update">
                                <button
                                    @click="openUploadModal(magang)"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-jakartaSemiBold text-primary bg-primary/10 rounded-md hover:bg-primary/20 transition-colors duration-200 cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Update File
                                </button>
                            </template>
                            <template v-else>
                                <span class="text-xs font-jakartaSemiBold text-success bg-success/10 px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Tidak Dapat Diubah
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-base font-jakartaBold text-text-primary mb-1">Tidak Ada Peserta Magang</h3>
                <p class="text-sm font-jakarta text-text-secondary">Anda belum memiliki mahasiswa magang yang aktif.</p>
            </div>
        </CardContainer>

        <!-- Upload Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showUploadModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeUploadModal">
                <div class="bg-card rounded-lg shadow-xl w-full max-w-md">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-jakartaBold text-text-primary">
                                {{ selectedMagang?.clearance ? 'Update Clearance' : 'Upload Clearance' }}
                            </h3>
                            <p class="text-xs font-jakarta text-text-secondary mt-0.5">
                                {{ selectedMagang?.mahasiswa.nama_lengkap }} — {{ selectedMagang?.mahasiswa.nim }}
                            </p>
                        </div>
                        <button @click="closeUploadModal" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="submitUpload" class="p-6">
                        <!-- Drag & Drop Upload -->
                        <div
                            class="border-2 border-dashed rounded-md p-8 text-center transition-colors duration-200 cursor-pointer mb-4"
                            :class="[
                                dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300',
                                selectedFile ? 'bg-primary/5 border-primary/30' : ''
                            ]"
                            @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false"
                            @drop.prevent="handleDrop"
                            @click="$refs.fileInput.click()"
                        >
                            <input ref="fileInput" type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />

                            <div v-if="selectedFile" class="flex flex-col items-center">
                                <svg class="w-10 h-10 text-primary mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-jakartaSemiBold text-text-primary text-center break-all">{{ selectedFile.name }}</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                                <button type="button" @click.stop="removeFile" class="mt-3 text-xs font-jakartaSemiBold text-danger hover:text-danger/80 cursor-pointer">
                                    Batal
                                </button>
                            </div>
                            <div v-else class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <p class="text-sm font-jakartaSemiBold text-text-primary">Pilih file PDF</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">Drag & drop atau klik area ini</p>
                                <p class="text-[10px] text-text-secondary mt-2 font-jakarta">Maksimal 10MB</p>
                            </div>
                        </div>

                        <!-- Validation Error -->
                        <p v-if="form.errors.file" class="text-xs text-danger mb-3 font-jakarta">{{ form.errors.file }}</p>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <button type="button" @click="closeUploadModal" class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold border border-gray-200 rounded-md hover:bg-gray-50 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="!selectedFile || form.processing"
                                class="flex-1 px-4 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover flex justify-center items-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            >
                                <template v-if="form.processing">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Mengunggah...
                                </template>
                                <template v-else>
                                    {{ selectedMagang?.clearance ? 'Update File' : 'Upload File' }}
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

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

const showUploadModal = ref(false);
const selectedMagang = ref(null);
const dragOver = ref(false);
const selectedFile = ref(null);

const form = useForm({
    file: null,
});

function openUploadModal(magang) {
    selectedMagang.value = magang;
    selectedFile.value = null;
    form.reset();
    form.clearErrors();
    showUploadModal.value = true;
}

function closeUploadModal() {
    showUploadModal.value = false;
    selectedMagang.value = null;
    selectedFile.value = null;
    form.reset();
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    validateAndSetFile(file);
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    validateAndSetFile(file);
}

function validateAndSetFile(file) {
    if (!file) return;

    if (file.type !== 'application/pdf') {
        alert('File harus berformat PDF');
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file maksimal 10MB');
        return;
    }

    selectedFile.value = file;
    form.file = file;
}

function removeFile() {
    selectedFile.value = null;
    form.file = null;
    if (document.querySelector('input[type=file]')) {
        document.querySelector('input[type=file]').value = '';
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function getBadgeClass(status) {
    return {
        'uploaded': 'bg-blue-100 text-blue-700',
        'pending': 'bg-warning/10 text-warning',
        'approved': 'bg-success/10 text-success',
        'rejected': 'bg-danger/10 text-danger',
    }[status] || '';
}

function getDotClass(status) {
    return {
        'uploaded': 'bg-blue-500',
        'pending': 'bg-warning',
        'approved': 'bg-success',
        'rejected': 'bg-danger',
    }[status] || '';
}

function submitUpload() {
    if (!selectedMagang.value) return;

    const url = selectedMagang.value.clearance
        ? `/industri/clearance/${selectedMagang.value.clearance.id}/update`
        : `/industri/clearance/${selectedMagang.value.id}`;

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            closeUploadModal();
        },
    });
}
</script>
