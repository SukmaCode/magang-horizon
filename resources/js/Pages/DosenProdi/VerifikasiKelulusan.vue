<template>
    <AuthenticatedLayout>
        <Head title="Verifikasi Kelulusan" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Verifikasi Kelulusan</h1>
            <p class="text-sm text-text-secondary mt-1">Review rekap nilai dan berikan tanda tangan digital untuk persetujuan kelulusan akhir.</p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="border-b border-gray-100">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Daftar Mahasiswa Siap Lulus</h2>
            </div>

            <div v-if="magangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in magangs" :key="magang.id" class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-base font-jakartaSemiBold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm text-text-secondary">{{ magang.mahasiswa.nim }}</p>
                            
                            <div class="mt-4 grid grid-cols-3 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100 max-w-lg">
                                <div>
                                    <p class="text-[10px] uppercase font-jakartaSemiBold text-text-secondary mb-1">Nilai Industri</p>
                                    <p class="text-lg font-jakartaSemiBold text-text-primary">{{ magang.nilai_industri }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-jakartaSemiBold text-text-secondary mb-1">Nilai Kampus</p>
                                    <p class="text-lg font-jakartaSemiBold text-text-primary">{{ magang.nilai_kampus }}</p>
                                </div>
                                <div class="border-l border-gray-200 pl-4">
                                    <p class="text-[10px] uppercase font-jakartaSemiBold text-text-secondary mb-1">Nilai Akhir</p>
                                    <p class="text-lg font-jakartaSemiBold text-success">{{ magang.nilai_akhir }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <button @click="openVerifyModal(magang)" class="inline-flex items-center gap-2 px-5 py-3 text-sm font-jakartaSemiBold text-white bg-success rounded-xl hover:bg-success/90 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                TTD Persetujuan Kelulusan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                <h3 class="text-base font-jakartaSemiBold text-text-primary mb-1">Tidak Ada Data</h3>
                <p class="text-sm text-text-secondary">Tidak ada mahasiswa yang menunggu verifikasi kelulusan saat ini.</p>
            </div>
        </CardContainer>

        <!-- Verify Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showVerifyModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-jakartaSemiBold text-text-primary">Persetujuan Kelulusan</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitVerify" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">
                            Dengan ini Anda menyetujui kelulusan <strong>{{ selectedMagang?.mahasiswa.nama_lengkap }}</strong> dengan nilai akhir <strong>{{ selectedMagang?.nilai_akhir }}</strong>. Sistem akan otomatis menandatangani dokumen dan menerbitkan sertifikat.
                        </p>
                        
                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="verifyForm.processing" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-success rounded-xl hover:bg-success/90 flex justify-center items-center gap-2">
                                <span v-if="verifyForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Setujui & TTD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    magangs: Array
});

const showVerifyModal = ref(false);
const selectedMagang = ref(null);
const verifyForm = useForm({});

function openVerifyModal(magang) {
    selectedMagang.value = magang;
    showVerifyModal.value = true;
}

function closeModal() {
    showVerifyModal.value = false;
    selectedMagang.value = null;
}

function submitVerify() {
    if(!selectedMagang.value) return;
    verifyForm.post(`/dosen-prodi/verifikasi-kelulusan/${selectedMagang.value.penilaian_id}/verify`, {
        preserveScroll: true,
        onSuccess: closeModal
    });
}
</script>
