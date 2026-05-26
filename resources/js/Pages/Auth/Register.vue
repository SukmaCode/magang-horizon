<template>
    <Head title="Daftar" />

    <div
        class="min-h-screen bg-white flex items-center justify-center p-4 sm:p-6 lg:p-8"
    >
        <!-- Decorative blobs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -left-40 w-96 h-96 bg-accent/5 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="relative w-full max-w-lg">
            <!-- Logo / Brand -->
            <div class="flex flex-col items-center gap-2 mb-8">
                <img
                    src="../../../assets/images/logo-horizon.png"
                    alt="logo"
                    class="w-24 h-24"
                />
                <h1 class="text-2xl font-jakartaSemiBold text-text-primary">
                    Buat Akun Baru
                </h1>
                <p class="mt-1.5 text-sm text-text-secondary">
                    Daftar untuk memulai program magang
                </p>
            </div>

            <CardContainer padding="p-4">
                <!-- Step Indicator -->
                <RegisterStepIndicator
                    :steps="steps"
                    :currentStep="currentStep"
                    :canGoToStep="canGoToStep"
                    @go-to-step="goToStep"
                />

                <!-- Flash Error -->
                <div
                    v-if="Object.keys(form.errors).length > 0"
                    class="mx-8 mb-4"
                >
                    <div
                        class="flex items-start gap-3 p-4 bg-danger/5 border border-danger/20 rounded-xl"
                    >
                        <svg
                            class="w-5 h-5 text-danger shrink-0 mt-0.5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <div class="text-sm text-danger">
                            <p v-for="(error, key) in form.errors" :key="key">
                                {{ error }}
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="handleSubmit" class="px-8 pb-8">
                    <Transition name="fade-slide" mode="out-in">
                        <!-- STEP 1: Choose Role -->
                        <RegisterRoleSelection
                            v-if="currentStep === 0"
                            key="step0"
                            v-model="form.role"
                            @next="nextStep"
                        />

                        <!-- STEP 2: Account Info -->
                        <RegisterAccountInfo
                            v-else-if="currentStep === 1"
                            key="step1"
                            :form="form"
                            :emailPlaceholder="emailPlaceholder"
                            :emailHint="emailHint"
                            :emailDomainError="emailDomainError"
                            :passwordMatchError="passwordMatchError"
                            :isStep1Valid="isStep1Valid"
                            @validate-email="validateEmailDomain"
                            @prev="prevStep"
                            @next="nextStep"
                        />

                        <!-- STEP 3: Role-Specific Profile -->
                        <div v-else-if="currentStep === 2" key="step2">
                            <RegisterProfileMahasiswa
                                v-if="form.role === 'mahasiswa'"
                                :form="form"
                            />
                            <RegisterProfileDosen
                                v-else-if="
                                    [
                                        'dosen_pembimbing',
                                        'dosen_prodi',
                                    ].includes(form.role)
                                "
                                :form="form"
                            />
                            <RegisterProfileIndustri
                                v-else-if="form.role === 'supervisor_industri'"
                                :form="form"
                            />

                            <!-- Actions -->
                            <div class="flex gap-3 pt-6">
                                <ButtonPrimary
                                    type="button"
                                    variant="secondary"
                                    @click="prevStep"
                                    class="flex-1"
                                >
                                    Kembali
                                </ButtonPrimary>
                                <ButtonPrimary
                                    type="button"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    @click="sendOtpAndNextStep"
                                    class="flex-1"
                                >
                                    Lanjutkan
                                </ButtonPrimary>
                            </div>
                        </div>

                        <!-- STEP 4: OTP Verification -->
                        <div v-else-if="currentStep === 3" key="step3">
                            <RegisterOtpVerification 
                                v-model="form"
                                :email="form.email"
                                :error="form.errors.otp"
                                @resend="sendOtpAndNextStep"
                            />
                            
                            <!-- Actions -->
                            <div class="flex gap-3 pt-6">
                                <ButtonPrimary
                                    type="button"
                                    variant="secondary"
                                    @click="prevStep"
                                    class="flex-1"
                                >
                                    Kembali
                                </ButtonPrimary>
                                <ButtonPrimary
                                    type="submit"
                                    :loading="form.processing"
                                    :disabled="form.processing || form.otp.length < 6"
                                    class="flex-1"
                                >
                                    Daftar
                                </ButtonPrimary>
                            </div>
                        </div>
                    </Transition>
                </form>

                <!-- Divider -->

                <div class="flex flex-row justify-center items-center gap-2">
                    <span class="bg-card font-jakarta text-text-secondary text-sm"
                        >Sudah punya akun?</span
                    >
                    <Link :href="url('/login')" class="text-red-800 font-jakartaSemiBold text-sm">
                        Masuk ke Akun
                    </Link>
                </div>
            </CardContainer>

            <!-- Footer -->
            <p class="text-center font-jakartaSemiBold text-xs text-text-secondary/60 mt-6">
                &copy; {{ new Date().getFullYear() }} Internship Horizon University Indonesia.
            </p>
            <p class="text-center font-jakartaSemiBold text-xs text-text-secondary/60">Faculty of Informatics and Computer Technology.</p>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import CardContainer from "@/Components/CardContainer.vue";
import ButtonPrimary from "@/Components/ButtonPrimary.vue";
import { url } from '@/utils/prefix';

// Composables
import { useRegisterForm } from "@/Composables/useRegisterForm";

// Components
import RegisterStepIndicator from "@/Components/Auth/RegisterStepIndicator.vue";
import RegisterRoleSelection from "@/Components/Auth/RegisterRoleSelection.vue";
import RegisterAccountInfo from "@/Components/Auth/RegisterAccountInfo.vue";
import RegisterProfileMahasiswa from "@/Components/Auth/RegisterProfileMahasiswa.vue";
import RegisterProfileDosen from "@/Components/Auth/RegisterProfileDosen.vue";
import RegisterProfileIndustri from "@/Components/Auth/RegisterProfileIndustri.vue";
import RegisterOtpVerification from "@/Components/Auth/RegisterOtpVerification.vue";

const {
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
    sendOtpAndNextStep,
    handleSubmit,
} = useRegisterForm();
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
