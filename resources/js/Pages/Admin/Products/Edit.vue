<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ArrowLeftIcon, PhotoIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { computed, ref } from "vue";
import FormInput from "@/Components/FormInput.vue";
import FormTextarea from "@/Components/FormTextarea.vue";
import FormSelect from "@/Components/FormSelect.vue";
import FormCheckbox from "@/Components/FormCheckbox.vue";

const props = defineProps({ product: Object, categories: Array });

const categoryOptions = computed(() =>
    props.categories.map((cat) => ({ value: cat.id, label: cat.name })),
);

const imagePreview = ref(null);
const showCurrentImage = ref(!!props.product.image);

const form = useForm({
    category_id: props.product.category_id,
    name: props.product.name,
    description: props.product.description || "",
    features: props.product.features ? props.product.features.join("\n") : "",
    image: null,
    remove_image: false,
    is_active: props.product.is_active,
    sort_order: props.product.sort_order,
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        form.remove_image = false;
        showCurrentImage.value = false;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const removeCurrentImage = () => {
    showCurrentImage.value = false;
    form.remove_image = true;
};

const submit = () => {
    const data = { ...form };
    if (typeof data.features === "string" && data.features.trim()) {
        try {
            data.features = JSON.parse(data.features);
        } catch {
            data.features = data.features.split("\n").filter(Boolean);
        }
    } else {
        data.features = null;
    }
    data._method = "PUT";
    data.remove_image = data.remove_image ? 1 : 0;
    form.transform(() => data).post(
        route("admin.products.update", props.product.id),
    );
};
</script>

<template>
    <Head :title="`Edit - ${product.name}`" />
    <AdminLayout>
        <div class="mb-6">
            <Link
                :href="route('admin.products.index')"
                class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800"
            >
                <ArrowLeftIcon class="w-4 h-4" /> Kembali ke Produk
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Produk</h1>
        </div>

        <div class="max-w-2xl">
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 p-6 space-y-5"
            >
                <FormSelect
                    v-model="form.category_id"
                    label="Kategori"
                    :options="categoryOptions"
                    :error="form.errors.category_id"
                    placeholder="Pilih Kategori"
                    required
                />

                <FormInput
                    v-model="form.name"
                    label="Nama Produk"
                    :error="form.errors.name"
                    required
                />

                <FormTextarea
                    v-model="form.description"
                    label="Deskripsi"
                    :rows="3"
                />

                <FormTextarea
                    v-model="form.features"
                    label="Fitur"
                    hint="Satu fitur per baris"
                    placeholder="Tanpa iklan&#10;Resolusi 4K&#10;Download offline"
                    :rows="3"
                />

                <!-- Image Upload -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Gambar</label
                    >
                    <div v-if="imagePreview" class="relative inline-block">
                        <img
                            :src="imagePreview"
                            class="w-36 h-36 object-cover rounded-lg ring-1 ring-gray-200"
                        />
                        <button
                            type="button"
                            @click="removeImage"
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-sm transition-colors"
                        >
                            <XMarkIcon class="w-3.5 h-3.5" />
                        </button>
                    </div>
                    <div
                        v-else-if="showCurrentImage"
                        class="relative inline-block"
                    >
                        <img
                            :src="product.image_url"
                            class="w-36 h-36 object-cover rounded-lg ring-1 ring-gray-200"
                        />
                        <button
                            type="button"
                            @click="removeCurrentImage"
                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-sm transition-colors"
                        >
                            <XMarkIcon class="w-3.5 h-3.5" />
                        </button>
                    </div>
                    <label
                        v-else
                        class="flex flex-col items-center justify-center w-36 h-36 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors"
                    >
                        <PhotoIcon class="w-10 h-10 text-gray-400" />
                        <span class="text-xs text-gray-500 mt-1.5"
                            >Pilih Gambar</span
                        >
                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp,image/svg+xml"
                            class="hidden"
                            @change="handleImageChange"
                        />
                    </label>
                    <p
                        v-if="form.errors.image"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.image }}
                    </p>
                    <p class="mt-1 text-xs text-gray-400">
                        JPG, PNG, WEBP, SVG. Maks 2MB
                    </p>
                </div>

                <FormInput
                    v-model="form.sort_order"
                    label="Urutan"
                    type="number"
                    placeholder="0"
                />

                <FormCheckbox
                    v-model="form.is_active"
                    label="Aktif"
                    id="product_active"
                />

                <div class="flex justify-end gap-3 pt-2 border-t">
                    <Link
                        :href="route('admin.products.index')"
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
