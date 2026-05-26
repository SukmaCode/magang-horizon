<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            >
                <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="$emit('update:show', false)"></div>
                
                <div class="relative w-full max-w-md bg-white rounded-md shadow-xl overflow-hidden transform transition-all">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-lg font-jakartaBold text-text-primary">
                            {{ step === 1 ? 'Lupa Password' : step === 2 ? 'Verifikasi OTP' : 'Reset Password' }}
                        </h3>
                        <button
                            @click="$emit('update:show', false)"
                            class="text-gray-400 hover:text-gray-500 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6">
                        <!-- Step 1: Input Email -->
                        <form v-if="step === 1" @submit.prevent="handleSendOtp" class="space-y-5">
                            <p class="text-sm text-text-secondary font-jakarta mb-4">
                                Masukkan email Anda yang terdaftar. Kami akan mengirimkan 6 digit kode OTP untuk mereset password Anda.
                            </p>
                            
                            <InputField
                                id="reset_email"
                                v-model="formEmail.email"
                                label="Email"
                                type="email"
                                placeholder="nama@krw.horizon.ac.id"
                                :error="formEmail.errors.email"
                                required
                            />

                            <ButtonPrimary
                                type="submit"
                                :loading="formEmail.processing"
                                :disabled="formEmail.processing"
                                full-width
                            >
                                Kirim OTP
                            </ButtonPrimary>
                        </form>

                        <!-- Step 2: Verifikasi OTP -->
                        <form v-else-if="step === 2" @submit.prevent="handleVerifyOtp" class="space-y-5">
                            <p class="text-sm text-text-secondary font-jakarta mb-4 text-center">
                                Masukkan kode 6 digit yang telah kami kirimkan ke <br>
                                <span class="font-jakartaBold text-text-primary">{{ formEmail.email }}</span>
                            </p>
                            
                            <div class="flex justify-center">
                                <input
                                    type="text"
                                    v-model="formOtp.otp"
                                    placeholder="••••••"
                                    class="w-32 text-center text-2xl tracking-widest font-jakartaBold rounded-md border-gray-300 focus:border-primary focus:ring-primary/20"
                                    maxlength="6"
                                    required
                                />
                            </div>
                            <p v-if="formOtp.errors.otp" class="text-sm text-danger text-center mt-1">
                                {{ formOtp.errors.otp }}
                            </p>

                            <ButtonPrimary
                                type="submit"
                                :loading="formOtp.processing"
                                :disabled="formOtp.processing || formOtp.otp.length !== 6"
                                full-width
                                class="mt-4"
                            >
                                Verifikasi
                            </ButtonPrimary>

                            <div class="text-center mt-4">
                                <button
                                    type="button"
                                    @click="step = 1"
                                    class="text-xs font-jakartaSemiBold text-text-secondary hover:text-primary transition-colors"
                                >
                                    Ganti Email
                                </button>
                            </div>
                        </form>

                        <!-- Step 3: Reset Password -->
                        <form v-else-if="step === 3" @submit.prevent="handleResetPassword" class="space-y-5">
                            <p class="text-sm text-text-secondary font-jakarta mb-4">
                                Silakan buat password baru Anda. Pastikan kombinasi aman dan mudah diingat.
                            </p>

                            <InputField
                                id="new_password"
                                v-model="formReset.password"
                                label="Password Baru"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                :error="formReset.errors.password"
                                required
                            />

                            <InputField
                                id="password_confirmation"
                                v-model="formReset.password_confirmation"
                                label="Konfirmasi Password"
                                type="password"
                                placeholder="Ulangi password baru"
                                :error="formReset.errors.password_confirmation"
                                required
                            />

                            <ButtonPrimary
                                type="submit"
                                :loading="formReset.processing"
                                :disabled="formReset.processing"
                                full-width
                            >
                                Simpan Password Baru
                            </ButtonPrimary>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputField from '@/Components/InputField.vue';
import ButtonPrimary from '@/Components/ButtonPrimary.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:show']);

const step = ref(1);

const formEmail = useForm({
    email: '',
});

const formOtp = useForm({
    email: '',
    otp: '',
});

const formReset = useForm({
    email: '',
    otp: '',
    password: '',
    password_confirmation: '',
});

// Reset forms when modal closes
watch(() => props.show, (newVal) => {
    if (!newVal) {
        setTimeout(() => {
            step.value = 1;
            formEmail.reset();
            formOtp.reset();
            formReset.reset();
            formEmail.clearErrors();
            formOtp.clearErrors();
            formReset.clearErrors();
        }, 300); // wait for animation
    }
});

const handleSendOtp = () => {
    formEmail.post('/internship/password/forgot', {
        preserveScroll: true,
        onSuccess: () => {
            formOtp.email = formEmail.email;
            formReset.email = formEmail.email;
            step.value = 2;
        },
    });
};

const handleVerifyOtp = () => {
    formOtp.post('/internship/password/verify-otp', {
        preserveScroll: true,
        onSuccess: () => {
            formReset.otp = formOtp.otp;
            step.value = 3;
        },
    });
};

const handleResetPassword = () => {
    formReset.post('/internship/password/reset', {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:show', false);
            alert('Password berhasil direset. Silakan login kembali.');
        },
    });
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .transform,
.modal-leave-to .transform {
    transform: scale(0.95);
}
</style>
