<template>
    <AuthenticatedLayout>
        <Head title="Internship Completion Letter" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Internship Completion Letter</h1>
            <p class="text-sm text-text-secondary mt-1">Isi data surat keterangan selesai magang untuk mahasiswa.</p>
        </div>

        <!-- Student List -->
        <CardContainer class="overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-text-primary font-jakarta">Daftar Mahasiswa Magang</h2>
            </div>

            <div v-if="magangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in magangs" :key="magang.id" class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <!-- Student Info -->
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm text-text-secondary">{{ magang.mahasiswa.nim }} · {{ magang.mahasiswa.prodi }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-primary/10 text-primary capitalize flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Tahap: {{ magang.status_label }}
                                </span>
                                <span v-if="magang.tanggal_mulai" class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-text-secondary">
                                    {{ magang.tanggal_mulai }} — {{ magang.tanggal_selesai ?? 'Sekarang' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status & Action -->
                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <div v-if="magang.sertifikat?.has_completion_letter" class="text-right">
                                <span class="text-xs font-semibold text-success bg-success/10 px-3 py-1.5 rounded-lg inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Sudah Diisi
                                </span>
                            </div>
                            <div v-else class="text-right">
                                <span class="text-sm font-semibold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Belum Diisi</span>
                            </div>

                            <button
                                @click="openModal(magang)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors duration-200"
                                :class="magang.sertifikat?.has_completion_letter ? 'bg-gray-100 text-text-primary hover:bg-gray-200' : 'bg-primary text-white hover:bg-primary-hover shadow-sm'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ magang.sertifikat?.has_completion_letter ? 'Edit Data' : 'Isi Data' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-base font-bold text-text-primary mb-1">Tidak Ada Peserta Magang</h3>
                <p class="text-sm text-text-secondary">Anda belum memiliki mahasiswa magang yang aktif.</p>
            </div>
        </CardContainer>

        <!-- Input Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-card rounded-t-2xl z-10">
                        <div>
                            <h3 class="text-lg font-bold text-text-primary font-jakarta">Internship Completion Letter</h3>
                            <p class="text-xs text-text-secondary mt-0.5">{{ selectedMagang?.mahasiswa.nama_lengkap }} — {{ selectedMagang?.mahasiswa.nim }}</p>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="submitForm" class="p-6 space-y-5">
                        <!-- Posisi Magang -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-1.5">
                                Posisi Magang <span class="text-danger">*</span>
                            </label>
                            <input
                                v-model="form.posisi_magang"
                                type="text"
                                placeholder="Contoh: UI/UX Designer"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                :class="{'border-danger': form.errors.posisi_magang}"
                            />
                            <p v-if="form.errors.posisi_magang" class="text-xs text-danger mt-1">{{ form.errors.posisi_magang }}</p>
                        </div>

                        <!-- Departemen -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-1.5">
                                Departemen <span class="text-danger">*</span>
                            </label>
                            <input
                                v-model="form.departemen"
                                type="text"
                                placeholder="Contoh: Marketing and Promotion Department"
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                :class="{'border-danger': form.errors.departemen}"
                            />
                            <p v-if="form.errors.departemen" class="text-xs text-danger mt-1">{{ form.errors.departemen }}</p>
                        </div>

                        <!-- Deskripsi Tugas -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-1.5">
                                Deskripsi Tugas & Tanggung Jawab <span class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="form.deskripsi_tugas"
                                rows="5"
                                placeholder="Jelaskan tugas dan tanggung jawab mahasiswa selama magang..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                :class="{'border-danger': form.errors.deskripsi_tugas}"
                            ></textarea>
                            <p v-if="form.errors.deskripsi_tugas" class="text-xs text-danger mt-1">{{ form.errors.deskripsi_tugas }}</p>
                            <p class="text-xs text-text-secondary mt-1">Tuliskan dalam bentuk paragraf atau poin-poin.</p>
                        </div>

                        <!-- Komentar Penutup -->
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-1.5">
                                Komentar Penutup <span class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="form.komentar_penutup"
                                rows="4"
                                placeholder="Berikan komentar penutup/apresiasi untuk mahasiswa..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                :class="{'border-danger': form.errors.komentar_penutup}"
                            ></textarea>
                            <p v-if="form.errors.komentar_penutup" class="text-xs text-danger mt-1">{{ form.errors.komentar_penutup }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-3 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover flex justify-center items-center gap-2 transition-colors"
                            >
                                <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash Message -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
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
    magangs: { type: Array, default: () => [] }
});

const showModal = ref(false);
const selectedMagang = ref(null);

const form = useForm({
    posisi_magang: '',
    departemen: '',
    deskripsi_tugas: '',
    komentar_penutup: '',
});

function openModal(magang) {
    selectedMagang.value = magang;
    // Pre-fill if data already exists
    if (magang.sertifikat) {
        form.posisi_magang = magang.sertifikat.posisi_magang || '';
        form.departemen = magang.sertifikat.departemen || '';
        form.deskripsi_tugas = magang.sertifikat.deskripsi_tugas || '';
        form.komentar_penutup = magang.sertifikat.komentar_penutup || '';
    } else {
        form.reset();
    }
    form.clearErrors();
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    selectedMagang.value = null;
}

function submitForm() {
    if (!selectedMagang.value) return;
    form.post(`/industri/completion-letter/${selectedMagang.value.id}`, {
        preserveScroll: true,
        onSuccess: closeModal,
    });
}
</script>
