<template>
    <AuthenticatedLayout>
        <Head title="Manajemen CV" />

        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-jakartaSemiBold text-text-primary">Manajemen CV</h1>
                <p class="text-sm font-jakarta text-text-secondary mt-1">Kelola Curriculum Vitae Anda untuk keperluan pendaftaran magang.</p>
            </div>
            <div v-if="hasCv">
                <button
                    @click="deleteCv"
                    class="px-4 py-2 text-sm font-jakartaSemiBold cursor-pointer text-danger bg-danger/10 rounded-md hover:bg-danger/20 transition-colors duration-200"
                    :disabled="deleteForm.processing"
                >
                    <span v-if="deleteForm.processing">Menghapus...</span>
                    <span v-else>Hapus CV</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Upload Section -->
            <div class="lg:col-span-1 space-y-6">
                <CardContainer>
                    <h2 class="text-base font-jakartaBold text-text-primary mb-4">
                        {{ hasCv ? 'Perbarui CV' : 'Upload CV Baru' }}
                    </h2>
                    
                    <form @submit.prevent="submitCv">
                        <div
                            class="border-2 border-dashed rounded-md p-8 text-center transition-colors duration-200 cursor-pointer mb-4"
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
                                <svg class="w-10 h-10 text-primary mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-jakartaSemiBold text-text-primary text-center break-all">{{ selectedFile.name }}</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                                <button type="button" @click.stop="removeFile" class="mt-3 text-xs font-semibold text-danger hover:text-danger/80">
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
                        <button
                            type="submit"
                            :disabled="!selectedFile || form.processing"
                            class="w-full px-5 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-4"
                        >
                            <template v-if="form.processing">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Mengunggah...
                            </template>
                            <template v-else>
                                {{ hasCv ? 'Ganti CV' : 'Upload CV' }}
                            </template>
                        </button>
                    </form>
                </CardContainer>
                
                <!-- Info Box -->
                <div class="bg-primary/5 border border-primary/10 rounded-md p-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-jakartaSemiBold text-text-primary">Informasi</h3>
                            <ul class="text-xs font-jakarta text-text-secondary mt-2 space-y-1.5 list-disc list-outside ml-3">
                                <li>CV wajib diunggah sebelum mendaftar magang.</li>
                                <li>CV ini akan dilampirkan otomatis setiap kali Anda melamar ke industri.</li>
                                <li>Supervisor Industri dapat melihat dan mengunduh CV Anda.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Preview Section -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-md border border-gray-100 overflow-hidden h-[600px] flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h2 class="text-base font-jakartaBold text-text-primary">Preview CV</h2>
                        <a v-if="activePreviewUrl" :href="activePreviewUrl" target="_blank" class="text-sm font-jakartaSemiBold text-primary hover:text-primary-hover flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Buka Fullscreen
                        </a>
                    </div>
                    
                    <div class="flex-1 bg-gray-100 relative">
                        <iframe 
                            v-if="activePreviewUrl"
                            :src="activePreviewUrl + '#toolbar=0'" 
                            class="w-full h-full no-scrollbar"
                            title="CV Preview"
                        ></iframe>
                        <div v-else-if="isLoadingPreview" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                            <div class="animate-spin w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full mb-4"></div>
                            <p class="text-sm font-medium text-text-primary">Memuat Preview...</p>
                        </div>
                        <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-text-primary mb-1">Belum ada CV</p>
                            <p class="text-xs text-text-secondary">Silakan upload CV Anda melalui form di samping.</p>
                        </div>
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
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { usePage, useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    hasCv: { type: Boolean, default: false },
    cvBase64: { type: String, default: null },
});

const dragOver = ref(false);
const selectedFile = ref(null);

const form = useForm({
    cv_file: null,
});

const deleteForm = useForm({});

const localPreviewUrl = ref(null);
const serverPreviewUrl = ref(null);
const isLoadingPreview = ref(false);

const activePreviewUrl = computed(() => {
    return localPreviewUrl.value || serverPreviewUrl.value;
});

watch(selectedFile, (newFile) => {
    if (newFile) {
        const reader = new FileReader();
        reader.readAsDataURL(newFile);
        reader.onloadend = () => {
            localPreviewUrl.value = reader.result;
        };
    } else {
        localPreviewUrl.value = null;
    }
});

onMounted(() => {
    if (props.hasCv && props.cvBase64) {
        serverPreviewUrl.value = props.cvBase64;
    }
});

watch(() => props.cvBase64, (newBase64) => {
    if (newBase64) {
        serverPreviewUrl.value = newBase64;
    }
});

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
    
    // Validate type
    if (file.type !== 'application/pdf') {
        alert('File harus berformat PDF');
        return;
    }
    
    // Validate size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file maksimal 10MB');
        return;
    }
    
    selectedFile.value = file;
    form.cv_file = file;
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

function submitCv() {
    form.post('/mahasiswa/manajemen-cv/upload', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            selectedFile.value = null;
            form.reset('cv_file');
            // Data Base64 akan otomatis terupdate via props dari Inertia reload
        },
    });
}

function deleteCv() {
    if (confirm('Apakah Anda yakin ingin menghapus CV? Anda tidak dapat mendaftar magang tanpa CV.')) {
        deleteForm.delete('/mahasiswa/manajemen-cv/delete', {
            preserveScroll: true,
        });
    }
}
</script>
