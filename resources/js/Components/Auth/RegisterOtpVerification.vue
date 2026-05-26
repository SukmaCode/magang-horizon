<template>
    <div class="space-y-6">
        <div class="text-center">
            <h3 class="text-lg font-jakartaSemiBold text-text-primary">Verifikasi Email</h3>
            <p class="text-sm text-text-secondary mt-2">
                Kami telah mengirimkan kode OTP berisi 6 digit ke email 
                <span class="font-jakartaSemiBold">{{ email }}</span>.
                Masukkan kode tersebut di bawah ini untuk memverifikasi akun Anda.
            </p>
        </div>

        <div>
            <label class="block text-sm font-jakartaMedium text-text-primary mb-2">
                Kode OTP
            </label>
            <input
                type="text"
                v-model="modelValue.otp"
                maxlength="6"
                placeholder="000000"
                class="w-full text-center text-2xl tracking-[0.5em] font-jakartaSemiBold px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary transition-colors bg-gray-50/50"
                :class="{ 'border-danger focus:border-danger focus:ring-danger': error }"
            />
            <p v-if="error" class="mt-2 text-sm text-danger text-center">
                {{ error }}
            </p>
        </div>

        <div class="text-center">
            <button 
                type="button" 
                @click="resendOtp" 
                class="text-sm text-primary hover:text-primary-dark font-jakartaMedium transition-colors"
                :disabled="isResending"
            >
                {{ isResending ? 'Mengirim ulang...' : 'Kirim Ulang Kode OTP' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
    email: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    }
});

const emit = defineEmits(['update:modelValue', 'resend']);

const isResending = ref(false);

const resendOtp = () => {
    isResending.value = true;
    emit('resend');
    
    // Reset state after a short delay to simulate network request if we just rely on parent
    setTimeout(() => {
        isResending.value = false;
    }, 2000);
};
</script>
