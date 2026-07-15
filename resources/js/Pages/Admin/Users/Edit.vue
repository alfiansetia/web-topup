<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    ArrowLeftIcon,
    UserIcon,
    EnvelopeIcon,
    ShieldCheckIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    ChatBubbleLeftRightIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({ user: Object });

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    is_blocked: props.user.is_blocked,
    telegram_id: props.user.telegram_id || "",
});

const submit = () => {
    form.put(route("admin.users.update", props.user.id));
};
</script>

<template>
    <Head :title="`Edit ${user.name}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.users.index')"
                class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1"
            >
                <ArrowLeftIcon class="w-4 h-4" />
                Kembali ke Pengguna
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Pengguna</h1>
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
                        Nama
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
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
                        <EnvelopeIcon class="w-4 h-4 inline text-gray-400" />
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p
                        v-if="form.errors.email"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Telegram Chat ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <ChatBubbleLeftRightIcon
                            class="w-4 h-4 inline text-gray-400"
                        />
                        Telegram Chat ID
                    </label>
                    <input
                        v-model="form.telegram_id"
                        type="text"
                        placeholder="Contoh: 123456789 (opsional)"
                        class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    />
                    <p
                        v-if="form.errors.telegram_id"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.telegram_id }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Kosongkan untuk menghapus koneksi Telegram
                    </p>
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

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status Akun
                    </label>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="form.is_blocked = false"
                            :class="[
                                'flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium transition-all',
                                !form.is_blocked
                                    ? 'bg-green-50 border-green-300 text-green-700 ring-2 ring-green-200'
                                    : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300',
                            ]"
                        >
                            <CheckCircleIcon class="w-4 h-4" />
                            Aktif
                        </button>
                        <button
                            type="button"
                            @click="form.is_blocked = true"
                            :class="[
                                'flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm font-medium transition-all',
                                form.is_blocked
                                    ? 'bg-red-50 border-red-300 text-red-700 ring-2 ring-red-200'
                                    : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300',
                            ]"
                        >
                            <NoSymbolIcon class="w-4 h-4" />
                            Diblokir
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl disabled:opacity-50 transition-all"
                    >
                        {{
                            form.processing
                                ? "Menyimpan..."
                                : "Simpan Perubahan"
                        }}
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
