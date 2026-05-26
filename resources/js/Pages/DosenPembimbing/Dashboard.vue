<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Dosen Pembimbing" />

        <div
            class="mb-8 flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center"
        >
            <div>
                <h1 class="text-3xl font-jakartaSemiBold text-text-primary">
                    Dashboard
                </h1>
                <p class="text-sm font-jakarta text-text-secondary">
                    Pantau perkembangan mahasiswa bimbingan Anda.
                </p>
            </div>
            <a
                v-if="suratKeputusan"
                :href="`/dosen-pembimbing/surat-keputusan/${suratKeputusan.id}/download`"
                class="w-fit inline-flex items-center gap-1.5 text-xs font-jakartaSemiBold text-primary transition-colors"
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
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                    />
                </svg>
                Download SK ({{ suratKeputusan.nomor_sk }})
            </a>
            <span v-else class="text-xs font-jakarta text-text-secondary italic"
                >SK belum tersedia</span
            >
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <CardContainer padding="p-6">
                <h3
                    class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Mahasiswa Bimbingan
                </h3>
                <p class="text-lg font-jakartaSemiBold text-text-primary">
                    {{ activeStudents }} Mahasiswa
                </p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">
                    Sedang melaksanakan magang
                </p>
            </CardContainer>

            <CardContainer
                padding="p-6"
                :class="pendingLaporan > 0 ? 'border-l-4 border-l-accent' : ''"
            >
                <h3
                    class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Laporan Perlu Review
                </h3>
                <p class="text-lg font-jakartaSemiBold text-text-primary">
                    {{ pendingLaporan }}
                </p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">
                    Menunggu persetujuan Anda
                </p>
            </CardContainer>

            <CardContainer
                padding="p-6"
                :class="studentsToGrade > 0 ? 'border-l-4 border-l-danger' : ''"
            >
                <h3
                    class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2"
                >
                    Belum Dinilai
                </h3>
                <p class="text-lg font-jakartaSemiBold text-text-primary">
                    {{ studentsToGrade }} Mahasiswa
                </p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">
                    Perlu input nilai akademis
                </p>
            </CardContainer>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <CardContainer padding="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2
                            class="text-lg font-jakartaSemiBold text-text-primary"
                        >
                            Laporan Akhir Terbaru
                        </h2>
                        <Link
                            href="/internship/dosen-pembimbing/review-laporan"
                            class="text-sm text-primary font-jakartaSemiBold hover:text-primary-hover"
                            >Lihat Semua</Link
                        >
                    </div>

                    <div v-if="recentLaporan.length > 0" class="space-y-3">
                        <div
                            v-for="laporan in recentLaporan"
                            :key="laporan.id"
                            class="flex items-center justify-between gap-4 p-4 bg-gray-50/50 rounded-xl border border-gray-100"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-jakartaSemiBold"
                                >
                                    {{ laporan.mahasiswa.charAt(0) }}
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-jakartaSemiBold text-text-primary"
                                    >
                                        {{ laporan.mahasiswa }}
                                    </p>
                                    <p class="text-xs font-jakarta text-text-secondary">
                                        {{ laporan.nim }} ·
                                        {{ laporan.updated_at }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span
                                    :class="[
                                        'text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold',
                                        statusBadge(laporan.status),
                                    ]"
                                >
                                    {{ laporan.status_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="p-8 text-center text-text-secondary text-sm bg-gray-50 rounded-xl border border-dashed"
                    >
                        Belum ada laporan akhir yang diupload mahasiswa
                        bimbingan.
                    </div>
                </CardContainer>
            </div>

            <div class="lg:col-span-1">
                <SignatureUpload :has-signature="hasSignature" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";
import SignatureUpload from "@/Components/SignatureUpload.vue";

defineProps({
    activeStudents: Number,
    pendingLaporan: Number,
    studentsToGrade: Number,
    recentLaporan: Array,
    hasSignature: { type: Boolean, default: false },
    suratKeputusan: { type: Object, default: null },
});

function statusBadge(status) {
    return (
        {
            pending: "bg-amber-50 text-amber-700",
            disetujui: "bg-success/10 text-success",
            revisi: "bg-danger/10 text-danger",
        }[status] || "bg-gray-100 text-gray-600"
    );
}
</script>
