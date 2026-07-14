<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    RocketLaunchIcon,
    EnvelopeIcon,
    LockClosedIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
} from "@heroicons/vue/24/outline";

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Masuk - TopUp Store" />

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
                            <RocketLaunchIcon class="w-8 h-8 text-indigo-600" />
                        </div>
                    </div>

                    <h1
                        class="text-xl font-bold text-gray-900 text-center mb-1"
                    >
                        Selamat Datang
                    </h1>
                    <p class="text-sm text-gray-500 text-center mb-6">
                        Masuk ke akun Anda untuk melanjutkan
                    </p>

                    <!-- Flash Status -->
                    <div
                        v-if="status"
                        class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2 text-sm text-green-700"
                    >
                        <CheckCircleIcon class="w-5 h-5" />
                        {{ status }}
                    </div>

                    <!-- Flash Error -->
                    <div
                        v-if="$page.props.flash?.error"
                        class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl flex items-center gap-2 text-sm text-red-700"
                    >
                        <ExclamationCircleIcon class="w-5 h-5" />
                        {{ $page.props.flash.error }}
                    </div>

                    <!-- Google Login -->
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
                        Masuk dengan Google
                    </a>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-100"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span
                                class="bg-white px-4 text-gray-400 uppercase tracking-wider font-medium"
                                >atau</span
                            >
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Email</label
                            >
                            <div class="relative">
                                <EnvelopeIcon
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="email@contoh.com"
                                    class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                                />
                            </div>
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Password</label
                                >
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                                    >Lupa password?</Link
                                >
                            </div>
                            <div class="relative">
                                <LockClosedIcon
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    v-model="form.password"
                                    type="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                                />
                            </div>
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="text-sm text-gray-600"
                                    >Ingat saya</span
                                >
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            {{ form.processing ? "Masuk..." : "Masuk" }}
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Belum punya akun?
                        <Link
                            :href="route('register')"
                            class="font-medium text-indigo-600 hover:text-indigo-800 transition-colors"
                            >Daftar sekarang</Link
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
