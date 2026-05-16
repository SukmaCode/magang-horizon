<template>
    <AuthenticatedLayout>
        <Head title="Verifikasi Kelulusan (Admin)" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary font-jakarta">Verifikasi Kelulusan</h1>
            <p class="text-sm text-text-secondary mt-1">Terbitkan sertifikat kelulusan resmi untuk mahasiswa yang telah disetujui Dosen Prodi.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <CardContainer class="overflow-hidden h-full">
                    <div class="border-b border-gray-100">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Menunggu Penerbitan Sertifikat</h2>
                    </div>

                    <div v-if="magangs.length > 0" class="divide-y divide-gray-50">
                        <div v-for="magang in magangs" :key="magang.id" class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h3 class="text-base font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa }}</h3>
                                        <span class="px-2 py-0.5 text-[10px] font-jakartaSemiBold uppercase tracking-wider bg-success/10 text-success rounded">Telah Disetujui Prodi</span>
                                    </div>
                                    <p class="text-sm text-text-secondary">{{ magang.nim }} · {{ magang.industri }}</p>
                                    
                                    <div class="mt-2 inline-flex items-center gap-2 text-sm">
                                        <span class="text-text-secondary">Nilai Akhir:</span>
                                        <span class="font-jakartaSemiBold text-text-primary">{{ magang.nilai_akhir }}</span>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    <form @submit.prevent="terbitkan(magang.id)">
                                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-jakartaSemiBold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                                            Terbitkan Sertifikat
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Tidak Ada Data</h3>
                        <p class="text-sm text-text-secondary">Tidak ada mahasiswa yang menunggu penerbitan sertifikat saat ini.</p>
                    </div>
                </CardContainer>
            </div>

            <!-- Recent Certificates -->
            <div class="lg:col-span-1">
                <CardContainer class="h-full">
                    <div class="border-b border-gray-100 mb-2">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Sertifikat Terbaru</h2>
                    </div>
                    <div class="">
                        <div v-if="sertifikats.length > 0" class="space-y-4">
                            <div v-for="cert in sertifikats" :key="cert.id" class="p-4 border border-gray-100 rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors cursor-default">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center text-accent shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-jakartaSemiBold text-text-primary truncate">{{ cert.nomor }}</p>
                                        <p class="text-[10px] text-text-secondary">{{ cert.tanggal }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-jak  artaSemiBold text-text-primary pl-11">{{ cert.mahasiswa }}</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-sm text-text-secondary">
                            Belum ada sertifikat yang diterbitkan.
                        </div>
                    </div>
                </CardContainer>
            </div>
        </div>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    magangs: Array,
    sertifikats: Array,
});

function terbitkan(id) {
    if(confirm('Terbitkan sertifikat kelulusan untuk mahasiswa ini?')) {
        router.post(`/admin/verifikasi-kelulusan/${id}/terbitkan`, {}, { preserveScroll: true });
    }
}
</script>
