<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    stats: Object,
    recent_orders: Array,
});

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);

const statusColor = (status) =>
    ({
        pending: "bg-yellow-100 text-yellow-800",
        paid: "bg-blue-100 text-blue-800",
        completed: "bg-green-100 text-green-800",
        cancelled: "bg-red-100 text-red-800",
        refunded: "bg-gray-100 text-gray-800",
    })[status] || "bg-gray-100 text-gray-800";
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <p class="text-sm text-gray-500">Total Kategori</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ stats.total_categories }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ stats.total_products }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <p class="text-sm text-gray-500">Pesanan Pending</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">
                    {{ stats.pending_orders }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-600 mt-1">
                    {{ formatCurrency(stats.total_revenue) }}
                </p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Pesanan Terbaru</h2>
                <Link
                    :href="route('admin.orders.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                    Lihat Semua →
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                No. Order
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Pelanggan
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Total
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Tanggal
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="order in recent_orders"
                            :key="order.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3 font-mono text-xs">
                                {{ order.order_number }}
                            </td>
                            <td class="px-5 py-3">{{ order.customer_name }}</td>
                            <td class="px-5 py-3">
                                {{ formatCurrency(order.total_amount) }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="[
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        statusColor(order.status),
                                    ]"
                                >
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                {{
                                    new Date(
                                        order.created_at,
                                    ).toLocaleDateString("id-ID")
                                }}
                            </td>
                        </tr>
                        <tr v-if="!recent_orders?.length">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-gray-400"
                            >
                                Belum ada pesanan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
