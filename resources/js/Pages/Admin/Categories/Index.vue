<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Swal from "sweetalert2";

defineProps({
    categories: Object,
    filters: Object,
});

const deleteCategory = (cat) => {
    Swal.fire({
        title: "Hapus Kategori?",
        text: `Kategori "${cat.name}" akan dihapus permanen.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.categories.destroy", cat.id));
        }
    });
};

const search = ref("");
const doSearch = () => {
    router.get(
        route("admin.categories.index"),
        { search: search.value },
        { preserveState: true },
    );
};
</script>

<template>
    <Head title="Kelola Kategori" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kategori</h1>
            <Link
                :href="route('admin.categories.create')"
                class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
                <PlusIcon class="w-4 h-4" /> Tambah Kategori
            </Link>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <form @submit.prevent="doSearch" class="flex items-center gap-2">
                <div class="relative max-w-sm">
                    <MagnifyingGlassIcon
                        class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari kategori..."
                        class="w-full pl-11 pr-4 py-2.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                    />
                </div>
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
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Nama
                        </th>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Slug
                        </th>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Gambar
                        </th>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Produk
                        </th>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Status
                        </th>
                        <th class="px-5 py-3 text-gray-500 font-medium">
                            Urutan
                        </th>
                        <th
                            class="px-5 py-3 text-gray-500 font-medium text-right"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr
                        v-for="cat in categories.data"
                        :key="cat.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-5 py-3 font-medium">{{ cat.name }}</td>
                        <td class="px-5 py-3 text-gray-500 font-mono text-xs">
                            {{ cat.slug }}
                        </td>
                        <td class="px-5 py-3">
                            <img
                                :src="cat.image_url"
                                :alt="cat.name"
                                class="w-10 h-10 object-cover rounded-lg ring-1 ring-gray-200"
                            />
                        </td>
                        <td class="px-5 py-3">
                            {{ cat.products_count }} produk
                        </td>
                        <td class="px-5 py-3">
                            <span
                                :class="
                                    cat.is_active
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'
                                "
                                class="px-2 py-1 rounded-full text-xs font-medium"
                            >
                                {{ cat.is_active ? "Aktif" : "Nonaktif" }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ cat.sort_order }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <Link
                                    :href="
                                        route('admin.categories.edit', cat.id)
                                    "
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-2 py-1 rounded text-xs font-medium transition-colors"
                                >
                                    <PencilIcon class="w-3.5 h-3.5" /> Edit
                                </Link>
                                <button
                                    @click="deleteCategory(cat)"
                                    class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded text-xs font-medium transition-colors"
                                >
                                    <TrashIcon class="w-3.5 h-3.5" /> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!categories.data?.length">
                        <td
                            colspan="7"
                            class="px-5 py-8 text-center text-gray-400"
                        >
                            Belum ada kategori
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="categories.links" />
    </AdminLayout>
</template>
