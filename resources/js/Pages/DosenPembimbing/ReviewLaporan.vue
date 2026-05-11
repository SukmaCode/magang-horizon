<template>
    <AuthenticatedLayout>
        <Head title="Review Laporan Akhir" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">
                Review Laporan Akhir
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Berikan persetujuan atau catatan revisi pada laporan akhir
                mahasiswa.
            </p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="border-b border-gray-100">
                <h2 class="text-base font-bold text-text-primary font-jakarta">
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
                                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0"
                            >
                                {{ laporan.mahasiswa.nama_lengkap.charAt(0) }}
                            </div>
                            <div>
                                <h3
                                    class="text-base font-bold text-text-primary"
                                >
                                    {{ laporan.mahasiswa.nama_lengkap }}
                                </h3>
                                <p class="text-sm text-text-secondary">
                                    {{ laporan.mahasiswa.nim }}
                                </p>
                                <p class="text-xs text-text-secondary mt-0.5">
                                    Diperbarui: {{ laporan.updated_at }}
                                </p>

                                <div class="mt-2 flex items-center gap-3">
                                    <span
                                        :class="[
                                            'text-xs py-0.5 rounded-full font-medium',
                                            statusBadge(laporan.status),
                                        ]"
                                    >
                                        {{ laporan.status_label }}
                                    </span>
                                    <a
                                        v-if="laporan.file_laporan"
                                        :href="`/dosen-pembimbing/review-laporan/${laporan.id}/download`"
                                        target="_blank"
                                        class="text-xs font-semibold text-primary hover:text-primary-hover flex items-center gap-1"
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
                                        class="text-xs font-semibold text-success hover:text-success/80 flex items-center gap-1"
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
                                    class="w-fit mt-2 text-xs font-medium px-3 py-2 rounded-lg border"
                                    :class="
                                        laporan.status === 'disetujui'
                                            ? 'text-success bg-success/5 border-success/10'
                                            : 'text-danger bg-danger/5 border-danger/10'
                                    "
                                >
                                    {{
                                        laporan.status === "disetujui"
                                            ? ""
                                            : "Revisi :"
                                    }} {{ laporan.catatan_revisi }}
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0 flex gap-2">
                            <button
                                v-if="laporan.status !== 'disetujui'"
                                @click="openReviewModal(laporan, 'revisi')"
                                class="px-4 py-2 text-sm font-semibold text-danger border border-danger/20 rounded-md hover:bg-danger/5 transition-colors"
                            >
                                Revisi
                            </button>
                            <button
                                v-if="laporan.status !== 'disetujui'"
                                @click="openReviewModal(laporan, 'disetujui')"
                                class="px-4 py-2 text-sm font-semibold text-white bg-success rounded-md hover:bg-success/90 transition-colors shadow-sm"
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
                <h3 class="text-base font-bold text-text-primary mb-1">
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
                        class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200"
                        :class="[
                            link.active
                                ? 'bg-primary text-white font-semibold'
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
                            class="text-lg font-bold"
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
                                class="block text-sm font-semibold text-text-primary mb-2"
                                >Catatan (Wajib jika revisi)
                                <span class="text-danger">*</span></label
                            >
                            <textarea
                                v-model="reviewForm.catatan"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-primary/20 focus:border-primary"
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
                                class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="reviewForm.processing"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white rounded-xl flex justify-center items-center gap-2"
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
                    'fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50',
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

defineProps({
    laporans: Object,
});

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
