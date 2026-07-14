<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import {
    RocketLaunchIcon,
    EnvelopeIcon,
    ChatBubbleLeftRightIcon,
    ArrowRightStartOnRectangleIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();
const user = computed(() => page.props.auth?.user);
const appName = computed(() => page.props.appName || "TopUp Store");
const mobileMenuOpen = ref(false);
const userDropdownOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <nav
            class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                                />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{
                            appName
                        }}</span>
                    </Link>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            href="/"
                            class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all"
                        >
                            Beranda
                        </Link>
                        <Link
                            :href="route('shop.products')"
                            class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all"
                        >
                            Produk
                        </Link>
                        <Link
                            :href="route('shop.track')"
                            class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all"
                        >
                            Lacak Pesanan
                        </Link>
                    </div>

                    <!-- Right: Auth -->
                    <div class="hidden md:flex items-center gap-3">
                        <template v-if="user">
                            <div class="relative">
                                <button
                                    @click="
                                        userDropdownOpen = !userDropdownOpen
                                    "
                                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all"
                                >
                                    <div
                                        class="w-7 h-7 bg-indigo-100 rounded-full flex items-center justify-center"
                                    >
                                        <span
                                            class="text-xs font-bold text-indigo-600"
                                            >{{
                                                user.name
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                            }}</span
                                        >
                                    </div>
                                    <span class="hidden lg:inline">{{
                                        user.name
                                    }}</span>
                                    <svg
                                        class="w-4 h-4 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"
                                        />
                                    </svg>
                                </button>
                                <div
                                    v-if="userDropdownOpen"
                                    @click="userDropdownOpen = false"
                                    class="fixed inset-0 z-40"
                                ></div>
                                <div
                                    v-if="userDropdownOpen"
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                                >
                                    <Link
                                        :href="route('dashboard.index')"
                                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"
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
                                                d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"
                                            />
                                        </svg>
                                        Dashboard
                                    </Link>
                                    <hr class="my-1 border-gray-100" />
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                    >
                                        <ArrowRightStartOnRectangleIcon
                                            class="w-4 h-4"
                                        />
                                        Keluar
                                    </Link>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all"
                            >
                                Masuk
                            </Link>
                            <Link
                                :href="route('register')"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-all"
                            >
                                Daftar
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100"
                    >
                        <svg
                            v-if="!mobileMenuOpen"
                            class="w-5 h-5 text-gray-700"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-5 h-5 text-gray-700"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18 18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Mobile menu -->
                <div
                    v-if="mobileMenuOpen"
                    class="md:hidden border-t border-gray-100 py-3 space-y-1"
                >
                    <Link
                        href="/"
                        class="block px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-indigo-50"
                        >Beranda</Link
                    >
                    <Link
                        :href="route('shop.products')"
                        class="block px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-indigo-50"
                        >Produk</Link
                    >
                    <Link
                        :href="route('shop.track')"
                        class="block px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-indigo-50"
                        >Lacak Pesanan</Link
                    >
                    <template v-if="user">
                        <Link
                            :href="route('dashboard.index')"
                            class="block px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-indigo-50"
                            >Dashboard</Link
                        >
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50"
                            >Keluar</Link
                        >
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="block px-3 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-indigo-50"
                            >Masuk</Link
                        >
                        <Link
                            :href="route('register')"
                            class="block px-3 py-2 text-sm font-medium text-indigo-600 rounded-lg hover:bg-indigo-50"
                            >Daftar</Link
                        >
                    </template>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center"
                            >
                                <svg
                                    class="w-4 h-4 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                                    />
                                </svg>
                            </div>
                            <span class="font-bold text-gray-900">{{
                                appName
                            }}</span>
                        </div>
                        <p class="text-sm text-gray-500">
                            Akun premium harga terjangkau dengan proses instan
                            dan garansi 100%.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Navigasi
                        </h4>
                        <div class="space-y-2">
                            <Link
                                href="/"
                                class="block text-sm text-gray-500 hover:text-indigo-600"
                                >Beranda</Link
                            >
                            <Link
                                :href="route('shop.track')"
                                class="block text-sm text-gray-500 hover:text-indigo-600"
                                >Lacak Pesanan</Link
                            >
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">
                            Bantuan
                        </h4>
                        <div class="space-y-2">
                            <span
                                class="flex items-center gap-2 text-sm text-gray-500"
                                ><EnvelopeIcon class="w-4 h-4 text-gray-400" />
                                support@topupstore.com</span
                            >
                            <span
                                class="flex items-center gap-2 text-sm text-gray-500"
                                ><ChatBubbleLeftRightIcon
                                    class="w-4 h-4 text-gray-400"
                                />
                                Chat WhatsApp</span
                            >
                        </div>
                    </div>
                </div>
                <div
                    class="border-t border-gray-100 mt-8 pt-6 text-center text-sm text-gray-400"
                >
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. All
                    rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
