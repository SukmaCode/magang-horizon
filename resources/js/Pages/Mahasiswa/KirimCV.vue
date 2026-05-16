<template>
    <AuthenticatedLayout>
        <Head title="Apply Magang" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Apply Magang</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Pilih industri tujuan dan kirim CV Anda untuk melamar magang.</p>
        </div>

        <!-- Already Accepted Alert -->
        <div v-if="hasAccepted" class="mb-6 p-4 bg-success/5 border border-success/20 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-jakartaSemiBold text-success">Agreement Telah Ditandatangani!</p>
                <p class="text-xs font-jakarta text-text-secondary mt-1">Anda sudah menandatangani file Agreement dari industri. Tidak dapat mengajukan lamaran baru.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left: Application Form -->
            <CardContainer class="lg:col-span-3">
                <!-- Form Header -->
                <div class="border-b border-gray-100">
                    <h2 class="text-base font-jakartaSemiBold text-text-primary">Formulir Lamaran</h2>
                    <p class="text-xs font-jakarta text-text-secondary mt-1">
                        Kuota: <span class="font-jakartaSemiBold" :class="pendingCount >= maxApplications ? 'text-danger' : 'text-primary'">{{ pendingCount }} / {{ maxApplications }}</span> lamaran aktif
                        </p>
                    </div>

                    <form @submit.prevent="submitApplication" class="py-6 space-y-6">
                        <!-- Industry Selection -->
                        <div>
                            <label class="block text-sm font-jakartaSemiBold text-text-primary mb-2">
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
                                    class="w-full pl-10 pr-4 font-jakarta py-2.5 border border-gray-200 rounded-xl text-sm text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
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
                                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ ind.nama_perusahaan }}</p>
                                        <p v-if="ind.alamat" class="text-xs font-jakarta text-text-secondary mt-0.5 truncate">{{ ind.alamat }}</p>
                                        <span v-if="getIndustriDisplayStatus(ind.id)" :class="['text-xs px-2 py-0.5 rounded-full font-jakartaSemiBold mt-1 inline-block', statusBadge(getIndustriDisplayStatus(ind.id))]">
                                            {{ getIndustriDisplayLabel(ind.id) }}
                                        </span>
                                    </div>
                                </label>

                                <div v-if="filteredIndustris.length === 0" class="p-6 text-center">
                                    <p class="text-sm text-text-secondary">Tidak ada industri ditemukan.</p>
                                </div>
                            </div>
                            <p v-if="form.errors.industri_id" class="text-xs text-danger mt-1.5">{{ form.errors.industri_id }}</p>
                        </div>

                        <!-- CV Check -->
                        <div v-if="!cvUploaded" class="mb-6 p-4 bg-danger/5 border border-danger/20 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-jakartaSemiBold text-danger">CV Belum Diunggah!</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">Anda wajib mengunggah CV terlebih dahulu sebelum dapat mengirim lamaran.</p>
                                <Link href="/mahasiswa/manajemen-cv" class="mt-2 inline-block px-3 py-2 text-xs font-jakartaSemiBold text-white bg-danger rounded-md hover:bg-danger/90 transition-colors">
                                    Upload CV Sekarang
                                </Link>
                            </div>
                        </div>
                        <div v-else class="mb-6 p-4 bg-primary/5 border border-primary/20 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-jakartaSemiBold text-primary">CV Telah Dilampirkan</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">Sistem akan otomatis menggunakan CV yang tersimpan di profil Anda.</p>
                                <Link href="/mahasiswa/manajemen-cv" class="mt-2 inline-block text-xs font-jakartaSemiBold text-primary hover:underline">
                                    Lihat atau ubah CV Anda
                                </Link>
                            </div>
                        </div>

                        <!-- LinkedIn Check -->
                        <div v-if="!linkedinFilled" class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-jakartaSemiBold text-amber-700">LinkedIn Belum Diisi!</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">Lengkapi profil LinkedIn terlebih dahulu sebelum melamar magang.</p>
                                <Link href="/mahasiswa/profil" class="mt-2 inline-block px-3 py-2 text-xs font-jakartaSemiBold text-white bg-amber-600 rounded-md hover:bg-amber-700 transition-colors">
                                    Lengkapi Profil Sekarang
                                </Link>
                            </div>
                        </div>
                        <div v-else class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-jakartaSemiBold text-blue-700">LinkedIn Terhubung</p>
                                <p class="text-xs font-jakarta text-text-secondary mt-1">Profil LinkedIn Anda sudah terlampir dan akan dilihat oleh industri.</p>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="!canSubmit || form.processing"
                            class="w-full px-5 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
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
                        <p v-if="hasAccepted" class="text-xs font-jakarta text-center text-text-secondary">Anda sudah menandatangani file Agreement dari industri.</p>
                        <p v-else-if="pendingCount >= maxApplications" class="text-xs font-jakarta text-center text-danger">Kuota lamaran aktif penuh ({{ maxApplications }}/{{ maxApplications }}).</p>
                    </form>
                </CardContainer>

            <!-- Right: Application History -->
            <CardContainer class="lg:col-span-2 h-fit">
                    <div class="border-b border-gray-100">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Riwayat Lamaran</h2>
                    </div>

                    <div v-if="pendaftarans.length > 0" class="divide-y divide-gray-50">
                        <div
                            v-for="p in pendaftarans"
                            :key="p.id"
                            class="py-4 hover:bg-gray-50/50 transition-colors"
                        >
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h3 class="text-sm font-jakartaSemiBold text-text-primary leading-tight">{{ p.industri.nama_perusahaan }}</h3>
                                <div class="flex flex-col items-end gap-1">
                                    <span :class="['text-xs px-2.5 py-0.5 rounded-full font-jakartaSemiBold shrink-0', statusBadge(p.agreement_rejected ? 'agreement_rejected' : p.status)]">
                                        {{ p.agreement_rejected ? 'Agreement Ditolak' : p.status_label }}
                                    </span>
                                </div>
                            </div>
                            <p v-if="p.industri.alamat" class="text-xs font-jakarta text-text-secondary mb-1.5 truncate">{{ p.industri.alamat }}</p>
                            <p class="text-xs font-jakarta text-text-secondary">{{ p.created_at }}</p>

                            <!-- Agreement Rejected info -->
                            <div v-if="p.agreement_rejected" class="mt-2.5 p-2.5 bg-amber-50 rounded-md border border-amber-200">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-xs font-jakartaSemiBold text-amber-700 mb-0.5">Anda menolak agreement dari industri ini</p>
                                        <p v-if="p.alasan_tolak_agreement" class="text-xs font-jakarta text-text-secondary">Alasan: {{ p.alasan_tolak_agreement }}</p>
                                        <p class="text-xs font-jakarta text-amber-600 mt-1">Anda dapat mengirim lamaran ulang ke industri ini atau industri lain.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Rejection reason -->
                            <div v-if="!p.agreement_rejected && p.status === 'ditolak' && p.keterangan" class="mt-2.5 p-2.5 bg-danger/5 rounded-md border border-danger/10">
                                <p class="text-xs font-jakartaSemiBold text-danger mb-0.5">Alasan Penolakan:</p>
                                <p class="text-xs font-jakarta text-text-primary">{{ p.keterangan }}</p>
                            </div>

                            <!-- Accepted info -->
                            <div v-if="!p.agreement_rejected && p.status === 'diterima' && p.keterangan" class="mt-2.5 p-2.5 bg-success/5 rounded-md border border-success/10">
                                <p class="text-xs font-jakartaSemiBold text-success mb-0.5">Catatan Industri:</p>
                                <p class="text-xs font-jakarta text-text-primary">{{ p.keterangan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="p-8 text-center">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-1">Belum Ada Lamaran</h3>
                        <p class="text-xs font-jakarta text-text-secondary">Kirim lamaran pertama Anda ke industri tujuan.</p>
                    </div>
            </CardContainer>
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
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { usePage, useForm, Link } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "../../Components/CardContainer.vue";

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
    linkedinFilled: { type: Boolean, default: false },
});

