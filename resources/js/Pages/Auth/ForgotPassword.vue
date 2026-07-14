<script setup>
import { Head, Link } from "@inertiajs/vue3";
import {
    RocketLaunchIcon,
    ArrowRightOnRectangleIcon,
    KeyIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";

defineProps({
    status: { type: String },
});
</script>

<template>
    <Head title="Lupa Password - TopUp Store" />

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
                    TopUp Store
                </Link>
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    ← Kembali ke Login
                </Link>
            </div>
        </nav>

        <!-- Main -->
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="w-full max-w-md">
                <div
                    class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8"
                >
                    <!-- Icon -->
                    <div class="flex justify-center mb-5">
                        <div
                            class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center"
                        >
                            <KeyIcon class="w-8 h-8 text-amber-600" />
                        </div>
                    </div>

                    <h1
                        class="text-xl font-bold text-gray-900 text-center mb-1"
                    >
                        Lupa Password?
                    </h1>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Tenang, Anda bisa mengatur ulang password dengan mudah
                        melalui langkah berikut:
                    </p>

                    <!-- Flash Status -->
                    <div
                        v-if="status"
                        class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2 text-sm text-green-700"
                    >
                        <CheckCircleIcon class="w-5 h-5" />
                        {{ status }}
                    </div>

                    <!-- Steps -->
                    <div class="space-y-4 mb-6">
                        <!-- Step 1 -->
                        <div
                            class="flex items-start gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100"
                        >
                            <div
                                class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                            >
                                1
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Login dengan Google
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Klik tombol "Masuk dengan Google" di halaman
                                    login
                                </p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div
                            class="flex items-start gap-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100"
                        >
                            <div
                                class="w-8 h-8 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                            >
                                2
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Atur Password Baru
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Setelah login, masuk ke
                                    <span class="font-medium"
                                        >Dashboard → Profil</span
                                    >
                                    untuk mengatur password baru
                                </p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div
                            class="flex items-start gap-4 p-4 bg-green-50 rounded-xl border border-green-100"
                        >
                            <div
                                class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center text-sm font-bold flex-shrink-0"
                            >
                                3
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    Selesai!
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Selanjutnya Anda bisa login dengan email &
                                    password
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <a
                        :href="route('google.redirect')"
                        class="group flex w-full items-center justify-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:border-gray-300 hover:shadow-md transition-all duration-200"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"
                                fill="#4285F4"
                            />
                            <path
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                fill="#34A853"
                            />
                            <path
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                fill="#FBBC05"
                            />
                            <path
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                fill="#EA4335"
                            />
                        </svg>
                        Login dengan Google Sekarang
                    </a>

                    <div class="mt-4 text-center">
                        <Link
                            :href="route('login')"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                        >
                            ← Kembali ke halaman login
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs text-gray-400">
                    &copy; {{ new Date().getFullYear() }} TopUp Store. All
                    rights reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
