<script setup>
defineProps({
    modelValue: { type: [String, Number], default: "" },
    label: String,
    error: String,
    hint: String,
    type: { type: String, default: "text" },
    placeholder: String,
    required: Boolean,
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label
            v-if="label"
            class="block text-sm font-medium text-gray-700 mb-1.5"
        >
            {{ label }}
            <span v-if="required" class="text-red-400">*</span>
        </label>
        <div class="relative">
            <input
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :required="required"
                class="block w-full rounded-lg border-0 py-2.5 px-3.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset transition-all duration-150"
                :class="
                    error
                        ? 'ring-red-300 placeholder:text-red-400 focus:ring-2 focus:ring-inset focus:ring-red-500'
                        : 'ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-500 hover:ring-gray-400'
                "
            />
        </div>
        <p v-if="hint && !error" class="mt-1.5 text-xs text-gray-400">
            {{ hint }}
        </p>
        <p
            v-if="error"
            class="mt-1.5 text-xs text-red-600 flex items-center gap-1"
        >
            <svg
                class="w-3.5 h-3.5 flex-shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"
                />
            </svg>
            {{ error }}
        </p>
    </div>
</template>
