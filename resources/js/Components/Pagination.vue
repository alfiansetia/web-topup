<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div
        v-if="links.length > 3"
        class="flex items-center justify-center gap-1 mt-6"
    >
        <template v-for="(link, i) in links" :key="i">
            <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                    'px-3 py-2 text-sm rounded-lg transition-colors',
                    link.active
                        ? 'bg-indigo-600 text-white'
                        : 'bg-white text-gray-700 hover:bg-gray-100 border',
                ]"
            />
            <span
                v-else
                v-html="link.label"
                class="px-3 py-2 text-sm text-gray-400"
            />
        </template>
    </div>
</template>
