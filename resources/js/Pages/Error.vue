<script setup>
import { Head, Link } from "@inertiajs/vue3";

const goBack = () => window.history.back();

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
});

const errorData = {
    404: {
        title: "Halaman Tidak Ditemukan",
        message:
            "Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.",
        icon: "search",
    },
    403: {
        title: "Akses Ditolak",
        message: "Anda tidak memiliki izin untuk mengakses halaman ini.",
        icon: "lock",
    },
    500: {
        title: "Server Bermasalah",
        message: "Terjadi kesalahan internal. Silakan coba lagi nanti.",
        icon: "server",
    },
    419: {
        title: "Sesi Berakhir",
        message: "Sesi Anda telah berakhir. Silakan muat ulang halaman.",
        icon: "clock",
    },
    429: {
        title: "Terlalu Banyak Permintaan",
        message:
            "Anda telah mengirim terlalu banyak permintaan. Silakan tunggu sebentar.",
        icon: "clock",
    },
};

const error = errorData[props.status] || {
    title: `Error ${props.status}`,
    message: "Terjadi kesalahan yang tidak terduga. Silakan coba lagi.",
    icon: "exclamation",
};
</script>

<template>
    <Head :title="`${status} - ${error.title}`" />

    <!-- Standalone error page (not wrapped in GuestLayout) -->
    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center px-6"
    >
        <div class="text-center max-w-lg">
            <!-- Error Code -->
            <div class="relative mb-6">
                <span
                    class="text-[10rem] font-black text-transparent bg-clip-text bg-gradient-to-br from-indigo-200 to-purple-200 leading-none select-none"
                >
                    {{ status }}
                </span>
            </div>

            <!-- Icon -->
            <div
                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100"
            >
                <!-- search (404) -->
                <svg
                    v-if="error.icon === 'search'"
                    class="h-8 w-8 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                    />
                </svg>
                <!-- lock (403) -->
                <svg
                    v-else-if="error.icon === 'lock'"
                    class="h-8 w-8 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                    />
                </svg>
                <!-- server (500) -->
                <svg
                    v-else-if="error.icon === 'server'"
                    class="h-8 w-8 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z"
                    />
                </svg>
                <!-- clock (419, 429) -->
                <svg
                    v-else-if="error.icon === 'clock'"
                    class="h-8 w-8 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                </svg>
                <!-- exclamation (default) -->
                <svg
                    v-else
                    class="h-8 w-8 text-indigo-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                    />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ error.title }}
            </h1>
            <p class="text-gray-500 mb-8 leading-relaxed">
                {{ error.message }}
            </p>

            <div
                class="flex flex-col sm:flex-row items-center justify-center gap-3"
            >
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                        />
                    </svg>
                    Kembali ke Beranda
                </Link>
                <button
                    @click="goBack"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200"
                >
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
                        />
                    </svg>
                    Kembali
                </button>
            </div>
        </div>
    </div>
</template>
