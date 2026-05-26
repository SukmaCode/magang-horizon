<template>
    <AuthenticatedLayout>
        <Head title="Sertifikat Kelulusan" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Sertifikat Kelulusan</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Pantau status kelulusan dan unduh sertifikat magang Anda.</p>
        </div>

        <!-- No Magang -->
        <div v-if="!hasMagang" class="p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-jakartaSemiBold text-text-primary">Belum memiliki magang aktif</p>
                <p class="text-xs font-jakarta text-text-secondary mt-1">Silakan ajukan lamaran magang terlebih dahulu.</p>
            </div>
        </div>

        <template v-else>
            <!-- Graduation Status Card -->
            <div
                :class="[
                    'rounded-2xl p-8 mb-8 text-center transition-all duration-500',
                    isLulus
                        ? 'bg-linear-to-br from-primary/10 via-primary/5 to-accent/5 border border-primary/20'
                        : 'bg-card border border-gray-100'
                ]"
            >
                <!-- Lulus State -->
                <template v-if="isLulus">
                    <div class="mb-4">
                        <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4 animate-bounce-subtle">
                            <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-jakartaSemiBold text-primary">Selamat, Anda Telah Lulus! 🎉</h2>
                        <p class="text-sm font-jakarta text-text-secondary mt-2">Magang Anda telah resmi selesai. Unduh sertifikat Anda di bawah.</p>
                    </div>
                </template>

                <!-- Not Lulus State -->
                <template v-else>
                    <div class="mb-4">
                        <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-jakartaSemiBold text-text-primary">Belum Lulus</h2>
                        <p class="text-sm font-jakarta text-text-secondary mt-2">Selesaikan seluruh tahapan magang untuk mendapatkan sertifikat.</p>
                    </div>
                </template>
            </div>

            <!-- Grading Info -->
            <div v-if="penilaian" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-card rounded-xl border border-gray-100 p-5">
                    <p class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Nilai Industri</p>
                    <p class="text-2xl font-jakartaSemiBold text-text-primary">
                        {{ penilaian.nilai_industri != null ? penilaian.nilai_industri : '—' }}
                    </p>
                </div>
                <div class="bg-card rounded-xl border border-gray-100 p-5">
                    <p class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Nilai Akademis</p>
                    <p class="text-2xl font-jakartaSemiBold text-text-primary">
                        {{ penilaian.nilai_kampus != null ? penilaian.nilai_kampus : '—' }}
                    </p>
                </div>
                <div class="bg-card rounded-xl border border-primary/20 p-5">
                    <p class="text-xs font-jakartaSemiBold text-primary uppercase tracking-wider mb-2">Nilai Akhir</p>
                    <p class="text-2xl font-jakartaSemiBold text-primary">
                        {{ penilaian.nilai_akhir != null ? penilaian.nilai_akhir : '—' }}
                    </p>
                    <span
                        v-if="penilaian.is_verified"
                        class="inline-flex items-center gap-1 text-xs text-success font-jakartaSemiBold mt-2"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Terverifikasi
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center gap-1 text-xs text-amber-600 font-jakartaSemiBold mt-2"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Menunggu verifikasi
                    </span>
                </div>
            </div>

            <!-- Graduation Requirements Checklist -->
            <div v-if="!isLulus" class="bg-card rounded-xl border border-gray-100 p-6 mb-8">
                <h3 class="text-base font-jakartaSemiBold text-text-primary mb-4">Syarat Kelulusan</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-lg" :class="penilaian?.nilai_industri != null ? 'bg-success/5' : 'bg-gray-50'">
                        <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0', penilaian?.nilai_industri != null ? 'bg-success/20 text-success' : 'bg-gray-200 text-gray-400']">
                            <svg v-if="penilaian?.nilai_industri != null" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span v-else class="text-xs font-jakartaSemiBold">1</span>
                        </div>
                        <span class="text-sm font-jakartaSemiBold" :class="penilaian?.nilai_industri != null ? 'text-success' : 'text-text-secondary'">Nilai dari Industri</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg" :class="penilaian?.nilai_kampus != null ? 'bg-success/5' : 'bg-gray-50'">
                        <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0', penilaian?.nilai_kampus != null ? 'bg-success/20 text-success' : 'bg-gray-200 text-gray-400']">
                            <svg v-if="penilaian?.nilai_kampus != null" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span v-else class="text-xs font-jakartaSemiBold">2</span>
                        </div>
                        <span class="text-sm font-jakartaSemiBold" :class="penilaian?.nilai_kampus != null ? 'text-success' : 'text-text-secondary'">Nilai dari Kampus</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-lg" :class="penilaian?.is_verified ? 'bg-success/5' : 'bg-gray-50'">
                        <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0', penilaian?.is_verified ? 'bg-success/20 text-success' : 'bg-gray-200 text-gray-400']">
                            <svg v-if="penilaian?.is_verified" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span v-else class="text-xs font-jakartaSemiBold">3</span>
                        </div>
                        <span class="text-sm font-jakartaSemiBold" :class="penilaian?.is_verified ? 'text-success' : 'text-text-secondary'">Verifikasi Admin</span>
                    </div>
                </div>
            </div>

            <!-- Certificate Download -->
            <div v-if="sertifikat" class="bg-card rounded-xl border border-primary/20 p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-jakartaSemiBold text-text-primary">Sertifikat Magang</h3>
                        <p class="text-xs text-text-secondary mt-1">No: {{ sertifikat.nomor_sertifikat }}</p>
                        <p class="text-xs text-text-secondary">Tanggal Terbit: {{ sertifikat.tanggal_terbit }}</p>
                    </div>
                    <a
                        v-if="sertifikat.has_file"
                        :href="url('/mahasiswa/sertifikat/download')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-jakartaSemiBold rounded-xl hover:bg-primary-hover transition-colors duration-200 shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>
                    <span v-else class="text-xs text-amber-600 bg-amber-50 px-3 py-1.5 rounded-full font-jakartaSemiBold">
                        Sertifikat sedang diproses
                    </span>
                </div>
            </div>
        </template>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.error" class="fixed bottom-6 right-6 bg-danger text-white px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50">
                {{ flash.error }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { url } from '@/utils/prefix';

const page = usePage();
const flash = computed(() => page.props.flash || {});

defineProps({
    sertifikat: { type: Object, default: null },
    penilaian: { type: Object, default: null },
    isLulus: { type: Boolean, default: false },
    hasMagang: { type: Boolean, default: false },
});
</script>

<style scoped>
@keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
.animate-bounce-subtle {
    animation: bounce-subtle 2s ease-in-out infinite;
}
</style>
