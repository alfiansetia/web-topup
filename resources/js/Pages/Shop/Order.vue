<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import QRCode from "qrcode";

const props = defineProps({
    order: Object,
});

const qrDataUrl = ref("");
const payment = props.order.payment_gateway_response || null;
const isPaymentPending = props.order.status === "pending" && payment;
const isPaymentExpired = ref(false);
const showAccount = ref({});

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

const formatCountdown = (expiredAt) => {
    if (!expiredAt) return "";
    const now = new Date();
    const exp = new Date(expiredAt);
    const diff = exp - now;
    if (diff <= 0) {
        isPaymentExpired.value = true;
        return "Kedaluwarsa";
    }
    const hours = Math.floor(diff / 3600000);
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    return `${hours}j ${minutes}m ${seconds}s`;
};

const countdown = ref("");
let countdownInterval = null;

onMounted(async () => {
    // Generate QR code from payment_number (EMV QRIS string)
    if (isPaymentPending && payment.payment_number) {
        try {
            qrDataUrl.value = await QRCode.toDataURL(payment.payment_number, {
                width: 280,
                margin: 2,
                color: { dark: "#000000", light: "#ffffff" },
            });
        } catch (err) {
            console.error("QR generation failed:", err);
        }
    }

    // Start countdown if pending
    if (isPaymentPending && payment.expired_at) {
        countdown.value = formatCountdown(payment.expired_at);
        countdownInterval = setInterval(() => {
            countdown.value = formatCountdown(payment.expired_at);
            if (isPaymentExpired.value) clearInterval(countdownInterval);
        }, 1000);
    }
});
</script>

