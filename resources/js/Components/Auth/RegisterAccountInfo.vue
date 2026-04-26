<template>
    <FormSection
        title="Informasi Akun"
        description="Data login yang akan Anda gunakan"
    >
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
            @blur="$emit('validate-email')"
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
            <ButtonPrimary
                type="button"
                variant="secondary"
                @click="$emit('prev')"
                class="flex-1"
            >
                Kembali
            </ButtonPrimary>
            <ButtonPrimary
                type="button"
                @click="$emit('next')"
                :disabled="!isStep1Valid"
                class="flex-1"
            >
                Lanjutkan
            </ButtonPrimary>
        </div>
    </FormSection>
</template>

<script setup>
import FormSection from "@/Components/FormSection.vue";
import InputField from "@/Components/InputField.vue";
import ButtonPrimary from "@/Components/ButtonPrimary.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },
    emailPlaceholder: {
        type: String,
        required: true,
    },
    emailHint: {
        type: String,
        required: true,
    },
    emailDomainError: {
        type: String,
        default: "",
    },
    passwordMatchError: {
        type: String,
        default: "",
    },
    isStep1Valid: {
        type: Boolean,
        required: true,
    },
});

defineEmits(["validate-email", "prev", "next"]);
</script>
