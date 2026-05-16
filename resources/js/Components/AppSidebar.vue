<template>
    <!-- Mobile Sidebar Backdrop -->
    <Transition
        enter-active-class="transition-opacity ease-linear duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-linear duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-show="isOpen"
            class="fixed inset-0 bg-gray-900/80 z-40 md:hidden"
            @click="$emit('close')"
        ></div>
    </Transition>

    <!-- Sidebar -->
    <aside
        :class="[
            isOpen ? 'translate-x-0' : '-translate-x-full',
            'fixed inset-y-0 left-0 z-50 w-64 bg-primary flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0',
        ]"
    >
        <!-- Logo -->
        <div
            class="px-6 py-3 flex items-center justify-between gap-3 border-b border-white/20 shrink-0"
        >
            <div class="flex items-center gap-3">
                <img
                    src="../../assets/images/logo-horizon.png"
                    alt="logo"
                    class="w-10 h-10"
                />
                <span class="font-jakartaSemiBold text-lg text-card"
                    >Magang<span class="text-card">Horizon</span></span
                >
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 mt-2 space-y-0.5 overflow-y-auto">
            <template v-for="item in navigation" :key="item.name">
                <Link
                    @click="$emit('close')"
                    :href="item.href"
                    :class="[
                        item.current
                            ? 'bg-surface text-primary font-jakartaSemiBold'
                            : 'text-white font-jakarta hover:bg-surface hover:text-primary',
                        'group flex items-center px-6 py-2 mx-2 rounded-sm text-sm font-jakartaSemiBold transition-all duration-200',
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            item.current
                                ? 'text-primary'
                                : 'text-surface group-hover:text-gray-500',
                            'shrink-0 -ml-1 mr-3 h-5 w-5 transition-colors duration-200',
                        ]"
                        aria-hidden="true"
                    />
                    <span class="truncate">{{ item.name }}</span>
                </Link>
            </template>
        </nav>

        <!-- Logout -->
        <div class="p-2 border-t border-white/20 shrink-0">
            <Link
                @click="$emit('close')"
                href="/logout"
                method="post"
                as="button"
                class="w-full flex items-center gap-2 px-3 py-2 text-sm font-jakartaSemiBold text-card cursor-pointer hover:text-danger rounded-xl transition-colors duration-200"
            >
                <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
                Keluar
            </Link>
        </div>
    </aside>
</template>

<script setup>
import { computed, h } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

// ─── Props & Emits ──────────────────────────────────────────────────────
defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["close"]);

// ─── State ──────────────────────────────────────────────────────────────
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const role = computed(() => user.value?.role);

// ─── Icons ──────────────────────────────────────────────────────────────
const HomeIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6",
                }),
            ],
        ),
};
const DocumentIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
                }),
            ],
        ),
};
const CheckIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
                }),
            ],
        ),
};
const ClipboardIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
                }),
            ],
        ),
};
const UploadIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12",
                }),
            ],
        ),
};
const CertificateIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z",
                }),
            ],
        ),
};
const UserIcon = {
    render: () =>
        h(
            "svg",
            { fill: "none", stroke: "currentColor", viewBox: "0 0 24 24" },
            [
                h("path", {
                    "stroke-linecap": "round",
                    "stroke-linejoin": "round",
                    "stroke-width": "2",
                    d: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
                }),
            ],
        ),
};

