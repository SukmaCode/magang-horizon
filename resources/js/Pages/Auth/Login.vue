<template>
    <Head title="Login" />

    <div
        class="min-h-screen bg-white flex items-center justify-center p-4 sm:p-6 lg:p-8"
    >
        <!-- Decorative blobs -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute -top-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent/5 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Logo / Brand -->
            <div class="flex flex-col items-center justify-center gap-2 mb-8">
                <img
                    src="../../../assets/images/logo-horizon.png"
                    alt="logo"
                    class="w-24 h-24"
                />

                <h1 class="text-2xl text-center font-sans font-bold text-text-primary">
                    Magang Horizon University Indonesia
                </h1>
                <p class="mt-1.5 text-sm text-center text-text-secondary">
                    Jl. Pangkal Perjuangan By Pass No.KM.1, Tanjungpura, Kec.
                    Karawang Bar., Karawang, Jawa Barat 41316, Indonesia
                </p>
            </div>

            <!-- Login Card -->
            <CardContainer padding="p-4">
                <!-- Flash Error -->
                <div
                    v-if="form.errors.email || loginError"
                    class="mb-6 flex items-start gap-3 p-4 bg-danger/5 border border-danger/20 rounded-xl"
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
                    <p class="text-sm text-danger">
                        {{ form.errors.email || loginError }}
                    </p>
                </div>

                <div>
                    <form @submit.prevent="handleLogin" class="space-y-5">
                        <InputField
                            id="email"
                            v-model="form.email"
                            label="Email"
                            type="email"
                            placeholder="nama@krw.horizon.ac.id"
                            :error="form.errors.email"
                            required
                        />

                        <InputField
                            id="password"
                            v-model="form.password"
                            label="Password"
                            type="password"
                            placeholder="••••••••"
                            :error="form.errors.password"
                            required
                        />

                        <!-- Remember me -->
                        <div class="flex items-center justify-between">
                            <label
                                class="flex items-center gap-2 cursor-pointer group"
                            >
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary/30 transition-colors"
                                />
                                <span
                                    class="text-sm text-text-secondary group-hover:text-text-primary transition-colors"
                                >
                                    Ingat saya
                                </span>
                            </label>
                        </div>

                        <ButtonPrimary
                            type="submit"
                            :loading="form.processing"
                            :disabled="form.processing"
                            full-width
                        >
                            Masuk
                        </ButtonPrimary>
                    </form>

                    <!-- Divider -->
                    <div class="relative mt-6 mb-6">
                        <div class="relative flex justify-center text-xs gap-2">
                            <span class="bg-card text-text-secondary"
                                >Belum punya akun?</span
                            >
                            <Link
                                href="/register"
                                class="text-primary font-semibold"
                            >
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>
                </div>
                <div></div>
            </CardContainer>

            <!-- Footer -->
            <p class="text-center text-xs text-text-secondary/60 mt-6">
                &copy; {{ new Date().getFullYear() }} Magang Horizon. All rights
                reserved.
            </p>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import ButtonPrimary from "@/Components/ButtonPrimary.vue";
import CardContainer from "@/Components/CardContainer.vue";
import { ref } from "vue";

const loginError = ref("");

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const handleLogin = () => {
    loginError.value = "";
    form.post("/login", {
        onError: () => {
            form.reset("password");
        },
    });
};
</script>
