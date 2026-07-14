<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import {
    MagnifyingGlassIcon,
    ArrowRightIcon,
    InboxIcon,
    FunnelIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const selectedCategory = ref(props.filters?.category || "");

let debounceTimer = null;
const debounce = (fn, delay = 400) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fn, delay);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

const doSearch = () => {
    debounce(() => {
        router.get(
            route("shop.products"),
            {
                search: search.value,
                category: selectedCategory.value,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    });
};

watch(search, () => doSearch());

const filterByCategory = (slug) => {
    selectedCategory.value = slug;
    router.get(
        route("shop.products"),
        {
            search: search.value,
            category: slug,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilters = () => {
    search.value = "";
    selectedCategory.value = "";
    router.get(route("shop.products"), {}, { replace: true });
};

const hasActiveFilters = () => {
    return search.value || selectedCategory.value;
};
</script>

<template>
    <ShopLayout>
        <Head title="Semua Produk - TopUp Store" />

        <!-- Header -->
        <section
            class="bg-gradient-to-r from-indigo-600 to-purple-600 py-12 sm:py-16"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex items-center gap-2 text-sm text-indigo-200 mb-4"
                >
                    <Link href="/" class="hover:text-white transition-colors"
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
                    <span class="text-white font-medium">Semua Produk</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white">
                    Semua Produk
                </h1>
                <p class="mt-2 text-indigo-100">
                    Temukan akun premium yang Anda butuhkan
                </p>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search & Filter Bar -->
            <div
                class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 shadow-sm mb-8"
            >
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <MagnifyingGlassIcon
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari produk..."
                            class="w-full pl-11 pr-10 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none focus:bg-white transition-all"
                        />
                        <button
                            v-if="search"
                            @click="search = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <XMarkIcon class="w-4 h-4" />
                        </button>
                    </div>

                    <!-- Category Filters -->
                    <div
                        class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0"
                    >
                        <FunnelIcon
                            class="w-4 h-4 text-gray-400 flex-shrink-0"
                        />
                        <button
                            @click="filterByCategory('')"
                            class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-lg transition-all"
                            :class="
                                !selectedCategory
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            "
                        >
                            Semua
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat.id"
                            @click="filterByCategory(cat.slug)"
                            class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-lg transition-all"
                            :class="
                                selectedCategory === cat.slug
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            "
                        >
                            {{ cat.name }}
                        </button>
                    </div>
                </div>

                <!-- Active filter indicator -->
                <div
                    v-if="hasActiveFilters()"
                    class="mt-3 flex items-center gap-2"
                >
                    <span class="text-xs text-gray-500">Filter aktif:</span>
                    <span
                        v-if="search"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs rounded-full"
                    >
                        "{{ search }}"
                        <button
                            @click="search = ''"
                            class="hover:text-indigo-800"
                        >
                            <XMarkIcon class="w-3 h-3" />
                        </button>
                    </span>
                    <span
                        v-if="selectedCategory"
                        class="inline-flex items-center gap-1 px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs rounded-full"
                    >
                        {{
                            categories.find((c) => c.slug === selectedCategory)
                                ?.name
                        }}
                        <button
                            @click="filterByCategory('')"
                            class="hover:text-indigo-800"
                        >
                            <XMarkIcon class="w-3 h-3" />
                        </button>
                    </span>
                    <button
                        @click="clearFilters()"
                        class="text-xs text-gray-400 hover:text-red-500 transition-colors ml-1"
                    >
                        Hapus semua
                    </button>
                </div>
            </div>

            <!-- Results count -->
            <p class="text-sm text-gray-500 mb-5">
                Menampilkan {{ products.data.length }} dari
                {{ products.total }} produk
            </p>

            <!-- Products Grid -->
            <div v-if="products.data.length === 0" class="text-center py-20">
                <MagnifyingGlassIcon
                    class="w-16 h-16 mx-auto text-gray-300 mb-4"
                />
                <p class="text-lg font-medium text-gray-900">
                    Produk tidak ditemukan
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Coba kata kunci lain atau hapus filter
                </p>
                <button
                    v-if="hasActiveFilters()"
                    @click="clearFilters()"
                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors"
                >
                    <XMarkIcon class="w-4 h-4" />
                    Hapus Filter
                </button>
            </div>

            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
            >
                <Link
                    v-for="product in products.data"
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
                        <div class="absolute top-3 left-3">
                            <span
                                class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-medium rounded-full shadow-sm"
                            >
                                {{ product.category?.name }}
                            </span>
                        </div>
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
                                <ArrowRightIcon class="w-3.5 h-3.5" />
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div
                v-if="products.last_page > 1"
                class="mt-10 flex items-center justify-center gap-2"
            >
                <Link
                    v-for="(link, i) in products.links"
                    :key="i"
                    :href="link.url || ''"
                    class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-sm font-medium rounded-lg transition-all"
                    :class="[
                        link.active
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : link.url
                              ? 'bg-white text-gray-700 border border-gray-200 hover:border-indigo-200 hover:text-indigo-600'
                              : 'bg-gray-50 text-gray-300 cursor-default',
                    ]"
                    :preserve-scroll="true"
                    v-html="link.label"
                />
            </div>
        </div>
    </ShopLayout>
</template>
