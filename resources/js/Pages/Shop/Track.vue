<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { LightBulbIcon } from "@heroicons/vue/24/outline";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const form = useForm({
    order_number: "",
});

const submit = () => {
    form.post(route("shop.track.submit"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <ShopLayout>
        <Head title="Lacak Pesanan" />

        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-8">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100"
                >
                    <svg
                        class="h-8 w-8 text-indigo-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                        />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Lacak Pesanan</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Masukkan nomor pesanan untuk melihat status pesanan Anda.
                </p>
                <Link
                    v-if="user"
                    :href="route('dashboard.orders')"
                    class="inline-flex items-center gap-1.5 mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors"
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
                            d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"
                        />
                    </svg>
                    Lihat Semua Pesanan Saya
                </Link>
            </div>

            <div
                class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm"
            >
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 mb-1.5"
                            >Nomor Pesanan</label
                        >
                        <input
                            v-model="form.order_number"
                            type="text"
                            required
                            placeholder="INV-A3BF9KXM"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                        />
                        <p
                            v-if="form.errors.order_number"
                            class="mt-1.5 text-xs text-red-500"
                        >
                            {{ form.errors.order_number }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                    >
                        <span
                            v-if="form.processing"
                            class="flex items-center justify-center gap-2"
                        >
                            <svg
                                class="animate-spin h-4 w-4"
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
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                ></path>
                            </svg>
                            Mencari...
                        </span>
                        <span
                            v-else
                            class="flex items-center justify-center gap-2"
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
                            Lacak Pesanan
                        </span>
                    </button>
                </form>

                <!-- Help text -->
                <div class="mt-5 p-4 bg-indigo-50 rounded-xl">
                    <p class="text-xs text-indigo-700">
                        <LightBulbIcon class="w-4 h-4 inline text-indigo-500" />
                        <strong>Tips:</strong> Nomor pesanan dikirimkan ke email
                        Anda setelah checkout. Format:
                        <code class="bg-indigo-100 px-1 rounded">A3BF9KXM</code>
                    </p>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
