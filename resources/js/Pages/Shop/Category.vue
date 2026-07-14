<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ArrowRightIcon, InboxIcon, HomeIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    category: Object,
    products: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <ShopLayout>
        <Head :title="category.name + ' - TopUp Store'" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <Link href="/" class="hover:text-indigo-600 transition-colors"
                    >Beranda</Link
                >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m8.25 4.5 7.5 7.5-7.5 7.5"
                    />
                </svg>
                <span class="text-gray-900 font-medium">{{
                    category.name
                }}</span>
            </nav>

            <!-- Category Header -->
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8"
            >
                <h1 class="text-3xl font-bold text-white">
                    {{ category.name }}
                </h1>
                <p v-if="category.description" class="mt-2 text-indigo-100">
                    {{ category.description }}
                </p>
                <p class="mt-1 text-sm text-indigo-200">
                    {{ products.length }} produk tersedia
                </p>
            </div>

            <!-- Products Grid -->
            <div v-if="products.length === 0" class="text-center py-16">
                <InboxIcon class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <p class="text-gray-500">Belum ada produk di kategori ini.</p>
                <Link
                    href="/"
                    class="mt-4 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800"
                    >← Kembali ke Beranda</Link
                >
            </div>

            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
            >
                <Link
                    v-for="product in products"
                    :key="product.id"
                    :href="route('shop.product', product.slug)"
                    class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-50 transition-all duration-300"
                >
                    <div
                        class="relative aspect-[4/3] bg-gray-50 overflow-hidden"
                    >
                        <img
                            :src="product.image_url"
                            :alt="product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <div class="p-4">
                        <h3
                            class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1"
                        >
                            {{ product.name }}
                        </h3>
                        <p
                            v-if="product.description"
                            class="mt-1 text-sm text-gray-500 line-clamp-2"
                        >
                            {{ product.description }}
                        </p>
                        <div class="mt-3 flex items-end justify-between">
                            <div>
                                <span class="text-xs text-gray-400"
                                    >Mulai dari</span
                                >
                                <div class="text-lg font-bold text-indigo-600">
                                    {{
                                        product.min_price
                                            ? formatPrice(product.min_price)
                                            : "-"
                                    }}
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 group-hover:translate-x-0.5 transition-transform"
                            >
                                Detail
                                <svg
                                    class="w-3.5 h-3.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                                    />
                                </svg>
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </ShopLayout>
</template>
