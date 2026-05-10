<template>
    <AuthenticatedLayout>
        <Head title="Persetujuan Logbook" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaBold text-text-primary">Persetujuan Logbook</h1>
            <p class="text-sm font-jakartaSemiBold text-text-secondary mt-1">Review dan berikan komentar pada kegiatan harian mahasiswa magang.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Mahasiswa List -->
            <div class="lg:col-span-1 space-y-4">
                <CardContainer class="w-full overflow-hidden flex flex-col gap-4">
                    <div class="border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-sm text-text-primary font-jakartaBold">Pilih Mahasiswa</h2>
                    </div>
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-50">
                        <div v-if="magangs.length === 0" class="text-center">
                            <p class="text-xs text-text-secondary">Tidak ada peserta aktif.</p>
                        </div>
                        <template v-else>
                            <Link
                                v-for="m in magangs"
                                :key="m.id"
                                :href="`/industri/persetujuan-logbook?magang_id=${m.id}`"
                                preserve-scroll
                                :class="['relative block transition-colors py-2 px-4', selectedMagangId === m.id ? 'bg-primary/5 border-l-2 border-primary' : 'hover:bg-gray-50 border-l-2 border-transparent']"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">   
                                        <p class="text-sm font-jakarta text-text-primary truncate">{{ m.nama_lengkap }}</p>
                                        <p class="text-xs font-jakartaSemiBold text-text-secondary">{{ m.nim }}</p>
                                    </div>
                                    <span v-if="m.pending_count > 0" class="absolute right-0 w-4 h-4 flex items-center justify-center rounded-full bg-accent text-white text-[10px] font-bold shrink-0">
                                        {{ m.pending_count }}
                                    </span>
                                </div>
                            </Link>
                        </template>
                    </div>
                </CardContainer>
            </div>

            <!-- Main: Logbook List -->
            <div class="lg:col-span-3">
                <CardContainer v-if="selectedMagangId" class="overflow-hidden flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-jakartaBold text-text-primary">Daftar Kegiatan</h2>
                    </div>

                    <div v-if="logbooks.data && logbooks.data.length > 0" class="divide-y-10 divide-gray-50">
                        <div v-for="log in logbooks.data" :key="log.id" class=" hover:bg-gray-50/30 transition-colors">
                            <div class="flex flex-col gap-4 justify-between">
                                <div class="flex flex-col gap-4">
                                    <div class="w-full flex justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-jakartaSemiBold text-text-primary">{{ log.tanggal_waktu }}</span>
                                            <span :class="['w-fit text-xs font-jakartaSemiBold', presensiColor(log.status_presensi)]">
                                                {{ log.status_presensi_label }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span v-if="log.is_approved" class="text-xs font-jakartaSemiBold text-success">
                                                Disetujui
                                            </span>
                                            <span v-else class="text-xs font-jakartaSemiBold text-amber-600">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-jakartaSemiBold text-text-primary text-sm">Kegiatan:</span>
                                        <p class="text-sm font-jakarta text-text-primary leading-relaxed">{{ log.kegiatan }}</p>
                                    </div>
                                    
                                    <div v-if="log.komentar_industri" class="mt-3 p-3 bg-blue-50/50 rounded-lg border border-blue-100">
                                        <p class="text-xs font-jakartaSemiBold text-blue-700 mb-0.5">Komentar Anda:</p>
                                        <p class="text-xs font-jakarta text-blue-600">{{ log.komentar_industri }}</p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-start">
                                    <button v-if="!log.is_approved" @click="openApproveModal(log)" class="px-4 py-2 text-xs font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover transition-colors shadow-sm whitespace-nowrap">
                                        Setujui Logbook
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <h3 class="text-base font-bold text-text-primary mb-1">Belum Ada Kegiatan</h3>
                        <p class="text-sm text-text-secondary">Mahasiswa belum mengisi logbook.</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="logbooks.links && logbooks.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex justify-center gap-1">
                        <template v-for="link in logbooks.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200" :class="[link.active ? 'bg-primary text-white font-semibold' : 'text-text-secondary hover:bg-gray-100']" v-html="link.label" preserve-scroll />
                            <span v-else class="px-3.5 py-2 text-sm text-gray-300" v-html="link.label" />
                        </template>
                    </div>
                </CardContainer>
                
                <div v-else class="h-full flex flex-col items-center justify-center p-12 bg-gray-50 rounded-md border border-dashed border-gray-200 min-h-[300px]">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                    <p class="text-sm text-text-secondary">Pilih mahasiswa di samping untuk melihat logbook.</p>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showApproveModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-text-primary font-jakarta">Setujui Logbook</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitApprove" class="p-6">
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-text-primary mb-2">Komentar/Saran (Opsional)</label>
                            <textarea v-model="approveForm.komentar" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-md text-sm focus:ring-primary/20 focus:border-primary" placeholder="Beri masukan untuk kegiatan hari ini..."></textarea>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 text-sm font-medium border border-gray-200 rounded-md hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="approveForm.processing" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-md hover:bg-primary-hover flex justify-center items-center gap-2">
                                <span v-if="approveForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Setujui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-md shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm, Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    logbooks: { type: Object, default: () => ({ data: [], links: [] }) },
    magangs: { type: Array, default: () => [] },
    selectedMagangId: { type: Number, default: null }
});

function presensiColor(status) {
    return {
        hadir: 'text-success',
        izin: 'text-blue-600',
        sakit: 'text-amber-600',
    }[status] || 'text-gray-600';
}

const showApproveModal = ref(false);
const selectedLogbook = ref(null);
const approveForm = useForm({ komentar: '' });

function openApproveModal(logbook) {
    selectedLogbook.value = logbook;
    approveForm.reset();
    showApproveModal.value = true;
}

function closeModal() {
    showApproveModal.value = false;
    selectedLogbook.value = null;
}

function submitApprove() {
    if(!selectedLogbook.value) return;
    approveForm.post(`/industri/persetujuan-logbook/${selectedLogbook.value.id}/approve`, {
        preserveScroll: true,
        onSuccess: closeModal
    });
}
</script>