<template>
    <ShopLayout>
        <Head :title="'Pesanan ' + order.order_number" />

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

            <!-- QRIS Payment Section (when pending) -->
            <div
                v-if="isPaymentPending && !isPaymentExpired"
                class="mb-6 bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm"
            >
                <div class="bg-yellow-50 border-b border-yellow-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center"
                        >
                            <svg
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
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">
                                Scan QRIS untuk Bayar
                            </h2>
                            <p class="text-sm text-gray-500">
                                Berlaku hingga
                                {{ formatDate(payment.expired_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <!-- QR Code Image -->
                        <div
                            class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm mb-4"
                        >
                            <img
                                v-if="qrDataUrl"
                                :src="qrDataUrl"
                                alt="QRIS Code"
                                class="w-[280px] h-[280px]"
                            />
                            <div
                                v-else
                                class="w-[280px] h-[280px] flex items-center justify-center bg-gray-50 rounded-lg"
                            >
                                <span class="text-gray-400 text-sm"
                                    >Memuat QR Code...</span
                                >
                            </div>
                        </div>

                        <!-- Countdown -->
                        <div
                            class="flex items-center gap-2 text-sm mb-4"
                            :class="
                                isPaymentExpired
                                    ? 'text-red-600'
                                    : 'text-yellow-700'
                            "
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
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                />
                            </svg>
                            <span class="font-medium">{{
                                isPaymentExpired
                                    ? "Pembayaran kedaluwarsa"
                                    : "Batas waktu: " + countdown
                            }}</span>
                        </div>

                        <!-- Price Breakdown -->
                        <div
                            class="w-full max-w-xs bg-gray-50 rounded-xl p-4 space-y-2 text-sm"
                        >
                            <div class="flex justify-between text-gray-600">
                                <span>Harga Produk</span>
                                <span>{{ formatPrice(payment.amount) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Admin (QRIS)</span>
                                <span>{{ formatPrice(payment.fee) }}</span>
                            </div>
                            <div
                                class="border-t border-gray-200 pt-2 flex justify-between font-bold text-gray-900"
                            >
                                <span>Total Pembayaran</span>
                                <span class="text-indigo-600">{{
                                    formatPrice(payment.total_payment)
                                }}</span>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-400 text-center">
                            Scan kode QR di atas menggunakan aplikasi e-wallet
                            atau mobile banking Anda
                        </p>
                    </div>
                </div>
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
                    <!-- Timeline -->
                    <div class="mb-6">
                        <template v-if="order.status !== 'cancelled'">
                            <div class="flex items-start gap-4">
                                <!-- Step 1: Dibuat -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0"
                                    >
                                        <svg
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-8 bg-green-300"></div>
                                </div>
                                <div class="pb-2">
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Pesanan Dibuat
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <!-- Step 2: Pembayaran -->
                                <div class="flex flex-col items-center">
                                    <div
                                        :class="
                                            order.paid_at ||
                                            order.status === 'paid'
                                                ? 'bg-green-500'
                                                : order.status === 'pending'
                                                  ? 'bg-blue-500'
                                                  : 'bg-gray-300'
                                        "
                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                    >
                                        <svg
                                            v-if="
                                                order.paid_at ||
                                                order.status === 'paid' ||
                                                order.status === 'completed'
                                            "
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="w-4 h-4 text-white"
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
                                    </div>
                                    <div
                                        :class="
                                            order.paid_at ||
                                            order.status === 'paid' ||
                                            order.status === 'completed'
                                                ? 'bg-green-300'
                                                : 'bg-gray-200'
                                        "
                                        class="w-0.5 h-8"
                                    ></div>
                                </div>
                                <div class="pb-2">
                                    <p
                                        class="text-sm font-semibold"
                                        :class="
                                            order.paid_at ||
                                            order.status === 'paid' ||
                                            order.status === 'completed'
                                                ? 'text-gray-900'
                                                : order.status === 'pending'
                                                  ? 'text-blue-600'
                                                  : 'text-gray-400'
                                        "
                                    >
                                        Pembayaran
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            order.paid_at
                                                ? formatDate(order.paid_at)
                                                : order.status === "pending"
                                                  ? "Menunggu pembayaran..."
                                                  : "-"
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <!-- Step 3: Diproses -->
                                <div class="flex flex-col items-center">
                                    <div
                                        :class="
                                            order.status === 'completed'
                                                ? 'bg-green-500'
                                                : order.status === 'paid'
                                                  ? 'bg-blue-500'
                                                  : 'bg-gray-300'
                                        "
                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                    >
                                        <svg
                                            v-if="order.status === 'completed'"
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714a2.25 2.25 0 0 0 .659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-1.756 5.268A1.125 1.125 0 0 1 16.134 21H7.866a1.125 1.125 0 0 1-1.11-1.232L5 14.5m14 0H5"
                                            />
                                        </svg>
                                    </div>
                                    <div
                                        :class="
                                            order.status === 'completed'
                                                ? 'bg-green-300'
                                                : 'bg-gray-200'
                                        "
                                        class="w-0.5 h-8"
                                    ></div>
                                </div>
                                <div class="pb-2">
                                    <p
                                        class="text-sm font-semibold"
                                        :class="
                                            order.status === 'completed'
                                                ? 'text-gray-900'
                                                : order.status === 'paid'
                                                  ? 'text-blue-600'
                                                  : 'text-gray-400'
                                        "
                                    >
                                        Sedang Diproses
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            order.status === "completed" ||
                                            order.status === "paid"
                                                ? "Admin sedang menyiapkan akun"
                                                : "-"
                                        }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <!-- Step 4: Selesai -->
                                <div class="flex flex-col items-center">
                                    <div
                                        :class="
                                            order.status === 'completed'
                                                ? 'bg-green-500'
                                                : 'bg-gray-300'
                                        "
                                        class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                    >
                                        <svg
                                            v-if="order.status === 'completed'"
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold"
                                        :class="
                                            order.status === 'completed'
                                                ? 'text-gray-900'
                                                : 'text-gray-400'
                                        "
                                    >
                                        Selesai
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            order.completed_at
                                                ? formatDate(order.completed_at)
                                                : "-"
                                        }}
                                    </p>
                                </div>
                            </div>
                        </template>

                        <!-- Cancelled timeline -->
                        <template v-else>
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0"
                                    >
                                        <svg
                                            class="w-4 h-4 text-white"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                    </div>
                                    <div class="w-0.5 h-8 bg-red-200"></div>
                                </div>
                                <div class="pb-2">
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                    >
                                        Pesanan Dibuat
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0"
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
                                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-red-600"
                                    >
                                        Dibatalkan
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            order.payment_gateway_status ===
                                            "expired"
                                                ? "Kedaluwarsa — tidak dibayar dalam 1 jam"
                                                : "Dibatalkan oleh admin"
                                        }}
                                    </p>
                                    <p
                                        v-if="order.canceled_at"
                                        class="text-xs text-gray-400 mt-0.5"
                                    >
                                        {{ formatDate(order.canceled_at) }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>

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
                            class="p-3 bg-gray-50 rounded-xl"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p
                                        class="font-medium text-sm text-gray-900"
                                    >
                                        {{ item.product_name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ item.variant_name }} ×
                                        {{ item.quantity }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="font-medium text-sm text-gray-900"
                                    >
                                        {{ formatPrice(item.subtotal) }}
                                    </p>
                                </div>
                            </div>
                            <!-- Akun yang diberikan (completed) -->
                            <div
                                v-if="
                                    order.status === 'completed' &&
                                    item.assigned_items &&
                                    item.assigned_items.length > 0
                                "
                                class="mt-3 pt-3 border-t border-gray-200 space-y-2"
                            >
                                <p class="text-xs font-semibold text-gray-600">
                                    Akun Anda:
                                </p>
                                <div
                                    v-for="(acc, idx) in item.assigned_items"
                                    :key="acc.id"
                                    class="flex items-center gap-2"
                                >
                                    <span class="text-xs text-gray-500 w-5"
                                        >{{ idx + 1 }}.</span
                                    >
                                    <code
                                        class="text-xs bg-white px-2.5 py-1.5 rounded-lg border font-mono flex-1 break-all"
                                    >
                                        {{
                                            showAccount[acc.id]
                                                ? acc.content
                                                : "••••••••••••"
                                        }}
                                    </code>
                                    <button
                                        @click="
                                            showAccount[acc.id] =
                                                !showAccount[acc.id]
                                        "
                                        class="text-gray-400 hover:text-indigo-600 p-1 rounded transition-colors"
                                        :title="
                                            showAccount[acc.id]
                                                ? 'Sembunyikan'
                                                : 'Tampilkan'
                                        "
                                    >
                                        <!-- Eye icon -->
                                        <svg
                                            v-if="!showAccount[acc.id]"
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                            />
                                        </svg>
                                        <!-- EyeSlash icon -->
                                        <svg
                                            v-else
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="
                                            navigator.clipboard.writeText(
                                                acc.content,
                                            )
                                        "
                                        class="text-gray-400 hover:text-indigo-600 p-1 rounded transition-colors"
                                        title="Salin"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9.75a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                    <!-- Show Pakasir total if payment exists and has fee -->
                    <template v-if="payment && payment.fee > 0">
                        <div class="space-y-1.5 text-sm mb-3">
                            <div class="flex justify-between text-gray-600">
                                <span>Harga Produk</span>
                                <span>{{ formatPrice(payment.amount) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Admin</span>
                                <span>{{ formatPrice(payment.fee) }}</span>
                            </div>
                        </div>
                        <div
                            class="border-t border-gray-200 pt-3 flex items-center justify-between"
                        >
                            <span class="text-base font-bold text-gray-900"
                                >Total Pembayaran</span
                            >
                            <span class="text-xl font-bold text-indigo-600">{{
                                formatPrice(payment.total_payment)
                            }}</span>
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-gray-900"
                                >Total Pembayaran</span
                            >
                            <span class="text-xl font-bold text-indigo-600">{{
                                formatPrice(order.total_amount)
                            }}</span>
                        </div>
                    </template>
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
