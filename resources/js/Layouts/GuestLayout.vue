<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const appName = computed(() => import.meta.env.VITE_APP_NAME || "TopUp Store");
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Left: Decorative Panel -->
        <div
            class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 overflow-hidden"
        >
            <!-- Animated orbs -->
            <div
                class="absolute -top-24 -left-24 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute top-1/2 left-1/3 w-64 h-64 bg-blue-400/15 rounded-full blur-2xl"
            ></div>

            <!-- Grid pattern overlay -->
            <div
                class="absolute inset-0 opacity-[0.03]"
                style="
                    background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
                "
            ></div>

            <div
                class="relative z-10 flex flex-col justify-center px-16 text-white"
            >
                <Link href="/" class="flex items-center gap-3 mb-10">
                    <div
                        class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20"
                    >
                        <svg
                            class="w-7 h-7 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                            />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight"
                        >TopUp Store</span
                    >
                </Link>

                <h1 class="text-4xl font-extrabold leading-tight mb-4">
                    Akun Premium<br />
                    <span class="text-indigo-200">Harga Terjangkau</span>
                </h1>
                <p class="text-lg text-indigo-100/80 leading-relaxed max-w-md">
                    Dapatkan akses ke layanan streaming, gaming, dan aplikasi
                    premium dengan harga terbaik dan proses instan.
                </p>

                <!-- Feature pills -->
                <div class="flex flex-wrap gap-3 mt-8">
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm border border-white/10"
                    >
                        <svg
                            class="w-4 h-4 text-green-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m4.5 12.75 6 6 9-13.5"
                            />
                        </svg>
                        Proses Instan
                    </span>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm border border-white/10"
                    >
                        <svg
                            class="w-4 h-4 text-green-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m4.5 12.75 6 6 9-13.5"
                            />
                        </svg>
                        Garansi 100%
                    </span>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm border border-white/10"
                    >
                        <svg
                            class="w-4 h-4 text-green-300"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m4.5 12.75 6 6 9-13.5"
                            />
                        </svg>
                        Support 24/7
                    </span>
                </div>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div
            class="flex w-full lg:w-1/2 flex-col items-center justify-center bg-white px-6 py-12"
        >
            <div class="w-full max-w-[420px]">
                <!-- Mobile logo -->
                <div
                    class="lg:hidden flex items-center justify-center gap-2 mb-8"
                >
                    <div
                        class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center"
                    >
                        <svg
                            class="w-6 h-6 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                            />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900"
                        >TopUp Store</span
                    >
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
