<template>
    <AuthenticatedLayout>
        <Head title="Assign Dosen Pembimbing" />

        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Assign Dosen Pembimbing</h1>
            <p class="text-sm text-text-secondary mt-1">Tetapkan dosen pembimbing kampus untuk mahasiswa yang telah memasuki tahap persiapan magang.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <CardContainer>
                    <div class="py-1 border-b border-gray-100">
                        <h2 class="text-base font-jakartaSemiBold text-text-primary">Tugaskan Dosen Pembimbing</h2>
                    </div>
                    
                    <form @submit.prevent="submitAssign" class="p-4">
                        <div class="mb-6">
                            <label class="block text-sm font-jakartaSemiBold text-text-primary mb-2">1. Pilih Dosen Pembimbing <span class="text-danger">*</span></label>
                            <select v-model="form.dosen_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm" required>
                                <option value="" disabled>-- Pilih Dosen --</option>
                                <option v-for="dosen in dosens" :key="dosen.id" :value="dosen.id">
                                    {{ dosen.nama }} ({{ dosen.nip }})
                                </option>
                            </select>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-jakartaSemiBold text-text-primary">2. Pilih Mahasiswa Magang <span class="text-danger">*</span></label>
                                <span class="text-xs font-jakartaSemiBold text-primary bg-primary/10 px-2 py-1 rounded-md">{{ form.magang_ids.length }} Dipilih</span>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                                <div v-if="magangs.length === 0" class="p-8 text-center text-text-secondary text-sm bg-gray-50">
                                    Semua mahasiswa magang aktif telah memiliki dosen pembimbing.
                                </div>
                                <label v-for="m in magangs" :key="m.id" class="flex items-center gap-3 p-4 hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox" :value="m.id" v-model="form.magang_ids" class="rounded text-primary focus:ring-primary/20 border-gray-300 w-5 h-5" />
                                    <div>
                                        <p class="text-sm font-jakartaSemiBold text-text-primary">{{ m.mahasiswa }}</p>
                                        <p class="text-xs text-text-secondary">{{ m.nim }} · {{ m.industri }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="w-full flex justify-center">
                            <button type="submit" :disabled="form.processing || form.magang_ids.length === 0 || !form.dosen_id" class="w-full py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-xl hover:bg-primary-hover disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2 shadow-sm">
                                <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Tetapkan Pembimbing
                            </button>
                        </div>
                    </form>
                </CardContainer>
            </div>

            <div class="lg:col-span-1">
                <CardContainer class="h-full">
                    <div class="p-1 border-b border-gray-100">
                        <h2 class="text-md font-jakartaSemiBold text-text-primary">Daftar Penugasan Terbaru</h2>
                    </div>
                    <div>
                        <div v-if="assignedMagangs.length > 0" class="space-y-4">
                            <div v-for="am in assignedMagangs" :key="am.id" class="flex flex-col gap-1 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-sm font-jakartaSemiBold text-text-primary">{{ am.mahasiswa }}</p>
                                <p class="text-xs text-text-secondary flex items-center gap-1">
                                    Pembimbing: <span class="font-jakartaSemiBold text-text-primary">{{ am.dosen }}</span>
                                    <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-sm text-text-secondary">
                            Belum ada penugasan yang dilakukan.
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
import { usePage, useForm } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    dosens: Array,
    magangs: Array,
    assignedMagangs: Array,
});

const form = useForm({
    dosen_id: '',
    magang_ids: [],
});

function submitAssign() {
    form.post('/admin/assign-pembimbing', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('magang_ids');
            form.dosen_id = '';
        }
    });
}
</script>
