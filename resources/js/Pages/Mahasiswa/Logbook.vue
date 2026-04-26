<template>
    <AuthenticatedLayout>
        <Head title="Logbook Kegiatan" />

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-xl font-bold text-text-primary font-jakarta">Logbook Kegiatan</h1>
                <p class="text-sm text-text-secondary mt-1">Catat aktivitas harian Anda selama magang.</p>
            </div>
            <button
                v-if="canSubmit"
                @click="showForm = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-hover transition-colors duration-200 shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Logbook
            </button>
        </div>

        <!-- Gate Warning -->
        <div v-if="!canSubmit && statusTahapan" class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Logbook tidak dapat diisi</p>
                <p class="text-xs text-amber-600 mt-1">
                    Logbook hanya bisa diisi saat status magang dalam tahap <strong>Pelaksanaan</strong>.
                    Status saat ini: <strong class="capitalize">{{ statusTahapan }}</strong>.
                </p>
            </div>
        </div>

        <div v-if="!statusTahapan" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-text-primary">Belum memiliki magang aktif</p>
                <p class="text-xs text-text-secondary mt-1">Silakan ajukan lamaran magang terlebih dahulu melalui halaman Kirim CV.</p>
            </div>
        </div>

        <!-- Logbook Form Modal -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showForm" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4" @click.self="showForm = false">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div v-if="showForm" class="bg-card rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-text-primary font-jakarta">Tambah Logbook</h3>
                            <button @click="showForm = false" class="p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <form @submit.prevent="submitLogbook" class="p-6 space-y-5">
                            <!-- Kegiatan -->
                            <div>
                                <label class="block text-sm font-semibold text-text-primary mb-2">Kegiatan Hari Ini <span class="text-danger">*</span></label>
                                <textarea
                                    v-model="form.kegiatan"
                                    rows="4"
                                    placeholder="Jelaskan aktivitas yang Anda kerjakan hari ini..."
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                    :class="{ 'border-danger': form.errors.kegiatan }"
                                ></textarea>
                                <p v-if="form.errors.kegiatan" class="text-xs text-danger mt-1">{{ form.errors.kegiatan }}</p>
                            </div>

                            <!-- Status Presensi -->
                            <div>
                                <label class="block text-sm font-semibold text-text-primary mb-2">Status Presensi</label>
                                <div class="flex gap-3">
                                    <label
                                        v-for="status in presensiOptions"
                                        :key="status.value"
                                        :class="[
                                            'flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-medium cursor-pointer transition-all duration-200',
                                            form.status_presensi === status.value
                                                ? 'border-primary bg-primary/5 text-primary'
                                                : 'border-gray-200 text-text-secondary hover:border-gray-300'
                                        ]"
                                    >
                                        <input type="radio" :value="status.value" v-model="form.status_presensi" class="sr-only" />
                                        <span :class="status.dot"></span>
                                        {{ status.label }}
                                    </label>
                                </div>
                            </div>

                            <!-- Location Toggle -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-semibold text-text-primary">Sertakan Lokasi</label>
                                    <button
                                        type="button"
                                        @click="toggleLocation"
                                        :class="[
                                            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                                            useLocation ? 'bg-primary' : 'bg-gray-200'
                                        ]"
                                    >
                                        <span
                                            :class="[
                                                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 shadow-sm',
                                                useLocation ? 'translate-x-6' : 'translate-x-1'
                                            ]"
                                        />
                                    </button>
                                </div>
                                <p v-if="useLocation && form.latitude" class="text-xs text-success mt-2 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Lokasi terdeteksi ({{ form.latitude?.toFixed(5) }}, {{ form.longitude?.toFixed(5) }})
                                </p>
                                <p v-if="useLocation && !form.latitude && !locationError" class="text-xs text-text-secondary mt-2">
                                    Mendeteksi lokasi...
                                </p>
                                <p v-if="locationError" class="text-xs text-danger mt-2">{{ locationError }}</p>
                            </div>

                            <!-- Submit -->
                            <div class="flex gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="showForm = false"
                                    class="flex-1 px-4 py-2.5 text-sm font-medium text-text-secondary border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        Menyimpan...
                                    </span>
                                    <span v-else>Simpan Logbook</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Logbook List -->
        <div v-if="logbooks.data && logbooks.data.length > 0" class="space-y-4">
            <div
                v-for="log in logbooks.data"
                :key="log.id"
                class="bg-card rounded-xl border border-gray-100 p-5 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <!-- Date & Status Row -->
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="text-sm font-bold text-text-primary font-jakarta">{{ log.tanggal_display }}</span>
                            <span
                                :class="[
                                    'text-xs px-2.5 py-0.5 rounded-full font-medium',
                                    presensiColor(log.status_presensi)
                                ]"
                            >
                                {{ log.status_presensi_label }}
                            </span>
                        </div>

                        <!-- Kegiatan -->
                        <p class="text-sm text-text-primary leading-relaxed">{{ log.kegiatan }}</p>

                        <!-- Komentar Industri -->
                        <div v-if="log.komentar_industri" class="mt-3 p-3 bg-blue-50/50 rounded-lg border border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 mb-1">Komentar Supervisor:</p>
                            <p class="text-xs text-blue-600">{{ log.komentar_industri }}</p>
                        </div>
                    </div>

                    <!-- Approval Badges -->
                    <div class="flex flex-col gap-2 shrink-0">
                        <span
                            :class="[
                                'inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-full font-medium whitespace-nowrap',
                                log.is_approved
                                    ? 'bg-success/10 text-success'
                                    : 'bg-amber-50 text-amber-600'
                            ]"
                        >
                            <span :class="[
                                'w-1.5 h-1.5 rounded-full',
                                log.is_approved ? 'bg-success' : 'bg-amber-500'
                            ]"></span>
                            {{ log.is_approved ? 'Disetujui' : 'Pending' }}
                        </span>
                        <span
                            v-if="log.is_checked_kampus"
                            class="inline-flex items-center gap-1.5 text-xs px-3 py-1 rounded-full font-medium bg-primary/10 text-primary whitespace-nowrap"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Dicek Kampus
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="logbooks.links && logbooks.links.length > 3" class="flex justify-center gap-1 pt-4">
                <template v-for="link in logbooks.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200"
                        :class="[
                            link.active
                                ? 'bg-primary text-white font-semibold'
                                : 'text-text-secondary hover:bg-gray-100'
                        ]"
                        v-html="link.label"
                        preserve-scroll
                    />
                    <span
                        v-else
                        class="px-3.5 py-2 text-sm text-gray-300"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

        <!-- Empty State -->
        <CardContainer v-else-if="statusTahapan" padding="p-6">
            <div class="flex flex-col items-center justify-center py-12 px-4 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="text-sm font-bold text-text-primary mb-1">Belum Ada Logbook</h3>
                <p class="text-xs text-text-secondary">
                    {{ canSubmit ? 'Mulai catat aktivitas magang Anda dengan menekan tombol "Tambah Logbook".' : 'Logbook akan tersedia saat magang memasuki tahap Pelaksanaan.' }}
                </p>
            </div>
        </CardContainer>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.success" class="fixed bottom-6 right-6 bg-success text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.success }}
            </div>
        </Transition>
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-4 opacity-0"
        >
            <div v-if="flash.error" class="fixed bottom-6 right-6 bg-danger text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium z-50">
                {{ flash.error }}
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, useForm, Link } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});

