<template>
    <AuthenticatedLayout>
        <Head title="Input Nilai Industri" />

        <div class="mb-8">
            <h1 class="text-xl font-bold text-text-primary font-jakarta">Input Nilai Industri</h1>
            <p class="text-sm text-text-secondary mt-1">Berikan penilaian akhir untuk mahasiswa yang telah selesai melaksanakan magang.</p>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-bold text-text-primary font-jakarta">Daftar Mahasiswa Magang</h2>
            </div>

            <div v-if="magangs.length > 0" class="divide-y divide-gray-50">
                <div v-for="magang in magangs" :key="magang.id" class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-text-primary">{{ magang.mahasiswa.nama_lengkap }}</h3>
                            <p class="text-sm text-text-secondary">{{ magang.mahasiswa.nim }} · {{ magang.mahasiswa.prodi }}</p>
                            
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-primary/10 text-primary capitalize flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    Tahap: {{ magang.status_label }}
                                </span>
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-gray-100 text-text-secondary">
                                    {{ magang.approved_logbook }} / {{ magang.total_logbook }} Logbook Disetujui
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-3 shrink-0">
                            <div v-if="magang.has_graded" class="text-right">
                                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Nilai Industri</p>
                                <div class="inline-flex items-baseline gap-1">
                                    <span class="text-3xl font-bold text-success font-jakarta">{{ magang.nilai_industri }}</span>
                                    <span class="text-sm font-medium text-success">/ 100</span>
                                </div>
                            </div>
                            <div v-else class="text-right">
                                <p class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1">Nilai Industri</p>
                                <span class="text-sm font-semibold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg">Belum Dinilai</span>
                            </div>

                            <button @click="openGradeModal(magang)" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors duration-200" :class="magang.has_graded ? 'bg-gray-100 text-text-primary hover:bg-gray-200' : 'bg-primary text-white hover:bg-primary-hover shadow-sm'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ magang.has_graded ? 'Ubah Nilai' : 'Input Nilai' }}
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
                <h3 class="text-base font-bold text-text-primary mb-1">Tidak Ada Peserta Aktif</h3>
                <p class="text-sm text-text-secondary">Anda belum memiliki mahasiswa magang yang aktif.</p>
            </div>
        </CardContainer>

        <!-- Input Nilai Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showGradeModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-sm">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-text-primary font-jakarta">Input Nilai Industri</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitGrade" class="p-6">
                        <div class="text-center mb-6">
                            <p class="text-sm font-semibold text-text-primary">{{ selectedMagang?.mahasiswa.nama_lengkap }}</p>
                            <p class="text-xs text-text-secondary mt-0.5">{{ selectedMagang?.mahasiswa.nim }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-text-primary mb-2 text-center">Nilai Angka (0-100) <span class="text-danger">*</span></label>
                            <input 
                                v-model="gradeForm.nilai" 
                                type="number" 
                                min="0" 
                                max="100" 
                                step="0.01"
                                class="w-full text-center text-3xl font-bold font-jakarta px-4 py-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                :class="{'border-danger': gradeForm.errors.nilai}"
                                placeholder="0"
                            />
                            <p v-if="gradeForm.errors.nilai" class="text-xs text-danger mt-2 text-center">{{ gradeForm.errors.nilai }}</p>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-3 text-sm font-medium border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="gradeForm.processing" class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover flex justify-center items-center gap-2">
                                <span v-if="gradeForm.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Simpan Nilai
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
    magangs: { type: Array, default: () => [] }
});

const showGradeModal = ref(false);
const selectedMagang = ref(null);

const gradeForm = useForm({ nilai: '' });

function openGradeModal(magang) {
    selectedMagang.value = magang;
    gradeForm.nilai = magang.nilai_industri !== null ? magang.nilai_industri : '';
    gradeForm.clearErrors();
    showGradeModal.value = true;
}

function closeModal() {
    showGradeModal.value = false;
    selectedMagang.value = null;
}

function submitGrade() {
    if(!selectedMagang.value) return;
    gradeForm.post(`/industri/input-nilai/${selectedMagang.value.id}`, {
        preserveScroll: true,
        onSuccess: closeModal
    });
}
</script>
