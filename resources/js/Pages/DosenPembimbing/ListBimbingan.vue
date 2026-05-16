<template>
    <AuthenticatedLayout>
        <Head title="Mahasiswa Bimbingan" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Mahasiswa Bimbingan</h1>
            <p class="text-sm text-text-secondary mt-1">Daftar mahasiswa magang yang menjadi tanggung jawab bimbingan Anda.</p>
        </div>

        <CardContainer class="mb-6">
            <div class="py-1 border-b border-gray-100 flex justify-between items-center mb-4">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Daftar Bimbingan</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-text-secondary uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 font-jakartaSemiBold">Mahasiswa</th>
                            <th class="px-4 py-3 font-jakartaSemiBold">Perusahaan / Industri</th>
                            <th class="px-4 py-3 font-jakartaSemiBold text-center">SK Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="assignment in assignments" :key="assignment.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-jakartaSemiBold text-text-primary">{{ assignment.magang_aktif.pendaftaran.mahasiswa.user.name }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-text-primary">{{ assignment.magang_aktif.pendaftaran.industri.nama_industri || 'N/A' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a 
                                    v-if="assignment.surat_keputusan"
                                    :href="`/dosen-pembimbing/surat-keputusan/${assignment.surat_keputusan.id}/download`" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-xs font-jakartaSemiBold text-primary bg-primary/10 hover:bg-primary/20 px-3 py-1.5 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Download SK
                                </a>
                                <span v-else class="text-xs text-text-secondary italic">
                                    Belum Tersedia
                                </span>
                            </td>
                        </tr>
                        <tr v-if="assignments.length === 0">
                            <td colspan="3" class="px-4 py-8 text-center text-text-secondary">
                                Belum ada mahasiswa bimbingan yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContainer>

    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    assignments: Array,
});
</script>
