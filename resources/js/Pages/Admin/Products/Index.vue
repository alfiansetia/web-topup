<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    CogIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const deleteProduct = (product) => {
    Swal.fire({
        title: "Hapus Produk?",
        text: `Produk "${product.name}" akan dihapus permanen.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.products.destroy", product.id));
        }
    });
};

const search = ref(props.filters?.search || "");
const categoryFilter = ref(props.filters?.category_id || "");

const doSearch = () => {
    router.get(
        route("admin.products.index"),
        { search: search.value, category_id: categoryFilter.value },
        { preserveState: true },
    );
};
</script>

<template>
    <Head title="Kelola Produk" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Produk</h1>
            <Link
                :href="route('admin.products.create')"
                class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
                <PlusIcon class="w-4 h-4" /> Tambah Produk
            </Link>
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
                        placeholder="Cari produk..."
                        class="w-64 pl-11 pr-4 py-2.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                    />
                </div>
                <select
                    v-model="categoryFilter"
                    @change="doSearch"
                    class="py-2.5 px-3.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                >
                    <option value="">Semua Kategori</option>
                    <option
                        v-for="cat in categories"
                        :key="cat.id"
                        :value="cat.id"
                    >
                        {{ cat.name }}
                    </option>
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
                                Produk
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Kategori
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Varian
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
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
                            v-for="product in products.data"
                            :key="product.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="w-10 h-10 object-cover rounded-lg ring-1 ring-gray-200"
                                    />
                                    <div>
                                        <div class="font-medium">
                                            {{ product.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-400 font-mono"
                                        >
                                            {{ product.slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs"
                                >
                                    {{ product.category?.name }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                {{ product.variants_count }} varian
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="
                                        product.is_active
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'
                                    "
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{
                                        product.is_active ? "Aktif" : "Nonaktif"
                                    }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'admin.variants.index',
                                                product.id,
                                            )
                                        "
                                        class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 hover:bg-green-50 px-2 py-1 rounded text-xs font-medium transition-colors"
                                    >
                                        <CogIcon class="w-3.5 h-3.5" /> Varian
                                    </Link>
                                    <Link
                                        :href="
                                            route(
                                                'admin.products.edit',
                                                product.id,
                                            )
                                        "
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 px-2 py-1 rounded text-xs font-medium transition-colors"
                                    >
                                        <PencilIcon class="w-3.5 h-3.5" /> Edit
                                    </Link>
                                    <button
                                        @click="deleteProduct(product)"
                                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded text-xs font-medium transition-colors"
                                    >
                                        <TrashIcon class="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!products.data?.length">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-gray-400"
                            >
                                Belum ada produk
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :links="products.links" />
    </AdminLayout>
</template>
