<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import {
    LockClosedIcon,
    CheckCircleIcon,
    ShieldCheckIcon,
    RocketLaunchIcon,
} from "@heroicons/vue/24/outline";

const form = useForm({
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.set.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Atur Password - TopUp Store" />

    <div
        class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link
                    :href="route('shop.home')"
                    class="inline-flex items-center gap-2 text-2xl font-bold text-gray-900"
                >
                    <RocketLaunchIcon class="w-7 h-7 text-indigo-600" />
                    TopUp Store
                </Link>
            </div>

            <!-- Card -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8"
            >
                <!-- Icon -->
                <div class="flex justify-center mb-5">
                    <div
                        class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center"
                    >
                        <ShieldCheckIcon class="w-8 h-8 text-indigo-600" />
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-xl font-bold text-gray-900 text-center mb-2">
                    Atur Password Anda
                </h1>
                <p class="text-sm text-gray-500 text-center mb-6">
                    Anda login menggunakan Google. Silakan buat password untuk
                    keamanan akun Anda agar bisa login tanpa Google juga.
                </p>

                <!-- Success -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2 text-sm text-green-700"
                >
                    <CheckCircleIcon class="w-5 h-5" />
                    {{ $page.props.flash.success }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Password Baru
                        </label>
                        <div class="relative">
                            <LockClosedIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="form.password"
                                type="password"
                                required
                                placeholder="Minimal 8 karakter"
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
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <LockClosedIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                placeholder="Ulangi password"
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        {{
                            form.processing ? "Menyimpan..." : "Simpan Password"
                        }}
                    </button>
                </form>

                <!-- Skip link -->
                <div class="mt-4 text-center">
                    <Link
                        :href="route('shop.home')"
                        class="text-sm text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        Lewati, nanti saja
                    </Link>
                </div>
            </div>

            <p class="text-xs text-gray-400 text-center mt-6">
                Password digunakan untuk login tanpa Google
            </p>
        </div>
    </div>
</template>
