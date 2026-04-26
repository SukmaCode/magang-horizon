import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

export function useRegisterForm() {
  const steps = ['Pilih Peran', 'Akun', 'Profil'];
  const currentStep = ref(0);

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

  return {
    steps,
    currentStep,
    form,
    emailDomainError,
    emailPlaceholder,
    emailHint,
    validateEmailDomain,
    passwordMatchError,
    isStep1Valid,
    nextStep,
    prevStep,
    canGoToStep,
    goToStep,
    handleSubmit,
  };
}
