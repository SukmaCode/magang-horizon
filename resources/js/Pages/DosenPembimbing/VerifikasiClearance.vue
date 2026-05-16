<template>
    <AuthenticatedLayout>
        <Head title="Verifikasi Clearance Issued By Company" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Verifikasi Clearance Issued By Company</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Verifikasi dokumen clearance dari perusahaan untuk mahasiswa bimbingan Anda.</p>
        </div>

        <!-- Empty State -->
        <div v-if="!clearances.length === 0" class="bg-card rounded-md border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Dokumen</p>
            <p class="text-xs font-jakarta text-text-secondary">Belum ada mahasiswa yang mengirimkan Clearance Issued By Company.</p>
        </div>

        <!-- Main Content -->
        <div v-else class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left: Clearance List -->
            <div class="xl:col-span-1">
                <CardContainer padding="p-0">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-jakartaBold text-text-primary">Daftar Dokumen</h2>
                        <p class="text-sm font-jakarta text-text-secondary mt-0.5">{{ clearances.length }} dokumen</p>
                    </div>
                    <div class="divide-y divide-gray-50 max-h-[600px] overflow-y-auto">
                        <button
                            v-for="cl in clearances"
                            :key="cl.id"
                            @click="selectClearance(cl)"
                            :class="[
                                'w-full text-left px-5 py-4 cursor-pointer transition-colors duration-150 hover:bg-gray-100',
                                selected?.id === cl.id ? 'bg-primary/5 border-l-2 border-l-primary' : ''
                            ]"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-md font-jakartaSemiBold text-text-primary truncate">{{ cl.mahasiswa.nama_lengkap }}</p>
                                    <p class="text-xs font-jakarta text-text-secondary mt-0.5">{{ cl.mahasiswa.nim }}</p>
                                    <p class="text-[10px] font-jakarta text-text-secondary mt-1">{{ cl.submitted_at || cl.uploaded_at }}</p>
                                </div>
                                <span
                                    :class="getBadgeClass(cl.status)"
                                    class="text-xs inline-flex items-center gap-1 px-2 py-1 rounded-full font-jakartaSemiBold shrink-0"
                                >
                                    <span class="text-lg w-1 h-1 rounded-full" :class="getDotClass(cl.status)"></span>
                                    {{ cl.status_label }}
                                </span>
                            </div>
                        </button>
                    </div>
                </CardContainer>
            </div>

            <!-- Right: Detail Panel -->
            <div class="xl:col-span-2">
                <template v-if="selected">
                    <!-- Document Info Card -->
                    <CardContainer class="mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
                            <div>
                                <h2 class="text-base font-jakartaBold text-text-primary">{{ selected.mahasiswa.nama_lengkap }}</h2>
                                <p class="text-xs font-jakarta text-text-secondary mt-0.5">{{ selected.mahasiswa.nim }} · {{ selected.industri }}</p>
                            </div>
                            <span
                                :class="getBadgeClass(selected.status)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-jakartaSemiBold self-start"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="getDotClass(selected.status)"></span>
                                {{ selected.status_label }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                            <div>
                                <p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">File</p>
                                <p class="text-xs font-jakartaSemiBold text-text-primary break-all">{{ selected.original_filename }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Upload</p>
                                <p class="text-xs font-jakartaSemiBold text-text-primary">{{ selected.uploaded_at }}</p>
                            </div>
                            <div v-if="selected.submitted_at">
                                <p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Dikirim</p>
                                <p class="text-xs font-jakartaSemiBold text-text-primary">{{ selected.submitted_at }}</p>
                            </div>
                            <div v-if="selected.reviewer_name">
                                <p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Reviewer</p>
                                <p class="text-xs font-jakartaSemiBold text-text-primary">{{ selected.reviewer_name }}</p>
                            </div>
                            <div v-if="selected.reviewed_at">
                                <p class="text-[10px] font-jakarta text-text-secondary uppercase tracking-wider mb-1">Tanggal Review</p>
                                <p class="text-xs font-jakartaSemiBold text-text-primary">{{ selected.reviewed_at }}</p>
                            </div>
                        </div>

                        <!-- Rejection Note -->
                        <div v-if="selected.status === 'rejected' && selected.rejection_note" class="bg-danger/5 border border-danger/15 rounded-md p-4 mb-5">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-danger shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-xs font-jakartaSemiBold text-danger">Alasan Penolakan</p>
                                    <p class="text-xs font-jakarta text-text-secondary mt-1">{{ selected.rejection_note }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons (only for pending) -->
                        <div v-if="selected.status === 'pending'" class="flex flex-col sm:flex-row gap-3">
                            <a
                                :href="downloadUrl"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-primary bg-primary/10 rounded-md hover:bg-primary/20 transition-colors duration-200 text-center flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download
                            </a>
                            <button
                                @click="approveClearance"
                                :disabled="approveForm.processing"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-success rounded-md hover:bg-success/90 transition-colors duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ approveForm.processing ? 'Memproses...' : 'Approve' }}
                            </button>
                            <button
                                @click="showRejectModal = true"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-danger rounded-md hover:bg-danger/90 transition-colors duration-200 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reject
                            </button>
                        </div>

                        <!-- Non-pending actions (download only) -->
                        <div v-else class="flex gap-3">
                            <a
                                :href="downloadUrl"
                                class="px-4 py-2.5 text-sm font-jakartaSemiBold text-primary bg-primary/10 rounded-md hover:bg-primary/20 transition-colors duration-200 text-center flex items-center justify-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download File
                            </a>
                        </div>
                    </CardContainer>

                    <!-- PDF Preview -->
                    <div class="bg-card rounded-md border border-gray-100 overflow-hidden h-[500px] flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <h2 class="text-sm font-jakartaBold text-text-primary">Preview PDF</h2>
                            <a :href="previewUrl" target="_blank" class="text-xs font-jakartaSemiBold text-primary hover:text-primary-hover flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Fullscreen
                            </a>
                        </div>
                        <div class="flex-1 bg-gray-100">
                            <iframe
                                :src="previewUrl + '#toolbar=0'"
                                class="w-full h-full"
                                title="Clearance Preview"
                            ></iframe>
                        </div>
                    </div>
                </template>

                <!-- No Selection State -->
                <div v-else class="bg-card rounded-md border border-gray-100 h-[600px] flex flex-col items-center justify-center text-center p-6">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Pilih Dokumen</p>
                    <p class="text-xs font-jakarta text-text-secondary">Klik salah satu dokumen di panel kiri untuk melihat detail dan melakukan verifikasi.</p>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="showRejectModal = false"></div>
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6 z-10">
                    <h3 class="text-base font-jakartaBold text-text-primary mb-1">Tolak Clearance</h3>
                    <p class="text-xs font-jakarta text-text-secondary mb-4">Berikan catatan revisi atau alasan penolakan.</p>

                    <form @submit.prevent="rejectClearance">
                        <textarea
                            v-model="rejectForm.rejection_note"
                            rows="4"
                            placeholder="Tuliskan alasan penolakan atau catatan revisi..."
                            class="w-full border border-gray-200 rounded-md px-4 py-3 text-sm font-jakarta text-text-primary placeholder-text-secondary/50 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none"
                        ></textarea>
                        <p v-if="rejectForm.errors.rejection_note" class="text-xs text-danger mt-1 font-jakarta">{{ rejectForm.errors.rejection_note }}</p>

                        <div class="flex gap-3 mt-4">
                            <button
                                type="button"
                                @click="showRejectModal = false"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-text-secondary bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200 cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="rejectForm.processing || !rejectForm.rejection_note"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-danger rounded-md hover:bg-danger/90 transition-colors duration-200 disabled:opacity-50 cursor-pointer"
                            >
                                {{ rejectForm.processing ? 'Memproses...' : 'Tolak Dokumen' }}
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
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const props = defineProps({
    clearances: { type: Array, default: () => [] },
});

const selected = ref(null);
const showRejectModal = ref(false);

const approveForm = useForm({});
const rejectForm = useForm({
    rejection_note: '',
});

// Route prefix based on role
const routePrefix = computed(() => {
    const role = page.props.auth?.user?.role;
    if (role === 'dosen_pembimbing') return '/dosen-pembimbing';
    if (role === 'dosen_prodi') return '/dosen-prodi';
    return '';
});

const downloadUrl = computed(() => {
    if (!selected.value) return '#';
    return `${routePrefix.value}/clearance/${selected.value.id}/download`;
});

const previewUrl = computed(() => {
    if (!selected.value) return '';
    return `${routePrefix.value}/clearance/${selected.value.id}/preview`;
});

function selectClearance(cl) {
    selected.value = cl;
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

function approveClearance() {
    if (!selected.value) return;
    if (!confirm('Apakah Anda yakin ingin menyetujui dokumen ini?')) return;

    approveForm.post(`${routePrefix.value}/clearance/${selected.value.id}/approve`, {
        preserveScroll: true,
        onSuccess: () => {
            selected.value.status = 'approved';
            selected.value.status_label = 'Disetujui';
            selected.value.status_color = 'success';
        },
    });
}

function rejectClearance() {
    if (!selected.value) return;

    rejectForm.post(`${routePrefix.value}/clearance/${selected.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            selected.value.status = 'rejected';
            selected.value.status_label = 'Ditolak';
            selected.value.status_color = 'danger';
            selected.value.rejection_note = rejectForm.rejection_note;
            rejectForm.reset('rejection_note');
        },
    });
}
</script>
