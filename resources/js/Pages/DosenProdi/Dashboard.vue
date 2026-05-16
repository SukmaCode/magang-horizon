<template>
    <AuthenticatedLayout>
        <Head title="Monitoring Makro Prodi" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Monitoring Makro Prodi</h1>
            <p class="text-sm text-text-secondary mt-1">Pantau status keseluruhan mahasiswa magang di lingkup program studi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <CardContainer padding="p-6">
                <h3 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Total Mahasiswa Aktif</h3>
                <p class="text-xl font-jakartaSemiBold text-primary">{{ totalActive }}</p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">Sedang melaksanakan magang</p>
            </CardContainer>

            <CardContainer padding="p-6">
                <h3 class="text-sm font-jakartaSemiBold text-text-secondary uppercase tracking-wider mb-2">Total Lulus</h3>
                <p class="text-xl font-jakartaSemiBold text-success">{{ totalFinished }}</p>
                <p class="text-xs font-jakarta text-text-secondary mt-2">Mahasiswa telah menyelesaikan magang</p>
            </CardContainer>
        </div>

        <CardContainer padding="p-6">
            <h2 class="text-lg font-jakartaSemiBold text-text-primary mb-6">Mahasiswa Magang Terbaru</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/50 text-text-secondary font-jakartaSemiBold border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-jakarta rounded-tl-lg">Nama Mahasiswa</th>
                            <th class="px-4 py-3 font-jakarta">Industri Tujuan</th>
                            <th class="px-4 py-3 font-jakarta rounded-tr-lg">Status Tahapan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" v-if="recentMagangs.length > 0">
                        <tr v-for="magang in recentMagangs" :key="magang.id" class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-4 py-3 font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa }}</td>
                            <td class="px-4 py-3 text-text-secondary">{{ magang.industri }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs font-jakartaSemiBold rounded-full bg-primary/10 text-primary capitalize">
                                    {{ magang.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-text-secondary bg-gray-50/30">
                                Belum ada data mahasiswa magang.
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
    totalActive: Number,
    totalFinished: Number,
    recentMagangs: Array,
});
</script>
