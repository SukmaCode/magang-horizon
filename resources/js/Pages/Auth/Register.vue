<template>
  <Head title="Daftar" />

  <div class="min-h-screen bg-bg flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <!-- Decorative blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-lg">
      <!-- Logo / Brand -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-primary rounded-2xl shadow-lg shadow-primary/25 mb-4">
          <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-text-primary">Buat Akun Baru</h1>
        <p class="mt-1.5 text-sm text-text-secondary">Daftar untuk memulai program magang</p>
      </div>

      <CardContainer padding="p-0">
        <!-- Step Indicator -->
        <div class="px-8 pt-8 pb-4">
          <div class="flex items-center justify-center gap-3">
            <template v-for="(step, idx) in steps" :key="idx">
              <!-- Step circle -->
              <button
                @click="goToStep(idx)"
                :disabled="!canGoToStep(idx)"
                :class="[
                  'relative flex items-center justify-center w-9 h-9 rounded-full text-xs font-bold transition-all duration-300',
                  idx === currentStep
                    ? 'bg-primary text-white shadow-lg shadow-primary/30 scale-110'
                    : idx < currentStep
                      ? 'bg-success text-white'
                      : 'bg-gray-100 text-text-secondary',
                  canGoToStep(idx) ? 'cursor-pointer' : 'cursor-default',
                ]"
              >
                <svg v-if="idx < currentStep" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                <span v-else>{{ idx + 1 }}</span>
              </button>
              <!-- Connector line -->
              <div
                v-if="idx < steps.length - 1"
                :class="[
                  'h-0.5 w-12 rounded-full transition-all duration-500',
                  idx < currentStep ? 'bg-success' : 'bg-gray-200',
                ]"
              ></div>
            </template>
          </div>
          <div class="mt-3 text-center">
            <p class="text-xs font-medium text-text-secondary uppercase tracking-wider">
              {{ steps[currentStep] }}
            </p>
          </div>
        </div>

        <!-- Flash Error -->
        <div v-if="Object.keys(form.errors).length > 0" class="mx-8 mb-4">
          <div class="flex items-start gap-3 p-4 bg-danger/5 border border-danger/20 rounded-xl">
            <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm text-danger">
              <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
            </div>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="px-8 pb-8">
          <!-- ============================== -->
          <!-- STEP 1: Choose Role            -->
          <!-- ============================== -->
          <Transition name="fade-slide" mode="out-in">
            <div v-if="currentStep === 0" key="step0">
              <FormSection title="Pilih Peran Anda" description="Setiap peran memiliki akses dan fitur berbeda">
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="role in roleOptions"
                    :key="role.value"
                    type="button"
                    @click="selectRole(role.value)"
                    :class="[
                      'relative flex flex-col items-center gap-2.5 p-5 rounded-2xl border-2 text-center transition-all duration-200',
                      form.role === role.value
                        ? 'border-primary bg-primary/5 shadow-sm'
                        : 'border-gray-100 bg-white hover:border-gray-200 hover:shadow-sm',
                    ]"
                  >
                    <!-- Selection dot -->
                    <div
                      :class="[
                        'absolute top-3 right-3 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200',
                        form.role === role.value
                          ? 'border-primary bg-primary'
                          : 'border-gray-200',
                      ]"
                    >
                      <svg v-if="form.role === role.value" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <!-- Icon -->
                    <div
                      :class="[
                        'flex items-center justify-center w-11 h-11 rounded-xl transition-colors duration-200',
                        form.role === role.value ? 'bg-primary/10 text-primary' : 'bg-gray-50 text-text-secondary',
                      ]"
                    >
                      <component :is="role.icon" class="w-5 h-5" />
                    </div>
                    <div>
                      <p
                        :class="[
                          'text-sm font-semibold transition-colors',
                          form.role === role.value ? 'text-primary' : 'text-text-primary',
                        ]"
                      >
                        {{ role.label }}
                      </p>
                      <p class="text-[11px] text-text-secondary mt-0.5">{{ role.desc }}</p>
                    </div>
                  </button>
                </div>

                <ButtonPrimary
                  type="button"
                  @click="nextStep"
                  :disabled="!form.role"
                  full-width
                  class="mt-2"
                >
                
                  Lanjutkan
                  
                </ButtonPrimary>
              </FormSection>
            </div>

            <!-- ============================== -->
            <!-- STEP 2: Account Info           -->
            <!-- ============================== -->
            <div v-else-if="currentStep === 1" key="step1">
              <FormSection title="Informasi Akun" description="Data login yang akan Anda gunakan">
                <InputField
                  id="reg-username"
                  v-model="form.username"
                  label="Username"
                  placeholder="contoh: ahmad_fauzi"
                  :error="form.errors.username"
                  required
                />

                <InputField
                  id="reg-email"
                  v-model="form.email"
                  label="Email"
                  type="email"
                  :placeholder="emailPlaceholder"
                  :error="form.errors.email || emailDomainError"
                  :hint="emailHint"
                  required
                  @blur="validateEmailDomain"
                />

                <InputField
                  id="reg-password"
                  v-model="form.password"
                  label="Password"
                  type="password"
                  placeholder="Minimal 8 karakter"
                  :error="form.errors.password"
                  required
                />

                <InputField
                  id="reg-password-confirm"
                  v-model="form.password_confirmation"
                  label="Konfirmasi Password"
                  type="password"
                  placeholder="Ketik ulang password"
                  :error="passwordMatchError"
                  required
                />

                <div class="flex gap-3 pt-2">
                  <ButtonPrimary type="button" variant="secondary" @click="prevStep" class="flex-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    Kembali
                  </ButtonPrimary>
                  <ButtonPrimary type="button" @click="nextStep" :disabled="!isStep1Valid" class="flex-1">
                    Lanjutkan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                  </ButtonPrimary>
                </div>
              </FormSection>
            </div>

            <!-- ============================== -->
            <!-- STEP 3: Role-Specific Profile  -->
            <!-- ============================== -->
            <div v-else-if="currentStep === 2" key="step2">
              <!-- MAHASISWA form -->
              <FormSection
                v-if="form.role === 'mahasiswa'"
                title="Data Mahasiswa"
                description="Lengkapi profil mahasiswa Anda"
              >
                <InputField
                  id="reg-nama"
                  v-model="form.nama_lengkap"
                  label="Nama Lengkap"
                  placeholder="Ahmad Fauzi"
                  :error="form.errors.nama_lengkap"
                  required
                />
                <div class="grid grid-cols-2 gap-3">
                  <InputField
                    id="reg-nim"
                    v-model="form.nim"
                    label="NIM"
                    placeholder="2024001"
                    :error="form.errors.nim"
                    required
                  />
                  <InputField
                    id="reg-prodi"
                    v-model="form.prodi"
                    label="Program Studi"
                    placeholder="Teknik Informatika"
                    :error="form.errors.prodi"
                  />
                </div>
              </FormSection>

              <!-- DOSEN form -->
              <FormSection
                v-else-if="form.role === 'dosen_pembimbing' || form.role === 'dosen_prodi'"
                title="Data Dosen"
                description="Lengkapi profil dosen Anda"
              >
                <InputField
                  id="reg-nama-dosen"
                  v-model="form.nama_dosen"
                  label="Nama Lengkap"
                  placeholder="Dr. Siti Aminah"
                  :error="form.errors.nama_dosen"
                  required
                />
                <InputField
                  id="reg-nip"
                  v-model="form.nip"
                  label="NIP"
                  placeholder="198501012020011001"
                  :error="form.errors.nip"
                  required
                />
              </FormSection>

              <!-- INDUSTRI form -->
              <FormSection
                v-else-if="form.role === 'supervisor_industri'"
                title="Data Perusahaan"
                description="Lengkapi informasi perusahaan Anda"
              >
                <InputField
                  id="reg-perusahaan"
                  v-model="form.nama_perusahaan"
                  label="Nama Perusahaan"
                  placeholder="PT Maju Mapan"
                  :error="form.errors.nama_perusahaan"
                  required
                />
                <InputField
                  id="reg-alamat"
                  v-model="form.alamat"
                  label="Alamat"
                  placeholder="Jl. Sudirman No. 123, Jakarta"
                  :error="form.errors.alamat"
                />
                <InputField
                  id="reg-kontak"
                  v-model="form.kontak_person"
                  label="Nama Supervisor"
                  placeholder="Budi Santoso"
                  :error="form.errors.kontak_person"
                />
                <div class="grid grid-cols-3 gap-3">
                  <InputField
                    id="reg-lat"
                    v-model="form.latitude"
                    label="Latitude"
                    type="number"
                    placeholder="-6.2088"
                    :error="form.errors.latitude"
                  />
                  <InputField
                    id="reg-lng"
                    v-model="form.longitude"
                    label="Longitude"
                    type="number"
                    placeholder="106.845"
                    :error="form.errors.longitude"
                  />
                  <InputField
                    id="reg-radius"
                    v-model="form.geofence_radius"
                    label="Radius (m)"
                    type="number"
                    placeholder="500"
                    :error="form.errors.geofence_radius"
                    hint="Meter"
                  />
                </div>
              </FormSection>

              <!-- Actions -->
              <div class="flex gap-3 pt-6">
                <ButtonPrimary type="button" variant="secondary" @click="prevStep" class="flex-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                  </svg>
                  Kembali
                </ButtonPrimary>
                <ButtonPrimary type="submit" :loading="form.processing" :disabled="form.processing" class="flex-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Daftar
                </ButtonPrimary>
              </div>
            </div>
          </Transition>
        </form>

        <!-- Divider -->
        <div class="px-8 pb-6">
          <div class="relative mb-4">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-100"></div>
            </div>
            <div class="relative flex justify-center text-xs">
              <span class="bg-card px-3 text-text-secondary">Sudah punya akun?</span>
            </div>
          </div>
          <Link
            href="/login"
            class="w-full inline-flex items-center justify-center gap-2 font-semibold text-sm rounded-xl px-6 py-3 text-primary hover:bg-primary/5 transition-all duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Masuk ke Akun
          </Link>
        </div>
      </CardContainer>

      <!-- Footer -->
      <p class="text-center text-xs text-text-secondary/60 mt-6">
        &copy; {{ new Date().getFullYear() }} Magang Horizon. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, h } from 'vue';
