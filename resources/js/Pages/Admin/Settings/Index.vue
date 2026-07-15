<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    telegram: Object,
    pakasir: Object,
});

const testForm = useForm({});
const testing = ref(false);

const sendTest = () => {
    testing.value = true;
    testForm.post(route("admin.settings.telegram.test"), {
        onFinish: () => {
            testing.value = false;
        },
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Pengaturan" />

        <div class="max-w-2xl">
            <!-- Header -->
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Pengaturan</h1>

            <!-- Telegram Section -->
            <div
                class="bg-white rounded-2xl border border-gray-100 overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <!-- Telegram Icon -->
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-blue-600"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.911.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.492-1.302.484-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.141a.506.506 0 01.171.325c.016.093.036.306.02.472z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">
                                Telegram Bot
                            </h2>
                            <p class="text-sm text-gray-500">
                                Notifikasi pesanan via Telegram
                            </p>
                        </div>
                        <!-- Status Badge -->
                        <span
                            class="ml-auto px-3 py-1 text-xs font-semibold rounded-full"
                            :class="
                                telegram.configured
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'
                            "
                        >
                            {{
                                telegram.configured
                                    ? "Terhubung"
                                    : "Belum Dikonfigurasi"
                            }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Config Info -->
                    <div class="text-sm space-y-2">
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Bot Token</span>
                            <span
                                class="font-medium"
                                :class="
                                    telegram.configured
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{
                                    telegram.configured
                                        ? "✓ Terisi"
                                        : "✗ Kosong"
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Bot Username</span>
                            <span
                                class="font-medium"
                                :class="
                                    telegram.bot_username
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{
                                    telegram.bot_username
                                        ? "@" + telegram.bot_username
                                        : "✗ Kosong"
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Chat IDs</span>
                            <span
                                class="font-medium"
                                :class="
                                    telegram.chat_ids.length
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{
                                    telegram.chat_ids.length
                                        ? telegram.chat_ids.length + " chat"
                                        : "✗ Kosong"
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500"
                                >Notif Pesanan Baru</span
                            >
                            <span
                                class="px-2 py-0.5 text-xs rounded-full"
                                :class="
                                    telegram.notify_new_order
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-500'
                                "
                            >
                                {{
                                    telegram.notify_new_order
                                        ? "Aktif"
                                        : "Nonaktif"
                                }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-500">Notif Pembayaran</span>
                            <span
                                class="px-2 py-0.5 text-xs rounded-full"
                                :class="
                                    telegram.notify_paid
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-gray-100 text-gray-500'
                                "
                            >
                                {{
                                    telegram.notify_paid ? "Aktif" : "Nonaktif"
                                }}
                            </span>
                        </div>
                    </div>

                    <!-- Test Button -->
                    <div class="pt-2">
                        <button
                            @click="sendTest"
                            :disabled="!telegram.configured || testing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <svg
                                v-if="testing"
                                class="w-4 h-4 animate-spin"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"
                                />
                            </svg>
                            {{ testing ? "Mengirim..." : "Kirim Pesan Test" }}
                        </button>
                    </div>

                    <!-- Hint -->
                    <p class="text-xs text-gray-400">
                        Konfigurasi Telegram di file
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded"
                            >.env</code
                        >:
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded"
                            >TELEGRAM_BOT_TOKEN</code
                        >
                        dan
                        <code class="bg-gray-100 px-1.5 py-0.5 rounded"
                            >TELEGRAM_CHAT_IDS</code
                        >
                        (pisahkan dengan koma untuk beberapa chat).
                    </p>
                </div>
            </div>

            <!-- Pakasir Section (info only) -->
            <div
                class="mt-6 bg-white rounded-2xl border border-gray-100 overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-purple-600"
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
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">
                                Pakasir Payment
                            </h2>
                            <p class="text-sm text-gray-500">
                                Payment gateway QRIS
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    <div class="space-y-2">
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Slug / Project</span>
                            <span
                                class="font-medium"
                                :class="
                                    pakasir.slug
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{ pakasir.slug || "✗ Kosong" }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Secret Key</span>
                            <span
                                class="font-medium"
                                :class="
                                    pakasir.secret_set
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                {{
                                    pakasir.secret_set ? "✓ Terisi" : "✗ Kosong"
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between py-2 border-b border-gray-100"
                        >
                            <span class="text-gray-500">Mode</span>
                            <span
                                class="px-2 py-0.5 text-xs rounded-full"
                                :class="
                                    pakasir.is_production
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-yellow-100 text-yellow-700'
                                "
                            >
                                {{
                                    pakasir.is_production
                                        ? "Production"
                                        : "Sandbox"
                                }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-500">Webhook URL</span>
                            <code
                                class="text-xs bg-gray-100 px-2 py-1 rounded select-all"
                                >{{ pakasir.webhook_url }}</code
                            >
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 pt-2">
                        Konfigurasi di file
                        <code class="bg-gray-100 px-1 py-0.5 rounded">.env</code
                        >:
                        <code class="bg-gray-100 px-1 py-0.5 rounded"
                            >PAKASIR_SLUG</code
                        >,
                        <code class="bg-gray-100 px-1 py-0.5 rounded"
                            >PAKASIR_SECRET_KEY</code
                        >,
                        <code class="bg-gray-100 px-1 py-0.5 rounded"
                            >PAKASIR_IS_PRODUCTION</code
                        >
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
