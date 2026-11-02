<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-lg bg-red-600 text-white shadow-sm"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span
            class="truncate leading-tight font-bold text-red-600 dark:text-red-400"
            >DarahKitaa</span
        >
        <span
            class="text-[10px] leading-tight font-medium text-muted-foreground"
            >Sistem Admin Donor</span
        >
    </div>
</template>