import InputField from '@/Components/InputField.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';
import CardContainer from '@/Components/CardContainer.vue';
import FormSection from '@/Components/FormSection.vue';

// ─── Icons (inline SVG render functions) ────────────────────
const StudentIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 14l9-5-9-5-9 5 9 5z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 14l6.16-3.422A12.083 12.083 0 0112 21a12.083 12.083 0 01-6.16-10.422L12 14z' }),
  ]),
};
const IndustryIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' }),
  ]),
};
const SupervisorIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' }),
  ]),
};
const ProdiIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-5 h-5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' }),
  ]),
};

// ─── Steps ──────────────────────────────────────────────────
const steps = ['Pilih Peran', 'Akun', 'Profil'];
const currentStep = ref(0);

// ─── Role Options ───────────────────────────────────────────
const roleOptions = [
  { value: 'mahasiswa', label: 'Mahasiswa', desc: 'Peserta magang', icon: StudentIcon },
  { value: 'supervisor_industri', label: 'Industri', desc: 'Perusahaan mitra', icon: IndustryIcon },
  { value: 'dosen_pembimbing', label: 'Dosen Pembimbing', desc: 'Pembimbing kampus', icon: SupervisorIcon },
  { value: 'dosen_prodi', label: 'Dosen Prodi', desc: 'Koordinator prodi', icon: ProdiIcon },
];