// ─── Navigation ─────────────────────────────────────────────────────────
const navigation = computed(() => {
    const currentPath = window.location.pathname;

    // 1. Menu dasar yang selalu ada di semua Role
    const menu = [];

    // 2. Tambahan menu KHUSUS MAHASISWA
    if (role.value === "mahasiswa") {
        menu.push({
            name: "Dashboard",
            href: "/mahasiswa/dashboard",
            icon: HomeIcon,
            current: currentPath.includes("dashboard"),
        });
        menu.push({
            name: "Manajemen CV",
            href: "/mahasiswa/manajemen-cv",
            icon: DocumentIcon,
            current: currentPath.includes("manajemen-cv"),
        });
        menu.push({
            name: "Kirim CV",
            href: "/mahasiswa/kirim-cv",
            icon: DocumentIcon,
            current: currentPath.includes("kirim-cv"),
        });
        menu.push({
            name: "Agreement",
            href: "/mahasiswa/agreement",
            icon: DocumentIcon,
            current: currentPath.includes("agreement"),
        });
        menu.push({
            name: "Logbook",
            href: "/mahasiswa/logbook",
            icon: ClipboardIcon,
            current: currentPath.includes("logbook"),
        });
        menu.push({
            name: "Laporan Akhir",
            href: "/mahasiswa/laporan-akhir",
            icon: UploadIcon,
            current: currentPath.includes("laporan-akhir"),
        });
        menu.push({
            name: "Clearance",
            href: "/mahasiswa/clearance",
            icon: UploadIcon,
            current: currentPath.includes("clearance"),
        });

        menu.push({
            name: "Surat Orisinalitas",
            href: "/mahasiswa/declaration",
            icon: DocumentIcon,
            current: currentPath.includes("declaration"),
        });
        menu.push({
            name: "Evaluasi Portfolio",
            href: "/mahasiswa/portfolio-evaluation",
            icon: DocumentIcon,
            current: currentPath.includes("portfolio-evaluation"),
        });
        menu.push({
            name: "Evaluasi Industri",
            href: "/mahasiswa/evaluasi",
            icon: CheckIcon,
            current: currentPath === "/mahasiswa/evaluasi",
        });
        menu.push({
            name: "Evaluasi Magang",
            href: "/mahasiswa/internship-evaluation",
            icon: CertificateIcon,
            current: currentPath.includes("internship-evaluation"),
        });
        menu.push({
            name: "Sertifikat",
            href: "/mahasiswa/sertifikat",
            icon: CertificateIcon,
            current: currentPath.includes("sertifikat"),
        });
    }

    // 3. Tambahan menu KHUSUS DOSEN PEMBIMBING
    if (role.value === "dosen_pembimbing") {
        menu.push({
            name: "Dashboard",
            href: "/dosen-pembimbing/dashboard",
            icon: HomeIcon,
            current: currentPath.includes("dashboard"),
        });
        menu.push({
            name: "Monitoring Logbook",
            href: "/dosen-pembimbing/monitoring-logbook",
            icon: ClipboardIcon,
            current: currentPath.includes("monitoring-logbook"),
        });
        menu.push({
            name: "Review Laporan",
            href: "/dosen-pembimbing/review-laporan",
            icon: CheckIcon,
            current: currentPath.includes("review-laporan"),
        });
        menu.push({
            name: "Verifikasi Declaration",
            href: "/dosen-pembimbing/declaration",
            icon: DocumentIcon,
            current: currentPath.includes("declaration"),
        });
        menu.push({
            name: "Clearance",
            href: "/dosen-pembimbing/clearance",
            icon: DocumentIcon,
            current: currentPath.includes("clearance"),
        });
        menu.push({
            name: "Evaluasi Magang",
            href: "/dosen-pembimbing/internship-evaluation",
            icon: ClipboardIcon,
            current: currentPath.includes("internship-evaluation"),
        });
    }

    // 4. Tambahan menu KHUSUS INDUSTRI
    if (role.value === "supervisor_industri") {
        menu.push({
            name: "Dashboard",
            href: "/industri/dashboard",
            icon: HomeIcon,
            current: currentPath.includes("dashboard"),
        });
        menu.push({
            name: "Review CV",
            href: "/industri/seleksi-cv",
            icon: DocumentIcon,
            current: currentPath.includes("seleksi-cv"),
        });
        menu.push({
            name: "Agreement",
            href: "/industri/agreement",
            icon: DocumentIcon,
            current: currentPath.includes("agreement"),
        });
        menu.push({
            name: "Persetujuan Logbook",
            href: "/industri/persetujuan-logbook",
            icon: CheckIcon,
            current: currentPath.includes("persetujuan-logbook"),
        });
        menu.push({
            name: "Evaluasi Mahasiswa",
            href: "/industri/evaluasi",
            icon: ClipboardIcon,
            current: currentPath.includes("evaluasi"),
        });
        menu.push({
            name: "Completion Letter",
            href: "/industri/completion-letter",
            icon: DocumentIcon,
            current: currentPath.includes("completion-letter"),
        });
        menu.push({
            name: "Surat Tanggung Jawab",
            href: "/industri/clearance",
            icon: DocumentIcon,
            current: currentPath.includes("clearance"),
        });
        menu.push({
            name: "Portfolio",
            href: "/industri/portfolio-evaluation",
            icon: DocumentIcon,
            current: currentPath.includes("portfolio-evaluation"),
        });
    }

    // 5. Tambahan menu KHUSUS DOSEN PRODI
    if (role.value === "dosen_prodi") {
        menu.push({
            name: "Dashboard",
            href: "/dosen-prodi/dashboard",
            icon: HomeIcon,
            current: currentPath.includes("dashboard"),
        });
        menu.push({
            name: "Verifikasi Kelulusan",
            href: "/dosen-prodi/verifikasi-kelulusan",
            icon: CheckIcon,
            current: currentPath.includes("verifikasi-kelulusan"),
        });
        menu.push({
            name: "Upload SK",
            href: "/dosen-prodi/surat-keputusan",
            icon: CheckIcon,
            current: currentPath.includes("surat-keputusan"),
        });
    }

    // 6. Tambahan menu KHUSUS ADMIN
    if (role.value === "admin") {
        menu.push({
            name: "Dashboard",
            href: "/admin/dashboard",
            icon: HomeIcon,
            current: currentPath.includes("dashboard"),
        });
        menu.push({
            name: "Kelola Periode",
            href: "/admin/periode",
            icon: ClipboardIcon,
            current: currentPath.includes("periode"),
        });
        menu.push({
            name: "Assign Pembimbing",
            href: "/admin/assign-pembimbing",
            icon: DocumentIcon,
            current: currentPath.includes("assign-pembimbing"),
        });
        menu.push({
            name: "Verifikasi Kelulusan",
            href: "/admin/verifikasi-kelulusan",
            icon: CertificateIcon,
            current: currentPath.includes("verifikasi-kelulusan"),
        });
        menu.push({
            name: "Manajemen User",
            href: "/admin/manajemen-user",
            icon: CheckIcon,
            current: currentPath.includes("manajemen-user"),
        });
    }

    return menu;
});
</script>
