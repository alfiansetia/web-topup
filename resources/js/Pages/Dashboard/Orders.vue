<script setup>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    ShoppingBagIcon,
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowRightIcon,
    MagnifyingGlassIcon,
    InboxIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    orders: Object,
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
        month: "long",
        year: "numeric",
    });
};

const statusConfig = {
    pending: {
        label: "Menunggu Pembayaran",
        color: "text-yellow-700 bg-yellow-50 border-yellow-200",
        dot: "bg-yellow-500",
    },
    paid: {
        label: "Sudah Dibayar",
        color: "text-blue-700 bg-blue-50 border-blue-200",
        dot: "bg-blue-500",
    },
    completed: {
        label: "Selesai",
        color: "text-green-700 bg-green-50 border-green-200",
        dot: "bg-green-500",
    },
    cancelled: {
        label: "Dibatalkan",
        color: "text-red-700 bg-red-50 border-red-200",
        dot: "bg-red-500",
    },
    refunded: {
        label: "Refund",
        color: "text-gray-700 bg-gray-50 border-gray-200",
        dot: "bg-gray-500",
    },
};

const getStatus = (status) => statusConfig[status] || statusConfig.pending;
</script>

<template>
    <DashboardLayout>
        <Head title="Pesanan Saya - TopUp Store" />

        <div class="space-y-4">
            <!-- Order Cards -->
            <div
                v-if="orders.data.length === 0"
                class="bg-white rounded-2xl border border-gray-100 p-10 text-center"
            >
                <InboxIcon class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <p class="text-lg font-medium text-gray-900">
                    Belum ada pesanan
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Mulai belanja untuk melihat pesanan Anda di sini
                </p>
                <Link
                    :href="route('shop.products')"
                    class="mt-4 inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors"
                >
                    <ShoppingBagIcon class="w-4 h-4" />
                    Mulai Belanja
                </Link>
            </div>

            <template v-else>
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="route('shop.order', order.order_number)"
                    class="block bg-white rounded-2xl border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all duration-200"
                >
                    <div class="p-5">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border"
                                    :class="getStatus(order.status).color"
                                >
                                    <span
                                        class="w-1.5 h-1.5 rounded-full"
                                        :class="getStatus(order.status).dot"
                                    ></span>
                                    {{ getStatus(order.status).label }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-400">{{
                                formatDate(order.created_at)
                            }}</span>
                        </div>

                        <!-- Order Number & Items -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-bold text-gray-900 tracking-wide"
                                >
                                    {{ order.order_number }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ order.items?.length || 0 }} item
                                </p>
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <p
                                        class="text-lg font-bold text-indigo-600"
                                    >
                                        {{ formatPrice(order.total_amount) }}
                                    </p>
                                </div>
                                <ArrowRightIcon class="w-4 h-4 text-gray-400" />
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Pagination -->
                <div
                    v-if="orders.last_page > 1"
                    class="flex items-center justify-center gap-2 pt-4"
                >
                    <Link
                        v-for="(link, i) in orders.links"
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
            </template>
        </div>
    </DashboardLayout>
</template>
