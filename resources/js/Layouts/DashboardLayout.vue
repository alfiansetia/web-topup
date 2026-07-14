<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import {
    HomeIcon,
    ShoppingBagIcon,
    UserCircleIcon,
    ExclamationTriangleIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const navItems = [
    {
        label: "Dashboard",
        href: route("dashboard.index"),
        routeName: "dashboard.index",
        icon: HomeIcon,
    },
    {
        label: "Pesanan",
        href: route("dashboard.orders"),
        routeName: "dashboard.orders",
        icon: ShoppingBagIcon,
    },
    {
        label: "Profil",
        href: route("dashboard.profile"),
        routeName: "dashboard.profile",
        icon: UserCircleIcon,
    },
];

const isActive = (routeName) => {
    return route().current(routeName);
};
</script>

<template>
    <ShopLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Dashboard Header -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Dashboard
                </h1>
                <p class="mt-1 text-gray-500">
                    Selamat datang kembali, {{ user?.name }}!
                </p>
            </div>

            <!-- Admin Alert -->
            <div
                v-if="user?.role === 'admin'"
                class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0"
                    >
                        <ExclamationTriangleIcon
                            class="w-5 h-5 text-amber-600"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-amber-900">
                            Anda masuk sebagai Admin
                        </p>
                        <p class="text-xs text-amber-700">
                            Anda berada di dashboard user. Akses panel admin
                            untuk mengelola toko.
                        </p>
                    </div>
                </div>
                <Link
                    :href="route('admin.dashboard')"
                    class="flex-shrink-0 px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-amber-700 transition-all"
                >
                    Ke Admin Panel
                </Link>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Nav -->
                <aside class="lg:w-56 flex-shrink-0">
                    <nav
                        class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0"
                    >
                        <Link
                            v-for="item in navItems"
                            :key="item.routeName"
                            :href="item.href"
                            class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-medium whitespace-nowrap transition-all"
                            :class="
                                isActive(item.routeName)
                                    ? 'bg-indigo-50 text-indigo-700 shadow-sm'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                            "
                        >
                            <component :is="item.icon" class="w-5 h-5" />
                            {{ item.label }}
                        </Link>
                    </nav>
                </aside>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">
                    <slot />
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
