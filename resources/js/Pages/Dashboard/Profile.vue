<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import {
    UserCircleIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    KeyIcon,
    EnvelopeIcon,
    AtSymbolIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const profileForm = useForm({
    name: user.value?.name || "",
    email: user.value?.email || "",
    telegram_id: user.value?.telegram_id || "",
});

const passwordForm = useForm({
    password: "",
    password_confirmation: "",
});

const profileSuccess = ref(false);
const passwordSuccess = ref(false);

const updateProfile = () => {
    profileForm.patch(route("dashboard.profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            profileSuccess.value = true;
            setTimeout(() => (profileSuccess.value = false), 3000);
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route("dashboard.password.update"), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            passwordSuccess.value = true;
            setTimeout(() => (passwordSuccess.value = false), 3000);
        },
    });
};
</script>

<template>
    <DashboardLayout>
        <Head title="Profil Saya" />

        <div class="space-y-6">
            <!-- Profile Info Card -->
            <div
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center"
                        >
                            <UserCircleIcon class="w-9 h-9 text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">
                                {{ user?.name }}
                            </h2>
                            <p class="text-indigo-100 text-sm">
                                {{ user?.email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-1">
                    Informasi Profil
                </h3>
                <p class="text-sm text-gray-500 mb-6">
                    Perbarui informasi akun dan Telegram ID Anda
                </p>

                <!-- Success message -->
                <div
                    v-if="profileSuccess"
                    class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2 text-sm text-green-700"
                >
                    <CheckCircleIcon class="w-5 h-5" />
                    Profil berhasil diperbarui!
                </div>

                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <UserCircleIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="profileForm.name"
                                type="text"
                                required
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                        <p
                            v-if="profileForm.errors.name"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ profileForm.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Email
                        </label>
                        <div class="relative">
                            <EnvelopeIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                :value="profileForm.email"
                                type="email"
                                disabled
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-500 placeholder-gray-400 shadow-sm cursor-not-allowed focus:outline-none"
                            />
                        </div>
                        <p
                            v-if="profileForm.errors.email"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ profileForm.errors.email }}
                        </p>
                        <p class="mt-1.5 text-xs text-gray-400">
                            Email tidak dapat diubah
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Telegram ID
                            <span class="text-gray-400 font-normal"
                                >(opsional)</span
                            >
                        </label>
                        <div class="relative">
                            <AtSymbolIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="profileForm.telegram_id"
                                type="text"
                                placeholder="123456789"
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">
                            Kirim pesan
                            <span class="font-mono bg-gray-100 px-1 rounded"
                                >/start</span
                            >
                            ke bot Telegram kami untuk mendapatkan ID Anda
                        </p>
                        <p
                            v-if="profileForm.errors.telegram_id"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ profileForm.errors.telegram_id }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            {{
                                profileForm.processing
                                    ? "Menyimpan..."
                                    : "Simpan Perubahan"
                            }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-1">Ubah Password</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Pastikan akun Anda menggunakan password yang kuat
                </p>

                <!-- Success message -->
                <div
                    v-if="passwordSuccess"
                    class="mb-4 p-3 bg-green-50 border border-green-100 rounded-xl flex items-center gap-2 text-sm text-green-700"
                >
                    <CheckCircleIcon class="w-5 h-5" />
                    Password berhasil diubah!
                </div>

                <form @submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Password Baru
                        </label>
                        <div class="relative">
                            <KeyIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                required
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                        <p
                            v-if="passwordForm.errors.password"
                            class="mt-1 text-xs text-red-500"
                        >
                            {{ passwordForm.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <KeyIcon
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            />
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                required
                                class="block w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            {{
                                passwordForm.processing
                                    ? "Menyimpan..."
                                    : "Ubah Password"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