// ─── Form ───────────────────────────────────────────────────
const form = useForm({
  role: '',
  username: '',
  email: '',
  password: '',
  password_confirmation: '',
  // Mahasiswa
  nama_lengkap: '',
  nim: '',
  prodi: '',
  // Dosen
  nama_dosen: '',
  nip: '',
  // Industri
  nama_perusahaan: '',
  alamat: '',
  kontak_person: '',
  latitude: '',
  longitude: '',
  geofence_radius: '',
});

// ─── Validation ─────────────────────────────────────────────
const emailDomainError = ref('');

const emailPlaceholder = computed(() => {
  if (form.role === 'supervisor_industri') return 'email@company.com';
  return 'nama@krw.horizon.ac.id';
});

const emailHint = computed(() => {
  if (form.role === 'supervisor_industri') return '';
  return 'Harus diakhiri @krw.horizon.ac.id';
});

const validateEmailDomain = () => {
  if (!form.email) {
    emailDomainError.value = '';
    return;
  }
  if (form.role !== 'supervisor_industri' && !form.email.endsWith('@krw.horizon.ac.id')) {
    emailDomainError.value = 'Email harus menggunakan domain @krw.horizon.ac.id';
  } else {
    emailDomainError.value = '';
  }
};

const passwordMatchError = computed(() => {
  if (!form.password_confirmation) return '';
  return form.password !== form.password_confirmation ? 'Password tidak cocok' : '';
});

const isStep1Valid = computed(() => {
  return (
    form.username.length > 0 &&
    form.email.length > 0 &&
    !emailDomainError.value &&
    form.password.length >= 8 &&
    form.password === form.password_confirmation
  );
});

// ─── Navigation ─────────────────────────────────────────────
const selectRole = (role) => {
  form.role = role;
};

const nextStep = () => {
  if (currentStep.value < steps.length - 1) {
    currentStep.value++;
  }
};

const prevStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};

const canGoToStep = (idx) => {
  if (idx === 0) return true;
  if (idx === 1) return !!form.role;
  if (idx === 2) return !!form.role && isStep1Valid.value;
  return false;
};

const goToStep = (idx) => {
  if (canGoToStep(idx)) {
    currentStep.value = idx;
  }
};

// ─── Submit ─────────────────────────────────────────────────
const handleSubmit = () => {
  form.post('/register', {
    onError: () => {
      // If server errors are on step 1 fields, go back
      if (form.errors.username || form.errors.email || form.errors.password) {
        currentStep.value = 1;
      } else if (form.errors.role) {
        currentStep.value = 0;
      }
    },
  });
};
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease-out;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateX(16px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateX(-16px);
}
</style>
