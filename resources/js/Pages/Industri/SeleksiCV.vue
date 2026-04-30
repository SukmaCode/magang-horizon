<template>
    <AuthenticatedLayout>
        <Head title="Seleksi CV Mahasiswa" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Seleksi CV Mahasiswa</h1>
            <p class="text-sm text-text-secondary mt-1">Review dan evaluasi lamaran magang yang masuk.</p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-text-primary font-jakarta">Daftar Lamaran</h2>
            </div>

            <div v-if="pendaftarans.data && pendaftarans.data.length > 0" class="divide-y divide-gray-50">
                <div v-for="app in pendaftarans.data" :key="app.id" class="p-6 hover:bg-gray-50/50 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary text-lg font-bold shrink-0">
                                {{ app.mahasiswa.nama_lengkap.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-text-primary">{{ app.mahasiswa.nama_lengkap }}</h3>
                                <p class="text-sm text-text-secondary">{{ app.mahasiswa.nim }} · {{ app.mahasiswa.prodi }}</p>
                                <p class="text-xs text-text-secondary mt-1">Dilamar pada: {{ app.created_at }}</p>
                                
                                <div class="mt-3 flex gap-2">
                                    <a v-if="app.cv_url" :href="app.cv_url" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Lihat CV
                                    </a>
                                    <span v-if="!app.cv_url" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-600 bg-amber-50 rounded-lg">
                                        CV Tidak Tersedia
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-3">
                            <span :class="['text-sm px-3 py-1 rounded-full font-medium', statusBadge(app.status)]">
                                {{ app.status_label }}
                            </span>
                            
                            <!-- Action Buttons if Pending -->
                            <div v-if="app.status === 'pending'" class="flex gap-2">
                                <button @click="openRejectModal(app)" class="px-4 py-2 text-sm font-semibold text-danger border border-danger/20 rounded-xl hover:bg-danger/5 transition-colors">
                                    Tolak
                                </button>
                                <button @click="openAcceptModal(app)" class="px-4 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:bg-success/90 transition-colors shadow-sm">
                                    Terima
                                </button>
                            </div>
                            
                            <div v-else-if="app.keterangan" class="text-right max-w-xs">
                                <p class="text-xs font-semibold text-text-secondary mb-0.5">Catatan:</p>
                                <p class="text-xs text-text-primary">{{ app.keterangan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="text-base font-bold text-text-primary mb-1">Belum Ada Lamaran</h3>
                <p class="text-sm text-text-secondary">Tidak ada lamaran masuk dari mahasiswa.</p>
            </div>

            <!-- Pagination -->
            <div v-if="pendaftarans.links && pendaftarans.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex justify-center gap-1">
                <template v-for="link in pendaftarans.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200"
                        :class="[link.active ? 'bg-primary text-white font-semibold' : 'text-text-secondary hover:bg-gray-100']"
                        v-html="link.label"
                        preserve-scroll
                    />
                    <span v-else class="px-3.5 py-2 text-sm text-gray-300" v-html="link.label" />
                </template>
            </div>
        </CardContainer>

        <!-- Accept Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showAcceptModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-text-primary">Terima Mahasiswa</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitAccept" class="p-6">
                        <p class="text-sm text-text-secondary mb-4">Anda yakin ingin menerima <strong>{{ selectedApp?.mahasiswa.nama_lengkap }}</strong>? Lamaran ini akan lanjut ke tahap persiapan (Agreement).</p>
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-text-primary mb-2">Catatan Penerimaan (Opsional)</label>
                            <textarea v-model="acceptForm.keterangan" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-primary/20 focus:border-primary" placeholder="Beri catatan instruksi untuk mahasiswa..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="acceptForm.processing" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-success rounded-xl hover:bg-success/90 flex justify-center items-center gap-2">
                                <span v-if="acceptForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Terima Lamaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Reject Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showRejectModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModals">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-danger">Tolak Lamaran</h3>
                        <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitReject" class="p-6">
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-text-primary mb-2">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea v-model="rejectForm.keterangan" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-primary/20 focus:border-primary" :class="{'border-danger': rejectForm.errors.keterangan}" placeholder="Jelaskan alasan penolakan..."></textarea>
                            <p v-if="rejectForm.errors.keterangan" class="text-xs text-danger mt-1">{{ rejectForm.errors.keterangan }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeModals" class="flex-1 px-4 py-2 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="rejectForm.processing" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-danger rounded-xl hover:bg-danger/90 flex justify-center items-center gap-2">
                                <span v-if="rejectForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Tolak Lamaran
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
import { usePage, useForm, Link, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    pendaftarans: { type: Object, default: () => ({ data: [], links: [] }) },
});

function statusBadge(status) {
    return {
        pending: 'bg-amber-50 text-amber-700',
        diterima: 'bg-success/10 text-success',
        ditolak: 'bg-danger/10 text-danger',
    }[status] || 'bg-gray-100 text-gray-600';
}

const showAcceptModal = ref(false);
const showRejectModal = ref(false);
const selectedApp = ref(null);

const acceptForm = useForm({ keterangan: '' });
const rejectForm = useForm({ keterangan: '' });

function openAcceptModal(app) {
    selectedApp.value = app;
    acceptForm.reset();
    showAcceptModal.value = true;
}

function openRejectModal(app) {
    selectedApp.value = app;
    rejectForm.reset();
    showRejectModal.value = true;
}

function closeModals() {
    showAcceptModal.value = false;
    showRejectModal.value = false;
    selectedApp.value = null;
}

function submitAccept() {
    if(!selectedApp.value) return;
    acceptForm.post(`/industri/seleksi-cv/${selectedApp.value.id}/accept`, {
        preserveScroll: true,
        onSuccess: () => {
            closeModals();
            router.visit('/industri/agreement');
        },
    });
}

function submitReject() {
    if(!selectedApp.value) return;
    rejectForm.post(`/industri/seleksi-cv/${selectedApp.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: closeModals
    });
}
</script>
