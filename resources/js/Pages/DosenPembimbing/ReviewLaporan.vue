<template>
    <AuthenticatedLayout>
        <Head title="Review Laporan Akhir" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">
                Review Laporan Akhir
            </h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">
                Berikan persetujuan atau catatan revisi pada laporan akhir
                mahasiswa.
            </p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="border-b border-gray-100">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">
                    Daftar Laporan Akhir
                </h2>
            </div>

            <div
                v-if="laporans.data && laporans.data.length > 0"
                class="divide-y divide-gray-50"
            >
                <div
                    v-for="laporan in laporans.data"
                    :key="laporan.id"
                    class="py-6"
                >
                    <div
                        class="flex flex-col md:flex-row gap-4 justify-between md:items-center"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-jakartaSemiBold shrink-0"
                            >
                                {{ laporan.mahasiswa.nama_lengkap.charAt(0) }}
                            </div>
                            <div>
                                <h3
                                    class="text-base font-jakartaSemiBold text-text-primary"
                                >
                                    {{ laporan.mahasiswa.nama_lengkap }} - {{ laporan.mahasiswa.nim }}
                                </h3>
                                <p class="text-xs font-jakarta text-text-secondary mt-0.5">
                                    Diperbarui: {{ laporan.updated_at }}
                                </p>

                                <div class="mt-2 flex items-center gap-3">
                                    <span
                                        :class="[
                                            'text-xs py-0.5 rounded-full font-jakartaSemiBold',
                                            statusBadge(laporan.status),
                                        ]"
                                    >
                                        {{ laporan.status_label }}
                                    </span>
                                    <a
                                        v-if="laporan.file_laporan"
                                        :href="`/dosen-pembimbing/review-laporan/${laporan.id}/download`"
                                        target="_blank"
                                        class="text-xs font-jakartaSemiBold text-primary hover:text-primary-hover flex items-center gap-1"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        Buka PDF
                                    </a>
                                    <a
                                        v-if="laporan.approval_letter_file"
                                        :href="`/dosen-pembimbing/review-laporan/${laporan.id}/download-approval`"
                                        target="_blank"
                                        class="text-xs font-jakartaSemiBold text-success hover:text-success/80 flex items-center gap-1"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        Approval Letter
                                    </a>
                                </div>
                                <div
                                    v-if="laporan.catatan_revisi"
                                    class="w-fit mt-2 text-xs font-jakartaSemiBold px-3 py-2 rounded-md border"
                                    :class="
                                        laporan.status === 'disetujui'
                                            ? 'text-success bg-success/5 border-success/10'
                                            : 'text-danger bg-danger/5 border-danger/10'
                                    "
                                >
                                    {{
                                        laporan.status === "disetujui"
                                            ? ""
                                            : "Catatan Revisi: "
                                    }} {{ laporan.catatan_revisi }}
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex gap-2">
                            <button
                                v-if="laporan.status !== 'disetujui'"
                                @click="openReviewModal(laporan, 'revisi')"
                                class="px-4 py-2 text-sm font-jakartaSemiBold text-danger border border-danger/20 rounded-md hover:bg-danger/5 transition-colors"
                            >
                                Revisi
                            </button>
                            <button
                                v-if="laporan.status !== 'disetujui'"
                                @click="openReviewModal(laporan, 'disetujui')"
                                class="px-4 py-2 text-sm font-jakartaSemiBold text-white bg-success rounded-md hover:bg-success/90 transition-colors shadow-sm"
                            >
                                Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="p-12 text-center">
                <svg
                    class="w-16 h-16 text-gray-200 mx-auto mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">
                    Belum Ada Laporan
                </h3>
                <p class="text-sm text-text-secondary">
                    Tidak ada laporan yang perlu direview.
                </p>
            </div>

            <!-- Pagination -->
            <div
                v-if="laporans.links && laporans.links.length > 3"
                class="px-6 py-4 border-t border-gray-100 flex justify-center gap-1"
            >
                <template v-for="link in laporans.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3.5 py-2 text-sm rounded-md transition-colors duration-200"
                        :class="[
                            link.active
                                ? 'bg-primary text-white font-jakartaSemiBold'
                                : 'text-text-secondary hover:bg-gray-100',
                        ]"
                        v-html="link.label"
                        preserve-scroll
                    />
                    <span
                        v-else
                        class="px-3.5 py-2 text-sm text-gray-300"
                        v-html="link.label"
                    />
                </template>
            </div>
        </CardContainer>

        <div class="mb-4 mt-12">
            <h2 class="text-xl font-jakartaSemiBold text-text-primary">
                Konfirmasi Bimbingan Mahasiswa
            </h2>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Konfirmasi catatan bimbingan sebagai syarat upload laporan akhir (minimal 8).</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Mahasiswa List -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-card rounded-md border border-gray-100 overflow-hidden shadow-sm">
                    <div class="px-4 py-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm font-jakartaSemiBold text-text-primary">Mahasiswa Bimbingan</h2>
                    </div>
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-50">
                        <div v-if="Object.keys(bimbingans || {}).length === 0" class="p-4 text-center">
                            <p class="text-xs text-text-secondary">Belum ada riwayat bimbingan.</p>
                        </div>
                        <template v-else>
                            <button
                                v-for="(group, magangId) in bimbingans"
                                :key="magangId"
                                @click="selectedBimbinganMagangId = magangId"
                                :class="['w-full text-left block p-4 transition-colors', selectedBimbinganMagangId == magangId ? 'bg-primary/5 border-l-4 border-primary' : 'hover:bg-gray-50 border-l-4 border-transparent']"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-jakartaSemiBold text-text-primary truncate">{{ group[0].mahasiswa_nama }}</p>
                                    </div>
                                    <span class="text-xs font-jakartaSemiBold text-text-secondary shrink-0">{{ group.length }} Bimb.</span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Main: Bimbingan List -->
            <div class="lg:col-span-3">
                <CardContainer v-if="selectedBimbinganMagangId && bimbingans[selectedBimbinganMagangId]" class="overflow-hidden">
                    <div class="border-b border-gray-100">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Riwayat Bimbingan</h2>
                    </div>

                    <div class="py-6">
                        <div class="space-y-3">
                            <div v-for="b in bimbingans[selectedBimbinganMagangId]" :key="b.id" class="flex items-start justify-between gap-4 p-4 rounded-md border border-gray-100" :class="b.is_approved ? 'bg-success/5 border-success/20' : 'bg-white'">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-jakartaSemiBold" :class="b.is_approved ? 'text-success' : 'text-amber-700'">
                                            {{ b.is_approved ? 'Disetujui' : 'Menunggu Validasi' }}
                                        </span>
                                        <span class="text-xs font-jakarta text-text-secondary">{{ b.tanggal_display }}</span>
                                    </div>
                                    <p class="text-sm font-jakarta text-text-primary mt-2">{{ b.catatan }}</p>
                                </div>
                                <div v-if="!b.is_approved" class="flex gap-2 shrink-0">
                                    <Link :href="`/dosen-pembimbing/review-laporan/bimbingan/${b.id}/reject`" method="post" as="button" preserve-scroll class="px-3 py-1.5 text-xs font-jakartaSemiBold text-danger border border-danger/20 rounded-md hover:bg-danger/5 transition-colors">Tolak</Link>
                                    <Link :href="`/dosen-pembimbing/review-laporan/bimbingan/${b.id}/approve`" method="post" as="button" preserve-scroll class="px-3 py-1.5 text-xs font-jakartaSemiBold text-white bg-success rounded-md hover:bg-success/90 transition-colors shadow-sm">Setujui</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContainer>
                
                <div v-else class="h-full flex flex-col items-center justify-center p-12 bg-gray-50 rounded-md border border-dashed border-gray-200 min-h-[300px]">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                    <p class="text-sm text-text-secondary">Pilih mahasiswa di samping untuk melihat riwayat bimbingan.</p>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showReviewModal"
                class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4"
                @click.self="closeModal"
            >
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div
                        class="px-6 py-5 border-b border-gray-100 flex justify-between items-center"
                    >
                        <h3
                            class="text-lg font-jakartaSemiBold"
                            :class="
                                reviewType === 'disetujui'
                                    ? 'text-success'
                                    : 'text-danger'
                            "
                        >
                            {{
                                reviewType === "disetujui"
                                    ? "Setujui Laporan"
                                    : "Berikan Revisi"
                            }}
                        </h3>
                        <button
                            @click="closeModal"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitReview" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">
                            Tindakan ini akan mengirimkan status
                            {{
                                reviewType === "disetujui"
                                    ? "Disetujui"
                                    : "Revisi"
                            }}
                            untuk laporan
                            <strong>{{
                                selectedLaporan?.mahasiswa.nama_lengkap
                            }}</strong
                            >.
                        </p>
                        <div class="mb-5" v-if="reviewType === 'revisi'">
                            <label
                                class="block text-sm font-jakartaSemiBold text-text-primary mb-2"
                                >Catatan (Wajib jika revisi)
                                <span class="text-danger">*</span></label
                            >
                            <textarea
                                v-model="reviewForm.catatan"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-200 rounded-md text-sm focus:ring-primary/20 focus:border-primary"
                                required
                                placeholder="Berikan catatan detail..."
                            ></textarea>
                            <div v-if="reviewForm.errors.catatan" class="text-danger text-xs mt-1">{{ reviewForm.errors.catatan }}</div>
                        </div>

                        <div class="mb-5" v-if="reviewType === 'disetujui'">
                            <p class="text-sm text-text-secondary mb-3">
                                Approval Letter akan di-generate secara otomatis saat Anda menyetujui laporan ini.
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="closeModal"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold border border-gray-200 rounded-md hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="reviewForm.processing"
                                class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white rounded-md flex justify-center items-center gap-2"
                                :class="
                                    reviewType === 'disetujui'
                                        ? 'bg-success hover:bg-success/90'
                                        : 'bg-danger hover:bg-danger/90'
                                "
                            >
                                <span
                                    v-if="reviewForm.processing"
                                    class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"
                                ></span>
                                Konfirmasi
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
            <div
                v-if="flashMsg"
                :class="[
                    'fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-jakartaSemiBold z-50',
                    flashType === 'success'
                        ? 'bg-success text-white'
                        : 'bg-danger text-white',
                ]"
            >
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, useForm, Link } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(
    () => flash.value.success || flash.value.error || null,
);
const flashType = computed(() => (flash.value.success ? "success" : "error"));

