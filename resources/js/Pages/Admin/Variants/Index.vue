<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import {
    PlusIcon,
    ArchiveBoxIcon,
    PencilIcon,
    TrashIcon,
    ArrowLeftIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

defineProps({ product: Object });

const deleteVariant = (v) => {
    Swal.fire({
        title: "Hapus Varian?",
        text: `Varian "${v.name}" akan dihapus permanen.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("admin.variants.destroy", [product.id, v.id]));
        }
    });
};

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
</script>

<template>
    <Head :title="`Varian - ${product.name}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.products.index')"
                class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800"
            >
                <ArrowLeftIcon class="w-4 h-4" /> Kembali ke Produk
            </Link>
            <div class="flex items-center justify-between mt-2">
                <h1 class="text-2xl font-bold text-gray-900">
                    Varian: {{ product.name }}
                </h1>
                <Link
                    :href="route('admin.variants.create', product.id)"
                    class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    <PlusIcon class="w-4 h-4" /> Tambah Varian
                </Link>
            </div>
        </div>

        <!-- Variant Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="v in product.variants"
                :key="v.id"
                class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow"
            >
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">
                            {{ v.name }}
                        </h3>
                        <p
                            v-if="v.description"
                            class="text-xs text-gray-500 mt-1"
                        >
                            {{ v.description }}
                        </p>
                    </div>
                    <span
                        :class="
                            v.is_active
                                ? 'bg-green-100 text-green-700'
                                : 'bg-red-100 text-red-700'
                        "
                        class="px-2 py-0.5 rounded-full text-xs font-medium"
                    >
                        {{ v.is_active ? "Aktif" : "Nonaktif" }}
                    </span>
                </div>

                <div class="mb-3">
                    <div class="flex items-baseline gap-2">
                        <span
                            v-if="v.discount_price"
                            class="text-lg font-bold text-indigo-600"
                            >{{ formatCurrency(v.discount_price) }}</span
                        >
                        <span
                            :class="
                                v.discount_price
                                    ? 'text-sm text-gray-400 line-through'
                                    : 'text-lg font-bold text-gray-900'
                            "
                        >
                            {{ formatCurrency(v.price) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span
                            class="text-sm text-gray-500 flex items-center gap-1"
                        >
                            <ArchiveBoxIcon class="w-4 h-4" /> Stok:
                        </span>
                        <span
                            :class="[
                                'px-2 py-0.5 rounded-full text-xs font-medium',
                                (v.available_count ?? v.stock_count) > 0
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-red-100 text-red-700',
                            ]"
                        >
                            {{ v.available_count ?? v.stock_count }} item
                        </span>
                    </div>
                </div>

                <div class="flex gap-2 pt-3 border-t">
                    <Link
                        :href="
                            route('admin.variants.items', [product.id, v.id])
                        "
                        class="flex-1 text-center inline-flex items-center justify-center gap-1 bg-green-50 text-green-700 hover:bg-green-100 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                    >
                        <ArchiveBoxIcon class="w-4 h-4" /> Kelola Stok
                    </Link>
                    <Link
                        :href="route('admin.variants.edit', [product.id, v.id])"
                        class="flex-1 text-center inline-flex items-center justify-center gap-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                    >
                        <PencilIcon class="w-4 h-4" /> Edit
                    </Link>
                    <button
                        @click="deleteVariant(v)"
                        class="bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div
                v-if="!product.variants?.length"
                class="col-span-full text-center py-12 text-gray-400"
            >
                Belum ada varian. Klik "Tambah Varian" untuk menambahkan.
            </div>
        </div>
    </AdminLayout>
</template>
