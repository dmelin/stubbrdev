<script setup>
import { onUnmounted, ref } from 'vue';
import { copyText } from '../../lib/token';

const props = defineProps({
    text: { type: [String, Function], required: true },
    label: { type: String, default: 'Copy' },
    variant: { type: String, default: 'ghost' },
    size: { type: String, default: 'sm' },
    mono: { type: Boolean, default: false },
});

const copied = ref(false);
let timer = null;

const copy = async () => {
    const value = typeof props.text === 'function' ? props.text() : props.text;
    const ok = await copyText(value);
    if (!ok) return;
    copied.value = true;
    if (timer) window.clearTimeout(timer);
    timer = window.setTimeout(() => {
        copied.value = false;
    }, 1200);
};

onUnmounted(() => {
    if (timer) window.clearTimeout(timer);
});
</script>

<template>
    <button
        type="button"
        class="btn"
        :class="[`btn-${variant}`, size !== 'md' ? `btn-${size}` : '', mono ? 'btn-mono' : '']"
        :aria-live="copied ? 'polite' : 'off'"
        @click="copy"
    >
        {{ copied ? 'copied' : label }}
    </button>
</template>
