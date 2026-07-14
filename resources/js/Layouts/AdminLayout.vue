<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    HomeIcon,
    FolderIcon,
    ShoppingBagIcon,
    ClipboardDocumentListIcon,
    Bars3Icon,
    ArrowLeftStartOnRectangleIcon,
    CheckCircleIcon,
    XCircleIcon,
} from "@heroicons/vue/24/outline";

const page = usePage();
const flash = computed(() => page.props.flash);
const user = computed(() => page.props.auth?.user);

const sidebarOpen = ref(false);
const sidebarCollapsed = ref(false);
const currentRoute = computed(() => route().current());

const navItems = [
    { name: "Dashboard", route: "admin.dashboard", icon: HomeIcon },
    { name: "Kategori", route: "admin.categories.index", icon: FolderIcon },
    { name: "Produk", route: "admin.products.index", icon: ShoppingBagIcon },
    {
        name: "Pesanan",
        route: "admin.orders.index",
        icon: ClipboardDocumentListIcon,
    },
];

const isActive = (routeName) => {
    return (
        currentRoute.value === routeName ||
        currentRoute.value?.startsWith(routeName.replace(".index", "."))
    );
};
</script>

<template>
    <div class="min-h-screen flex bg-gray-100">
        <!-- Sidebar Overlay (mobile) -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 lg:hidden"
            @click="sidebarOpen = false"
        >
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 bg-gray-900 text-white transform transition-all duration-200 lg:translate-x-0 lg:static lg:z-auto',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'lg:w-16' : 'w-64',
            ]"
        >
            <!-- Logo -->
            <div
                class="flex items-center gap-3 px-6 py-5 border-b border-gray-800"
            >
                <div
                    class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                >
                    T
                </div>
                <span
                    v-if="!sidebarCollapsed"
                    class="text-lg font-bold tracking-tight"
                    >TopUp Store</span
                >
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-3 space-y-1">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    @click="sidebarOpen = false"
                    :title="sidebarCollapsed ? item.name : undefined"
                    :class="[
                        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                        sidebarCollapsed ? 'justify-center' : '',
                        isActive(item.route)
                            ? 'bg-indigo-600 text-white'
                            : 'text-gray-300 hover:bg-gray-800 hover:text-white',
                    ]"
                >
                    <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    <span v-if="!sidebarCollapsed">{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User Info -->
            <div
                class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                    >
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div v-if="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">
                            {{ user?.name }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ user?.email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b sticky top-0 z-30">
                <div
                    class="flex items-center justify-between px-4 py-3 lg:px-6"
                >
                    <button
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100"
                    >
                        <Bars3Icon class="w-5 h-5" />
                    </button>
                    <button
                        @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden lg:block p-2 -ml-2 rounded-lg hover:bg-gray-100"
                    >
                        <Bars3Icon class="w-5 h-5" />
                    </button>
                    <div></div>
                    <Link
                        :href="route('admin.dashboard')"
                        class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700"
                    >
                        <ArrowLeftStartOnRectangleIcon class="w-4 h-4" />
                        Kembali ke Situs
                    </Link>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="flash?.success" class="mx-4 lg:mx-6 mt-4">
                <div
                    class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center justify-between"
                >
                    <span class="flex items-center gap-2">
                        <CheckCircleIcon class="w-5 h-5 text-green-500" />
                        {{ flash.success }}
                    </span>
                    <button
                        @click="page.props.flash.success = null"
                        class="text-green-500 hover:text-green-700"
                    >
                        &times;
                    </button>
                </div>
            </div>
            <div v-if="flash?.error" class="mx-4 lg:mx-6 mt-4">
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center justify-between"
                >
                    <span class="flex items-center gap-2">
                        <XCircleIcon class="w-5 h-5 text-red-500" />
                        {{ flash.error }}
                    </span>
                    <button
                        @click="page.props.flash.error = null"
                        class="text-red-500 hover:text-red-700"
                    >
                        &times;
                    </button>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
