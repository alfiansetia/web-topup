<script setup>
defineProps({
    modelValue: { type: Boolean, default: false },
    label: String,
    id: String,
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <label
        :for="id"
        class="flex items-center gap-3 cursor-pointer group select-none"
    >
        <button
            type="button"
            role="switch"
            :aria-checked="modelValue"
            @click="$emit('update:modelValue', !modelValue)"
            class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            :class="modelValue ? 'bg-indigo-600' : 'bg-gray-200'"
        >
            <span
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out mt-0.5"
                :class="modelValue ? 'translate-x-[22px]' : 'translate-x-0.5'"
            />
        </button>
        <span class="text-sm text-gray-700">{{ label }}</span>
    </label>
</template>
