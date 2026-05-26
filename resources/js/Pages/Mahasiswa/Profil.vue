<template>
    <AuthenticatedLayout>
        <Head title="Profil Saya" />

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-xl font-jakartaSemiBold text-text-primary">Profil Saya</h1>
            <p class="text-sm font-jakarta text-text-secondary mt-1">Kelola informasi profil profesional Anda untuk proses magang.</p>
        </div>

        <!-- Profile Completeness -->
        <div class="mb-6 p-5 bg-card rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-jakartaSemiBold text-text-primary">Kelengkapan Profil</h3>
                <span class="text-sm font-jakartaSemiBold" :class="completeness.percentage === 100 ? 'text-success' : 'text-primary'">
                    {{ completeness.percentage }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                <div
                    class="h-2.5 rounded-full transition-all duration-700 ease-out"
                    :class="completeness.percentage === 100 ? 'bg-success' : 'bg-primary'"
                    :style="{ width: completeness.percentage + '%' }"
                ></div>
            </div>
            <div v-if="!completeness.is_ready_to_apply" class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-xs font-jakarta text-amber-700">
                    Lengkapi <strong>LinkedIn</strong> dan <strong>CV</strong> sebelum Anda dapat melamar magang.
                </p>
            </div>
            <div v-else class="mt-3 p-3 bg-success/5 border border-success/20 rounded-xl flex items-start gap-2.5">
                <svg class="w-4 h-4 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs font-jakarta text-success">Profil Anda sudah lengkap dan siap untuk melamar magang.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Profile Card -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Photo Card -->
                <CardContainer>
                    <div class="flex flex-col items-center py-4">
                        <!-- Profile Photo -->
                        <div class="relative group mb-4">
                            <div v-if="profile.profile_photo_url" class="w-28 h-28 rounded-full overflow-hidden border-4 border-primary/10 shadow-md">
                                <img :src="profile.profile_photo_url" alt="Foto Profil" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-28 h-28 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 border-4 border-primary/10 flex items-center justify-center shadow-md">
                                <span class="text-3xl font-jakartaSemiBold text-primary">{{ profile.nama_lengkap?.charAt(0) }}</span>
                            </div>

                            <!-- Upload overlay -->
                            <label class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer flex items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <input type="file" accept="image/jpeg,image/jpg,image/png" class="hidden" @change="handlePhotoUpload" />
                            </label>
                        </div>

                        <!-- Delete photo -->
                        <button
                            v-if="profile.profile_photo_url"
                            @click="deletePhoto"
                            class="text-xs font-jakarta text-danger hover:underline mb-3"
                        >
                            Hapus Foto
                        </button>

                        <h2 class="text-lg font-jakartaSemiBold text-text-primary text-center">{{ profile.nama_lengkap }}</h2>
                        <p class="text-sm font-jakarta text-text-secondary">{{ profile.nim }}</p>
                        <span class="mt-1 inline-block px-3 py-1 text-xs font-jakartaSemiBold text-primary bg-primary/10 rounded-full">
                            {{ profile.prodi || 'Prodi belum diisi' }}
                        </span>
                    </div>

                    <!-- Contact Info -->
                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-jakarta text-text-primary truncate">{{ profile.email }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm font-jakarta text-text-primary">{{ profile.nomor_hp || '—' }}</span>
                        </div>
                    </div>

                    <!-- Documents Status -->
                    <div class="border-t border-gray-100 pt-4 mt-4 space-y-2.5">
                        <h4 class="text-xs font-jakartaSemiBold text-text-secondary uppercase tracking-wider">Dokumen</h4>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-jakarta text-text-primary">CV</span>
                            <div class="flex items-center gap-2">
                                <span v-if="profile.has_cv" class="text-xs font-jakartaSemiBold text-success flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Uploaded
                                </span>
                                <span v-else class="text-xs font-jakartaSemiBold text-danger">Belum</span>
                                <a v-if="profile.cv_preview_url" :href="profile.cv_preview_url" target="_blank" class="text-xs text-primary hover:underline">Lihat</a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-jakarta text-text-primary">LinkedIn</span>
                            <span v-if="profile.linkedin_url" class="text-xs font-jakartaSemiBold text-success flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Terisi
                            </span>
                            <span v-else class="text-xs font-jakartaSemiBold text-danger">Belum diisi</span>
                        </div>
                    </div>
                </CardContainer>
            </div>

            <!-- Right: Edit Form -->
            <CardContainer class="lg:col-span-2 h-fit">
                <div class="border-b border-gray-100 pb-4 mb-6">
                    <h2 class="text-base font-jakartaSemiBold text-text-primary">Edit Profil</h2>
                    <p class="text-xs font-jakarta text-text-secondary mt-1">Perbarui informasi profil profesional Anda.</p>
                </div>

                <form @submit.prevent="submitProfile" class="space-y-5">
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1.5">
                            Nomor HP
                        </label>
                        <input
                            v-model="form.nomor_hp"
                            type="tel"
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-jakarta text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                        <p v-if="form.errors.nomor_hp" class="text-xs text-danger mt-1">{{ form.errors.nomor_hp }}</p>
                    </div>

                    <!-- LinkedIn -->
                    <div>
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1.5">
                            LinkedIn URL <span class="text-danger">*</span>
                            <span class="text-xs font-jakarta text-text-secondary ml-1">(Wajib untuk apply magang)</span>
                        </label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            <input
                                v-model="form.linkedin_url"
                                type="url"
                                placeholder="https://www.linkedin.com/in/username"
                                class="w-full pl-11 pr-4 py-2.5 border rounded-xl text-sm font-jakarta text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                :class="linkedinValidation.class"
                            />
                        </div>
                        <p v-if="form.errors.linkedin_url" class="text-xs text-danger mt-1">{{ form.errors.linkedin_url }}</p>
                        <p v-else-if="form.linkedin_url && !linkedinValidation.valid" class="text-xs text-danger mt-1">
                            Format tidak valid. Contoh: https://www.linkedin.com/in/username
                        </p>
                        <p v-else-if="form.linkedin_url && linkedinValidation.valid" class="text-xs text-success mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            URL LinkedIn valid
                        </p>
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1.5">
                            Bio Singkat
                            <span class="text-xs font-jakarta text-text-secondary ml-1">(Maks. 500 karakter)</span>
                        </label>
                        <textarea
                            v-model="form.bio"
                            rows="3"
                            placeholder="Ceritakan tentang diri Anda secara singkat..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-jakarta text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <p v-if="form.errors.bio" class="text-xs text-danger">{{ form.errors.bio }}</p>
                            <span class="text-xs text-text-secondary ml-auto">{{ (form.bio || '').length }}/500</span>
                        </div>
                    </div>

                    <!-- Skills -->
                    <div>
                        <label class="block text-sm font-jakartaSemiBold text-text-primary mb-1.5">
                            Skill & Keahlian
                            <span class="text-xs font-jakarta text-text-secondary ml-1">(Maks. 1000 karakter)</span>
                        </label>
                        <textarea
                            v-model="form.skills"
                            rows="4"
                            placeholder="Jelaskan skill dan keahlian utama Anda. Contoh: Menguasai framework Laravel dan Vue.js untuk pengembangan web full-stack. Berpengalaman dalam database design, REST API, dan version control dengan Git..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-jakarta text-text-primary placeholder-gray-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <p v-if="form.errors.skills" class="text-xs text-danger">{{ form.errors.skills }}</p>
                            <span class="text-xs text-text-secondary ml-auto">{{ (form.skills || '').length }}/1000</span>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-auto px-8 py-3 text-sm font-jakartaSemiBold text-white bg-primary rounded-xl hover:bg-primary-hover transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <template v-if="form.processing">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Menyimpan...
                            </template>
                            <template v-else>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </template>
                        </button>
                    </div>
                </form>
            </CardContainer>
        </div>

        <!-- Flash Messages -->
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
import { ref, computed, watch } from "vue";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const flashMsg = ref(null);
const flashType = ref('success');

watch(flash, (val) => {
    if (val.success || val.error) {
        flashMsg.value = val.success || val.error;
        flashType.value = val.success ? 'success' : 'error';
        setTimeout(() => { flashMsg.value = null; }, 4000);
    }
}, { immediate: true });

const props = defineProps({
    profile: { type: Object, required: true },
    completeness: { type: Object, required: true },
});

// Profile edit form
const form = useForm({
    nomor_hp: props.profile.nomor_hp || '',
    bio: props.profile.bio || '',
    skills: props.profile.skills || '',
    linkedin_url: props.profile.linkedin_url || '',
});

// Real-time LinkedIn validation
const linkedinRegex = /^https?:\/\/(www\.)?linkedin\.com\/in\/.+$/i;

const linkedinValidation = computed(() => {
    if (!form.linkedin_url) {
        return { valid: false, class: 'border-gray-200' };
    }
    const valid = linkedinRegex.test(form.linkedin_url);
    return {
        valid,
        class: valid ? 'border-success' : 'border-danger',
    };
});

function submitProfile() {
    form.put('/mahasiswa/profil', {
        preserveScroll: true,
    });
}

// Photo upload
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const photoForm = useForm({ photo: file });
    photoForm.post('/mahasiswa/profil/photo', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            event.target.value = '';
        },
    });
}

function deletePhoto() {
    if (!confirm('Yakin ingin menghapus foto profil?')) return;
    router.delete('/mahasiswa/profil/photo', {
        preserveScroll: true,
    });
}
</script>
