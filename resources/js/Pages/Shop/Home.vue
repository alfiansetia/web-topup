<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    PlayCircleIcon,
    PuzzlePieceIcon,
    BriefcaseIcon,
    ShoppingBagIcon,
    ArrowRightIcon,
    BoltIcon,
    MagnifyingGlassIcon,
    StarIcon,
    InboxIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    categories: Array,
    products: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

const categoryIconMap = {
    Streaming: PlayCircleIcon,
    Gaming: PuzzlePieceIcon,
    Produktivitas: BriefcaseIcon,
};

const getCategoryIcon = (name) => categoryIconMap[name] || ShoppingBagIcon;
</script>

<template>
    <ShopLayout>
        <Head title="Akun Premium Harga Terjangkau" />

        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 overflow-hidden"
        >
            <div
                class="absolute -top-24 -right-24 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl"
            ></div>

            <div
                class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24"
            >
                <div class="text-center">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm text-indigo-100 border border-white/10 mb-6"
                    >
                        <BoltIcon class="w-4 h-4 text-yellow-300" />
                        Proses Instan &amp; Garansi 100%
                    </div>
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight"
                    >
                        Akun Premium
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 to-purple-200"
                        >
                            Harga Terjangkau</span
                        >
                    </h1>
                    <p
                        class="mt-5 text-lg text-indigo-100/80 max-w-2xl mx-auto"
                    >
                        Dapatkan akses ke layanan streaming, gaming, dan
                        aplikasi premium favorit Anda dengan harga terbaik.
                        Proses cepat, aman, dan bergaransi.
                    </p>
                    <div
                        class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3"
                    >
                        <Link
                            :href="route('shop.products')"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-indigo-700 font-semibold rounded-xl shadow-lg shadow-indigo-900/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200"
                        >
                            <ShoppingBagIcon class="w-5 h-5" />
                            Lihat Semua Produk
                        </Link>
                        <a
                            href="#kategori"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-200"
                        >
                            Jelajahi Kategori
                            <ArrowRightIcon class="w-4 h-4" />
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                        <div>
                            <div class="text-2xl font-bold text-white">
                                1000+
                            </div>
                            <div class="text-sm text-indigo-200">Pesanan</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">99%</div>
                            <div class="text-sm text-indigo-200">Kepuasan</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">
                                24/7
                            </div>
                            <div class="text-sm text-indigo-200">Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section
            id="kategori"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
        >
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Kategori Produk
                </h2>
                <p class="mt-2 text-gray-500">
                    Pilih kategori yang Anda butuhkan
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('shop.category', cat.slug)"
                    class="group relative bg-white rounded-2xl border border-gray-100 p-6 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-50 transition-all duration-300"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                        >
                            <component
                                :is="getCategoryIcon(cat.name)"
                                class="w-7 h-7 text-indigo-600"
                            />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3
                                class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors"
                            >
                                {{ cat.name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                {{
                                    cat.description ||
                                    "Koleksi akun premium terbaik"
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-400"
                            >{{ cat.products_count || 0 }} produk</span
                        >
                        <ArrowRightIcon
                            class="w-5 h-5 text-gray-300 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"
                        />
                    </div>
                </Link>
            </div>
        </section>

        <!-- Featured Products (4 only) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Produk Pilihan
                    </h2>
                    <p class="mt-1 text-gray-500">
                        Produk terlaris kami minggu ini
                    </p>
                </div>
                <Link
                    :href="route('shop.products')"
                    class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors"
                >
                    Lihat Semua
                    <ArrowRightIcon class="w-4 h-4" />
                </Link>
            </div>

            <div v-if="products.length === 0" class="text-center py-16">
                <InboxIcon class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <p class="text-gray-500">Belum ada produk tersedia.</p>
            </div>

            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
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
                        <div class="absolute top-3 left-3">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-yellow-50 backdrop-blur-sm text-yellow-700 text-xs font-medium rounded-full shadow-sm"
                            >
                                <StarIcon
                                    class="w-3 h-3 text-yellow-500 fill-yellow-400"
                                />
                                Populer
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

            <!-- Mobile CTA -->
            <div class="mt-8 text-center sm:hidden">
                <Link
                    :href="route('shop.products')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors"
                >
                    Lihat Semua Produk
                    <ArrowRightIcon class="w-4 h-4" />
                </Link>
            </div>
        </section>

        <!-- Track Order CTA -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div
                class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 sm:p-10 text-center"
            >
                <MagnifyingGlassIcon
                    class="w-10 h-10 mx-auto text-white/80 mb-3"
                />
                <h3 class="text-2xl font-bold text-white">
                    Sudah Pernah Memesan?
                </h3>
                <p class="mt-2 text-indigo-100">
                    Lacak status pesanan Anda dengan mudah
                </p>
                <Link
                    :href="route('shop.track')"
                    class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-700 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200"
                >
                    <MagnifyingGlassIcon class="w-5 h-5" />
                    Lacak Pesanan
                </Link>
            </div>
        </section>
    </ShopLayout>
</template>
