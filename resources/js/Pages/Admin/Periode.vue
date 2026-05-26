<template>
    <AuthenticatedLayout>
        <Head title="Kelola Periode Magang" />

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-lg font-jakartaSemiBold text-text-primary">Kelola Periode Magang</h1>
                <p class="text-sm font-jakarta text-text-secondary mt-1">Atur jadwal buka dan tutup pendaftaran magang untuk mahasiswa.</p>
            </div>
            <button @click="showAddModal = true" class="px-3 py-1.5 md:py-2.5 text-xs font-jakartaSemiBold text-white bg-primary rounded-md hover:bg-primary-hover shadow-sm flex items-center gap-2">
                Tambah Periode
            </button>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="pb-2 border-b border-gray-100">
                <h2 class="text-base font-jakartaSemiBold text-text-primary">Daftar Periode</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/50 text-text-secondary font-jakartaSemiBold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3">Tahun Akademik</th>
                            <th class="px-6 py-3">Semester</th>
                            <th class="px-6 py-3">Tanggal Buka</th>
                            <th class="px-6 py-3">Tanggal Tutup</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" v-if="periodes.length > 0">
                        <tr v-for="periode in periodes" :key="periode.id" class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-4 font-jakartaSemiBold text-text-primary">{{ periode.tahun_akademik }}</td>
                            <td class="px-6 py-4 font-jakartaSemiBold text-text-secondary capitalize">{{ periode.semester }}</td>
                            <td class="px-6 py-4 font-jakartaSemiBold text-text-secondary">{{ formatDate(periode.tanggal_buka) }}</td>
                            <td class="px-6 py-4 font-jakartaSemiBold text-text-secondary">{{ formatDate(periode.tanggal_tutup) }}</td>
                            <td class="px-6 py-4">
                                <span v-if="periode.is_active" class="px-2.5 py-1 text-xs font-jakartaSemiBold rounded-full bg-success/10 text-success">
                                    Aktif
                                </span>
                                <span v-else class="px-2.5 py-1 text-xs font-jakartaSemiBold rounded-full bg-gray-100 text-gray-600">
                                    Tidak Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button v-if="!periode.is_active" @click="activatePeriode(periode.id)" class="text-sm font-jakartaSemiBold text-primary hover:text-primary-hover">
                                    Aktifkan
                                </button>
                                <span v-else class="text-xs text-text-secondary italic">Sedang Aktif</span>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Belum ada periode yang didaftarkan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContainer>

        <!-- Add Modal -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showAddModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="showAddModal = false">
                <div class="bg-card rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-jakartaSemiBold text-text-primary">Tambah Periode Baru</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Tahun Akademik</label>
                            <input v-model="form.tahun_akademik" type="text" placeholder="Contoh: 2026/2027" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm" required />
                        </div>
                        <div>
                            <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Semester</label>
                            <select v-model="form.semester" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm" required>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                                <option value="pendek">Pendek</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Tanggal Buka</label>
                                <input v-model="form.tanggal_buka" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm" required />
                            </div>
                            <div>
                                <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1">Tanggal Tutup</label>
                                <input v-model="form.tanggal_tutup" type="date" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm" required />
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-2">
                            <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded text-primary focus:ring-primary/20 border-gray-300" />
                            <label for="is_active" class="text-sm text-text-secondary">Langsung Aktifkan Periode Ini</label>
                        </div>
                        
                        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                            <button type="button" @click="showAddModal = false" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold border border-gray-200 rounded-xl hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-2.5 text-sm font-jakartaSemiBold text-white bg-primary rounded-xl hover:bg-primary-hover flex justify-center items-center gap-2">
                                <span v-if="form.processing" class="animate-spin w-4 h-4 border-2 border-white/20 border-t-white rounded-full"></span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Flash -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="flashMsg" :class="['fixed bottom-6 right-6 px-5 py-3 rounded-xl shadow-lg text-sm font-jakartaSemiBold z-50', flashType === 'success' ? 'bg-success text-white' : 'bg-danger text-white']">
                {{ flashMsg }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';
import { url } from '@/utils/prefix';

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = computed(() => flash.value.success || flash.value.error || null);
const flashType = computed(() => flash.value.success ? 'success' : 'error');

defineProps({
    periodes: Array,
});

const showAddModal = ref(false);
const form = useForm({
    tahun_akademik: '',
    semester: 'ganjil',
    tanggal_buka: '',
    tanggal_tutup: '',
    is_active: false,
});

function submitForm() {
    form.post(url('/admin/periode'), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
        }
    });
}

function activatePeriode(id) {
    if(confirm('Yakin ingin mengaktifkan periode ini? Periode lain akan dinonaktifkan.')){
        router.patch(url(`/admin/periode/${id}`), { is_active: true });
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>
