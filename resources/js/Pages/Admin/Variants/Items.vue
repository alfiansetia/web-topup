<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    ArchiveBoxIcon,
    PlusIcon,
    PencilSquareIcon,
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
    BanknotesIcon,
} from "@heroicons/vue/24/outline";
import Swal from "sweetalert2";

const props = defineProps({
    product: Object,
    variant: Object,
});

// Tambah item
const addForm = useForm({ content: "" });
const showAddForm = ref(false);

const storeItem = () => {
    addForm.post(
        route("admin.variants.items.store", [
            props.product.id,
            props.variant.id,
        ]),
        {
            onSuccess: () => {
                addForm.reset();
                showAddForm.value = false;
            },
        },
    );
};

// Edit item
const editingId = ref(null);
const editForm = useForm({ content: "" });

const startEdit = (item) => {
    editingId.value = item.id;
    editForm.content = item.content;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const saveEdit = (item) => {
    editForm.put(
        route("admin.variants.items.update", [
            props.product.id,
            props.variant.id,
            item.id,
        ]),
        {
            onSuccess: () => cancelEdit(),
        },
    );
};

const deleteItem = (item) => {
    Swal.fire({
        title: "Hapus Item Stok?",
        text: "Item ini akan dihapus permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("admin.variants.items.destroy", [
                    props.product.id,
                    props.variant.id,
                    item.id,
                ]),
            );
        }
    });
};

const changeStatus = (item, status) => {
    const labels = {
        available: "Tersedia",
        sold: "Terjual",
        reserved: "Direservasi",
    };
    const icons = {
        available: "success",
        sold: "info",
        reserved: "warning",
    };
    Swal.fire({
        title: `Ubah Status?`,
        text: `Ubah status ke "${labels[status]}"?`,
        icon: icons[status],
        showCancelButton: true,
        confirmButtonColor: "#4f46e5",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya, Ubah",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(
                route("admin.variants.items.status", [
                    props.product.id,
                    props.variant.id,
                    item.id,
                ]),
                { status },
            );
        }
    });
};

const statusColor = (status) =>
    ({
        available: "bg-green-100 text-green-700",
        sold: "bg-blue-100 text-blue-700",
        reserved: "bg-yellow-100 text-yellow-700",
    })[status] || "bg-gray-100 text-gray-700";

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
</script>

<template>
    <Head :title="`Stok - ${variant.name}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.variants.index', product.id)"
                class="text-sm text-indigo-600 hover:text-indigo-800"
            >
                ← Kembali ke Varian
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">
                {{ product.name }} — {{ variant.name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Harga:
                <span class="font-medium">{{
                    formatCurrency(variant.discount_price || variant.price)
                }}</span>
                <span
                    v-if="variant.discount_price"
                    class="text-gray-400 line-through ml-2"
                    >{{ formatCurrency(variant.price) }}</span
                >
                · Stok tersedia:
                <span class="font-medium text-green-600">{{
                    variant.items?.filter((i) => i.status === "available")
                        .length
                }}</span>
            </p>
        </div>

        <!-- Tambah Item -->
        <div class="bg-white rounded-xl shadow-sm border p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-gray-900">
                    <ArchiveBoxIcon class="w-5 h-5 inline text-gray-500" />
                    Tambah Stok
                </h2>
                <button
                    @click="showAddForm = !showAddForm"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors"
                >
                    <PlusIcon class="w-4 h-4" />
                    {{ showAddForm ? "Tutup" : "Tambah Baru" }}
                </button>
            </div>
            <form
                v-if="showAddForm"
                @submit.prevent="storeItem"
                class="space-y-3"
            >
                <textarea
                    v-model="addForm.content"
                    rows="5"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Masukkan isi konten item...&#10;Bisa berupa akun, kode lisensi, data apapun&#10;Tulis sebebas mungkin"
                    required
                ></textarea>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Isi konten bebas — akun, lisensi, info, dll.
                    </p>
                    <button
                        type="submit"
                        :disabled="
                            addForm.processing || !addForm.content.trim()
                        "
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50 transition-colors"
                    >
                        {{ addForm.processing ? "Menambahkan..." : "Tambah" }}
                    </button>
                </div>
                <p v-if="addForm.errors.content" class="text-red-500 text-xs">
                    {{ addForm.errors.content }}
                </p>
            </form>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-900">
                    Daftar Stok ({{ variant.items?.length || 0 }} item)
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-5 py-3 text-gray-500 font-medium w-8">
                                #
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Isi Konten
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Status
                            </th>
                            <th class="px-5 py-3 text-gray-500 font-medium">
                                Ditambahkan
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
                            v-for="(item, idx) in variant.items"
                            :key="item.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-5 py-3 text-gray-400">
                                {{ idx + 1 }}
                            </td>
                            <td class="px-5 py-3">
                                <!-- Mode Edit -->
                                <div
                                    v-if="editingId === item.id"
                                    class="flex items-start gap-2"
                                >
                                    <textarea
                                        v-model="editForm.content"
                                        rows="3"
                                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    ></textarea>
                                    <div
                                        class="flex flex-col gap-1 flex-shrink-0"
                                    >
                                        <button
                                            @click="saveEdit(item)"
                                            :disabled="editForm.processing"
                                            class="px-2.5 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors disabled:opacity-50"
                                        >
                                            Simpan
                                        </button>
                                        <button
                                            @click="cancelEdit"
                                            class="px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                                        >
                                            Batal
                                        </button>
                                    </div>
                                </div>
                                <!-- Mode Baca -->
                                <div v-else>
                                    <p
                                        class="text-sm text-gray-800 whitespace-pre-wrap break-all max-w-md"
                                    >
                                        {{ item.content }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    :class="statusColor(item.status)"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{
                                    new Date(
                                        item.created_at,
                                    ).toLocaleDateString("id-ID")
                                }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <button
                                        v-if="
                                            editingId !== item.id &&
                                            item.status !== 'sold'
                                        "
                                        @click="startEdit(item)"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded transition-colors"
                                        title="Edit"
                                    >
                                        <PencilSquareIcon class="w-3.5 h-3.5" />
                                        Edit
                                    </button>
                                    <button
                                        v-if="item.status !== 'available'"
                                        @click="changeStatus(item, 'available')"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded transition-colors"
                                        title="Tandai Tersedia"
                                    >
                                        <CheckCircleIcon class="w-3.5 h-3.5" />
                                        Tersedia
                                    </button>
                                    <button
                                        v-if="item.status !== 'sold'"
                                        @click="changeStatus(item, 'sold')"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded transition-colors"
                                        title="Tandai Terjual"
                                    >
                                        <BanknotesIcon class="w-3.5 h-3.5" />
                                        Terjual
                                    </button>
                                    <button
                                        v-if="item.status !== 'reserved'"
                                        @click="changeStatus(item, 'reserved')"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded transition-colors"
                                        title="Tandai Direservasi"
                                    >
                                        <ClockIcon class="w-3.5 h-3.5" />
                                        Reserved
                                    </button>
                                    <button
                                        v-if="item.status !== 'sold'"
                                        @click="deleteItem(item)"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded transition-colors"
                                    >
                                        <XCircleIcon class="w-3.5 h-3.5" />
                                        Hapus
                                    </button>
                                    <span
                                        v-if="
                                            item.status === 'sold' &&
                                            editingId !== item.id
                                        "
                                        class="text-xs text-gray-400 italic"
                                        >Terjual</span
                                    >
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!variant.items?.length">
                            <td
                                colspan="5"
                                class="px-5 py-8 text-center text-gray-400"
                            >
                                Belum ada stok
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
