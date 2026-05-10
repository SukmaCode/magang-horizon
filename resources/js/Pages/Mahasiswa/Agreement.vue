<template>
    <AuthenticatedLayout>
        <Head title="Agreement Magang" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Agreement Magang</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">
                Review dan tandatangani dokumen kesepakatan magang dengan industri.
            </p>
        </div>

        <CardContainer class="w-full">
            <div class="border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Status Dokumen</h2>
                <span class="text-xs font-jakartaSemiBold text-text-secondary">{{ agreements.length }} Perusahaan</span>
            </div>

            <div class="py-4">
                <!-- Empty State -->
                <div v-if="agreements.length === 0" class="py-8 text-center">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Belum Ada Agreement</h3>
                    <p class="text-sm font-jakarta text-text-secondary">Anda belum berada di tahap persiapan atau belum memiliki magang aktif.</p>
                </div>

                <!-- Agreement Cards (one per company) -->
                <div
                    v-for="agr in agreements"
                    :key="agr.id"
                    class="mb-3 border border-gray-200 rounded-xl overflow-hidden"
                >
                    <!-- Card Header: Company name + status badge -->
                    <div class="px-5 py-3 flex flex-col md:flex-row items-start md:items-center justify-between gap-1 md:gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-md font-jakartaSemiBold text-text-primary">{{ agr.industri }}</span>
                        </div>

                        <!-- Status Badge -->
                        <span v-if="agr.status_agreement === 'accepted'" class="px-3 py-1 text-success text-xs font-jakartaSemiBold flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Diterima &amp; Ditandatangani
                        </span>
                        <span v-else-if="agr.status_agreement === 'rejected'" class="px-3 py-1 bg-danger/10 text-danger text-xs font-jakartaSemiBold rounded-full flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Ditolak
                        </span>
                        <span v-else-if="agr.has_agreement" class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-jakartaSemiBold rounded-full flex items-center gap-1.5 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Menunggu Respon Anda
                        </span>
                        <span
                            v-else
                            class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-jakartaSemiBold rounded-full shrink-0"
                        >
                            Menunggu Industri
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="px-5 py-4">
                        <!-- Waiting for industri to upload -->
                        <div v-if="!agr.has_agreement" class="flex items-center gap-3 text-sm font-jakarta text-text-secondary py-2">
                            <svg class="w-5 h-5 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Supervisor industri sedang mempersiapkan dokumen agreement.
                        </div>

                        <!-- Pending response: download + accept/reject -->
                        <div v-else-if="agr.has_agreement && agr.status_agreement !== 'accepted' && agr.status_agreement !== 'rejected'" class="space-y-4">
                            <p class="text-sm font-jakartaSemiBold text-text-secondary">
                                Industri telah mengirimkan dokumen agreement.
                                Silakan unduh, baca dengan teliti, dan berikan
                                tanggapan Anda.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a :href="agr.download_url" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-jakartaSemiBold text-primary bg-white border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors">Unduh &amp; Baca Agreement</a>
                                <button @click="openAcceptModal(agr)" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-jakartaSemiBold text-white bg-success rounded-lg hover:bg-success/90 transition-colors">Terima &amp; Unggah TTD</button>
                                <button @click="openRejectModal(agr)" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-jakartaSemiBold text-danger bg-white border border-danger/20 rounded-lg hover:bg-danger/5 transition-colors">Tolak Agreement</button>
                            </div>
                        </div>

                        <!-- Accepted -->
                        <div v-else-if="agr.status_agreement === 'accepted'" class="flex flex-col md:flex-row justify-center items-center gap-3">
                            <div class="w-8 h-8 bg-success/10 rounded-full flex items-center justify-center text-success shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-center items-center md:justify-start md:items-start">
                                <p class="text-sm font-jakartaSemiBold text-text-primary">Agreement Selesai</p>
                                <p class="text-xs font-jakarta text-text-secondary text-center md:text-start">Anda telah menerima dan menandatangani agreement.</p>
                            </div>
                            <a :href="agr.download_url" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-jakartaSemiBold text-primary bg-primary/10 rounded-sm hover:bg-primary/20 transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Dokumen
                            </a>
                        </div>

                        <!-- Rejected -->
                        <div v-else-if="agr.status_agreement === 'rejected'" class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-danger/10 rounded-full flex items-center justify-center text-danger shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-jakartaSemiBold text-text-primary">Agreement Ditolak</p>
                            </div>
                            <div class="bg-danger/5 border border-danger/10 rounded-lg p-3">
                                <p class="text-xs font-jakartaSemiBold text-danger mb-1">Alasan Penolakan:</p>
                                <p class="text-sm font-jakarta text-text-primary">{{ agr.alasan_tolak }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </CardContainer>

        <!-- Accept Modal (Upload PDF) -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showAcceptModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-jakartaSemiBold text-text-primary font-jakarta">Terima &amp; Unggah TTD</h3>
                            <p class="text-xs text-text-secondary mt-0.5">{{ selectedAgreement?.industri }}</p>
                        </div>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitAccept" class="p-6">
                        <p class="text-sm font-jakarta text-text-secondary mb-4">
                            Silakan unggah kembali dokumen agreement yang telah
                            <strong>Anda tandatangani</strong> (format PDF).
                        </p>

                        <div class="mb-6">
                            <div class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer"
                                :class="[dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 hover:border-gray-300', selectedFile ? 'bg-primary/5 border-primary/30' : '']"
                                @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop" @click="$refs.fileInput.click()">
                                <input ref="fileInput" type="file" accept=".pdf" class="hidden" @change="handleFileSelect" />
                                <div v-if="selectedFile" class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-jakartaSemiBold text-text-primary">{{ selectedFile.name }}</p>
                                    <p class="text-xs text-text-secondary mt-1">{{ formatFileSize(selectedFile.size) }}</p>
                                </div>
                                <div v-else class="flex flex-col items-center">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm font-jakartaSemiBold text-text-primary">Pilih File PDF</p>
                                    <p class="text-xs font-jakarta text-text-secondary mt-1">Atau drag and drop kesini</p>
                                </div>
                            </div>
                            <p v-if="acceptForm.errors.file" class="text-xs text-danger mt-1">{{ acceptForm.errors.file }}</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="!selectedFile || acceptForm.processing" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-success rounded-xl hover:bg-success/90 flex justify-center items-center gap-2 disabled:opacity-50">
                                <span v-if="acceptForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Konfirmasi &amp; Kirim
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Reject Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showRejectModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-jakartaSemiBold text-danger font-jakarta">Tolak Agreement</h3>
                            <p class="text-xs text-text-secondary mt-0.5">{{ selectedAgreement?.industri }}</p>
                        </div>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitReject" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">
                            Tindakan ini akan mengakhiri proses persiapan magang
                            Anda di industri terkait.
                        </p>

                        <div class="mb-5">
                            <label class="block text-sm font-jakartaSemiBold text-text-primary mb-2">
                                Alasan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea v-model="rejectForm.alasan" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-danger/20 focus:border-danger" :class="{'border-danger': rejectForm.errors.alasan}" placeholder="Contoh: Terdapat ketidaksesuaian pada poin jam kerja..."></textarea>
                            <p v-if="rejectForm.errors.alasan" class="text-xs text-danger mt-1">{{ rejectForm.errors.alasan }}</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Kembali</button>
                            <button type="submit" :disabled="rejectForm.processing" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-danger rounded-xl hover:bg-danger/90 flex justify-center items-center gap-2">
                                <span v-if="rejectForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Tolak Agreement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
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
import { ref, computed } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => (flash.value.success ? "success" : "error"));

