<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'secondary' },
    size: { type: String, default: 'md' },
    to: { type: String, default: '' },
    href: { type: String, default: '' },
    type: { type: String, default: 'button' },
    block: { type: Boolean, default: false },
    icon: { type: Boolean, default: false },
    mono: { type: Boolean, default: false },
});

const classes = computed(() => [
    'btn',
    `btn-${props.variant}`,
    props.size !== 'md' ? `btn-${props.size}` : '',
    props.block ? 'btn-block' : '',
    props.icon ? 'btn-icon' : '',
    props.mono ? 'btn-mono' : '',
]);
</script>

<template>
    <RouterLink v-if="to" :to="to" :class="classes"><slot /></RouterLink>
    <a v-else-if="href" :href="href" :class="classes" target="_blank" rel="noopener noreferrer"><slot /></a>
    <button v-else :type="type" :class="classes"><slot /></button>
</template>
