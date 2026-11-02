<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="currentColor"
        :class="className"
        v-bind="$attrs"
    >
        <path
            d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"
            fill="currentColor"
            class="text-red-500"
        />
        <!-- Inner plus cross -->
        <path
            d="M12 9v6M9 12h6"
            stroke="white"
            stroke-width="2"
            stroke-linecap="round"
        />
    </svg>
</template>
