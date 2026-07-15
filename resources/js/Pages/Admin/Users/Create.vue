<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    ArrowLeftIcon,
    UserIcon,
    ShieldCheckIcon,
    KeyIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "user",
});

const showPassword = ref(false);

const submit = () => {
    form.post(route("admin.users.store"));
};
</script>

<template>
    <Head title="Tambah Pengguna" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.users.index')"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1"
            >
                <ArrowLeftIcon class="w-4 h-4" />
                Kembali ke Pengguna
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">
                Tambah Pengguna
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Buat akun pengguna baru secara manual
            </p>
        </div>

        <div class="max-w-xl">
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-6 space-y-5"
            >
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <UserIcon class="w-4 h-4 inline text-gray-400" />
                        Nama <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        autofocus
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="contoh@email.com"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <KeyIcon class="w-4 h-4 inline text-gray-400" />
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Minimal 8 karakter"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 pr-20 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            {{ showPassword ? "Sembunyikan" : "Tampilkan" }}
                        </button>
                    </div>
                    <p
                        v-if="form.errors.password"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Password
                        <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Ulangi password"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <ShieldCheckIcon class="w-4 h-4 inline text-gray-400" />
                        Role
                    </label>
                    <select
                        v-model="form.role"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <p
                        v-if="form.errors.role"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.role }}
                    </p>
                </div>

                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700">
                        User yang dibuat secara manual akan langsung aktif
                        (email terverifikasi) dan bisa login dengan email &amp;
                        password yang diatur di sini.
                    </p>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl disabled:opacity-50 transition-all"
                    >
                        {{ form.processing ? "Menyimpan..." : "Buat Pengguna" }}
                    </button>
                    <Link
                        :href="route('admin.users.index')"
                        class="px-5 py-2.5 text-sm text-gray-600 hover:text-gray-800 transition-colors"
                    >
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
