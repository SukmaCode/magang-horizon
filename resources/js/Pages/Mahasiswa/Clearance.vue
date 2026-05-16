<template>
    <AuthenticatedLayout>
        <Head title="Clearance Issued By Company" />

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-jakartaSemiBold text-text-primary">Clearance Issued By Company</h1>
                    <p class="text-sm font-jakarta text-text-secondary mt-1">Dokumen clearance dari perusahaan tempat magang Anda.</p>
                </div>
                <div v-if="clearance" class="flex items-center gap-2">
                    <span
                        :class="statusBadgeClass"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-jakartaSemiBold"
                    >
                        <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClass"></span>
                        {{ clearance.status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- No Active Magang Warning -->
        <div v-if="!hasMagang" class="bg-warning/5 border border-warning/20 rounded-md p-6 text-center">
            <div class="w-14 h-14 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Memiliki Magang Aktif</h3>
            <p class="text-xs font-jakarta text-text-secondary">Anda harus memiliki magang aktif sebelum dapat melihat Clearance.</p>
        </div>

        <!-- No Clearance Yet -->
        <div v-else-if="!clearance" class="bg-card rounded-md border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Clearance</h3>
            <p class="text-xs font-jakarta text-text-secondary"><strong>{{ industriName }}</strong> belum mengupload dokumen Clearance Issued By Company untuk Anda.</p>
        </div>

        <!-- Main Content -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Info & Actions -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Submit Card -->
                <CardContainer>
                    <h2 class="text-base font-jakartaBold text-text-primary mb-4">Kirim ke Dosen</h2>

                    <!-- Can Submit -->
                    <template v-if="clearance.can_submit">
                        <p class="text-xs font-jakarta text-text-secondary mb-4">
                            Dokumen clearance dari <strong>{{ industriName }}</strong> sudah tersedia. Kirimkan ke Dosen Pembimbing untuk diverifikasi.
                        </p>
                        <button
                            @click="submitClearance"
                            :disabled="submitForm.processing"
                            class="w-full px-5 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <template v-if="submitForm.processing">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Mengirim...
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim untuk Verifikasi
                            </template>
                        </button>
                    </template>

                    <!-- Already Submitted / Approved -->
                    <template v-else-if="clearance.status === 'pending'">
                        <div class="text-center py-4">
                            <div class="w-14 h-14 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-jakartaSemiBold text-warning">Menunggu Verifikasi</p>
                            <p class="text-xs font-jakarta text-text-secondary mt-1">Dokumen sedang diproses oleh Dosen Pembimbing.</p>
                        </div>
                    </template>

                    <template v-else-if="clearance.status === 'approved'">
                        <div class="text-center py-4">
                            <div class="w-14 h-14 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-jakartaSemiBold text-success">Dokumen Disetujui</p>
                            <p class="text-xs font-jakarta text-text-secondary mt-1">Clearance telah diverifikasi dan disetujui.</p>
                        </div>
                    </template>
                </CardContainer>

                <!-- Document Info -->
                <CardContainer>
                    <h2 class="text-base font-jakartaBold text-text-primary mb-4">Informasi Dokumen</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-jakarta text-text-secondary shrink-0">Nama File</span>
                            <span :title="clearance.original_filename" class="text-xs font-jakartaSemiBold text-text-primary text-right break-all line-clamp-1">{{ clearance.original_filename }}</span>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-jakarta text-text-secondary">Perusahaan</span>
                            <span class="text-xs font-jakartaSemiBold text-text-primary text-right">{{ industriName }}</span>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-jakarta text-text-secondary">Tanggal Upload</span>
                            <span class="text-xs font-jakartaSemiBold text-text-primary text-right">{{ clearance.uploaded_at }}</span>
                        </div>
                        <template v-if="clearance.submitted_at">
                            <div class="border-t border-gray-100"></div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-jakarta text-text-secondary">Tanggal Submit</span>
                                <span class="text-xs font-jakartaSemiBold text-text-primary text-right">{{ clearance.submitted_at }}</span>
                            </div>
                        </template>
                        <template v-if="clearance.reviewer_name">
                            <div class="border-t border-gray-100"></div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-jakarta text-text-secondary">Diverifikasi Oleh</span>
                                <span class="text-xs font-jakartaSemiBold text-text-primary text-right">{{ clearance.reviewer_name }}</span>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-jakarta text-text-secondary">Tanggal Verifikasi</span>
                                <span class="text-xs font-jakartaSemiBold text-text-primary text-right">{{ clearance.reviewed_at }}</span>
                            </div>
                        </template>
                    </div>
                </CardContainer>

                <!-- Rejection Note -->
                <div v-if="clearance.status === 'rejected' && clearance.rejection_note" class="bg-danger/5 border border-danger/15 rounded-md p-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-jakartaSemiBold text-danger">Catatan Revisi</h3>
                            <p class="text-xs font-jakarta text-text-secondary mt-1.5 leading-relaxed">{{ clearance.rejection_note }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-primary/5 border border-primary/10 rounded-md p-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-jakartaSemiBold text-text-primary">Informasi</h3>
                            <ul class="text-xs font-jakarta text-text-secondary mt-2 space-y-1.5 list-disc list-outside ml-3">
                                <li>Dokumen Clearance diupload oleh perusahaan tempat magang Anda.</li>
                                <li>Setelah tersedia, Anda wajib mengirimkan dokumen ini untuk diverifikasi oleh Dosen Pembimbing.</li>
                                <li>Jika <strong>Ditolak</strong>, hubungi perusahaan untuk mengupload ulang dokumen yang benar.</li>
                                <li>Clearance menjadi salah satu syarat sebelum proses penutupan magang.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Preview Section -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-md border border-gray-100 overflow-hidden h-[600px] flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h2 class="text-base font-jakartaBold text-text-primary">Preview Dokumen</h2>
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
                            title="Clearance Preview"
                        ></iframe>
                        <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-jakartaSemiBold text-text-primary mb-1">Preview Tidak Tersedia</p>
                            <p class="text-xs text-text-secondary">Dokumen belum tersedia untuk ditampilkan.</p>
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
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
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
    clearance: { type: Object, default: null },
    pdfBase64: { type: String, default: null },
    hasMagang: { type: Boolean, default: false },
    industriName: { type: String, default: '-' },
});

const submitForm = useForm({});

const serverPreviewUrl = ref(null);

const activePreviewUrl = computed(() => {
    return serverPreviewUrl.value;
});

// Status badge styling
const statusBadgeClass = computed(() => {
    if (!props.clearance) return '';
    return {
        'uploaded': 'bg-blue-100 text-blue-700',
        'pending': 'bg-warning/10 text-warning',
        'approved': 'bg-success/10 text-success',
        'rejected': 'bg-danger/10 text-danger',
    }[props.clearance.status] || '';
});

const statusDotClass = computed(() => {
    if (!props.clearance) return '';
    return {
        'uploaded': 'bg-blue-500',
        'pending': 'bg-warning',
        'approved': 'bg-success',
        'rejected': 'bg-danger',
    }[props.clearance.status] || '';
});

// Load server PDF on mount
onMounted(() => {
    if (props.clearance && props.pdfBase64) {
        serverPreviewUrl.value = props.pdfBase64;
    }
});

watch(() => props.pdfBase64, (newBase64) => {
    if (newBase64) {
        serverPreviewUrl.value = newBase64;
    }
});

function submitClearance() {
    if (!props.clearance) return;
    if (!confirm('Apakah Anda yakin ingin mengirimkan dokumen ini ke Dosen Pembimbing untuk diverifikasi?')) return;

    submitForm.post(`/mahasiswa/clearance/${props.clearance.id}/submit`, {
        preserveScroll: true,
    });
}
</script>
