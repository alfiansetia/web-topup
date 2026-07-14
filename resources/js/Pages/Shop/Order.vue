<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    order: Object,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const statusConfig = {
    pending: { label: "Menunggu Pembayaran", color: "yellow", icon: "clock" },
    paid: { label: "Sudah Dibayar", color: "blue", icon: "check" },
    completed: { label: "Selesai", color: "green", icon: "check-circle" },
    cancelled: { label: "Dibatalkan", color: "red", icon: "x" },
    refunded: { label: "Dikembalikan", color: "gray", icon: "arrow-uturn" },
};

const status = statusConfig[props.order.status] || statusConfig.pending;
</script>

<template>
    <ShopLayout>
        <Head :title="'Pesanan ' + order.order_number + ' - TopUp Store'" />

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <span class="text-gray-900 font-medium">Detail Pesanan</span>
            </nav>

            <!-- Success Banner -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 flex items-start gap-3 rounded-xl bg-green-50 border border-green-100 px-4 py-3.5 text-sm text-green-700"
            >
                <svg
                    class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                    />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- Order Header -->
            <div
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden"
            >
                <!-- Status Banner -->
                <div
                    class="px-6 py-4 border-b border-gray-100"
                    :class="{
                        'bg-yellow-50': order.status === 'pending',
                        'bg-blue-50': order.status === 'paid',
                        'bg-green-50': order.status === 'completed',
                        'bg-red-50': order.status === 'cancelled',
                        'bg-gray-50': order.status === 'refunded',
                    }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- Status Icons -->
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center"
                                :class="{
                                    'bg-yellow-100': order.status === 'pending',
                                    'bg-blue-100': order.status === 'paid',
                                    'bg-green-100':
                                        order.status === 'completed',
                                    'bg-red-100': order.status === 'cancelled',
                                    'bg-gray-100': order.status === 'refunded',
                                }"
                            >
                                <svg
                                    v-if="order.status === 'pending'"
                                    class="w-5 h-5 text-yellow-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="order.status === 'completed'"
                                    class="w-5 h-5 text-green-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="order.status === 'paid'"
                                    class="w-5 h-5 text-blue-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="w-5 h-5 text-red-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">
                                    {{ status.label }}
                                </h2>
                                <p class="text-sm text-gray-500">
                                    {{ order.order_number }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs font-semibold rounded-full"
                            :class="{
                                'bg-yellow-100 text-yellow-700':
                                    order.status === 'pending',
                                'bg-blue-100 text-blue-700':
                                    order.status === 'paid',
                                'bg-green-100 text-green-700':
                                    order.status === 'completed',
                                'bg-red-100 text-red-700':
                                    order.status === 'cancelled',
                                'bg-gray-100 text-gray-700':
                                    order.status === 'refunded',
                            }"
                            >{{ status.label }}</span
                        >
                    </div>
                </div>

                <!-- Order Info -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Nama</span>
                            <p class="font-medium text-gray-900">
                                {{ order.customer_name }}
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Email</span>
                            <p class="font-medium text-gray-900">
                                {{ order.customer_email }}
                            </p>
                        </div>
                        <div v-if="order.customer_phone">
                            <span class="text-gray-500">WhatsApp</span>
                            <p class="font-medium text-gray-900">
                                {{ order.customer_phone }}
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Tanggal</span>
                            <p class="font-medium text-gray-900">
                                {{ formatDate(order.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border-t border-gray-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">
                        Detail Produk
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl"
                        >
                            <div>
                                <p class="font-medium text-sm text-gray-900">
                                    {{ item.product_name }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ item.variant_name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-sm text-gray-900">
                                    {{ formatPrice(item.subtotal) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900"
                            >Total Pembayaran</span
                        >
                        <span class="text-xl font-bold text-indigo-600">{{
                            formatPrice(order.total_amount)
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div
                class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3"
            >
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm transition-all duration-200"
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
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                        />
                    </svg>
                    Kembali ke Beranda
                </Link>
                <Link
                    :href="route('shop.track')"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 text-sm font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 shadow-sm transition-all duration-200"
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
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                        />
                    </svg>
                    Lacak Pesanan Lain
                </Link>
            </div>
        </div>
    </ShopLayout>
</template>
