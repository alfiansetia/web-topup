<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({ product: Object });

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
</script>

<template>
    <Head :title="product.name" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.products.index')"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >← Kembali ke Produk</Link
            >
            <h1 class="text-2xl font-bold text-gray-900 mt-2">
                {{ product.name }}
            </h1>
        </div>

        <!-- Product Info -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">
                    Informasi Produk
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Kategori</dt>
                        <dd>{{ product.category?.name }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Slug</dt>
                        <dd class="font-mono text-xs">{{ product.slug }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Deskripsi</dt>
                        <dd>{{ product.description || "—" }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Cara Pakai</dt>
                        <dd>{{ product.instruction_use || "—" }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Checkout Info</dt>
                        <dd>{{ product.checkout_instruction || "—" }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Status</dt>
                        <dd>
                            <span
                                :class="
                                    product.is_active
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'
                                "
                                class="px-2 py-1 rounded-full text-xs font-medium"
                            >
                                {{ product.is_active ? "Aktif" : "Nonaktif" }}
                            </span>
                        </dd>
                    </div>
                </dl>
                <div v-if="product.features?.length" class="mt-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">
                        Fitur:
                    </h4>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        <li v-for="f in product.features" :key="f">{{ f }}</li>
                    </ul>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Ringkasan</h3>
                <div class="space-y-3">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-indigo-600">
                            {{ product.variants?.length || 0 }}
                        </p>
                        <p class="text-xs text-gray-500">Total Varian</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">
                            {{
                                product.variants?.reduce(
                                    (sum, v) => sum + (v.items_count || 0),
                                    0,
                                )
                            }}
                        </p>
                        <p class="text-xs text-gray-500">Total Stok</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.variants.index', product.id)"
                    class="mt-4 block text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    Kelola Varian →
                </Link>
            </div>
        </div>

        <!-- Variants Preview -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Varian Produk</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Varian
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Harga
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Diskon
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Stok
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="v in product.variants"
                            :key="v.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3 font-medium">{{ v.name }}</td>
                            <td class="px-5 py-3">
                                {{ formatCurrency(v.price) }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    v-if="v.discount_price"
                                    class="text-green-600 font-medium"
                                    >{{
                                        formatCurrency(v.discount_price)
                                    }}</span
                                >
                                <span v-else class="text-gray-400">—</span>
                            </td>
                            <td class="px-5 py-3">{{ v.items_count }} item</td>
                            <td class="px-5 py-3">
                                <span
                                    :class="
                                        v.is_active
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-700'
                                    "
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ v.is_active ? "Aktif" : "Nonaktif" }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!product.variants?.length">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-gray-400"
                            >
                                Belum ada varian
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