const props = defineProps({
    laporans: Object,
    bimbingans: Object,
});

const selectedBimbinganMagangId = ref(
    props.bimbingans && Object.keys(props.bimbingans).length > 0
        ? Object.keys(props.bimbingans)[0]
        : null
);

function statusBadge(status) {
    return (
        {
            pending: "bg-amber-50 text-amber-700",
            disetujui: "text-success",
            revisi: "bg-danger/10 text-danger",
        }[status] || "bg-gray-100 text-gray-600"
    );
}

const showReviewModal = ref(false);
const selectedLaporan = ref(null);
const reviewType = ref("disetujui");
const reviewForm = useForm({ status: "", catatan: "" });

function openReviewModal(laporan, type) {
    selectedLaporan.value = laporan;
    reviewType.value = type;
    reviewForm.status = type;
    reviewForm.catatan = laporan.catatan_revisi || "";
    reviewForm.clearErrors();
    showReviewModal.value = true;
}

function closeModal() {
    showReviewModal.value = false;
    selectedLaporan.value = null;
    reviewForm.reset();
    reviewForm.clearErrors();
}

function submitReview() {
    if (!selectedLaporan.value) return;
    reviewForm.post(
        `/dosen-pembimbing/review-laporan/${selectedLaporan.value.id}/review`,
        {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: closeModal,
        },
    );
}
</script>
