<script setup>
defineProps({
    sending: { type: Boolean, default: false },
    countdown: { type: [Number, null], default: null },
    disabled: { type: Boolean, default: false },
    label: { type: String, default: 'Send request' },
});
</script>

<template>
    <button type="button" class="btn btn-primary btn-block send-button" :disabled="disabled || sending" :aria-busy="sending ? 'true' : 'false'">
        <span v-if="sending" class="spinner" aria-hidden="true"></span>
        <span v-if="sending && countdown">Waiting · {{ (countdown / 1000).toFixed(1) }} s</span>
        <span v-else-if="sending">Sending…</span>
        <span v-else>{{ label }}</span>
    </button>
</template>

<style scoped>
.send-button {
    font-variant-numeric: tabular-nums;
}
</style>
