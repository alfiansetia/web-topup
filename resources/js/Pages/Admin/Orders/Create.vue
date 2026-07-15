<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";
import {
    ArrowLeftIcon,
    PlusIcon,
    TrashIcon,
    ShoppingBagIcon,
    UserIcon,
    CheckCircleIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

const props = defineProps({ products: Array });

const userFound = ref(null); // null = belum cek, false = tidak ketemu, object = ketemu
let emailTimer = null;

const checkEmail = () => {
    clearTimeout(emailTimer);
    userFound.value = null;

    const email = form.customer_email.trim();
    if (!email || email.length < 5) return;

    emailTimer = setTimeout(async () => {
        try {
            const { data } = await axios.post(
                route("admin.orders.lookup-user"),
                { email },
            );
            if (data.found) {
                userFound.value = data.user;
                // Auto-fill hanya kalau field masih kosong
                if (!form.customer_name) form.customer_name = data.user.name;
            } else {
                userFound.value = false;
            }
        } catch {
            userFound.value = null;
        }
    }, 500);
};

const form = useForm({
    customer_name: "",
    customer_email: "",
    customer_phone: "",
    notes: "",
    send_email: true,
    items: [{ product_id: "", variant_id: "", quantity: 1 }],
});

// Ambil variants dari product yang dipilih
const getVariants = (productIndex) => {
    const productId = form.items[productIndex].product_id;
    const product = props.products.find((p) => p.id == productId);
    return product ? product.variants : [];
};

// Set variant_id ketika product berubah
const onProductChange = (index) => {
    form.items[index].variant_id = "";
};

// Tambah item row
const addItem = () => {
    form.items.push({ product_id: "", variant_id: "", quantity: 1 });
};

// Hapus item row
const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

// Hitung subtotal per item
const getItemSubtotal = (item) => {
    if (!item.variant_id) return 0;
    for (const product of props.products) {
        const variant = product.variants.find((v) => v.id == item.variant_id);
        if (variant) return variant.effective_price * item.quantity;
    }
    return 0;
};

// Total amount
const totalAmount = computed(() =>
    form.items.reduce((sum, item) => sum + getItemSubtotal(item), 0),
);

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);

// Cari variant info (untuk tampilan)
const getVariantInfo = (variantId) => {
    if (!variantId) return null;
    for (const product of props.products) {
        const variant = product.variants.find((v) => v.id == variantId);
        if (variant) return { product, variant };
    }
    return null;
};

const submit = () => {
    // Validasi sederhana
    const emptyVariant = form.items.some((item) => !item.variant_id);
    if (emptyVariant) {
        Swal.fire("Oops", "Pilih produk/variant untuk setiap item.", "warning");
        return;
    }

    Swal.fire({
        title: "Buat Pesanan?",
        html: `Total: <b>${formatCurrency(totalAmount.value)}</b>`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Buat",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            form.post(route("admin.orders.store"));
        }
    });
};
</script>

