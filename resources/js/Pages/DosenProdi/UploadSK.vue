<template>
    <AuthenticatedLayout>
        <Head title="Distribusi SK Pembimbing" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Distribusi SK Pembimbing</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Unggah dan distribusikan Surat Keputusan (SK) kepada dosen pembimbing magang.</p>
        </div>

        <CardContainer class="mb-6">
            <div class="pb-2 border-b border-gray-100 flex justify-between items-center mb-4">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Daftar Penugasan Pembimbing</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-text-secondary uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 font-jakartaSemiBold">No</th>
                            <th class="px-4 py-3 font-jakartaSemiBold">Mahasiswa</th>
                            <th class="px-4 py-3 font-jakartaSemiBold">Dosen Pembimbing</th>
                            <th class="px-4 py-3 font-jakartaSemiBold">Status SK</th>
                            <th class="px-4 py-3 font-jakartaSemiBold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="(assignment, index) in assignments" :key="assignment.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <p class="font-jakartaSemiBold text-text-primary">{{ index + 1 }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-jakartaSemiBold text-text-primary">{{ assignment.magang_aktif.pendaftaran.mahasiswa.nama_lengkap }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-jakartaSemiBold text-text-primary">{{ assignment.dosen.nama_dosen }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span v-if="assignment.surat_keputusan" class="inline-flex items-center text-xs font-jakartaBold text-success">
                                    Sudah Diunggah
                                </span>
                                <span v-else class="inline-flex items-center text-xs font-jakartaBold text-warning">
                                    Belum Ada SK
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button 
                                    v-if="!assignment.surat_keputusan" 
                                    @click="openUploadModal(assignment)"
                                    class="text-xs font-jakartaSemiBold text-white bg-primary hover:bg-primary-hover px-6 py-2 rounded-sm transition-colors">
                                    Upload SK
                                </button>
                                <a 
                                    v-else
                                    :href="`/dosen-prodi/surat-keputusan/${assignment.surat_keputusan.id}/download`" 
                                    target="_blank"
                                    class="text-xs font-jakartaSemiBold text-primary bg-primary/10 hover:bg-primary/20 px-4 py-2 rounded-sm transition-colors">
                                    Lihat SK
                                </a>
                            </td>
                        </tr>
                        <tr v-if="assignments.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-text-secondary">
                                Belum ada penugasan dosen pembimbing.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContainer>

        <!-- Modal Upload SK -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-jakartaSemiBold text-text-primary">Upload SK Pembimbing</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitUpload" class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Nomor SK <span class="text-danger">*</span></label>
                        <input type="text" v-model="form.nomor_sk" class="w-full px-4 py-2 font-jakarta border-b border-gray-300 focus:border-primary text-sm" required placeholder="Contoh: 123-SK-2026" />
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Tanggal SK <span class="text-danger">*</span></label>
                        <input type="date" v-model="form.tanggal_sk" class="w-full px-4 py-2 font-jakarta border-b border-gray-300 focus:border-primary text-sm" required />
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">File SK (PDF, Maks 10MB) <span class="text-danger">*</span></label>
                        <input type="file" @change="e => form.file_sk = e.target.files[0]" accept="application/pdf" class="w-full text-xs font-jakarta text-text-secondary file:mr-4 file:py-2 file:px-6 file:rounded-sm file:border-0 file:text-xs file:font-jakartaBold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-colors" required />
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-jakartaSemiBold text-text-secondary hover:text-text-primary transition-colors">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-jakartaSemiBold text-white bg-primary rounded-sm hover:bg-primary-hover disabled:opacity-50 flex items-center gap-2">
                            <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                            Upload SK
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const props = defineProps({
    assignments: Array,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

const isModalOpen = ref(false);
const selectedAssignment = ref(null);

const form = useForm({
    assignment_id: '',
    nomor_sk: '',
    tanggal_sk: '',
    file_sk: null,
});

function openUploadModal(assignment) {
    selectedAssignment.value = assignment;
    form.assignment_id = assignment.id;
    isModalOpen.value = true;
}

function closeModal() {
    isModalOpen.value = false;
    selectedAssignment.value = null;
    form.reset();
}

function submitUpload() {
    form.post('/dosen-prodi/surat-keputusan', {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        }
    });
}
</script>
