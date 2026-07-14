<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ArrowLeftIcon } from "@heroicons/vue/24/outline";
import FormInput from "@/Components/FormInput.vue";
import FormCheckbox from "@/Components/FormCheckbox.vue";

const props = defineProps({ product: Object, variant: Object });

const form = useForm({
    name: props.variant.name,
    description: props.variant.description || "",
    price: props.variant.price,
    discount_price: props.variant.discount_price || "",
    is_active: props.variant.is_active,
    sort_order: props.variant.sort_order,
});

const submit = () => {
    form.put(
        route("admin.variants.update", [props.product.id, props.variant.id]),
    );
};
</script>

<template>
    <Head :title="`Edit - ${variant.name}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.variants.index', product.id)"
                class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800"
            >
                <ArrowLeftIcon class="w-4 h-4" /> Kembali ke Varian
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">
                Edit Varian — {{ product.name }}
            </h1>
        </div>

        <div class="max-w-2xl">
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-6 space-y-5"
            >
                <FormInput
                    v-model="form.name"
                    label="Nama Varian"
                    :error="form.errors.name"
                    required
                />

                <FormInput v-model="form.description" label="Deskripsi" />

                <div class="grid grid-cols-2 gap-4">
                    <FormInput
                        v-model="form.price"
                        label="Harga (Rp)"
                        type="number"
                        :error="form.errors.price"
                        placeholder="0"
                        required
                    />
                    <FormInput
                        v-model="form.discount_price"
                        label="Harga Diskon (Rp)"
                        type="number"
                        hint="Kosongkan jika tidak diskon"
                        placeholder="0"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <FormInput
                        v-model="form.sort_order"
                        label="Urutan"
                        type="number"
                        placeholder="0"
                    />
                    <div class="flex items-end pb-0.5">
                        <FormCheckbox
                            v-model="form.is_active"
                            label="Aktif"
                            id="variant_active"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t">
                    <Link
                        :href="route('admin.variants.index', product.id)"
                        class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50 transition-colors"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                    >
                        {{ form.processing ? "Menyimpan..." : "Perbarui" }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
