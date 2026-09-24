<script setup>
defineProps({
    size: { type: String, default: 'md' },
    version: { type: Boolean, default: false },
    to: { type: String, default: '/' },
    name: { type: String, default: 'Stubbr' },
});
</script>

<template>
    <RouterLink :to="to" class="brand" :class="`brand-${size}`" aria-label="Stubbr home">
        <span class="brand-mark" aria-hidden="true">{&gt;}</span>
        <span class="brand-name">{{ name }}</span>
        <span v-if="version" class="brand-version">v0.9 · hobby</span>
    </RouterLink>
</template>