const props = defineProps({
    logbooks: { type: Object, default: () => ({ data: [], links: [] }) },
    canSubmit: { type: Boolean, default: false },
    magangId: { type: Number, default: null },
    statusTahapan: { type: String, default: null },
});

const showForm = ref(false);
const useLocation = ref(false);
const locationError = ref(null);

const presensiOptions = [
    { value: 'hadir', label: 'Hadir', dot: 'w-2 h-2 rounded-full bg-success' },
    { value: 'izin', label: 'Izin', dot: 'w-2 h-2 rounded-full bg-blue-500' },
    { value: 'sakit', label: 'Sakit', dot: 'w-2 h-2 rounded-full bg-amber-500' },
];

const form = useForm({
    kegiatan: '',
    status_presensi: 'hadir',
    latitude: null,
    longitude: null,
});

function presensiColor(status) {
    const map = {
        hadir: 'bg-success/10 text-success',
        izin: 'bg-blue-50 text-blue-600',
        sakit: 'bg-amber-50 text-amber-600',
    };
    return map[status] || 'bg-gray-100 text-gray-600';
}

function toggleLocation() {
    useLocation.value = !useLocation.value;
    locationError.value = null;

    if (useLocation.value) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    form.latitude = pos.coords.latitude;
                    form.longitude = pos.coords.longitude;
                },
                (err) => {
                    locationError.value = 'Tidak dapat mengambil lokasi: ' + err.message;
                    useLocation.value = false;
                }
            );
        } else {
            locationError.value = 'Browser tidak mendukung geolokasi.';
            useLocation.value = false;
        }
    } else {
        form.latitude = null;
        form.longitude = null;
    }
}

function submitLogbook() {
    form.post('/mahasiswa/logbook', {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.reset();
            useLocation.value = false;
        },
    });
}
</script>
