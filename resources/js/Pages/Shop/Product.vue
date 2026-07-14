<script setup>
import ShopLayout from "@/Layouts/ShopLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    product: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isLoggedIn = computed(() => !!user.value);

const selectedVariant = ref(props.product.variants?.[0] || null);
const showCheckoutForm = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

const form = useForm({
    notes: "",
    variant_id: selectedVariant.value?.id || null,
    quantity: 1,
});

const selectVariant = (variant) => {
    selectedVariant.value = variant;
    form.variant_id = variant.id;
};

const startCheckout = () => {
    if (!isLoggedIn.value) {
        window.location.href = route("login");
        return;
    }
    showCheckoutForm.value = true;
    form.variant_id = selectedVariant.value?.id;
};

const submitOrder = () => {
    form.post(route("shop.checkout"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <ShopLayout>
        <Head :title="product.name" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <Link
                    :href="route('shop.category', product.category?.slug)"
                    class="hover:text-indigo-600 transition-colors"
                    >{{ product.category?.name }}</Link
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
                <span class="text-gray-900 font-medium">{{
                    product.name
                }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Left: Product Image & Info -->
                <div>
                    <div
                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden"
                    >
                        <div class="aspect-[4/3] bg-gray-50">
                            <img
                                :src="product.image_url"
                                :alt="product.name"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>

                    <!-- Features -->
                    <div
                        v-if="product.features && product.features.length > 0"
                        class="mt-6 bg-white rounded-2xl border border-gray-100 p-6"
                    >
                        <h3
                            class="font-semibold text-gray-900 mb-4 flex items-center gap-2"
                        >
                            <svg
                                class="w-5 h-5 text-indigo-500"
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
                            Fitur Produk
                        </h3>
                        <ul class="space-y-2.5">
                            <li
                                v-for="(feature, i) in product.features"
                                :key="i"
                                class="flex items-start gap-2.5 text-sm text-gray-600"
                            >
                                <svg
                                    class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m4.5 12.75 6 6 9-13.5"
                                    />
                                </svg>
                                {{ feature }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right: Product Details & Purchase -->
                <div>
                    <div class="sticky top-24">
                        <!-- Category badge -->
                        <Link
                            :href="
                                route('shop.category', product.category?.slug)
                            "
                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-full hover:bg-indigo-100 transition-colors mb-4"
                        >
                            {{ product.category?.name }}
                        </Link>

                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ product.name }}
                        </h1>

                        <p
                            v-if="product.description"
                            class="mt-3 text-gray-500 leading-relaxed"
                        >
                            {{ product.description }}
                        </p>

                        <!-- Variant Selection -->
                        <div class="mt-6">
                            <h3
                                class="text-sm font-semibold text-gray-900 mb-3"
                            >
                                Pilih Varian
                            </h3>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 gap-2.5"
                            >
                                <button
                                    v-for="variant in product.variants"
                                    :key="variant.id"
                                    @click="selectVariant(variant)"
                                    class="relative text-left p-4 rounded-xl border-2 transition-all duration-200"
                                    :class="
                                        selectedVariant?.id === variant.id
                                            ? 'border-indigo-500 bg-indigo-50 shadow-sm'
                                            : 'border-gray-100 bg-white hover:border-gray-200'
                                    "
                                >
                                    <div
                                        class="font-medium text-sm text-gray-900"
                                    >
                                        {{ variant.name }}
                                    </div>
                                    <div
                                        v-if="variant.description"
                                        class="text-xs text-gray-500 mt-0.5"
                                    >
                                        {{ variant.description }}
                                    </div>
                                    <div class="mt-2 flex items-baseline gap-2">
                                        <span
                                            v-if="variant.is_discounted"
                                            class="text-xs text-gray-400 line-through"
                                            >{{
                                                formatPrice(variant.price)
                                            }}</span
                                        >
                                        <span
                                            class="text-base font-bold"
                                            :class="
                                                variant.is_discounted
                                                    ? 'text-red-500'
                                                    : 'text-indigo-600'
                                            "
                                        >
                                            {{
                                                formatPrice(
                                                    variant.effective_price,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <!-- Selected indicator -->
                                    <div
                                        v-if="
                                            selectedVariant?.id === variant.id
                                        "
                                        class="absolute top-2 right-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-indigo-500"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                            />
                                        </svg>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div
                            class="mt-6 p-5 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Harga</span>
                                <span
                                    class="text-2xl font-bold text-indigo-700"
                                >
                                    {{
                                        selectedVariant
                                            ? formatPrice(
                                                  selectedVariant.effective_price,
                                              )
                                            : "-"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Checkout Form -->
                        <div
                            v-if="showCheckoutForm && isLoggedIn"
                            class="mt-6 bg-white rounded-2xl border border-gray-100 p-6"
                        >
                            <h3 class="font-semibold text-gray-900 mb-4">
                                Detail Pesanan
                            </h3>

                            <!-- Logged-in user info -->
                            <div
                                class="mb-4 p-4 bg-indigo-50 rounded-xl border border-indigo-100"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-indigo-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-gray-900"
                                        >
                                            {{ user.name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ user.email }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form
                                @submit.prevent="submitOrder"
                                class="space-y-4"
                            >
                                <!-- Checkout Instruction -->
                                <div
                                    v-if="product.checkout_instruction"
                                    class="flex items-start gap-3 p-4 bg-amber-50 rounded-xl border border-amber-200"
                                >
                                    <svg
                                        class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                                        />
                                    </svg>
                                    <p class="text-sm text-amber-800">
                                        {{ product.checkout_instruction }}
                                    </p>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Keterangan
                                        <span class="text-gray-400"
                                            >(opsional)</span
                                        ></label
                                    >
                                    <textarea
                                        v-model="form.notes"
                                        rows="2"
                                        placeholder="Catatan untuk pesanan Anda..."
                                        class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all resize-none"
                                    ></textarea>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                        >Jumlah</label
                                    >
                                    <input
                                        v-model.number="form.quantity"
                                        type="number"
                                        min="1"
                                        max="10"
                                        required
                                        class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                                    />
                                    <p
                                        v-if="form.errors.quantity"
                                        class="mt-1 text-xs text-red-500"
                                    >
                                        {{ form.errors.quantity }}
                                    </p>
                                </div>

                                <!-- Order summary -->
                                <div class="p-4 bg-gray-50 rounded-xl text-sm">
                                    <div
                                        class="flex justify-between text-gray-600"
                                    >
                                        <span
                                            >{{ selectedVariant?.name }} x
                                            {{ form.quantity }}</span
                                        >
                                        <span>{{
                                            selectedVariant
                                                ? formatPrice(
                                                      selectedVariant.effective_price *
                                                          form.quantity,
                                                  )
                                                : "-"
                                        }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between font-bold text-gray-900 mt-2 pt-2 border-t border-gray-200"
                                    >
                                        <span>Total</span>
                                        <span class="text-indigo-600">{{
                                            selectedVariant
                                                ? formatPrice(
                                                      selectedVariant.effective_price *
                                                          form.quantity,
                                                  )
                                                : "-"
                                        }}</span>
                                    </div>
                                </div>

                                <div
                                    v-if="form.errors.checkout"
                                    class="p-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600"
                                >
                                    {{ form.errors.checkout }}
                                </div>

                                <button
                                    type="submit"
                                    :disabled="
                                        form.processing || !selectedVariant
                                    "
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
                                        Memproses...
                                    </span>
                                    <span v-else>Buat Pesanan</span>
                                </button>
                            </form>
                        </div>

                        <!-- Buy Button -->
                        <button
                            v-else
                            @click="startCheckout"
                            :disabled="!selectedVariant"
                            class="mt-6 w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                                />
                            </svg>
                            {{
                                !selectedVariant
                                    ? "Pilih Varian Terlebih Dahulu"
                                    : "Beli Sekarang"
                            }}
                        </button>

                        <!-- Trust badges -->
                        <div class="mt-6 grid grid-cols-3 gap-3">
                            <div
                                class="text-center p-3 bg-white rounded-xl border border-gray-100"
                            >
                                <svg
                                    class="w-6 h-6 text-green-500 mx-auto mb-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"
                                    />
                                </svg>
                                <span
                                    class="text-[11px] font-medium text-gray-600"
                                    >Garansi 100%</span
                                >
                            </div>
                            <div
                                class="text-center p-3 bg-white rounded-xl border border-gray-100"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-500 mx-auto mb-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"
                                    />
                                </svg>
                                <span
                                    class="text-[11px] font-medium text-gray-600"
                                    >Proses Instan</span
                                >
                            </div>
                            <div
                                class="text-center p-3 bg-white rounded-xl border border-gray-100"
                            >
                                <svg
                                    class="w-6 h-6 text-purple-500 mx-auto mb-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"
                                    />
                                </svg>
                                <span
                                    class="text-[11px] font-medium text-gray-600"
                                    >Support 24/7</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>
