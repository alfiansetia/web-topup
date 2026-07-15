<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    MagnifyingGlassIcon,
    NoSymbolIcon,
    CheckCircleIcon,
    TrashIcon,
    PencilSquareIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

defineProps({
    users: Object,
    filters: Object,
});

const search = ref("");
const roleFilter = ref("");
const blockedFilter = ref("");

const doSearch = () => {
    router.get(
        route("admin.users.index"),
        {
            search: search.value,
            role: roleFilter.value,
            blocked: blockedFilter.value,
        },
        { preserveState: true },
    );
};

const toggleBlock = (user) => {
    const action = user.is_blocked ? "mengaktifkan" : "memblokir";
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} user?`,
        text: `Yakin ingin ${action} ${user.name}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: user.is_blocked ? "#10b981" : "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(
                route("admin.users.toggle-block", user.id),
                {},
                {
                    preserveScroll: true,
                },
            );
        }
    });
};

const deleteUser = (user) => {
    Swal.fire({
        title: "Hapus user?",
        text: `Yakin ingin menghapus ${user.name}? Tindakan ini tidak dapat dibatalkan.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.users.destroy", user.id), {
                preserveScroll: true,
            });
        }
    });
};

const roleColor = (role) =>
    ({
        admin: "bg-purple-100 text-purple-800",
        user: "bg-gray-100 text-gray-700",
    })[role] || "bg-gray-100 text-gray-700";
</script>

<template>
    <Head title="Kelola Pengguna" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pengguna</h1>
            <span class="text-sm text-gray-500"
                >{{ users.total }} pengguna</span
            >
        </div>

        <!-- Filters -->
        <div class="mb-4">
            <form
                @submit.prevent="doSearch"
                class="flex flex-wrap items-center gap-2"
            >
                <div class="relative">
                    <MagnifyingGlassIcon
                        class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama atau email..."
                        class="w-64 pl-11 pr-4 py-2.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                    />
                </div>
                <select
                    v-model="roleFilter"
                    @change="doSearch"
                    class="py-2.5 px-3.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                >
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <select
                    v-model="blockedFilter"
                    @change="doSearch"
                    class="py-2.5 px-3.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                >
                    <option value="">Semua Status</option>
                    <option value="0">Aktif</option>
                    <option value="1">Diblokir</option>
                </select>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors"
                >
                    <MagnifyingGlassIcon class="w-4 h-4" />
                    Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Pengguna
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Role
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Login Via
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Terdaftar
                            </th>
                            <th
                                class="px-5 py-3 text-gray-500 font-medium text-right"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-gray-50 transition-colors"
                            :class="{ 'bg-red-50/50': user.is_blocked }"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img
                                        v-if="user.google_avatar"
                                        :src="user.google_avatar"
                                        :alt="user.name"
                                        class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200"
                                    />
                                    <div
                                        v-else
                                        class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0"
                                    >
                                        {{
                                            user.name?.charAt(0)?.toUpperCase()
                                        }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ user.name }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="roleColor(user.role)"
                                    class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    v-if="user.google_id"
                                    class="inline-flex items-center gap-1 text-xs text-gray-600"
                                >
                                    <svg
                                        class="w-3.5 h-3.5"
                                        viewBox="0 0 24 24"
                                    >
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
                                    Google
                                </span>
                                <span v-else class="text-xs text-gray-400"
                                    >Email/Password</span
                                >
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    v-if="user.is_blocked"
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800"
                                >
                                    <NoSymbolIcon class="w-3 h-3" />
                                    Diblokir
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800"
                                >
                                    <CheckCircleIcon class="w-3 h-3" />
                                    Aktif
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString("id-ID", {
                                        day: "numeric",
                                        month: "short",
                                        year: "numeric",
                                    })
                                }}
                            </td>
                            <td class="px-5 py-3">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Link
                                        :href="
                                            route('admin.users.edit', user.id)
                                        "
                                        title="Edit"
                                        class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors"
                                    >
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </Link>
                                    <button
                                        v-if="
                                            user.id !==
                                            $page.props.auth.user?.id
                                        "
                                        @click="toggleBlock(user)"
                                        :title="
                                            user.is_blocked
                                                ? 'Aktifkan'
                                                : 'Blokir'
                                        "
                                        :class="[
                                            'p-1.5 rounded-lg transition-colors',
                                            user.is_blocked
                                                ? 'text-green-600 hover:bg-green-50'
                                                : 'text-yellow-600 hover:bg-yellow-50',
                                        ]"
                                    >
                                        <NoSymbolIcon
                                            v-if="!user.is_blocked"
                                            class="w-4 h-4"
                                        />
                                        <CheckCircleIcon
                                            v-else
                                            class="w-4 h-4"
                                        />
                                    </button>
                                    <button
                                        v-if="
                                            user.id !==
                                                $page.props.auth.user?.id &&
                                            user.role !== 'admin'
                                        "
                                        @click="deleteUser(user)"
                                        title="Hapus"
                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="6"
                                class="px-5 py-12 text-center text-gray-400"
                            >
                                Tidak ada pengguna ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <Pagination :links="users.links" />
        </div>
    </AdminLayout>
</template>
