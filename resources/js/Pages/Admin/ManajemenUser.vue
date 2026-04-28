<template>
    <AuthenticatedLayout>
        <Head title="Manajemen User" />

        <div class="mb-8 flex justify-between items-center gap-4">
            <div>
                <h1 class="text-lg font-bold text-text-primary font-jakarta">Manajemen User</h1>
                <p class="text-xs text-text-secondary mt-1">Kelola akun pengguna, peran, dan hak akses dalam sistem.</p>
            </div>
            <button class="px-3 py-1.5 md:py-2.5 text-xs md:text-sm font-semibold text-white bg-primary rounded-lg hover:bg-primary-hover shadow-sm flex items-center gap-2">
                Tambah User
            </button>
        </div>

        <CardContainer class="overflow-hidden">
            <div class="py-1 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <div class="relative w-72">
                    <input type="text" placeholder="Cari pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-primary/20 focus:border-primary" />
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50/50 text-text-secondary font-semibold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" v-if="users.data && users.data.length > 0">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-text-primary">{{ user.username }}</td>
                            <td class="px-6 py-4 text-text-secondary">{{ user.email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize" :class="roleBadge(user.role)">
                                    {{ user.role_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button class="text-sm font-semibold text-text-secondary hover:text-primary transition-colors">Edit</button>
                                <button class="text-sm font-semibold text-text-secondary hover:text-danger transition-colors">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-text-secondary">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.links && users.links.length > 3" class="px-6 py-4 border-t border-gray-100 flex justify-center gap-1 bg-gray-50/30">
                <template v-for="link in users.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" class="px-3.5 py-2 text-sm rounded-lg transition-colors duration-200" :class="[link.active ? 'bg-primary text-white font-semibold shadow-sm' : 'text-text-secondary hover:bg-gray-200']" v-html="link.label" preserve-scroll />
                    <span v-else class="px-3.5 py-2 text-sm text-gray-300" v-html="link.label" />
                </template>
            </div>
        </CardContainer>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CardContainer from '@/Components/CardContainer.vue';

defineProps({
    users: Object,
});

function roleBadge(role) {
    const badges = {
        admin: 'bg-danger/10 text-danger border border-danger/20',
        mahasiswa: 'bg-primary/10 text-primary border border-primary/20',
        supervisor_industri: 'bg-accent/10 text-accent border border-accent/20',
        dosen_pembimbing: 'bg-blue-50 text-blue-600 border border-blue-200',
        dosen_prodi: 'bg-success/10 text-success border border-success/20'
    };
    return badges[role] || 'bg-gray-100 text-gray-600';
}
</script>