const searchQuery = ref('');

const form = useForm({
    industri_id: null,
    cv_exists: props.cvUploaded,
});

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

// Get display status for industry list (considers agreement rejection)
function getIndustriDisplayStatus(industriId) {
    const p = props.pendaftarans.find(a => a.industri.id === industriId);
    if (!p) return null;
    if (p.agreement_rejected) return 'agreement_rejected';
    return p.status;
}

function getIndustriDisplayLabel(industriId) {
    const p = props.pendaftarans.find(a => a.industri.id === industriId);
    if (!p) return '';
    if (p.agreement_rejected) return 'Agreement Ditolak';
    return p.status_label;
}

function isIndustriDisabled(industriId) {
    const status = getIndustriStatus(industriId);
    const pendaftaran = props.pendaftarans.find(a => a.industri.id === industriId);
    // Allow re-selection if the agreement was rejected for this industry
    if (pendaftaran?.agreement_rejected) return false;
    return status === 'pending' || status === 'diterima' || status === 'menunggu_mahasiswa' || props.hasAccepted;
}

const canSubmit = computed(() => {
    if (props.hasAccepted) return false;
    if (props.pendingCount >= props.maxApplications) return false;
    if (!form.industri_id) return false;
    if (!props.cvUploaded) return false;
    if (!props.linkedinFilled) return false;
    return true;
});

function statusBadge(status) {
    const map = {
        pending: 'text-amber-700',
        diterima: 'text-success',
        menunggu_mahasiswa: 'text-blue-700',
        ditolak: 'text-danger',
        agreement_rejected: 'text-amber-700',
    };
    return map[status] || 'bg-gray-100 text-gray-600';
}



function submitApplication() {
    form.post('/mahasiswa/kirim-cv', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('industri_id');
            searchQuery.value = '';
            // Update cv_exists state if it changed
            form.cv_exists = props.cvUploaded;
        },
    });
}
</script>
