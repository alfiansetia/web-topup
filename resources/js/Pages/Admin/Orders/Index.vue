<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { MagnifyingGlassIcon, PlusIcon } from "@heroicons/vue/24/outline";

defineProps({
    orders: Object,
    filters: Object,
});

const search = ref("");
const statusFilter = ref("");

const doSearch = () => {
    router.get(
        route("admin.orders.index"),
        { search: search.value, status: statusFilter.value },
        { preserveState: true },
    );
};

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

const statuses = [
    { value: "", label: "Semua" },
    { value: "pending", label: "Pending" },
    { value: "paid", label: "Dibayar" },
    { value: "completed", label: "Selesai" },
    { value: "cancelled", label: "Dibatalkan" },
];
</script>

<template>
    <Head title="Kelola Pesanan" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
            <Link
                :href="route('admin.orders.create')"
                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition"
            >
                <PlusIcon class="w-4 h-4" />
                Buat Pesanan
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-4">
            <form
                @submit.prevent="doSearch"
                class="flex flex-wrap items-center gap-2"
            >
                <div class="relative">
                    <MagnifyingGlassIcon
                        class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                    />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari no. order, nama, email..."
                        class="w-64 pl-11 pr-4 py-2.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                    />
                </div>
                <select
                    v-model="statusFilter"
                    @change="doSearch"
                    class="py-2.5 px-3.5 text-sm bg-white rounded-lg ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400 transition-all duration-150"
                >
                    <option
                        v-for="s in statuses"
                        :key="s.value"
                        :value="s.value"
                    >
                        {{ s.label }}
                    </option>
                </select>
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors"
                >
                    <MagnifyingGlassIcon class="w-4 h-4" />
                    Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
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
                                Item
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Total
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Pembayaran
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Tanggal
                            </th>
                            <th
                                class="px-5 py-3 text-gray-500 font-medium text-right"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="order in orders.data"
                            :key="order.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3 font-mono text-xs font-medium">
                                {{ order.order_number }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium">
                                    {{ order.customer_name }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ order.customer_email }}
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                {{ order.items?.length || 0 }} item
                            </td>
                            <td class="px-5 py-3 font-medium">
                                {{ formatCurrency(order.total_amount) }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    v-if="order.payment_method"
                                    class="text-xs bg-gray-100 px-2 py-1 rounded-full"
                                >
                                    {{ order.payment_method }}
                                </span>
                                <span v-else class="text-gray-400 text-xs"
                                    >—</span
                                >
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
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{
                                    new Date(
                                        order.created_at,
                                    ).toLocaleDateString("id-ID")
                                }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <Link
                                    :href="route('admin.orders.show', order.id)"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-xs"
                                >
                                    Detail →
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!orders.data?.length">
                            <td
                                colspan="8"
                                class="px-5 py-8 text-center text-gray-400"
                            >
                                Belum ada pesanan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :links="orders.links" />
    </AdminLayout>
</template>