<template>
    <Head title="Buat Pesanan Manual" />
    <AdminLayout>
        <div class="flex items-center gap-3 mb-6">
            <Link
                :href="route('admin.orders.index')"
                class="p-2 rounded-lg hover:bg-gray-100 transition"
            >
                <ArrowLeftIcon class="w-5 h-5 text-gray-500" />
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">
                Buat Pesanan Manual
            </h1>
        </div>

        <form @submit.prevent="submit" class="max-w-3xl">
            <!-- Info Pelanggan -->
            <div
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-5 mb-5"
            >
                <h2
                    class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2"
                >
                    <UserIcon class="w-5 h-5 text-gray-400" />
                    Informasi Pelanggan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Nama <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.customer_name"
                            type="text"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            placeholder="Nama pelanggan"
                            required
                        />
                        <p
                            v-if="form.errors.customer_name"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.customer_name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.customer_email"
                            @input="checkEmail"
                            type="email"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            placeholder="email@example.com"
                            required
                        />
                        <p
                            v-if="userFound"
                            class="text-green-600 text-xs mt-1 flex items-center gap-1"
                        >
                            <CheckCircleIcon class="w-3.5 h-3.5" />
                            User ditemukan: {{ userFound.name }}
                        </p>
                        <p
                            v-else-if="
                                userFound === false &&
                                form.customer_email.length > 5
                            "
                            class="text-gray-400 text-xs mt-1"
                        >
                            Email belum terdaftar — masukkan nama manual
                        </p>
                        <p
                            v-if="form.errors.customer_email"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.customer_email }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            No. Telepon
                        </label>
                        <input
                            v-model="form.customer_phone"
                            type="text"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            placeholder="08xxxxxxxxxx (opsional)"
                        />
                    </div>
                </div>
            </div>

            <!-- Pilih Produk -->
            <div
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-5 mb-5"
            >
                <h2
                    class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2"
                >
                    <ShoppingBagIcon class="w-5 h-5 text-gray-400" />
                    Produk yang Dipesan
                </h2>

                <div
                    v-for="(item, index) in form.items"
                    :key="index"
                    class="flex flex-col sm:flex-row gap-3 mb-4 p-4 bg-gray-50 rounded-lg"
                >
                    <!-- Product selector -->
                    <div class="flex-1">
                        <label
                            v-if="index === 0"
                            class="block text-xs font-medium text-gray-500 mb-1"
                        >
                            Produk
                        </label>
                        <select
                            v-model="item.product_id"
                            @change="onProductChange(index)"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option value="">Pilih Produk...</option>
                            <option
                                v-for="product in products"
                                :key="product.id"
                                :value="product.id"
                            >
                                {{ product.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Variant selector -->
                    <div class="flex-1">
                        <label
                            v-if="index === 0"
                            class="block text-xs font-medium text-gray-500 mb-1"
                        >
                            Varian
                        </label>
                        <select
                            v-model="item.variant_id"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :disabled="!item.product_id"
                        >
                            <option value="">Pilih Varian...</option>
                            <option
                                v-for="variant in getVariants(index)"
                                :key="variant.id"
                                :value="variant.id"
                            >
                                {{ variant.name }} —
                                {{ formatCurrency(variant.effective_price) }}
                            </option>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div class="w-full sm:w-24">
                        <label
                            v-if="index === 0"
                            class="block text-xs font-medium text-gray-500 mb-1"
                        >
                            Qty
                        </label>
                        <input
                            v-model.number="item.quantity"
                            type="number"
                            min="1"
                            max="10"
                            class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <!-- Subtotal + remove -->
                    <div class="flex items-center gap-3 sm:pt-5">
                        <span
                            class="text-sm font-semibold text-indigo-600 whitespace-nowrap"
                        >
                            {{ formatCurrency(getItemSubtotal(item)) }}
                        </span>
                        <button
                            v-if="form.items.length > 1"
                            type="button"
                            @click="removeItem(index)"
                            class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                        >
                            <TrashIcon class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    @click="addItem"
                    class="flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium mt-2"
                >
                    <PlusIcon class="w-4 h-4" />
                    Tambah Item
                </button>

                <!-- Total -->
                <div
                    class="flex justify-between items-center mt-5 pt-4 border-t border-gray-200"
                >
                    <span class="text-sm font-medium text-gray-500">Total</span>
                    <span class="text-xl font-bold text-indigo-600">
                        {{ formatCurrency(totalAmount) }}
                    </span>
                </div>
            </div>

            <!-- Catatan -->
            <div
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-5 mb-5"
            >
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan (opsional)
                </label>
                <textarea
                    v-model="form.notes"
                    rows="3"
                    class="w-full rounded-lg ring-1 ring-inset ring-gray-300 py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    placeholder="Catatan admin..."
                ></textarea>
            </div>

            <!-- Kirim Email -->
            <div
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-5 mb-5"
            >
                <label
                    class="flex items-center gap-3 cursor-pointer select-none"
                >
                    <button
                        type="button"
                        @click="form.send_email = !form.send_email"
                        :class="[
                            form.send_email ? 'bg-indigo-600' : 'bg-gray-200',
                            'relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none',
                        ]"
                    >
                        <span
                            :class="[
                                form.send_email
                                    ? 'translate-x-5'
                                    : 'translate-x-0',
                                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 mt-0.5 ml-0.5 transition duration-200 ease-in-out',
                            ]"
                        />
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        Kirim email notifikasi ke pelanggan
                    </span>
                </label>
                <p class="text-xs text-gray-400 mt-1.5 ml-14">
                    Email berisi detail pesanan & instruksi pembayaran akan
                    dikirim ke {{ form.customer_email || "..." }}
                </p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3">
                <Link
                    :href="route('admin.orders.index')"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white rounded-lg ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition"
                >
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
                >
                    {{ form.processing ? "Menyimpan..." : "Buat Pesanan" }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
