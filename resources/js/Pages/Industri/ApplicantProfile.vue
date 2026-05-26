<template>
    <AuthenticatedLayout>
        <Head :title="`Profil ${mahasiswa.nama_lengkap}`" />

        <!-- Back Button -->
        <div class="mb-6">
            <Link href="/industri/seleksi-cv" class="inline-flex items-center gap-2 text-sm font-jakartaSemiBold text-text-secondary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Seleksi CV
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Profile Summary -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Profile Card -->
                <CardContainer>
                    <div class="flex flex-col items-center py-4">
                        <!-- Photo -->
                        <div class="mb-4">
                            <div v-if="mahasiswa.profile_photo_url" class="w-28 h-28 rounded-full overflow-hidden border-4 border-primary/10 shadow-md">
                                <img :src="mahasiswa.profile_photo_url" :alt="mahasiswa.nama_lengkap" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-28 h-28 rounded-full bg-linear-to-br from-primary/20 to-primary/5 border-4 border-primary/10 flex items-center justify-center shadow-md">
                                <span class="text-3xl font-jakartaSemiBold text-primary">{{ mahasiswa.nama_lengkap?.charAt(0) }}</span>
                            </div>
                        </div>

                        <h2 class="text-lg font-jakartaSemiBold text-text-primary text-center">{{ mahasiswa.nama_lengkap }}</h2>
                        <p class="text-sm font-jakarta text-text-secondary">{{ mahasiswa.nim }}</p>
                        <span class="mt-1 inline-block px-3 py-1 text-xs font-jakartaSemiBold text-primary bg-primary/10 rounded-full">
                            {{ mahasiswa.prodi || '-' }}
                        </span>
                    </div>

                    <!-- Contact -->
                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-jakarta text-text-primary truncate">{{ mahasiswa.email || '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm font-jakarta text-text-primary">{{ mahasiswa.nomor_hp || '—' }}</span>
                        </div>
                    </div>

                    <!-- Application Status -->
                    <div class="border-t border-gray-100 pt-4 mt-4">
                        <h4 class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-3">Status Lamaran</h4>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-jakarta text-text-primary">Status</span>
                            <span :class="['text-xs px-2.5 py-1 rounded-full font-jakartaSemiBold', statusBadge(pendaftaran.status)]">
                                {{ pendaftaran.status_label }}
                            </span>
                        </div>
                        <p class="text-xs font-jakarta text-text-secondary mt-2">Dilamar pada: {{ pendaftaran.created_at }}</p>
                        <div v-if="pendaftaran.keterangan" class="mt-3 p-2.5 bg-gray-50 rounded-lg">
                            <p class="text-xs font-jakartaSemiBold text-text-secondary mb-0.5">Catatan:</p>
                            <p class="text-xs font-jakarta text-text-primary">{{ pendaftaran.keterangan }}</p>
                        </div>
                    </div>
                </CardContainer>

                <!-- Actions -->
                <CardContainer>
                    <h4 class="text-sm font-jakartaSemiBold text-text-primary mb-4">Dokumen</h4>
                    <div class="space-y-3">
                        <a
                            v-if="mahasiswa.cv_url"
                            :href="mahasiswa.cv_url"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-jakartaSemiBold text-primary bg-primary/5 border border-primary/10 rounded-xl hover:bg-primary/10 transition-colors"
                        >
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat CV
                        </a>
                        <span v-else class="flex items-center gap-3 px-4 py-3 text-sm font-jakartaSemiBold text-amber-700 bg-amber-50 rounded-xl">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            CV Belum Diupload
                        </span>

                        <a
                            v-if="mahasiswa.linkedin_url"
                            :href="mahasiswa.linkedin_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-jakartaSemiBold text-blue-700 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-100 transition-colors"
                        >
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            Buka LinkedIn
                        </a>
                        <span v-else class="flex items-center gap-3 px-4 py-3 text-sm font-jakartaSemiBold text-gray-500 bg-gray-50 rounded-xl">
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            LinkedIn Belum Diisi
                        </span>
                    </div>
                </CardContainer>
            </div>

            <!-- Right: Detailed Info -->
            <CardContainer class="lg:col-span-2 h-fit">
                <div class="border-b border-gray-100 pb-4 mb-6">
                    <h2 class="text-base font-jakartaSemiBold text-text-primary">Profil Profesional</h2>
                    <p class="text-xs font-jakarta text-text-secondary mt-1">Informasi detail tentang mahasiswa pelamar.</p>
                </div>

                <!-- Bio -->
                <div class="mb-8">
                    <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Tentang
                    </h3>
                    <div v-if="mahasiswa.bio" class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-jakarta text-text-primary leading-relaxed whitespace-pre-line">{{ mahasiswa.bio }}</p>
                    </div>
                    <p v-else class="text-sm font-jakarta text-text-secondary italic">Belum ada bio.</p>
                </div>

                <!-- Skills -->
                <div class="mb-8">
                    <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Skill & Keahlian
                    </h3>
                    <div v-if="mahasiswa.skills" class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-sm font-jakarta text-text-primary leading-relaxed whitespace-pre-line">{{ mahasiswa.skills }}</p>
                    </div>
                    <p v-else class="text-sm font-jakarta text-text-secondary italic">Belum ada deskripsi skill.</p>
                </div>

                <!-- Academic Info -->
                <div>
                    <h3 class="text-sm font-jakartaSemiBold text-text-primary mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Informasi Akademik
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Nama Lengkap</p>
                            <p class="text-sm font-jakarta text-text-primary">{{ mahasiswa.nama_lengkap }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">NIM</p>
                            <p class="text-sm font-jakarta text-text-primary">{{ mahasiswa.nim }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Program Studi</p>
                            <p class="text-sm font-jakarta text-text-primary">{{ mahasiswa.prodi || '-' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <p class="text-xs font-jakartaSemiBold text-text-secondary mb-1">Email</p>
                            <p class="text-sm font-jakarta text-text-primary truncate">{{ mahasiswa.email || '-' }}</p>
                        </div>
                    </div>
                </div>
            </CardContainer>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

defineProps({
    pendaftaran: { type: Object, required: true },
    mahasiswa: { type: Object, required: true },
});

function statusBadge(status) {
    return {
        pending: 'text-amber-700 bg-amber-50',
        diterima: 'text-success bg-success/10',
        ditolak: 'text-danger bg-danger/5',
        menunggu_mahasiswa: 'text-blue-700 bg-blue-50',
    }[status] || 'text-gray-600 bg-gray-100';
}
</script>
