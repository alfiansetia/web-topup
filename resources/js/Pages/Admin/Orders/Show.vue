<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    ClockIcon,
    CreditCardIcon,
    CheckCircleIcon,
    UserIcon,
    ArchiveBoxIcon,
    EyeIcon,
    EyeSlashIcon,
    ClipboardIcon,
    CurrencyDollarIcon,
    BoltIcon,
    PaperClipIcon,
    XCircleIcon,
    ArrowLeftIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

const props = defineProps({ order: Object });

const notesForm = useForm({ notes: props.order.notes || "" });
const showAccount = ref({});

const saveNotes = () => {
    notesForm.put(route("admin.orders.notes", props.order.id));
};

const verifyPayment = () => {
    Swal.fire({
        title: "Verifikasi Pembayaran?",
        text: "Pembayaran order ini akan diverifikasi.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Verifikasi",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("admin.orders.verify", props.order.id));
        }
    });
};

const completeOrder = () => {
    Swal.fire({
        title: "Selesaikan Order?",
        text: "Akun akan dikirim ke pelanggan.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Selesaikan",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("admin.orders.complete", props.order.id));
        }
    });
};

const cancelOrder = () => {
    Swal.fire({
        title: "Batalkan Order?",
        text: "Stok akan dikembalikan ke inventori.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Batalkan",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("admin.orders.cancel", props.order.id));
        }
    });
};

const toggleAccount = (id) => {
    showAccount.value[id] = !showAccount.value[id];
};

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);

const statusColor = (status) =>
    ({
        pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
        paid: "bg-blue-100 text-blue-800 border-blue-200",
        completed: "bg-green-100 text-green-800 border-green-200",
        cancelled: "bg-red-100 text-red-800 border-red-200",
        refunded: "bg-gray-100 text-gray-800 border-gray-200",
    })[status] || "bg-gray-100 text-gray-800 border-gray-200";

const statusSteps = [
    { key: "pending", label: "Menunggu Bayar", icon: ClockIcon },
    { key: "paid", label: "Sudah Dibayar", icon: CreditCardIcon },
    { key: "completed", label: "Selesai", icon: CheckCircleIcon },
];

