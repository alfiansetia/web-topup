<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ShoppingBagIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowRightIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    user: Object,
    recentOrders: Array,
    stats: Object,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

const statusConfig = {
    pending: {
        label: "Menunggu",
        color: "text-yellow-600 bg-yellow-50",
        icon: ClockIcon,
    },
    paid: {
        label: "Dibayar",
        color: "text-blue-600 bg-blue-50",
        icon: CheckCircleIcon,
    },
    completed: {
        label: "Selesai",
        color: "text-green-600 bg-green-50",
        icon: CheckCircleIcon,
    },
    cancelled: {
        label: "Dibatalkan",
        color: "text-red-600 bg-red-50",
        icon: XCircleIcon,
    },
    refunded: {
        label: "Refund",
        color: "text-gray-600 bg-gray-50",
        icon: XCircleIcon,
    },
};

const getStatus = (status) => statusConfig[status] || statusConfig.pending;
</script>

<template>
    <DashboardLayout>
        <Head title="Dashboard" />

        <!-- Admin Alert -->
        <div
            v-if="user?.role === 'admin'"
            class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl flex items-center justify-between gap-4"
        >
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0"
                >
                    <ExclamationTriangleIcon class="w-5 h-5 text-amber-600" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-amber-900">
                        Anda masuk sebagai Admin
                    </p>
                    <p class="text-xs text-amber-700">
                        Anda berada di dashboard user. Akses panel admin untuk
                        mengelola toko.
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center"
                    >
                        <ShoppingBagIcon class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Pesanan</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ stats.total }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center"
                    >
                        <ClockIcon class="w-5 h-5 text-yellow-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Menunggu</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ stats.pending }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center"
                    >
                        <CheckCircleIcon class="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Selesai</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ stats.completed }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl border border-gray-100">
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-gray-100"
            >
                <h2 class="font-semibold text-gray-900">Pesanan Terbaru</h2>
                <Link
                    :href="route('dashboard.orders')"
                    class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    Lihat Semua
                    <ArrowRightIcon class="w-4 h-4" />
                </Link>
            </div>

            <div v-if="recentOrders.length === 0" class="p-10 text-center">
                <ShoppingBagIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                <p class="text-gray-500">Belum ada pesanan.</p>
                <Link
                    :href="route('shop.products')"
                    class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100"
                >
                    Mulai Belanja
                    <ArrowRightIcon class="w-4 h-4" />
                </Link>
            </div>

            <div v-else class="divide-y divide-gray-50">
                <Link
                    v-for="order in recentOrders"
                    :key="order.id"
                    :href="route('shop.order', order.order_number)"
                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex items-center gap-4 min-w-0">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                            :class="getStatus(order.status).color"
                        >
                            <component
                                :is="getStatus(order.status).icon"
                                class="w-5 h-5"
                            />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ order.order_number }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <p class="text-sm font-bold text-gray-900">
                            {{ formatPrice(order.total_amount) }}
                        </p>
                        <span
                            class="inline-block mt-0.5 px-2 py-0.5 text-[10px] font-semibold rounded-full"
                            :class="getStatus(order.status).color"
                        >
                            {{ getStatus(order.status).label }}
                        </span>
                    </div>
                </Link>
            </div>
        </div>
    </DashboardLayout>
</template>