const props = defineProps({
    agreements: { type: Array, default: () => [] },
});

const showAcceptModal = ref(false);
const showRejectModal = ref(false);
const selectedAgreement = ref(null);

const selectedFile = ref(null);
const dragOver = ref(false);

const acceptForm = useForm({ file: null });
const rejectForm = useForm({ alasan: "" });

function openAcceptModal(agr) {
    selectedAgreement.value = agr;
    selectedFile.value = null;
    acceptForm.reset();
    showAcceptModal.value = true;
}

function openRejectModal(agr) {
    selectedAgreement.value = agr;
    rejectForm.reset();
    showRejectModal.value = true;
}

function closeModals() {
    showAcceptModal.value = false;
    showRejectModal.value = false;
    selectedFile.value = null;
    selectedAgreement.value = null;
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file && file.type === "application/pdf") {
        selectedFile.value = file;
        acceptForm.file = file;
    }
}

function handleDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === "application/pdf") {
        selectedFile.value = file;
        acceptForm.file = file;
    }
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}

function submitAccept() {
    if (!selectedAgreement.value || !selectedFile.value) return;
    acceptForm.post(
        `/mahasiswa/agreement/${selectedAgreement.value.id}/accept`,
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: closeModals,
        },
    );
}

function submitReject() {
    if (!selectedAgreement.value) return;
    rejectForm.post(
        `/mahasiswa/agreement/${selectedAgreement.value.id}/reject`,
        {
            preserveScroll: true,
            onSuccess: closeModals,
        },
    );
}
</script>
