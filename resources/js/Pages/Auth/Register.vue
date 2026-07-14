<script setup>
import { Head, Link } from "@inertiajs/vue3";
import {
    RocketLaunchIcon,
    UserPlusIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
} from "@heroicons/vue/24/outline";
</script>

<template>
    <Head title="Daftar - TopUp Store" />

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
                    :href="route('shop.home')"
                    class="text-sm text-gray-500 hover:text-indigo-600 transition-colors"
                >
                    ← Kembali
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
                            class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center"
                        >
                            <UserPlusIcon class="w-8 h-8 text-indigo-600" />
                        </div>
                    </div>

                    <h1
                        class="text-xl font-bold text-gray-900 text-center mb-1"
                    >
                        Buat Akun Baru
                    </h1>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Daftar dengan akun Google Anda untuk mulai berbelanja.
                    </p>

                    <!-- Flash Error -->
                    <div
                        v-if="$page.props.flash?.error"
                        class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2 text-sm text-red-700"
                    >
                        <ExclamationCircleIcon class="w-5 h-5" />
                        {{ $page.props.flash.error }}
                    </div>

                    <!-- Google Sign Up -->
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
                        Daftar dengan Google
                    </a>

                    <!-- Info note -->
                    <div
                        class="mt-5 flex items-start gap-2 rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3"
                    >
                        <InformationCircleIcon
                            class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5"
                        />
                        <span class="text-xs text-indigo-700"
                            >Data Anda aman. Kami hanya mengambil nama dan email
                            dari akun Google Anda.</span
                        >
                    </div>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Sudah punya akun?
                        <Link
                            :href="route('login')"
                            class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                            >Masuk</Link
                        >
                    </p>
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
