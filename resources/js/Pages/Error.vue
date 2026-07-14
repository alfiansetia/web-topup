<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { RocketLaunchIcon } from "@heroicons/vue/24/outline";

const page = usePage();
const appName = computed(() => page.props.appName || "TopUp Store");
const goBack = () => window.history.back();

const props = defineProps({
    status: { type: Number, required: true },
});

const errorData = {
    404: {
        title: "Halaman Tidak Ditemukan",
        message:
            "Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.",
    },
    403: {
        title: "Akses Ditolak",
        message: "Anda tidak memiliki izin untuk mengakses halaman ini.",
    },
    500: {
        title: "Server Bermasalah",
        message: "Terjadi kesalahan internal. Silakan coba lagi nanti.",
    },
    419: {
        title: "Sesi Berakhir",
        message: "Sesi Anda telah berakhir. Silakan muat ulang halaman.",
    },
    429: {
        title: "Terlalu Banyak Permintaan",
        message:
            "Anda telah mengirim terlalu banyak permintaan. Silakan tunggu sebentar.",
    },
};

const error = errorData[props.status] || {
    title: `Error ${props.status}`,
    message: "Terjadi kesalahan yang tidak terduga. Silakan coba lagi.",
};
</script>

<template>
    <Head :title="`${status} - ${error.title}`" />

    <div
        class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex flex-col"
    >
        <!-- Navbar -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
            >
                <Link
                    :href="route('shop.home')"
                    class="flex items-center gap-2 text-xl font-bold text-gray-900"
                >
                    <RocketLaunchIcon class="w-6 h-6 text-indigo-600" />
                    {{ appName }}
                </Link>
                <Link
                    :href="route('shop.home')"
                    class="text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    ← Kembali ke Beranda
                </Link>
            </div>
        </nav>

        <!-- Main -->
        <div class="flex-1 flex items-center justify-center p-4">
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
                    <svg
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
                        :href="route('shop.home')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm hover:shadow-md transition-all duration-200"
                    >
                        Kembali ke Beranda
                    </Link>
                    <button
                        @click="goBack"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200"
                    >
                        ← Kembali
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs text-gray-400">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. All
                    rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
