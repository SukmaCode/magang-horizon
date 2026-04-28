<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Admin" />

        <div class="mb-4">
            <h1 class="text-lg font-bold text-text-primary font-jakarta">
                Dashboard Admin
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Sistem informasi dan pengelolaan pengguna Magang Horizon.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
            <CardContainer padding="p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-xs font-semibold text-text-secondary uppercase tracking-wider"
                        >
                            Total Pengguna
                        </h3>
                        <p
                            class="text-xl font-bold text-text-primary font-jakarta"
                        >
                            {{ totalUsers }}
                        </p>
                    </div>
                </div>
            </CardContainer>

            <CardContainer padding="p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-xs font-semibold text-text-secondary uppercase tracking-wider"
                        >
                            Total Magang
                        </h3>
                        <p
                            class="text-xl font-bold text-text-primary font-jakarta"
                        >
                            {{ totalMagang }}
                        </p>
                    </div>
                </div>
            </CardContainer>

            <CardContainer padding="p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center text-success"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h3
                            class="text-xs font-semibold text-text-secondary uppercase tracking-wider"
                        >
                            Mahasiswa Lulus
                        </h3>
                        <p
                            class="text-xl font-bold text-text-primary font-jakarta"
                        >
                            {{ totalLulus }}
                        </p>
                    </div>
                </div>
            </CardContainer>
        </div>

        <CardContainer class="overflow-hidden">
            <div
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between"
            >
                <h2 class="text-base font-bold text-text-primary font-jakarta">
                    Pengguna Terdaftar Terbaru
                </h2>
                <button
                    class="text-sm font-semibold text-primary hover:text-primary-hover"
                >
                    Kelola Pengguna
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-gray-50/50 text-text-secondary font-semibold border-b border-gray-100"
                    >
                        <tr>
                            <th class="px-6 py-3">Nama Lengkap</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-50"
                        v-if="recentUsers.length > 0"
                    >
                        <tr
                            v-for="user in recentUsers"
                            :key="user.id"
                            class="hover:bg-gray-50/30 transition-colors"
                        >
                            <td class="px-6 py-4 font-medium text-text-primary">
                                {{ user.name }}
                            </td>
                            <td class="px-6 py-4 text-text-secondary">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize"
                                    :class="roleBadge(user.role)"
                                >
                                    {{ user.role.replace("_", " ") }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-text-secondary">
                                {{ user.created_at }}
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td
                                colspan="4"
                                class="px-6 py-8 text-center text-text-secondary"
                            >
                                Belum ada pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContainer>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CardContainer from "@/Components/CardContainer.vue";

defineProps({
    totalUsers: Number,
    totalMagang: Number,
    totalLulus: Number,
    recentUsers: Array,
});

function roleBadge(role) {
    const badges = {
        admin: "bg-danger/10 text-danger",
        mahasiswa: "bg-primary/10 text-primary",
        supervisor_industri: "bg-accent/10 text-accent",
        dosen_pembimbing: "bg-blue-50 text-blue-600",
        dosen_prodi: "bg-success/10 text-success",
    };
    return badges[role] || "bg-gray-100 text-gray-600";
}
</script>
