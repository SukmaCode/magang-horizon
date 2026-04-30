<template>
    <AuthenticatedLayout>
        <Head title="Agreement Magang" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Agreement Magang</h1>
            <p class="text-sm text-text-secondary mt-1">Review dan tandatangani dokumen kesepakatan magang dengan industri.</p>
        </div>

        <CardContainer class="max-w-3xl">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-text-primary font-jakarta">Status Dokumen</h2>
                <span v-if="agreement?.status_agreement === 'accepted'" class="px-3 py-1 bg-success/10 text-success text-xs font-semibold rounded-full flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Diterima & Ditandatangani
                </span>
                <span v-else-if="agreement?.status_agreement === 'rejected'" class="px-3 py-1 bg-danger/10 text-danger text-xs font-semibold rounded-full flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Ditolak
                </span>
                <span v-else-if="agreement?.has_agreement" class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-semibold rounded-full flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Menunggu Respon Anda
                </span>
                <span v-else-if="agreement" class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                    Menunggu Industri
                </span>
            </div>

            <div class="p-6">
                <!-- Empty State: No active magang in persiapan -->
                <div v-if="!agreement" class="py-8 text-center">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-base font-bold text-text-primary mb-1">Belum Ada Agreement</h3>
                    <p class="text-sm text-text-secondary">Anda belum berada di tahap persiapan atau belum memiliki magang aktif.</p>
                </div>

                <!-- Waiting for Industri -->
                <div v-else-if="!agreement.has_agreement" class="py-8 text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-text-primary mb-1">Menunggu Dokumen</h3>
                    <p class="text-sm text-text-secondary max-w-sm mx-auto">Supervisor industri sedang mempersiapkan dan akan mengunggah dokumen agreement magang Anda.</p>
                </div>

                <!-- Agreement Uploaded -> Pending Response -->
                <div v-else-if="agreement.has_agreement && agreement.status_agreement !== 'accepted' && agreement.status_agreement !== 'rejected'" class="space-y-6">
                    <div class="bg-primary/5 border border-primary/20 rounded-xl p-5 flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-text-primary mb-1">Dokumen Baru dari {{ agreement.industri }}</h3>
                            <p class="text-sm text-text-secondary mb-3">Industri telah mengirimkan dokumen agreement. Silakan unduh, baca dengan teliti, dan berikan tanggapan Anda.</p>
                            <a :href="agreement.download_url" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-primary bg-white border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Unduh & Baca Agreement
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="text-sm font-semibold text-text-primary mb-4">Tanggapan Anda</h4>
                        <div class="flex gap-3">
                            <button @click="openAcceptModal" class="flex-1 px-4 py-3 bg-success text-white text-sm font-semibold rounded-xl hover:bg-success/90 flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Terima & Unggah TTD
                            </button>
                            <button @click="openRejectModal" class="flex-1 px-4 py-3 bg-white text-danger border border-danger/20 text-sm font-semibold rounded-xl hover:bg-danger/5 flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                Tolak Agreement
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Accepted State -->
                <div v-else-if="agreement.status_agreement === 'accepted'" class="py-6 text-center">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4 text-success">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-text-primary mb-2">Agreement Selesai</h3>
                    <p class="text-sm text-text-secondary max-w-sm mx-auto mb-6">Anda telah menerima dan menandatangani agreement. Magang Anda siap dilanjutkan ke tahap pelaksanaan.</p>
                    
                    <a :href="agreement.download_url" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-primary bg-primary/10 rounded-xl hover:bg-primary/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Lihat Dokumen
                    </a>
                </div>

                <!-- Rejected State -->
                <div v-else-if="agreement.status_agreement === 'rejected'" class="py-6 text-center">
                    <div class="w-16 h-16 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-4 text-danger">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-text-primary mb-2">Agreement Ditolak</h3>
                    <p class="text-sm text-text-secondary max-w-md mx-auto mb-4">Anda telah menolak agreement dari {{ agreement.industri }}.</p>
                    
                    <div class="bg-danger/5 border border-danger/10 rounded-lg p-4 text-left max-w-md mx-auto">
                        <p class="text-xs font-semibold text-danger mb-1">Alasan Penolakan:</p>
                        <p class="text-sm text-text-primary">{{ agreement.alasan_tolak }}</p>
                    </div>
                </div>
            </div>
        </CardContainer>

        <!-- Accept Modal (Upload PDF) -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showAcceptModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-text-primary font-jakarta">Terima & Unggah TTD</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitAccept" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">Silakan unggah kembali dokumen agreement yang telah <strong>Anda tandatangani</strong> (format PDF).</p>
                        
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
                                    <p class="text-xs text-text-secondary mt-1">Atau drag and drop kesini</p>
                                </div>
                            </div>
                            <p v-if="acceptForm.errors.file" class="text-xs text-danger mt-1">{{ acceptForm.errors.file }}</p>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="!selectedFile || acceptForm.processing" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-success rounded-xl hover:bg-success/90 flex justify-center items-center gap-2 disabled:opacity-50">
                                <span v-if="acceptForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Konfirmasi & Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Reject Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showRejectModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-danger font-jakarta">Tolak Agreement</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitReject" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">Tindakan ini akan mengakhiri proses persiapan magang Anda di industri terkait.</p>
                        
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-text-primary mb-2">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea v-model="rejectForm.alasan" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-danger/20 focus:border-danger" :class="{'border-danger': rejectForm.errors.alasan}" placeholder="Contoh: Terdapat ketidaksesuaian pada poin jam kerja..."></textarea>
                            <p v-if="rejectForm.errors.alasan" class="text-xs text-danger mt-1">{{ rejectForm.errors.alasan }}</p>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Kembali</button>
                            <button type="submit" :disabled="rejectForm.processing" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-danger rounded-xl hover:bg-danger/90 flex justify-center items-center gap-2">
                                <span v-if="rejectForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Tolak Agreement
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

const props = defineProps({
    agreement: { type: Object, default: null }
});

const showAcceptModal = ref(false);
const showRejectModal = ref(false);

const selectedFile = ref(null);
const dragOver = ref(false);

const acceptForm = useForm({ file: null });
const rejectForm = useForm({ alasan: '' });

function openAcceptModal() {
    selectedFile.value = null;
    acceptForm.reset();
    showAcceptModal.value = true;
}

function openRejectModal() {
    rejectForm.reset();
    showRejectModal.value = true;
}

function closeModals() {
    showAcceptModal.value = false;
    showRejectModal.value = false;
    selectedFile.value = null;
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        acceptForm.file = file;
    }
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        selectedFile.value = file;
        acceptForm.file = file;
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function submitAccept() {
    if (!props.agreement || !selectedFile.value) return;
    acceptForm.post(`/mahasiswa/agreement/${props.agreement.id}/accept`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: closeModals
    });
}

function submitReject() {
    if (!props.agreement) return;
    rejectForm.post(`/mahasiswa/agreement/${props.agreement.id}/reject`, {
        preserveScroll: true,
        onSuccess: closeModals
    });
}
</script>