const currentStepIndex = statusSteps.findIndex(
    (s) => s.key === props.order.status,
);
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.orders.index')"
                class="text-sm text-indigo-600 hover:text-indigo-800"
                >← Kembali ke Pesanan</Link
            >
            <div class="flex items-center justify-between mt-2">
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ order.order_number }}
                </h1>
                <span
                    :class="[
                        'px-3 py-1.5 rounded-full text-sm font-medium border',
                        statusColor(order.status),
                    ]"
                >
                    {{ order.status }}
                </span>
            </div>
        </div>

        <!-- Status Stepper -->
        <div
            v-if="order.status !== 'cancelled' && order.status !== 'refunded'"
            class="bg-white rounded-xl shadow-sm border p-5 mb-6"
        >
            <div class="flex items-center justify-between">
                <template v-for="(step, idx) in statusSteps" :key="step.key">
                    <div class="flex flex-col items-center">
                        <div
                            :class="[
                                'w-10 h-10 rounded-full flex items-center justify-center border-2',
                                idx <= currentStepIndex
                                    ? 'bg-indigo-600 border-indigo-600 text-white'
                                    : 'bg-gray-100 border-gray-200 text-gray-400',
                            ]"
                        >
                            <component :is="step.icon" class="w-5 h-5" />
                        </div>
                        <span
                            :class="[
                                'text-xs mt-1',
                                idx <= currentStepIndex
                                    ? 'text-indigo-600 font-medium'
                                    : 'text-gray-400',
                            ]"
                        >
                            {{ step.label }}
                        </span>
                    </div>
                    <div
                        v-if="idx < statusSteps.length - 1"
                        :class="[
                            'flex-1 h-0.5 mx-3 mb-5',
                            idx < currentStepIndex
                                ? 'bg-indigo-600'
                                : 'bg-gray-200',
                        ]"
                    ></div>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">
                        <UserIcon class="w-5 h-5 inline text-gray-500" /> Info
                        Pelanggan
                    </h2>
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Nama</dt>
                            <dd class="font-medium">
                                {{ order.customer_name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Email</dt>
                            <dd>{{ order.customer_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Telepon</dt>
                            <dd>{{ order.customer_phone || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Akun Terdaftar</dt>
                            <dd>
                                {{ order.user ? order.user.name : "Guest" }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Items -->
                <div
                    class="bg-white rounded-xl shadow-sm border overflow-hidden"
                >
                    <div class="px-5 py-4 border-b">
                        <h2 class="font-semibold text-gray-900">
                            <ArchiveBoxIcon
                                class="w-5 h-5 inline text-gray-500"
                            />
                            Item Pesanan
                        </h2>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-5 py-3 text-gray-500 font-medium">
                                    Produk
                                </th>
                                <th class="px-5 py-3 text-gray-500 font-medium">
                                    Varian
                                </th>
                                <th class="px-5 py-3 text-gray-500 font-medium">
                                    Harga
                                </th>
                                <th class="px-5 py-3 text-gray-500 font-medium">
                                    Akun
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="item in order.items"
                                :key="item.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-5 py-3 font-medium">
                                    {{ item.product_name }}
                                </td>
                                <td class="px-5 py-3">
                                    {{ item.variant_name }}
                                </td>
                                <td class="px-5 py-3">
                                    {{ formatCurrency(item.price) }}
                                </td>
                                <td class="px-5 py-3">
                                    <div
                                        v-if="
                                            item.product_item &&
                                            order.status === 'completed'
                                        "
                                        class="flex items-center gap-2"
                                    >
                                        <code
                                            class="text-xs bg-gray-50 px-2 py-1 rounded border font-mono"
                                        >
                                            {{
                                                showAccount[item.id]
                                                    ? item.product_item.content
                                                    : "••••••••"
                                            }}
                                        </code>
                                        <button
                                            @click="toggleAccount(item.id)"
                                            class="text-gray-400 hover:text-gray-600 text-xs"
                                        >
                                            <EyeSlashIcon
                                                v-if="showAccount[item.id]"
                                                class="w-4 h-4"
                                            />
                                            <EyeIcon v-else class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="
                                                navigator.clipboard.writeText(
                                                    item.product_item.content,
                                                )
                                            "
                                            class="text-gray-400 hover:text-gray-600 text-xs"
                                        >
                                            <ClipboardIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <span v-else class="text-gray-400 text-xs"
                                        >Menunggu selesai</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div
                        class="px-5 py-3 bg-gray-50 border-t flex justify-between items-center"
                    >
                        <span class="text-sm font-medium text-gray-700"
                            >Total</span
                        >
                        <span class="text-lg font-bold text-indigo-600">{{
                            formatCurrency(order.total_amount)
                        }}</span>
                    </div>
                </div>

                <!-- Payment Gateway Info -->
                <div
                    v-if="order.payment_ref"
                    class="bg-white rounded-xl shadow-sm border p-5"
                >
                    <h2 class="font-semibold text-gray-900 mb-3">
                        <CreditCardIcon class="w-5 h-5 inline text-gray-500" />
                        Info Payment Gateway
                    </h2>
                    <dl class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Ref ID</dt>
                            <dd class="font-mono text-xs">
                                {{ order.payment_ref }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Metode</dt>
                            <dd>{{ order.payment_method || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Channel</dt>
                            <dd>{{ order.payment_channel || "—" }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Fee</dt>
                            <dd>{{ formatCurrency(order.payment_fee) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Gateway Status</dt>
                            <dd>{{ order.payment_gateway_status || "—" }}</dd>
                        </div>
                        <div v-if="order.payment_url">
                            <dt class="text-gray-500">Payment URL</dt>
                            <dd>
                                <a
                                    :href="order.payment_url"
                                    target="_blank"
                                    class="text-indigo-600 hover:underline text-xs break-all"
                                    >{{ order.payment_url }}</a
                                >
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right: Actions & Notes -->
            <div class="space-y-6">
                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h2
                        class="font-semibold text-gray-900 mb-3 flex items-center gap-1.5"
                    >
                        <BoltIcon class="w-5 h-5 text-gray-500" /> Aksi
                    </h2>
                    <div class="space-y-2">
                        <button
                            v-if="order.status === 'pending'"
                            @click="verifyPayment"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors"
                        >
                            <CheckCircleIcon class="w-5 h-5 inline" />
                            Verifikasi Pembayaran
                        </button>
                        <button
                            v-if="order.status === 'paid'"
                            @click="completeOrder"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors"
                        >
                            <ArchiveBoxIcon class="w-5 h-5 inline" /> Selesaikan
                            & Kirim Akun
                        </button>
                        <button
                            v-if="['pending', 'paid'].includes(order.status)"
                            @click="cancelOrder"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors border border-red-200"
                        >
                            <XCircleIcon class="w-5 h-5 inline" /> Batalkan
                            Order
                        </button>
                        <a
                            v-if="order.payment_proof"
                            :href="`/storage/${order.payment_proof}`"
                            target="_blank"
                            class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors border"
                        >
                            <PaperClipIcon class="w-5 h-5 inline" /> Lihat Bukti
                            Bayar
                        </a>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">
                        📝 Catatan Admin
                    </h2>
                    <form @submit.prevent="saveNotes" class="space-y-3">
                        <textarea
                            v-model="notesForm.notes"
                            rows="4"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            placeholder="Tambah catatan..."
                        ></textarea>
                        <button
                            type="submit"
                            :disabled="notesForm.processing"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            Simpan Catatan
                        </button>
                    </form>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">
                        🕐 Timeline
                    </h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex gap-3">
                            <div
                                class="w-2 h-2 bg-gray-300 rounded-full mt-1.5 shrink-0"
                            ></div>
                            <div>
                                <p class="text-gray-700">Order dibuat</p>
                                <p class="text-xs text-gray-400">
                                    {{
                                        new Date(
                                            order.created_at,
                                        ).toLocaleString("id-ID")
                                    }}
                                </p>
                            </div>
                        </div>
                        <div v-if="order.paid_at" class="flex gap-3">
                            <div
                                class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 shrink-0"
                            ></div>
                            <div>
                                <p class="text-gray-700">Pembayaran diterima</p>
                                <p class="text-xs text-gray-400">
                                    {{
                                        new Date(order.paid_at).toLocaleString(
                                            "id-ID",
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div v-if="order.completed_at" class="flex gap-3">
                            <div
                                class="w-2 h-2 bg-green-500 rounded-full mt-1.5 shrink-0"
                            ></div>
                            <div>
                                <p class="text-gray-700">Order selesai</p>
                                <p class="text-xs text-gray-400">
                                    {{
                                        new Date(
                                            order.completed_at,
                                        ).toLocaleString("id-ID")
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
